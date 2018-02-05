<?php
/**
 * WCFM plugin views
 *
 * Plugin Third Party Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfm/views/product-manager
 * @version   2.1.0
 */
global $wp, $WCFM;

// Yoast SEO Support
$yoast_wpseo_focuskw_text_input = '';
$yoast_wpseo_metadesc = '';

// WooCommerce Custom Product Tabs Lite Support
$product_tabs = array();

// WooCommerce barcode & ISBN support
$barcode = '';
$ISBN = '';

// WooCommerce MSRP Pricing Support
$msrp_price = '';

// Quantities and Units for WooCommerce
$_wpbo_deactive = 'no';
$_wpbo_override = 'no';
$_wpbo_step = '';
$_wpbo_minimum = '';
$_wpbo_maximum = '';
$_wpbo_minimum_oos = '';
$_wpbo_maximum_oos = '';
$unit = '';

// WooCommerce Product Fees Support
$product_fee_name = '';
$product_fee_amount = '';
$product_fee_multiplier = 'no';

// WooCommerce Bulk Discount Support
$_bulkdiscount_enabled = 'no';
$_bulkdiscount_text_info = '';
$_bulkdiscounts = array();

// WC Role Based Price
$_role_based_price = array();

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		// Yoast SEO Support
		if(WCFM_Dependencies::wcfm_yoast_plugin_active_check()) {
			$yoast_wpseo_focuskw_text_input = get_post_meta( $product_id, '_yoast_wpseo_focuskw_text_input', true );
			$yoast_wpseo_metadesc = get_post_meta( $product_id, '_yoast_wpseo_metadesc', true );
		}
		
		// WooCommerce Custom Product Tabs Lite Support
		if(WCFM_Dependencies::wcfm_wc_tabs_lite_plugin_active_check()) {
			$product_tabs = (array) get_post_meta( $product_id, 'frs_woo_product_tabs', true );
		}
		
		// WooCommerce barcode & ISBN Support
		if(WCFM_Dependencies::wcfm_wc_barcode_isbn_plugin_active_check()) {
			$barcode = get_post_meta( $product_id, 'barcode', true );
			$ISBN = get_post_meta( $product_id, 'ISBN', true );
		}
		
		// WooCommerce MSRP Pricing Support
		if(WCFM_Dependencies::wcfm_wc_msrp_pricing_plugin_active_check()) {
			$msrp_price = get_post_meta( $product_id, '_msrp_price', true );
		}
		
		// Quantities and Units for WooCommerce
		if(WCFM_Dependencies::wcfm_wc_quantities_units_plugin_active_check()) {
			$_wpbo_deactive = ( get_post_meta( $product_id, '_wpbo_deactive', true ) ) ? 'on' : '';
			$_wpbo_override = ( get_post_meta( $product_id, '_wpbo_override', true ) ) ? 'on' : '';
			$_wpbo_step = get_post_meta( $product_id, '_wpbo_step', true );
			$_wpbo_minimum = get_post_meta( $product_id, '_wpbo_minimum', true );
			$_wpbo_maximum = get_post_meta( $product_id, '_wpbo_maximum', true );
			$_wpbo_minimum_oos = get_post_meta( $product_id, '_wpbo_minimum_oos', true );
			$_wpbo_maximum_oos = get_post_meta( $product_id, '_wpbo_maximum_oos', true );
			$unit = get_post_meta( $product_id, 'unit', true );
		}
		
		// WooCommerce Product Fees Support
		if( $allow_product_fees = apply_filters( 'wcfm_is_allow_product_fees', true ) ) {
			if(WCFM_Dependencies::wcfm_wc_product_fees_plugin_active_check()) {
				$product_fee_name = get_post_meta( $product_id, 'product-fee-name', true );
				$product_fee_amount = get_post_meta( $product_id, 'product-fee-amount', true );
				$product_fee_multiplier = get_post_meta( $product_id, 'product-fee-multiplier', true );
			}
		}
		
		// WooCommerce Bulk Discount Support
		if( $allow_bulk_discount = apply_filters( 'wcfm_is_allow_bulk_discount', true ) ) {
			if(WCFM_Dependencies::wcfm_wc_bulk_discount_plugin_active_check()) {
				$_bulkdiscount_enabled = get_post_meta( $product_id, '_bulkdiscount_enabled', true );
				$_bulkdiscount_text_info = get_post_meta( $product_id, '_bulkdiscount_text_info', true );
				$_bulkdiscounts = (array) get_post_meta( $product_id, '_bulkdiscounts', true );
			}
		}
		
		// WC Role Based Price Support - 3.2.8
		if( $allow_bulk_discount = apply_filters( 'wcfm_is_allow_role_based_price', true ) ) {
			if(WCFM_Dependencies::wcfm_wc_role_based_price_active_check()) {
				$role_based_price = (array) get_post_meta( $product_id, '_role_based_price', true );
			}
		}
	}
}

