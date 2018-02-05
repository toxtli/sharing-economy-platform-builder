<?php

/**
 * WCFM plugin core
 *
 * Marketplace WC Vendors Support
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   1.0.1
 */
 
class WCFM_WCVendors {
	
	private $vendor_id;
	
	public function __construct() {
    global $WCFM;
    
    if( wcfm_is_vendor() ) {
    	
    	$this->vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
    	
    	// Remove Date Range
    	add_filter( 'wcvendors_orders_date_range', array( &$this, 'wcvendors_orders_date_range' ) );
    	
    	// Store Identity
    	add_filter( 'wcfm_store_logo', array( &$this, 'wcvendors_store_logo' ) );
    	add_filter( 'wcfm_store_name', array( &$this, 'wcvendors_store_name' ) );
    	
    	// WCFM Menu Filter
    	add_filter( 'wcfm_menus', array( &$this, 'wcvendors_wcfm_menus' ), 30 );
    	add_filter( 'wcfm_add_new_product_sub_menu', array( &$this, 'wcvendors_add_new_product_sub_menu' ) );
    	add_filter( 'wcfm_add_new_coupon_sub_menu', array( &$this, 'wcvendors_add_new_coupon_sub_menu' ) );
    	
    	// WCFM Home Menu at WCV Dashboard
    	add_action( 'wcvendors_before_links', array( &$this, 'wcfm_home' ), 5 );
    	
    	// WCVendors Menu Fiter
    	add_filter( 'wcv_add_product_url', array( &$this, 'wcvendors_wcfm_add_product_url' ) );
    	add_filter( 'wcv_edit_product_url', array( &$this, 'wcvendors_wcfm_edit_product_url' ) );
    	
    	// WCVendors Pro Menu filter
    	add_filter( 'wcv_dashboard_quick_links', array( &$this, 'wcvendors_wcfm_dashboard_quick_links' ) );
    	add_filter( 'wcv_dashboard_pages_nav', array( &$this, 'wcvendors_wcfm_dashboard_pages_nav' ) );
    	
			// Allow Vendor user to manage product from catalog
			add_filter( 'wcfm_allwoed_user_rols', array( &$this, 'allow_wcvendors_vendor_role' ) );
			
			// Filter Vendor Products
			add_filter( 'wcfm_products_args', array( &$this, 'wcvendors_products_args' ) );
			add_filter( 'get_booking_products_args', array( $this, 'wcvendors_products_args' ) );
			add_filter( 'get_appointment_products_args', array( $this, 'wcvendors_products_args' ) );
			add_filter( 'wpjmp_job_form_products_args', array( &$this, 'wcvendors_products_args' ) );
			add_filter( 'wpjmp_admin_job_form_products_args', array( &$this, 'wcvendors_products_args' ) );
			
			// Listing Filter for specific vendor
    	add_filter( 'wcfm_listing_args', array( $this, 'wcvendors_listing_args' ), 20 );
    	
    	// Booking Filter
			add_filter( 'wcfm_wcb_include_bookings', array( &$this, 'wcvendors_wcb_include_bookings' ) );
			
			// Manage Vendor Product Permissions
			add_filter( 'wcfm_product_types', array( &$this, 'wcvendors_is_allow_product_types'), 100 );
			add_filter( 'wcfm_product_manage_fields_general', array( &$this, 'wcvendors_is_allow_fields_general' ), 100 );
			add_filter( 'wcfm_is_allow_inventory', array( &$this, 'wcvendors_is_allow_inventory' ) );
			add_filter( 'wcfm_is_allow_shipping', array( &$this, 'wcvendors_is_allow_shipping' ) );
			add_filter( 'wcfm_is_allow_tax', array( &$this, 'wcvendors_is_allow_tax' ) );
			add_filter( 'wcfm_is_allow_attribute', array( &$this, 'wcvendors_is_allow_attribute' ) );
			add_filter( 'wcfm_is_allow_variable', array( &$this, 'wcvendors_is_allow_variable' ) );
			add_filter( 'wcfm_is_allow_linked', array( &$this, 'wcvendors_is_allow_linked' ) );
			add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcvendors_product_manage_vendor_association' ), 10, 2 );
			add_action( 'wcfm_geo_locator_default_address', array( &$this, 'wcvendors_geo_locator_default_address' ), 10 );
			
			// Manage Vendor Product Export Permissions - 2.4.2
			add_filter( 'product_type_selector', array( $this, 'wcvendors_filter_product_types' ), 98 );
			add_filter( 'woocommerce_product_export_row_data', array( &$this, 'wcvendors_product_export_row_data' ), 100, 2 );
			
			// Filter Vendor Coupons
			add_filter( 'wcfm_coupons_args', array( &$this, 'wcvendors_coupons_args' ) );
			
			// Manage Vendor Coupon Permission
			add_filter( 'wcfm_coupon_types', array( &$this, 'wcvendors_coupon_types' ) );
			
			// Manage Order Details Permission
			add_filter( 'wcfm_allow_order_details', array( &$this, 'wcvendors_is_allow_order_details' ) );
			add_filter( 'wcfm_allow_order_customer_details', array( &$this, 'wcvendors_is_allow_order_customer_details' ) );
			add_filter( 'wcfm_valid_line_items', array( &$this, 'wcvendors_valid_line_items' ), 10, 3 );
			add_filter( 'wcfm_order_details_shipping_line_item', array( &$this, 'wcvendors_is_allow_order_details_shipping_line_item' ) );
			add_filter( 'wcfm_order_details_tax_line_item', array( &$this, 'wcvendors_is_allow_order_details_tax_line_item' ) );
			add_filter( 'wcfm_order_details_line_total_head', array( &$this, 'wcvendors_is_allow_order_details_line_total_head' ) );
			add_filter( 'wcfm_order_details_line_total', array( &$this, 'wcvendors_is_allow_order_details_line_total' ) );
			add_filter( 'wcfm_order_details_tax_total', array( &$this, 'wcvendors_is_allow_order_details_tax_total' ) );
			add_filter( 'wcfm_order_details_fee_line_item', array( &$this, 'wcvendors_is_allow_order_details_fee_line_item' ) );
			add_filter( 'wcfm_order_details_refund_line_item', array( &$this, 'wcvendors_is_allow_order_details_refund_line_item' ) );
			add_filter( 'wcfm_order_details_coupon_line_item', array( &$this, 'wcvendors_is_allow_order_details_coupon_line_item' ) );
			add_filter( 'wcfm_order_details_total', array( &$this, 'wcvendors_is_allow_wcfm_order_details_total' ) );
			add_action ( 'wcfm_order_details_after_line_total_head', array( &$this, 'wcvendors_after_line_total_head' ) );
			add_action ( 'wcfm_after_order_details_line_total', array( &$this, 'wcvendors_after_line_total' ), 10, 2 );
			add_action ( 'wcfm_order_totals_after_total', array( &$this, 'wcvendors_order_total_commission' ) );
			//add_filter( 'wcfm_generate_csv_url', array( &$this, 'wcvendors_generate_csv_url' ), 10, 2 );
			
			// Report Filter
			add_filter( 'wcfm_report_out_of_stock_query_from', array( &$this, 'wcvendors_report_out_of_stock_query_from' ), 100, 2 );
			add_filter( 'woocommerce_reports_order_statuses', array( &$this, 'wcvendors_reports_order_statuses' ) );
			add_filter( 'woocommerce_dashboard_status_widget_top_seller_query', array( &$this, 'wcvendors_dashboard_status_widget_top_seller_query'), 100 );
			//add_filter( 'woocommerce_reports_get_order_report_data', array( &$this, 'wcvendors_reports_get_order_report_data'), 100 );
		}
  }
  
