<?php
/**
 * WCFM plugin core
 *
 * Enquiry board core
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   3.2.8
 */
 
class WCFM_Enquiry {

	public function __construct() {
		global $WCFM;
		
		add_filter( 'wcfm_query_vars', array( &$this, 'wcfm_enquiry_query_vars' ), 20 );
		add_filter( 'wcfm_endpoint_title', array( &$this, 'wcfm_enquiry_endpoint_title' ), 20, 2 );
		add_action( 'init', array( &$this, 'wcfm_enquiry_init' ), 20 );
		
		// Enquiry Endpoint Edit
		add_filter( 'wcfm_endpoints_slug', array( $this, 'enquiry_wcfm_endpoints_slug' ) );
		
		// Enquiry Load Scripts
		add_action( 'wcfm_load_scripts', array( &$this, 'load_scripts' ), 30 );
		add_action( 'after_wcfm_load_scripts', array( &$this, 'load_scripts' ), 30 );
		
		// Enquiry Load Styles
		add_action( 'wcfm_load_styles', array( &$this, 'load_styles' ), 30 );
		add_action( 'after_wcfm_load_styles', array( &$this, 'load_styles' ), 30 );
		
		// Enquiry Load views
		add_action( 'wcfm_load_views', array( &$this, 'load_views' ), 30 );
		add_action( 'before_wcfm_load_views', array( &$this, 'load_views' ), 30 );
		
		// Enquiry Ajax Controllers
		add_action( 'after_wcfm_ajax_controller', array( &$this, 'ajax_controller' ) );
		add_action( 'wp_ajax_nopriv_wcfm_ajax_controller', array( &$this, 'ajax_controller' ) );
		
		// Delete Enquiry
		add_action( 'wp_ajax_delete_wcfm_enquiry', array( &$this, 'delete_wcfm_enquiry' ) );
		
		// Enquiry tab on Single Product
		if( apply_filters( 'wcfm_is_pref_enquiry_tab', true ) ) {
			add_filter( 'woocommerce_product_tabs', array( &$this, 'wcfm_enquiry_product_tab' ) );
		}
		
		// Enquiry list in WCFM Dashboard
		add_action( 'after_wcfm_dashboard_zone_analytics', array( $this, 'wcfm_dashboard_enquiry_list' ) );
		
		// Enquiry direct message type
		add_filter( 'wcfm_message_types', array( &$this, 'wcfm_enquiry_message_types' ) );
		
		//enqueue scripts
		add_action('wp_enqueue_scripts', array(&$this, 'wcfm_enquiry_scripts'));
		//enqueue styles
		add_action('wp_enqueue_scripts', array(&$this, 'wcfm_enquiry_styles'));
	}
	
	/**
   * Enquiry Query Var
   */
  function wcfm_enquiry_query_vars( $query_vars ) {
  	$wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );
  	
		$query_enquiry_vars = array(
			'wcfm-enquiry'                 => ! empty( $wcfm_modified_endpoints['wcfm-enquiry'] ) ? $wcfm_modified_endpoints['wcfm-enquiry'] : 'wcfm-enquiry',
			'wcfm-enquiry-manage'          => ! empty( $wcfm_modified_endpoints['wcfm-enquiry-manage'] ) ? $wcfm_modified_endpoints['wcfm-enquiry-manage'] : 'wcfm-enquiry-manage'
		);
		
		$query_vars = array_merge( $query_vars, $query_enquiry_vars );
		
