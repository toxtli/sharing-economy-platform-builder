<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Vendors Setings Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.1.1
 */

class WCFM_Settings_WCVendors_Controller {
	
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
		
		// sanitize html editor content
		$wcfm_settings_form['shop_description'] = ! empty( $_POST['profile'] ) ? wp_kses_post( stripslashes( $_POST['profile'] ) ) : '';
		
		update_user_meta( $user_id, 'pv_shop_name', $wcfm_settings_form['shop_name'] );
		update_user_meta( $user_id, 'pv_paypal', $wcfm_settings_form['paypal'] );
		update_user_meta( $user_id, 'pv_seller_info', apply_filters( 'wcfm_editor_content_before_save', $wcfm_settings_form['seller_info'] ) );
		update_user_meta( $user_id, 'pv_shop_description', apply_filters( 'wcfm_editor_content_before_save', $wcfm_settings_form['shop_description'] ) );
		update_user_meta( $user_id, '_wcv_company_url', $wcfm_settings_form['_wcv_company_url'] );
		update_user_meta( $user_id, '_wcv_store_phone', $wcfm_settings_form['_wcv_store_phone'] );
		
		// Set Vendor Store Logo
		if(isset($wcfm_settings_form['wcfm_logo']) && !empty($wcfm_settings_form['wcfm_logo'])) {
			$wcfm_settings_form['wcfm_logo'] = $WCFM->wcfm_get_attachment_id($wcfm_settings_form['wcfm_logo']);
		} else {
			$wcfm_settings_form['wcfm_logo'] = '';
		}
		update_user_meta( $user_id, '_wcv_store_icon_id', $wcfm_settings_form['wcfm_logo'] );
		
		// MangoPay Support - 3.4.3 
		if( apply_filters( 'wcfm_is_allow_billing_mangopay_settings', true ) ) {
			if( WCFM_Dependencies::wcfm_wc_mangopay_plugin_active_check() ) {
				$wcfm_mangopay_setting_fields = array( 
																							'vendor_account_type'        => 'vendor_account_type',
																							'vendor_iban'                => 'vendor_iban',
																							'vendor_bic'                 => 'vendor_bic',
																							'vendor_gb_accountnumber'    => 'vendor_gb_accountnumber',
																							'vendor_gb_sortcode'         => 'vendor_gb_sortcode',
																							'vendor_us_accountnumber'    => 'vendor_us_accountnumber',
																							'vendor_us_aba'              => 'vendor_us_aba',
																							'vendor_us_datype'           => 'vendor_us_datype',
																							'vendor_ca_bankname'         => 'vendor_ca_bankname',
																							'vendor_ca_instnumber'       => 'vendor_ca_instnumber',
																							'vendor_ca_branchcode'       => 'vendor_ca_branchcode',
																							'vendor_ca_accountnumber'    => 'vendor_ca_accountnumber',
																							'vendor_ot_country'          => 'vendor_ot_country',
																							'vendor_ot_bic'              => 'vendor_ot_bic',
																							'vendor_ot_accountnumber'    => 'vendor_ot_accountnumber',
																							'vendor_account_name'        => 'vendor_account_name',
																							'vendor_account_address1'    => 'vendor_account_address1',
																							'vendor_account_address2'    => 'vendor_account_address2',
																							'vendor_account_city'        => 'vendor_account_city',
																							'vendor_account_postcode'    => 'vendor_account_postcode',
																							'vendor_account_country'     => 'vendor_account_country',
																							'vendor_account_region'      => 'vendor_account_region'
																						);
				foreach( $wcfm_mangopay_setting_fields as $wcfm_setting_store_key => $wcfm_setting_store_field ) {
					if( isset( $wcfm_settings_form[$wcfm_setting_store_field] ) ) {
						update_user_meta( $user_id, $wcfm_setting_store_key, $wcfm_settings_form[$wcfm_setting_store_field] );
					}
				}
			}
		}
		
		do_action( 'wcfm_wcvendors_settings_update', $user_id, $wcfm_settings_form );
		
		echo '{"status": true, "message": "' . __( 'Settings saved successfully', 'wc-frontend-manager' ) . '"}';
		 
		die;
	}
}