  // WCFM WCV Date Range
  function wcvendors_orders_date_range( $date ) {
  	global $start_date, $end_date;
  	if( is_wcfm_page() || defined('DOING_AJAX') ) {
  		if( $start_date > strtotime( '-30 DAY', strtotime( date( 'Ymd', current_time( 'timestamp' ) ) ) ) ) {
  			$start_date = strtotime( '-30 DAY', strtotime( date( 'Ymd', current_time( 'timestamp' ) ) ) );
  			$date['after'] = date( 'Y-m-d', strtotime( '-30 DAY', strtotime( date( 'Ymd', current_time( 'timestamp' ) ) ) ) );
  		}
  	}
  	return $date;
  }
  
  // WCFM WCV Store Logo
  function wcvendors_store_logo( $store_logo ) {
  	$user_id = $this->vendor_id;
  	$logo = get_user_meta( $user_id, '_wcv_store_icon_id', true );
  	$logo_image_url = wp_get_attachment_image_src( $logo, 'thumbnail' );

		if ( !empty( $logo_image_url ) ) {
			$store_logo = $logo_image_url[0];
		}
  	return $store_logo;
  }
  
  // WCFM WCV Store Name
  function wcvendors_store_name( $store_name ) {
  	$user_id = $this->vendor_id;
  	$shop_name = get_user_meta( $user_id, 'pv_shop_name', true );
  	if( $shop_name ) $store_name = $shop_name;
  	$shop_link       = WCV_Vendors::get_vendor_shop_page( wp_get_current_user()->user_login );
  	if( $shop_name ) { $store_name = '<a target="_blank" href="' . apply_filters('wcv_vendor_shop_permalink', $shop_link) . '">' . $shop_name . '</a>'; }
  	else { $store_name = '<a target="_blank" href="' . apply_filters('wcv_vendor_shop_permalink', $shop_link) . '">' . __('Shop', 'wc-frontend-manager') . '</a>'; }
  	return $store_name;
  }
  
