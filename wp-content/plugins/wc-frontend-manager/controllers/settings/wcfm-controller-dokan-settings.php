<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Dokan Setings Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   3.3.0
 */

class WCFM_Settings_Dokan_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		
		$wcfm_settings_form_data = array();
	  parse_str($_POST['wcfm_settings_form'], $wcfm_settings_form);
	  
	  // sanitize
		//$wcfm_settings_form = array_map( 'sanitize_text_field', $wcfm_settings_form );
		//$wcfm_settings_form = array_map( 'stripslashes', $wcfm_settings_form );
		
		// Set Gravatar
		if(isset($wcfm_settings_form['gravatar']) && !empty($wcfm_settings_form['gravatar'])) {
			$wcfm_settings_form['gravatar'] = $WCFM->wcfm_get_attachment_id($wcfm_settings_form['gravatar']);
		} else {
			$wcfm_settings_form['gravatar'] = '';
		}
		
		// Set Banner
		if(isset($wcfm_settings_form['banner']) && !empty($wcfm_settings_form['banner'])) {
			$wcfm_settings_form['banner'] = $WCFM->wcfm_get_attachment_id($wcfm_settings_form['banner']);
		} else {
			$wcfm_settings_form['banner'] = '';
		}
		
		if ( dokan_get_option( 'new_seller_enable_selling', 'dokan_selling', 'on' ) == 'off' ) {
				update_user_meta( $user_id, 'dokan_enable_selling', 'no' );
		} else {
				update_user_meta( $user_id, 'dokan_enable_selling', 'yes' );
		}
		
		// Vacation Settings
		if( !isset( $wcfm_settings_form['wcfm_vacation_mode'] ) ) $wcfm_settings_form['wcfm_vacation_mode'] = 'no';
		if( !isset( $wcfm_settings_form['wcfm_disable_vacation_purchase'] ) ) $wcfm_settings_form['wcfm_disable_vacation_purchase'] = 'no';
		
		update_user_meta( $user_id, 'dokan_profile_settings', $wcfm_settings_form );
		
		do_action( 'wcfm_dokan_settings_update', $user_id, $wcfm_settings_form );
		
		echo '{"status": true, "message": "' . __( 'Settings saved successfully', 'wc-frontend-manager' ) . '"}';
		 
		die;
	}
}