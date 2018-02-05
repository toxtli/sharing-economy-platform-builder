<?php

/**
 * WC Dependency Checker
 *
 */
class WCMp_Dependencies_Stripe_Gateway {

    private static $active_plugins;

    static function init() {
        self::$active_plugins = (array) get_option('active_plugins', array());
        if (is_multisite())
            self::$active_plugins = array_merge(self::$active_plugins, get_site_option('active_sitewide_plugins', array()));
    }

    static function woocommerce_plugin_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('woocommerce/woocommerce.php', self::$active_plugins) || array_key_exists('woocommerce/woocommerce.php', self::$active_plugins);
        return false;
    }

    // WC Marketplace
    static function wc_marketplace_plugin_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('dc-woocommerce-multi-vendor/dc_product_vendor.php', self::$active_plugins) || array_key_exists('dc-woocommerce-multi-vendor/dc_product_vendor.php', self::$active_plugins);
        return false;
    }

    static function woocommerce_gateway_stripe_plugin_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('woocommerce-gateway-stripe/woocommerce-gateway-stripe.php', self::$active_plugins) || array_key_exists('woocommerce-gateway-stripe/woocommerce-gateway-stripe.php', self::$active_plugins);
        return false;
    }

}