  // WCFM WCVendors Menu
  function wcvendors_wcfm_menus( $menus ) {
  	global $WCFM;
  	
  	$can_view_orders = WC_Vendors::$pv_options->get_option( 'can_show_orders' );
  	$can_view_sales = WC_Vendors::$pv_options->get_option( 'can_view_frontend_reports' );
  	
  	if( !current_user_can( 'edit_products' ) ) unset( $menus['wcfm-products'] );
  	if( !current_user_can( 'edit_shop_coupons' ) ) unset( $menus['wcfm-coupons'] );
  	if( !$can_view_orders ) unset( $menus['wcfm-orders'] );
  	if( !$can_view_sales ) unset( $menus['wcfm-reports'] );
  	
  	return $menus;
  }
  
  // WCV Add New Product Sub menu
  function wcvendors_add_new_product_sub_menu( $has_new ) {
  	if( !current_user_can( 'edit_products' ) ) $has_new = false;
  	return $has_new;
  }
  
  // WCV Add New Coupon Sub menu
  function wcvendors_add_new_coupon_sub_menu( $has_new ) {
  	if( !current_user_can( 'edit_shop_coupons' ) ) $has_new = false;
  	return $has_new;
  }
  
  // WCFM Home Menu at WCV Dashboard
  function wcfm_home() {
  	global $WCFM;
  	
  	echo '<a href="' . get_wcfm_page() . '"><img class="text_tip" data-tip="' . __( 'WCFM Home', 'wc-frontend-manager' ) . '" id="wcfm_home" src="' . $WCFM->plugin_url . '/assets/images/wcfm-30x30.png" alt="' . __( 'WCFM Home', 'wc-frontend-manager' ) . '" /></a>';
  }
  
  // WCFM WCVendors Add product URL
  function wcvendors_wcfm_add_product_url( $submit_link ) {
  	$can_submit = WC_Vendors::$pv_options->get_option( 'can_submit_products' );
  	if( $can_submit ) $submit_link = get_wcfm_edit_product_url();
  	return $submit_link;
  }
  
