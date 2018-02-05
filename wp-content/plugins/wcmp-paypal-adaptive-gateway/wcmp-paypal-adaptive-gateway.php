<?php

/**
 * Plugin Name: WCMp Paypal Adaptive Gateway
 * Plugin URI: https://wc-marketplace.com/
 * Description: WCMp Paypal Adaptive Gateway is a payment gateway for woocommerce shopping plateform also compatible with WC Marketplace.
 * Author: WC Marketplace, The Grey Parrots
 * Version: 1.0.3
 * Author URI: https://wc-marketplace.com/
 *
 * Text Domain: wcmp-paypal-adaptive-gateway
 * Domain Path: /languages/
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
if (!class_exists('WCMP_Paypal_Adaptive_Gateway_Dependencies')) {
    require_once 'includes/class-wcmp-paypal-adaptive-gateway-dependencies.php';
}
require_once 'includes/wcmp-paypal-adaptive-gateway-core-functions.php';
require_once 'wcmp-paypal-adaptive-gateway-config.php';

if (!defined('WCMP_PAYPAL_ADAPTIVE_GATEWAY_PLUGIN_TOKEN')) {
    exit;
}
if (!defined('WCMP_PAYPAL_ADAPTIVE_GATEWAY_TEXT_DOMAIN')) {
    exit;
}

if(!WCMP_Paypal_Adaptive_Gateway_Dependencies::woocommerce_active_check()){
    add_action('admin_notices', 'woocommerce_inactive_notice');
}

if (!class_exists('WCMP_Paypal_Adaptive_Gateway') && WCMP_Paypal_Adaptive_Gateway_Dependencies::woocommerce_active_check()) {
    require_once( 'classes/class-wcmp-paypal-adaptive-gateway.php' );
    global $WCMP_Paypal_Adaptive_Gateway;
    $WCMP_Paypal_Adaptive_Gateway = new WCMP_Paypal_Adaptive_Gateway(__FILE__);
    $GLOBALS['WCMP_Paypal_Adaptive_Gateway'] = $WCMP_Paypal_Adaptive_Gateway;
}
