<?php

/**
 * WC Dependency Checker
 *
 */
class WC_Dependencies_Product_Vendor {

    private static $active_plugins;

    public static function init() {
        self::$active_plugins = (array) get_option('active_plugins', array());
        if (is_multisite())
            self::$active_plugins = array_merge(self::$active_plugins, get_site_option('active_sitewide_plugins', array()));
    }
    /**
     * Check woocommerce exist
     * @return Boolean
     */
    public static function woocommerce_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('woocommerce/woocommerce.php', self::$active_plugins) || array_key_exists('woocommerce/woocommerce.php', self::$active_plugins);
    }
    /**
     * Check if woocommerce active
     * @return Boolean
     */
    public static function is_woocommerce_active() {
        return self::woocommerce_active_check();
    }
    /**
     * Check if Woocommerce Extra Checkout Fields For Brazil plugin active
     * @return Boolean
     */
    public static function woocommerce_extra_checkout_fields_for_brazil_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php', self::$active_plugins) || array_key_exists('woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php', self::$active_plugins);
    }
    /**
     * Check if Woocommerce Product Enquiry Form active
     * @return Boolean
     */
    public static function woocommerce_product_enquiry_form_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('woocommerce-product-enquiry-form/product-enquiry-form.php', self::$active_plugins) || array_key_exists('woocommerce-product-enquiry-form/product-enquiry-form.php', self::$active_plugins);
    }

}
