<?php
/**
 * WCFM plugin core
 *
 * Plugin Capability Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   2.3.1
 */
 
class WCFM_Capability {
	
	private $wcfm_capability_options = array();

	public function __construct() {
		global $WCFM;
		
		$this->wcfm_capability_options = apply_filters( 'wcfm_capability_options_rules', (array) get_option( 'wcfm_capability_options' ) );
		
		// Menu Filter
		add_filter( 'wcfm_menus', array( &$this, 'wcfmcap_wcfm_menus' ), 500 );
		add_filter( 'wcfm_product_menu', array( &$this, 'wcfmcap_product_menu' ), 500 );
		add_filter( 'wcfm_add_new_product_sub_menu', array( &$this, 'wcfmcap_is_allow_add_products' ), 500 );
		add_filter( 'wcfm_is_allow_add_products', array( &$this, 'wcfmcap_is_allow_add_products' ), 500 );
		add_filter( 'wcfm_add_new_coupon_sub_menu', array( &$this, 'wcfmcap_add_new_coupon_sub_menu' ),500 );
		
		// Manage Product Permission
		add_filter( 'wcfm_is_allow_publish_live_products', array( &$this, 'wcfmcap_is_allow_publish_live_products' ), 500 );
		add_filter( 'wcfm_is_allow_product_limit', array( &$this, 'wcfmcap_is_allow_product_limit' ), 500 );
		add_filter( 'wcfm_products_limit_label', array( &$this, 'wcfmcap_products_limit_label' ), 50 );
		add_filter( 'wcfm_product_types', array( &$this, 'wcfmcap_is_allow_product_types'), 500 );
		add_filter( 'product_type_selector', array( &$this, 'wcfmcap_is_allow_product_types'), 500 ); // WC Product Types
		add_filter( 'wcfm_is_allow_job_package', array( &$this, 'wcfmcap_is_allow_job_package'), 500 );
		add_filter( 'wcfm_product_manage_fields_general', array( &$this, 'wcfmcap_is_allow_fields_general' ), 500 );
		add_filter( 'wcfm_is_allow_inventory', array( &$this, 'wcfmcap_is_allow_inventory' ), 500 );
		add_filter( 'wcfm_is_allow_shipping', array( &$this, 'wcfmcap_is_allow_shipping' ), 500 );
		add_filter( 'wcfm_is_allow_tax', array( &$this, 'wcfmcap_is_allow_tax' ), 500 );
		add_filter( 'wcfm_is_allow_attribute', array( &$this, 'wcfmcap_is_allow_attribute' ), 500 );
		add_filter( 'wcfm_is_allow_variable', array( &$this, 'wcfmcap_is_allow_variable' ), 500 );
		add_filter( 'wcfm_is_allow_linked', array( &$this, 'wcfmcap_is_allow_linked' ), 500 );
		add_filter( 'wcfm_is_allow_catalog', array( &$this, 'wcfmcap_is_allow_catalog' ), 500 );
		
		// Manage Listings Permission
		add_filter( 'wcfm_is_allow_listings', array( &$this, 'wcfmcap_is_allow_listings'), 500 );
		
		// Manage Product Export Permission - 2.4.2
		add_filter( 'woocommerce_product_export_product_default_columns', array( &$this, 'wcfmcap_is_allow_product_columns'), 500 ); // WC Product Columns
		
		// Manage Product Import Permission - 2.4.2
		//add_filter( 'woocommerce_csv_product_import_mapping_options', array( &$this, 'wcfmcap_is_allow_product_columns'), 500 ); // WC Product Columns
		
		// Manage Order Permission
		add_filter( 'wcfm_is_allow_orders', array( &$this, 'wcfmcap_is_allow_orders' ), 500 );
		add_filter( 'wcfm_is_allow_order_status_update', array( &$this, 'wcfmcap_is_allow_order_status_update' ), 500 );
		add_filter( 'wcfm_allow_order_details', array( &$this, 'wcfmcap_is_allow_order_details' ), 500 );
		add_filter( 'wcfm_is_allow_order_details', array( &$this, 'wcfmcap_is_allow_order_details' ), 500 );
		add_filter( 'wcfm_allow_customer_billing_details', array( &$this, 'wcfmcap_is_allow_customer_billing_details' ), 500 );
		add_filter( 'wcfm_allow_customer_shipping_details', array( &$this, 'wcfmcap_is_allow_customer_shipping_details' ), 500 );
		add_filter( 'wcfm_allow_order_customer_details', array( &$this, 'wcfmcap_is_allow_order_customer_details' ), 500 );
		add_filter( 'wcfm_is_allow_export_csv', array( &$this, 'wcfmcap_is_allow_export_csv' ), 500 );
		add_filter( 'wcfm_is_allow_pdf_invoice', array( &$this, 'wcfmcap_is_allow_pdf_invoice' ), 500 );
		add_filter( 'wcfm_is_allow_pdf_packing_slip', array( &$this, 'wcfmcap_is_allow_pdf_packing_slip' ), 500 );
		
		// Manage Reports Permission
		add_filter( 'wcfm_is_allow_reports', array( &$this, 'wcfmcap_is_allow_reports' ), 500 );
		
		// Custom Caps
		add_filter( 'wcfm_is_allow_commission_manage', array( &$this, 'wcfmcap_is_allow_commission_manage' ), 500 );
		add_filter( 'wcfm_allow_wp_admin_view', array( &$this, 'wcfmcap_is_allow_wp_admin_view' ), 500 );
	}
	
