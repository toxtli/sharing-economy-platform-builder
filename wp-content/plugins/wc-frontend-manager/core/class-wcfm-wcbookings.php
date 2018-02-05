<?php

/**
 * WCFM plugin core
 *
 * Booking WC Booking Support
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   2.1.0
 */
 
class WCFM_WCBookings {
	
	public function __construct() {
    global $WCFM;
    
    if( wcfm_is_booking() ) {
    	
			// WC Booking Query Var Filter
			add_filter( 'wcfm_query_vars', array( &$this, 'wcb_wcfm_query_vars' ), 20 );
			add_filter( 'wcfm_endpoint_title', array( &$this, 'wcb_wcfm_endpoint_title' ), 20, 2 );
			add_action( 'init', array( &$this, 'wcb_wcfm_init' ), 20 );
    		
    	if ( current_user_can( 'manage_bookings' ) ) {
    		// WC Booking Menu Filter
				add_filter( 'wcfm_menus', array( &$this, 'wcb_wcfm_menus' ), 20 );
				
				// Bookable Product Type
				add_filter( 'wcfm_product_types', array( &$this, 'wcb_product_types' ), 20 );
				
				// Bookable Product Type Capability
				add_filter( 'wcfm_settings_fields_product_types', array( &$this, 'wcfmcap_product_types' ), 20, 3 );
				
				// Bookings Load WCFMu Scripts
				add_action( 'wcfm_load_scripts', array( &$this, 'wcb_load_scripts' ), 30 );
				add_action( 'after_wcfm_load_scripts', array( &$this, 'wcb_load_scripts' ), 30 );
				
				// Bookings Load WCFMu Styles
				add_action( 'wcfm_load_styles', array( &$this, 'wcb_load_styles' ), 30 );
				add_action( 'after_wcfm_load_styles', array( &$this, 'wcb_load_styles' ), 30 );
				
				// Bookings Load WCFMu views
				add_action( 'wcfm_load_views', array( &$this, 'wcb_load_views' ), 30 );
				add_action( 'before_wcfm_load_views', array( &$this, 'wcb_load_views' ), 30 );
				
				// Bookings Ajax Controllers
				add_action( 'after_wcfm_ajax_controller', array( &$this, 'wcb_ajax_controller' ) );
				
				// Booking General Block
				add_action( 'after_wcfm_products_manage_general', array( &$this, 'wcb_product_manage_general' ), 10, 2 );
				
				// Booking Product Manage View
				add_action( 'end_wcfm_products_manage', array( &$this, 'wcb_wcfm_products_manage_form_load_views' ), 20 );
			}
    }
    
    // Add vendor email for confirm booking email
		add_filter( 'woocommerce_email_recipient_new_booking', array( $this, 'wcfm_filter_booking_emails' ), 20, 2 );

		// Add vendor email for cancelled booking email
		add_filter( 'woocommerce_email_recipient_booking_cancelled', array( $this, 'wcfm_filter_booking_emails' ), 20, 2 );
  }
  
  /**
   * WC Booking Query Var
   */
  function wcb_wcfm_query_vars( $query_vars ) {
  	$wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );
  	
		$query_booking_vars = array(
			'wcfm-bookings-dashboard'       => ! empty( $wcfm_modified_endpoints['wcfm-bookings-dashboard'] ) ? $wcfm_modified_endpoints['wcfm-bookings-dashboard'] : 'wcfm-bookings-dashboard',
			'wcfm-bookings'                 => ! empty( $wcfm_modified_endpoints['wcfm-bookings'] ) ? $wcfm_modified_endpoints['wcfm-bookings'] : 'wcfm-bookings',
			'wcfm-bookings-resources'       => ! empty( $wcfm_modified_endpoints['wcfm-bookings-resources'] ) ? $wcfm_modified_endpoints['wcfm-bookings-resources'] : 'wcfm-bookings-resources',
			'wcfm-bookings-resources-manage'=> ! empty( $wcfm_modified_endpoints['wcfm-bookings-resources-manage'] ) ? $wcfm_modified_endpoints['wcfm-bookings-resources-manage'] : 'wcfm-bookings-resources-manage',
			'wcfm-bookings-manual'          => ! empty( $wcfm_modified_endpoints['wcfm-bookings-manual'] ) ? $wcfm_modified_endpoints['wcfm-bookings-manual'] : 'wcfm-bookings-manual',
			'wcfm-bookings-calendar'        => ! empty( $wcfm_modified_endpoints['wcfm-bookings-calendar'] ) ? $wcfm_modified_endpoints['wcfm-bookings-calendar'] : 'wcfm-bookings-calendar',
			'wcfm-bookings-details'         => ! empty( $wcfm_modified_endpoints['wcfm-bookings-details'] ) ? $wcfm_modified_endpoints['wcfm-bookings-details'] : 'wcfm-bookings-details',
			'wcfm-bookings-settings'        => ! empty( $wcfm_modified_endpoints['wcfm-bookings-settings'] ) ? $wcfm_modified_endpoints['wcfm-bookings-settings'] : 'wcfm-bookings-settings',
		);
		
