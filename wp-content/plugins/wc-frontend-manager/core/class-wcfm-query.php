<?php
/**
 * Contains the query functions for WCFM which alter the front-end post queries and loops
 *
 * @class 		WCFM_Query
 * @version		1.0.0
 * @package		wcfm/core/
 * @category	Class
 * @author 		WC Lovers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WCFM_Query Class.
 */
class WCFM_Query {

	/** @public array Query vars to add to wp */
	public $query_vars = array();

	/**
	 * Constructor for the query class. Hooks in methods.
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_endpoints' ) );
		if ( ! is_admin() ) {
			//add_action( 'wp_loaded', array( $this, 'get_errors' ), 20 );
			add_filter( 'query_vars', array( $this, 'add_query_vars'), 0 );
			add_action( 'parse_request', array( $this, 'parse_request'), 0 );
			//add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
			//add_action( 'wp', array( $this, 'remove_product_query' ) );
			//add_action( 'wp', array( $this, 'remove_ordering_args' ) );
		}
		$this->init_query_vars();
	}

	/**
	 * Get any errors from querystring.
	 */
	public function get_errors() {
		if ( ! empty( $_GET['wc_error'] ) && ( $error = sanitize_text_field( $_GET['wc_error'] ) ) && ! wc_has_notice( $error, 'error' ) ) {
			wc_add_notice( $error, 'error' );
		}
	}

	/**
	 * Init query vars by loading options.
	 */
	public function init_query_vars() {
		
		$wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );
		
