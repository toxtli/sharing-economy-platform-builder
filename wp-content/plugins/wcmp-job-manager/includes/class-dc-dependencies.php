<?php

/**
 * Dependency Checker
 *
 */
class WCMp_Job_Manager_Dependencies {

    private static $active_plugins;

    static function init() {
        self::$active_plugins = (array) get_option('active_plugins', array());
        if (is_multisite()) {
            self::$active_plugins = array_merge(self::$active_plugins, get_site_option('active_sitewide_plugins', array()));
        }
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
    
    static function wp_job_manager_plugin_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('wp-job-manager/wp-job-manager.php', self::$active_plugins) || array_key_exists('wp-job-manager/wp-job-manager.php', self::$active_plugins);
        return false;
    }
    static function wp_job_manager_paid_listing_plugin_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('wp-job-manager-wc-paid-listings/wp-job-manager-wc-paid-listings.php', self::$active_plugins) || array_key_exists('wp-job-manager-wc-paid-listings/wp-job-manager-wc-paid-listings.php', self::$active_plugins);
        return false;
    }
    

}
