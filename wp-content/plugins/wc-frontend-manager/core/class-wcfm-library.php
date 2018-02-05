<?php

/**
 * WCFM plugin library
 *
 * Plugin intiate library
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   1.0.0
 */
 
class WCFM_Library {
	
	public $lib_path;
  
  public $lib_url;
  
  public $php_lib_path;
  
  public $php_lib_url;
  
  public $js_lib_path;
  
  public $js_lib_url;
  
  public $js_lib_url_min;
  
  public $css_lib_path;
  
  public $css_lib_url;
  
  public $views_path;
  
  	/**
	 * Billing fields.
	 *
	 * @var array
	 */
	protected static $billing_fields = array();

	/**
	 * Shipping fields.
	 *
	 * @var array
	 */
	protected static $shipping_fields = array();
	
	public function __construct() {
    global $WCFM;
		
	  $this->lib_path = $WCFM->plugin_path . 'assets/';

    $this->lib_url = $WCFM->plugin_url . 'assets/';
    
    $this->php_lib_path = $this->lib_path . 'php/';
    
    $this->php_lib_url = $this->lib_url . 'php/';
    
    $this->js_lib_path = $this->lib_path . 'js/';
    
    $this->js_lib_url = $this->lib_url . 'js/';
    
    $this->js_lib_url_min = $this->lib_url . 'js/min/';
    
    $this->css_lib_path = $this->lib_path . 'css/';
    
    $this->css_lib_url = $this->lib_url . 'css/';
    
    $this->views_path = $WCFM->plugin_path . 'views/';
	}
	
