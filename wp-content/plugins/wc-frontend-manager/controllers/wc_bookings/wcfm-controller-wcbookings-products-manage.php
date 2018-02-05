<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Booking Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.1.0
 */

class WCFM_WCBookings_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Booking Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcb_wcfm_products_manage_meta_save' ), 20, 2 );
	}
	
	/**
	 * WC Booking Product Meta data save
	 */
	function wcb_wcfm_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST;
		
		// Only set props if the product is a bookable product.
		$product_type = empty( $wcfm_products_manage_form_data['product_type'] ) ? WC_Product_Factory::get_product_type( $new_product_id ) : sanitize_title( stripslashes( $wcfm_products_manage_form_data['product_type'] ) );
		if ( 'booking' != $product_type ) {
			return;
		}
		
		$classname    = WC_Product_Factory::get_product_classname( $new_product_id, 'booking' );
		$product      = new $classname( $new_product_id );
		
		$errors = $product->set_props( apply_filters( 'wcfm_booking_data_factory', array(
			'apply_adjacent_buffer'      => isset( $wcfm_products_manage_form_data['_wc_booking_apply_adjacent_buffer'] ),
			'base_cost'                  => wc_clean( $wcfm_products_manage_form_data['_wc_booking_base_cost'] ),
			'buffer_period'              => wc_clean( $wcfm_products_manage_form_data['_wc_booking_buffer_period'] ),
			'calendar_display_mode'      => wc_clean( $wcfm_products_manage_form_data['_wc_booking_calendar_display_mode'] ),
			'cancel_limit_unit'          => wc_clean( $wcfm_products_manage_form_data['_wc_booking_cancel_limit_unit'] ),
			'cancel_limit'               => wc_clean( $wcfm_products_manage_form_data['_wc_booking_cancel_limit'] ),
			'cost'                       => wc_clean( $wcfm_products_manage_form_data['_wc_booking_cost'] ),
			'default_date_availability'  => wc_clean( $wcfm_products_manage_form_data['_wc_booking_default_date_availability'] ),
			'display_cost'               => wc_clean( $wcfm_products_manage_form_data['_wc_display_cost'] ),
			'duration_type'              => wc_clean( $wcfm_products_manage_form_data['_wc_booking_duration_type'] ),
			'duration_unit'              => wc_clean( $wcfm_products_manage_form_data['_wc_booking_duration_unit'] ),
			'duration'                   => wc_clean( $wcfm_products_manage_form_data['_wc_booking_duration'] ),
			'enable_range_picker'        => isset( $wcfm_products_manage_form_data['_wc_booking_enable_range_picker'] ),
			'max_date_unit'              => wc_clean( $wcfm_products_manage_form_data['_wc_booking_max_date_unit'] ),
			'max_date_value'             => wc_clean( $wcfm_products_manage_form_data['_wc_booking_max_date'] ),
			'max_duration'               => wc_clean( $wcfm_products_manage_form_data['_wc_booking_max_duration'] ),
			'min_date_unit'              => wc_clean( $wcfm_products_manage_form_data['_wc_booking_min_date_unit'] ),
			'min_date_value'             => wc_clean( $wcfm_products_manage_form_data['_wc_booking_min_date'] ),
			'min_duration'               => wc_clean( $wcfm_products_manage_form_data['_wc_booking_min_duration'] ),
			'qty'                        => wc_clean( $wcfm_products_manage_form_data['_wc_booking_qty'] ),
			'requires_confirmation'      => isset( $wcfm_products_manage_form_data['_wc_booking_requires_confirmation'] ),
			'user_can_cancel'            => isset( $wcfm_products_manage_form_data['_wc_booking_user_can_cancel'] ),
		), $new_product_id, $product, $wcfm_products_manage_form_data ) );
		
		if ( is_wp_error( $errors ) ) {
			//echo '{"status": false, "message": "' . $errors->get_error_message() . '", "id": "' . $new_product_id . '", "redirect": "' . get_permalink( $new_product_id ) . '"}';
		}
		
		$product->save();
	}
	
}