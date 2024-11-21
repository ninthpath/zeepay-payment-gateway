<?php

if (!defined('ABSPATH')) exit;

/** 
 * Plugin Name: Zeepay Payment Gateway
 * Plugin URI: https://wordpress.org/plugins/zeepay-payment-gateway
 * Description: Take Mobile money and card payments on your store.
 * Author: Zeepay Technology
 * Author URI: https://www.myzeepay.com/
 * Version: 1.0.6
 * Copyright: © 2009-2011 Zeepay Ghana.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */


if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;


add_action('plugins_loaded', 'ZPPAYMENTGHANA_payment_gateway', 11);


function ZPPAYMENTGHANA_payment_gateway()
{

	if (class_exists('WC_Payment_Gateway')) {

		class ZPPAYMENTGHANA_zeepay_payment_gateway extends WC_Payment_Gateway
		{

			public $clientId;
			public $clientSecret;
			public $username;
			public $password;


			public function __construct()
			{

				$this->id = 'zeepay_payment';
				$this->icon = apply_filters(
					'ZPPAYMENTGHANA_woocommerce_zeepay_icon',
					plugins_url('/assets/icon-128x128.png', __FILE__)
				);
				$this->has_fields = false;
				$this->method_title = __('Zeepay Payment', 'zeepay-payment-gateway');
				$this->method_description = __('Zeepay local content payment systems.', 'zeepay-payment-gateway');

				$this->title = $this->get_option('title');
				$this->description = $this->get_option('description');
				$this->instructions = $this->get_option('instructions');
				$this->endpoint = $this->get_option('endpoint');

				$this->clientId = $this->get_option('clientId');
				$this->clientSecret = $this->get_option('clientSecret');
				$this->username = $this->get_option('username');
				$this->password = $this->get_option('password');

				$this->init_form_fields();
				$this->init_settings();

				add_action('woocommerce_update_options_payment_gateways\_' . $this->id, array($this, 'process_admin_options'));
				add_action('woocommerce_thank_you_' . $this->id, array($this, 'thank_you_page'));
				add_action('woocommerce_review_order_before_payment', array($this, 'show_payment_gateway_option'));

				add_action('admin_notices', array($this, 'admin_notices'));
			}



			public function get_icon()
			{
				$icon_url = plugins_url('/icon-128x128.png', __FILE__);

				ob_start(); ?>

				<img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($this->title); ?>" />
				

				<?php
				$icon = ob_get_clean();

				return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
			}




			public function init_form_fields()
			{
				$this->form_fields = apply_filters(
					'woo_zeepay_payment_fields',
					array(

						'title' => array(
							'title' => __(
								'Zeepay Payments Gateway',
								'zeepay-payment-gateway'
							),
							'type' => 'text',
							'default' => __(
								'',
								'zeepay-payment-gateway'
							),
							'desc_tip' => true,
							'description' => __('Add a new title for the Zeepay
						  Payment Gateway that customers will see when they are in 
						  the checkout page.', 'zeepay-payment-gateway'),


						),
						'description' => array(
							'title' => __(
								'Zeepay Payments Gateway Description',
								'zeepay-payment-gateway'
							),
							'type' => 'textarea',
							'default' => __(
								'Make Payment with mobile money wallet. Zeepay, MTN, Airtel, Telecel',
								'zeepay-payment-gateway'
							),
							'desc_tip' => true,
							'description' => __(
								'Add a new title for the 
						  Zeepay Payment Gateway that customers will see when they are in the checkout page.',
								'zeepay-payment-gateway'
							),

						),

						'endpoint' => array(
							'title' => __(
								'Zeepay endpoint',
								'zeepay-payment-gateway'
							),
							'type' => 'text',
							'description' => 'Enter your Zeepay endpoint'
						),
						
						'clientId' => array(
							'title' => __(
								'Zeepay API Client ID',
								'zeepay-payment-gateway'
							),
							'type' => 'text',
							'description' => 'Enter your Zeepay API Client ID'
						),

						'clientSecret' => array(
							'title' => __(
								'Zeepay API Client Secret',
								'zeepay-payment-gateway'
							),
							'type' => 'text',
							'description' => 'Enter your Zeepay API Client Secret'
						),

						'username' => array(
							'title' => __('Zeepay API Username', 'zeepay-payment-gateway'),
							'type' => 'text',
							'description' => 'Enter your Zeepay API Username'
						),

						'password' => array(
							'title' => __(
								'Zeepay API Password',
								'zeepay-payment-gateway'
							),
							'type' => 'text',
							'description' => 'Enter your Zeepay API Password'
						),

						'ZPPAYMENTGHANA_nonce' => array(
							'type' => 'hidden', 
							'default' => wp_create_nonce('ZPPAYMENTGHANA_zeepay_settings_nonce')
						)
					)
				);
			}


			/**
			 * validate zeepay details .
			 */
			public function admin_notices()
			{
				if ($this->enabled == 'no') {
					return;
				}
					if (!empty($this->get_option('ZPPAYMENTGHANA_nonce')) && wp_verify_nonce($this->get_option('ZPPAYMENTGHANA_nonce'), 'ZPPAYMENTGHANA_zeepay_settings_nonce') && (isset($_POST['save']) || isset($_POST['save_changes'])) )  {
						
						if (!current_user_can('manage_options')) {
							echo '<div class="notice notice-warning is-dismissible"><p>' . sprintf(esc_html__('Not Authorized', 'zeepay-payment-gateway'), esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=zeepay'))) . '</p></div>';
							return;
						}

						parent::process_admin_options();
						

						$clientSecret = $this->get_option('clientSecret');
						$client_id = $this->get_option('clientId');
						$username = $this->get_option('username');
						$password = $this->get_option('password');

						$token = $this->getToken($clientSecret, $client_id, $username, $password);

						if (empty($token)) {
							echo '<div class="notice notice-warning is-dismissible"><p>' . sprintf(esc_html__('Please enter valid zeepay details to be able to use the zeepay WooCommerce plugin.','zeepay-payment-gateway'), esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=zeepay'))) . '</p></div>';
							return;
						} else {
							echo '<div class="notice notice-success is-dismissible"><p>' . sprintf(esc_html__('Zeepay token generated successfully', 'zeepay-payment-gateway')) . '</p></div>';
							return;
						}
					}
			}



			public function process_admin_options()
			{
				if (!current_user_can('manage_options') || !isset($_POST[$this->plugin_id . '_nonce_field'])) {
					return;
				}

				$nonce = isset($_POST[$this->plugin_id . '_nonce_field']) ? sanitize_text_field(wp_unslash($_POST[$this->plugin_id . '_nonce_field'])) : '';
				$nonce = wp_verify_nonce($nonce, 'ZPPAYMENTGHANA_zeepay_settings_nonce') ? $nonce : '';

				if (empty($nonce)) {
					return;
				}

				parent::process_admin_options();
			}




			public function process_payment($order_id)
			{
				global $woocommerce;


				$order = wc_get_order($order_id);

				$order_data = $order->get_data();
				$order_total = $order_data['total'];

				$items = $order->get_items();

				$item_ids = array();

				foreach ($items as $item) {
					$item_ids[] = $item->get_product_id();
				}

				$response = $this->createCheckout($order_id, $item_ids, "979", 'GH', 'GHS', $order_total, 'Test');


				if ($response['code'] === 411 && $response['checkout-url']) {
					return array(
						'result' => 'success',
						'redirect' => $response['checkout-url'],
					);
				} else {
					wc_add_notice(__('Payment Error: ', 'zeepay-payment-gateway') . 'Faileddddd to initiate payment', 'error');
					return;
				}
			}


			private function getToken($clientSecret, $client_id, $username, $password)
			{
				$endpoint = $this->get_option('endpoint');

				try {
					$auth_url = $endpoint."/oauth/token";


					$startTime = round(microtime(true) * 1000);

					$request = [
						'grant_type' => 'password',
						'client_secret' => $clientSecret,
						'client_id' => $client_id,
						'username' => $username,
						'password' => $password,
					];

					$response = wp_remote_post($auth_url, array(
						'method' => 'POST',
						'headers' => array(
							'Content-Type' => 'application/json',
							'Accept' => 'application/json',
						),
						'body' => wp_json_encode($request),
						'timeout' => 30,
					));






					if (is_wp_error($response)) {
						$error_message = $response->get_error_message();
						echo esc_html("Something went wrong: $error_message");
					} else {
						$response_body = wp_remote_retrieve_body($response);
						$json_response = json_decode($response_body, true);

						if (isset($json_response['access_token'])) {
							return $json_response['access_token'];
						}
					}
				} catch (\Exception $e) {
					echo "couldn't get token..try" . esc_html($e->getMessage());
				}


				return null;
			}



			public function createCheckout($order_id, $item_id, $product, $country, $currency, $order_total, $description)
			{
				$callId = gmdate("YmdHis") . wp_rand(1000, 9999);
				$endpoint = $this->get_option('endpoint');


				try {

					$clientSecret = $this->get_option('clientSecret');
					$client_id = $this->get_option('clientId');
					$username = $this->get_option('username');
					$password = $this->get_option('password');

					$token =  $this->getToken($clientSecret, $client_id, $username, $password);


					if (is_null($token)) {
						wc_add_notice(__('Payment Error: ', 'zeepay-payment-gateway') . 'Failed to authenticate', 'error');
						return;
					}

					$base_url = $endpoint."/api/v2/instntmny-local/transactions/3rd-party/checkout";


					$nonce = substr(str_shuffle(MD5(microtime())), 0, 12);

					wc_add_order_item_meta($order_id, 'ipn_nonce', $nonce);

					$order = new WC_Order($order_id);

					$body = [
						"amount" => $order_total,
						"services" => "wallet,card",
						"currency" => $currency,
						"reference" => $order_id  . "_" . wp_rand(10000, 999999) . $item_id,
						"product" => $product,
						"callback_url" => get_bloginfo('url') . "/wc-api/zeepay-payment-gateway/?nonce=" . $nonce . "&order_id=" . $order_id,
						"cancelUrl" => wc_get_cart_url(),
						"returnUrl" => $this->get_return_url($order),
						"description" => $description . " - " . $order_id,
					];


					// making a post request
					$response = wp_remote_post($base_url, array(
						'method' => 'POST',
						'headers' => array(
							'Content-Type' => 'application/json',
							'Accept' => 'application/json',
							'Authorization' => 'Bearer ' . $token,
						),
						'body' => wp_json_encode($body),
						'timeout' => 90,
					));



					$endTime = round(microtime(true) * 1000);

					$response_code = wp_remote_retrieve_response_code($response);

					if (is_wp_error($response)) {
						$error_message = $response->get_error_message();
						echo esc_html("Something went wrong: $error_message");
					} else {
						$response_body = wp_remote_retrieve_body($response);
						$json_response = json_decode($response_body, true);

						if (isset($json_response['code']) && $json_response['code'] == 411) {
							return [
								'code' => 411,
								'checkout-url' => $json_response['checkout-url']
							];
						}
					}
				} catch (\Exception $e) {
					echo "couldn't checkout" . esc_html($e->getMessage());
				}

				wc_add_notice(__('Payment Error: ', 'zeepay-payment-gateway') . 'Failed to create checkout', 'error');
				return;
			}



			public function thank_you_page()
			{
				if ($this->instructions) {
					echo esc_html(wpautop($this->instructions));
				}
			}

			/**
			 * Show payment gateway option on the checkout page
			 */
			public function show_payment_gateway_option()
			{
				if ($this->is_available()) {
				?>
					<input type="radio" id="zeepay_payment" name="payment_method" value="<?php echo esc_attr($this->id); ?>">

<?php
				}
			}
		}
	} else {
		die('WC_Payment_Gateway does not exist');
	}
}

function ZPPAYMENTGHANA_add_zeepay_payment_gateway($methods)
{
	$methods[] = 'ZPPAYMENTGHANA_zeepay_payment_gateway';
	return $methods;
}

add_filter('woocommerce_payment_gateways', 'ZPPAYMENTGHANA_add_zeepay_payment_gateway');
