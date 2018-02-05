<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Product Vendors Setings Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.1.1
 */

class WCFM_Settings_WCPVendors_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$wcfm_settings_form_data = array();
	  parse_str($_POST['wcfm_settings_form'], $wcfm_settings_form_data);
	  
	  $vendor_data = WC_Product_Vendors_Utils::get_vendor_data_from_user();
	  
	  // sanitize
		$wcfm_settings_form = array_map( 'sanitize_text_field', $wcfm_settings_form_data );
		$wcfm_settings_form = array_map( 'stripslashes', $wcfm_settings_form );
		
		// Change Shop Name
		if( isset($wcfm_settings_form['shop_name']) && !empty($wcfm_settings_form['shop_name']) ) {
			wp_update_term( WC_Product_Vendors_Utils::get_logged_in_vendor(), WC_PRODUCT_VENDORS_TAXONOMY, array( 'name' => $wcfm_settings_form['shop_name'] ) );
		}
		
		// sanitize html editor content
		$wcfm_settings_form['profile'] = ! empty( $_POST['profile'] ) ? apply_filters( 'wcfm_editor_content_before_save', wp_kses_post( stripslashes( $_POST['profile'] ) ) ) : '';
		
		// Set Product Featured Image
		if(isset($wcfm_settings_form['wcfm_logo']) && !empty($wcfm_settings_form['wcfm_logo'])) {
			$wcfm_settings_form['logo'] = $WCFM->wcfm_get_attachment_id($wcfm_settings_form['wcfm_logo']);
		} else {
			$wcfm_settings_form['logo'] = '';
		}
		
		if( !isset( $wcfm_settings_form['wcfm_vacation_mode'] ) ) $wcfm_settings_form['wcfm_vacation_mode'] = 'no';
		if( !isset( $wcfm_settings_form['wcfm_disable_vacation_purchase'] ) ) $wcfm_settings_form['wcfm_disable_vacation_purchase'] = 'no';
		
		// merge the changes with existing settings
		$wcfm_settings_form = array_merge( $vendor_data, $wcfm_settings_form );
		
		// Toolset Custom Field Support
		if( isset( $wcfm_settings_form_data['wpcf'] ) && ! empty( $wcfm_settings_form_data['wpcf'] ) ) {
			foreach( $wcfm_settings_form_data['wpcf'] as $toolset_types_filed_key => $toolset_types_filed_value ) {
				update_term_meta( WC_Product_Vendors_Utils::get_logged_in_vendor(), $toolset_types_filed_key, $toolset_types_filed_value );
			}
		}
		
		//do_action( 'wcfm_wcpvendors_settings_update', WC_Product_Vendors_Utils::get_logged_in_vendor(), $wcfm_settings_form );
		
		$updated = update_term_meta( WC_Product_Vendors_Utils::get_logged_in_vendor(), 'vendor_data', $wcfm_settings_form );
		
		if ( !is_wp_error( $updated ) ) {
			echo '{"status": true, "message": "' . __( 'Settings saved successfully', 'wc-frontend-manager' ) . '"}';
		} else {
			echo '{"status": false, "message": "' . __( 'Settings failed to save', 'wc-frontend-manager' ) . '"}';
		}
		 
		die;
	}
}