		$query_vars = array_merge( $query_vars, $query_booking_vars );
		
		return $query_vars;
  }
  
  /**
   * WC Booking End Point Title
   */
  function wcb_wcfm_endpoint_title( $title, $endpoint ) {
  	global $wp;
  	switch ( $endpoint ) {
  		case 'wcfm-bookings-dashboard' :
				$title = __( 'Bookings Dashboard', 'wc-frontend-manager' );
			break;
			case 'wcfm-bookings' :
				$title = __( 'Bookings List', 'wc-frontend-manager' );
			break;
			case 'wcfm-bookings-resources' :
				$title = __( 'Bookings Resources', 'wc-frontend-manager' );
			break;
			case 'wcfm-bookings-resources-manage' :
				$title = __( 'Bookings Resources Manage', 'wc-frontend-manager' );
			break;
			case 'wcfm-bookings-manual' :
				$title = __( 'Create Bookings', 'wc-frontend-manager' );
			break;
			case 'wcfm-bookings-calendar' :
				$title = __( 'Bookings Calendar', 'wc-frontend-manager' );
			break;
			case 'wcfm-bookings-details' :
				$title = sprintf( __( 'Booking Details #%s', 'wc-frontend-manager' ), $wp->query_vars['wcfm-bookings-details'] );
			break;
			case 'wcfm-bookings-settings' :
				$title = __( 'Bookings settings', 'wc-frontend-manager' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * WC Booking Endpoint Intialize
   */
  function wcb_wcfm_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		if( !get_option( 'wcfm_updated_end_point_wc_bookings' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_wc_bookings', 1 );
		}
  }
  
  /**
   * WC Booking Menu
   */
  function wcb_wcfm_menus( $menus ) {
  	global $WCFM;
  	
  	if ( current_user_can( 'manage_bookings' ) ) {
  		if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
				$menus = array_slice($menus, 0, 3, true) +
														array( 'wcfm-bookings-dashboard' => array(   'label'  => __( 'Bookings', 'woocommerce-bookings'),
																												 'url'       => get_wcfm_bookings_dashboard_url(),
																												 'icon'      => 'calendar-check-o',
																												 'priority'  => 15
																												) )	 +
															array_slice($menus, 3, count($menus) - 3, true) ;
			} else {
				$menus = array_slice($menus, 0, 3, true) +
													array( 'wcfm-bookings' => array(   'label'  => __( 'Bookings', 'woocommerce-bookings'),
																											 'url'       => get_wcfm_bookings_url(),
																											 'icon'      => 'calendar-check-o',
																											 'priority'  => 15
																											) )	 +
														array_slice($menus, 3, count($menus) - 3, true) ;
			}
		}
		
  	return $menus;
  }
  
  /**
   * WC Booking Product Type
   */
  function wcb_product_types( $pro_types ) {
  	global $WCFM;
  	if ( current_user_can( 'manage_bookings' ) ) {
  		$pro_types['booking'] = __( 'Bookable product', 'woocommerce-bookings' );
  	}
  	
  	return $pro_types;
  }
  
  /**
	 * WCFM Capability Vendor Product Types
	 */
	function wcfmcap_product_types( $product_types, $handler = 'wcfm_capability_options', $wcfm_capability_options = array() ) {
		global $WCFM;
		
		$booking = ( isset( $wcfm_capability_options['booking'] ) ) ? $wcfm_capability_options['booking'] : 'no';
		
		$product_types["booking"] = array('label' => __('Bookable', 'wc-frontend-manager') , 'name' => $handler . '[booking]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $booking);
		
		return $product_types;
	}
	
	/**
   * WC Booking Scripts
   */
  public function wcb_load_scripts( $end_point ) {
	  global $WCFM;
    
	  switch( $end_point ) {
	  	case 'wcfm-products-manage':
	  		wp_enqueue_script( 'wcfm_wcbookings_products_manage_js', $WCFM->library->js_lib_url . 'wc_bookings/wcfm-script-wcbookings-products-manage.js', array('jquery'), $WCFM->version, true );
	  	break;
	  	
	  	case 'wcfm-bookings-dashboard':
	    	wp_enqueue_script( 'wcfm_bookings_dashboard_js', $WCFM->library->js_lib_url . 'wc_bookings/wcfm-script-wcbookings-dashboard.js', array('jquery'), $WCFM->version, true );
      break;
      
	  	case 'wcfm-bookings':
      	$WCFM->library->load_datatable_lib();
	    	wp_enqueue_script( 'wcfm_bookings_js', $WCFM->library->js_lib_url . 'wc_bookings/wcfm-script-wcbookings.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
	    	
	    	// Screen manager
	    	$wcfm_screen_manager = (array) get_option( 'wcfm_screen_manager' );
	    	$wcfm_screen_manager_data = array();
	    	if( isset( $wcfm_screen_manager['booking'] ) ) $wcfm_screen_manager_data = $wcfm_screen_manager['booking'];
	    	if( !isset( $wcfm_screen_manager_data['admin'] ) ) {
					$wcfm_screen_manager_data['admin'] = $wcfm_screen_manager_data;
					$wcfm_screen_manager_data['vendor'] = $wcfm_screen_manager_data;
				}
				if( wcfm_is_vendor() ) {
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['vendor'];
				} else {
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['admin'];
				}
	    	wp_localize_script( 'wcfm_bookings_js', 'wcfm_bookings_screen_manage', $wcfm_screen_manager_data );
      break;
      
      case 'wcfm-bookings-details':
	    	wp_enqueue_script( 'wcfm_bookings_details_js', $WCFM->library->js_lib_url . 'wc_bookings/wcfm-script-wcbookings-details.js', array('jquery'), $WCFM->version, true );
      break;
	  }
	}
	
	/**
   * WC Booking Styles
   */
	public function wcb_load_styles( $end_point ) {
	  global $WCFM, $WCFMu;
		
	  switch( $end_point ) {
	  	case 'wcfm-products-manage':
	  		wp_enqueue_style( 'wcfm_wcbookings_products_manage_css',  $WCFM->library->css_lib_url . 'wc_bookings/wcfm-style-wcbookings-products-manage.css', array(), $WCFM->version );
	  	break;
	  	
	  	case 'wcfm-bookings-dashboard':
	    	wp_enqueue_style( 'wcfm_bookings_dashboard_css',  $WCFM->library->css_lib_url . 'wc_bookings/wcfm-style-wcbookings-dashboard.css', array(), $WCFM->version );
		  break;
		  
	    case 'wcfm-bookings':
	    	wp_enqueue_style( 'wcfm_bookings_css',  $WCFM->library->css_lib_url . 'wc_bookings/wcfm-style-wcbookings.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-bookings-details':
		  	wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
	    	wp_enqueue_style( 'wcfm_bookings_details_css',  $WCFM->library->css_lib_url . 'wc_bookings/wcfm-style-wcbookings-details.css', array(), $WCFM->version );
		  break;
	  }
	}
	
	/**
   * WC Booking Views
   */
  public function wcb_load_views( $end_point ) {
	  global $WCFM, $WCFMu;
	  
	  switch( $end_point ) {
	  	case 'wcfm-bookings-dashboard':
        require_once( $WCFM->library->views_path . 'wc_bookings/wcfm-view-wcbookings-dashboard.php' );
      break;
      
	  	case 'wcfm-bookings':
        require_once( $WCFM->library->views_path . 'wc_bookings/wcfm-view-wcbookings.php' );
      break;
      
      case 'wcfm-bookings-details':
        require_once( $WCFM->library->views_path . 'wc_bookings/wcfm-view-wcbookings-details.php' );
      break;
	  }
	}
	
	/**
   * WC Booking Ajax Controllers
   */
  public function wcb_ajax_controller() {
  	global $WCFM, $WCFMu;
  	
  	$controllers_path = $WCFM->plugin_path . 'controllers/wc_bookings/';
  	
  	$controller = '';
  	if( isset( $_POST['controller'] ) ) {
  		$controller = $_POST['controller'];
  		
  		switch( $controller ) {
  			case 'wcfm-bookings':
					require_once( $controllers_path . 'wcfm-controller-wcbookings.php' );
					new WCFM_WCBookings_Controller();
				break;
  		}
  	}
  }
	
  /**
   * WC Booking Product General Options
   */
  function wcb_product_manage_general( $product_id, $product_type ) {
  	global $WCFM, $WCFM;
  	
  	$bookable_product = new WC_Product_Booking( $product_id );
  	
  	$duration_type = $bookable_product->get_duration_type( 'edit' );
		$duration      = $bookable_product->get_duration( 'edit' );
		$duration_unit = $bookable_product->get_duration_unit( 'edit' );
		
		$min_duration = $bookable_product->get_min_duration( 'edit' );
		$max_duration = $bookable_product->get_max_duration( 'edit' );
		$enable_range_picker = $bookable_product->get_enable_range_picker( 'edit' ) ? 'yes' : 'no';
		
		$calendar_display_mode = $bookable_product->get_calendar_display_mode( 'edit' );
		$requires_confirmation = $bookable_product->get_requires_confirmation( 'edit' ) ? 'yes' : 'no';
		
		$user_can_cancel = $bookable_product->get_user_can_cancel( 'edit' ) ? 'yes' : 'no';
		$cancel_limit = $bookable_product->get_cancel_limit( 'edit' );
		$cancel_limit_unit = $bookable_product->get_cancel_limit_unit( 'edit' );
  	?>
  	<!-- collapsible Booking 1 -->
	  <div class="page_collapsible products_manage_downloadable booking" id="wcfm_products_manage_form_booking_options_head"><label class="fa fa-calendar"></label><?php _e('Booking Options', 'woocommerce-bookings'); ?><span></span></div>
		<div class="wcfm-container booking">
			<div id="wcfm_products_manage_form_downloadable_expander" class="wcfm-content">
			  <?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcbokings_general_fields', array(  
						
						"_wc_booking_duration_type" => array('label' => __('Booking Duration', 'woocommerce-bookings') , 'type' => 'select', 'options' => array( 'fixed' => __( 'Fixed blocks of', 'woocommerce-bookings'), 'customer' => __( 'Customer defined blocks of', 'woocommerce-bookings' ) ), 'class' => 'wcfm-select wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $duration_type ),
						"_wc_booking_duration" => array('type' => 'number', 'class' => 'wcfm-text wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $duration ),
						"_wc_booking_duration_unit" => array('type' => 'select', 'options' => array( 'month' => __( 'Month(s)', 'woocommerce-bookings'), 'day' => __( 'Day(s)', 'woocommerce-bookings' ), 'hour' => __( 'Hour(s)', 'woocommerce-bookings' ), 'minute' => __( 'Minute(s)', 'woocommerce-bookings' ) ), 'class' => 'wcfm-select wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $duration_unit ),
						"_wc_booking_min_duration" => array('label' => __('Minimum duration', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele duration_type_customer_ele booking', 'label_class' => 'wcfm_title duration_type_customer_ele booking', 'value' => $min_duration, 'hints' => __( 'The minimum allowed duration the user can input.', 'woocommerce-bookings' ), 'attributes' => array( 'min' => '', 'step' => '1' ) ),
						"_wc_booking_max_duration" => array('label' => __('Maximum duration', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele duration_type_customer_ele booking', 'label_class' => 'wcfm_title duration_type_customer_ele booking', 'value' => $max_duration, 'hints' => __( 'The maximum allowed duration the user can input.', 'woocommerce-bookings' ), 'attributes' => array( 'min' => '', 'step' => '1' ) ),
						"_wc_booking_enable_range_picker" => array('label' => __('Enable Calendar Range Picker?', 'woocommerce-bookings') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele duration_type_customer_ele booking', 'label_class' => 'wcfm_title duration_type_customer_ele booking', 'value' => 'yes', 'dfvalue' => $enable_range_picker, 'hints' => __( 'Lets the user select a start and end date on the calendar - duration will be calculated automatically.', 'woocommerce-bookings' ) ),
						"_wc_booking_calendar_display_mode" => array('label' => __('Calendar display mode', 'woocommerce-bookings') , 'type' => 'select', 'options' => array( '' => __( 'Display calendar on click', 'woocommerce-bookings'), 'always_visible' => __( 'Calendar always visible', 'woocommerce-bookings' ) ), 'class' => 'wcfm-select wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $calendar_display_mode ),
						"_wc_booking_requires_confirmation" => array('label' => __('Requires confirmation?', 'woocommerce-bookings') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele booking', 'label_class' => 'wcfm_title checkbox_title booking', 'value' => 'yes', 'dfvalue' => $requires_confirmation, 'hints' => __( 'Check this box if the booking requires admin approval/confirmation. Payment will not be taken during checkout.', 'woocommerce-bookings' ) ),
						"_wc_booking_user_can_cancel" => array('label' => __('Can be cancelled?', 'woocommerce-bookings') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele booking', 'label_class' => 'wcfm_title checkbox_title booking', 'value' => 'yes', 'dfvalue' => $user_can_cancel, 'hints' => __( 'Check this box if the booking can be cancelled by the customer after it has been purchased. A refund will not be sent automatically.', 'woocommerce-bookings' ) ),
						"_wc_booking_cancel_limit" => array('label' => __('Booking can be cancelled until', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele can_cancel_ele booking', 'label_class' => 'wcfm_title can_cancel_ele booking', 'value' => $cancel_limit ),
						"_wc_booking_cancel_limit_unit" => array('type' => 'select', 'options' => array( 'month' => __( 'Month(s)', 'woocommerce-bookings'), 'day' => __( 'Day(s)', 'woocommerce-bookings' ), 'hour' => __( 'Hour(s)', 'woocommerce-bookings' ), 'minute' => __( 'Minute(s)', 'woocommerce-bookings' ) ), 'class' => 'wcfm-select wcfm_ele can_cancel_ele booking', 'label_class' => 'wcfm_title can_cancel_ele booking', 'desc_class' => 'can_cancel_ele in_the_future booking', 'value' => $cancel_limit_unit, 'desc' => __( 'before the start date.', 'woocommerce-bookings' ) )
						
																															), $product_id ) );
			  
			  ?>
		  </div>
		</div>
		<!-- end collapsible Booking -->
		<div class="wcfm_clearfix"></div>
  	<?php
  }
  
  /**
   * WC Booking load views
   */
  function wcb_wcfm_products_manage_form_load_views( ) {
		global $WCFM;
	  
	 require_once( $WCFM->library->views_path . 'products-manager/wcfm-view-wcbookings-products-manage.php' );
	}
	
	/**
	 * Add vendor email to booking admin emails - 2.6.2
	 */
	public function wcfm_filter_booking_emails( $recipients, $this_email ) {
		global $WCFM;
		if ( ! empty( $this_email ) ) {
			if( $WCFM->is_marketplace ) {
				if( $WCFM->is_marketplace == 'wcmarketplace' ) {
					$vendor = get_wcmp_product_vendors( $this_email->product_id );
					if( $vendor ) {
						$vendor_id = $vendor->id;
						$vendor_data = get_userdata( $vendor_id );
						if ( ! empty( $vendor_data ) ) {
							if ( isset( $recipients ) ) {
								$recipients .= ',' . $vendor_data->user_email;
							} else {
								$recipients = $vendor_data->user_email;
							}
						}
					}
				} elseif( $WCFM->is_marketplace == 'wcvendors' ) {
					$product = get_post( $this_email->product_id );
					$vendor_id = $product->post_author;
					if( WCV_Vendors::is_vendor( $vendor_id ) ) {
						$vendor_data = get_userdata( $vendor_id );
						if ( ! empty( $vendor_data ) ) {
							if ( isset( $recipients ) ) {
								$recipients .= ',' . $vendor_data->user_email;
							} else {
								$recipients = $vendor_data->user_email;
							}
						}
					}
				} elseif( $WCFM->is_marketplace == 'wcpvendors' ) {
					$vendor_id = WC_Product_Vendors_Utils::get_vendor_id_from_product( $this_email->product_id );
					$vendor_data = WC_Product_Vendors_Utils::get_vendor_data_by_id( $vendor_id );
		
					if ( ! empty( $vendor_id ) && ! empty( $vendor_data ) ) {
						if ( isset( $recipients ) ) {
							$recipients .= ',' . $vendor_data['email'];
						} else {
							$recipients = $vendor_data['email'];
						}
					}
				} elseif( $WCFM->is_marketplace == 'dokan' ) {
					$product = get_post( $this_email->product_id );
					$vendor_id = $product->post_author;
					if( dokan_is_user_seller( $vendor_id ) ) {
						$vendor_data = get_userdata( $vendor_id );
						if ( ! empty( $vendor_data ) ) {
							if ( isset( $recipients ) ) {
								$recipients .= ',' . $vendor_data->user_email;
							} else {
								$recipients = $vendor_data->user_email;
							}
						}
					}
				}
			}
		}

		return $recipients;
	}
}