		// Query vars to add to WP.
		$this->query_vars = apply_filters( 'wcfm_query_vars', array( 
			'wcfm-products'                 => ! empty( $wcfm_modified_endpoints['wcfm-products'] ) ? $wcfm_modified_endpoints['wcfm-products'] : 'wcfm-products',
			'wcfm-products-manage'          => ! empty( $wcfm_modified_endpoints['wcfm-products-manage'] ) ? $wcfm_modified_endpoints['wcfm-products-manage'] : 'wcfm-products-manage',
			'wcfm-stock-manage'             => ! empty( $wcfm_modified_endpoints['wcfm-stock-manage'] ) ? $wcfm_modified_endpoints['wcfm-stock-manage'] : 'wcfm-stock-manage',
			
			// Import / Export
			'wcfm-products-import'          => ! empty( $wcfm_modified_endpoints['wcfm-products-import'] ) ? $wcfm_modified_endpoints['wcfm-products-import'] : 'wcfm-products-import',
			'wcfm-products-export'          => ! empty( $wcfm_modified_endpoints['wcfm-products-export'] ) ? $wcfm_modified_endpoints['wcfm-products-export'] : 'wcfm-products-export',
			
			// Coupon
			'wcfm-coupons'                  => ! empty( $wcfm_modified_endpoints['wcfm-coupons'] ) ? $wcfm_modified_endpoints['wcfm-coupons'] : 'wcfm-coupons',
			'wcfm-coupons-manage'           => ! empty( $wcfm_modified_endpoints['wcfm-coupons-manage'] ) ? $wcfm_modified_endpoints['wcfm-coupons-manage'] : 'wcfm-coupons-manage',
			
			// Order
			'wcfm-orders'                   => ! empty( $wcfm_modified_endpoints['wcfm-orders'] ) ? $wcfm_modified_endpoints['wcfm-orders'] : 'wcfm-orders',
			'wcfm-orders-details'           => ! empty( $wcfm_modified_endpoints['wcfm-orders-details'] ) ? $wcfm_modified_endpoints['wcfm-orders-details'] : 'wcfm-orders-details',
			
			// Reports
			'wcfm-reports-sales-by-date'    => ! empty( $wcfm_modified_endpoints['wcfm-reports-sales-by-date'] ) ? $wcfm_modified_endpoints['wcfm-reports-sales-by-date'] : 'wcfm-reports-sales-by-date',
			'wcfm-reports-out-of-stock'     => ! empty( $wcfm_modified_endpoints['wcfm-reports-out-of-stock'] ) ? $wcfm_modified_endpoints['wcfm-reports-out-of-stock'] : 'wcfm-reports-out-of-stock',
			
			// WCFMu Reports
			'wcfm-reports-sales-by-product' => ! empty( $wcfm_modified_endpoints['wcfm-reports-sales-by-product'] ) ? $wcfm_modified_endpoints['wcfm-reports-sales-by-product'] : 'wcfm-reports-sales-by-product',
			'wcfm-reports-coupons-by-date'  => ! empty( $wcfm_modified_endpoints['wcfm-reports-coupons-by-date'] ) ? $wcfm_modified_endpoints['wcfm-reports-coupons-by-date'] : 'wcfm-reports-coupons-by-date',
			'wcfm-reports-low-in-stock'     => ! empty( $wcfm_modified_endpoints['wcfm-reports-low-in-stock'] ) ? $wcfm_modified_endpoints['wcfm-reports-low-in-stock'] : 'wcfm-reports-low-in-stock',
			
			// Profile
			'wcfm-profile'                  => ! empty( $wcfm_modified_endpoints['wcfm-profile'] ) ? $wcfm_modified_endpoints['wcfm-profile'] : 'wcfm-profile',
			
			// Settings
			'wcfm-settings'                 => ! empty( $wcfm_modified_endpoints['wcfm-settings'] ) ? $wcfm_modified_endpoints['wcfm-settings'] : 'wcfm-settings',
			'wcfm-capability'               => ! empty( $wcfm_modified_endpoints['wcfm-capability'] ) ? $wcfm_modified_endpoints['wcfm-capability'] : 'wcfm-capability',
			
			// Knowledgebase
			'wcfm-knowledgebase'            => ! empty( $wcfm_modified_endpoints['wcfm-knowledgebase'] ) ? $wcfm_modified_endpoints['wcfm-knowledgebase'] : 'wcfm-knowledgebase',
			'wcfm-knowledgebase-manage'     => ! empty( $wcfm_modified_endpoints['wcfm-knowledgebase-manage'] ) ? $wcfm_modified_endpoints['wcfm-knowledgebase-manage'] : 'wcfm-knowledgebase-manage',
			
			// Notice
			'wcfm-notices'                 => ! empty( $wcfm_modified_endpoints['wcfm-notices'] ) ? $wcfm_modified_endpoints['wcfm-notices'] : 'wcfm-notices',
			'wcfm-notice-manage'           => ! empty( $wcfm_modified_endpoints['wcfm-notice-manage'] ) ? $wcfm_modified_endpoints['wcfm-notice-manage'] : 'wcfm-notice-manage',
			'wcfm-notice-view'             => ! empty( $wcfm_modified_endpoints['wcfm-notice-view'] ) ? $wcfm_modified_endpoints['wcfm-notice-view'] : 'wcfm-notice-view',
			
			// Messages
			'wcfm-messages'                => ! empty( $wcfm_modified_endpoints['wcfm-messages'] ) ? $wcfm_modified_endpoints['wcfm-messages'] : 'wcfm-messages',
		) );
	}

	/**
	 * Get page title for an endpoint.
	 * @param  string
	 * @return string
	 */
	public function get_endpoint_title( $endpoint ) {
		global $wp, $WCFM;

		switch ( $endpoint ) {
			case 'wcfm-products' :
				$title = __( 'Products Dashboard', 'wc-frontend-manager' );
			break;
			case 'wcfm-products-manage' :
				$product = '';
				if( !empty($wp->query_vars['wcfm-products-manage']) ) $product = get_post( absint($wp->query_vars['wcfm-products-manage']) );
				$title = ( $product ) ? sprintf( __( 'Product Manager -%s', 'wc-frontend-manager' ), $product->post_title ) : __( 'Product Manager', 'wc-frontend-manager' );
			break;
			case 'wcfm-stock-manage' :
				$title = __( 'Products Stock Manager', 'wc-frontend-manager' );
			break;
			case 'wcfm-products-import' :
				$title = __( 'Products Import', 'wc-frontend-manager' );
			break;
			case 'wcfm-products-export' :
				$title = __( 'Products Export', 'wc-frontend-manager' );
			break;
			case 'wcfm-coupons' :
				$title = __( 'Coupons Dashboard', 'wc-frontend-manager' );
			break;
			case 'wcfm-coupons-manage' :
				$coupon = '';
				if( !empty($wp->query_vars['wcfm-coupons-manage']) ) $coupon = get_post( absint($wp->query_vars['wcfm-coupons-manage']) );
				$title = ( $coupon ) ? sprintf( __( 'Coupon Manager -%s', 'wc-frontend-manager' ), $coupon->post_title ) : __( 'Coupon Manager', 'wc-frontend-manager' );
			break;
			case 'wcfm-orders' :
				$title = __( 'Orders Dashboard', 'wc-frontend-manager' );
			break;
			case 'wcfm-orders-details' :
				$order = wc_get_order( $wp->query_vars['wcfm-orders-details'] );
				$title = ( $order ) ? sprintf( __( 'Order Details #%s', 'wc-frontend-manager' ), $order->get_order_number() ) : __( 'Order Details', 'wc-frontend-manager' );
			break;
			case 'wcfm-reports-sales-by-date' :
				$title = __( 'Reports - Sales by Date', 'wc-frontend-manager' );
			break;
			case 'wcfm-reports-sales-by-product' :
				$title = __( 'Reports - Sales by Product', 'wc-frontend-manager' );
			break;
			case 'wcfm-reports-coupons-by-date' :
				$title = __( 'Reports - Coupons by Date', 'wc-frontend-manager' );
			break;
			case 'wcfm-reports-out-of-stock' :
				$title = __( 'Reports - Out of Stock', 'wc-frontend-manager' );
			break;
			case 'wcfm-reports-low-in-stock' :
				$title = __( 'Reports - Low in Stock', 'wc-frontend-manager' );
			break; 
			case 'wcfm-analytics' :
				$title = __( 'Analytics', 'wc-frontend-manager' );
			break;
			case 'wcfm-profile' :
				$title = __( 'Profile', 'wc-frontend-manager' );
			break;
			case 'wcfm-settings' :
				$title = __( 'Settings', 'wc-frontend-manager' );
			break;
			case 'wcfm-knowledgebase' :
				$title = __( 'Knowledgebase', 'wc-frontend-manager' );
			break;
		  case 'wcfm-knowledgebase-manage' :
		  	$title = __( 'Knowledgebase Manager', 'wc-frontend-manager' );
			break;
			case 'wcfm-notices' :
				$title = __( 'Notice Dashboard', 'wc-frontend-manager' );
			break;
			case 'wcfm-notice-manage' :
				$title = __( 'Notice Manager', 'wc-frontend-manager' );
			break;
			case 'wcfm-notice-view' :
				$title = __( 'Notice', 'wc-frontend-manager' );
			break;
			case 'wcfm-messages' :
				$title = __( 'Message Dashboard', 'wc-frontend-manager' );
			break;
			default :
				$title = apply_filters( 'wcfm_endpoint_' . $endpoint . '_title', '' );
			break;
		}
		
		$title = apply_filters( 'wcfm_endpoint_title', $title, $endpoint );

		return $title;
	}

	/**
	 * Endpoint mask describing the places the endpoint should be added.
	 *
	 * @since 1.0.0
	 * @return int
	 */
	protected function get_endpoints_mask() {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$page_on_front     = get_option( 'page_on_front' );
			$wc_frontend_manager_page_id = get_option( 'wc_frontend_manager_page_id' );

			if ( in_array( $page_on_front, array( $wc_frontend_manager_page_id ) ) ) {
				return EP_ROOT | EP_PAGES;
			}
		}

		return EP_PAGES;
	}

	/**
	 * Add endpoints for query vars.
	 */
	public function add_endpoints() {
		$mask = $this->get_endpoints_mask();

		foreach ( $this->query_vars as $key => $var ) {
			if ( ! empty( $var ) ) {
				add_rewrite_endpoint( $var, $mask );
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
	public function add_query_vars( $vars ) {
		foreach ( $this->query_vars as $key => $var ) {
			$vars[] = $key;
		}
		return $vars;
	}

	/**
	 * Get query vars.
	 *
	 * @return array
	 */
	public function get_query_vars() {
		return $this->query_vars;
	}

	/**
	 * Get query current active query var.
	 *
	 * @return string
	 */
	public function get_current_endpoint() {
		global $wp;
		foreach ( $this->get_query_vars() as $key => $value ) {
			if ( isset( $wp->query_vars[ $key ] ) ) {
				return $key;
			}
		}
		return '';
	}

	/**
	 * Parse the request and look for query vars - endpoints may not be supported.
	 */
	public function parse_request() {
		global $wp;

		// Map query vars to their keys, or get them if endpoints are not supported
		foreach ( $this->query_vars as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) {
				$wp->query_vars[ $key ] = $_GET[ $var ];
			}

			elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
	}
}