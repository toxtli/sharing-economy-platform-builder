<?php

/**
 * WCFM plugin core
 *
 * WC Subscriptions Support
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   2.2.2
 */
 
class WCFM_WCSubscriptions {
	
	public function __construct() {
    global $WCFM;
    
    if( wcfm_is_subscription() ) {
    	
    	// Subscriptions Product Type
    	add_filter( 'wcfm_product_types', array( &$this, 'wcs_product_types' ), 30 );
    	
    	// Subscriptions Product Type Capability
			add_filter( 'wcfm_settings_fields_product_types', array( &$this, 'wcfmcap_product_types' ), 30, 3 );
    	
    	// Subscriptions Product options
    	add_filter( 'wcfm_product_manage_fields_pricing', array( &$this, 'wcs_product_manage_fields_pricing' ), 30, 2 );
    	
    	// Subscriptions Product Meta Data Save
    	add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcs_wcfm_product_meta_save' ), 30, 2 );
    }
    
  }
  
  /**
   * WC Subscriptions Product Type
   */
  function wcs_product_types( $pro_types ) {
  	global $WCFM;
  	
  	$pro_types['subscription'] = __( 'Simple subscription', 'woocommerce-subscriptions' );
  	
  	return $pro_types;
  }
  
  /**
	 * WCFM Capability Vendor Product Types
	 */
	function wcfmcap_product_types( $product_types, $handler = 'wcfm_capability_options', $wcfm_capability_options = array() ) {
		global $WCFM;
		
		$subscription = ( isset( $wcfm_capability_options['subscription'] ) ) ? $wcfm_capability_options['subscription'] : 'no';
		$variable_subscription = ( isset( $wcfm_capability_options['variable-subscription'] ) ) ? $wcfm_capability_options['variable-subscription'] : 'no';
		
		$product_types["subscription"]          = array('label' => __('Subscriptions', 'wc-frontend-manager') , 'name' => $handler . '[subscription]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $subscription);
		$product_types["variable-subscription"] = array('label' => __('Variable Subscriptions', 'wc-frontend-manager') , 'name' => $handler . '[variable-subscription]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $variable_subscription);
		
		return $product_types;
	}
	
  /**
	 * WC Subscriptions Product General options
	 */
	function wcs_product_manage_fields_pricing( $general_fields, $product_id ) {
		global $WCFM;
		
		$chosen_price        = '';
		$chosen_interval     = 1;
		$chosen_period       = 'month';
		$subscription_length = '';
		
		if( $product_id ) {
			$chosen_price        = get_post_meta( $product_id, '_subscription_price', true );
			$chosen_interval     = get_post_meta( $product_id, '_subscription_period_interval', true );
			$chosen_period       = get_post_meta( $product_id, '_subscription_period', true );
			$subscription_length = get_post_meta( $product_id, '_subscription_length', true );
		}
		
		$general_fields = array_slice($general_fields, 0, 3, true) +
																	array( "_subscription_price" => array('label' => sprintf( esc_html__( 'Subscription price (%s)', 'woocommerce-subscriptions' ), esc_html( get_woocommerce_currency_symbol() ) ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele subscription_price_ele subscription', 'label_class' => 'wcfm_title wcfm_ele subscription', 'hints' => __( 'Choose the subscription price, billing interval and period.', 'woocommerce-subscriptions' ), 'value' => $chosen_price ),
																				"_subscription_period_interval" => array( 'type' => 'select', 'options' => wcs_get_subscription_period_interval_strings(), 'class' => 'wcfm-select wcfm_ele subscription_price_ele subscription', 'label_class' => 'wcfm_title wcfm_ele subscription', 'value' => $chosen_interval ),
																				"_subscription_period" => array( 'type' => 'select', 'options' => wcs_get_subscription_period_strings(), 'class' => 'wcfm-select wcfm_ele subscription_price_ele subscription', 'label_class' => 'wcfm_title wcfm_ele subscription', 'value' => $chosen_period ),
																				"_subscription_length_day" => array( 'label' => __('Subscription length', 'woocommerce-subscriptions' ), 'type' => 'select', 'options' => wcs_get_subscription_ranges( 'day' ), 'class' => 'wcfm-select wcfm_ele subscription_length_ele subscription_length_day subscription', 'label_class' => 'wcfm_title wcfm_ele subscription_length_ele subscription_length_day subscription', 'hints' => __( 'Automatically expire the subscription after this length of time. This length is in addition to any free trial or amount of time provided before a synchronised first renewal date.', 'woocommerce-subscriptions' ), 'value' => $subscription_length ),
																				"_subscription_length_week" => array( 'label' => __('Subscription length', 'woocommerce-subscriptions' ), 'type' => 'select', 'options' => wcs_get_subscription_ranges( 'week' ), 'class' => 'wcfm-select wcfm_ele subscription_length_ele subscription_length_week subscription', 'label_class' => 'wcfm_title wcfm_ele subscription_length_ele subscription_length_week subscription', 'hints' => __( 'Automatically expire the subscription after this length of time. This length is in addition to any free trial or amount of time provided before a synchronised first renewal date.', 'woocommerce-subscriptions' ), 'value' => $subscription_length ),
																				"_subscription_length_month" => array( 'label' => __('Subscription length', 'woocommerce-subscriptions' ), 'type' => 'select', 'options' => wcs_get_subscription_ranges( 'month' ), 'class' => 'wcfm-select wcfm_ele subscription_length_ele subscription_length_month subscription', 'label_class' => 'wcfm_title wcfm_ele subscription_length_ele subscription_length_month subscription', 'hints' => __( 'Automatically expire the subscription after this length of time. This length is in addition to any free trial or amount of time provided before a synchronised first renewal date.', 'woocommerce-subscriptions' ), 'value' => $subscription_length ),
																				"_subscription_length_year" => array( 'label' => __('Subscription length', 'woocommerce-subscriptions' ), 'type' => 'select', 'options' => wcs_get_subscription_ranges( 'year' ), 'class' => 'wcfm-select wcfm_ele subscription_length_ele subscription_length_year subscription', 'label_class' => 'wcfm_title wcfm_ele subscription_length_ele subscription_length_year subscription', 'hints' => __( 'Automatically expire the subscription after this length of time. This length is in addition to any free trial or amount of time provided before a synchronised first renewal date.', 'woocommerce-subscriptions' ), 'value' => $subscription_length ),
																				) +
																	array_slice($general_fields, 3, count($general_fields) - 1, true) ;
		return $general_fields;
	}
	
	/**
	 * WC Subscriptions Product Meta data save
	 */
	function wcs_wcfm_product_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST;
		
		if( $wcfm_products_manage_form_data['product_type'] == 'subscription' ) {
			$subscription_price = isset( $wcfm_products_manage_form_data['_subscription_price'] ) ? wc_format_decimal( $wcfm_products_manage_form_data['_subscription_price'] ) : '';
			$sale_price         = wc_format_decimal( $wcfm_products_manage_form_data['sale_price'] );
	
			update_post_meta( $new_product_id, '_subscription_price', $subscription_price );
	
			// Set sale details - these are ignored by WC core for the subscription product type
			update_post_meta( $new_product_id, '_regular_price', $subscription_price );
			update_post_meta( $new_product_id, '_sale_price', $sale_price );
	
			$date_from = ( isset( $wcfm_products_manage_form_data['sale_date_from'] ) ) ? wcs_date_to_time( $wcfm_products_manage_form_data['sale_date_from'] ) : '';
			$date_to   = ( isset( $wcfm_products_manage_form_data['sale_date_upto'] ) ) ? wcs_date_to_time( $wcfm_products_manage_form_data['sale_date_upto'] ) : '';
	
			$now = gmdate( 'U' );
	
			if ( ! empty( $date_to ) && empty( $date_from ) ) {
				$date_from = $now;
			}
	
			update_post_meta( $new_product_id, '_sale_price_dates_from', $date_from );
			update_post_meta( $new_product_id, '_sale_price_dates_to', $date_to );
	
			// Update price if on sale
			if ( ! empty( $sale_price ) && ( ( empty( $date_to ) && empty( $date_from ) ) || ( $date_from < $now && ( empty( $date_to ) || $date_to > $now ) ) ) ) {
				$price = $sale_price;
			} else {
				$price = $subscription_price;
			}
	
			update_post_meta( $new_product_id, '_price', stripslashes( $price ) );
	
			$subscription_fields = array(
				'_subscription_period',
				'_subscription_period_interval'
			);
	
			foreach ( $subscription_fields as $field_name ) {
				if ( isset( $wcfm_products_manage_form_data[ $field_name ] ) ) {
					update_post_meta( $new_product_id, $field_name, stripslashes( $wcfm_products_manage_form_data[ $field_name ] ) );
				}
			}
			
			update_post_meta( $new_product_id, '_subscription_length', stripslashes( $wcfm_products_manage_form_data[ '_subscription_length_' . $wcfm_products_manage_form_data[ '_subscription_period' ]  ] ) );
		}
	}
}