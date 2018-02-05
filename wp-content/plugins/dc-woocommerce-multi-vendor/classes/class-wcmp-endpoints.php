<?php

/**
 * Define Endpoints of WCMp
 * @version 2.5.4
 * @author WC Marketplace
 */
class WCMp_Endpoints {

    /** @public array Query vars to add to wp */
    public $wcmp_query_vars = array();

    /**
     * Class Constructor
     */
    function __construct() {
        add_action('init', array(&$this, 'add_wcmp_endpoints'), 15);
        if (!is_admin()) {
            add_filter('query_vars', array($this, 'add_wcmp_query_vars'), 0);
            add_action('parse_request', array($this, 'wcmp_parse_request'), 0);
            add_action( 'pre_get_posts', array( &$this, 'wcmp_pre_get_posts' ) );
        }
        $this->init_wcmp_query_vars();
    }

    /**
     * Init query vars by loading options.
     */
    public function init_wcmp_query_vars() {
        global $WCMp;
        // Query vars to add to WP.
        $this->wcmp_query_vars = apply_filters('wcmp_endpoints_query_vars', array(
            'vendor-announcements' => array(
                'label' => __('Vendor Announcements', 'dc-woocommerce-multi-vendor'),
                'endpoint' => get_wcmp_vendor_settings('wcmp_vendor_announcements_endpoint', 'vendor', 'general', 'vendor-announcements')
            )
            , 'shop-front' => array(
                'label' => __('Shop Front', 'dc-woocommerce-multi-vendor'),
                'endpoint' => get_wcmp_vendor_settings('wcmp_store_settings_endpoint', 'vendor', 'general', 'shop-front')
            )
            , 'vendor-billing' => array(
                'label' => __('Vendor Billing', 'dc-woocommerce-multi-vendor'),
                'endpoint' => get_wcmp_vendor_settings('wcmp_vendor_billing_endpoint', 'vendor', 'general', 'vendor-billing')
            )
            , 'vendor-policies' => array(
                'label' => __('Vendor Policies', 'dc-woocommerce-multi-vendor'),
                'endpoint' => get_wcmp_vendor_settings('wcmp_vendor_policies_endpoint', 'vendor', 'general', 'vendor-policies')
            )
            , 'vendor-shipping' => array(
                'label' => __('Vendor Shipping', 'dc-woocommerce-multi-vendor'),
                'endpoint' => get_wcmp_vendor_settings('wcmp_vendor_shipping_endpoint', 'vendor', 'general', 'vendor-shipping')
            )
            , 'vendor-report' => array(
                'label' => __('Vendor Report', 'dc-woocommerce-multi-vendor'),
                'endpoint' => get_wcmp_vendor_settings('wcmp_vendor_report_endpoint', 'vendor', 'general', 'vendor-report')
            )
            , 'vendor-orders' => array(
                'label' => __('Vendor Orders', 'dc-woocommerce-multi-vendor'),
                'endpoint' => get_wcmp_vendor_settings('wcmp_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders')
            )
            , 'vendor-withdrawal' => array(
                'label' => __('Vendor Withdrawals', 'dc-woocommerce-multi-vendor'),
                'endpoint' => get_wcmp_vendor_settings('wcmp_vendor_withdrawal_endpoint', 'vendor', 'general', 'vendor-withdrawal')
            )
            , 'transaction-details' => array(
                'label' => __('Transaction Details', 'dc-woocommerce-multi-vendor'),
                'endpoint' => get_wcmp_vendor_settings('wcmp_transaction_details_endpoint', 'vendor', 'general', 'transaction-details')
            )
            , 'vendor-knowledgebase' => array(
                'label' => __('Vendor Knowledgebase', 'dc-woocommerce-multi-vendor'),
                'endpoint' => get_wcmp_vendor_settings('wcmp_vendor_knowledgebase_endpoint', 'vendor', 'general', 'vendor-knowledgebase')
            )
        ));
    }

    /**
     * Endpoint mask describing the places the endpoint should be added.
     *
     * @since 2.6.2
     * @return int
     */
    protected function get_wcmp_endpoints_mask() {
        if ('page' === get_option('show_on_front')) {
            $page_on_front = get_option('page_on_front');
            if (in_array($page_on_front, array(wcmp_vendor_dashboard_page_id()))) {
                return EP_ROOT | EP_PAGES;
            }
        }

        return EP_PAGES;
    }

    /**
     * Add endpoints for query vars.
     */
    public function add_wcmp_endpoints() {
        $this->init_wcmp_query_vars();
        $mask = $this->get_wcmp_endpoints_mask();
        foreach ($this->wcmp_query_vars as $key => $var) {
            if (!empty($var['endpoint'])) {
                add_rewrite_endpoint($var['endpoint'], $mask);
            }
        }
    }

    /**
     * Add query vars.
     *
     * @access public
     * @param array $vars
     * @return array
     */
    public function add_wcmp_query_vars($vars) {
        foreach ($this->wcmp_query_vars as $key => $var) {
            $vars[] = $key;
        }
        return $vars;
    }

    /**
     * Parse the request and look for query vars - endpoints may not be supported.
     */
    public function wcmp_parse_request() {
        global $wp;
        // Map query vars to their keys, or get them if endpoints are not supported
        foreach ($this->wcmp_query_vars as $key => $var) {
            if (isset($_GET[$var['endpoint']])) {
                $wp->query_vars[$key] = $_GET[$var['endpoint']];
            } elseif (isset($wp->query_vars[$var['endpoint']])) {
                $wp->query_vars[$key] = $wp->query_vars[$var['endpoint']];
            }
        }
    }
    /**
     * Fix Vendor dashboard end points on home page
     * @param Object $q
     */
    public function wcmp_pre_get_posts($q){
        // Fix for endpoints on the homepage
        if ( $q->is_home() && 'page' === get_option( 'show_on_front' ) && absint( get_option( 'page_on_front' ) ) !== absint( $q->get( 'page_id' ) ) ) {
            $_query = wp_parse_args( $q->query );
            if ( ! empty( $_query ) && array_intersect( array_keys( $_query ), array_keys( $this->wcmp_query_vars ) ) ) {
                $q->is_page     = true;
                $q->is_home     = false;
                $q->is_singular = true;
                $q->set( 'page_id', (int) get_option( 'page_on_front' ) );
                add_filter( 'redirect_canonical', '__return_false' );
            }
        }
    }
    
    public function get_current_endpoint() {
        global $wp;
        foreach ($this->wcmp_query_vars as $key => $value) {
            if (isset($wp->query_vars[$key])) {
                return $key;
            }
        }
        return '';
    }

}