	// WCFM wcfmcap Menu
  function wcfmcap_wcfm_menus( $menus ) {
  	global $WCFM;
  	
  	$manage_products = ( isset( $this->wcfm_capability_options['manage_products'] ) ) ? $this->wcfm_capability_options['manage_products'] : 'no';
  	$manage_coupons = ( isset( $this->wcfm_capability_options['manage_coupons'] ) ) ? $this->wcfm_capability_options['manage_coupons'] : 'no';
  	$view_orders  = ( isset( $this->wcfm_capability_options['view_orders'] ) ) ? $this->wcfm_capability_options['view_orders'] : 'no';
  	$view_reports  = ( isset( $this->wcfm_capability_options['view_reports'] ) ) ? $this->wcfm_capability_options['view_reports'] : 'no';
  	$manage_booking = ( isset( $this->wcfm_capability_options['manage_booking'] ) ) ? $this->wcfm_capability_options['manage_booking'] : 'no';
  	
  	if( !current_user_can( 'edit_products' ) || ( $manage_products == 'yes' ) ) unset( $menus['wcfm-products'] );
  	if( !current_user_can( 'edit_shop_coupons' ) || ( $manage_coupons == 'yes' ) ) unset( $menus['wcfm-coupons'] );
  	if( $view_orders == 'yes' ) unset( $menus['wcfm-orders'] );
  	if( $view_reports == 'yes' ) unset( $menus['wcfm-reports'] );
  	if( $manage_booking == 'yes' ) unset( $menus['wcfm-bookings-dashboard'] );
  	
  	return $menus;
  }
  
  // WCFM Product Menu
  function wcfmcap_product_menu( $has_new ) {
  	$manage_products = ( isset( $this->wcfm_capability_options['manage_products'] ) ) ? $this->wcfm_capability_options['manage_products'] : 'no';
  	if( $manage_products == 'yes' ) $has_new = false;
  	if( !current_user_can( 'edit_products' ) ) $has_new = false;
  	return $has_new;
  }
  
  // WCFM wcfmcap Add Products
  function wcfmcap_is_allow_add_products( $allow ) {
  	$manage_products = ( isset( $this->wcfm_capability_options['manage_products'] ) ) ? $this->wcfm_capability_options['manage_products'] : 'no';
  	if( $manage_products == 'yes' ) return false;
  	$add_products = ( isset( $this->wcfm_capability_options['add_products'] ) ) ? $this->wcfm_capability_options['add_products'] : 'no';
  	if( $add_products == 'yes' ) return false;
  	if( !current_user_can( 'edit_products' ) ) return false;
  	return $allow;
  }
  
  // WCV Add New Coupon Sub menu
  function wcfmcap_add_new_coupon_sub_menu( $has_new ) {
  	if( !current_user_can( 'edit_shop_coupons' ) ) $has_new = false;
  	return $has_new;
  }
  
  // WCFM auto publish live products
  function wcfmcap_is_allow_publish_live_products( $allow ) {
  	$publish_live_products = ( isset( $this->wcfm_capability_options['publish_live_products'] ) ) ? $this->wcfm_capability_options['publish_live_products'] : 'no';
  	if( $publish_live_products == 'yes' ) return false;
  	return $allow;
  }
  
