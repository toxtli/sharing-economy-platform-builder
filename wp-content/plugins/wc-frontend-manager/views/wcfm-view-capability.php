<?php
/**
 * WCFM plugin view
 *
 * WCFM Capability Settings View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   2.4.7
 */

global $WCFM;

$wcfm_is_allow_capability_controller = apply_filters( 'wcfm_is_allow_capability_controller', true );
if( !$wcfm_is_allow_capability_controller ) {
	wcfm_restriction_message_show( "Capability" );
	return;
}

$wcfm_capability_options = (array) get_option( 'wcfm_capability_options' );

// Product Capabilities
$submit_products = ( isset( $wcfm_capability_options['submit_products'] ) ) ? $wcfm_capability_options['submit_products'] : 'no';
$add_products = ( isset( $wcfm_capability_options['add_products'] ) ) ? $wcfm_capability_options['add_products'] : 'no';
$publish_products = ( isset( $wcfm_capability_options['publish_products'] ) ) ? $wcfm_capability_options['publish_products'] : 'no';
$edit_live_products = ( isset( $wcfm_capability_options['edit_live_products'] ) ) ? $wcfm_capability_options['edit_live_products'] : 'no';
$publish_live_products = ( isset( $wcfm_capability_options['publish_live_products'] ) ) ? $wcfm_capability_options['publish_live_products'] : 'no';
$delete_products = ( isset( $wcfm_capability_options['delete_products'] ) ) ? $wcfm_capability_options['delete_products'] : 'no';

$simple = ( isset( $wcfm_capability_options['simple'] ) ) ? $wcfm_capability_options['simple'] : 'no';
$variable = ( isset( $wcfm_capability_options['variable'] ) ) ? $wcfm_capability_options['variable'] : 'no';
$grouped = ( isset( $wcfm_capability_options['grouped'] ) ) ? $wcfm_capability_options['grouped'] : 'no';
$external = ( isset( $wcfm_capability_options['external'] ) ) ? $wcfm_capability_options['external'] : 'no';

$inventory = ( isset( $wcfm_capability_options['inventory'] ) ) ? $wcfm_capability_options['inventory'] : 'no';
$shipping = ( isset( $wcfm_capability_options['shipping'] ) ) ? $wcfm_capability_options['shipping'] : 'no';
$taxes = ( isset( $wcfm_capability_options['taxes'] ) ) ? $wcfm_capability_options['taxes'] : 'no';
$linked = ( isset( $wcfm_capability_options['linked'] ) ) ? $wcfm_capability_options['linked'] : 'no';
$attributes = ( isset( $wcfm_capability_options['attributes'] ) ) ? $wcfm_capability_options['attributes'] : 'no';
$advanced = ( isset( $wcfm_capability_options['advanced'] ) ) ? $wcfm_capability_options['advanced'] : 'no';
$catalog = ( isset( $wcfm_capability_options['catalog'] ) ) ? $wcfm_capability_options['catalog'] : 'no';

// Miscellaneous Capabilities
$manage_booking = ( isset( $wcfm_capability_options['manage_booking'] ) ) ? $wcfm_capability_options['manage_booking'] : 'no';
$manage_appointment = ( isset( $wcfm_capability_options['manage_appointment'] ) ) ? $wcfm_capability_options['manage_appointment'] : 'no';
$manage_subscription = ( isset( $wcfm_capability_options['manage_subscription'] ) ) ? $wcfm_capability_options['manage_subscription'] : 'no';
$associate_listings = ( isset( $wcfm_capability_options['associate_listings'] ) ) ? $wcfm_capability_options['associate_listings'] : 'no';

$submit_coupons = ( isset( $wcfm_capability_options['submit_coupons'] ) ) ? $wcfm_capability_options['submit_coupons'] : 'no';
$publish_coupons = ( isset( $wcfm_capability_options['publish_coupons'] ) ) ? $wcfm_capability_options['publish_coupons'] : 'no';
$edit_live_coupons = ( isset( $wcfm_capability_options['edit_live_coupons'] ) ) ? $wcfm_capability_options['edit_live_coupons'] : 'no';
$delete_coupons = ( isset( $wcfm_capability_options['delete_coupons'] ) ) ? $wcfm_capability_options['delete_coupons'] : 'no';

