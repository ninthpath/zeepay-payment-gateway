== Zeepay Payment Gateway ==
Author: James Obeng
Author URI: https://www.myzeepay.com/
Tags: zeepay, woocommerce, payment gateway,mobile money
Donate link: https://www.myzeepay.com/
Stable Tag: 1.0.6
Requires at least: 4.7
Requires PHP: 5.6
Tested up to: 6.4.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Contributors: wordpressdotorg, obeng123

Zeepay payment plugin allow you to accept secure payments from multiple local and global payment channels.

 == Description ==
 Easy Integration process for all your secure payments
 Accept all secure payment from Mobile Wallets

 == Features ==

*   __Accept payment__ via USSD, Mobile Money.
*   __Seamless integration__ into the WooCommerce checkout page. Accept payment directly on your site


== Changelog ==

= 1.0.6 =
This is the latest version


== Screenshots ==

1. This screen shot 1.png, 2.png, 3.png, 4.png, 5.png, 6.png shows how our checkout interface looks and the required feilds to complete a payment.
2. Screen shot 7.png shows how shop.digitaltermination.com dashboard looks like.


== Upgrade Notice ==

= 1.0.6=
User should upgrade to newer versions because it will have new features and user's customer will be able to make more secure payment with different currency and support all kinds of online payment like card payment.


 == Installation ==

*   Go to __WordPress Admin__ > __Plugins__ > __Add New__ from the left-hand menu
*   In the search box type __Zeepay Payments Gateway__
*   Click on Install now when you see __Zeepay Payment Gateway__ to install the plugin
*   After installation, __activate__ the plugin.


== Zeepay Setup and Configuration ==
*   Go to __WooCommerce > Settings__ and click on the __Payments__ tab
*   You'll see Zeepay listed along with your other payment methods. Click __Set Up__
*   On the next screen, configure the plugin. There is a selection of options on the screen. Read what each one does below.

1. __Enable/Disable__ - Check this checkbox to Enable Zeepay on your store's checkout
2. __Zeepay Payments Gateway Description__ - This will represent Zeepay on your list of Payment options during checkout. It guides users to know which option to select to pay with Zeepay. 
3. __Description__ - This controls the message that appears under the payment fields on the checkout page. Use this space to give more details to customers about what Zeepay is and what payment methods they can use with it.
4. __API Keys__ - The next four text boxes are for your Zeepay API keys, which you can get from your Zeepay Plugin documentsion. If you enabled Test Mode in step four, then you'll need to use your test API keys here. Otherwise, you can enter your live keys.
5. __Mobile Wallet__ - If enabled user can choose to pay with their mobile wallets.
6. Click on __Save Changes__ to update the settings.



== Troubleshooting: ==
== If Zeepay is not available among the Payment method options, please ensure: ==

*   Make sure you have the latest version of WordPress installed.
*   Make sure you have the latest version of the plugin installed.
*   You've set your checkout page shortcode to [woocommerce_checkout].
*   You've checked the __"Enable/Disable"__ checkbox
*   You've entered your __API Keys__ in the appropriate field
*   You've clicked on __Save Changes__ during setup


== Frequently Asked Questions ==

= How do i get my get my username, password, client ID and client API key? =

Visit Zeepay Plugin documentation at [www.myzeepay.com], fill out the contact form with your details, and we'll contact you with your username, password, client ID, client API key .

= What Do I Need To Use The Plugin =
An active [WooCommerce installation](https://docs.woocommerce.com/document/installing-uninstalling-woocommerce/)
A valid [SSL Certificate](https://docs.woocommerce.com/document/ssl-and-https/)


== Domains ==

shop.digitaltermination.com, your comprehensive solution for tracking payments and managing finances on your WordPress site. With easy sign-in access and customizable password reset options, users gain instant visibility into their payment history, including recent, successful, and unsuccessful transactions. The intuitive dashboard provides real-time updates on wallet balances, empowering users with actionable insights to optimize their financial strategies. Simplify your WordPress payment management experience with shop.digitaltermination.com today.

$auth_url = "https://shop.digitaltermination.com/oauth/token";
The auth_url serves as an endpoint for generating an authentication token essential for initiating the payment process. Utilizing provided credentials such as username, password, client ID, and client API key, users can obtain an authentication token required for accessing the payment checkout endpoint.

$base_url = "https://shop.digitaltermination.com/api/v2/instntmny-local/transactions/3rd-party/checkout";
The base_url denotes an endpoint leading to a payment checkout page. This page will be made accessible to customers on your WordPress site, enabling them to execute payments for their purchases using various mobile money networks.

In order for wordpress site owners to be able to access this endpoints, Owner of wordpress site must provide a static IP of thier site to be whitelisted