// GEO my WP
if(WCFM_Dependencies::wcfm_geo_my_wp_plugin_active_check()) {
	$forms = get_option('gmw_forms');
  if ( empty( $forms ) || ! is_array( $forms ) ) {
  	$forms = array();
	}
	$forms_json = "";		
	foreach( $forms as $form ) {
		$form['name'] = ( !empty( $form['name'] ) ) ? $form['name'] : 'form_id_'.$form['ID'];
		if( $forms_json == '' ) $forms_json = "[";
		else $forms_json .= ',';
		$forms_json .= "{value: '" . absint( $form['ID'] ) . "', text: '" . esc_html( $form['name'] ) . "'}";
	}
	if( $forms_json == '' ) $forms_json = "[";
	$forms_json .= "]";
	?>
	<script>
	var gmw_forms = <?php echo $forms_json; ?>;
	</script>
	<?php
}
?>

    <?php if( $allow_seo = apply_filters( 'wcfm_is_allow_seo', true ) ) { ?>
			<?php if(WCFM_Dependencies::wcfm_yoast_plugin_active_check()) { ?>
				<!-- collapsible 10 - Yoast SEO Support -->
				<div class="page_collapsible products_manage_yoast simple variable grouped external booking" id="wcfm_products_manage_form_yoast_head"><label class="fa fa-yoast"></label><?php _e('Yoast SEO', 'wc-frontend-manager'); ?><span></span></div>
				<div class="wcfm-container simple variable external grouped booking">
					<div id="wcfm_products_manage_form_yoast_expander" class="wcfm-content">
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_yoast', array(  
																																																"yoast_wpseo_focuskw_text_input" => array('label' => __('Enter a focus keyword', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $yoast_wpseo_focuskw_text_input, 'hints' => __( 'It should appear in title and first paragraph of the copy.', 'wc-frontend-manager' )),
																																																"yoast_wpseo_metadesc" => array('label' => __('Meta description', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_ele wcfm_title simple variable external grouped booking', 'value' => $yoast_wpseo_metadesc, 'hints' => __( 'It should not be more than 156 characters.', 'wc-frontend-manager' ))
																																											)) );
						?>
					</div>
				</div>
				<!-- end collapsible -->
				<div class="wcfm_clearfix"></div>
			<?php } ?>
		<?php } ?>
		
		<?php if(WCFM_Dependencies::wcfm_wc_tabs_lite_plugin_active_check()) { ?>
			<!-- collapsible 11 - WooCommerce Custom Product Tabs Lite Support -->
			<div class="page_collapsible products_manage_wc_tabs_lite simple variable grouped external booking" id="wcfm_products_manage_form_wc_tabs_lite_head"><label class="fa fa-list-alt"></label><?php _e('Custom Tabs', 'wc-frontend-manager'); ?><span></span></div>
			<div class="wcfm-container simple variable external grouped booking">
				<div id="wcfm_products_manage_form_wc_tabs_lite_expander" class="wcfm-content">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_wc_tabs_lite', array( 
																																													"product_tabs" => array('label' => __('Tabs', 'wc-frontend-manager') , 'type' => 'multiinput', 'class' => 'wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $product_tabs, 'options' => array(  
																																															"title" => array('label' => __('Title', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'hints' => __( 'Required for tab to be visible', 'wc-frontend-manager' )),
																																															"content" => array('label' => __('Content', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_ele wcfm_title simple variable external grouped booking', 'placeholder' => __( 'HTML or Text to display ...', 'wc-frontend-manager' ))
																																													) ) 
																																												) ) );
					?>
				</div>
			</div>
			<!-- end collapsible -->
			<div class="wcfm_clearfix"></div>
		<?php } ?>
		
		<?php if( $allow_barcode_isbn = apply_filters( 'wcfm_is_allow_barcode_isbn', true ) ) { ?>
			<?php if(WCFM_Dependencies::wcfm_wc_barcode_isbn_plugin_active_check()) { ?>
				<!-- collapsible 12 - WooCommerce Barcode & ISBN Support -->
				<div class="page_collapsible products_manage_barcode_isbn simple external non-variable-subscription" id="wcfm_products_manage_form_barcode_isbn_head"><label class="fa fa-barcode"></label><?php _e('Barcode & ISBN', 'wc-frontend-manager'); ?><span></span></div>
				<div class="wcfm-container simple external non-variable-subscription">
					<div id="wcfm_products_manage_form_barcode_isbn_expander" class="wcfm-content">
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_barcode_isbn', array(  
																																																"barcode" => array('label' => __('Barcode', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple external non-variable-subscription', 'label_class' => 'wcfm_title wcfm_ele simple external non-variable-subscription', 'value' => $barcode ),
																																																"ISBN" => array('label' => __('ISBN', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple external non-variable-subscription', 'label_class' => 'wcfm_ele wcfm_title simple external non-variable-subscription', 'value' => $ISBN )
																																											) ) );
						?>
					</div>
				</div>
				<!-- end collapsible -->
				<div class="wcfm_clearfix"></div>
			<?php } ?>
		<?php } ?>
		
		<?php if( $allow_msrp_pricing = apply_filters( 'wcfm_is_allow_msrp_pricing', true ) ) { ?>
			<?php if(WCFM_Dependencies::wcfm_wc_msrp_pricing_plugin_active_check()) { ?>
				<!-- collapsible 13 - WooCommerce MSRP Pricing Support -->
				<div class="page_collapsible products_manage_msrp_pricing simple external non-variable-subscription" id="wcfm_products_manage_form_msrp_pricing_head"><label class="fa fa-bitcoin"></label><?php _e('MSRP Pricing', 'wc-frontend-manager'); ?><span></span></div>
				<div class="wcfm-container simple external non-variable-subscription">
					<div id="wcfm_products_manage_form_msrp_pricing_expander" class="wcfm-content">
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_msrp_pricing', array(  
																																																"_msrp_price" => array('label' => __('MSRP Price', 'wc-frontend-manager') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple external non-variable-subscription', 'label_class' => 'wcfm_title wcfm_ele simple external non-variable-subscription', 'value' => $barcode )
																																											) ) );
						?>
					</div>
				</div>
				<!-- end collapsible -->
				<div class="wcfm_clearfix"></div>
			<?php } ?>
		<?php } ?>
		
		<?php if( $allow_quantities_units = apply_filters( 'wcfm_is_allow_quantities_units', true ) ) { ?>
			<?php if(WCFM_Dependencies::wcfm_wc_quantities_units_plugin_active_check()) { ?>
				<!-- collapsible 14 - Quantities and Units for WooCommerce Support -->
				<div class="page_collapsible products_manage_quantities_units simple variable external grouped booking" id="wcfm_products_manage_form_quantities_units_head"><label class="fa fa-calculator"></label><?php _e('Quantities and Units', 'wc-frontend-manager'); ?><span></span></div>
				<div class="wcfm-container simple variable external grouped booking">
					<div id="wcfm_products_manage_form_quantities_units_expander" class="wcfm-content">
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_quantities_units', array(  
																																																"_wpbo_deactive" => array('label' => __('Deactivate Quantity Rules', 'wc-frontend-manager'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => 'on', 'dfvalue' => $_wpbo_deactive ),
																																																"_wpbo_override" => array('label' => __('Override Quantity Rules', 'wc-frontend-manager'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => 'on', 'dfvalue' => $_wpbo_override ),
																																																"_wpbo_step" => array('label' => __('Step Value', 'wc-frontend-manager'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $_wpbo_step ),
																																																"_wpbo_minimum" => array('label' => __('Minimum Quantity', 'wc-frontend-manager'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $_wpbo_minimum ),
																																																"_wpbo_maximum" => array('label' => __('Maximum Quantity', 'wc-frontend-manager'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $_wpbo_maximum ),
																																																"_wpbo_minimum_oos" => array('label' => __('Out of Stock Minimum', 'wc-frontend-manager'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $_wpbo_minimum_oos ),
																																																"_wpbo_maximum_oos" => array('label' => __('Out of Stock Maximum', 'wc-frontend-manager'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $_wpbo_maximum_oos ),
																																																"unit" => array('label' => __('Unit', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $unit )
																																																) ) );
						?>
					</div>
				</div>
				<!-- end collapsible -->
				<div class="wcfm_clearfix"></div>
			<?php } ?>
		<?php } ?>
		
		<?php if( $allow_product_fees = apply_filters( 'wcfm_is_allow_product_fees', true ) ) { ?>
			<?php if(WCFM_Dependencies::wcfm_wc_product_fees_plugin_active_check()) { ?>
				<!-- collapsible 15 - WooCommerce Product Fees Support -->
				<div class="page_collapsible products_manage_product_fees simple variable grouped external booking" id="wcfm_products_manage_form_product_fees_head"><label class="fa fa-cart-plus"></label><?php _e('Product Fees', 'wc-frontend-manager'); ?><span></span></div>
				<div class="wcfm-container simple variable external grouped booking">
					<div id="wcfm_products_manage_form_product_fees_expander" class="wcfm-content">
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_product_fees', array(  
																																																		"product-fee-name" => array('label' => __('Fee Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $product_fee_name, 'hints' => __( 'This will be shown at the checkout description the added fee.', 'wc-frontend-manager' )),
																																																		"product-fee-amount" => array('label' => __('Fee Amount', 'wc-frontend-manager') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_ele wcfm_title simple variable external grouped booking', 'value' => $product_fee_amount, 'hints' => __( 'Enter a monetary decimal without any currency symbols or thousand separator. This field also accepts percentages.', 'wc-frontend-manager' )),
																																																		"product-fee-multiplier" => array('label' => __('Multiple Fee by Quantity', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple variable external grouped booking', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title simple variable external grouped booking', 'hints' => __( 'Multiply the fee by the quantity of this product that is added to the cart.', 'wc-frontend-manager' ), 'dfvalue' => $product_fee_multiplier ),
																																													)) );
						?>
					</div>
				</div>
				<!-- end collapsible -->
				<div class="wcfm_clearfix"></div>
			<?php } ?>
		<?php } ?>
		
		<?php if( $allow_bulk_discount = apply_filters( 'wcfm_is_allow_bulk_discount', true ) ) { ?>
			<?php if(WCFM_Dependencies::wcfm_wc_bulk_discount_plugin_active_check()) { ?>
				<!-- collapsible 16 - WooCommerce Bulk Discount Support -->
				<div class="page_collapsible products_manage_bulk_discount simple variable grouped external booking" id="wcfm_products_manage_form_bulk_discount_head"><label class="fa fa-bullseye"></label><?php _e('Bulk Discount', 'wc-frontend-manager'); ?><span></span></div>
				<div class="wcfm-container simple variable external grouped booking">
					<div id="wcfm_products_manage_form_bulk_discount_expander" class="wcfm-content">
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_bulk_discount', array(  
																																																		"_bulkdiscount_enabled" => array('label' => __('Bulk Discount enabled', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple variable external grouped booking', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title simple variable external grouped booking', 'dfvalue' => $_bulkdiscount_enabled ),
																																																		"_bulkdiscount_text_info" => array('label' => __('Bulk discount special offer text in product description', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_ele wcfm_title simple variable external grouped booking', 'value' => $_bulkdiscount_text_info ),
																																																		"_bulkdiscounts" => array('label' => __('Discount Rules', 'wc-frontend-manager') , 'type' => 'multiinput', 'custom_attributes' => array( 'limit' => 5 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $_bulkdiscounts, 'options' => array(
																																																						"quantity" => array('label' => __('Quantity (min.)', 'wc-frontend-manager'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title'),
																																																						"discount" => array('label' => __('Discount (%)', 'wc-frontend-manager'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title'),
																																																				))
																																													)) );
						?>
					</div>
				</div>
				<!-- end collapsible -->
				<div class="wcfm_clearfix"></div>
			<?php } ?>
		<?php } ?>
		
		<?php if( apply_filters( 'wcfm_is_allow_role_based_price', true ) ) { ?>
			<?php if(WCFM_Dependencies::wcfm_wc_role_based_price_active_check()) { ?>
				<!-- collapsible 17 - WooCommerce Role Based Price -->
				<div class="page_collapsible products_manage_role_based_price simple grouped external" id="wcfm_products_manage_form_role_based_price_head"><label class="fa fa-users"></label><?php _e('Role Based Price', 'wc-frontend-manager'); ?><span></span></div>
				<div class="wcfm-container simple variable external grouped booking">
					<div id="wcfm_products_manage_form_bulk_discount_expander" class="wcfm-content">
						<?php
						if ( !function_exists('get_editable_roles') ) {
							 require_once( ABSPATH . '/wp-admin/includes/user.php' );
						}
						$wp_roles = get_editable_roles();
						$wc_rbp_general = (array) get_option( 'wc_rbp_general' );
						if( !empty( $wc_rbp_general ) ) {
							$wc_rbp_allowed_roles = ( isset( $wc_rbp_general['wc_rbp_allowed_roles'] ) ) ? $wc_rbp_general['wc_rbp_allowed_roles'] : array();
							$wc_rbp_regular_price_label = ( isset( $wc_rbp_general['wc_rbp_regular_price_label'] ) ) ? $wc_rbp_general['wc_rbp_regular_price_label'] : __( 'Regular Price', 'wc-frontend-manager' );
							$wc_rbp_selling_price_label = ( isset( $wc_rbp_general['wc_rbp_selling_price_label'] ) ) ? $wc_rbp_general['wc_rbp_selling_price_label'] : __( 'Selling Price', 'wc-frontend-manager' );
							if( !empty( $wc_rbp_allowed_roles ) ) {
								foreach( $wc_rbp_allowed_roles as $wc_rbp_allowed_role ) {
									$regular_price = '';
									$selling_price = '';
									if( isset( $role_based_price[$wc_rbp_allowed_role] ) && isset( $role_based_price[$wc_rbp_allowed_role]['regular_price'] ) ) $regular_price = $role_based_price[$wc_rbp_allowed_role]['regular_price'];
									if( isset( $role_based_price[$wc_rbp_allowed_role] ) && isset( $role_based_price[$wc_rbp_allowed_role]['selling_price'] ) ) $selling_price = $role_based_price[$wc_rbp_allowed_role]['selling_price'];
									if( isset( $wp_roles[$wc_rbp_allowed_role] ) ) echo '<h2>' . $wp_roles[$wc_rbp_allowed_role]['name'] . '</h2><div class="wcfm-clearfix"></div>';
									$WCFM->wcfm_fields->wcfm_generate_form_field( array(  
																																			"_role_based_regular_price" => array( 'label' => $wc_rbp_regular_price_label, 'name' => 'role_based_price[' . $wc_rbp_allowed_role . '][regular_price]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple external grouped', 'label_class' => 'wcfm_ele wcfm_title simple external grouped', 'value' => $regular_price ),
																																			"_role_based_sale_price"    => array( 'label' => $wc_rbp_selling_price_label, 'name' => 'role_based_price[' . $wc_rbp_allowed_role . '][selling_price]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple external grouped', 'label_class' => 'wcfm_ele wcfm_title simple external grouped', 'value' => $selling_price ),
																														) );
								}
							}
						}
						?>
					</div>
				</div>
				<!-- end collapsible -->
				<div class="wcfm_clearfix"></div>
			<?php } ?>
		<?php } ?>