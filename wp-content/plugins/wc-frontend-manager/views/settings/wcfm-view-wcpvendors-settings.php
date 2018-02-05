<?php
/**
 * WCFM plugin view
 *
 * WCFM WC Product Vendors Settings View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   2.1.1
 */

global $WCFM;

$wcfm_is_allow_manage_settings = apply_filters( 'wcfm_is_allow_manage_settings', true );
if( !$wcfm_is_allow_manage_settings ) {
	wcfm_restriction_message_show( "Settings" );
	return;
}

$vendor_data = WC_Product_Vendors_Utils::get_vendor_data_from_user();

// logo image
$logo = ! empty( $vendor_data['logo'] ) ? $vendor_data['logo'] : '';

$logo_image_url = wp_get_attachment_image_src( $logo, 'full' );

if ( !empty( $logo_image_url ) ) {
	$logo_image_url = $logo_image_url[0];
}

$vendor_term = get_term( WC_Product_Vendors_Utils::get_logged_in_vendor(), WC_PRODUCT_VENDORS_TAXONOMY );

$shop_name         = ! empty( $vendor_data['shop_name'] ) ? $vendor_data['shop_name'] : $vendor_term->name;
$profile           = ! empty( $vendor_data['profile'] ) ? $vendor_data['profile'] : '';
$email             = ! empty( $vendor_data['email'] ) ? $vendor_data['email'] : '';
$paypal            = ! empty( $vendor_data['paypal'] ) ? $vendor_data['paypal'] : '';
$vendor_commission = ! empty( $vendor_data['commission'] ) ? $vendor_data['commission'] : get_option( 'wcpv_vendor_settings_default_commission', '0' );
$tzstring          = ! empty( $vendor_data['timezone'] ) ? $vendor_data['timezone'] : '';
$wcfm_vacation_mode = isset( $vendor_data['wcfm_vacation_mode'] ) ? $vendor_data['wcfm_vacation_mode'] : 'no';
$wcfm_disable_vacation_purchase = isset( $vendor_data['wcfm_disable_vacation_purchase'] ) ? $vendor_data['wcfm_disable_vacation_purchase'] : 'no';
$wcfm_vacation_mode_msg = ! empty( $vendor_data['wcfm_vacation_mode_msg'] ) ? $vendor_data['wcfm_vacation_mode_msg'] : '';

$rich_editor = apply_filters( 'wcfm_is_allow_rich_editor', 'rich_editor' );
if( !$rich_editor ) {
	$breaks = array("<br />","<br>","<br/>"); 
	
	$profile = str_ireplace( $breaks, "\r\n", $profile );
	$profile = strip_tags( $profile );
}

$user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );

if ( empty( $tzstring ) ) {
	$tzstring = WC_Product_Vendors_Utils::get_default_timezone_string();
}

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
	  
		<?php do_action( 'before_wcfm_wcpvendors_settings' ); ?>
		
		<form id="wcfm_settings_form" class="wcfm">
	
			<?php do_action( 'begin_wcfm_wcpvendors_settings_form' ); ?>
			
			<div class="wcfm-tabWrap">
				<!-- collapsible -->
				<div class="page_collapsible" id="wcfm_settings_dashboard_head">
					<label class="fa fa-shopping-bag"></label>
				  <?php _e('Store Settings', 'wc-frontend-manager'); ?><span></span>
				</div>
				<div class="wcfm-container">
					<div id="wcfm_settings_form_style_expander" class="wcfm-content">
						<?php
						  $rich_editor = apply_filters( 'wcfm_is_allow_rich_editor', 'rich_editor' );
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcpvendors_settings_fields_store', array(
																																																"wcfm_logo" => array('label' => __('Logo', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'prwidth' => 150, 'value' => $logo_image_url),
																																																"shop_name" => array('label' => __('Shop Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $shop_name, 'hints' => __( 'Your shop name is public and must be unique.', 'wc-frontend-manager' ) ),
																																																"email" => array('label' => __('Vendor Email', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $email, 'hints' => __( 'Enter the email for this vendor. This is the email where all notifications will be send such as new orders and customer inquiries. You may enter more than one email separating each with a comma.', 'wc-frontend-manager' ) ),
																																																"shop_description" => array('label' => __('Profile', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele ' . $rich_editor, 'label_class' => 'wcfm_title', 'value' => $profile, 'hints' => __( 'Enter the profile information you would like for customer to see.', 'wc-frontend-manager' ) ),
																																																) ) );
							
						?>
						<br />
						<p class="tzstring wcfm_title wcfm_ele"><strong><?php _e('Timezone', 'wc-frontend-manager'); ?></strong><span class="img_tip" data-tip="<?php _e('Set the local timezone.', 'wc-frontend-manager'); ?>" data-hasqtip="4"></span></p>
						<label class="screen-reader-text" for="tzstring"><?php _e('Timezone', 'wc-frontend-manager'); ?></label>
						<select id="timezone" name="timezone" class="wcfm-select wcfm_ele" style="width: 60%;">
							<?php echo wp_timezone_choice( $tzstring ); ?>
						</select>
					</div>
				</div>
				<div class="wcfm_clearfix"></div>
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
																																																													"wcfm_vacation_mode" => array('label' => __('Enable Vacation Mode', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vacation_mode ),
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
				<?php if( $wcfm_is_allow_billing_settings = apply_filters( 'wcfm_is_allow_billing_settings', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_settings_form_payment_head">
						<label class="fa fa-money"></label>
						<?php _e('Payment', 'wc-frontend-manager'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_settings_form_payment_expander" class="wcfm-content">
							<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcpvendors_settings_fields_billing', array(
																																															"paypal" => array('label' => __('Paypal Email', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $paypal, 'hints' => __( 'PayPal email account where you will receive your commission.', 'wc-frontend-manager' ) ),
																																															"vendor_commission" => array('label' => __('Commission', 'wc-frontend-manager') , 'type' => 'text', 'attributes' => array( 'disabled' => 'disabled' ), 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_commission, 'hints' => __( 'Default commission you will receive per product sale. Please note product level commission can override this. Check your product to confirm.', 'wc-frontend-manager' ) ),
																																															) ) );
							?>
						</div>
					</div>
					<div class="wcfm_clearfix"></div>
				<?php } ?>
				<!-- end collapsible -->
				
		    <?php do_action( 'end_wcfm_wcpvendors_settings', $vendor_data ); ?>
		  </div>
			
			<div id="wcfm_settings_submit" class="wcfm_form_simple_submit_wrapper">
			  <div class="wcfm-message" tabindex="-1"></div>
			  
				<input type="submit" name="save-data" value="<?php _e( 'Save', 'wc-frontend-manager' ); ?>" id="wcfm_settings_save_button" class="wcfm_submit_button" />
			</div>
			
		
		</form>
		<?php
		do_action( 'after_wcfm_wcpvendors_settings' );
		?>
	</div>
</div>