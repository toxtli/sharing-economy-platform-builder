<?php

/**
 * Plugin Name: Marketplace Stripe Gateway
 * Plugin URI: https://wc-marketplace.com/
 * Description: Stripe Payment Gateway ( WooCommerce MarketPlace Compatible )
 * Author: WC Marketplace, The Grey Parrots
 * Version: 1.0.7
 * Author URI: https://wc-marketplace.com/
 * 
 * Text Domain: marketplace-stripe-gateway
 * Domain Path: /languages/
 */

if (!class_exists('WCMp_Dependencies_Stripe_Gateway')) {
    require_once trailingslashit(dirname(__FILE__)) . 'includes/class-wcmp-stripe-dependencies.php';
}
require_once trailingslashit(dirname(__FILE__)) . 'includes/wcmp-stripe-gateway-core-functions.php';
require_once trailingslashit(dirname(__FILE__)) . 'marketplace-stripe-gateway-config.php';
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
} 
if (!defined('WCMp_STRIPE_GATEWAY_PLUGIN_TOKEN')) {
    exit;
}
if (!defined('WCMp_STRIPE_GATEWAY_TEXT_DOMAIN')) {
    exit;
}

if (!WCMp_Dependencies_Stripe_Gateway::woocommerce_plugin_active_check()) {
    add_action('admin_notices', 'woocommerce_inactive_notice_stripe');
}

if (!WCMp_Dependencies_Stripe_Gateway::woocommerce_gateway_stripe_plugin_active_check()) {
    add_action('admin_notices', 'wc_stripe_inactive_notice_stripe');
}

if (WCMp_Dependencies_Stripe_Gateway::woocommerce_plugin_active_check() && WCMp_Dependencies_Stripe_Gateway::woocommerce_gateway_stripe_plugin_active_check()) {
    if (!class_exists('WCMp_Stripe_Gateway')) {
        require_once( trailingslashit(dirname(__FILE__)) . 'classes/class-wcmp-stripe-gateway.php' );
        global $WCMp_Stripe_Gateway;
        $WCMp_Stripe_Gateway = new WCMp_Stripe_Gateway(__FILE__);
        $GLOBALS['WCMp_Stripe_Gateway'] = $WCMp_Stripe_Gateway;
        // Activation Hooks
        register_activation_hook(__FILE__, array($WCMp_Stripe_Gateway, 'activate_wcmp_stripe_gateway'));
        register_activation_hook(__FILE__, 'flush_rewrite_rules');
        // Deactivation Hooks
        register_deactivation_hook(__FILE__, array($WCMp_Stripe_Gateway, 'deactivate_wcmp_stripe_gateway'));
    }
}
?>
