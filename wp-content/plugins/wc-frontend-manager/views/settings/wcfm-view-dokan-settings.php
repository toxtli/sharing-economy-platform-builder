<?php
/**
 * WCFM plugin view
 *
 * WCFM DOkan Settings View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   3.3.0
 */

global $WCFM;

$wcfm_is_allow_manage_settings = apply_filters( 'wcfm_is_allow_manage_settings', true );
if( !$wcfm_is_allow_manage_settings ) {
	wcfm_restriction_message_show( "Settings" );
	return;
}

$user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );

$vendor_data = get_user_meta( $user_id, 'dokan_profile_settings', true );

$gravatar       = isset( $vendor_data['gravatar'] ) ? absint( $vendor_data['gravatar'] ) : 0;
$banner         = isset( $vendor_data['banner'] ) ? absint( $vendor_data['banner'] ) : 0;
$store_name     = isset( $vendor_data['store_name'] ) ? esc_attr( $vendor_data['store_name'] ) : '';
$store_name     = empty( $store_name ) ? get_user_by( 'id', $user_id )->display_name : $store_name;
$store_ppp      = isset( $vendor_data['store_ppp'] ) ? esc_attr( $vendor_data['store_ppp'] ) : '';
$phone          = isset( $vendor_data['phone'] ) ? esc_attr( $vendor_data['phone'] ) : '';
$show_email     = isset( $vendor_data['show_email'] ) ? esc_attr( $vendor_data['show_email'] ) : 'no';
$show_more_ptab = isset( $vendor_data['show_more_ptab'] ) ? esc_attr( $vendor_data['show_more_ptab'] ) : 'yes';

$address         = isset( $vendor_data['address'] ) ? $vendor_data['address'] : '';
$street_1 = isset( $vendor_data['address']['street_1'] ) ? $vendor_data['address']['street_1'] : '';
$street_2 = isset( $vendor_data['address']['street_2'] ) ? $vendor_data['address']['street_2'] : '';
$city    = isset( $vendor_data['address']['city'] ) ? $vendor_data['address']['city'] : '';
$zip     = isset( $vendor_data['address']['zip'] ) ? $vendor_data['address']['zip'] : '';
$country = isset( $vendor_data['address']['country'] ) ? $vendor_data['address']['country'] : '';
$state   = isset( $vendor_data['address']['state'] ) ? $vendor_data['address']['state'] : '';

$map_location   = isset( $vendor_data['location'] ) ? esc_attr( $vendor_data['location'] ) : '';
$map_address    = isset( $vendor_data['find_address'] ) ? esc_attr( $vendor_data['find_address'] ) : '';
$dokan_category = isset( $vendor_data['dokan_category'] ) ? $vendor_data['dokan_category'] : '';
$enable_tnc     = isset( $vendor_data['enable_tnc'] ) ? $vendor_data['enable_tnc'] : '';
$store_tnc      = isset( $vendor_data['store_tnc'] ) ? $vendor_data['store_tnc'] : '' ;

// Country -> States
$country_obj   = new WC_Countries();
$countries     = $country_obj->countries;
$states        = $country_obj->states;
$state_options = array();
if( $state && isset( $states[$country] ) && is_array( $states[$country] ) ) {
	$state_options = $states[$country];
}

// Gravatar image
$gravatar_url = $gravatar ? wp_get_attachment_url( $gravatar ) : '';

// banner URL
$banner_url = $banner ? wp_get_attachment_url( $banner ) : '';