  // WCFM WCVendors Edit product URL
  function wcvendors_wcfm_edit_product_url( $edit_link ) {
  	$can_submit = WC_Vendors::$pv_options->get_option( 'can_submit_products' );
  	if( $can_submit ) $edit_link = get_wcfm_products_url();
  	return $edit_link;
  }
  
  // WCFM WCVendors Pro Quick Links
  function wcvendors_wcfm_dashboard_quick_links( $quick_links ) {
  	if( isset( $quick_links['shop_coupon'] ) ) $quick_links['shop_coupon']['url'] = get_wcfm_coupons_manage_url();
  	return $quick_links;
  }
  
  // WCFM WCVendors Pro Dasboard Menu
  function wcvendors_wcfm_dashboard_pages_nav( $navs ) {
  	
  	if( isset( $navs['product'] ) ) $navs['product']['slug'] = get_wcfm_products_url();
  	if( isset( $navs['shop_coupon'] ) ) $navs['shop_coupon']['slug'] = get_wcfm_coupons_url();
  	if( isset( $navs['order'] ) ) $navs['order']['slug'] = get_wcfm_orders_url();
  	
  	return $navs;
  }
  
  function allow_wcvendors_vendor_role( $allowed_roles ) {
  	if( wcfm_is_vendor() ) $allowed_roles[] = 'vendor';
  	return $allowed_roles;
  }
  
  function wcvendors_products_args( $args ) {
  	$args['author'] = $this->vendor_id;
  	return $args;
  }
  
  // WCV Listing args
	function wcvendors_listing_args( $args ) {
  	$args['author'] = $this->vendor_id;
  	return $args;
  }
  
  /**
   * WC Vendors Bookings
   */
  function wcvendors_wcb_include_bookings( ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	$vendor_products = $this->wcv_get_vendor_products( $this->vendor_id );
		
		if( empty($vendor_products) ) return array(0);
		
  	$query = "SELECT ID FROM {$wpdb->posts} as posts
							INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
							WHERE 1=1
							AND posts.post_type IN ( 'wc_booking' )
							AND postmeta.meta_key = '_booking_product_id' AND postmeta.meta_value in (" . implode(',', $vendor_products) . ")";
		
		$vendor_bookings = $wpdb->get_results($query);
		if( empty($vendor_bookings) ) return array(0);
		$vendor_bookings_arr = array();
		foreach( $vendor_bookings as $vendor_booking ) {
			$vendor_bookings_arr[] = $vendor_booking->ID;
		}
		if( !empty($vendor_bookings_arr) ) return $vendor_bookings_arr;
		return array(0);
  }
  
  // Product Types
  function wcvendors_is_allow_product_types( $product_types ) {
  	$types = (array) WC_Vendors::$pv_options->get_option( 'hide_product_types' );
  	foreach ( $product_types as $key => $value ) {
			if ( !empty( $types[ $key ] ) ) {
				unset( $product_types[ $key ] );
			}
		}
		$product_panel = (array) WC_Vendors::$pv_options->get_option( 'hide_product_panel' );
  	if( !empty( $product_panel['attribute'] ) ) unset( $product_types['variable'] );
  	
		return $product_types;
  }
  
  // General Fields
  function wcvendors_is_allow_fields_general( $general_fields ) {
  	$product_misc = (array) WC_Vendors::$pv_options->get_option( 'hide_product_misc' );
  	if( !empty( $product_misc['sku'] ) ) unset( $general_fields['sku'] );
  		
  	return $general_fields;
  }
  
  // Inventory
  function wcvendors_is_allow_inventory( $allow ) {
  	$product_panel = (array) WC_Vendors::$pv_options->get_option( 'hide_product_panel' );
  	if( !empty( $product_panel['inventory'] ) ) return false;
  	return $allow;
  }
  