	public function load_scripts( $end_point ) {
	  global $WCFM;
	  
	  // Load Menu JS
	  wp_enqueue_script( 'wcfm_menu_js', $this->js_lib_url . 'wcfm-script-menu.js', array('jquery'), $WCFM->version, true );
	  // Localized Script
	  wp_localize_script( 'wcfm_menu_js', 'wcfm_notification_sound', $this->lib_url . 'sounds/audio_file.mp3' );
    $wcfm_dashboard_messages = get_wcfm_dashboard_messages();
		wp_localize_script( 'wcfm_menu_js', 'wcfm_dashboard_messages', $wcfm_dashboard_messages );
	  
	  $noloader = 0;
	  $wcfm_options = get_option('wcfm_options');
	  $noloader = isset( $wcfm_options['noloader'] ) ? $wcfm_options['noloader'] : 'no';
	  wp_localize_script( 'wcfm_menu_js', 'wcfm_noloader', $noloader );
	  
	  $this->load_blockui_lib();
	  
	  do_action( 'before_wcfm_load_scripts', $end_point );
	  
	  switch( $end_point ) {
	  	
	  	case 'wcfm-dashboard':
        $this->load_chartjs_lib();
        wp_enqueue_script( 'wcfm_dashboard_js', $this->js_lib_url . 'wcfm-script-dashboard.js', array('jquery'), $WCFM->version, true );
      break;
      
	    case 'wcfm-products':
	    	$this->load_select2_lib();
        $this->load_datatable_lib();
        wp_enqueue_script( 'wcfm_products_js', $this->js_lib_url . 'wcfm-script-products.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
        
        // Screen manager
	    	$wcfm_screen_manager = (array) get_option( 'wcfm_screen_manager' );
	    	$wcfm_screen_manager_data = array();
	    	if( isset( $wcfm_screen_manager['product'] ) ) $wcfm_screen_manager_data = $wcfm_screen_manager['product'];
	    	if( !isset( $wcfm_screen_manager_data['admin'] ) ) {
					$wcfm_screen_manager_data['admin'] = $wcfm_screen_manager_data;
					$wcfm_screen_manager_data['vendor'] = $wcfm_screen_manager_data;
				}
				if( wcfm_is_vendor() ) {
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['vendor'];
				} else {
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['admin'];
				}
	    	if( !$WCFM->is_marketplace || wcfm_is_vendor() ) {
	    		$wcfm_screen_manager_data[10] = 'yes';
	    	}
	    	if( ! apply_filters( 'wcfm_is_allow_inventory', true ) ) {
	    		$wcfm_screen_manager_data[5] = 'yes';
	    	}
	    	wp_localize_script( 'wcfm_products_js', 'wcfm_products_screen_manage', $wcfm_screen_manager_data );
      break;
      
      case 'wcfm-products-manage':
      	$this->load_tinymce_lib();
      	$this->load_upload_lib();
      	$this->load_select2_lib();
      	$this->load_datepicker_lib();
      	$this->load_collapsible_lib();
        wp_enqueue_script( 'wcfm_products_manage_js', $this->js_lib_url . 'products-manager/wcfm-script-products-manage.js', array('jquery', 'select2_js'), $WCFM->version, true );
        
		  	// WC Subscription Support
		  	if( wcfm_is_subscription() ) {
		  		wp_enqueue_script( 'wcfm_wcsubscriptions_products_manage_js', $this->js_lib_url . 'products-manager/wcfm-script-wcsubscriptions-products-manage.js', array('jquery'), $WCFM->version, true );
		  	}
		  	
		  	// YITH Auction Free Support - 3.0.4
		  	if( $wcfm_allow_auction = apply_filters( 'wcfm_is_allow_auction', true ) ) {
					if( WCFM_Dependencies::wcfm_yith_auction_free_active_check() ) {
						$this->load_timepicker_lib();
						wp_enqueue_script( 'wcfm_yithauction_products_manage_js', $this->js_lib_url . 'products-manager/wcfm-script-yithauction-products-manage.js', array( 'jquery', 'wcfm_timepicker_js', 'wcfm_products_manage_js' ), $WCFM->version, true );
					}
				}
		  	
        // Localized Script
        $wcfm_messages = get_wcfm_products_manager_messages();
			  wp_localize_script( 'wcfm_products_manage_js', 'wcfm_products_manage_messages', $wcfm_messages );
			  $wcfm_product_type_categories = (array) get_option( 'wcfm_product_type_categories' );
			  wp_localize_script( 'wcfm_products_manage_js', 'wcfm_product_type_categories', $wcfm_product_type_categories );
			  $wcfm_product_type_default_tab = apply_filters( 'wcfm_product_type_default_tab', array( 'simple' => 'wcfm_products_manage_form_inventory_head', 'variable' => 'wcfm_products_manage_form_inventory_head', 'external' => 'wcfm_products_manage_form_inventory_head', 'grouped' => 'wcfm_products_manage_form_grouped_head', 'booking' => 'wcfm_products_manage_form_booking_options_head', 'accommodation-booking' => 'wcfm_products_manage_form_accommodation_options_head', 'auction' => 'wcfm_products_manage_form_auction_head', 'redq_rental' => 'wcfm_products_manage_form_inventory_head', 'rental' => 'wcfm_products_manage_form_redq_rental_head', 'appointment' => 'wcfm_products_manage_form_appointment_options_head', 'bundle' => 'wcfm_products_manage_form_wc_product_bundle_head'  ) );
			  wp_localize_script( 'wcfm_products_manage_js', 'wcfm_product_type_default_tab', $wcfm_product_type_default_tab );
			  
			  // Single Product Multi-seller support - 3.3.7
			  $wcfm_auto_product_suggest = false;
			  if( wcfm_is_vendor() && ( $WCFM->is_marketplace == 'wcmarketplace' ) && function_exists( 'get_wcmp_vendor_settings' ) ) {
			  	if ( get_wcmp_vendor_settings('is_singleproductmultiseller', 'general') == 'Enable' ) {
			  		$wcfm_auto_product_suggest = true;
			  	}
			  }
			  wp_localize_script( 'wcfm_products_manage_js', 'wcfm_auto_product_suggest', array( 'allow' => $wcfm_auto_product_suggest ) );
      break;
      
      case 'wcfm-products-export':
      	//wp_register_script( 'wc-product-export', WC()->plugin_url() . '/assets/js/admin/wc-product-export.js', array( 'jquery' ), WC_VERSION );
				//wp_enqueue_script( 'wc-product-export' );
				$this->load_select2_lib();
        wp_enqueue_script( 'wc-product-export', $this->js_lib_url . 'wcfm-script-products-export.js', array('jquery'), $WCFM->version, true );
        wp_localize_script( 'wc-product-export', 'wc_product_export_params', array(
					'export_nonce' => wp_create_nonce( 'wc-product-export' ),
				) );
      break;
        
        
      case 'wcfm-coupons':
        $this->load_datatable_lib();
        wp_enqueue_script( 'wcfm_coupons_js', $this->js_lib_url . 'wcfm-script-coupons.js', array('jquery', 'dataTables_js' ), $WCFM->version, true );
      break;
      
      case 'wcfm-coupons-manage':
      	$this->load_collapsible_lib();
      	$this->load_datepicker_lib();
        wp_enqueue_script( 'wcfm_coupons_manage_js', $this->js_lib_url . 'wcfm-script-coupons-manage.js', array('jquery'), $WCFM->version, true );
        // Localized Script
        $wcfm_messages = get_wcfm_coupons_manage_messages();
			  wp_localize_script( 'wcfm_coupons_manage_js', 'wcfm_coupons_manage_messages', $wcfm_messages );
      break;
      
      case 'wcfm-orders':
        $this->load_datatable_lib();
        $this->load_datatable_download_lib();
        wp_enqueue_script( 'wcfm_orders_js', $this->js_lib_url . 'wcfm-script-orders.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
        
        // Screen manager
	    	$wcfm_screen_manager = (array) get_option( 'wcfm_screen_manager' );
	    	$wcfm_screen_manager_data = array();
	    	if( isset( $wcfm_screen_manager['order'] ) ) $wcfm_screen_manager_data = $wcfm_screen_manager['order'];
	    	if( !isset( $wcfm_screen_manager_data['admin'] ) ) {
					$wcfm_screen_manager_data['admin'] = $wcfm_screen_manager_data;
					$wcfm_screen_manager_data['vendor'] = $wcfm_screen_manager_data;
				}
				if( wcfm_is_vendor() ) {
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['vendor'];
				} else {
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['admin'];
				}
				if( !$WCFM->is_marketplace ) {
	    		$wcfm_screen_manager_data[4] = 'yes';
	    	}
	    	wp_localize_script( 'wcfm_orders_js', 'wcfm_orders_screen_manage', $wcfm_screen_manager_data );
      break;
      
      case 'wcfm-orders-details':
        wp_enqueue_script( 'wcfm_orders_details_js', $this->js_lib_url . 'wcfm-script-orders-details.js', array('jquery'), $WCFM->version, true );
      break;
      
      case 'wcfm-listings':
      	$this->load_datatable_lib();
	    	wp_enqueue_script( 'wcfm_listings_js', $this->js_lib_url . 'wcfm-script-listings.js', array('jquery'), $WCFM->version, true );
	    	
	    	// Screen manager
	    	$wcfm_screen_manager = (array) get_option( 'wcfm_screen_manager' );
	    	$wcfm_screen_manager_data = array();
	    	if( isset( $wcfm_screen_manager['listing'] ) ) $wcfm_screen_manager_data = $wcfm_screen_manager['listing'];
	    	if( !isset( $wcfm_screen_manager_data['admin'] ) ) {
					$wcfm_screen_manager_data['admin'] = $wcfm_screen_manager_data;
					$wcfm_screen_manager_data['vendor'] = $wcfm_screen_manager_data;
				}
				if( wcfm_is_vendor() ) {
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['vendor'];
				} else {
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['admin'];
				}
	    	wp_localize_script( 'wcfm_listings_js', 'wcfm_listings_screen_manage', $wcfm_screen_manager_data );
      break;
      
      case 'wcfm-reports-sales-by-date':
      	$this->load_chartjs_lib();
        //wp_enqueue_script( 'wcfm_reports_js', $this->js_lib_url . 'wcfm-script-reports-sales-by-date.js', array('jquery'), $WCFM->version, true );
      break;
      
      case 'wcfm-reports-out-of-stock':
      	$this->load_datatable_lib();
      	$this->load_datatable_download_lib();
        wp_enqueue_script( 'wcfm_reports_js', $this->js_lib_url . 'wcfm-script-reports-out-of-stock.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
      break;
      
      case 'wcfm-profile':
      	$this->load_select2_lib();
      	$this->load_collapsible_lib();
      	$this->load_tinymce_lib();
      	$this->load_upload_lib();
      	wp_enqueue_script( 'wcfm_profile_js', $this->js_lib_url . 'wcfm-script-profile.js', array('jquery','select2_js'), $WCFM->version, true );
      break;
      
      case 'wcfm-settings':
      	if( $WCFM->is_marketplace && wcfm_is_vendor() ) {
      		$this->load_tinymce_lib();
      		
      		if( $WCFM->is_marketplace == 'dokan' ) {
      			wp_enqueue_script( 'jquery-ui' );
            wp_enqueue_script( 'jquery-ui-autocomplete' );
      			wp_enqueue_script( 'wc-country-select' );
      			wp_enqueue_script( 'wcfm_dokan_settings_js', $this->js_lib_url . 'wcfm-script-dokan-settings.js', array('jquery'), $WCFM->version, true );
      			
      			$scheme  = is_ssl() ? 'https' : 'http';
						$api_key = dokan_get_option( 'gmap_api_key', 'dokan_general', false );
		
						if ( $api_key ) {
							wp_enqueue_script( 'wcfm-dokan-setting-google-maps', $scheme . '://maps.google.com/maps/api/js?key=' . $api_key );
						}
      		}
      	}
      	$this->load_collapsible_lib();
      	$this->load_upload_lib();
      	$this->load_select2_lib();
      	
      	if( !wcfm_is_vendor() ) {
					$this->load_colorpicker_lib();
					wp_enqueue_script( 'iris', admin_url('js/iris.min.js'),array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);
					wp_enqueue_script( 'wp-color-picker', admin_url('js/color-picker.min.js'), array('iris'), false,1);
					
					$colorpicker_l10n = array('clear' => __('Clear'), 'defaultString' => __('Default'), 'pick' => __('Select Color'));
					wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
					
					$wcfm_color_setting_options = $WCFM->wcfm_color_setting_options();
					wp_localize_script( 'wp-color-picker', 'wcfm_color_setting_options', $wcfm_color_setting_options );
				}
				
				wp_enqueue_script( 'wcfm_settings_js', $this->js_lib_url . 'wcfm-script-settings.js', array('jquery'), $WCFM->version, true );
				
      break;
      
      case 'wcfm-capability':
      	$this->load_collapsible_lib();
      	$this->load_select2_lib();
      	wp_enqueue_script( 'wcfm_capability_js', $this->js_lib_url . 'wcfm-script-capability.js', array('jquery'), $WCFM->version, true );
      break;
      
      case 'wcfm-knowledgebase':
      	$this->load_tinymce_lib();
      	$this->load_datatable_lib();
      	$this->load_collapsible_lib();
      	wp_enqueue_script( 'wcfm_knowledgebase_js', $this->js_lib_url . 'wcfm-script-knowledgebase.js', array('jquery'), $WCFM->version, true );
      break;
      
      case 'wcfm-knowledgebase-manage':
      	$this->load_tinymce_lib();
      	wp_enqueue_script( 'wcfm_knowledgebase_manage_js', $this->js_lib_url . 'wcfm-script-knowledgebase-manage.js', array('jquery'), $WCFM->version, true );
      	// Localized Script
        $wcfm_messages = get_wcfm_knowledgebase_manage_messages();
			  wp_localize_script( 'wcfm_knowledgebase_manage_js', 'wcfm_knowledgebase_manage_messages', $wcfm_messages );
      break;
      
      case 'wcfm-notices':
      	$this->load_datatable_lib();
      	wp_enqueue_script( 'wcfm_notices_js', $this->js_lib_url . 'wcfm-script-notices.js', array('jquery'), $WCFM->version, true );
      break;
      
      case 'wcfm-notice-manage':
      	$this->load_tinymce_lib();
      	wp_enqueue_script( 'wcfm_notice_manage_js', $this->js_lib_url . 'wcfm-script-notice-manage.js', array('jquery'), $WCFM->version, true );
      	// Localized Script
        $wcfm_messages = get_wcfm_notice_manage_messages();
			  wp_localize_script( 'wcfm_notice_manage_js', 'wcfm_notice_manage_messages', $wcfm_messages );
      break;
      
      case 'wcfm-notice-view':
      	$this->load_tinymce_lib();
      	wp_enqueue_script( 'wcfm_notice_view_js', $this->js_lib_url . 'wcfm-script-notice-view.js', array('jquery'), $WCFM->version, true );
      	// Localized Script
        $wcfm_messages = get_wcfm_notice_view_messages();
			  wp_localize_script( 'wcfm_notice_view_js', 'wcfm_notice_view_messages', $wcfm_messages );
      break;
      
      case 'wcfm-messages':
      	$this->load_tinymce_lib();
      	$this->load_datatable_lib();
      	$this->load_select2_lib();
      	wp_enqueue_script( 'wcfm_messages_js', $this->js_lib_url . 'wcfm-script-messages.js', array('jquery', 'dataTables_js', 'select2_js'), $WCFM->version, true );
      break;
      
      case 'wcfm-vendors':
      	$this->load_datatable_lib();
      	$this->load_select2_lib();
      	$this->load_datatable_download_lib();
      	wp_enqueue_script( 'wcfm_vendors_js', $this->js_lib_url . 'vendors/wcfm-script-vendors.js', array('jquery'), $WCFM->version, true );
      	
      	// Screen manager
	    	$wcfm_screen_manager_data = array();
	    	if( !WCFM_Dependencies::wcfmvm_plugin_active_check() ) {
	    		$wcfm_screen_manager_data = array( 2  => __( 'Memebership', 'wc-frontend-manager' ) );
	    	}
	    	wp_localize_script( 'wcfm_vendors_js', 'wcfm_vendors_screen_manage', $wcfm_screen_manager_data );
      break;
      
      case 'wcfm-vendors-manage':
      	$this->load_datatable_lib();
      	$this->load_select2_lib();
      	$this->load_tinymce_lib();
      	wp_enqueue_script( 'wcfm_vendors_manage_js', $this->js_lib_url . 'vendors/wcfm-script-vendors-manage.js', array('jquery'), $WCFM->version, true );
      break;
      
      case 'wcfm-vendors-commission':
      	$this->load_datatable_lib();
      	wp_enqueue_script( 'wcfm_vendors_commission_js', $this->js_lib_url . 'vendors/wcfm-script-vendors-commission.js', array('jquery'), $WCFM->version, true );
      break;
      
      default :
        do_action( 'wcfm_load_scripts', $end_point );
      break;
        
    }
    
    do_action( 'after_wcfm_load_scripts', $end_point );
	}
	
	public function load_styles( $end_point ) {
	  global $WCFM;
	  
	  $wcfm_options = get_option('wcfm_options');
	  
	  // Load Menu Style
	  wp_enqueue_style( 'wcfm_menu_css',  $this->css_lib_url . 'wcfm-style-menu.css', array(), $WCFM->version );
	  
	  // Load No-menu style
	  $is_menu_disabled = isset( $wcfm_options['menu_disabled'] ) ? $wcfm_options['menu_disabled'] : 'no';
	  if( $is_menu_disabled == 'yes' ) {
	  	wp_enqueue_style( 'wcfm_no_menu_css',  $this->css_lib_url . 'wcfm-style-no-menu.css', array('wcfm_menu_css'), $WCFM->version );
	  }
	  
	  // Load Slick Menu Style
	  $is_slick_menu_disabled = isset( $wcfm_options['slick_menu_disabled'] ) ? $wcfm_options['slick_menu_disabled'] : 'no';
	  if( $is_slick_menu_disabled != 'yes' ) {
	    wp_enqueue_style( 'wcfm_menu_slick_css',  $this->css_lib_url . 'wcfm-style-menu-slick.css', array( 'wcfm_menu_css' ), $WCFM->version );
	  }
	  
	  // Load Float Button Style
	  $is_float_button_disabled = isset( $wcfm_options['float_button_disabled'] ) ? $wcfm_options['float_button_disabled'] : 'no';
	  if( $is_float_button_disabled != 'yes' ) {
	    wp_enqueue_style( 'wcfm_float_button_css',  $this->css_lib_url . 'wcfm-style-float-button.css', array( 'wcfm_menu_css' ), $WCFM->version );
	  }
	  
	  // Load Template Style
	  $is_dashboard_full_view_disabled = isset( $wcfm_options['dashboard_full_view_disabled'] ) ? $wcfm_options['dashboard_full_view_disabled'] : 'no';
	  if( $is_dashboard_full_view_disabled != 'yes' ) {
	  	wp_enqueue_style( 'wcfm_template_css',  $WCFM->plugin_url . 'templates/classic/template-style.css', array( ), $WCFM->version );
	  }
	  
	  do_action( 'before_wcfm_load_styles', $end_point );
	  
	  switch( $end_point ) {
	  	
	  	case 'wcfm-dashboard':
	  		//wp_enqueue_style( 'dashicons' );
		    wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		    wp_enqueue_style( 'wcfm_dashboard_css',  $this->css_lib_url . 'wcfm-style-dashboard.css', array(), $WCFM->version );
		  break;
	  	
	    case 'wcfm-products':
		    wp_enqueue_style( 'wcfm_products_css',  $this->css_lib_url . 'wcfm-style-products.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-products-manage':
		    wp_enqueue_style( 'wcfm_products_manage_css',  $this->css_lib_url . 'products-manager/wcfm-style-products-manage.css', array(), $WCFM->version );
		    
		  	// WC Subscriptions Support
		    if( wcfm_is_subscription() ) {
		  		wp_enqueue_style( 'wcfm_wcsubscriptions_products_manage_css',  $this->css_lib_url . 'products-manager/wcfm-style-wcsubscriptions-products-manage.css', array(), $WCFM->version );
		  	}
		  	
		  	// Load RTL Style
				if( is_rtl() ) {
					wp_enqueue_style( 'wcfm_products_manage_rtl_css',  $this->css_lib_url . 'products-manager/wcfm-style-products-manage-rtl.css', array('wcfm_products_manage_css'), $WCFM->version );
				}
		  break;
		  
		  case 'wcfm-products-export':
		  	wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		    wp_enqueue_style( 'wcfm_products_export_css',  $this->css_lib_url . 'wcfm-style-products-export.css', array(), $WCFM->version );
		  break;
		    
		  case 'wcfm-coupons':
		    wp_enqueue_style( 'wcfm_coupons_css',  $this->css_lib_url . 'wcfm-style-coupons.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-coupons-manage':
		    wp_enqueue_style( 'wcfm_coupons_manage_css',  $this->css_lib_url . 'wcfm-style-coupons-manage.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-orders':
		    wp_enqueue_style( 'wcfm_orders_css',  $this->css_lib_url . 'wcfm-style-orders.css', array(), $WCFM->version );
		  break;                                                                                                                                    
		  
		  case 'wcfm-orders-details':
		  	wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		    wp_enqueue_style( 'wcfm_orders_details_css',  $this->css_lib_url . 'wcfm-style-orders-details.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-listings':
	    	wp_enqueue_style( 'wcfm_listings_css',  $this->css_lib_url . 'wcfm-style-listings.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-reports-sales-by-date':
		  	wp_enqueue_style( 'reports_menus_css',  $this->css_lib_url . 'wcfm-style-reports-menus.css', array(), $WCFM->version );
		    wp_enqueue_style( 'wcfm_reports_css',  $this->css_lib_url . 'wcfm-style-reports-sales-by-date.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-reports-out-of-stock':
		  	wp_enqueue_style( 'reports_menus_css',  $this->css_lib_url . 'wcfm-style-reports-menus.css', array(), $WCFM->version );
		    //wp_enqueue_style( 'wcfm_reports_css',  $this->css_lib_url . 'wcfm-style-reports.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-profile':
		  	wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		    wp_enqueue_style( 'wcfm_profile_css',  $this->css_lib_url . 'wcfm-style-profile.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-settings':
		  	$this->load_checkbox_offon_lib();
		    wp_enqueue_style( 'wcfm_settings_css',  $this->css_lib_url . 'wcfm-style-settings.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-capability':
		  	$this->load_checkbox_offon_lib();
		    wp_enqueue_style( 'wcfm_capability_css',  $this->css_lib_url . 'wcfm-style-capability.css', array(), $WCFM->version );
      break;
		  
		  case 'wcfm-knowledgebase':
		    wp_enqueue_style( 'wcfm_knowledgebase_css',  $this->css_lib_url . 'wcfm-style-knowledgebase.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-knowledgebase-manage':
		  	wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		  	wp_enqueue_style( 'wcfm_knowledgebase_manage_css',  $this->css_lib_url . 'wcfm-style-knowledgebase-manage.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-notices':
		    wp_enqueue_style( 'wcfm_notices_css',  $this->css_lib_url . 'wcfm-style-notices.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-notice-manage':
		  	wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		  	wp_enqueue_style( 'wcfm_notices_manage_css',  $this->css_lib_url . 'wcfm-style-notices-manage.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-notice-view':
		  	wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		  	wp_enqueue_style( 'wcfm_notice_view_css',  $this->css_lib_url . 'wcfm-style-notice-view.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-messages':
		    wp_enqueue_style( 'wcfm_messages_css',  $this->css_lib_url . 'wcfm-style-messages.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-vendors':
		    wp_enqueue_style( 'wcfm_vendors_css',  $this->css_lib_url . 'vendors/wcfm-style-vendors.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-vendors-manage':
		  	wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		  	wp_enqueue_style( 'wcfm_dashboard_css',  $this->css_lib_url . 'wcfm-style-dashboard.css', array(), $WCFM->version );
		    wp_enqueue_style( 'wcfm_vendors_manage_css',  $this->css_lib_url . 'vendors/wcfm-style-vendor-manage.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-vendors-commission':
		    wp_enqueue_style( 'wcfm_vendors_commission_css',  $this->css_lib_url . 'vendors/wcfm-style-vendors-commission.css', array(), $WCFM->version );
		  break;
		  
		  default :
        do_action( 'wcfm_load_styles', $end_point );
      break;
		    
		}
		
		// WCFM Custom CSS
		$upload_dir      = wp_upload_dir();
		$wcfm_style_custom = get_option( 'wcfm_style_custom' );
		if( file_exists( trailingslashit( $upload_dir['basedir'] ) . 'wcfm/' . $wcfm_style_custom ) ) {
			wp_enqueue_style( 'wcfm_custom_css',  trailingslashit( $upload_dir['baseurl'] ) . 'wcfm/' . $wcfm_style_custom, array( 'wcfm_menu_css' ), $WCFM->version );
		}
		
		// Load RTL Style
	  if( is_rtl() ) {
	  	wp_enqueue_style( 'wcfm_rtl_css',  $this->css_lib_url . 'wcfm-style-rtl.css', array( ), $WCFM->version );
	  }
		
		do_action( 'after_wcfm_load_styles', $end_point );
	}
	
	public function load_views( $end_point, $menu = true ) {
	  global $WCFM;
	  
	  // WCFM Menu
	  if( $menu )
	  	require_once( $this->views_path . 'wcfm-view-menu.php' );
	  
	  do_action( 'before_wcfm_load_views', $end_point );
    
	  switch( $end_point ) {
	  	
	  	case 'wcfm-dashboard':
	  		if( $WCFM->is_marketplace && wcfm_is_vendor() ) {
					require_once( $this->views_path . 'dashboard/wcfm-view-' . $WCFM->is_marketplace . '-dashboard.php' );
				} else {
					require_once( $this->views_path . 'dashboard/wcfm-view-dashboard.php' );
				}
      break;
	  	
	    case 'wcfm-products':
        require_once( $this->views_path . 'wcfm-view-products.php' );
      break;
      
      case 'wcfm-products-manage':
        require_once( $this->views_path . 'products-manager/wcfm-view-products-manage.php' );
      break;
      
      case 'wcfm-products-export':
        require_once( $this->views_path . 'wcfm-view-products-export.php' );
      break;
        
      case 'wcfm-coupons':
        require_once( $this->views_path . 'wcfm-view-coupons.php' );
      break;
      
      case 'wcfm-coupons-manage':
        require_once( $this->views_path . 'wcfm-view-coupons-manage.php' );
      break;
      
      case 'wcfm-orders':
        require_once( $this->views_path . 'wcfm-view-orders.php' );
      break;
      
      case 'wcfm-orders-details':
        require_once( $this->views_path . 'wcfm-view-orders-details.php' );
      break;
      
      case 'wcfm-listings':
        require_once( $this->views_path . 'wcfm-view-listings.php' );
      break;
      
      case 'wcfm-reports-sales-by-date':
      	if( $WCFM->is_marketplace && wcfm_is_vendor() ) {
					require_once( $this->views_path . 'reports/wcfm-view-reports-' . $WCFM->is_marketplace . '-sales-by-date.php' );
				} else {
					require_once( $this->views_path . 'reports/wcfm-view-reports-sales-by-date.php' );
				}
      break;
      
      case 'wcfm-reports-out-of-stock':
        require_once( $this->views_path . 'wcfm-view-reports-out-of-stock.php' );
      break;
      
      case 'wcfm-profile':
        require_once( $this->views_path . 'wcfm-view-profile.php' );
      break;
      
      case 'wcfm-settings':
      	if( $WCFM->is_marketplace && wcfm_is_vendor() ) {
					require_once( $this->views_path . 'settings/wcfm-view-' . $WCFM->is_marketplace . '-settings.php' );
				} else {
					require_once( $this->views_path . 'settings/wcfm-view-settings.php' );
				}
      break;
      
      case 'wcfm-capability':
      	require_once( $this->views_path . 'wcfm-view-capability.php' );
      break;
      
      case 'wcfm-knowledgebase':
        require_once( $this->views_path . 'wcfm-view-knowledgebase.php' );
      break;
      
      case 'wcfm-knowledgebase-manage':
        require_once( $this->views_path . 'wcfm-view-knowledgebase-manage.php' );
      break;
      
      case 'wcfm-notices':
        require_once( $this->views_path . 'wcfm-view-notices.php' );
      break;
      
      case 'wcfm-notice-manage':
        require_once( $this->views_path . 'wcfm-view-notice-manage.php' );
      break;
      
      case 'wcfm-notice-view':
        require_once( $this->views_path . 'wcfm-view-notice-view.php' );
      break;
      
      case 'wcfm-messages':
        require_once( $this->views_path . 'wcfm-view-messages.php' );
      break;
      
      case 'wcfm-vendors':
        require_once( $this->views_path . 'vendors/wcfm-view-vendors.php' );
      break;
      
      case 'wcfm-vendors-manage':
        require_once( $this->views_path . 'vendors/wcfm-view-vendors-manage.php' );
      break;
      
      case 'wcfm-vendors-commission':
        require_once( $this->views_path . 'vendors/wcfm-view-vendors-commission.php' );
      break;
      
      default :
        do_action( 'wcfm_load_views', $end_point );
      break;
        
    }
    
    do_action( 'after_wcfm_load_views', $end_point );
	}
	
	/**
	 * PHP WCFM fields Library
	*/
	public function load_wcfm_fields() {
	  global $WCFM;
	  require_once ( $WCFM->plugin_path . 'includes/libs/php/class-wcfm-fields.php');
	  $WCFM_Fields = new WCFM_Fields(); 
	  return $WCFM_Fields;
	}
	
	/**
	 * Jquery dataTable library
	 */
	function load_datatable_lib() {
		global $WCFM;
		
		// JS
		wp_enqueue_script( 'dataTables_js', 'https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js', array('jquery'), $WCFM->version, true );
		wp_enqueue_script( 'dataTables_responsive_js', 'https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
		
		$dataTables_language = '{"processing": "' . __('Processing...', 'wc-frontend-manager' ) . '" , "search": "' . __('Search:', 'wc-frontend-manager' ) . '", "lengthMenu": "' . __('Show _MENU_ entries', 'wc-frontend-manager' ) . '", "info": " ' . __('Showing _START_ to _END_ of _TOTAL_ entries', 'wc-frontend-manager' ) . '", "infoEmpty": "' . __('Showing 0 to 0 of 0 entries', 'wc-frontend-manager' ) . '", "infoFiltered": "' . __('(filtered _MAX_ entries of total)', 'wc-frontend-manager' ) . '", "loadingRecords": "' . __('Loading...', 'wc-frontend-manager' ) . '", "zeroRecords": "' . __('No matching records found', 'wc-frontend-manager' ) . '", "emptyTable": "' . __('No data in the table', 'wc-frontend-manager' ) . '", "paginate": {"first": "' . __('First', 'wc-frontend-manager' ) . '", "previous": "' . __('Previous', 'wc-frontend-manager' ) . '", "next": "' . __('Next', 'wc-frontend-manager' ) . '", "last": "' .  __('Last', 'wc-frontend-manager') . '"}, "buttons": {"print": "' . __('Print', 'wc-frontend-manager' ) . '", "pdf": "' . __('PDF', 'wc-frontend-manager' ) . '", "excel": "' . __('Excel', 'wc-frontend-manager' ) . '", "csv": "' . __('CSV', 'wc-frontend-manager' ) . '"}}';
		wp_localize_script( 'dataTables_js', 'dataTables_language', $dataTables_language );
		
		// CSS
		//wp_enqueue_style( 'wcfm_responsive_css',  $this->css_lib_url . 'wcfm-style-responsive.css', array('wcfm_menu_css'), $WCFM->version );
		wp_enqueue_style( 'dataTables_css',  'https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css', array(), $WCFM->version );
		wp_enqueue_style( 'dataTables_responsive_css',  'https://cdn.datatables.net/responsive/2.1.1/css/responsive.dataTables.min.css', array(), $WCFM->version );
	}
	
	/**
	 * Jquery dataTable library
	 */
	function load_datatable_download_lib() {
		global $WCFM;
		
		//JS
		wp_enqueue_script( 'dataTables_buttons_js', 'https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
		wp_enqueue_script( 'dataTables_buttons_flash_js', 'https://cdn.datatables.net/buttons/1.4.2/js/buttons.flash.min.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
		wp_enqueue_script( 'dataTables_buttons_jszip_js', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
		wp_enqueue_script( 'dataTables_buttons_html5_js', 'https://cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
		wp_enqueue_script( 'dataTables_buttons_pdf_js', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
		wp_enqueue_script( 'dataTables_buttons_vfs_js', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
		wp_enqueue_script( 'dataTables_buttons_print_js', 'https://cdn.datatables.net/buttons/1.4.2/js/buttons.print.min.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
		
		// CSS
		wp_enqueue_style( 'dataTables_buttons_css',  'https://cdn.datatables.net/buttons/1.4.2/css/buttons.dataTables.min.css', array(), $WCFM->version );
	}
	
	/**
	 * Jquery TinyMCE library
	 */
	public function load_tinymce_lib() {
	  global $WCFM;
	  wp_enqueue_script('tinymce_js', $WCFM->plugin_url . 'includes/libs/tinymce/tinymce.min.js', array('jquery'), $WCFM->version, true);
	  //wp_enqueue_script('jquery_tinymce_js', '//cdnjs.cloudflare.com/ajax/libs/tinymce/4.7.1/tinymce.jquery.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_theme_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/theme.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_avlist_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/avlist.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_anchor_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/anchor.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_autolink_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/autolink.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_autosize_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/autoresize.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_fullscreen_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/fullscreen.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_link_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/link.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_list_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/lists.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_preview_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/preview.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_media_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/media.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_image_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/image.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_charmap_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/charmap.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_plugin_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/code.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_contextmenu_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/contextmenu.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_directionally_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/directionality.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_datetime_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/insertdatetime.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_paste_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/paste.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_searchreplace_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/searchreplace.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_visual_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/visualblocks.plugin.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_table_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/table.plugin.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_print_js', $WCFM->plugin_url . 'includes/libs/tinymce/plugins/print.plugin.min.js', array('jquery'), $WCFM->version, true);
	  
	  wp_enqueue_style( 'jquery_tinymce_skin_css',  $WCFM->plugin_url . 'includes/libs/tinymce/skins/lightgray/skin.min.css', array(), $WCFM->version );
	  //wp_enqueue_style( 'jquery_tinymce_content_css',  $WCFM->plugin_url . 'includes/libs/tinymce/skins/lightgray/content.min.css', array(), $WCFM->version );
	}
	
	/**
	 * Jquery qTip library
	*/
	public function load_qtip_lib() {
	  global $WCFM;
	  wp_enqueue_script( 'wcfm_qtip_js', $WCFM->plugin_url . 'includes/libs/qtip/qtip.js', array('jquery'), $WCFM->version, true );
		wp_enqueue_style( 'wcfm_qtip_css',  $WCFM->plugin_url . 'includes/libs/qtip/qtip.css', array(), $WCFM->version );
	}
	
	/**
	 * WP Media library
	*/
	public function load_upload_lib() {
	  global $WCFM;
	  wp_enqueue_media();
	  wp_enqueue_script( 'upload_js', $WCFM->plugin_url . 'includes/libs/upload/media-upload.js', array('jquery'), $WCFM->version, true );
	  wp_enqueue_style( 'upload_css',  $WCFM->plugin_url . 'includes/libs/upload/media-upload.css', array(), $WCFM->version );
	  $uploads_language = array( "choose_media" => __( 'Choose Media', 'wc-frontend-manager' ), "choose_image" => __( 'Choose Image', 'wc-frontend-manager' ), "add_to_gallery" => __( 'Add to Gallery', 'wc-frontend-manager' ) );
	  wp_localize_script( 'upload_js', 'uploads_language', $uploads_language );
	}
	
	/**
	 * WP ColorPicker library
	*/
	public function load_colorpicker_lib() {
	  global $WCFM;
	  wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_script( 'colorpicker_init', $WCFM->plugin_url . 'includes/libs/colorpicker/colorpicker.js', array( 'jquery', 'wp-color-picker' ), $WCFM->version, true );
    wp_enqueue_style( 'wp-color-picker' );
	}
	
	/**
	 * Select2 library
	*/
	public function load_select2_lib() {
	  global $WCFM;
	  wp_enqueue_script( 'select2_js', $WCFM->plugin_url . 'includes/libs/select2/select2.js', array('jquery'), $WCFM->version, true );
	  wp_enqueue_style( 'select2_css',  $WCFM->plugin_url . 'includes/libs/select2/select2.css', array(), $WCFM->version );
	}
	
	/**
	 * Jquery Accordian library
	 */
	public function load_collapsible_lib() {
	  global $WCFM;
	  wp_enqueue_script( 'collapsible_js', $this->js_lib_url . 'jquery.collapsiblepanel.js', array('jquery'), $WCFM->version, true );
	  //wp_enqueue_script( 'collapsible_cookie_js', $this->js_lib_url . 'jquery.cookie.js', array('jquery'), $WCFM->version, true );
	  wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
	}
	
	/**
	 * WP DatePicker library
	*/
	public function load_datepicker_lib() {
	  global $WCFM;
	  wp_enqueue_script( 'jquery-ui-datepicker' );
	  wp_enqueue_style( 'jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css', array(), $WCFM->version );
	}
	
	/**
	 * Timepicker library
	*/
	public function load_timepicker_lib() {
	  global $WCFM;
	  wp_enqueue_script('jquery-ui-datepicker');
	  wp_enqueue_script( 'wcfm_timepicker_js', $WCFM->plugin_url . 'includes/libs/timepicker/timepicker.js', array('jquery', 'jquery-ui-datepicker'), $WCFM->version, true );
	  wp_enqueue_style( 'wcfm_timepicker_css',  $WCFM->plugin_url . 'includes/libs/timepicker/timepicker.css', array(), $WCFM->version );
	}
	
	/**
	 * Jquery Flot library
	*/
	public function load_flot_lib() {
	  global $WCFM;
	  
	  wp_enqueue_script( 'jquery-flot_js', $WCFM->plugin_url . 'includes/libs/jquery-flot/jquery.flot.min.js', array('jquery'), $WCFM->version, true );
	  wp_enqueue_script( 'jquery-flot-resize_js', $WCFM->plugin_url . 'includes/libs/jquery-flot/jquery.flot.resize.min.js', array('jquery', 'jquery-flot_js'), $WCFM->version, true );
	  wp_enqueue_script( 'jquery-flot-timme_js', $WCFM->plugin_url . 'includes/libs/jquery-flot/jquery.flot.time.min.js', array('jquery', 'jquery-flot_js'), $WCFM->version, true );
	  wp_enqueue_script( 'jquery-flot-pie_js', $WCFM->plugin_url . 'includes/libs/jquery-flot/jquery.flot.pie.min.js', array('jquery', 'jquery-flot_js'), $WCFM->version, true );
	  wp_enqueue_script( 'jquery-flot-stack_js', $WCFM->plugin_url . 'includes/libs/jquery-flot/jquery.flot.stack.min.js', array('jquery', 'jquery-flot_js'), $WCFM->version, true );
	}
	
	/**
	 * Jquery Chart.js library
	*/
	public function load_chartjs_lib() {
	  global $WCFM;
	  wp_enqueue_script( 'jquery-chart_moment_js', $WCFM->plugin_url . 'includes/libs/chart-js/moment.min.js', array('jquery'), $WCFM->version, true );
	  wp_enqueue_script( 'jquery-chart_js', $WCFM->plugin_url . 'includes/libs/chart-js/chart.min.js', array('jquery', 'jquery-chart_moment_js'), $WCFM->version, true );
	  wp_enqueue_script( 'jquery-chart_util_js', $WCFM->plugin_url . 'includes/libs/chart-js/chart-util.js', array(), $WCFM->version, true );
	}
	
	/**
	 * Jquery tiptip library
	*/
	public function load_tiptip_lib() {
	  global $WCFM;
	  
	  wp_enqueue_script( 'jquery-tip_js', $WCFM->plugin_url . 'includes/libs/jquery-tiptip/jquery.tipTip.min.js', array('jquery'), $WCFM->version, true );
	}
	
	/**
	 * Jquery blockUI library
	*/
	public function load_blockui_lib() {
	  global $WCFM;
	  
	  wp_enqueue_script( 'jquery-blockui_js', $WCFM->plugin_url . 'includes/libs/jquery-blockui/jquery.blockUI.min.js', array('jquery'), $WCFM->version, true );
	}
	
	/**
	 * CSS Checkbox OFF-ON library
	*/
	public function load_checkbox_offon_lib() {
	  global $WCFM;
	  wp_enqueue_style( 'checkbox-offon-style', $WCFM->plugin_url . 'includes/libs/checkbox-offon/checkbox_offon.css', array(), $WCFM->version );
	}
	
	public static function init_address_fields() {

		self::$billing_fields = apply_filters( 'woocommerce_admin_billing_fields', array(
			'first_name' => array(
				'label' => __( 'First Name', 'woocommerce' ),
				'show'  => false
			),
			'last_name' => array(
				'label' => __( 'Last Name', 'woocommerce' ),
				'show'  => false
			),
			'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'  => false
			),
			'address_1' => array(
				'label' => __( 'Address 1', 'woocommerce' ),
				'show'  => false
			),
			'address_2' => array(
				'label' => __( 'Address 2', 'woocommerce' ),
				'show'  => false
			),
			'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'  => false
			),
			'postcode' => array(
				'label' => __( 'Postcode', 'woocommerce' ),
				'show'  => false
			),
			'country' => array(
				'label'   => __( 'Country', 'woocommerce' ),
				'show'    => false,
				'class'   => 'js_field-country select short',
				'type'    => 'select',
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_allowed_countries()
			),
			'state' => array(
				'label' => __( 'State/County', 'woocommerce' ),
				'class'   => 'js_field-state select short',
				'show'  => false
			),
			'email' => array(
				'label' => __( 'Email', 'woocommerce' ),
			),
			'phone' => array(
				'label' => __( 'Phone', 'woocommerce' ),
			),
		) );

		self::$shipping_fields = apply_filters( 'woocommerce_admin_shipping_fields', array(
			'first_name' => array(
				'label' => __( 'First Name', 'woocommerce' ),
				'show'  => false
			),
			'last_name' => array(
				'label' => __( 'Last Name', 'woocommerce' ),
				'show'  => false
			),
			'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'  => false
			),
			'address_1' => array(
				'label' => __( 'Address 1', 'woocommerce' ),
				'show'  => false
			),
			'address_2' => array(
				'label' => __( 'Address 2', 'woocommerce' ),
				'show'  => false
			),
			'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'  => false
			),
			'postcode' => array(
				'label' => __( 'Postcode', 'woocommerce' ),
				'show'  => false
			),
			'country' => array(
				'label'   => __( 'Country', 'woocommerce' ),
				'show'    => false,
				'type'    => 'select',
				'class'   => 'js_field-country select short',
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_shipping_countries()
			),
			'state' => array(
				'label' => __( 'State/County', 'woocommerce' ),
				'class'   => 'js_field-state select short',
				'show'  => false
			),
		) );
	}
	
	/**
	 * Get sales report data.
	 * @return object
	 */
	private function get_sales_report_data() {
		include_once( dirname( WC_PLUGIN_FILE ) . '/includes/admin/reports/class-wc-report-sales-by-date.php' );

		$sales_by_date                 = new WC_Report_Sales_By_Date();
		$sales_by_date->start_date     = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
		$sales_by_date->end_date       = current_time( 'timestamp' );
		$sales_by_date->chart_groupby  = 'day';
		$sales_by_date->group_by_query = 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date)';

		return $sales_by_date->get_report_data();
	}
	
	/**
	 * Get top seller from DB.
	 * @return object
	 */
	private function get_top_seller() {
		global $wpdb;

		$query            = array();
		$query['fields']  = "SELECT SUM( order_item_meta.meta_value ) as qty, order_item_meta_2.meta_value as product_id
			FROM {$wpdb->posts} as posts";
		$query['join']    = "INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_id ";
		$query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id ";
		$query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id ";
		$query['where']   = "WHERE posts.post_type IN ( 'shop_order','shop_order_refund' ) ";
		$query['where']  .= "AND posts.post_status IN ( 'wc-" . implode( "','wc-", apply_filters( 'woocommerce_reports_order_statuses', array( 'completed', 'processing', 'on-hold' ) ) ) . "' ) ";
		$query['where']  .= "AND order_item_meta.meta_key = '_qty' ";
		$query['where']  .= "AND order_item_meta_2.meta_key = '_product_id' ";
		$query['where']  .= "AND posts.post_date >= '" . date( 'Y-m-d', strtotime( '-7 DAY', current_time( 'timestamp' ) ) ) . "' ";
		$query['where']  .= "AND posts.post_date <= '" . date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) . "' ";
		$query['groupby'] = "GROUP BY product_id";
		$query['orderby'] = "ORDER BY qty DESC";
		$query['limits']  = "LIMIT 1";
		
		return $wpdb->get_row( implode( ' ', apply_filters( 'woocommerce_dashboard_status_widget_top_seller_query', $query ) ) );
	}
	
	/**
	 * Sort an array by 'title'
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return array
	 */
	public function sort_by_title( array $a, array $b ) {
		return strcasecmp( $a[ 'title' ], $b[ 'title' ] );
	}
	
	// Generate Taxonomy HTML
	function generateTaxonomyHTML( $taxonomy, $product_taxonomies, $selected_taxonomies, $nbsp = '', $is_checklist = false, $is_custom = false, $is_hierarchical = true ) {
		global $WCFM;
		
		foreach ( $product_taxonomies as $cat ) {
			$wcfm_allowed_taxonomies = apply_filters( 'wcfm_allowed_taxonomies', true, $taxonomy, $cat->term_id );
			$checklis_label_class = '';
			if( !$wcfm_allowed_taxonomies ) $checklis_label_class = 'product_cats_checklist_item_hide_by_cap';
			if( $is_checklist ) {
				echo '<li class="product_cats_checklist_item checklist_item_' . esc_attr( $cat->term_id ) . '" data-item="' . esc_attr( $cat->term_id ) . '">';
				if( !$nbsp ) echo '<span class="fa fa-arrow-circle-right sub_checklist_toggler"></span>';
				if( $is_custom ) {
					echo '<label class="selectit">' . $nbsp . '<input type="checkbox" class="wcfm-checkbox ' . $checklis_label_class . '" name="product_custom_taxonomies[' . $taxonomy . '][]" value="' . esc_attr( $cat->term_id ) . '"' . checked( in_array( $cat->term_id, $selected_taxonomies ), true, false ) . '/>' . esc_html( $cat->name ) . '</label>';
				} else {
					echo '<label class="selectit">' . $nbsp . '<input type="checkbox" class="wcfm-checkbox ' . $checklis_label_class . '" name="product_cats[]" value="' . esc_attr( $cat->term_id ) . '"' . checked( in_array( $cat->term_id, $selected_taxonomies ), true, false ) . '/><span>' . esc_html( $cat->name ) . '</span></label>';
				}
			} else {
				if( $wcfm_allowed_taxonomies ) {
					echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $selected_taxonomies ), true, false ) . '>' . $nbsp . esc_html( $cat->name ) . '</option>';
				}
			}
			
			if( $is_hierarchical ) {
				$product_child_taxonomies   = get_terms( $taxonomy, 'orderby=name&hide_empty=0&parent=' . absint( $cat->term_id ) );
				if ( $product_child_taxonomies ) {
					if( $is_checklist ) { echo '<ul class="product_taxonomy_sub_checklist">'; }
					$this->generateTaxonomyHTML( $taxonomy, $product_child_taxonomies, $selected_taxonomies, $nbsp . '&nbsp;&nbsp;', $is_checklist, $is_custom, $is_hierarchical );
					if( $is_checklist ) { echo '</ul>'; }
				}
			}
			if( $is_checklist ) { echo '</li>'; }
		}
	}
}