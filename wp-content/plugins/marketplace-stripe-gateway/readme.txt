=== Marketplace Stripe Gateway ===
Contributors:   Dualcube, greyparrots, arimghosh, dualcube_subrata
Tags: Marketplace stripe gateway, wcmp stripe gateway, stripe payment, stripe commission, wc marketplace, wc market place, woocommerce product vendors, WooCommerce vendors, woocommerce marketplace, vendor, vendors, vendor system, woocommerce market place, WooCommerce multivendor, WooCommerce multi vendor, woocommerce vendors, woo vendors, WooCommerce vendors, Woocommerce Shipping,wc marketplace shipping, wcmp shipping, multivendor, multi vendor, multi vendors,  multi seller
Donate link: https://wc-marketplace.com/donate
Requires at least: 4.2
Tested up to: 4.8
Stable tag: 1.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Free Payment Gateway for Marketplace allowing you to Pay Your Vendors Using Stripe.


== Description ==

Marketplace Stripe Gateway allows you, the marketplace owner, to mass pay your vendors' commission using Stripe Connect. It supports both manual and schedule disbursement of payments. Make sure you have [WooCommerce Stripe Payment Gateway](https://wordpress.org/plugins/woocommerce-gateway-stripe/) installed on your site, to activate Marketplace Stripe Gateway. Currently supported multi vendor plugins are [WC Marketplace](https://wordpress.org/plugins/dc-woocommerce-multi-vendor/) and [WooCommecr Product Vendors](https://woocommerce.com/products/product-vendors/). 

<strong>Admin can</strong>

Pay the vendor their commission via Stripe by activating the plugin.
To know how admin can set the stripe account please visit : [WooCommerce Stripe Payment Gateway](https://wordpress.org/plugins/woocommerce-gateway-stripe/)

<strong>Vendor can</strong>

Vendor just need to connect with stripe to accept the commission vis Stripe. Vendor should be connected with Stripe in order to receive commission via Stripe.

<strong>Stripe Advantages</strong>
- Easy to set up, funds go directly into yours and the vendors receives their part of the commission in their accounts after the schedule.
- Keeps the customer on your site entering the credit card number on your checkout form
- Admins pay no extra fee to receive admins portion of the sale, vendor pays all fees.


To know more about Marketplace Stripe Gateway, please [visit us](http://wc-marketplace.com/knowledgebase/marketplace-stripe-gateway/).

== Installation ==

NOTE:  Marketplace Stripe Gateway plugin is an extension of WooCommerce and WooCommerce Stripe Payment Gateway. As such, WooCommerce and WooCommerce Stripe Payment Gateway plugin needs to be installed and activated on your WordPress site for this plugin to work properly.


1. Download and install Woocommerce
2. Download and install WooCommerce Stripe Payment Gateway
3. Download and install Marketplace Stripe Gateway plugin using the built-in Word Press plugin installer. If you download Marketplace Stripe Gateway plugin 
   manually, make sure that it's uploaded to /WP-content/plug ins/ and activate the plug in from the Plugin menu from your WordPress dashboard.      
   Alternatively, follow these steps below and install the addon: 
   Plugins > Add new > Upload plugin > Upload marketplace-stripe-gateway.zip > Install Now > Activate Plug in.
4. Active marketplace features from Woocommerce > WCMp > Payment tab > Stripe Gateway sub tab.




== Frequently Asked Questions ==
= Does this plugin work with newest WP version and also older versions? =
Ans. Yes, this plugin works fine with WordPress 4.8! It is also compatible for older WordPress versions from 4.2
= Up to which version of WooCommerce this plugin compatible with? =
Ans. This plugin is compatible with WooCommerce 3.0.
= Up to which version of php this plugin is compatible with? =
Ans. This plugin is tested with php version 5.6.
= What Will be  the  plug in requirements for working with this plugin? =
Ans. Woocommerce, WooCommerce Stripe Payment Gateway must be installed in your system.


== Screenshots ==
1. Marketplace Settings: Enable or disable marketplace stripe gateway and configure stripe client id.
3. Vendor Connect: Add vendor account to the admin's stripe connected account list .
4. Vendor Disconnect: Remove vendor account to the admin's stripe connected account list .

== Changelog ==

= 1.0.7 = 
* Added: Support WCMp 2.7.6

= 1.0.6 =
* Added: Commission payment support for WooCommerce Product Vendors.
* Fixed: CSS issue
* Updated: Language file

= 1.0.5 =
* Updated: Setting panel

= 1.0.4 =
* Added: Support WCMp 2.7.3
* Updated: Language file

= 1.0.3 =
* Added: Woocommerce 3.0 support
* Added: WCMp 2.6 support
* Added: Dependency on WooCommerce Stripe Payment Gateway
* Modify: Vendor stripe account disconnect logic

= 1.0.2 =
* Fixed: Woocommerce dependency check.
* Added: Support Woocommerce 2.6.11
* Added: Support WC Marketplace 2.5.0 

= 1.0.1 =
* Fixed: (github #1) Commission amount conversion.

= 1.0.0 =
* Initial Version release.

== Upgrade Notice ==

= 1.0.7 = 
* Added: Support WCMp 2.7.6