  // WCFM wcfmcap Add Products
  function wcfmcap_is_allow_product_limit( $allow ) {
  	$manage_products = ( isset( $this->wcfm_capability_options['manage_products'] ) ) ? $this->wcfm_capability_options['manage_products'] : 'no';
  	if( $manage_products == 'yes' ) return false;
  	$add_products = ( isset( $this->wcfm_capability_options['add_products'] ) ) ? $this->wcfm_capability_options['add_products'] : 'no';
  	if( $add_products == 'yes' ) return false;
  	
  	// Limit Restriction
  	$productlimit = ( isset( $this->wcfm_capability_options['productlimit'] ) ) ? $this->wcfm_capability_options['productlimit'] : '';
  	if( $productlimit ) $productlimit = absint($productlimit);
  	if( $productlimit && ( $productlimit >= 0 ) ) {
  		$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			$count_products  = wcfm_get_user_posts_count( $current_user_id, 'product', 'any' );
			if( $productlimit <= $count_products ) return false;
  	}
  	return $allow;
  }
  
  // WCFM Product Limit Label
  function wcfmcap_products_limit_label( $label ) {
  	
  	$label = __( 'Products Limit: ', 'wc-frontend-manager' );
  	
  	$productlimit = ( isset( $this->wcfm_capability_options['productlimit'] ) ) ? $this->wcfm_capability_options['productlimit'] : '';
  	if( $productlimit ) $productlimit = absint($productlimit);
  	if( $productlimit && ( $productlimit >= 0 ) ) {
  		$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			$count_products  = wcfm_get_user_posts_count( $current_user_id, 'product', 'any' );
			$label .= ' ' . ( $productlimit - $count_products ) . ' ' . __( 'remaining', 'wc-frontend-manager' );
  	} else {
  		$label .= __( 'Unlimited', 'wc-frontend-manager' );
  	}
  	
  	$label = '<span class="wcfm_products_limit_label">' . $label . '</span>';
  	
  	return $label;
  }
	
  // Product Types
  function wcfmcap_is_allow_product_types( $product_types ) {
  	
  	$simple = ( isset( $this->wcfm_capability_options['simple'] ) ) ? $this->wcfm_capability_options['simple'] : 'no';
		$variable = ( isset( $this->wcfm_capability_options['variable'] ) ) ? $this->wcfm_capability_options['variable'] : 'no';
		$grouped = ( isset( $this->wcfm_capability_options['grouped'] ) ) ? $this->wcfm_capability_options['grouped'] : 'no';
		$external = ( isset( $this->wcfm_capability_options['external'] ) ) ? $this->wcfm_capability_options['external'] : 'no';
		$booking = ( isset( $this->wcfm_capability_options['booking'] ) ) ? $this->wcfm_capability_options['booking'] : 'no';
		$accommodation = ( isset( $this->wcfm_capability_options['accommodation'] ) ) ? $this->wcfm_capability_options['accommodation'] : 'no';
		$appointment = ( isset( $this->wcfm_capability_options['appointment'] ) ) ? $this->wcfm_capability_options['appointment'] : 'no';
		$job_package = ( isset( $this->wcfm_capability_options['job_package'] ) ) ? $this->wcfm_capability_options['job_package'] : 'no';
		$resume_package = ( isset( $this->wcfm_capability_options['resume_package'] ) ) ? $this->wcfm_capability_options['resume_package'] : 'no';
		$auction = ( isset( $this->wcfm_capability_options['auction'] ) ) ? $this->wcfm_capability_options['auction'] : 'no';
		$rental = ( isset( $this->wcfm_capability_options['rental'] ) ) ? $this->wcfm_capability_options['rental'] : 'no';
		$subscription = ( isset( $this->wcfm_capability_options['subscription'] ) ) ? $this->wcfm_capability_options['subscription'] : 'no';
		$variable_subscription = ( isset( $this->wcfm_capability_options['variable-subscription'] ) ) ? $this->wcfm_capability_options['variable-subscription'] : 'no';
		$attributes = ( isset( $this->wcfm_capability_options['attributes'] ) ) ? $this->wcfm_capability_options['attributes'] : 'no';
  	
  	if( $simple == 'yes' ) unset( $product_types[ 'simple' ] );
		if( $variable == 'yes' ) unset( $product_types[ 'variable' ] );
		if( $grouped == 'yes' ) unset( $product_types[ 'grouped' ] );
		if( $external == 'yes' ) unset( $product_types[ 'external' ] );
		if( $booking == 'yes' ) unset( $product_types[ 'booking' ] );
		if( $accommodation == 'yes' ) unset( $product_types[ 'accommodation-booking' ] );
		if( $appointment == 'yes' ) unset( $product_types[ 'appointment' ] );
		if( $job_package == 'yes' ) unset( $product_types[ 'job_package' ] );
		if( $resume_package == 'yes' ) unset( $product_types[ 'resume_package' ] );
		if( $auction == 'yes' ) unset( $product_types[ 'auction' ] );
		if( $rental == 'yes' ) unset( $product_types[ 'redq_rental' ] );
		if( $subscription == 'yes' ) unset( $product_types[ 'subscription' ] );
  	if( $variable_subscription == 'yes' ) unset( $product_types[ 'variable-subscription' ] );
  	if( $attributes == 'yes' ) unset( $product_types[ 'variable' ] );
  	if( $attributes == 'yes' ) unset( $product_types[ 'variable-subscription' ] );
		
		return $product_types;
  }
  
