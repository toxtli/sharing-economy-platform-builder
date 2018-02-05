<?php

class WCMp_Stripe_Gateway_Admin {

    public $settings;

    public function __construct() {
        //admin script and style
        add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'));
        add_action('wcmp_stripe_gateway_dualcube_admin_footer', array(&$this, 'dualcube_admin_footer_for_wcmp_stripe_gateway'));
        if (class_exists('WCMp')) {
            add_filter('wcmp_tabsection_payment', array(&$this, 'wcmp_tabsection_payment_callback'));
            add_action('settings_page_payment_stripe_gateway_tab_init', array(&$this, 'payment_stripe_gateway_tab_init'), 10, 5);
        }
    }
    /**
     * Add setting option to WCMp for stripe
     * @param array $submenue_tab
     * @return array
     */
    function wcmp_tabsection_payment_callback($submenue_tab) {
        $submenue_tab['stripe_gateway'] = __('Stripe Gateway', 'marketplace-stripe-gateway');
        return $submenue_tab;
    }
    /**
     * Create stripe option in WCMp setting page
     * @global type $WCMp_Stripe_Gateway
     * @param string $tab
     * @param string $subsection
     */
    function payment_stripe_gateway_tab_init($tab, $subsection) {
        global $WCMp_Stripe_Gateway;
        $this->load_class("settings-{$tab}-{$subsection}", $WCMp_Stripe_Gateway->plugin_path, $WCMp_Stripe_Gateway->token);
        new WCMp_Payment_Stripe_Gateway_Settings_Gneral($tab, $subsection);
    }

    function load_class($class_name = '') {
        global $WCMp_Stripe_Gateway;

        if ('' != $class_name) {
            require_once ($WCMp_Stripe_Gateway->plugin_path . '/admin/class-' . esc_attr($WCMp_Stripe_Gateway->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }

// End load_class()

    function dualcube_admin_footer_for_wcmp_stripe_gateway() {
        global $WCMp_Stripe_Gateway;
        ?>
        <div style="clear: both"></div>
        <div id="dc_admin_footer">
        <?php _e('Powered by', 'marketplace-stripe-gateway'); ?> <a href="http://dualcube.com" target="_blank"><img src="<?php echo $WCMp_Stripe_Gateway->plugin_url . '/assets/images/dualcube.png'; ?>"></a><?php _e('Dualcube', 'marketplace-stripe-gateway'); ?> &copy; <?php echo date('Y'); ?>
        </div>
        <?php
    }

    /**
     * Admin Scripts
     */
    public function enqueue_admin_script() {
        global $WCMp_Stripe_Gateway;
        $screen = get_current_screen();

        // Enqueue admin script and stylesheet from here
        if (in_array($screen->id, array('toplevel_page_wcmp-stripe-gateway-setting-admin'))) :
            $WCMp_Stripe_Gateway->library->load_qtip_lib();
            wp_enqueue_script('admin_js', $WCMp_Stripe_Gateway->plugin_url . 'assets/admin/js/admin.js', array('jquery'), $WCMp_Stripe_Gateway->version, true);
            wp_enqueue_style('admin_css', $WCMp_Stripe_Gateway->plugin_url . 'assets/admin/css/admin.css', array(), $WCMp_Stripe_Gateway->version);
        endif;
    }

}