$view_orders  = ( isset( $wcfm_capability_options['view_orders'] ) ) ? $wcfm_capability_options['view_orders'] : 'no';
$order_status_update  = ( isset( $wcfm_capability_options['order_status_update'] ) ) ? $wcfm_capability_options['order_status_update'] : 'no';
$view_order_details = ( isset( $wcfm_capability_options['view_order_details'] ) ) ? $wcfm_capability_options['view_order_details'] : 'no';
$view_billing_details = ( isset( $wcfm_capability_options['view_billing_details'] ) ) ? $wcfm_capability_options['view_billing_details'] : 'no';
$view_shipping_details =  ( isset( $wcfm_capability_options['view_shipping_details'] ) ) ? $wcfm_capability_options['view_shipping_details'] : 'no';
$view_email  = ( isset( $wcfm_capability_options['view_email'] ) ) ? $wcfm_capability_options['view_email'] : 'no';
$view_comments  = ( isset( $wcfm_capability_options['view_comments'] ) ) ? $wcfm_capability_options['view_comments'] : 'no';
$submit_comments  = ( isset( $wcfm_capability_options['submit_comments'] ) ) ? $wcfm_capability_options['submit_comments'] : 'no';
$export_csv  = ( isset( $wcfm_capability_options['export_csv'] ) ) ? $wcfm_capability_options['export_csv'] : 'no';
$pdf_invoice = ( isset( $wcfm_capability_options['pdf_invoice'] ) ) ? $wcfm_capability_options['pdf_invoice'] : 'no';
$pdf_packing_slip = ( isset( $wcfm_capability_options['pdf_packing_slip'] ) ) ? $wcfm_capability_options['pdf_packing_slip'] : 'no';

$view_reports  = ( isset( $wcfm_capability_options['view_reports'] ) ) ? $wcfm_capability_options['view_reports'] : 'no';

$vnd_wpadmin = ( isset( $wcfm_capability_options['vnd_wpadmin'] ) ) ? $wcfm_capability_options['vnd_wpadmin'] : 'no';

$is_marketplace = wcfm_is_marketplace();

?>