$paypal = isset( $vendor_data['payment']['paypal']['email'] ) ? esc_attr( $vendor_data['payment']['paypal']['email'] ) : '' ;
$skrill = isset( $vendor_data['payment']['skrill']['email'] ) ? esc_attr( $vendor_data['payment']['skrill']['email'] ) : '' ;
$ac_name   = isset( $vendor_data['payment']['bank']['ac_name'] ) ? esc_attr( $vendor_data['payment']['bank']['ac_name'] ) : '';
$ac_number = isset( $vendor_data['payment']['bank']['ac_number'] ) ? esc_attr( $vendor_data['payment']['bank']['ac_number'] ) : '';
$bank_name      = isset( $vendor_data['payment']['bank']['bank_name'] ) ? esc_attr( $vendor_data['payment']['bank']['bank_name'] ) : '';
$bank_addr      = isset( $vendor_data['payment']['bank']['bank_addr'] ) ? esc_textarea( $vendor_data['payment']['bank']['bank_addr'] ) : '';
$routing_number = isset( $vendor_data['payment']['bank']['routing_number'] ) ? esc_attr( $vendor_data['payment']['bank']['routing_number'] ) : '';
$iban           = isset( $vendor_data['payment']['bank']['iban'] ) ? esc_attr( $vendor_data['payment']['bank']['iban'] ) : '';
$swift     = isset( $vendor_data['payment']['bank']['swift'] ) ? esc_attr( $vendor_data['payment']['bank']['swift'] ) : '';

$wcfm_vacation_mode = isset( $vendor_data['wcfm_vacation_mode'] ) ? $vendor_data['wcfm_vacation_mode'] : 'no';
$wcfm_disable_vacation_purchase = isset( $vendor_data['wcfm_disable_vacation_purchase'] ) ? $vendor_data['wcfm_disable_vacation_purchase'] : 'no';
$wcfm_vacation_mode_msg = ! empty( $vendor_data['wcfm_vacation_mode_msg'] ) ? $vendor_data['wcfm_vacation_mode_msg'] : '';

if( WCFM_Dependencies::dokanpro_plugin_active_check() ) {
	// Shipping
	$processing_time = dokan_get_shipping_processing_times();
	$dps_shipping_enable     = get_user_meta( $user_id, '_dps_shipping_enable', true );
	$dps_shipping_type_price = get_user_meta( $user_id, '_dps_shipping_type_price', true );
	$dps_additional_product  = get_user_meta( $user_id, '_dps_additional_product', true );
	$dps_additional_qty      = get_user_meta( $user_id, '_dps_additional_qty', true );
	$dps_pt                  = get_user_meta( $user_id, '_dps_pt', true );
	$dps_ship_policy         = get_user_meta( $user_id, '_dps_ship_policy', true );
	$dps_refund_policy       = get_user_meta( $user_id, '_dps_refund_policy', true );
	
	$dps_form_location       = get_user_meta( $user_id, '_dps_form_location', true );
	$dps_country_rates       = get_user_meta( $user_id, '_dps_country_rates', true );
	$dps_state_rates         = get_user_meta( $user_id, '_dps_state_rates', true );
	
	// SEO
	$address  = isset( $vendor_data['store_seo'] ) ? $vendor_data['store_seo'] : '';
	$dokan_seo_meta_title = isset( $vendor_data['store_seo']['dokan-seo-meta-title'] ) ? $vendor_data['store_seo']['dokan-seo-meta-title'] : '';
	$dokan_seo_meta_desc = isset( $vendor_data['store_seo']['dokan-seo-meta-desc'] ) ? $vendor_data['store_seo']['dokan-seo-meta-desc'] : '';
	$dokan_seo_meta_keywords    = isset( $vendor_data['store_seo']['dokan-seo-meta-keywords'] ) ? $vendor_data['store_seo']['dokan-seo-meta-keywords'] : '';
	$dokan_seo_og_title     = isset( $vendor_data['store_seo']['dokan-seo-og-title'] ) ? $vendor_data['store_seo']['dokan-seo-og-title'] : '';
	$dokan_seo_og_desc = isset( $vendor_data['store_seo']['dokan-seo-og-desc'] ) ? $vendor_data['store_seo']['dokan-seo-og-desc'] : '';
	$dokan_seo_og_image   = isset( $vendor_data['store_seo']['dokan-seo-og-image'] ) ? $vendor_data['store_seo']['dokan-seo-og-image'] : 0;
	$dokan_seo_twitter_title     = isset( $vendor_data['store_seo']['dokan-seo-twitter-title'] ) ? $vendor_data['store_seo']['dokan-seo-twitter-title'] : '';
	$dokan_seo_twitter_desc = isset( $vendor_data['store_seo']['dokan-seo-twitter-desc'] ) ? $vendor_data['store_seo']['dokan-seo-twitter-desc'] : '';
	$dokan_seo_twitter_image   = isset( $vendor_data['store_seo']['dokan-seo-twitter-image'] ) ? $vendor_data['store_seo']['dokan-seo-twitter-image'] : 0;
	
	// Facebook image
	$dokan_seo_og_image_url = $dokan_seo_og_image ? wp_get_attachment_thumb_url( $dokan_seo_og_image ) : '';
	
	// Twitter URL
	$dokan_seo_twitter_image_url = $dokan_seo_twitter_image ? wp_get_attachment_thumb_url( $dokan_seo_twitter_image ) : '';
}

