<?php
/**
 * WCFM plugin controllers
 *
 * Custom Field Plugin Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.3.7
 */

class WCFM_Custom_Field_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Third Party Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_customfield_products_manage_meta_save' ), 200, 2 );
	}
	
	/**
	 * Custom Field Product Meta data save
	 */
	function wcfm_customfield_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $WCFM;
		
		// Product Custom Fields
		$wcfm_product_custom_fields = (array) get_option( 'wcfm_product_custom_fields' );
		if( $wcfm_product_custom_fields && is_array( $wcfm_product_custom_fields ) && !empty( $wcfm_product_custom_fields ) ) {
			foreach( $wcfm_product_custom_fields as $wcfm_product_custom_field ) {
				$is_group = !empty( $wcfm_product_custom_field['group_name'] ) ? 'yes' : 'no';
				$is_group = !empty( $wcfm_product_custom_field['is_group'] ) ? 'yes' : 'no';
				if( $is_group == 'yes' ) {
					$group_name = $wcfm_product_custom_field['group_name'];
					if(isset($wcfm_products_manage_form_data[$group_name]) && !empty($wcfm_products_manage_form_data[$group_name])) {
						$group_value = $wcfm_products_manage_form_data[$group_name];
						$group_value = apply_filters( 'wcfm_custom_field_group_data_save', $group_value, $group_name );
						update_post_meta( $new_product_id, $group_name, $group_value );
					} else {
						update_post_meta( $new_product_id, $group_name, array() );
					}
				} else {
					$wcfm_product_custom_block_fields = $wcfm_product_custom_field['wcfm_product_custom_block_fields'];
					if( !empty( $wcfm_product_custom_block_fields ) ) {
						foreach( $wcfm_product_custom_block_fields as $wcfm_product_custom_block_field ) {
							$field_name = $wcfm_product_custom_block_field['name'];
							if(isset($wcfm_products_manage_form_data[$field_name]) && !empty($wcfm_products_manage_form_data[$field_name])) {
								update_post_meta( $new_product_id, $field_name, $wcfm_products_manage_form_data[$field_name] );
							} else {
								if( $wcfm_product_custom_block_field['type'] == 'checkbox' ) {
									if( isset($wcfm_products_manage_form_data[$field_name]) ) {
										update_post_meta( $new_product_id, $field_name, 'yes' );
									} else {
										update_post_meta( $new_product_id, $field_name, 'no' );
									}
								} else {
									update_post_meta( $new_product_id, $field_name, '' );
								}
							}
						}
					}
				}
			}
		}
	}
}