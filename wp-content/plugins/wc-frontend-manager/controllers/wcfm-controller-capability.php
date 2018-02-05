<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Capabiity Setings Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.4.7
 */

class WCFM_Capability_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$wcfm_capability_form_data = array();
	  parse_str($_POST['wcfm_capability_form'], $wcfm_capability_form);
	  
	  // Save WCFM Vendor capability option
		if( isset( $wcfm_capability_form['wcfm_capability_options'] ) ) {
			update_option( 'wcfm_capability_options', $wcfm_capability_form['wcfm_capability_options'] );
		} else {
			update_option( 'wcfm_capability_options', array() ); 
		}
	  
		if( wcfm_is_marketplace() ) {
			$WCFM->wcfm_vendor_support->vendors_capability_option_updates();
		}
		
		do_action( 'wcfm_capability_update', $wcfm_capability_form );
		
		echo '{"status": true, "message": "' . __( 'Capability saved successfully', 'wc-frontend-manager' ) . '"}';
		
		die;
	}
}