  // Job Package
  function wcfmcap_is_allow_job_package( $allow ) {
  	$job_package = ( isset( $this->wcfm_capability_options['job_package'] ) ) ? $this->wcfm_capability_options['job_package'] : 'no';
  	if( $job_package == 'yes' ) return false;
  	return $allow;
  }
  
  // General Fields
  function wcfmcap_is_allow_fields_general( $general_fields ) {
  	//$product_misc = (array) WC_Vendors::$pv_options->get_option( 'hide_product_misc' );
  	//if( !empty( $product_misc['sku'] ) ) unset( $general_fields['sku'] );
  		
  	return $general_fields;
  }
  
  // Inventory
  function wcfmcap_is_allow_inventory( $allow ) {
  	$inventory = ( isset( $this->wcfm_capability_options['inventory'] ) ) ? $this->wcfm_capability_options['inventory'] : 'no';
  	if( $inventory == 'yes' ) return false;
  	return $allow;
  }
  
  // Shipping
  function wcfmcap_is_allow_shipping( $allow ) {
  	$shipping = ( isset( $this->wcfm_capability_options['shipping'] ) ) ? $this->wcfm_capability_options['shipping'] : 'no';
  	if( $shipping == 'yes' ) return false;
  	return $allow;
  }
  
  // Tax
  function wcfmcap_is_allow_tax( $allow ) {
  	$taxes = ( isset( $this->wcfm_capability_options['taxes'] ) ) ? $this->wcfm_capability_options['taxes'] : 'no';
  	if( $taxes == 'yes' ) return false;
  	return $allow;
  }
  
  // Attributes
  function wcfmcap_is_allow_attribute( $allow ) {
  	$attributes = ( isset( $this->wcfm_capability_options['attributes'] ) ) ? $this->wcfm_capability_options['attributes'] : 'no';
  	if( $attributes == 'yes' ) return false;
  	return $allow;
  }
  
  // Variable
  function wcfmcap_is_allow_variable( $allow ) {
  	$attributes = ( isset( $this->wcfm_capability_options['attributes'] ) ) ? $this->wcfm_capability_options['attributes'] : 'no';
  	$variable = ( isset( $this->wcfm_capability_options['variable'] ) ) ? $this->wcfm_capability_options['variable'] : 'no';
  	$variable_subscription = ( isset( $this->wcfm_capability_options['variable-subscription'] ) ) ? $this->wcfm_capability_options['variable-subscription'] : 'no';
  	
  	if( ( $attributes == 'yes' ) && ( $variable == 'yes' ) && ( $variable_subscription == 'yes' ) ) return false;
  	return $allow;
  }
  
  // Linked
  function wcfmcap_is_allow_linked( $allow ) {
  	$linked = ( isset( $this->wcfm_capability_options['linked'] ) ) ? $this->wcfm_capability_options['linked'] : 'no';
  	if( $linked == 'yes' ) return false;
  	return $allow;
  }
  
  // Catalog
  function wcfmcap_is_allow_catalog( $allow ) {
  	$catalog = ( isset( $this->wcfm_capability_options['catalog'] ) ) ? $this->wcfm_capability_options['catalog'] : 'no';
  	if( $catalog == 'yes' ) return false;
  	return $allow;
  }
  
