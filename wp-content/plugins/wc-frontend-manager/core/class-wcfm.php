<?php

/**
 * WCFM plugin core
 *
 * Plugin intiate
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   1.0.0
 */
 
class WCFM {

	public $plugin_base_name;
	public $plugin_url;
	public $plugin_path;
	public $version;
	public $token;
	public $text_domain;
	public $wcfm_query;
	public $library;
	public $shortcode;
	public $admin;
	public $frontend;
	public $ajax;
	public $non_ajax;
	private $file;
	public $wcfm_fields;
	public $is_marketplace;
	public $wcfm_marketplace;
	public $wcfm_capability;
	public $wcfm_preferences;
	public $wcfm_vendor_support;
	public $wcfm_wcbooking;
	public $wcfm_wccsubscriptions;
	public $wcfm_thirdparty_support;
	public $wcfm_customfield_support;
	public $wcfm_enquiry;
	public $wcfm_catalog;
	public $wcfm_withdrawal;
	public $wcfm_notification;
	public $wcfm_buddypress;

	public function __construct($file) {

		$this->file = $file;
		$this->plugin_base_name = plugin_basename( $file );
		$this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
		$this->plugin_path = trailingslashit(dirname($file));
		$this->token = WCFM_TOKEN;
		$this->text_domain = WCFM_TEXT_DOMAIN;
		$this->version = WCFM_VERSION;
		
		// Updation Hook
		add_action( 'init', array( &$this, 'update_wcfm' ) );

		add_action( 'init', array(&$this, 'init' ) );
		
		// WC Vendors shop_order_vendor - register post type fix - since 2.0.4
		add_filter( 'woocommerce_register_post_type_shop_order_vendor', array( &$this, 'wcvendors_register_post_type_shop_order_vendor' ) );
		
		// WCFM User Capability Load
		add_filter( 'wcfm_capability_options_rules', array( &$this, 'wcfm_capability_options_rules' ) );
	}
	
