<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Marketplace Setings Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.2.5
 */

class WCFM_Settings_WCMarketplace_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST, $WCMp;
		
		$user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		
		$wcfm_settings_form_data = array();
	  parse_str($_POST['wcfm_settings_form'], $wcfm_settings_form);
	  
	  // sanitize
		//$wcfm_settings_form = array_map( 'sanitize_text_field', $wcfm_settings_form );
		//$wcfm_settings_form = array_map( 'stripslashes', $wcfm_settings_form );
		
		// sanitize html editor content
		$wcfm_settings_form['shop_description'] = ! empty( $_POST['profile'] ) ? stripslashes( html_entity_decode( $_POST['profile'], ENT_QUOTES, 'UTF-8' ) ) : '';
		update_user_meta( $user_id, '_vendor_description', apply_filters( 'wcfm_editor_content_before_save', $wcfm_settings_form['shop_description'] ) );
		
		$wcfm_setting_store_fields = array( 
																					'_vendor_page_title'          => 'shop_name',
																					'_vendor_page_slug'           => 'shop_slug',
																					'_vendor_image'               => 'wcfm_logo',
																					'_vendor_policy_tab_title'    => 'vendor_policy_tab_title',
																					'_vendor_shipping_policy'     => 'vendor_shipping_policy',
																					'_vendor_refund_policy'       => 'vendor_refund_policy',
																					'_vendor_cancellation_policy' => 'vendor_cancellation_policy',
																					'_vendor_customer_phone'      => 'vendor_customer_phone',
																					'_vendor_customer_email'      => 'vendor_customer_email',
																					'_vendor_csd_return_address1' => 'vendor_csd_return_address1',
																					'_vendor_csd_return_address2' => 'vendor_csd_return_address2',
																					'_vendor_csd_return_country'  => 'vendor_csd_return_country',
																					'_vendor_csd_return_state'    => 'vendor_csd_return_state',
																					'_vendor_csd_return_city'     => 'vendor_csd_return_city',
																					'_vendor_csd_return_zip'      => 'vendor_csd_return_zip'
																			  );
		foreach( $wcfm_setting_store_fields as $wcfm_setting_store_key => $wcfm_setting_store_field ) {
			if( isset( $wcfm_settings_form[$wcfm_setting_store_field] ) ) {
				update_user_meta( $user_id, $wcfm_setting_store_key, $wcfm_settings_form[$wcfm_setting_store_field] );
			}
		}
		
		// Update Page Title
		$vendor = new WCMp_Vendor( $user_id );
		if( $vendor ) {
			$vendor->update_page_title( wc_clean( $wcfm_settings_form['shop_name'] ) );
			wp_update_user( array( 'ID' => $user_id, 'display_name' => wc_clean( $wcfm_settings_form['shop_name'] ) ) );
			if( isset( $wcfm_settings_form['shop_slug'] ) && !empty( $wcfm_settings_form['shop_slug'] ) ) {
				$vendor->update_page_slug( wc_clean( $wcfm_settings_form['shop_slug'] ) );
			}
		}
		
		// Store Adcanced settings
		$wcfm_settings_store_fields = array( 	'_vendor_phone'      => 'shop_phone',
																					//'_vendor_email'      => 'shop_email',
																					'_vendor_banner'     => 'banner',
																					'_vendor_address_1'  => 'addr_1',
																					'_vendor_address_2'  => 'addr_2',
																					'_vendor_country'    => 'country',
																					'_vendor_city'       => 'city',
																					'_vendor_state'      => 'state',
																					'_vendor_postcode'   => 'zip',
																					'_shop_template'     => 'shop_template'
																			  );
		
		foreach( $wcfm_settings_store_fields as $wcfm_settings_store_key => $wcfm_settings_store_field ) {
			update_user_meta( $user_id, $wcfm_settings_store_key, $wcfm_settings_form[$wcfm_settings_store_field] );
		}
  	
		// Billing Settings
  	$wcfm_setting_bank_fields = array( 	'_vendor_paypal_email'          => 'paypal_email',
																				'_vendor_bank_account_type'     => '_vendor_bank_account_type',
																				'_vendor_bank_account_number'   => '_vendor_bank_account_number',
																				'_vendor_bank_name'             => '_vendor_bank_name',
																				'_vendor_aba_routing_number'    => '_vendor_aba_routing_number',
																				'_vendor_bank_address'          => '_vendor_bank_address',
																				'_vendor_destination_currency'  => '_vendor_destination_currency',
																				'_vendor_iban'                  => '_vendor_iban',
																				'_vendor_account_holder_name'   => '_vendor_account_holder_name',
																				'_vendor_payment_mode'          => '_vendor_payment_mode'
																			);
		foreach( $wcfm_setting_bank_fields as $wcfm_setting_bank_key => $wcfm_setting_bank_field ) {
			if( isset( $wcfm_settings_form[$wcfm_setting_bank_field] ) ) {
				update_user_meta( $user_id, $wcfm_setting_bank_key, $wcfm_settings_form[$wcfm_setting_bank_field] );
			}
		}
		
		// Shipping Settings
		if( isset( $wcfm_settings_form['wcfm_vendor_shipping_data'] ) && !empty( $wcfm_settings_form['wcfm_vendor_shipping_data'] ) ) {
			$shipping_class_id = get_user_meta($user_id, 'shipping_class_id', true);
			$raw_zones = WC_Shipping_Zones::get_zones();
			$raw_zones[] = array('id' => 0);
			foreach ($raw_zones as $raw_zone) {
				$zone = new WC_Shipping_Zone($raw_zone['id']);
				$raw_methods = $zone->get_shipping_methods();
				foreach ($raw_methods as $raw_method) {
					if ($raw_method->id == 'flat_rate') {
						$option_name = "woocommerce_" . $raw_method->id . "_" . $raw_method->instance_id . "_settings";
						$shipping_details = get_option($option_name);
						$class = "class_cost_" . $shipping_class_id;
						$shipping_details[$class] = stripslashes($wcfm_settings_form['wcfm_vendor_shipping_data'][$option_name . '_' . $class]);
						update_option($option_name, $shipping_details);
					}
				}
			}
			$shipping_updt = update_user_meta( $user_id, 'vendor_shipping_data', $wcfm_settings_form['wcfm_vendor_shipping_data'] );
		}
		
		// Table Rate Shipping Settings
		if( isset( $wcfm_settings_form['wcfm_table_rate_shipping_rules'] ) && !empty( $wcfm_settings_form['wcfm_table_rate_shipping_rules'] ) ) {
			$table_rate_data_rules = $wcfm_settings_form['wcfm_table_rate_shipping_rules'];
			$shipping_class_id = absint( get_user_meta( $user_id, 'shipping_class_id', true) );
			$old_rate_ids = array();
      if (!empty($table_rate_data_rules) && is_array($table_rate_data_rules)) {
      	foreach ($table_rate_data_rules as $shipping_method_id => $table_rate_datas) {
				
					// Fetching Old Rates for this Shipping method ID
					$old_table_rates = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woocommerce_shipping_table_rates WHERE `rate_class` = {$shipping_class_id} AND `shipping_method_id` = {$shipping_method_id} order by 'shipping_method_id' ", OBJECT);
					if( !empty($old_table_rates) ) {
						foreach ( $old_table_rates as $old_table_rate ) {
							$old_rate_ids[$old_table_rate->rate_id] = $old_table_rate->rate_id;
						}
					}
				
					foreach ($table_rate_datas as $table_rate_data) {
						$rate_id = $table_rate_data['rate_id'];
						$rate_class = $shipping_class_id;
						$rate_condition = $table_rate_data['rate_condition'];
						$rate_min = isset($table_rate_data['rate_min']) ? $table_rate_data['rate_min'] : '';
						$rate_max = isset($table_rate_data['rate_max']) ? $table_rate_data['rate_max'] : '';
						$rate_cost = isset($table_rate_data['rate_cost']) ? rtrim(rtrim(number_format((double) $table_rate_data['rate_cost'], 4, '.', ''), '0'), '.') : '';
						$rate_cost_per_item = isset($table_rate_data['rate_cost_per_item']) ? rtrim(rtrim(number_format((double) $table_rate_data['rate_cost_per_item'], 4, '.', ''), '0'), '.') : '';
						$rate_cost_per_weight_unit = isset($table_rate_data['rate_cost_per_weight_unit']) ? rtrim(rtrim(number_format((double) $table_rate_data['rate_cost_per_weight_unit'], 4, '.', ''), '0'), '.') : '';
						$rate_cost_percent = isset($table_rate_data['rate_cost_percent']) ? rtrim(rtrim(number_format((double) str_replace('%', '', $table_rate_data['rate_cost_percent']), 2, '.', ''), '0'), '.') : '';
						$rate_label = isset($table_rate_data['rate_label']) ? $table_rate_data['rate_label'] : '';
						if ($rate_id > 0) {
							$wpdb->update(
											$wpdb->prefix . 'woocommerce_shipping_table_rates', array(
									'rate_condition' => sanitize_title($rate_condition),
									'rate_min' => $rate_min,
									'rate_max' => $rate_max,
									'rate_cost' => $rate_cost,
									'rate_cost_per_item' => $rate_cost_per_item,
									'rate_cost_per_weight_unit' => $rate_cost_per_weight_unit,
									'rate_cost_percent' => $rate_cost_percent,
									'rate_label' => $rate_label
											), array(
									'rate_id' => $rate_id
											), array(
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s'
											), array(
									'%d'
								)
							);
							unset( $old_rate_ids[$rate_id] );
						} else {
							$wpdb->insert("{$wpdb->prefix}woocommerce_shipping_table_rates", array(
									'rate_class' => $rate_class,
									'rate_condition' => sanitize_title($rate_condition),
									'rate_min' => $rate_min,
									'rate_max' => $rate_max,
									'rate_cost' => $rate_cost,
									'rate_cost_per_item' => $rate_cost_per_item,
									'rate_cost_per_weight_unit' => $rate_cost_per_weight_unit,
									'rate_cost_percent' => $rate_cost_percent,
									'shipping_method_id' => $shipping_method_id,
									'rate_label' => $rate_label
											), array(
									'%d',
									'%s',
									'%d',
									'%d',
									'%s',
									'%s',
									'%s',
									'%s',
									'%d',
									'%s'
								)
							);
						}
						
					}
				}
			}
			// Removing Old Rates
			if( !empty( $old_rate_ids ) ) {
				foreach( $old_rate_ids as $old_rate_id ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_shipping_table_rates WHERE rate_id = %d;", absint( $old_rate_id ) ) );
				}
			}
		}
		
		do_action( 'wcfm_wcmarketplace_settings_update', $user_id, $wcfm_settings_form );
		
		echo '{"status": true, "message": "' . __( 'Settings saved successfully', 'wc-frontend-manager' ) . '"}';
		 
		die;
	}
}