<?php
/**
 * WCFM plugin core
 *
 * WCFM Withdrawal core
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   3.3.0
 */
 
class WCFM_Withdrawal {

	public function __construct() {
		global $WCFM;
		
		  // WCFM WCMp Query Var Filter - 2.5.3
			add_filter( 'wcfm_query_vars', array( &$this, 'wcfm_withdrawal_query_vars' ), 10 );
			add_filter( 'wcfm_endpoint_title', array( &$this, 'wcfm_withdrawal_endpoint_title' ), 10, 2 );
			add_action( 'init', array( &$this, 'wcfm_withdrawal_init' ), 120 );
			
    	// WCFMu WCMp Load WCFMu Scripts
			add_action( 'wcfm_load_scripts', array( &$this, 'wcfm_withdrawal_load_scripts' ), 10 );
			add_action( 'after_wcfm_load_scripts', array( &$this, 'wcfm_withdrawal_load_scripts' ), 10 );
			
			// WCFMu WCMp Load WCFMu Styles
			add_action( 'wcfm_load_styles', array( &$this, 'wcfm_withdrawal_load_styles' ), 10 );
			add_action( 'after_wcfm_load_styles', array( &$this, 'wcfm_withdrawal_load_styles' ), 10 );
			
			// WCFMu WCMp Load WCFMu views
			add_action( 'wcfm_load_views', array( &$this, 'wcfm_withdrawal_load_views' ), 10 );
			add_action( 'before_wcfm_load_views', array( &$this, 'wcfm_withdrawal_load_views' ), 10 );
			
			// WCFMu Thirdparty Ajax Controller
			add_action( 'after_wcfm_ajax_controller', array( &$this, 'wcfm_withdrawal_ajax_controller' ) );
			
			add_filter( 'wcfm_menus', array( &$this, 'wcfm_withdrawal_menus' ), 30 );
		
	}
	
	/**
   * WCMp Query Var
   */
  function wcfm_withdrawal_query_vars( $query_vars ) {
  	$wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );
  	
		$query_wcmp_vars = array(
			'wcfm-payments'        => ! empty( $wcfm_modified_endpoints['wcfm-payments'] ) ? $wcfm_modified_endpoints['wcfm-payments'] : 'wcfm-payments',
			'wcfm-withdrawal'      => ! empty( $wcfm_modified_endpoints['wcfm-withdrawal'] ) ? $wcfm_modified_endpoints['wcfm-withdrawal'] : 'wcfm-withdrawal',
		);
		$query_vars = array_merge( $query_vars, $query_wcmp_vars );
		
