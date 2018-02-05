<?php
/**
 * WCFM plugin core
 *
 * Plugin WCFM Preferences Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   3.2.8
 */
 
class WCFM_Preferences {
	
	private $wcfm_module_options = array();
	private $wcfm_options = array();

	public function __construct() {
		global $WCFM;
		
		$this->wcfm_options = (array) get_option( 'wcfm_options' );
		$this->wcfm_module_options = isset( $this->wcfm_options['module_options'] ) ? $this->wcfm_options['module_options'] : array();
		$this->wcfm_module_options = apply_filters( 'wcfm_module_options', $this->wcfm_module_options );
		
		add_filter( 'wcfm_is_pref_hover_submenu', array( &$this, 'wcfmpref_hover_submenu' ), 750 );
		add_filter( 'wcfm_is_pref_dashboard_logo', array( &$this, 'wcfmpref_dashboard_logo' ), 750 );
		add_filter( 'wcfm_is_pref_welcome_box', array( &$this, 'wcfmpref_welcome_box' ), 750 );
		
		add_filter( 'wcfm_is_pref_notice', array( &$this, 'wcfmpref_notice' ), 750 );
		add_filter( 'wcfm_is_pref_direct_message', array( &$this, 'wcfmpref_direct_message' ), 750 );
		add_filter( 'wcfm_is_pref_enquiry', array( &$this, 'wcfmpref_enquiry' ), 750 );
		add_filter( 'wcfm_is_pref_enquiry_tab', array( &$this, 'wcfmpref_enquiry_tab' ), 750 );
		add_filter( 'wcfm_is_pref_catalog', array( &$this, 'wcfmpref_catalog' ), 750 );
		add_filter( 'wcfm_is_pref_knowledgebase', array( &$this, 'wcfmpref_knowledgebase' ), 750 );
		add_filter( 'wcfm_is_pref_profile', array( &$this, 'wcfmpref_profile' ), 750 );
		add_filter( 'wcfm_is_pref_withdrawal', array( &$this, 'wcfmpref_withdrawal' ), 750 );
		
		add_filter( 'wcfm_is_pref_buddypress', array( &$this, 'wcfmpref_buddypress' ), 750 );
		
		add_filter( 'wcfm_is_pref_custom_field', array( &$this, 'wcfmpref_custom_field' ), 750 );
		
	}
	
	// Hover Sub menu
  function wcfmpref_hover_submenu( $is_pref ) {
  	$submenu = ( isset( $this->wcfm_module_options['submenu'] ) ) ? $this->wcfm_module_options['submenu'] : 'no';
  	if( $submenu == 'yes' ) $is_pref = false;
  	return $is_pref;
  }
  
  // Dashboard Loago
  function wcfmpref_dashboard_logo( $is_pref ) {
  	$submenu = ( isset( $this->wcfm_options['dashboard_logo_disabled'] ) ) ? $this->wcfm_options['dashboard_logo_disabled'] : 'no';
  	if( $submenu == 'yes' ) $is_pref = false;
  	return $is_pref;
  }
  
  // Welcome Box
  function wcfmpref_welcome_box( $is_pref ) {
  	$submenu = ( isset( $this->wcfm_options['welcome_box_disabled'] ) ) ? $this->wcfm_options['welcome_box_disabled'] : 'no';
  	if( $submenu == 'yes' ) $is_pref = false;
  	return $is_pref;
  }
  
  // Notice
  function wcfmpref_notice( $is_pref ) {
  	$notice = ( isset( $this->wcfm_module_options['notice'] ) ) ? $this->wcfm_module_options['notice'] : 'no';
  	if( $notice == 'yes' ) $is_pref = false;
  	return $is_pref;
  }
  
  // Direct Message
  function wcfmpref_direct_message( $is_pref ) {
  	$direct_message = ( isset( $this->wcfm_module_options['direct_message'] ) ) ? $this->wcfm_module_options['direct_message'] : 'no';
  	if( $direct_message == 'yes' ) $is_pref = false;
  	return $is_pref;
  }
  
  // Enquiry
  function wcfmpref_enquiry( $is_pref ) {
  	$enquiry = ( isset( $this->wcfm_module_options['enquiry'] ) ) ? $this->wcfm_module_options['enquiry'] : 'no';
  	if( $enquiry == 'yes' ) $is_pref = false;
  	return $is_pref;
  }
  
  // Enquiry Tab
  function wcfmpref_enquiry_tab( $is_pref ) {
  	$enquiry_tab = ( isset( $this->wcfm_module_options['enquiry_tab'] ) ) ? $this->wcfm_module_options['enquiry_tab'] : 'no';
  	if( $enquiry_tab == 'yes' ) $is_pref = false;
  	return $is_pref;
  }
  
  // Catalog
  function wcfmpref_catalog( $is_pref ) {
  	$catalog = ( isset( $this->wcfm_module_options['catalog'] ) ) ? $this->wcfm_module_options['catalog'] : 'no';
  	if( $catalog == 'yes' ) $is_pref = false;
  	return $is_pref;
  }
  
  // Knowledgebase
  function wcfmpref_knowledgebase( $is_pref ) {
  	$knowledgebase = ( isset( $this->wcfm_module_options['knowledgebase'] ) ) ? $this->wcfm_module_options['knowledgebase'] : 'no';
  	if( $knowledgebase == 'yes' ) $is_pref = false;
  	return $is_pref;
  }
  
  // Profile
  function wcfmpref_profile( $is_pref ) {
  	$profile = ( isset( $this->wcfm_module_options['profile'] ) ) ? $this->wcfm_module_options['profile'] : 'no';
  	if( $profile == 'yes' ) $is_pref = false;
  	return $is_pref;
  }
  
  // Withdrawal
  function wcfmpref_withdrawal( $is_pref ) {
  	$withdrawal = ( isset( $this->wcfm_module_options['withdrawal'] ) ) ? $this->wcfm_module_options['withdrawal'] : 'no';
  	if( $withdrawal == 'yes' ) $is_pref = false;
  	return $is_pref;
  }
  
  // BuddyPress
  function wcfmpref_buddypress( $is_pref ) {
  	$buddypress = ( isset( $this->wcfm_module_options['buddypress'] ) ) ? $this->wcfm_module_options['buddypress'] : 'no';
  	if( $buddypress == 'yes' ) $is_pref = false;
  	return $is_pref;
  }
  
  // Custom Field
  function wcfmpref_custom_field( $is_pref ) {
  	$custom_field = ( isset( $this->wcfm_module_options['custom_field'] ) ) ? $this->wcfm_module_options['custom_field'] : 'no';
  	if( $custom_field == 'yes' ) $is_pref = false;
  	return $is_pref;
  }
  
}