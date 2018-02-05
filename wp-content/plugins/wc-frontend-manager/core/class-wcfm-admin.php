<?php

/**
 * WCFM Admin Class
 *
 * @version		1.0.0
 * @package		wcfm/core
 * @author 		WC Lovers
 */
class WCFM_Admin {
	
 	public function __construct() {
 		global $WCFM;
 		
 		// Browse WCFM Dashbaord setup page
 		add_action( 'admin_init', array( &$this, 'wcfm_redirect_to_setup' ), 5 );
 		
 		// WCFM - Ultimate inactive notice
 		if(!WCFM_Dependencies::wcfmu_plugin_active_check()) {
			add_action( 'admin_notices', array( &$this, 'wcfm_wcfmu_inactive_notice' ) );
		} else {
			// WCFM - Membership inactive notice 
			if(!WCFM_Dependencies::wcfmvm_plugin_active_check()) {
				add_action( 'admin_notices', array( &$this, 'wcfm_wcfmvm_inactive_notice' ) );
			} else {
				// WCFM - Groups & Staffs inactive notice 
				if(!WCFM_Dependencies::wcfmgs_plugin_active_check()) {
					add_action( 'admin_notices', array( &$this, 'wcfm_wcfmgs_inactive_notice' ) );
				}
			}
		}
 		
 		if ( current_user_can( 'view_woocommerce_reports' ) || current_user_can( 'manage_woocommerce' ) || current_user_can( 'publish_shop_orders' ) ) {
 			// WCFM Dashboard widget
			add_action( 'wp_dashboard_setup', array( &$this, 'wcfm_admin_dashboard_init' ) );
 		
			// WCFM view meta boxes
			add_action( 'add_meta_boxes', array( &$this, 'wcfm_meta_boxes' ), 10, 2 );
			
			// WCFM View @dashboards
			add_action( 'restrict_manage_posts', array( $this, 'wcfm_view_manage_posts' ) );
		}
		
		/**
		 * Register our wcfm_settings_init to the admin_init action hook
		 */
		add_action( 'admin_init', array( &$this, 'wcfm_settings_init' ) );
		
		/**
		 * Register our wcfm_options_page to the admin_menu action hook
		 */
		add_action( 'admin_menu', array( &$this, 'wcfm_options_page' ) );
		
		// WCFM Admin Style
		add_action( 'admin_enqueue_scripts', array( &$this, 'wcfm_admin_script' ), 30 );
	}
	
	/**
	 * WCFM activation redirect transient
	 */
	function wcfm_redirect_to_setup(){
		if ( get_transient( '_wc_activation_redirect' ) ) {
			delete_transient( '_wc_activation_redirect' );
			return;
		}
		if ( get_transient( '_wcfm_activation_redirect' ) ) {
			delete_transient( '_wcfm_activation_redirect' );
			if ( ( ! empty( $_GET['page'] ) && in_array( $_GET['page'], array( 'wcfm-setup' ) ) ) || is_network_admin() || isset( $_GET['activate-multi'] ) || apply_filters( 'wcfm_prevent_automatic_setup_redirect', false ) ) {
			  return;
			}
			wp_safe_redirect( admin_url( 'index.php?page=wcfm-setup' ) );
			exit;
		}
	}
	
	/**
	 * WCFM - Ultimate notice
	 *
	 * @since  3.3.6
	 *
	 * @return void
	 */
	public function wcfm_wcfmu_inactive_notice() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// check if it has already been dismissed
		$offer_key = 'wcfm_wcfmu_inactive1501';
		$hide_notice = get_option( $offer_key . '_tracking_notice', 'no' );

		if ( 'hide' == $hide_notice ) {
			return;
		}