		return $query_vars;
  }
  
  /**
   * WCMp End Point Title
   */
  function wcfm_withdrawal_endpoint_title( $title, $endpoint ) {
  	
  	switch ( $endpoint ) {
			case 'wcfm-payments' :
				$title = __( 'Payments History', 'wc-frontend-manager' );
			break;
			
			case 'wcfm-withdrawal' :
				$title = __( 'Withdrawal Request', 'wc-frontend-manager' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * WCMp Endpoint Intialize
   */
  function wcfm_withdrawal_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		//if( !get_option( 'wcfm_updated_end_point_payment' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_payment', 1 );
		//}
  }
  
	/**
   * WCFM wcmarketplace Menu
   */
  function wcfm_withdrawal_menus( $menus ) {
  	global $WCFM;
  		
		$menus = array_slice($menus, 0, 3, true) +
												array( 'wcfm-payments' => array( 'label'  => __( 'Payments', 'wc-frontend-manager' ),
																										 'url'        => wcfm_payments_url(),
																										 'icon'       => 'credit-card',
																										 'priority'   => 38
																										) )	 +
													array_slice($menus, 3, count($menus) - 3, true) ;
  	return $menus;
  }
  
	/**
   * WCMp Scripts
   */
  public function wcfm_withdrawal_load_scripts( $end_point ) {
	  global $WCFM;
    
	  switch( $end_point ) {
      case 'wcfm-payments':
      	$WCFM->library->load_datatable_lib();
      	$WCFM->library->load_datepicker_lib();
      	$WCFM->library->load_datatable_download_lib();
      	if( $WCFM->is_marketplace == 'wcmarketplace' ) {
      		wp_enqueue_script( 'wcfmu_wcmp_payments_js', $WCFM->library->js_lib_url . 'withdrawal/wcmp/wcfm-script-payments.js', array('jquery'), $WCFM->version, true );
      	} elseif( $WCFM->is_marketplace == 'dokan' ) {
      		wp_enqueue_script( 'wcfmu_dokan_payments_js', $WCFM->library->js_lib_url . 'withdrawal/dokan/wcfm-script-payments.js', array('jquery'), $WCFM->version, true );
      	}
      break;
      
      case 'wcfm-withdrawal':
      	$WCFM->library->load_datatable_lib();
      	$WCFM->library->load_datatable_download_lib();
      	if( $WCFM->is_marketplace == 'wcmarketplace' ) {
      		wp_enqueue_script( 'wcfmu_wcmp_withdrawal_js', $WCFM->library->js_lib_url . 'withdrawal/wcmp/wcfm-script-withdrawal.js', array('jquery'), $WCFM->version, true );
      	} elseif( $WCFM->is_marketplace == 'dokan' ) {
      		wp_enqueue_script( 'wcfmu_dokan_withdrawal_js', $WCFM->library->js_lib_url . 'withdrawal/dokan/wcfm-script-withdrawal.js', array('jquery'), $WCFM->version, true );
      	}
      break;
	  }
	}
	
	/**
   * WCMp Styles
   */
	public function wcfm_withdrawal_load_styles( $end_point ) {
	  global $WCFM;
		
	  switch( $end_point ) {
	  	case 'wcfm-payments':
	  		if( $WCFM->is_marketplace == 'wcmarketplace' ) {
	  			wp_enqueue_style( 'wcfm_wcmp_payments_css',  $WCFM->library->css_lib_url . 'withdrawal/wcmp/wcfm-style-payments.css', array(), $WCFM->version );
	  		} elseif( $WCFM->is_marketplace == 'dokan' ) {
	  			wp_enqueue_style( 'wcfm_wcmp_payments_css',  $WCFM->library->css_lib_url . 'withdrawal/dokan/wcfm-style-payments.css', array(), $WCFM->version );
	  		}
		  break;
		  
		  case 'wcfm-withdrawal':
		  	if( $WCFM->is_marketplace == 'wcmarketplace' ) {
		  		wp_enqueue_style( 'wcfm_wcmp_withdrawal_css',  $WCFM->library->css_lib_url . 'withdrawal/wcmp/wcfm-style-withdrawal.css', array(), $WCFM->version );
		  	} elseif( $WCFM->is_marketplace == 'dokan' ) {
		  		wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		  		wp_enqueue_style( 'wcfm_wcmp_withdrawal_css',  $WCFM->library->css_lib_url . 'withdrawal/dokan/wcfm-style-withdrawal.css', array(), $WCFM->version );
		  	}
		  break;
	  }
	}
	
	/**
   * WCMp Views
   */
  public function wcfm_withdrawal_load_views( $end_point ) {
	  global $WCFM;
	  
	  switch( $end_point ) {
      case 'wcfm-payments':
      	if( $WCFM->is_marketplace == 'wcmarketplace' ) {
      		require_once( $WCFM->library->views_path . 'withdrawal/wcmp/wcfm-view-payments.php' );
      	} elseif( $WCFM->is_marketplace == 'dokan' ) {
      		require_once( $WCFM->library->views_path . 'withdrawal/dokan/wcfm-view-payments.php' );
      	}
      break;
      
      case 'wcfm-withdrawal':
      	if( $WCFM->is_marketplace == 'wcmarketplace' ) {
      		require_once( $WCFM->library->views_path . 'withdrawal/wcmp/wcfm-view-withdrawal.php' );
      	} elseif( $WCFM->is_marketplace == 'dokan' ) {
      		require_once( $WCFM->library->views_path . 'withdrawal/dokan/wcfm-view-withdrawal.php' );
      	}
      break;
	  }
	}
	
	/**
   * WCMp Ajax Controllers
   */
  public function wcfm_withdrawal_ajax_controller() {
  	global $WCFM;
  	
  	$controllers_path = $WCFM->plugin_path . 'controllers/withdrawal/';
  	
  	$controller = '';
  	if( isset( $_POST['controller'] ) ) {
  		$controller = $_POST['controller'];
  		switch( $controller ) {
  			case 'wcfm-payments':
  				if( $WCFM->is_marketplace == 'wcmarketplace' ) {
						require_once( $controllers_path . 'wcmp/wcfm-controller-payments.php' );
						new WCFM_Payments_Controller();
					} elseif( $WCFM->is_marketplace == 'dokan' ) {
						require_once( $controllers_path . 'dokan/wcfm-controller-payments.php' );
						new WCFM_Payments_Controller();
					}
  			break;
  			
  			case 'wcfm-withdrawal':
  				if( $WCFM->is_marketplace == 'wcmarketplace' ) {
						require_once( $controllers_path . 'wcmp/wcfm-controller-withdrawal.php' );
						new WCFM_Withdrawal_Controller();
					}
  			break;
  			
  			case 'wcfm-withdrawal-request':
  				if( $WCFM->is_marketplace == 'wcmarketplace' ) {
						require_once( $controllers_path . 'wcmp/wcfm-controller-withdrawal-request.php' );
						new WCFM_Withdrawal_Request_Controller();
					} elseif( $WCFM->is_marketplace == 'dokan' ) {
						require_once( $controllers_path . 'dokan/wcfm-controller-withdrawal-request.php' );
						new WCFM_Withdrawal_Request_Controller();
					}
  			break;
  		}
  	}
  }
	
	
}