  // Linstings
  function wcfmcap_is_allow_listings( $allow ) {
  	$associate_listings = ( isset( $this->wcfm_capability_options['associate_listings'] ) ) ? $this->wcfm_capability_options['associate_listings'] : 'no';
  	if( $associate_listings == 'yes' ) return false;
  	return $allow;
  }
  
  // Product Columns
  function wcfmcap_is_allow_product_columns( $product_columns ) {
  	
  	$inventory = ( isset( $this->wcfm_capability_options['inventory'] ) ) ? $this->wcfm_capability_options['inventory'] : 'no';
  	$shipping = ( isset( $this->wcfm_capability_options['shipping'] ) ) ? $this->wcfm_capability_options['shipping'] : 'no';
  	$taxes = ( isset( $this->wcfm_capability_options['taxes'] ) ) ? $this->wcfm_capability_options['taxes'] : 'no';
  	//$attributes = ( isset( $this->wcfm_capability_options['attributes'] ) ) ? $this->wcfm_capability_options['attributes'] : 'no';
  	$advanced = ( isset( $this->wcfm_capability_options['advanced'] ) ) ? $this->wcfm_capability_options['advanced'] : 'no';
  	$linked = ( isset( $this->wcfm_capability_options['linked'] ) ) ? $this->wcfm_capability_options['linked'] : 'no';
  	$downloadable = ( isset( $this->wcfm_capability_options['downloadable'] ) ) ? $this->wcfm_capability_options['downloadable'] : 'no';
  	$grouped = ( isset( $this->wcfm_capability_options['grouped'] ) ) ? $this->wcfm_capability_options['grouped'] : 'no';
		$external = ( isset( $this->wcfm_capability_options['external'] ) ) ? $this->wcfm_capability_options['external'] : 'no';
		$gallery = ( isset( $this->wcfm_capability_options['gallery'] ) ) ? $this->wcfm_capability_options['gallery'] : 'no';
		$category = ( isset( $this->wcfm_capability_options['category'] ) ) ? $this->wcfm_capability_options['category'] : 'no';
		$tags = ( isset( $this->wcfm_capability_options['tags'] ) ) ? $this->wcfm_capability_options['tags'] : 'no';
		
  	
  	if( $inventory == 'yes' ) unset( $product_columns[ 'stock_status' ] );
		if( $inventory == 'yes' ) unset( $product_columns[ 'stock' ] );
		if( $inventory == 'yes' ) unset( $product_columns[ 'backorders' ] );
		if( $inventory == 'yes' ) unset( $product_columns[ 'sold_individually' ] );
		
		if( $shipping == 'yes' ) unset( $product_columns[ 'weight' ] );
		if( $shipping == 'yes' ) unset( $product_columns[ 'length' ] );
		if( $shipping == 'yes' ) unset( $product_columns[ 'width' ] );
		if( $shipping == 'yes' ) unset( $product_columns[ 'height' ] );
		if( $shipping == 'yes' ) unset( $product_columns[ 'shipping_class_id' ] );
		
		if( $taxes == 'yes' ) unset( $product_columns[ 'tax_status' ] );
		if( $taxes == 'yes' ) unset( $product_columns[ 'tax_class' ] );
		
		//if( $attributes == 'yes' ) unset( $product_columns[ 'subscription' ] );
		
  	if( $advanced == 'yes' ) unset( $product_columns[ 'reviews_allowed' ] );
  	if( $advanced == 'yes' ) unset( $product_columns[ 'purchase_note' ] );
  	
  	if( $linked == 'yes' ) unset( $product_columns[ 'upsell_ids' ] );
  	if( $linked == 'yes' ) unset( $product_columns[ 'cross_sell_ids' ] );
  	
  	if( $downloadable == 'yes' ) unset( $product_columns[ 'download_limit' ] );
  	if( $downloadable == 'yes' ) unset( $product_columns[ 'download_expiry' ] );
  	
  	if( $grouped == 'yes' ) unset( $product_columns[ 'grouped_products' ] );
  	if( $external == 'yes' ) unset( $product_columns[ 'product_url' ] );
  	if( $external == 'yes' ) unset( $product_columns[ 'button_text' ] );
  	
  	if( $gallery == 'yes' ) unset( $product_columns[ 'images' ] );
  	
  	if( $category == 'yes' ) unset( $product_columns[ 'category_ids' ] );
  	if( $tags == 'yes' ) unset( $product_columns[ 'tag_ids' ] );
		
		return $product_columns;
  }
  