  // Shipping
  function wcvendors_is_allow_shipping( $allow ) {
  	$product_panel = (array) WC_Vendors::$pv_options->get_option( 'hide_product_panel' );
  	if( !empty( $product_panel['shipping'] ) ) return false;
  	return $allow;
  }
  
  // Tax
  function wcvendors_is_allow_tax( $allow ) {
  	$product_misc = (array) WC_Vendors::$pv_options->get_option( 'hide_product_misc' );
  	if( !empty( $product_misc['taxes'] ) ) return false;
  	return $allow;
  }
  
  // Attributes
  function wcvendors_is_allow_attribute( $allow ) {
  	$product_panel = (array) WC_Vendors::$pv_options->get_option( 'hide_product_panel' );
  	if( !empty( $product_panel['attribute'] ) ) return false;
  	return $allow;
  }
  
  // Variable
  function wcvendors_is_allow_variable( $allow ) {
  	$product_panel = (array) WC_Vendors::$pv_options->get_option( 'hide_product_panel' );
  	if( !empty( $product_panel['attribute'] ) ) return false;
  	$types = (array) WC_Vendors::$pv_options->get_option( 'hide_product_types' );
  	if( !empty( $types['variable'] ) ) return false;
  	return $allow;
  }
  
  // Linked
  function wcvendors_is_allow_linked( $allow ) {
  	$product_panel = (array) WC_Vendors::$pv_options->get_option( 'hide_product_panel' );
  	if( !empty( $product_panel['linked_product'] ) ) return false;
  	return $allow;
  }
  
  // Product Vendor association on Product save
  function wcvendors_product_manage_vendor_association( $new_product_id, $wcfm_products_manage_form_data ) {
  	global $WCFM, $WCMp;
  	
		// Admin Message for Pending Review
		$product_ststua = get_post_status( $new_product_id );
		if( $product_ststua == 'pending' ) {
			$author_id = $this->vendor_id;
			$author_is_admin = 0;
			$author_is_vendor = 1;
			$message_to = 0;
			$wcfm_messages = sprintf( __( 'Product awaiting <b>%s</b> for review', 'wc-frontend-manager' ), '<a class="wcfm_dashboard_item_title" href="' . get_wcfm_edit_product_url( $new_product_id ) . '">' . get_the_title( $new_product_id ) . '</a>' );
			$WCFM->frontend->wcfm_send_direct_message( $author_id, $message_to, $author_is_admin, $author_is_vendor, $wcfm_messages, 'product_review' );
		}
  }
  
  // Geo Locator default address- 3.2.8
  function wcvendors_geo_locator_default_address( $address ) {
  	global $WCFM;
  	
  	$user_id = $this->vendor_id;
  	
  	$addr_1  = get_user_meta( $user_id, '_wcv_store_address1', true );
		$addr_2  = get_user_meta( $user_id, '_wcv_store_address2', true );
		$country  = get_user_meta( $user_id, '_wcv_store_country', true );
		$city  = get_user_meta( $user_id, '_wcv_store_city', true );
		$state  = get_user_meta( $user_id, '_wcv_store_state', true );
		$zip  = get_user_meta( $user_id, '_wcv_store_postcode', true );
				
  	$address  = $addr_1;
		if( $addr_2 ) $address  .= ' ' . $addr_2;
		if( $city ) $address  .= ', ' . $city;
		if( $state ) $address  .= ', ' . $state;
		if( $zip ) $address  .= ' ' . $zip;
		if( $country ) $address  .= ', ' . $country;
  	
  	return $address;
  }
  
  // Remove WC Vendors Buggy filter
  function wcvendors_filter_product_types( $types ) {
  	remove_all_filters( 'product_type_selector', 99 );
  	return $types;
  }
  