$general_settings = get_option( 'dokan_general', [] );
$banner_width = ! empty( $general_settings['store_banner_width'] ) ? $general_settings['store_banner_width'] : 625;
$banner_height = ! empty( $general_settings['store_banner_height'] ) ? $general_settings['store_banner_height'] : 300;

$banner_help_text = sprintf(
		__('Upload a banner for your store. Banner size is (%sx%s) pixels.', 'dokan-lite' ),
		$banner_width, $banner_height
);

$is_marketplace = wcfm_is_marketplace();
?>

<div class="collapse wcfm-collapse" id="">
  <div class="wcfm-page-headig">
		<span class="fa fa-cogs"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Settings', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
	  <div class="wcfm-container wcfm-top-element-container">
	  	<h2><?php _e('Store Settings', 'wc-frontend-manager' ); ?></h2>
	  	<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
		
	  <?php do_action( 'before_wcfm_dokan_settings' ); ?>
		
	  <form id="wcfm_settings_form" class="wcfm">
	
			<?php do_action( 'begin_wcfm_dokan_settings_form' ); ?>
			
			<div class="wcfm-tabWrap">
				<!-- collapsible -->
				<div class="page_collapsible" id="wcfm_settings_dashboard_head">
					<label class="fa fa-shopping-bag"></label>
					<?php _e('Store', 'wc-frontend-manager'); ?><span></span>
				</div>
				<div class="wcfm-container">
					<div id="wcfm_settings_form_store_expander" class="wcfm-content">
						<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_dokan_settings_fields_general', array(
																																																"gravatar" => array('label' => __('Profile Image', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'prwidth' => 150, 'value' => $gravatar_url ),
																																																"banner" => array('label' => __('Banner', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'prwidth' => 250, 'value' => $banner_url, 'hints' => $banner_help_text ),
																																																"store_name" => array('label' => __('Shop Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $store_name ),
																																																"store_ppp" => array('label' => __('Store Product Per Page', 'wc-frontend-manager') , 'type' => 'number', 'placeholder' => '10', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $store_ppp ),
																																																"phone" => array('label' => __('Store Phone', 'wc-frontend-manager') , 'type' => 'text', 'placeholder' => '+123456..', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $phone ),
																																																"show_email" => array('label' => __('Show email in store', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $show_email ),
																																																"show_more_ptab" => array('label' => __('Show tab on product single page view', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $show_more_ptab ),
																																																) ) );
						?>
						
						<div class="wcfm_clearfix"></div>
						<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Store Address', 'wc-frontend-manager' ); ?></h3></div>
						<div class="store_address">
							<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_dokan_settings_fields_address', array(
																																																	"street_1" => array('label' => __('Street', 'wc-frontend-manager'), 'placeholder' => __('Street adress', 'wc-frontend-manager'), 'name' => 'address[street_1]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $street_1 ),
																																																	"street_2" => array('label' => __('Street 2', 'wc-frontend-manager'), 'placeholder' => __('Apartment, suit, unit etc. (optional)', 'wc-frontend-manager'), 'name' => 'address[street_2]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $street_2 ),
																																																	"city" => array('label' => __('City/Town', 'wc-frontend-manager'), 'placeholder' => __('Town / City', 'wc-frontend-manager'), 'name' => 'address[city]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $city ),
																																																	"zip" => array('label' => __('Postcode/Zip', 'wc-frontend-manager'), 'placeholder' => __('Postcode / Zip', 'wc-frontend-manager'), 'name' => 'address[zip]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $zip ),
																																																	"country" => array('label' => __('Country', 'wc-frontend-manager'), 'name' => 'address[country]', 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'custom_attributes' => array( 'required' => true ), 'value' => $country ),
																																																	"state" => array('label' => __('State/County', 'wc-frontend-manager'), 'name' => 'address[state]', 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'custom_attributes' => array( 'required' => true ), 'options' => $state_options, 'value' => $state ),
																																																	) ) );
							?>
						</div>
						
						<div class="wcfm_clearfix"></div>
						<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Store Location', 'wc-frontend-manager' ); ?></h3></div>
						<div class="store_address">
							<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_dokan_settings_fields_location', array(
																																																	"find_address" => array('label' => __( 'Find Address', 'dokan-lite' ), 'placeholder' => __( 'Type an address to find', 'dokan-lite' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $map_address ),
																																																	"location" => array( 'type' => 'hidden', 'value' => $map_location ),
																																																	) ) );
							?>
							<div class="wcfm_clearfix"></div><br />
							<div class="wcfm-dokan-google-map" id="wcfm-dokan-map" style="width:300px; height:300px; border:1px solid #DFDFDF; margin-left: 35%;"></div>
							<div class="wcfm_clearfix"></div><br />
						</div>
						
						<?php
						$tnc_enable = dokan_get_option( 'seller_enable_terms_and_conditions', 'dokan_general', 'off' );
						if ( $tnc_enable == 'on' ) :
						?>
							<div class="wcfm_clearfix"></div>
							<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Terms and Conditions', 'dokan-lite' ); ?></h3></div>
							<div class="store_address">
								<?php
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_dokan_settings_fields_terms', array(
																																																												"enable_tnc" => array('label' => __('Show terms and conditions in store page', 'dokan-lite') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $enable_tnc ),
																																																												"store_tnc" => array('label' => __('TOC Details', 'dokan-lite') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $store_tnc )
																																																											 ) ) );
								?>
							</div>
						<?php
						endif;
						?>
					</div>
				</div>
				<div class="wcfm_clearfix"></div>
				<!-- end collapsible -->
			
			  <!-- collapsible -->
				<?php if( $wcfm_is_allow_billing_settings = apply_filters( 'wcfm_is_allow_billing_settings', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_settings_form_payment_head">
						<label class="fa fa-money"></label>
						<?php _e('Payment', 'wc-frontend-manager'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_settings_form_payment_expander" class="wcfm-content">
							<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_dokan_settings_fields_billing', array(
																																															"paypal" => array('label' => __('PayPal Email', 'wc-frontend-manager'), 'name' => 'payment[paypal][email]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $paypal ),
																																															"skrill" => array('label' => __('Skrill Email', 'wc-frontend-manager'), 'name' => 'payment[skrill][email]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $skrill ),
																																															) ) );
							?>
							
							<div class="wcfm_clearfix"></div>
							<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Bank Details', 'wc-frontend-manager' ); ?></h3></div>
							<div class="store_address">
								<?php
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_dokan_settings_fields_billing_bank', array(
																																			"ac_name" => array('label' => __('Account Name', 'wc-frontend-manager'), 'placeholder' => __('Your bank account name', 'dokan-lite'), 'name' => 'payment[bank][ac_name]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $ac_name ),
																																			"ac_number" => array('label' => __('Account Number', 'wc-frontend-manager'), 'placeholder' => __('Your bank account number', 'dokan-lite'), 'name' => 'payment[bank][ac_number]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $ac_number ),
																																			"bank_name" => array('label' => __('Bank Name', 'wc-frontend-manager'), 'placeholder' => __('Name of bank', 'dokan-lite'), 'name' => 'payment[bank][bank_name]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bank_name ),
																																			"bank_addr" => array('label' => __('Bank Address', 'wc-frontend-manager'), 'placeholder' => __('Address of your bank', 'dokan-lite'), 'name' => 'payment[bank][bank_addr]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bank_addr ),
																																			"routing_number" => array('label' => __('Routing Number', 'wc-frontend-manager'), 'placeholder' => __( 'Routing number', 'dokan-lite' ), 'name' => 'payment[bank][routing_number]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $routing_number ),
																																			"iban" => array('label' => __('IBAN', 'wc-frontend-manager'), 'placeholder' => __('IBAN', 'dokan-lite'), 'name' => 'payment[bank][iban]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $iban ),
																																			"swift" => array('label' => __('Swift Code', 'wc-frontend-manager'), 'placeholder' => __('Swift code', 'dokan-lite'), 'name' => 'payment[bank][swift]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $swift ),
																																			) ) );
								?>
							</div>
							
							
						</div>
					</div>
					<div class="wcfm_clearfix"></div>
				<?php } ?>
				<!-- end collapsible -->
			
				<!-- collapsible -->
				<?php if( $wcfm_is_allow_vacation_settings = apply_filters( 'wcfm_is_allow_vacation_settings', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_settings_form_vacation_head">
						<label class="fa fa-tripadvisor"></label>
						<?php _e('Vacation Mode', 'wc-frontend-manager'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_settings_form_vacation_expander" class="wcfm-content">
							<?php
							if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_vendors_settings_fields_vacation', array(
																																																													"wcfm_vacation_mode" => array('label' => __('Enable Vacation Mode', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vacation_mode ),
																																																													"wcfm_disable_vacation_purchase" => array('label' => __('Disable Purchase During Vacation', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_disable_vacation_purchase ),
																																																													"wcfm_vacation_mode_msg" => array('label' => __('Vacation Message', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_vacation_mode_msg )
																																																												 ) ) );
							} else {
								if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
									wcfmu_feature_help_text_show( __( 'Vacation Mode', 'wc-frontend-manager' ) );
								}
							}
							?>
						</div>
					</div>
					<div class="wcfm_clearfix"></div>
				<?php } ?>
				<!-- end collapsible -->
			
				<!-- collapsible -->
				<?php if( WCFM_Dependencies::dokanpro_plugin_active_check() ) { ?>
					<?php if( $wcfm_is_allow_vshipping_settings = apply_filters( 'wcfm_is_allow_vshipping_settings', true ) ) { ?>
						<div class="page_collapsible" id="wcfm_settings_form_shipping_head">
							<label class="fa fa-truck"></label>
							<?php _e('Shipping', 'wc-frontend-manager'); ?><span></span>
						</div>
						<div class="wcfm-container">
							<div id="wcfm_settings_form_shipping_expander" class="wcfm-content">
								<?php
								// Dokan Pro Settings
								if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_vendors_settings_fields_shipping', array(
																																								"dps_shipping_enable" => array('label' => __('Enable Shipping', 'wc-frontend-manager') , 'name' => 'shipping[_dps_shipping_enable]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $dps_shipping_enable, 'hints' => __('Check this if you want to enable shipping for your store', 'dokan') ),
																																								"dps_shipping_type_price" => array('label' => __('Default Shipping Price', 'dokan'), 'name' => 'shipping[_dps_shipping_type_price]', 'placeholder' => '0.00', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dps_shipping_type_price, 'hints' => __('This is the base price and will be the starting shipping price for each product', 'dokan') ),
																																								"dps_additional_product" => array('label' => __('Per Product Additional Price', 'dokan'), 'name' => 'shipping[_dps_additional_product]', 'placeholder' => '0.00', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dps_additional_product, 'hints' => __('If a customer buys more than one type product from your store, first product of the every second type will be charged with this price', 'dokan') ),
																																								"dps_additional_qty" => array('label' => __('Per Qty Additional Price', 'dokan'), 'name' => 'shipping[_dps_additional_qty]', 'placeholder' => '0.00', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dps_additional_qty, 'hints' => __('Every second product of same type will be charged with this price', 'dokan') ),
																																								"dps_pt" => array('label' => __('Processing Time', 'dokan'), 'name' => 'shipping[_dps_pt]', 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => $processing_time, 'value' => $dps_pt, 'hints' => __('The time required before sending the product for delivery', 'dokan') ),
																																								"dps_ship_policy" => array('label' => __('Shipping Policy', 'wc-frontend-manager'), 'name' => 'shipping[_dps_ship_policy]', 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dps_ship_policy, 'hints' => __( 'Write your terms, conditions and instructions about shipping', 'dokan' ) ),
																																								"dps_refund_policy" => array('label' => __('Refund Policy', 'wc-frontend-manager'), 'name' => 'shipping[_dps_refund_policy]', 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dps_refund_policy, 'hints' => __( 'Write your terms, conditions and instructions about refund', 'dokan' ) ),
																																								"dps_form_location" => array('label' => __('Ships from:', 'dokan'), 'name' => 'shipping[_dps_form_location]','type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dps_form_location, 'hints' => __( 'Location from where the products are shipped for delivery. Usually it is same as the store.', 'dokan' ) ),
																									
																																								) ) );
									
									$dps_shipping_rates = array();
									$state_options = array();
									if ( $dps_country_rates ) {
                    foreach ( $dps_country_rates as $country => $country_rate ) {
                    	$dps_shipping_state_rates = array();
                    	if ( !empty( $dps_state_rates ) && isset( $dps_state_rates[$country] ) ) {
                    		foreach ( $dps_state_rates[$country] as $state => $state_rate ) {
                    			$state_options[$state] = $state;
                    			$dps_shipping_state_rates[] = array( 'dps_state_to' => $state, 'dps_state_to_price' => $state_rate );
                    		}
                    	}
                    	$dps_shipping_rates[] = array( 'dps_country_to' => $country, 'dps_country_to_price' => $country_rate, 'dps_shipping_state_rates' => $dps_shipping_state_rates );
                   	} 	
                  }
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_vendors_settings_fields_shipping_rates', array( 
																																												"dps_shipping_rates" => array('label' => __('Shipping Rates', 'wc-frontend-manager') , 'type' => 'multiinput', 'label_class' => 'wcfm_title', 'value' => $dps_shipping_rates, 'desc' => __( 'Add the countries you deliver your products to. You can specify states as well. If the shipping price is same except some countries/states, there is an option Everywhere Else, you can use that.', 'wc-frontend-manager' ), 'options' => array(
																																																									"dps_country_to" => array('label' => __('Country', 'wc-frontend-manager'), 'type' => 'country', 'class' => 'wcfm-select dps_country_to_select', 'label_class' => 'wcfm_title' ),
																																																									"dps_country_to_price" => array( 'label' => __('Cost', 'wc-frontend-manager') . '('.get_woocommerce_currency_symbol().')', 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title' ),
																																																									"dps_shipping_state_rates" => array('label' => __('State Shipping Rates', 'wc-frontend-manager') , 'type' => 'multiinput', 'label_class' => 'wcfm_title', 'options' => array(
																																																																											"dps_state_to" => array( 'label' => __('State', 'wc-frontend-manager'), 'type' => 'select', 'class' => 'wcfm-select dps_state_to_select', 'label_class' => 'wcfm_title', 'options' => $state_options ),
																																																																											"dps_state_to_price" => array( 'label' => __('Cost', 'wc-frontend-manager') . '('.get_woocommerce_currency_symbol().')', 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title' ),
																																																									)	)		
																																									) )
																																							) ) );
								} else {
									if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
										wcfmu_feature_help_text_show( __( 'Dokan Pro Shipping Settings', 'wc-frontend-manager' ) );
									}
								}
								?>
							</div>
						</div>
					<?php } ?>
				
					<?php if( $wcfm_is_allow_vseo_settings = apply_filters( 'wcfm_is_allow_vseo_settings', true ) ) { ?>
						<div class="page_collapsible" id="wcfm_settings_form_seo_head">
							<label class="fa fa-globe"></label>
							<?php _e('SEO', 'wc-frontend-manager'); ?><span></span>
						</div>
						<div class="wcfm-container">
							<div id="wcfm_settings_form_shipping_expander" class="wcfm-content">
								<?php
								// Dokan Pro Settings
								if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_vendors_settings_fields_shipping', array(
																																								"dokan-seo-meta-title" => array('label' => __('SEO Title', 'dokan') , 'name' => 'store_seo[dokan-seo-meta-title]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dokan_seo_meta_title, 'hints' => __('SEO Title is shown as the title of your store page', 'dokan') ),
																																								"dokan-seo-meta-desc" => array('label' => __('Meta Description', 'dokan'), 'name' => 'store_seo[dokan-seo-meta-desc]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dokan_seo_meta_desc, 'hints' => __('The meta description is often shown as the black text under the title in a search result. For this to work it has to contain the keyword that was searched for and should be less than 156 chars.', 'dokan') ),
																																								"dokan-seo-meta-keywords" => array('label' => __('Meta Keywords', 'dokan'), 'name' => 'store_seo[dokan-seo-meta-keywords]', 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dokan_seo_meta_keywords, 'hints' => __('Insert some comma separated keywords for better ranking of your store page.', 'dokan') ),
																																								"dokan-seo-og-title" => array('label' => __('Facebook Title', 'dokan'), 'name' => 'store_seo[dokan-seo-og-title]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dokan_seo_og_title ),
																																								"dokan-seo-og-desc" => array('label' => __('Facebook Description', 'dokan'), 'name' => 'store_seo[dokan-seo-og-desc]', 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dokan_seo_og_desc ),
																																								"dokan-seo-og-image" => array('label' => __('Facebook Image', 'dokan'), 'name' => 'store_seo[dokan-seo-og-image]', 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dokan_seo_og_image_url ),
																																								"dokan-seo-twitter-title" => array('label' => __('Twitter Title', 'dokan'), 'name' => 'store_seo[dokan-seo-twitter-title]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dokan_seo_twitter_title ),
																																								"dokan-seo-twitter-desc" => array('label' => __('Twitter Description', 'dokan'), 'name' => 'store_seo[dokan-seo-twitter-desc]', 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dokan_seo_twitter_desc ),
																																								"dokan-seo-twitter-image" => array('label' => __('Twitter Image', 'dokan'), 'name' => 'store_seo[dokan-seo-twitter-image]', 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $dokan_seo_twitter_image_url ),
																																							 ) ) );
									
								} else {
									if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
										wcfmu_feature_help_text_show( __( 'Dokan Pro SEO Settings', 'wc-frontend-manager' ) );
									}
								}
								?>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
				<!-- end collapsible -->
			
			  <?php do_action( 'end_wcfm_dokan_settings', $user_id ); ?>
			  
			</div>
			
			<div id="wcfm_settings_submit" class="wcfm_form_simple_submit_wrapper">
			  <div class="wcfm-message" tabindex="-1"></div>
			  
				<input type="submit" name="save-data" value="<?php _e( 'Save', 'wc-frontend-manager' ); ?>" id="wcfm_settings_save_button" class="wcfm_submit_button" />
			</div>
			
		</form>
		<?php
		do_action( 'after_wcfm_dokan_settings' );
		?>
	</div>
</div>

<?php
$locations = explode( ',', $map_location );
$def_lat = isset( $locations[0] ) ? $locations[0] : 90.40714300000002;
$def_long = isset( $locations[1] ) ? $locations[1] : 23.709921;
?>
<script type="text/javascript">
	var selected_state = '<?php echo $state; ?>';
	var input_selected_state = '<?php echo $state; ?>';
	var def_zoomval = 12;
	var def_longval = '<?php echo $def_long; ?>';
	var def_latval = '<?php echo $def_lat; ?>';
</script>