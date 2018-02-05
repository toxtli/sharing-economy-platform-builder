<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Profile Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.2.5
 */

class WCFM_Profile_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$user_id = get_current_user_id();
		
		$wcfm_profile_default_fields = array( 'first_name'          => 'first_name',
																					'last_name'           => 'last_name',
																					//'billing_email'       => 'email',
																					'billing_phone'       => 'phone',
																					'billing_first_name'  => 'bfirst_name',
																					'billing_last_name'   => 'blast_name',
																					'billing_address_1'   => 'baddr_1',
																					'billing_address_2'   => 'baddr_2',
																					'billing_country'     => 'bcountry',
																					'billing_city'        => 'bcity',
																					'billing_state'       => 'bstate',
																					'billing_postcode'    => 'bzip'
																			  );
		
		$wcfm_profile_shipping_fields = array( 
																					'shipping_first_name'  => 'sfirst_name',
																					'shipping_last_name'   => 'slast_name',
																					'shipping_address_1'   => 'saddr_1',
																					'shipping_address_2'   => 'saddr_2',
																					'shipping_country'     => 'scountry',
																					'shipping_city'        => 'scity',
																					'shipping_state'       => 'sstate',
																					'shipping_postcode'    => 'szip'
																			  );
		
		$wcfm_profile_form_data = array();
	  parse_str($_POST['wcfm_profile_form'], $wcfm_profile_form);
	  
	  // sanitize
		//$wcfm_profile_form = array_map( 'sanitize_text_field', $wcfm_profile_form );
		//$wcfm_profile_form = array_map( 'stripslashes', $wcfm_profile_form );
		
		$description = ! empty( $_POST['about'] ) ? stripslashes( html_entity_decode( $_POST['about'], ENT_QUOTES, 'UTF-8' ) ) : '';
		update_user_meta( $user_id, 'description', apply_filters( 'wcfm_editor_content_before_save', $description ) );
		
		//Locale
		if( isset( $wcfm_profile_form['locale'] ) && !empty( $wcfm_profile_form['locale'] ) ) {
			if( $wcfm_profile_form['locale'] != 'site-default' ) {
				update_user_meta( $user_id, 'locale', $wcfm_profile_form['locale'] );
			} else {
				delete_user_meta( $user_id, 'locale' );
			}
		}
		
		// Set User Avatar
		if(isset($wcfm_profile_form['wp_user_avatar']) && !empty($wcfm_profile_form['wp_user_avatar'])) {
			$wp_user_avatar = $WCFM->wcfm_get_attachment_id($wcfm_profile_form['wp_user_avatar']);
			update_user_meta( $user_id, 'wp_user_avatar', $wp_user_avatar );
		} else {
			delete_user_meta( $user_id, 'wp_user_avatar' );
		}
		
		foreach( $wcfm_profile_default_fields as $wcfm_profile_default_key => $wcfm_profile_default_field ) {
			update_user_meta( $user_id, $wcfm_profile_default_key, $wcfm_profile_form[$wcfm_profile_default_field] );
		}
		
		foreach( $wcfm_profile_shipping_fields as $wcfm_profile_shipping_key => $wcfm_profile_shipping_field ) {
			update_user_meta( $user_id, $wcfm_profile_shipping_key, $wcfm_profile_form[$wcfm_profile_shipping_field] );
		}
		
		if( !wcfm_is_vendor() ) {
			$wcfm_profile_social_fields = array( 
																						'_twitter_profile'      => 'twitter',
																						'_fb_profile'           => 'facebook',
																						'_instagram'            => 'instagram',
																						'_youtube'              => 'youtube',
																						'_linkdin_profile'      => 'linkdin',
																						'_google_plus_profile'  => 'google_plus',
																						'_snapchat'             => 'snapchat',
																						'_pinterest'            => 'pinterest',
																						'googleplus'            => 'google_plus',
																						'twitter'               => 'twitter',
																						'facebook'              => 'facebook',
																						
																					);
			foreach( $wcfm_profile_social_fields as $wcfm_profile_social_key => $wcfm_profile_social_field ) {
				update_user_meta( $user_id, $wcfm_profile_social_key, $wcfm_profile_form[$wcfm_profile_social_field] );
			}
		}
		
		do_action( 'wcfm_profile_update', $user_id, $wcfm_profile_form );
		
		echo '{"status": true, "message": "' . __( 'Profile saved successfully', 'wc-frontend-manager' ) . '"}';
		 
		die;
	}
}