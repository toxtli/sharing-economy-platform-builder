<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Coupons Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.0.0
 */

class WCFM_Coupons_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $wcfm_coupon_manager_form_data;
		
		$wcfm_coupon_manager_form_data = array();
	  parse_str($_POST['wcfm_coupons_manage_form'], $wcfm_coupon_manager_form_data);
	  
	  $wcfm_coupon_messages = get_wcfm_coupons_manage_messages();
	  $has_error = false;
	  
	  if(isset($wcfm_coupon_manager_form_data['title']) && !empty($wcfm_coupon_manager_form_data['title'])) {
	  	$is_update = false;
	  	$is_publish = false;
	  	$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
	  	
	  	if(isset($wcfm_coupon_manager_form_data['status']) && ($wcfm_coupon_manager_form_data['status'] == 'draft')) {
	  		$coupon_status = 'draft';
	  	} else {
	  		if( current_user_can( 'publish_shop_coupons' ) )
	  			$coupon_status = 'publish';
	  		else
	  		  $coupon_status = 'pending';
			}
	  	
	  	// Creating new coupon
			$new_coupon = array(
				'post_title'   => wc_clean( $wcfm_coupon_manager_form_data['title'] ),
				'post_status'  => $coupon_status,
				'post_type'    => 'shop_coupon',
				'post_excerpt' => apply_filters( 'wcfm_editor_content_before_save', $wcfm_coupon_manager_form_data['description'] ),
				'post_author'  => $current_user_id
				//'post_name' => sanitize_title($wcfm_coupon_manager_form_data['title'])
			);
			
			if(isset($wcfm_coupon_manager_form_data['coupon_id']) && $wcfm_coupon_manager_form_data['coupon_id'] == 0) {
				if ($coupon_status != 'draft') {
					$is_publish = true;
				}
				$new_coupon_id = wp_insert_post( $new_coupon, true );
			} else { // For Update
				$is_update = true;
				$new_coupon['ID'] = $wcfm_coupon_manager_form_data['coupon_id'];
				if( !wcfm_is_marketplace() ) unset( $new_coupon['post_author'] );
				if( get_post_status( $new_coupon['ID'] ) != 'draft' ) {
					//unset( $new_coupon['post_status'] );
				} else if( (get_post_status( $new_coupon['ID'] ) == 'draft') && ($coupon_status != 'draft') ) {
					$is_publish = true;
				}
				$new_coupon_id = wp_update_post( $new_coupon, true );
			}
			
			if(!is_wp_error($new_coupon_id)) {
				// For Update
				if($is_update) $new_coupon_id = $wcfm_coupon_manager_form_data['coupon_id'];
				
				
				// Check for dupe coupons
				$coupon_code  = wc_format_coupon_code( wc_clean( $wcfm_coupon_manager_form_data['title'] ) );
				$id_from_code = wc_get_coupon_id_by_code( $coupon_code, $new_coupon_id );
		
				if ( $id_from_code ) {
					if(!$is_update) {
						echo '{"status": false, "message": "' . __( 'Coupon code already exists - customers will use the latest coupon with this code.', 'woocommerce' ) . '", "id": "' . $new_coupon_id . '"}';
						$has_error = true;
					}
				}
				
				$wc_coupon = new WC_Coupon( $new_coupon_id );
				$wc_coupon->set_props( apply_filters( 'wcfm_coupon_data_factory', array(
					'code'                        => wc_clean( $wcfm_coupon_manager_form_data['title'] ),
					'discount_type'               => wc_clean( $wcfm_coupon_manager_form_data['discount_type'] ),
					'amount'                      => wc_format_decimal( $wcfm_coupon_manager_form_data['coupon_amount'] ),
					'date_expires'                => wc_clean( $wcfm_coupon_manager_form_data['expiry_date'] ),
					'free_shipping'               => isset( $wcfm_coupon_manager_form_data['free_shipping'] ),
				), $new_coupon_id, $wcfm_coupon_manager_form_data ) );
				$wc_coupon->save();
				
				// Hook for additional processing
				do_action( 'wcfm_coupons_manage_from_process', $new_coupon_id, $wcfm_coupon_manager_form_data );
				
				if(!$has_error) {
					if( get_post_status( $new_coupon_id ) == 'draft' ) {
						if(!$has_error) echo '{"status": true, "message": "' . $wcfm_coupon_messages['coupon_saved'] . '", "id": "' . $new_coupon_id . '"}';
					} else {
						if(!$has_error) echo '{"status": true, "message": "' . $wcfm_coupon_messages['coupon_published'] . '", "redirect": "' . get_wcfm_coupons_url() . '"}';
					}
				}
				die;
			}
		} else {
			echo '{"status": false, "message": "' . $wcfm_coupon_messages['no_title'] . '"}';
		}
	}
}