		return $query_vars;
  }
  
  /**
   * Enquiry End Point Title
   */
  function wcfm_enquiry_endpoint_title( $title, $endpoint ) {
  	global $wp;
  	switch ( $endpoint ) {
  		case 'wcfm-enquiry' :
				$title = __( 'Enquiry Dashboard', 'wc-frontend-manager' );
			break;
			case 'wcfm-enquiry-manage' :
				$title = __( 'Enquiry Manager', 'wc-frontend-manager' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * Enquiry Endpoint Intialize
   */
  function wcfm_enquiry_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		if( !get_option( 'wcfm_updated_end_point_Enquiry' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_Enquiry', 1 );
		}
  }
  
  /**
	 * Enquiry Endpoiint Edit
	 */
	function enquiry_wcfm_endpoints_slug( $endpoints ) {
		
		$enquiry_endpoints = array(
													'wcfm-enquiry'          => 'wcfm-enquiry',
													'wcfm-enquiry-manage'   => 'wcfm-enquiry-manage',
													);
		
		$endpoints = array_merge( $endpoints, $enquiry_endpoints );
		
		return $endpoints;
	}
  
  /**
   * Enquiry Scripts
   */
  public function load_scripts( $end_point ) {
	  global $WCFM;
    
	  switch( $end_point ) {
	  	case 'wcfm-enquiry':
      	$WCFM->library->load_datatable_lib();
      	$WCFM->library->load_select2_lib();
      	wp_enqueue_script( 'wcfm_enquiry_js', $WCFM->library->js_lib_url . 'enquiry/wcfm-script-enquiry.js', array('jquery'), $WCFM->version, true );
      	
      	$wcfm_screen_manager_data = array();
    		if( !$WCFM->is_marketplace || wcfm_is_vendor() ) {
	    		$wcfm_screen_manager_data[3] = 'yes';
	    	}
	    	$wcfm_screen_manager_data = apply_filters( 'wcfm_enquiry_screen_manage', $wcfm_screen_manager_data );
	    	wp_localize_script( 'wcfm_enquiry_js', 'wcfm_enquiry_screen_manage', $wcfm_screen_manager_data );
      break;
      
      case 'wcfm-enquiry-manage':
      	$WCFM->library->load_tinymce_lib();
      	wp_enqueue_script( 'wcfm_enquiry_manage_js', $WCFM->library->js_lib_url . 'enquiry/wcfm-script-enquiry-manage.js', array('jquery'), $WCFM->version, true );
      	// Localized Script
        $wcfm_messages = get_wcfm_enquiry_manage_messages();
			  wp_localize_script( 'wcfm_enquiry_manage_js', 'wcfm_enquiry_manage_messages', $wcfm_messages );
      break;
	  }
	}
	
	/**
   * Enquiry Styles
   */
	public function load_styles( $end_point ) {
	  global $WCFM, $WCFMu;
		
	  switch( $end_point ) {
	  	case 'wcfm-enquiry':
		    wp_enqueue_style( 'wcfm_enquiry_css',  $WCFM->library->css_lib_url . 'enquiry/wcfm-style-enquiry.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-enquiry-manage':
		  	wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		  	wp_enqueue_style( 'wcfm_enquiry_manage_css',  $WCFM->library->css_lib_url . 'enquiry/wcfm-style-enquiry-manage.css', array(), $WCFM->version );
		  break;
	  }
	}
	
	/**
   * Enquiry Views
   */
  public function load_views( $end_point ) {
	  global $WCFM, $WCFMu;
	  
	  switch( $end_point ) {
	  	case 'wcfm-enquiry':
        require_once( $WCFM->library->views_path . 'enquiry/wcfm-view-enquiry.php' );
      break;
      
      case 'wcfm-enquiry-manage':
        require_once( $WCFM->library->views_path . 'enquiry/wcfm-view-enquiry-manage.php' );
      break;
	  }
	}
	
	/**
   * Enquiry Ajax Controllers
   */
  public function ajax_controller() {
  	global $WCFM, $WCFMu;
  	
  	$controllers_path = $WCFM->plugin_path . 'controllers/enquiry/';
  	
  	$controller = '';
  	if( isset( $_POST['controller'] ) ) {
  		$controller = $_POST['controller'];
  		
  		switch( $controller ) {
  			case 'wcfm-enquiry':
					require_once( $controllers_path . 'wcfm-controller-enquiry.php' );
					new WCFM_Enquiry_Controller();
				break;
				
				case 'wcfm-enquiry-manage':
					require_once( $controllers_path . 'wcfm-controller-enquiry-manage.php' );
					new WCFM_Enquiry_Manage_Controller();
				break;
				
				case 'wcfm-enquiry-tab':
					require_once( $controllers_path . 'wcfm-controller-enquiry-tab.php' );
					new WCFM_Enquiry_Tab_Controller();
				break;
  		}
  	}
  }
  
  /**
   * Delete Enquiry 
   */
  function delete_wcfm_enquiry() {
  	global $WCFM, $wpdb, $_POST;
  	
  	if( isset( $_POST['enquiryid'] ) && !empty( $_POST['enquiryid'] ) ) {
  		$enquiryid = $_POST['enquiryid'];
  		$wpdb->query( "DELETE FROM {$wpdb->prefix}wcfm_enquiries WHERE ID = {$enquiryid}" );
  	}
  	
  	echo "success";
  	die;
  }
	
  /**
   * Enquiry Tab on Single Product
   */
	function wcfm_enquiry_product_tab( $tabs ) {
		global $WCFM, $wp;
		
		$tabs['wcfm_enquiry_tab'] = array(
			'title' 	=> __( 'Enquiries', 'wc-frontend-manager' ),
			'priority' 	=> 10,
			'callback' 	=> array( &$this, 'wcfm_enquiry_product_tab_content' )
		);
	
		return $tabs;
	}
	
	/**
   * Enquiry List on WCFM Dashboard
   *
   * @since 3.3.5
   */
	function wcfm_dashboard_enquiry_list() {
		global $WCFM, $wpdb;
		
		if( apply_filters( 'wcfm_is_pref_enquiry', true ) && apply_filters( 'wcfm_is_allow_enquiry', true ) ) {
			$vendor_id = apply_filters( 'wcfm_message_author', get_current_user_id() );
			
			$enquiry_query = "SELECT * FROM {$wpdb->prefix}wcfm_enquiries AS wcfm_enquiries";
			$enquiry_query .= " WHERE 1 = 1";
			$enquiry_query .= " AND `reply` = ''";
			if( wcfm_is_vendor() ) { 
				$enquiry_query .= " AND `vendor_id` = {$vendor_id}";
			}
			$enquiry_query = apply_filters( 'wcfm_enquery_list_query', $enquiry_query );
			$enquiry_query .= " ORDER BY wcfm_enquiries.`ID` DESC";
			$enquiry_query .= " LIMIT 8";
			$enquiry_query .= " OFFSET 0";
			
			$wcfm_enquirys_array = $wpdb->get_results( $enquiry_query );
			
			?>
			<div class="wcfm_dashboard_enquiries">
				<div class="page_collapsible" id="wcfm_dashboard_enquiries"><span class="fa fa-question-circle-o"></span><span class="dashboard_widget_head"><?php _e('Enquiries', 'wc-frontend-manager'); ?></span></div>
				<div class="wcfm-container">
					<div id="wcfm_dashboard_enquiries_expander" class="wcfm-content">
					  <?php
					  if( !empty( $wcfm_enquirys_array ) ) {
					  	$counter = 0;
							foreach($wcfm_enquirys_array as $wcfm_enquirys_single) {
								if( $counter == 6 ) break;
								echo '<div class="wcfm_dashboard_enquiry"><a href="' . get_wcfm_enquiry_manage_url($wcfm_enquirys_single->ID) . '" class="wcfm_dashboard_item_title"><span class="fa fa-question-circle-o"></span>' . substr( $wcfm_enquirys_single->enquiry, 0, 80 ) . ' ...</a></div>';
								$counter++;
							}
							if( count( $wcfm_enquirys_array ) > 6 ) {
								echo '<div class="wcfm_dashboard_enquiry_show_all"><a class="wcfm_submit_button" href="' . get_wcfm_enquiry_url() . '">' . __( 'Show All', 'wc-frontend-manager' ) . ' >></a></div>';
							}
						} else {
							_e( 'There is no enquiry yet!!', 'wc-frontend-manager' );
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}
	}
	
	/**
   * Enquiry Tab content on Single Product
   */
	function wcfm_enquiry_product_tab_content() {
		global $WCFM, $wp;
		require_once( $WCFM->library->views_path . 'enquiry/wcfm-view-enquiry-tab.php' );
	}
	
	function wcfm_enquiry_message_types( $message_types ) {
		$message_types['enquiry'] = __( 'Enquiry', 'wc-frontend-manager' );
		return $message_types;
	}
	
	/**
	 * WCFM Enquiry JS
	 */
	function wcfm_enquiry_scripts() {
 		global $WCFM, $wp, $WCFM_Query;
 		
 		if( is_product() ) {
 			wp_enqueue_script( 'wcfm_enquiry_js', $WCFM->library->js_lib_url . 'enquiry/wcfm-script-enquiry-tab.js', array('jquery' ), $WCFM->version, true );
 			// Localized Script
			$wcfm_messages = get_wcfm_enquiry_manage_messages();
			wp_localize_script( 'wcfm_enquiry_js', 'wcfm_enquiry_manage_messages', $wcfm_messages );
 		}
 	}
 	
 	/**
 	 * WCFM Enquiry CSS
 	 */
 	function wcfm_enquiry_styles() {
 		global $WCFM, $wp, $WCFM_Query;
 		
 		if( is_product() ) {
 			wp_enqueue_style( 'wcfm_enquiry_css',  $WCFM->library->css_lib_url . 'enquiry/wcfm-style-enquiry-tab.css', array(), $WCFM->version );
 		}
 	}
}