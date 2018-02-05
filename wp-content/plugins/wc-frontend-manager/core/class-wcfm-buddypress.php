<?php

/**
 * WCFM Buddypress Class
 *
 * @version		3.4.2
 * @package		wcfm/core
 * @author 		WC Lovers
 */
class WCFM_Buddypress {
	
	public function __construct() {
		global $WCFM;
		
		if( wcfm_is_allow_wcfm() ) {
			add_action( 'before_wcfm_dashboard', array( &$this, 'bp_wcfm_member_header' ) );
		}
	}
	
	function bp_wcfm_member_header() {
		global $WCFM;
		
		if( is_wcfm_page() ) {
			include_once( $WCFM->plugin_path . 'templates/buddypress/member-header.php' );
		}
	}
	
}