  // Product Export Data Filter
  function wcvendors_product_export_row_data( $row, $product ) {
  	global $WCFM;
  	
  	$user_id = $this->vendor_id;
  	
  	$products = $this->wcv_get_vendor_products();
		
		if( !in_array( $product->get_ID(), $products ) ) return array();
		
		return $row;
  }
  
  // Coupons Args
  function wcvendors_coupons_args( $args ) {
  	if( wcfm_is_vendor() ) $args['author'] = $this->vendor_id;
  	return $args;
  }
  
  // Coupon Types
  function wcvendors_coupon_types( $types ) {
  	$wcmp_coupon_types = array( 'percent', 'fixed_product' );
  	foreach( $types as $type => $label ) 
  		if( !in_array( $type, $wcmp_coupon_types ) ) unset( $types[$type] );
  	return $types;
  } 
  
  // Order Status details
  function wcvendors_is_allow_order_details( $allow ) {
  	return false;
  }
  
  // Order Customer Details
  function wcvendors_is_allow_order_customer_details( $allow ) {
  	$can_view_emails = WC_Vendors::$pv_options->get_option( 'can_view_order_emails' );
  	if( !$can_view_emails ) return false;
  	return $allow;
  }
  
  // Filter Order Details Line Items as Per Vendor
  function wcvendors_valid_line_items( $items, $order_id ) {
  	$valid_items = (array) WCV_Queries::get_products_for_order( $order_id );
  	
  	$valid = array();
  	foreach ($items as $key => $value) {
			if ( in_array( $value->get_variation_id(), $valid_items) || in_array( $value->get_product_id(), $valid_items ) ) {
				$valid[$key] = $value;
			}
		}
  	return $valid;
  }
  