<div class="collapse wcfm-collapse" id="">
  <div class="wcfm-page-headig">
		<span class="fa fa-user-times"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Capability Controller', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('Capability Settings', 'wc-frontend-manager' ); ?></h2>
			
			<?php 
			echo '<a id="wcfm_settings" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_settings_url().'" data-tip="' . __('Dashboard Settings', 'wc-frontend-manager') . '"><span class="fa fa-cogs"></span><span class="text">' . __( 'Settings', 'wc-frontend-manager') . '</span></a>';
			?>
			<div class="wcfm_clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_capability' ); ?>
		
		<form id="wcfm_capability_form" class="wcfm">
	
			<?php do_action( 'begin_wcfm_capability_form' ); ?>
			
			<?php if( $is_marketplace ) { ?>
				<!-- collapsible -->
				<div class="page_collapsible" id="wcfm_capability_form_vendor_head">
					<label class="fa fa-user fa-user-o"></label>
					<?php _e('Vendors Capability', 'wc-frontend-manager'); ?>
					<span></span>
				</div>                                                                            
				<div class="wcfm-container">
					<div id="wcfm_settings_form_vendor_expander" class="wcfm-content">
						<div class="capability_head_message"><?php _e( "Configure what to hide from all Vendors", 'wc-frontend-manager' ); ?></div>
					
						<div class="vendor_capability">
							
							<div class="vendor_product_capability">
								<div class="vendor_capability_heading"><h3><?php _e( 'Products', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_products', array("submit_products" => array('label' => __('Manage Products', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[submit_products]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $submit_products),
																																																													 "add_products" => array('label' => __('Add Products', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[add_products]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $add_products),
																																																													 "publish_products" => array('label' => __('Publish Products', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[publish_products]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $publish_products),
																																																													 "edit_live_products" => array('label' => __('Edit Live Products', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[edit_live_products]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $edit_live_products),
																																																													 "publish_live_products" => array('label' => __('Auto Publish Live Products', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[publish_live_products]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $publish_live_products),
																																																													 "delete_products" => array('label' => __('Delete Products', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[delete_products]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $delete_products)
																													) ) );
								?>
								
								<div class="wcfm_clearfix"></div>
								<div class="vendor_capability_sub_heading"><h3><?php _e( 'Types', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_product_types', array("simple" => array('label' => __('Simple', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[simple]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $simple),
																																																																"variable" => array('label' => __('Variable', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[variable]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $variable),
																																																																"grouped" => array('label' => __('Grouped', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[grouped]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $grouped),
																																																																"external" => array('label' => __('External / Affiliate', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[external]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $external),
																													), 'wcfm_capability_options', $wcfm_capability_options ) );
								?>
								
								<div class="wcfm_clearfix"></div>
								<div class="vendor_capability_sub_heading"><h3><?php _e( 'Panels', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_product_panels', array("inventory" => array('label' => __('Inventory', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[inventory]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $inventory),
																																																																 "shipping" => array('label' => __('Shipping', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[shipping]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $shipping),
																																																																 "taxes" => array('label' => __('Taxes', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[taxes]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $taxes),
																																																																 "linked" => array('label' => __('Linked', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[linked]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $linked),
																																																																 "attributes" => array('label' => __('Attributes', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[attributes]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $attributes),
																																																																 "advanced" => array('label' => __('Advanced', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[advanced]', 'type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $advanced),
																																																																 "catalog" => array('label' => __('Catalog', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[catalog]', 'type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $catalog),
																													) ) );
								
								do_action( 'wcfm_capability_settings_product', $wcfm_capability_options );
								?>
							</div>
							
							<div class="vendor_other_capability">
								<div class="vendor_capability_heading"><h3><?php _e( 'Miscellaneous', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								if( wcfm_is_booking() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_booking', array(  "manage_booking" => array('label' => __('Manage Bookings', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[manage_booking]', 'type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $manage_booking),
																															) ) );
								} else {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_booking', array(  "manage_booking" => array('label' => __('Manage Bookings', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[manage_booking]', 'type' => 'checkboxoffon', 'custom_tags' => array( 'disabled' => 'disabled' ), 'desc' => __( 'Install WC Bookings to enable this feature.', 'wc-frontend-manager' ), 'class' => 'wcfm-checkbox wcfm-checkbox-disabled wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $manage_booking),
																															) ) );
								}
								
								if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
									if( WCFMu_Dependencies::wcfm_wc_appointments_active_check() ) {
										$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_appointment', array(  "manage_appointment" => array('label' => __('Manage Appointments', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[manage_appointment]', 'type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $manage_appointment),
																																) ) );
									} else {
										$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_appointment', array(  "manage_appointment" => array('label' => __('Manage Appointments', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[manage_appointment]', 'type' => 'checkboxoffon', 'custom_tags' => array( 'disabled' => 'disabled' ), 'desc' => __( 'Install WC Appointments to enable this feature.', 'wc-frontend-manager' ), 'class' => 'wcfm-checkbox wcfm-checkbox-disabled wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $manage_appointment),
																																) ) );
									}
								}
								
								if( wcfm_is_subscription() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_subscription', array(  "manage_subscription" => array('label' => __('Manage Subscriptions', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[manage_subscription]', 'type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $manage_subscription),
																															) ) );
								} else {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_subscription', array(  "manage_subscription" => array('label' => __('Manage Subscriptions', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[manage_subscription]', 'type' => 'checkboxoffon', 'custom_tags' => array( 'disabled' => 'disabled' ), 'desc' => __( 'Install WC Subscriptions to enable this feature.', 'wc-frontend-manager' ), 'class' => 'wcfm-checkbox wcfm-checkbox-disabled wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $manage_subscription),
																															) ) );
								}
								
								if( WCFM_Dependencies::wcfm_wp_job_manager_plugin_active_check() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_listings', array(  "associate_listings" => array('label' => __('Associate Listings', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[associate_listings]', 'type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'desc' => __( 'by WP Job Manager.', 'wc-frontend-manager' ), 'dfvalue' => $associate_listings),
																															) ) );
								} else {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_listings', array(  "associate_listings" => array('label' => __('Associate Listings', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[associate_listings]', 'type' => 'checkboxoffon', 'custom_tags' => array( 'disabled' => 'disabled' ), 'desc' => __( 'Install WP Job Manager to enable this feature.', 'wc-frontend-manager' ), 'class' => 'wcfm-checkbox wcfm-checkbox-disabled wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $associate_listings),
																															) ) );
								}
								?>
								
								<div class="wcfm_clearfix"></div>
								<div class="vendor_capability_sub_heading"><h3><?php _e( 'Coupons', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_coupons', array("submit_coupons" => array('label' => __('Submit Coupons', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[submit_coupons]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $submit_coupons),
																																																													 "publish_coupons" => array('label' => __('Publish Coupons', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[publish_coupons]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $publish_coupons),
																																																													 "edit_live_coupons" => array('label' => __('Edit Live Coupons', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[edit_live_coupons]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $edit_live_coupons),
																																																													 "delete_coupons" => array('label' => __('Delete Coupons', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[delete_coupons]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $delete_coupons)
																													) ) );
								?>
								
								<div class="wcfm_clearfix"></div>
								<div class="vendor_capability_sub_heading"><h3><?php _e( 'Orders', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_orders', array(  "view_orders" => array('label' => __('View Orders', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[view_orders]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $view_orders),
																																																													 "order_status_update" => array('label' => __('Status Update', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[order_status_update]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $order_status_update),
																																																													 "view_order_details" => array('label' => __('View Details', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[view_order_details]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $view_order_details),
																																																													 "view_billing_details" => array('label' => __('Billing Address', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[view_billing_details]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $view_billing_details),
																																																													 "view_shipping_details" => array('label' => __('Shipping Address', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[view_shipping_details]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $view_shipping_details),
																																																													 "view_email" => array('label' => __('Customer Email', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[view_email]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $view_email),
																																																													 "view_comments" => array('label' => __('View Comments', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[view_comments]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $view_comments),
																																																													 "submit_comments" => array('label' => __('Submit Comments', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[submit_comments]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $submit_comments),
																																																													 "export_csv" => array('label' => __('Export CSV', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[export_csv]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $export_csv),
																														 ) ) );
								if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_invoice', array(  
																																							 "pdf_invoice" => array('label' => __('PDF Invoice', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[pdf_invoice]', 'type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $pdf_invoice),
																																							 "pdf_packing_slip" => array('label' => __('PDF Packing Slip', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[pdf_packing_slip]', 'type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $pdf_packing_slip),
																																) ) );
								} else {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_invoice', array(
																																						 "pdf_invoice" => array('label' => __('PDF Invoice', 'wc-frontend-manager'), 'name' => 'wcfm_capability_options[pdf_invoice]', 'type' => 'checkboxoffon', 'custom_tags' => array( 'disabled' => 'disabled' ), 'desc' => __( 'Install WCFM Ultimate to enable this feature.', 'wc-frontend-manager' ), 'class' => 'wcfm-checkbox wcfm-checkbox-disabled wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $pdf_invoice),
																															) ) );
								}
								?>
								
								<div class="wcfm_clearfix"></div>
								<div class="vendor_capability_sub_heading"><h3><?php _e( 'Reports', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_reports', array("view_reports" => array('label' => __('View Reports', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[view_reports]', 'type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $view_reports),
																														 ) ) );
								?>
								
								<div class="wcfm_clearfix"></div>
								<div class="vendor_capability_sub_heading"><h3><?php _e( 'Access', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_access', array(  
																																							 "vnd_wpadmin" => array('label' => __('Backend Access', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[vnd_wpadmin]', 'type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $vnd_wpadmin),
																																) ) );
								} else {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_access', array(
																																						 "vnd_wpadmin" => array('label' => __('Backend Access', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[vnd_wpadmin]', 'type' => 'checkboxoffon', 'custom_tags' => array( 'disabled' => 'disabled' ), 'desc' => __( 'Install WCFM Ultimate to enable this feature.', 'wc-frontend-manager' ), 'class' => 'wcfm-checkbox wcfm-checkbox-disabled wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $vnd_wpadmin),
																															) ) );
								}
								do_action( 'wcfm_capability_settings_miscellaneous', $wcfm_capability_options );
								?>
							</div>
						</div>
						
						<div class="vendor_advanced_capability">
							<?php
							if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
								if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
									wcfmu_feature_help_text_show( __( 'Advanced Capability', 'wc-frontend-manager' ) );
								}
							} else {
								do_action( 'wcfm_settings_capability', $wcfm_capability_options );
							}
							?>
						</div>
					</div>
				</div>
				<div class="wcfm_clearfix"></div><br />
				<!-- end collapsible -->
			<?php } ?>
			
			
			<!-- collapsible -->
			<div class="page_collapsible" id="wcfm_capability_form_shop_manager_head">
				<label class="fa fa-user-secret"></label>
				<?php _e('Shop Managers Capability', 'wc-frontend-manager'); ?>
				<span></span>
			</div>                                                                            
			<div class="wcfm-container">
				<div id="wcfm_settings_form_shop_manager_expander" class="wcfm-content">
				  <?php
					if( WCFM_Dependencies::wcfmgs_plugin_active_check() ) {
						do_action( 'wcfm_shop_manager_capability_settings' );
					} else {
						if( $is_wcfmgs_inactive_notice_show = apply_filters( 'is_wcfmgs_inactive_notice_show', true ) ) {
							wcfmgs_feature_help_text_show( __( 'Shop Managers Capability', 'wc-frontend-manager' ) );
						}
					}
					?>
				</div>
			</div>
			<div class="wcfm_clearfix"></div><br />
			<!-- end collapsible -->
			
			<!-- collapsible -->
			<div class="page_collapsible" id="wcfm_capability_form_shop_staff_head">
				<label class="fa fa-user"></label>
				<?php _e('Shop Staffs Capability', 'wc-frontend-manager'); ?>
				<span></span>
			</div>                                                                            
			<div class="wcfm-container">
				<div id="wcfm_settings_form_shop_staff_expander" class="wcfm-content">
				  <?php
					if( WCFM_Dependencies::wcfmgs_plugin_active_check() ) {
						do_action( 'wcfm_shop_staff_capability_settings' );
					} else {
						if( $is_wcfmgs_inactive_notice_show = apply_filters( 'is_wcfmgs_inactive_notice_show', true ) ) {
							wcfmgs_feature_help_text_show( __( 'Shop Staffs Capability', 'wc-frontend-manager' ) );
						}
					}
					?>
				</div>
			</div>
			<?php
			if( WCFM_Dependencies::wcfmgs_plugin_active_check() && $is_marketplace && ( $is_marketplace == 'wcpvendors' ) ) {
				?>
				<div style="color: #00897b;"><?php _e( '*** Vendor Managers are treated as Shop Staff for a Vendor Store.', 'wc-frontend-manager' ); ?></div>
				<?php
			}
			?>
			<div class="wcfm_clearfix"></div><br />
			<!-- end collapsible -->
			
			<?php do_action( 'end_wcfm_capability', $wcfm_options ); ?>
			
			<div id="wcfm_capability_submit" class="wcfm_form_simple_submit_wrapper">
			  <div class="wcfm-message" tabindex="-1"></div>
			  
				<input type="submit" name="save-data" value="<?php _e( 'Save', 'wc-frontend-manager' ); ?>" id="wcfm_capability_save_button" class="wcfm_submit_button" />
			</div>
			
			<?php do_action( 'end_wcfm_capability_form' ); ?>
		</form>	
		<?php
		do_action( 'after_wcfm_capability' );
		?>
	</div>
</div>