		$offer_msg = sprintf( __( '<h2>
										           Is there anything missing in your front-end dashboard !!!
								               </h2>', 'wc-frontend-manager' ) );
		/*$offer_msg = sprintf( __( '<h2>
										           Warm wishes for Bright & Prosperous New Year. It\'s time to grab your best deals !!!
								               </h2>', 'wc-frontend-manager' ) );*/
		$offer_msg .= sprintf( __( '<p>WooCommerce Frontend Manage - Ultimate is there to fill up all those for you. Product image gallery, shipment tracing, direct messaging, GEO map, product importer, custom attriutes and many many more, almost a never ending features list for you.</p>', 'wc-frontend-manager' ) );
		?>
			<div class="notice is-dismissible wcfm_addon_inactive_notice_box" id="wcfm-ultimate-notice">
				<img src="https://ps.w.org/wc-frontend-manager/assets/icon-128x128.jpg?rev=1800818" alt="">
				<?php echo $offer_msg; ?>
				<span class="dashicons dashicons-megaphone"></span>
				<a href="https://wclovers.com/product/woocommerce-frontend-manager-ultimate/" class="button button-primary promo-btn" target="_blank"><?php _e( 'WCFM U >>', 'wc-frontend-manager' ); ?></a>
			</div>

			<script type='text/javascript'>
				jQuery('body').on('click', '#wcfm-ultimate-notice .notice-dismiss', function(e) {
					e.preventDefault();

					wp.ajax.post('wcfm-dismiss-addon-inactive-notice', {
						wcfm_wcfmu_inactive: true
					});
				});
			</script>
		<?php
	}
	
	/**
	 * WCFM - Membership notice
	 *
	 * @since  3.3.6
	 *
	 * @return void
	 */
	public function wcfm_wcfmvm_inactive_notice() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// check if it has already been dismissed
		$offer_key = 'wcfm_wcfmvm_inactive1501';
		$hide_notice = get_option( $offer_key . '_tracking_notice', 'no' );

		if ( 'hide' == $hide_notice ) {
			return;
		}

		$offer_msg = sprintf( __( '<h2>
										           Now setup your vendor membership subscription in minutes & it\'s FREE !!!
								               </h2>', 'wc-frontend-manager' ) );
		$offer_msg .= sprintf( __( '<p>A simple membership plugin for offering FREE AND PREMIUM SUBSCRIPTION for your multi-vendor marketplace. You may set up unlimited membership levels (example: free, silver, gold etc) with different pricing plan, capabilities and commission.</p>', 'wc-frontend-manager' ) );
		?>
			<div class="notice is-dismissible wcfm_addon_inactive_notice_box" id="wcfm-membership-notice">
				<img src="https://ps.w.org/wc-multivendor-membership/assets/icon-128x128.jpg?rev=1788354" alt="">
				<?php echo $offer_msg; ?>
				<span class="dashicons dashicons-groups"></span>
				<a href="https://wordpress.org/plugins/wc-multivendor-membership/" class="button button-primary promo-btn" target="_blank"><?php _e( 'View Details', 'wc-frontend-manager' ); ?></a>
			</div>

			<script type='text/javascript'>
				jQuery('body').on('click', '#wcfm-membership-notice .notice-dismiss', function(e) {
					e.preventDefault();

					wp.ajax.post('wcfm-dismiss-addon-inactive-notice', {
						wcfm_wcfmvm_inactive: true
					});
				});
			</script>
		<?php
	}
	
	/**
	 * WCFM - Groups & Staffs notice
	 *
	 * @since  3.3.6
	 *
	 * @return void
	 */
	public function wcfm_wcfmgs_inactive_notice() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// check if it has already been dismissed
		$offer_key = 'wcfm_wcfmgs_inactive1501';
		$hide_notice = get_option( $offer_key . '_tracking_notice', 'no' );

		if ( 'hide' == $hide_notice ) {
			return;
		}

		$offer_msg = sprintf( __( '<h2>
										           Do you want to have different capabilities for each membership levels !!!
								               </h2>', 'wc-frontend-manager' ) );
		/*$offer_msg = sprintf( __( '<h2>
										           Warm wishes for Bright & Prosperous New Year. It\'s time to grab your best deals !!!
								               </h2>', 'wc-frontend-manager' ) );*/
		$offer_msg .= sprintf( __( '<p>WCFM - Groups & Staffs will empower you to set up totaly different capability rules for your each membership levels very easily.</p>', 'wc-frontend-manager' ) );
		?>
			<div class="notice is-dismissible wcfm_addon_inactive_notice_box" id="wcfm-groups-sttafs-notice">
				<img src="https://ps.w.org/wc-multivendor-membership/assets/icon-128x128.jpg?rev=1788354" alt="">
				<?php echo $offer_msg; ?>
				<span class="dashicons dashicons-groups"></span>
				<a href="http://wclovers.com/product/woocommerce-frontend-manager-groups-staffs/" class="button button-primary promo-btn" target="_blank"><?php _e( 'WCFM GS >>', 'wc-frontend-manager' ); ?></a>
			</div>

			<script type='text/javascript'>
				jQuery('body').on('click', '#wcfm-groups-sttafs-notice .notice-dismiss', function(e) {
					e.preventDefault();

					wp.ajax.post('wcfm-dismiss-addon-inactive-notice', {
						wcfm_wcfmgs_inactive: true
					});
				});
			</script>
		<?php
	}
	
	/**
	 * Admin dashboard widget init
	 */
	function wcfm_admin_dashboard_init() {
		global $WCFM;
		wp_add_dashboard_widget( 'wcfm_dashboard_status', __( 'WCFM View', 'wc-frontend-manager' ), array( &$this, 'wcfm_status_widget' ) );
	}
	
	/**
	 * WCFM status widget
	 */
	function wcfm_status_widget() {
		global $wpdb, $WCFM;
		
		$WCFM->library->load_chartjs_lib();
		?>
    <style>
    #sales-piechart {
			background: #fff;
			padding: 12px;
			height: 275px;
			margin: 10px;
		}
		#wcfm-logo {
			text-align: right;
			margin: 10px;
		}
		</style>
		<div class="postbox">
			<a href="<?php echo get_wcfm_page(); ?>">
				<div id="sales-piechart"><canvas id="sales-piechart-canvas"></canvas></div>
				<div id="wcfm-logo"><img src="<?php echo $WCFM->plugin_url; ?>/assets/images/wcfm-30x30.png" alt="WCFM Home" /></div>
			</a>
		</div>
    <?php
    do_action('after_wcfm_dashboard_sales_report');
	}
	
	/**
	 * Register WCFM Metabox
	 */
	function wcfm_meta_boxes( $post_type, $post ) {
		global $WCFM;
		
		if( in_array( $post_type, array( 'product', 'shop_coupon', 'shop_order' ) ) ) {
			add_meta_box( 'wcfm-view', __( 'WCFM View', 'wc-frontend-manager' ), array( &$this, 'wcfm_view_metabox' ), 'product', 'side', 'high' );
			add_meta_box( 'wcfm-view', __( 'WCFM View', 'wc-frontend-manager' ), array( &$this, 'wcfm_view_metabox' ), 'shop_coupon', 'side', 'high' );
			add_meta_box( 'wcfm-view', __( 'WCFM View', 'wc-frontend-manager' ), array( &$this, 'wcfm_view_metabox' ), 'shop_order', 'side', 'high' );
		}
 	}
	
	/**
	 * WCFM View Meta Box
	 */
	function wcfm_view_metabox( $post ) {
		global $WCFM;
		
		$wcfm_url = get_wcfm_page();
		if( $post->ID && $post->post_type ) {
			if( $post->post_type == 'product' ) $wcfm_url = get_wcfm_edit_product_url($post->ID);
			else if( $post->post_type == 'shop_coupon' ) $wcfm_url = get_wcfm_coupons_manage_url($post->ID);
			else if( $post->post_type == 'shop_order' ) $wcfm_url = get_wcfm_view_order_url($post->ID);
		}
		
		echo '<div style="text-align: center;"><a href="' . $wcfm_url . '"><img src="' . $WCFM->plugin_url . '/assets/images/wcfm-30x30.png" alt="' . __( 'WCFM Home', 'wc-frontend-manager' ) . '" /></a></div>';
	}
	
	/**
	 * WCFM View at dashboards
	 */
	function wcfm_view_manage_posts() {
		global $WCFM, $typenow;

		if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ) ) ) {
			echo '<a style="float: right;" href="' . get_wcfm_orders_url() . '"><img src="' . $WCFM->plugin_url . '/assets/images/wcfm-30x30.png" alt="' . __( 'WCFM Home', 'wc-frontend-manager' ) . '" /></a>';
		} elseif ( 'product' == $typenow ) {
			echo '<a style="float: right;" href="' . get_wcfm_products_url() . '"><img src="' . $WCFM->plugin_url . '/assets/images/wcfm-30x30.png" alt="' . __( 'WCFM Home', 'wc-frontend-manager' ) . '" /></a>';
		} elseif ( 'shop_coupon' == $typenow ) {
			echo '<a style="float: right;" href="' . get_wcfm_coupons_url() . '"><img src="' . $WCFM->plugin_url . '/assets/images/wcfm-30x30.png" alt="' . __( 'WCFM Home', 'wc-frontend-manager' ) . '" /></a>';
		}
	}
	
	/**
	 * Custom option and settings
	 */
	function wcfm_settings_init() {
		global $WCFM;
		 // register a new setting for "wcfm" page
		 register_setting( 'wcfm', 'wcfm_page_options' );
		 
		 // register a new section in the "wcfm" page
		 add_settings_section(
			 'wcfm_section_developers',
			 __( 'WCFM Page Settings', $WCFM->text_domain ),
			 array( &$this, 'wcfm_section_developers_cb'),
			 'wcfm'
		 );
		 
		 // register a new field in the "wcfm_section_developers" section, inside the "wcfm" page
		 add_settings_field(
			 'wcfm_field_page', 
			 __( 'WCFM Page', $WCFM->text_domain ),
			  array( &$this, 'wcfm_field_page_cb' ),
			 'wcfm',
			 'wcfm_section_developers',
			 [
			 'label_for' => 'wc_frontend_manager_page_id',
			 'class' => 'wcfm_row',
			 'wcfm_custom_data' => 'wc_frontend_manager_page',
			 ]
		 );
		 
	}
	
	/**
	 * custom option and settings:
	 * callback functions
	 */
	function wcfm_section_developers_cb( $args ) {
		global $WCFM;
		
		_e( 'This page should contain "[wc_frontend_manager]" short code', 'wc-frontend-manager' );
		?>
		<div class="wcfm_setting_help_box">
	    <p><?php printf( __( 'WCFM totally works from front-end ... check dashboard settings %shere >>%s', 'wc-frontend-manager' ), '<a class="primary" target="_blank" href="' . get_wcfm_settings_url() . '">', '</a>' ); ?></p>
	  </div>
	  <?php
	}
	 
	function wcfm_field_page_cb( $args ) {
		global $WCFM;
	  // get the value of the setting we've registered with register_setting()
	  $options = get_option( 'wcfm_page_options' );
	  $pages = get_pages(); 
	  $pages_array = array();
		$woocommerce_pages = array ( wc_get_page_id('shop'), wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('myaccount'));
		foreach ( $pages as $page ) {
			if(!in_array($page->ID, $woocommerce_pages)) {
				$pages_array[$page->ID] = $page->post_title;
			}
		}
	 // output the field
	 ?>
	 <select id="<?php echo esc_attr( $args['label_for'] ); ?>"
	 data-custom="<?php echo esc_attr( $args['wcfm_custom_data'] ); ?>"
	 name="wcfm_page_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	 >
	 <?php
	   foreach($pages_array as $p_id => $p_name) {
	   	 ?>
	   	 <option value="<?php echo $p_id; ?>" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], $p_id, false ) ) : ( '' ); ?>>
	   	 <?php esc_html_e( $p_name, $WCFM->text_domain ); ?>
	   	 </option>
	   	 <?php
	   }
	 ?>
	 </select>
	 <?php
	}
	
	/**
	 * top level menu
	 */
	function wcfm_options_page() {
		global $WCFM;
		 // add top level menu page
		 add_menu_page(
		 __( 'WC Frontend Manager', $WCFM->text_domain ),
		 __( 'WCFM Options', $WCFM->text_domain ),
		 'manage_options',
		 'wcfm_settings',
		 array( &$this, 'wcfm_options_page_html' )
		 );
	}
 
	/**
	 * top level menu:
	 * callback functions
	 */
	function wcfm_options_page_html() {
		global $WCFM;
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
		 return;
		}
		
		// add error/update messages
		
		if ( isset( $_GET['settings-updated'] ) ) {
		 // add settings saved message with the class of "updated"
		 add_settings_error( 'wcfm_messages', 'wcfm_message', __( 'Settings Saved', $WCFM->text_domain ), 'updated' );
		}
		
		// show error/update messages
		settings_errors( 'wcfm_messages' );
		?>
		<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<div style="float: left; display: inline-block; width: 60%;">
				 <?php  
					 settings_fields( 'wcfm' );
					 do_settings_sections( 'wcfm' );
					 submit_button( 'Save Settings' );
				 ?>
			</div>
			
			<div class="wcfm_admin_message_wrapper">
				<?php if(!WCFM_Dependencies::wcfmu_plugin_active_check()) { ?>
					<div class="wcfm_admin_message wcfm_admin_help_docs">
						<h2>How can we help you?</h2>
						<ul style="list-style: outside; margin-left: 50px;">
							<li><a target="_blank" href="https://wclovers.com/blog/woocommerce-frontend-manager/">WCFM - what will do for you?</a></li>
							<li><a target="_blank" href="https://wclovers.com/blog/wcfm-dashboard-as-site-template/">Customize WCFM Dashboard</a></li>
							<li><a target="_blank" href="https://wclovers.com/blog/choose-best-woocommerce-multi-vendor-marketplace-plugin/">Choose your Marketplace plugin</a></li>
							<li><a target="_blank" href="https://wclovers.com/blog/location-products-wcfm/">WCFM -> GEO my WP</a></li>
						</ul>
					</div>
				<?php } ?>
				<?php if(!WCFM_Dependencies::wcfmvm_plugin_active_check()) { ?>
					<div class="wcfm_admin_message">
						<h2>Setup vendor subscription in 5 minutes -</h2>
						<a class="primary membership_btn" href="https://wordpress.org/plugins/wc-multivendor-membership/" target="_blank">WCFM - Membership</a>
					</div>
				<?php } ?>
				<?php if(!WCFM_Dependencies::wcfmu_plugin_active_check()) { ?>
					<div class="wcfm_admin_message">
						<h2>Are you looking for something like this?</h2>
						<ul style="list-style: outside; margin-left: 50px;">
							<li>Image Gallery</li>
							<li>Custom Attributes</li>
							<li>PDF Invoice</li>
							<li>Product Importer</li>
							<li>Shipping Tracking</li>
							<li>Advanced Custom Fields</li>
							<li>GEO Map integration</li>
						</ul>
						<a class="primary" href="https://wclovers.com/product/woocommerce-frontend-manager-ultimate/" target="_blank">Click here to get all this...</a>
					</div>
				<?php } elseif(!WCFM_Dependencies::wcfma_plugin_active_check()) { ?>
					<div class="wcfm_admin_message">
						<h2>How a store can even without Analytics?</h2>
						<ul style="list-style: outside; margin-left: 50px;">
							<li>Analytics by Region</li>
							<li>Analytics by Store</li>
							<li>Analytics by Product</li>
							<li>Analytics by Category</li>
							<li>Analytics Comparison</li>
						</ul>
						<a class="primary" href="https://wclovers.com/product/woocommerce-frontend-manager-analytics/" target="_blank">Click here to get all this...</a>
					</div>
				<?php } ?>
				<div class="wcfm_admin_message wcfm_admin_support_docs">
					<h2>All we want is Love!!</h2>
					<ul style="list-style: outside; margin-left: 50px;">
						<li><a href="https://twitter.com/wcfmlovers" target="_blank">Get in touch</a></li>
						<li><a href="https://wordpress.org/support/plugin/wc-frontend-manager/reviews/" target="_blank">Be with US</a></li>
					</ul>
				</div>
			</div>
		</form>
		</div>
		<?php
  }
	
  function wcfm_admin_script() {
  	global $WCFM;
  	
 	  $screen = get_current_screen(); 
 	 
 	  // WC Icon set
	  //wp_enqueue_style( 'wcfm_icon_css',  $WCFM->library->css_lib_url . 'wcfm-style-icon.css', array(), $WCFM->version );
	  
	  // Font Awasome Icon set
	  //wp_enqueue_style( 'wcfm_fa_icon_css',  $WCFM->plugin_url . 'assets/fonts/font-awesome/css/font-awesome.min.css', array(), $WCFM->version );
	  
	  // Admin Bar CSS
	  wp_enqueue_style( 'wcfm_admin_bar_css',  $WCFM->library->css_lib_url . 'wcfm-style-adminbar.css', array(), $WCFM->version );
  }
  
}