  // Order Details Shipping Line Item
  function wcvendors_is_allow_order_details_shipping_line_item( $allow ) {
  	//if ( !WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) $allow = false;
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Tax Line Item
  function wcvendors_is_allow_order_details_tax_line_item( $allow ) {
  	//if ( !WC_Vendors::$pv_options->get_option( 'give_tax' ) ) $allow = false;
  	$allow = false;
  	return $allow;
  }
  
  // Order Total Line Item Head
  function wcvendors_is_allow_order_details_line_total_head( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Total Line Item
  function wcvendors_is_allow_order_details_line_total( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Tax Total
  function wcvendors_is_allow_order_details_tax_total( $allow ) {
  	//if ( !WC_Vendors::$pv_options->get_option( 'give_tax' ) ) $allow = false;
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Fee Line Item
  function wcvendors_is_allow_order_details_fee_line_item( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Coupon Line Item
  function wcvendors_is_allow_order_details_coupon_line_item( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Refunded Line Item
  function wcvendors_is_allow_order_details_refund_line_item( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Total
  function wcvendors_is_allow_wcfm_order_details_total( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // WCVendors After Order Total Line Head
  function wcvendors_after_line_total_head( $order ) {
  	global $WCFM;
  	?>
		<th class="line_cost sortable" data-sort="float"><?php _e( 'Commission', 'wc-frontend-manager' ); ?></th>
  	<?php
  	if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) {
  		?>
  		<th class="line_cost sortable no_ipad no_mob" data-sort="float"><?php _e( 'Shipping', 'wc-frontend-manager' ); ?></th>
  		<?php
  	}
  	
  	if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) {
  		?>
  		<th class="line_cost sortable no_ipad no_mob" data-sort="float"><?php _e( 'Tax', 'wc-frontend-manager' ); ?></th>
  		<th class="line_cost sortable no_ipad no_mob"></th>
  		<?php
  	}
  	?>
  	<th class="line_cost sortable no_ipad no_mob"><?php _e( 'Total', 'wc-frontend-manager' ); ?></th>
  	<?php
  }
  
  // WCVendors after Order total Line item
  function wcvendors_after_line_total( $item, $order ) {
  	global $WCFM, $wpdb;
  	$order_currency = $order->get_currency();
  	$commission_rate = WCV_Commission::get_commission_rate( $item['product_id'] );
  	$qty = ( isset( $item['qty'] ) ? esc_html( $item['qty'] ) : '1' );
		$line_total = $item->get_total();
		
		$sql = "
			SELECT total_due as line_total, total_shipping, tax 
			FROM {$wpdb->prefix}pv_commission
			WHERE   (product_id = " . $item['product_id'] . " OR product_id = " . $item['variation_id'] . ")
			AND     order_id = " . $order->get_id() . "
			AND     vendor_id = " . $this->vendor_id;
		$order_line_due = $wpdb->get_results( $sql );
		if( !empty( $order_line_due ) ) {
			$line_total += $order_line_due[0]->total_shipping; 
		  $line_total += $order_line_due[0]->tax;
		?>
			<td class="line_cost" width="1%">
				<div class="view"><?php echo wc_price( $order_line_due[0]->line_total, array( 'currency' => $order_currency ) ); ?></div>
			</td>
			<?php if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) { ?>
			<td class="line_cost no_ipad no_mob" width="1%">
				<div class="view"><?php echo wc_price( $order_line_due[0]->total_shipping, array( 'currency' => $order_currency ) ); ?></div>
			</td>
			<?php } ?>
			<?php if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) { ?>
			<td class="line_cost no_ipad no_mob">
				<div class="view"><?php echo wc_price( $order_line_due[0]->tax, array( 'currency' => $order_currency ) ); ?></div>
			</td>
			<td class="line_cost no_ipad no_mob">
				<div class="view"></div>
			</td>
			<?php } ?>
		<?php
		} else {
			?>
			<td class="line_cost" width="1%">
				<div class="view"><?php echo wc_price( 0, array( 'currency' => $order_currency ) ); ?></div>
			</td>
			<?php if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) { ?>
			<td class="line_cost" width="1%">
				<div class="view"><?php echo wc_price( 0, array( 'currency' => $order_currency ) ); ?></div>
			</td>
			<?php } ?>
			<?php if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) { ?>
			<td class="line_cost" width="1%">
				<div class="view"><?php echo wc_price( 0, array( 'currency' => $order_currency ) ); ?></div>
			</td>
			<td class="line_cost no_ipad no_mob">
				<div class="view"></div>
			</td>
			<?php } ?>
			<?php
		}
		?>
		<td class="line_cost total_cost no_ipad no_mob"><?php echo wc_price( $line_total, array( 'currency' => $order_currency ) ); ?></td>
		<?php
  }
  
  // WCVendors Order Total Commission
  function wcvendors_order_total_commission( $order_id ) {
  	global $WCFM, $wpdb;
  	$gross_sale_order = $WCFM->wcfm_vendor_support->wcfm_get_gross_sales_by_vendor( $this->vendor_id, '', '', $order_id );
  	$order = wc_get_order( $order_id );
  	$order_currency = $order->get_currency();
  	
  	$sql = "
  	SELECT SUM(total_due) as line_total,
	   SUM(total_shipping) as shipping,
       SUM(tax) as tax
       FROM {$wpdb->prefix}pv_commission
       WHERE order_id = " . $order_id . "
       AND vendor_id = " . $this->vendor_id;
    $order_due = $wpdb->get_results( $sql );
  	$total = $order_due[0]->line_total; 
  	if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) {
  		$total += $order_due[0]->shipping; 
  	}
  	if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) {
  		$total += $order_due[0]->tax; 
  	}
		?>
		<tr>
			<td class="label"><?php _e( 'Line Commission', 'wc-frontend-manager' ); ?>:</td>
			<td>
				
			</td>
			<td class="total">
				<div class="view"><?php echo wc_price( $order_due[0]->line_total, array( 'currency' => $order_currency ) ); ?></div>
			</td>
		</tr>
		<?php if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) { ?>
		<tr>
			<td class="label"><?php _e( 'Shipping', 'wc-frontend-manager' ); ?>:</td>
			<td>
				
			</td>
			<td class="total">
				<div class="view"><?php echo wc_price( $order_due[0]->shipping, array( 'currency' => $order_currency ) ); ?></div>
			</td>
		</tr>
		<?php } ?>
		<?php if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) { ?>
		<tr>
			<td class="label"><?php _e( 'Tax', 'wc-frontend-manager' ); ?>:</td>
			<td>
				
			</td>
			<td class="total">
				<div class="view"><?php echo wc_price( $order_due[0]->tax, array( 'currency' => $order_currency ) ); ?></div>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td class="label"><?php _e( 'Total Earning', 'wc-frontend-manager' ); ?>:</td>
			<td>
				
			</td>
			<td class="total">
				<div class="view"><?php echo wc_price( $total, array( 'currency' => $order_currency ) ); ?></div>
			</td>
		</tr>
		<tr>
			<td class="label"><?php _e( 'Gross Total', 'wc-frontend-manager' ); ?>:</td>
			<td>
				
			</td>
			<td class="total">
				<div class="view">
					<?php 
					echo wc_price( $gross_sale_order, array( 'currency' => $order_currency ) ); 
					?>
				</div>
			</td>
		</tr>
		<?php
  }
  
  // CSV Export URL
  function wcvendors_generate_csv_url( $url, $order_id ) {
  	//$url = admin_url('admin.php?action=wcvendors_csv_download_per_order&orders_for_product=' . $order_id . '&nonce=' . wp_create_nonce('wcmp_vendor_csv_download_per_order'));
  	return $url;
  }
  
  // Report Vendor Filter
  function wcvendors_report_out_of_stock_query_from( $query_from, $stock ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	$user_id = $this->vendor_id;
  	
  	$query_from = "FROM {$wpdb->posts} as posts
			INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
			INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
			WHERE 1=1
			AND posts.post_type IN ( 'product', 'product_variation' )
			AND posts.post_status = 'publish'
			AND posts.post_author = {$user_id}
			AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
			AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$stock}'
		";
		
		return $query_from;
  }
  
  // Report Order Data Status
  function wcvendors_reports_order_statuses( $order_status ) {
  	$order_status = array( 'completed', 'processing' );
  	return $order_status;
  }
  
  // WCVendor dashboard top seller query
  function wcvendors_dashboard_status_widget_top_seller_query( $query ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	$user_id = $this->vendor_id;
  	
  	$products = $this->wcv_get_vendor_products();
		
		if( !empty($products) )
		  $query['where'] .= "AND order_item_meta_2.meta_value in (" . implode( ',', $products ) . ")";
  	
  	return $query;
  }
  
  // Report Data Filter as per Vendor
  function wcvendors_reports_get_order_report_data( $result ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	$user_id = $this->vendor_id;
  	
  	$products = $this->wcv_get_vendor_products();
  	
  	if( !empty( $result ) && is_array( $result ) ) {
  		foreach( $result as $result_key => $result_val ) {
  			if( !in_array( $result_val->product_id, $products ) ) unset( $result[$result_key] );
  		}
  	}
  	
  	return $result;
  }
  
  /**
   * WC Vendors current venndor products
   */
  function wcv_get_vendor_products( $vendor_id = 0 ) {
  	if( !$vendor_id ) $vendor_id = $this->vendor_id;
  	
  	$args = array(
							'posts_per_page'   => -1,
							'offset'           => 0,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'DESC',
							'include'          => '',
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'product',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array('draft', 'pending', 'publish'),
							'suppress_filters' => 0 
						);
		
		$args = apply_filters( 'wcfm_products_args', $args );
		$products = get_posts( $args );
		$products_arr = array(0);
		if(!empty($products)) {
			foreach($products as $product) {
				$products_arr[] = $product->ID;
			}
		}
		
		return $products_arr;
  }
}