	/**
	 * initilize plugin on WP init
	 */
	function init() {
		global $WCFM;
		
		if( !session_id() ) session_start();
		
		// Init Text Domain
		$this->load_plugin_textdomain();
		
		// Load WCFM Dashbaord setup class
		// http://localhost/wrd/wp-admin/?page=wcfm-setup&step=dashboard
		if ( is_admin() ) {
			$current_page = filter_input( INPUT_GET, 'page' );
			if ( $current_page && $current_page == 'wcfm-setup' ) {
				require_once $this->plugin_path . 'helpers/class-wcfm-setup.php';
			}
		}
		
		// Register Knowledgebase Post Type
		register_post_type( 'wcfm_knowledgebase', array( 'public' => false ) );
		
		// Register Notice Post Type - 3.0.6
		register_post_type( 'wcfm_notice', array( 'public' => false ) );
		
		if (!is_admin() || defined('DOING_AJAX')) {
			$this->load_class( 'preferences' );
			$this->wcfm_preferences = new WCFM_Preferences();
		}
		
		if (!is_admin() || defined('DOING_AJAX')) {
			$this->load_class( 'capability' );
			$this->wcfm_capability = new WCFM_Capability();
		}
		
		// Check Marketplace
		$this->is_marketplace = wcfm_is_marketplace();
		if( $this->is_marketplace ) {
			$this->load_class( 'vendor-support' );
			$this->wcfm_vendor_support = new WCFM_Vendor_Support();
		}
		
		if (!is_admin() || defined('DOING_AJAX')) {
			if( $this->is_marketplace ) {
				if( wcfm_is_vendor() ) {
					$this->load_class( $this->is_marketplace );
					if( $this->is_marketplace == 'wcvendors' ) $this->wcfm_marketplace = new WCFM_WCVendors();
					elseif( $this->is_marketplace == 'wcmarketplace' ) $this->wcfm_marketplace = new WCFM_WCMarketplace();
					elseif( $this->is_marketplace == 'wcpvendors' ) $this->wcfm_marketplace = new WCFM_WCPVendors();
					elseif( $this->is_marketplace == 'dokan' ) $this->wcfm_marketplace = new WCFM_Dokan();
				}
			}
		}  
		
		// Load withdrawal module
		if( apply_filters( 'wcfm_is_pref_withdrawal', true ) ) {
			if (!is_admin() || defined('DOING_AJAX')) {
				if( wcfm_is_vendor() ) {
					if( $this->is_marketplace && in_array( $this->is_marketplace, array( 'dokan', 'wcmarketplace' ) ) ) {
						$this->load_class( 'withdrawal' );
						$this->wcfm_withdrawal = new WCFM_Withdrawal();
					}
				}
			}
		}
		
		// Check WC Booking
		if( wcfm_is_booking() ) {
			if (!is_admin() || defined('DOING_AJAX')) {
				$this->load_class('wcbookings');
				$this->wcfm_wcbooking = new WCFM_WCBookings();
			}
		} else {
			if( get_option( 'wcfm_updated_end_point_wc_bookings' ) ) {
				delete_option( 'wcfm_updated_end_point_wc_bookings' );
			}
		}
		
		// Check WC Subscription
		if( wcfm_is_subscription() ) {
			if (!is_admin() || defined('DOING_AJAX')) {
				$this->load_class('wcsubscriptions');
				$this->wcfm_wcsubscriptions = new WCFM_WCSubscriptions();
			}
		}
		
		// Init library
		$this->load_class( 'library' );
		$this->library = new WCFM_Library();

		// Init ajax
		if ( defined('DOING_AJAX') ) {
			$this->load_class( 'ajax' );
			$this->ajax = new WCFM_Ajax();
		}

		if ( is_admin() ) {
			$this->load_class( 'admin' );
			$this->admin = new WCFM_Admin();
		}

		if ( !is_admin() || defined('DOING_AJAX') ) {
			$this->load_class( 'thirdparty-support' );
			$this->wcfm_thirdparty_support = new WCFM_ThirdParty_Support();
		}
		
		if( apply_filters( 'wcfm_is_pref_custom_field', true ) ) {
			if ( !is_admin() || defined('DOING_AJAX') ) {
				$this->load_class( 'customfield-support' );
				$this->wcfm_customfield_support = new WCFM_Custom_Field_Support();
			}
		}
		
		if( apply_filters( 'wcfm_is_pref_enquiry', true ) ) {
			if ( !is_admin() || defined('DOING_AJAX') ) {
				$this->load_class( 'enquiry' );
				$this->wcfm_enquiry = new WCFM_Enquiry();
			}
		}
		
		if( apply_filters( 'wcfm_is_pref_enquiry', true ) && apply_filters( 'wcfm_is_pref_catalog', true ) ) {
			if ( !is_admin() || defined('DOING_AJAX') ) {
				$this->load_class( 'catalog' );
				$this->wcfm_catalog = new WCFM_Catalog();
			}
		}
		
		if( !defined('DOING_AJAX') ) {
			$this->load_class( 'non-ajax' );
			$this->non_ajax = new WCFM_Non_Ajax();
		}
		
		if ( !is_admin() || defined('DOING_AJAX') ) {
			$this->load_class( 'frontend' );
			$this->frontend = new WCFM_Frontend();
		}
		
		if ( !is_admin() || defined('DOING_AJAX') ) {
			$this->load_class( 'notification' );
			$this->wcfm_notification = new WCFM_Notification();
		}
		
		if( apply_filters( 'wcfm_is_pref_buddypress', true ) && WCFM_Dependencies::wcfm_biddypress_plugin_active_check() ) {
			if ( !is_admin() || defined('DOING_AJAX') ) {
				$this->load_class( 'buddypress' );
				$this->wcfm_buddypress = new WCFM_Buddypress();
			}
		}
		
		// init shortcode
		$this->load_class( 'shortcode' );
		$this->shortcode = new WCFM_Shortcode();
		
		if( !is_admin() && ( WCFM_Dependencies::wcfm_wc_dhl_shipping_active_check() || WCFM_Dependencies::wcfm_wc_fedex_shipping_active_check() ) ) {
			require_once( $this->library->views_path . 'wcfm-view-orders-details-fedex-dhl-express.php' );
		}
		
		// WCFM Fields Lib
		$this->wcfm_fields = $this->library->load_wcfm_fields();
	}
	
	/**
	 * WCFM Capability Load as per User Role
	 */
	function wcfm_capability_options_rules( $wcfm_capability_options ) {
		$user = wp_get_current_user();
		
		if ( in_array( 'vendor', $user->roles ) ) {
			$wcfm_capability_options =(array) get_option( 'wcfm_capability_options' );
			$wcfm_capability_options['manage_commission'] = 'yes';
			$wcfm_capability_options['wp_admin_view'] = 'yes';
		} elseif ( in_array( 'seller', $user->roles ) ) {
			$wcfm_capability_options =(array) get_option( 'wcfm_capability_options' );
			$wcfm_capability_options['manage_commission'] = 'yes';
			$wcfm_capability_options['wp_admin_view'] = 'yes';
		} elseif ( in_array( 'dc_vendor', $user->roles ) ) {
			$wcfm_capability_options = (array) get_option( 'wcfm_capability_options' );
			$wcfm_capability_options['manage_commission'] = 'yes';
			$wcfm_capability_options['wp_admin_view'] = 'yes';
		} elseif ( in_array( 'wc_product_vendors_admin_vendor', $user->roles ) ) {
			$wcfm_capability_options = (array) get_option( 'wcfm_capability_options' );
			$wcfm_capability_options['manage_commission'] = 'yes';
			$wcfm_capability_options['wp_admin_view'] = 'yes';
		} elseif ( in_array( 'wc_product_vendors_manager_vendor', $user->roles ) ) {
			$wcfm_capability_options = (array) get_option( 'wcfm_capability_options' );
			$wcfm_capability_options['manage_commission'] = 'yes';
			$wcfm_capability_options['wp_admin_view'] = 'yes';
		} else {
			$wcfm_capability_options = array();
		}
		
		return $wcfm_capability_options;
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
		$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'wc-frontend-manager' );
		