  // Allow View Orders
  function wcfmcap_is_allow_orders( $allow ) {
  	$view_orders = ( isset( $this->wcfm_capability_options['view_orders'] ) ) ? $this->wcfm_capability_options['view_orders'] : 'no';
  	if( $view_orders == 'yes' ) return false;
  	return $allow;
  }
  
  // Allow Order Status Update
  function wcfmcap_is_allow_order_status_update( $allow ) {
  	$order_status_update = ( isset( $this->wcfm_capability_options['order_status_update'] ) ) ? $this->wcfm_capability_options['order_status_update'] : 'no';
  	if( $order_status_update == 'yes' ) return false;
  	return $allow;
  	
  }
  
  // Allow View Order Details
  function wcfmcap_is_allow_order_details( $allow ) {
  	$view_order_details = ( isset( $this->wcfm_capability_options['view_order_details'] ) ) ? $this->wcfm_capability_options['view_order_details'] : 'no';
  	if( $view_order_details == 'yes' ) return false;
  	return $allow;
  }
  
  // Custome Billing Address
  function wcfmcap_is_allow_customer_billing_details( $allow ) {
  	$view_billing_details = ( isset( $this->wcfm_capability_options['view_billing_details'] ) ) ? $this->wcfm_capability_options['view_billing_details'] : 'no';
  	if( $view_billing_details == 'yes' ) return false;
  	return $allow;
  }
  
  // Custome Shipping Address
  function wcfmcap_is_allow_customer_shipping_details( $allow ) {
  	$view_shipping_details = ( isset( $this->wcfm_capability_options['view_shipping_details'] ) ) ? $this->wcfm_capability_options['view_shipping_details'] : 'no';
  	if( $view_shipping_details == 'yes' ) return false;
  	return $allow;
  }
  
  // Order Customer Details
  function wcfmcap_is_allow_order_customer_details( $allow ) {
  	$view_email = ( isset( $this->wcfm_capability_options['view_email'] ) ) ? $this->wcfm_capability_options['view_email'] : 'no';
  	if( $view_email == 'yes' ) return false;
  	return $allow;
  }
  
  // Order EXport CSV
  function wcfmcap_is_allow_export_csv( $allow ) {
  	$export_csv = ( isset( $this->wcfm_capability_options['export_csv'] ) ) ? $this->wcfm_capability_options['export_csv'] : 'no';
  	if( $export_csv == 'yes' ) return false;
  	return $allow;
  }
  
  // Order PDF Invoice
  function wcfmcap_is_allow_pdf_invoice( $allow ) {
  	$pdf_invoice = ( isset( $this->wcfm_capability_options['pdf_invoice'] ) ) ? $this->wcfm_capability_options['pdf_invoice'] : 'no';
  	if( $pdf_invoice == 'yes' ) return false;
  	return $allow;
  }
  
  // Order PDF Packing Slip
  function wcfmcap_is_allow_pdf_packing_slip( $allow ) {
  	$pdf_packing_slip = ( isset( $this->wcfm_capability_options['pdf_packing_slip'] ) ) ? $this->wcfm_capability_options['pdf_packing_slip'] : 'no';
  	if( $pdf_packing_slip == 'yes' ) return false;
  	return $allow;
  }
  
  // Allow View Reports
  function wcfmcap_is_allow_reports( $allow ) {
  	$view_reports = ( isset( $this->wcfm_capability_options['view_reports'] ) ) ? $this->wcfm_capability_options['view_reports'] : 'no';
  	if( $view_reports == 'yes' ) return false;
  	return $allow;
  }
  
  // Commission Manage
  function wcfmcap_is_allow_commission_manage( $allow ) {
  	$manage_commission = ( isset( $this->wcfm_capability_options['manage_commission'] ) ) ? $this->wcfm_capability_options['manage_commission'] : 'no';
  	if( $manage_commission == 'yes' ) return false;
  	return $allow;
  }
  
  // WP Admin View
  function wcfmcap_is_allow_wp_admin_view( $allow ) {
  	$wp_admin_view = ( isset( $this->wcfm_capability_options['wp_admin_view'] ) ) ? $this->wcfm_capability_options['wp_admin_view'] : 'no';
  	if( $wp_admin_view == 'yes' ) return false;
  	return $allow;
  }
}