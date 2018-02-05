<?php

class WCMp_Stripe_Gateway_Frontend {

    public function __construct() {
        //enqueue scripts
        add_action('wp_enqueue_scripts', array(&$this, 'frontend_scripts'));
        //enqueue styles
        add_action('wp_enqueue_scripts', array(&$this, 'frontend_styles'));
        if (class_exists('WCMp')) {
            add_filter('wcmp_transaction_item_totals', array(&$this, 'wcmp_transaction_item_totals'), 10, 2);
        }
    }

    function frontend_scripts() {
        global $WCMp_Stripe_Gateway;
        $frontend_script_path = $WCMp_Stripe_Gateway->plugin_url . 'assets/frontend/js/';
        $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
        $pluginURL = str_replace(array('http:', 'https:'), '', $WCMp_Stripe_Gateway->plugin_url);
        $suffix = defined('WCMp_STRIPE_GATEWAY_SCRIPT_DEBUG') && WCMp_STRIPE_GATEWAY_SCRIPT_DEBUG ? '' : '.min';
        // Enqueue your frontend javascript from here
    }

    function frontend_styles() {
        global $WCMp_Stripe_Gateway;
        $frontend_style_path = $WCMp_Stripe_Gateway->plugin_url . 'assets/frontend/css/';
        $frontend_style_path = str_replace(array('http:', 'https:'), '', $frontend_style_path);
        $suffix = defined('WCMp_STRIPE_GATEWAY_SCRIPT_DEBUG') && WCMp_STRIPE_GATEWAY_SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_style('wcmp_stripe_css', $frontend_style_path . 'frontend.css', array(), $WCMp_Stripe_Gateway->version);
        // Enqueue your frontend stylesheet from here
    }
    /** 
     * Add Trnsaction mode label in WCMp withdrawal recipet
     * @param array $item_totals
     * @param int $transaction_id
     * @return array
     */
    public function wcmp_transaction_item_totals($item_totals, $transaction_id) {
        $transaction_mode = get_post_meta($transaction_id, 'transaction_mode', true);
        if ($transaction_mode == 'stripe_masspay') {
            $item_totals['via'] = array('label' => __('Transaction Mode', 'marketplace-stripe-gateway'), 'value' => __('Stripe Connect', 'marketplace-stripe-gateway'));
        }
        return $item_totals;
    }

}