		//load_plugin_textdomain( 'wc-frontend-manager' );
		//load_textdomain( 'wc-frontend-manager', WP_LANG_DIR . "/wc-frontend-manager/wc-frontend-manager-$locale.mo");
		load_textdomain( 'wc-frontend-manager', $this->plugin_path . "/lang/wc-frontend-manager-$locale.mo");
		load_textdomain( 'wc-frontend-manager', ABSPATH . "wp-content/languages/plugins/wc-frontend-manager-$locale.mo");
	}

	public function load_class($class_name = '') {
		if ('' != $class_name && '' != $this->token) {
			require_once ('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}

	// End load_class()
	
	// WCV Shop Vendor 
	function wcvendors_register_post_type_shop_order_vendor( $shop_order_vendor ) {
		$shop_order_vendor['exclude_from_order_reports'] = true;
		return $shop_order_vendor;
	}

	/**
	 * Install upon activation.
	 *
	 * @access public
	 * @return void
	 */
	static function activate_wcfm() {
		global $WCFM;

		require_once ( $WCFM->plugin_path . 'helpers/class-wcfm-install.php' );
		$WCFM_Install = new WCFM_Install();

		update_option('wcfm_installed', 1);
	}
	
	/**
	 * Check upon update.
	 *
	 * @access public
	 * @return void
	 */
	static function update_wcfm() {
		global $WCFM, $WCFM_Query, $wpdb;

		if( !get_option( 'wcfm_updated_3_2_8' ) ) {
			
			require_once ( $WCFM->plugin_path . 'helpers/class-wcfm-install.php' );
			$WCFM_Install = new WCFM_Install();
			
			// Create Notice post for existing ones
			$sql = 'SELECT * FROM ' . $wpdb->prefix . 'wcfm_messages AS wcfm_messages';
			$sql .= ' WHERE 1=1';
			$sql .= " AND `is_notice` = 1";
			$wcfm_messages = $wpdb->get_results( $sql );
			if ( !empty( $wcfm_messages ) ) {
				register_post_type( 'wcfm_notice', array( 'public' => false ) );
				foreach ( $wcfm_messages as $wcfm_message ) {
					$new_notice = array(
						'post_title'   => substr( wp_strip_all_tags( $wcfm_message->message, true ), 0, 100 ),
						'post_status'  => 'publish',
						'post_type'    => 'wcfm_notice',
						'post_author'  => $wcfm_message->author_id,
						'post_content' => htmlspecialchars_decode($wcfm_message->message)
					);
					$notice_id = wp_insert_post( $new_notice );
					update_post_meta( $notice_id, 'allow_reply', 'no' );
					
					// Removing entry from wcfm_messages table
					$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'wcfm_messages WHERE ID = ' . $wcfm_message->ID );
				}
			}
			
			delete_option( 'wcfm_updated_3_2_7' );
			update_option( 'wcfm_updated_3_2_8', 1 );
		}
	}

	/**
	 * UnInstall upon deactivation.
	 *
	 * @access public
	 * @return void
	 */
	static function deactivate_wcfm() {
		global $WCFM;
		delete_option('wcfm_installed');
	}
	
	function get_wcfm_menus() {
		global $WCFM;
		$wcfm_menus = apply_filters( 'wcfm_menus', array( 'wcfm-products' => array( 'label'  => __( 'Products', 'wc-frontend-manager'),
																																			 'url'        => get_wcfm_products_url(),
																																			 'icon'       => 'cubes',
																																			 'has_new'    => true,
																																			 'new_class'  => 'wcfm_sub_menu_items_product_manage',
																																			 'new_url'    => get_wcfm_edit_product_url(),
																																			 'capability' => 'wcfm_product_menu',
																																			 'submenu_capability' => 'wcfm_add_new_product_sub_menu',
																																			 'priority'   => 5
																																			),
																									'wcfm-coupons' => array(  'label'      => __( 'Coupons', 'wc-frontend-manager'),
																																			 'url'        => get_wcfm_coupons_url(),
																																			 'icon'       => 'gift',
																																			 'has_new'    => true,
																																			 'new_class'  => 'wcfm_sub_menu_items_coupon_manage',
																																			 'new_url'    => get_wcfm_coupons_manage_url(),
																																			 'capability' => 'wcfm_add_new_coupon_sub_menu',
																																			 'priority'   => 40
																																			),
																									'wcfm-orders' => array(  'label'  => __( 'Orders', 'wc-frontend-manager'),
																																			 'url'        => get_wcfm_orders_url(),
																																			 'icon'       => 'shopping-cart',
																																			 'priority'   => 35
																																			),
																									'wcfm-reports' => array(  'label'      => __( 'Reports', 'wc-frontend-manager'),
																																			 'url'        => get_wcfm_reports_url(),
																																			 'icon'       => 'pie-chart',
																																			 'priority'   => 70
																																			),
																									'wcfm-settings' => array( 'label'      => __( 'Settings', 'wc-frontend-manager'),
																																			 'url'        => get_wcfm_settings_url(),
																																			 'icon'       => 'cogs',
																																			 'priority'   => 75
																																			)
																								)
														);
		
		if ( !wc_coupons_enabled() ) unset( $wcfm_menus['wcfm-coupons'] );
		
		uasort( $wcfm_menus, array( &$this, 'wcfm_sort_by_priority' ) );
		
		return $wcfm_menus;
	}
	
	/**
	 * List of WCFM modules
	 */
	function get_wcfm_modules() {
		$wcfm_modules = array(
													'enquiry'             => array( 'label' => __( 'Enquiry', 'wc-frontend-manager' ) ),
													'enquiry_tab'         => array( 'label' => __( 'Enquiry Tab', 'wc-frontend-manager' ), 'hints' => __( 'If you just want to hide Single Product page `Enquiry Tab`, but keep enable `Enquiry Module` for `Catalog Mode`.', 'wc-frontend-manager' ) ),
													'catalog'             => array( 'label' => __( 'Catalog', 'wc-frontend-manager' ), 'hints' => __( 'If you disable `Enquiry Module` then `Catalog Module` will stop working automatically.', 'wc-frontend-manager' ) ),
													'withdrawal'          => array( 'label' => __( 'Withdrawal', 'wc-frontend-manager' ) ),
													'notice'              => array( 'label' => __( 'Notice', 'wc-frontend-manager' ) ),
													'direct_message'      => array( 'label' => __( 'Notifications', 'wc-frontend-manager' ), 'notice' => true ),
													'knowledgebase'       => array( 'label' => __( 'Knowledgebase', 'wc-frontend-manager' ) ),
													'profile'             => array( 'label' => __( 'Profile', 'wc-frontend-manager' ) ),
													'custom_field'        => array( 'label' => __( 'Custom Field', 'wc-frontend-manager' ) ),
													'submenu'             => array( 'label' => __( 'Sub-menu', 'wc-frontend-manager' ), 'hints' => __( 'This will disable `Add New` sub-menus on hover.', 'wc-frontend-manager' ) ),
													);
		
		if( WCFM_Dependencies::wcfm_biddypress_plugin_active_check() ) {
			$wcfm_modules['buddypress'] = array( 'label' => __( 'BuddyPress Integration', 'wc-frontend-manager' ) );
		}
			
		return apply_filters( 'wcfm_modules', $wcfm_modules );
	}
	
	/**
	 * Sorts array of custom fields by priority value.
	 *
	 * @param array $a
	 * @param array $b
	 * @return int
	 */
	function wcfm_sort_by_priority( $a, $b ) {
		if ( ! isset( $a['priority'] ) || ! isset( $b['priority'] ) || $a['priority'] === $b['priority'] ) {
				return 0;
		}
		return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
	}
	
	function wcfm_color_setting_options() {
		global $WCFM;
		
		$color_options = apply_filters( 'wcfm_color_setting_options', array( 'wcfm_field_base_highlight_color' => array( 'label' => __( 'Base Highlighter Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_base_highlight_color_settings', 'default' => '#00897b', 'element' => '.wcfm_dashboard_membership_details, div.wcfm-collapse-content h2, #wcfm_page_load .fa, #wcfm-main-contentainer .wcfm_header_panel a:hover, #wcfm-main-contentainer .wcfm_header_panel a.active, ul.wcfm_products_menus li a, ul.wcfm_listings_menus li a, #wcfm-main-contentainer .wcfm-container-box .wcfm-container .booking_dashboard_section_icon, #wcfm-main-contentainer .wcfm_bookings_gloabl_settings, #wcfm-main-contentainer .fields_collapser, #wcfm-main-contentainer .wcfm_gloabl_settings, #wcfm-main-contentainer .wcfm_screen_manager_dummy, #wcfm-main-contentainer .wcfm_screen_manager, #wcfm-main-contentainer .woocommerce-reports-wide .postbox div.stats_range ul li.active a, .wcfm_reports_menus li a, #wcfm-main-contentainer .sales_schedule, #wcfm-main-contentainer .woocommerce-exporter-wrapper .wc-progress-form-content .woocommerce-importer-done::before, #wcfm-main-contentainer .woocommerce-exporter-wrapper .woocommerce-exporter .woocommerce-importer-done::before, #wcfm-main-contentainer .woocommerce-exporter-wrapper .woocommerce-importer .woocommerce-importer-done::before, #wcfm-main-contentainer .woocommerce-importer-wrapper .wc-progress-form-content .woocommerce-importer-done::before, #wcfm-main-contentainer .woocommerce-importer-wrapper .woocommerce-exporter .woocommerce-importer-done::before, .woocommerce-importer-wrapper .woocommerce-importer .woocommerce-importer-done::before, .woocommerce-progress-form-wrapper .wc-progress-form-content .woocommerce-importer-done::before, .woocommerce-progress-form-wrapper .woocommerce-exporter .woocommerce-importer-done::before, .woocommerce-progress-form-wrapper .woocommerce-importer .woocommerce-importer-done::before, .woocommerce-exporter-wrapper .wc-progress-steps li.done, .woocommerce-importer-wrapper .wc-progress-steps li.done, .woocommerce-progress-form-wrapper .wc-progress-steps li.done, .woocommerce-exporter-wrapper .wc-progress-steps li.active, .woocommerce-importer-wrapper .wc-progress-steps li.active, #wcfm-main-contentainer ul.wcfm_orders_menus li a, ul.wcfm_bookings_menus li a, #wcfm-main-contentainer .wc_bookings_calendar_form .wc_bookings_calendar td .bookings ul li a strong, #wcfm-main-contentainer .wc_bookings_calendar_form .tablenav .views a, #wcfm-main-contentainer .wc_bookings_calendar_form .tablenav .date_selector a, #wcfm-main-contentainer ul.wcfm_appointments_menus li a, #wcfm-main-contentainer .wcfm-container-box .wcfm-container .appointment_dashboard_section_icon, #wcfm-main-contentainer .wcfm_appointment_gloabl_settings, #wcfm-main-contentainer .wc_appointments_calendar_form .wc_appointments_calendar td .appointments ul li a strong, #wcfm-main-contentainer .wc_appointments_calendar_form .calendar_wrapper ul li a strong, #wcfm-main-contentainer .wc_appointments_calendar_form .tablenav .views a, #wcfm-main-contentainer .wc_appointments_calendar_form .tablenav .date_selector a, #wcfm-main-contentainer .mapp-m-panel a, #wcfm-main-contentainer .woocommerce-reports-wide .postbox div.stats_range ul li.custom.active, #wcfm-main-contentainer .sub_checklist_toggler, .woocommerce-progress-form-wrapper .wc-progress-steps li.active', 'style' => 'color', 'element2' => '.woocommerce-exporter-wrapper .wc-progress-steps li.active::before, .woocommerce-importer-wrapper .wc-progress-steps li.active::before, .woocommerce-progress-form-wrapper .wc-progress-steps li.active::before, .woocommerce-exporter-wrapper .wc-progress-steps li.done::before, .woocommerce-importer-wrapper .wc-progress-steps li.done::before, .woocommerce-progress-form-wrapper .wc-progress-steps li.done::before,  .woocommerce-exporter-wrapper .wc-progress-steps li.done::before, .woocommerce-importer-wrapper .wc-progress-steps li.done::before, .woocommerce-progress-form-wrapper .wc-progress-steps li.done::before, .woocommerce-exporter-wrapper .wc-progress-steps li.done, .woocommerce-importer-wrapper .wc-progress-steps li.done, .woocommerce-progress-form-wrapper .wc-progress-steps li.done, .woocommerce-exporter-wrapper .wc-progress-steps li.active, .woocommerce-importer-wrapper .wc-progress-steps li.active, .wcfm_vacation_msg, a.add_new_wcfm_ele_dashboard:hover, a.wcfm_import_export:hover, #wcfm_auto_suggest_product_title li a:hover', 'style2' => 'background-color', 'element3' => '#wcfm-main-contentainer .woocommerce-reports-wide .button:hover, #mapp_e_search, #wcfm-main-contentainer #wcfm_quick_edit_button:hover, #wcfm-main-contentainer #wcfm_screen_manager_button:hover, .woocommerce-exporter-wrapper .wc-progress-steps li.done::before, .woocommerce-importer-wrapper .wc-progress-steps li.done::before, .woocommerce-progress-form-wrapper .wc-progress-steps li.done::before, #wcfm-main-contentainer .wcfm_admin_message .primary:hover, #wcfm-main-contentainer input.wcfm_submit_button:hover, #wcfm-main-contentainer a.wcfm_submit_button:hover, #wcfm-main-contentainer .wcfm_add_attribute:hover, #wcfm-main-contentainer input.upload_button:hover, #wcfm-main-contentainer input.remove_button:hover, #wcfm-main-contentainer .multi_input_block_manupulate:hover, #wcfm-main-contentainer .dataTables_wrapper .dt-buttons .dt-button:hover, #wcfm_vendor_approval_response_button:hover', 'style3' => 'background', 'element4' => '#wcfm-main-contentainer .page_collapsible::before, #wcfm-main-contentainer input.wcfm_submit_button, #wcfm-main-contentainer a.wcfm_submit_button, #wcfm-main-contentainer .wcfm_add_attribute, #wcfm-main-contentainer input.upload_button, #wcfm-main-contentainer input.remove_button, #wcfm-main-contentainer a.add_new_wcfm_ele_dashboard, #wcfm-main-contentainer a.wcfm_import_export, #wcfm_menu .wcfm_menu_items a.wcfm_menu_item::before, #wcfm-main-contentainer .wcfm-page-headig::before, .wcfm_dashboard_welcome_content::before, .wcfm_dashboard_stats_block, .wcfm-container-box .wcfm-container, .wcfm-collapse .wcfm-container, .wcfm-tabWrap, .wcfm-action-icon, #wcfm_vendor_approval_response_button', 'style4' => 'border-bottom-color', 'element5' => '.woocommerce-progress-form-wrapper .wc-progress-steps li.active, .woocommerce-exporter-wrapper .wc-progress-steps li.active::before, .woocommerce-importer-wrapper .wc-progress-steps li.active::before, .woocommerce-progress-form-wrapper .wc-progress-steps li.active::before, .wcfm_header_panel a.wcfm_header_panel_profile.active img', 'style5' => 'border-color' ),
																																				 'wcfm_field_header_bg_color' => array( 'label' => __( 'Top Bar Background Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_header_bg_color_settings', 'default' => '#afd2cf', 'element' => '#wcfm-main-contentainer .wcfm-page-headig', 'style' => 'background' ),
																																				 'wcfm_field_header_text_color' => array( 'label' => __( 'Top Bar Text Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_header_text_color_settings', 'default' => '#2f3b4c', 'element' => '#wcfm-main-contentainer .wcfm-page-headig, #wcfm-main-contentainer .wcfm-page-headig .fa', 'style' => 'color' ),
																																				 'wcfm_field_dashboard_bg_color' => array( 'label' => __( 'Dashboard Background Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_dashboard_bg_color_settings', 'default' => '#eceef2', 'element' => '#wcfm-main-contentainer .wcfm-collapse', 'style' => 'background', 'element2' => '#wcfm_menu .wcfm_menu_items a.active::after', 'style2' => 'border-right-color' ),
																																				 'wcfm_field_container_color' => array( 'label' => __( 'Container Background Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_container_background_color_settings', 'default' => '#ffffff', 'element' => '.wcfm-collapse .wcfm-container, #wcfm-main-contentainer div.wcfm-content', 'style' => 'background' ),
																																				 'wcfm_field_primary_bg_color' => array( 'label' => __( 'Container Head Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_primary_bg_color_settings', 'default' => '#2a3344', 'element' => '.page_collapsible, .collapse-close', 'style' => 'background' ),
																																				 'wcfm_field_primary_font_color' => array( 'label' => __( 'Container Head Text Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_primary_font_color_settings', 'default' => '#b0bec5', 'element' => '.page_collapsible, .page_collapsible label, .collapse-close', 'style' => 'color' ),
																																				 'wcfm_field_secondary_bg_color' => array( 'label' => __( 'Container Head Active Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_secondary_bg_color_settings', 'default' => '#2a3344', 'element' => '.collapse-open', 'style' => 'background' ),
																																				 'wcfm_field_secondary_font_color' => array( 'label' => __( 'Container Head Active Text Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_secondary_font_color_settings', 'default' => '#ffffff', 'element' => '.collapse-open, .page_collapsible:hover label, .page_collapsible.collapse-open label', 'style' => 'color' ),
																																				 'wcfm_field_menu_bg_color' => array( 'label' => __( 'Menu Background Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_menu_bg_color_settings', 'default' => '#2d4e4c', 'element' => '#wcfm_menu', 'style' => 'background' ),
																																				 'wcfm_field_menu_icon_bg_color' => array( 'label' => __( 'Menu Item Background', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_menu_icon_bg_color_settings', 'default' => '#2a3344', 'element' => '#wcfm_menu .wcfm_menu_items a.wcfm_menu_item, #wcfm_menu span.wcfm_sub_menu_items', 'style' => 'background' ),
																																				 'wcfm_field_menu_icon_color' => array( 'label' => __( 'Menu Item Text Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_menu_icon_color_settings', 'default' => '#b0bec5', 'element' => '#wcfm_menu .wcfm_menu_item span, #wcfm_menu span.wcfm_sub_menu_items a', 'style' => 'color' ),
																																				 'wcfm_field_menu_icon_active_bg_color' => array( 'label' => __( 'Menu Active Item Background', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_menu_icon_active_bg_color_settings', 'default' => '#2a3344', 'element' => '#wcfm_menu .wcfm_menu_items a.active', 'style' => 'background' ),
																																				 'wcfm_field_menu_icon_active_color' => array( 'label' => __( 'Menu Active Item Text Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_menu_icon_active_color_settings', 'default' => '#ffffff', 'element' => '#wcfm_menu .wcfm_menu_items:hover a span.fa, #wcfm_menu .wcfm_menu_items a.active span, .wcfm_menu_logo h4, .wcfm_menu_logo h4 a, .wcfm_menu_no_logo h4, .wcfm_menu_no_logo h4 a', 'style' => 'color' ),
																																				 'wcfm_field_button_color' => array( 'label' => __( 'Button Background Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_button_background_color_settings', 'default' => '#2a3344', 'element' => '#wcfm-main-contentainer a.add_new_wcfm_ele_dashboard, #wcfm-main-contentainer a.wcfm_import_export, #wcfm-main-contentainer input.wcfm_submit_button, #wcfm-main-contentainer a.wcfm_submit_button, #wcfm-main-contentainer .wcfm_add_attribute, #wcfm-main-contentainer input.upload_button, #wcfm-main-contentainer input.remove_button, #wcfm-main-contentainer .dataTables_wrapper .dt-buttons .dt-button, #wcfm_vendor_approval_response_button', 'style' => 'background' ),
																																				 'wcfm_field_button_text_color' => array( 'label' => __( 'Button Text Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_button_text_color_settings', 'default' => '#b0bec5', 'element' => '#wcfm-main-contentainer a.add_new_wcfm_ele_dashboard, #wcfm-main-contentainer a.wcfm_import_export, #wcfm-main-contentainer input.wcfm_submit_button, #wcfm-main-contentainer a.wcfm_submit_button, #wcfm-main-contentainer .wcfm_add_attribute, #wcfm-main-contentainer input.upload_button, #wcfm-main-contentainer input.remove_button, #wcfm-main-contentainer .dataTables_wrapper .dt-buttons .dt-button', 'style' => 'color' ),
																																			) );
		
		return $color_options;
	}
	
	/**
	 * Create WCFM custom CSS
	 */
	function wcfm_create_custom_css() {
		global $WCFM;
		
		$wcfm_options = get_option('wcfm_options');
		$color_options = $WCFM->wcfm_color_setting_options();
		$custom_color_data = '';
		foreach( $color_options as $color_option_key => $color_option ) {
		  $custom_color_data .= $color_option['element'] . '{ ' . "\n";
			$custom_color_data .= "\t" . $color_option['style'] . ': ';
			if( isset( $wcfm_options[ $color_option['name'] ] ) ) { $custom_color_data .= $wcfm_options[ $color_option['name'] ]; } else { $custom_color_data .= $color_option['default']; }
			$custom_color_data .= ';' . "\n";
			$custom_color_data .= '}' . "\n\n";
			
			if( isset( $color_option['element2'] ) && isset( $color_option['style2'] ) ) {
				$custom_color_data .= $color_option['element2'] . '{ ' . "\n";
				$custom_color_data .= "\t" . $color_option['style2'] . ': ';
				if( isset( $wcfm_options[ $color_option['name'] ] ) ) { $custom_color_data .= $wcfm_options[ $color_option['name'] ]; } else { $custom_color_data .= $color_option['default']; }
				$custom_color_data .= ';' . "\n";
				$custom_color_data .= '}' . "\n\n";
			}
			
			if( isset( $color_option['element3'] ) && isset( $color_option['style3'] ) ) {
				$custom_color_data .= $color_option['element3'] . '{ ' . "\n";
				$custom_color_data .= "\t" . $color_option['style3'] . ': ';
				if( isset( $wcfm_options[ $color_option['name'] ] ) ) { $custom_color_data .= $wcfm_options[ $color_option['name'] ]; } else { $custom_color_data .= $color_option['default']; }
				$custom_color_data .= ';' . "\n";
				$custom_color_data .= '}' . "\n\n";
			}
			
			if( isset( $color_option['element4'] ) && isset( $color_option['style4'] ) ) {
				$custom_color_data .= $color_option['element4'] . '{ ' . "\n";
				$custom_color_data .= "\t" . $color_option['style4'] . ': ';
				if( isset( $wcfm_options[ $color_option['name'] ] ) ) { $custom_color_data .= $wcfm_options[ $color_option['name'] ]; } else { $custom_color_data .= $color_option['default']; }
				$custom_color_data .= ';' . "\n";
				$custom_color_data .= '}' . "\n\n";
			}
			
			if( isset( $color_option['element5'] ) && isset( $color_option['style5'] ) ) {
				$custom_color_data .= $color_option['element5'] . '{ ' . "\n";
				$custom_color_data .= "\t" . $color_option['style5'] . ': ';
				if( isset( $wcfm_options[ $color_option['name'] ] ) ) { $custom_color_data .= $wcfm_options[ $color_option['name'] ]; } else { $custom_color_data .= $color_option['default']; }
				$custom_color_data .= ';' . "\n";
				$custom_color_data .= '}' . "\n\n";
			}
		}
		
		$upload_dir      = wp_upload_dir();

		$files = array(
			array(
				'base' 		=> $upload_dir['basedir'] . '/wcfm',
				'file' 		=> 'wcfm-style-custom-' . time() . '.css',
				'content' 	=> $custom_color_data,
			)
		);

		$wcfm_style_custom = get_option( 'wcfm_style_custom' );
		if( file_exists( trailingslashit( $upload_dir['basedir'] ) . 'wcfm/' . $wcfm_style_custom ) ) {
			unlink( trailingslashit( $upload_dir['basedir'] ) . 'wcfm/' . $wcfm_style_custom );
		}
		
		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) ) {
				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
					$wcfm_style_custom = $file['file'];
					update_option( 'wcfm_style_custom', $file['file'] );
					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
		return $wcfm_style_custom;
	}
	
	function wcfm_get_attachment_id($attachment_url) {
		global $wpdb;
		$upload_dir_paths = wp_upload_dir();
		$attachment_id = 0;
		if( class_exists('WPH') ) {
			global $wph;
			$new_upload_path = $wph->functions->get_module_item_setting('new_upload_path');
			$new_content_path = $wph->functions->get_module_item_setting('new_content_path');
			$attachment_url = str_replace( $new_upload_path, 'wp-content/uploads', $attachment_url );
		}
		
		if( class_exists('HideMyWP') ) {
			global $HideMyWP;
			$new_upload_path = trim($HideMyWP->opt('new_upload_path'));
			$new_content_path = trim($HideMyWP->opt('new_content_path'));
			$attachment_url = str_replace( $new_content_path, '/wp-content', str_replace( $new_upload_path, '/uploads', $attachment_url ) );
		}
		
		// If this is the URL of an auto-generated thumbnail, get the URL of the original image
		if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] . '/' ) ) {
			$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
			
			// Remove the upload path base directory from the attachment URL
			$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );
			
			// Finally, run a custom database query to get the attachment ID from the modified attachment URL
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
		} elseif( class_exists( 'Amazon_Web_Services' ) ) {
			global $as3cf;
			$scheme = $as3cf->get_s3_url_scheme();
			$bucket = $as3cf->get_setting( 'bucket' );
			$region = $as3cf->get_setting( 'region' );
			if ( is_wp_error( $region ) ) {
				$region = '';
			}
	
			$domain = $as3cf->get_s3_url_domain( $bucket, $region, null, array(), true );
			$amazon_s3_url_domain = $scheme . '://' . $domain . '/';
			
			$attachment_url = str_replace( $amazon_s3_url_domain, '', $attachment_url );
		
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = 'amazonS3_info' AND wpostmeta.meta_value LIKE '%s' AND wposts.post_type = 'attachment'", '%' . $attachment_url . '%' ) );
		}
		
		return $attachment_id; 
	}
	
	/**
	 * Prepare Chart Data
	 */
	function wcfm_prepare_chart_data( $chart_datas ) {
		
		$chart_data_label = '';
		$chart_data_set = '';
		
		if( !empty( $chart_datas ) ) {
			$chart_data_label .= '';
			$chart_data_set .= '';
			foreach( $chart_datas as $chart_data_key => $chart_data ) {
				if( $chart_data_label != '' ) $chart_data_label .= ',';
				if( $chart_data_set != '' ) $chart_data_set .= ',';
				
				$chart_data_label .= '"' . date( 'm d', ($chart_data_key/1000) ) . '"';
				$chart_data_set   .= '"' . $chart_data[1] . '"';
				
			}
			$chart_data_sets = '{"labels" : [' . $chart_data_label . '], "datas" : [' . $chart_data_set . ']}';
		}
		
		return $chart_data_sets;
	}
	
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