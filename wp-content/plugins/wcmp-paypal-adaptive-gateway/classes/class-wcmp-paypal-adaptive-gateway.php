<?php

class WCMP_Paypal_Adaptive_Gateway {

    public $plugin_url;
    public $plugin_path;
    public $version;
    public $token;
    public $text_domain;
    public $library;
    public $shortcode;
    public $admin;
    public $frontend;
    public $template;
    public $ajax;
    private $file;
    public $settings;
    public $dc_wp_fields;
    public $payment_admin_settings;

    public function __construct($file) {

        $this->file = $file;
        $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
        $this->plugin_path = trailingslashit(dirname($file));
        $this->token = WCMP_PAYPAL_ADAPTIVE_GATEWAY_PLUGIN_TOKEN;
        $this->text_domain = WCMP_PAYPAL_ADAPTIVE_GATEWAY_TEXT_DOMAIN;
        $this->version = WCMP_PAYPAL_ADAPTIVE_GATEWAY_PLUGIN_VERSION;

        add_action('init', array(&$this, 'init'), 0);
        $wcmp_paypal_adaptive_settings = get_option('woocommerce_wcmp-paypal-adaptive-payments_settings');
        $this->payment_admin_settings = get_option('wcmp_payment_settings_name');
        if (isset($wcmp_paypal_adaptive_settings['enabled']) && $wcmp_paypal_adaptive_settings['enabled'] == 'yes' && WCMP_Paypal_Adaptive_Gateway_Dependencies::wcmp_active_check()) {
            add_filter('automatic_payment_method', array($this, 'admin_paypal_adaptive_mode'), 10);
            add_filter('wcmp_vendor_payment_mode', array($this, 'vendor_paypal_adaptive_mode'), 10);
            add_action('other_exta_field_dcmv', array(&$this, 'add_paypal_adaptive_email'));
            add_action('woocommerce_order_status_cancelled', array(&$this, 'woocommerce_order_status_cancelled'));
        }
    }

    /**
     * initilize plugin on WP init
     */
    function init() {

        // Init Text Domain
        $this->load_plugin_textdomain();

        if (!is_admin() || defined('DOING_AJAX')) {
            $this->load_class('frontend');
            $this->frontend = new WCMP_Paypal_Adaptive_Gateway_Frontend();
        }
        if (class_exists('WC_Payment_Gateway')) {
            $this->load_class('payment-method');
            add_filter('woocommerce_payment_gateways', array($this, 'add_paypal_adaptive_gateway'));
            if (WCMP_Paypal_Adaptive_Gateway_Dependencies::wcmp_active_check()) {
                $this->load_class('wcmp-payment-method');
                add_filter('wcmp_payment_gateways', array(&$this, 'add_wcmp_paypal_adaptive_payment_gateway'));
            }
        }
    }

    /**
     * Add payment gatway to woocommerce
     * @param array $arg
     * @return array
     */
    public function admin_paypal_adaptive_mode($arg) {
        $arg['paypal_adaptive'] = __('PayPal Adaptive', 'wcmp-paypal-adaptive-gateway');
        return $arg;
    }

    /**
     * Add WooCommerce adaptive gateway
     * @param array $methods
     * @return array payment methods
     */
    public function add_paypal_adaptive_gateway($methods) {
        $methods[] = 'WCMP_Paypal_Adaptive_Gateway_Payment_Method';
        return $methods;
    }

    /**
     * Add payment gatway to WCMp
     * @param array $load_gateways
     * @return array
     */
    public function add_wcmp_paypal_adaptive_payment_gateway($load_gateways) {
        $load_gateways[] = 'WCMp_Gateway_Paypal_Adaptive';
        return $load_gateways;
    }

    public function vendor_paypal_adaptive_mode($arg) {
        if (isset($this->payment_admin_settings['payment_method_paypal_adaptive']) && $this->payment_admin_settings['payment_method_paypal_adaptive'] = 'Enable') {
            $arg['paypal_adaptive'] = __('PayPal Adaptive', 'wcmp-paypal-adaptive-gateway');
        }
        return $arg;
    }

    public function add_paypal_adaptive_email() {
        $vendor_selected_payment_method = get_user_meta(get_current_user_id(), '_vendor_payment_mode', true);
        if ($vendor_selected_payment_method == 'paypal_adaptive') {
            $vendor_paypal_email = get_user_meta(get_current_user_id(), '_vendor_paypal_email', true);
            ?>
            <div class="wcmp_headding2"><?php _e('Paypal', 'wcmp-paypal-adaptive-gateway'); ?></div>
            <p><?php _e('Enter your Paypal ID', 'wcmp-paypal-adaptive-gateway'); ?></p>
            <input  class="long no_input" readonly type="text" name="vendor_paypal_email" value="<?php echo $vendor_paypal_email ? $vendor_paypal_email : ''; ?>"  placeholder="<?php _e('Enter your Paypal ID', 'wcmp-paypal-adaptive-gateway'); ?>">
            <?php
        }
    }

    public function woocommerce_order_status_cancelled($order_id) {
        global $wpdb;
        if (!$order = wc_get_order($order_id)) {
            return;
        }
        if ('wcmp-paypal-adaptive-payments' == $order->get_payment_method()) {
            $vendor_orders_in_order = get_wcmp_vendor_orders(array('order_id' => $order_id));
            if (!empty($vendor_orders_in_order)) {
                $commission_ids = wp_list_pluck($vendor_orders_in_order, 'commission_id');
                if ($commission_ids && is_array($commission_ids)) {
                    foreach ($commission_ids as $commission_id) {
                        wp_delete_post($commission_id);
                    }
                }
            }
            $wpdb->delete($wpdb->prefix . 'wcmp_vendor_orders', array('order_id' => $order_id), array('%d'));
            delete_post_meta($order_id, '_commissions_processed');
        }
    }

    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present
     *
     * @access public
     * @return void
     */
    public function load_plugin_textdomain() {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'wcmp-paypal-adaptive-gateway');
        load_textdomain('wcmp-paypal-adaptive-gateway', WP_LANG_DIR . '/wcmp-paypal-adaptive-gateway/wcmp-paypal-adaptive-gateway-' . $locale . '.mo');
        load_plugin_textdomain('wcmp-paypal-adaptive-gateway', false, plugin_basename(dirname(dirname(__FILE__))) . '/languages');
    }

    public function load_class($class_name = '') {
        if ('' != $class_name && '' != $this->token) {
            require_once ('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }

    // End load_class()

    /** Cache Helpers ******************************************************** */

    /**
     * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
     *
     * @access public
     * @return void
     */
    function nocache() {
        if (!defined('DONOTCACHEPAGE'))
            define("DONOTCACHEPAGE", "true");
        // WP Super Cache constant
    }

}
