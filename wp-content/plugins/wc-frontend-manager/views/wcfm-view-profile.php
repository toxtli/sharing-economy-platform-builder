<?php
/**
 * WCFM plugin view
 *
 * WCFM Profile View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   2.2.5
 */

global $WCFM;

if( !apply_filters( 'wcfm_is_pref_profile', true ) || !apply_filters( 'wcfm_is_allow_profile', true ) ) {
	wcfm_restriction_message_show( "Profile" );
	return;
}

$user_id = get_current_user_id();

$wp_user_avatar_id = get_user_meta( $user_id, 'wp_user_avatar', true );
$wp_user_avatar = wp_get_attachment_url( $wp_user_avatar_id );
if ( !$wp_user_avatar ) {
	$wp_user_avatar = '';
}

$first_name = get_user_meta( $user_id, 'first_name', true );
$last_name  = get_user_meta( $user_id, 'last_name', true );
//$email  = get_user_meta( $user_id, 'billing_email_address', true );
$phone  = get_user_meta( $user_id, 'billing_phone', true );
$about  = get_user_meta( $user_id, 'description', true );
$locale = get_user_meta( $user_id, 'locale', true );

$rich_editor = apply_filters( 'wcfm_is_allow_rich_editor', 'rich_editor' );
if( !$rich_editor ) {
	$breaks = array("<br />","<br>","<br/>"); 
	
	$about = str_ireplace( $breaks, "\r\n", $about );
	$about = strip_tags( $about );
}

if( $wcfm_is_allow_address_profile = apply_filters( 'wcfm_is_allow_address_profile', true ) ) {
	$bfirst_name = get_user_meta( $user_id, 'billing_first_name', true );
	$blast_name  = get_user_meta( $user_id, 'billing_last_name', true );
	$baddr_1  = get_user_meta( $user_id, 'billing_address_1', true );
	$baddr_2  = get_user_meta( $user_id, 'billing_address_2', true );
	$bcountry  = get_user_meta( $user_id, 'billing_country', true );
	$bcity  = get_user_meta( $user_id, 'billing_city', true );
	$bstate  = get_user_meta( $user_id, 'billing_state', true );
	$bzip  = get_user_meta( $user_id, 'billing_postcode', true );
	
	$sfirst_name = get_user_meta( $user_id, 'shipping_first_name', true );
	$slast_name  = get_user_meta( $user_id, 'shipping_last_name', true );
	$saddr_1  = get_user_meta( $user_id, 'shipping_address_1', true );
	$saddr_2  = get_user_meta( $user_id, 'shipping_address_2', true );
	$scountry  = get_user_meta( $user_id, 'shipping_country', true );
	$scity  = get_user_meta( $user_id, 'shipping_city', true );
	$sstate  = get_user_meta( $user_id, 'shipping_state', true );
	$szip  = get_user_meta( $user_id, 'shipping_postcode', true );
}


$is_marketplace = wcfm_is_marketplace();

if( $wcfm_is_allow_social_profile = apply_filters( 'wcfm_is_allow_social_profile', true ) ) {
	if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
		if( wcfm_is_vendor() ) {
			if( $is_marketplace == 'wcvendors' )  {
				$twitter = get_user_meta( $user_id, '_wcv_twitter_username', true );
				$facebook = get_user_meta( $user_id, '_wcv_facebook_url', true );
				$instagram = get_user_meta( $user_id, '_wcv_instagram_username', true );
				$youtube = get_user_meta( $user_id, '_wcv_youtube_url', true );
				$linkdin = get_user_meta( $user_id, '_wcv_linkedin_url', true );
				$google_plus = get_user_meta( $user_id, '_wcv_googleplus_url', true );
				$snapchat = get_user_meta( $user_id, '_wcv_snapchat_username', true );
				$pinterest = get_user_meta( $user_id, '_wcv_pinterest_url', true );
			} elseif( $is_marketplace == 'wcmarketplace' )  {
				$twitter = get_user_meta( $user_id, '_vendor_twitter_profile', true );
				$facebook = get_user_meta( $user_id, '_vendor_fb_profile', true );
				$instagram = get_user_meta( $user_id, '_vendor_instagram', true );
				$youtube = get_user_meta( $user_id, '_vendor_youtube', true );
				$linkdin = get_user_meta( $user_id, '_vendor_linkdin_profile', true );
				$google_plus = get_user_meta( $user_id, '_vendor_google_plus_profile', true );
				$snapchat = get_user_meta( $user_id, '_vendor_snapchat', true );
				$pinterest = get_user_meta( $user_id, '_vendor_pinterest', true );
			} elseif( $is_marketplace == 'dokan' )  {
				$vendor_data = get_user_meta( $user_id, 'dokan_profile_settings', true );
				$social_fields = isset( $vendor_data['social'] ) ? $vendor_data['social'] : array();
				$twitter = isset( $social_fields['twitter'] ) ? $social_fields['twitter'] : '';
				$facebook = isset( $social_fields['fb'] ) ? $social_fields['fb'] : '';
				$instagram = isset( $social_fields['instagram'] ) ? $social_fields['instagram'] : '';
				$youtube = isset( $social_fields['youtube'] ) ? $social_fields['youtube'] : '';
				$linkdin = isset( $social_fields['linkedin'] ) ? $social_fields['linkedin'] : '';
				$google_plus = isset( $social_fields['gplus'] ) ? $social_fields['gplus'] : '';
				$snapchat = isset( $social_fields['snapchat'] ) ? $social_fields['snapchat'] : '';
				$flickr = isset( $social_fields['flickr'] ) ? $social_fields['flickr'] : '';
				$pinterest = isset( $social_fields['pinterest'] ) ? $social_fields['pinterest'] : '';
			} else {	
				$twitter = get_user_meta( $user_id, '_twitter_profile', true );
				$facebook = get_user_meta( $user_id, '_fb_profile', true );
				$instagram = get_user_meta( $user_id, '_instagram', true );
				$youtube = get_user_meta( $user_id, '_youtube', true );
				$linkdin = get_user_meta( $user_id, '_linkdin_profile', true );
				$google_plus = get_user_meta( $user_id, '_google_plus_profile', true );
				$snapchat = get_user_meta( $user_id, '_snapchat', true );
				$pinterest = get_user_meta( $user_id, '_pinterest', true );
			}
		} else {	
			$twitter = get_user_meta( $user_id, '_twitter_profile', true );
			$facebook = get_user_meta( $user_id, '_fb_profile', true );
			$instagram = get_user_meta( $user_id, '_instagram', true );
			$youtube = get_user_meta( $user_id, '_youtube', true );
			$linkdin = get_user_meta( $user_id, '_linkdin_profile', true );
			$google_plus = get_user_meta( $user_id, '_google_plus_profile', true );
			$snapchat = get_user_meta( $user_id, '_snapchat', true );
			$pinterest = get_user_meta( $user_id, '_pinterest', true );
		}
	}
}

?>

<div class="collapse wcfm-collapse" id="">
  <div class="wcfm-page-headig">
		<span class="fa fa-user-circle"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Profile', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('Profile Manager', 'wc-frontend-manager' ); ?></h2>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_wcvendors_profile' ); ?>
	  
		<form id="wcfm_profile_form" class="wcfm">
	
			<?php do_action( 'begin_wcfm_wcvendors_profile_form' ); ?>
			
			<div class="wcfm-tabWrap">
			
				<!-- collapsible -->
				<div class="page_collapsible" id="wcfm_profile_address_head">
					<label class="fa fa-user-o"></label>
					<?php _e('Personal', 'wc-frontend-manager'); ?><span></span>
				</div>
				<div class="wcfm-container">
					<div id="wcfm_profile_personal_expander" class="wcfm-content">
						<?php
							$rich_editor = apply_filters( 'wcfm_is_allow_rich_editor', 'rich_editor' );
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_profile_fields_billing', array(
																																																"wp_user_avatar" => array('label' => __('Avatar', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'prwidth' => 150, 'value' => $wp_user_avatar ),
																																																"first_name" => array('label' => __('First Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $first_name ),
																																																"last_name" => array('label' => __('Last Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $last_name ),
																																																//"email" => array('label' => __('Email', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $email ),
																																																"phone" => array('label' => __('Phone', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $phone ),
																																																) ) );
							
							// User Locale Support - 3.0.0
							require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
							$translations = wp_get_available_translations();
							$languages = get_available_languages();
							
							if ( $languages ) {
								if ( 'en_US' === $locale || 'en' === $locale ) {
									$locale = 'en';
								} elseif ( '' === $locale || ! in_array( $locale, $languages, true ) ) {
									$locale = 'site-default';
								}
								$list_laguages = array( 'site-default' => __( 'Site Default', 'wc-frontend-manager' ), 'en' => 'English (United States)' );
								foreach( $languages as $language ) {
									if( isset( $translations[$language] ) ) {
										$list_laguages[$language] = $translations[$language]['native_name'];
									}
								}
								
								$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																																		"locale" => array('label' => __('Language', 'wc-frontend-manager') , 'type' => 'select', 'options' => $list_laguages, 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $locale )
																																		) );
								
							}
							
							$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																																	"about" => array('label' => __('About', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele ' . $rich_editor, 'label_class' => 'wcfm_title', 'value' => $about ),
																																	) );
						?>
					</div>
				</div>
				<div class="wcfm_clearfix"></div>
				<!-- end collapsible -->
			
				<!-- collapsible -->
				<?php if( $wcfm_is_allow_address_profile = apply_filters( 'wcfm_is_allow_address_profile', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_profile_address_head">
						<label class="fa fa-address-card-o"></label>
						<?php _e('Address', 'wc-frontend-manager'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_profile_address_expander" class="wcfm-content">
							<div class="wcfm_profile_heading"><h3><?php _e( 'Billing', 'wc-frontend-manager' ); ?></h3></div>
							<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_profile_fields_billing', array(
																																																	"bfirst_name" => array('label' => __('First Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bfirst_name ),
																																																	"blast_name" => array('label' => __('Last Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $blast_name ),
																																																	"baddr_1" => array('label' => __('Address 1', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $baddr_1 ),
																																																	"baddr_2" => array('label' => __('Address 2', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $baddr_2 ),
																																																	"bcountry" => array('label' => __('Country', 'wc-frontend-manager') , 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'style' => 'width: 60%;' ), 'value' => $bcountry ),
																																																	"bcity" => array('label' => __('City/Town', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bcity ),
																																																	"bstate" => array('label' => __('State/County', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bstate ),
																																																	"bzip" => array('label' => __('Postcode/Zip', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bzip ),
																																																	) ) );
							?>
							
							<div class="wcfm_clearfix"></div>
							<div class="wcfm_profile_heading"><h3><?php _e( 'Shipping', 'wc-frontend-manager' ); ?></h3></div>
							<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_profile_fields_shipping', array(
																																																	"sfirst_name" => array('label' => __('First Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $sfirst_name ),
																																																	"slast_name" => array('label' => __('Last Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $slast_name ),
																																																	"saddr_1" => array('label' => __('Address 1', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $saddr_1 ),
																																																	"saddr_2" => array('label' => __('Address 2', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $saddr_2 ),
																																																	"scountry" => array('label' => __('Country', 'wc-frontend-manager') , 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'style' => 'width: 60%;' ), 'value' => $scountry ),
																																																	"scity" => array('label' => __('City/Town', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $scity ),
																																																	"sstate" => array('label' => __('State/County', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $sstate ),
																																																	"szip" => array('label' => __('Postcode/Zip', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $szip ),
																																																	) ) );
							?>
						</div>
					</div>
					<div class="wcfm_clearfix"></div>
				<?php } ?>
				<!-- end collapsible -->
				
				<!-- collapsible -->
				<?php if( $wcfm_is_allow_social_profile = apply_filters( 'wcfm_is_allow_social_profile', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_profile_form_social_head">
						<label class="fa fa-users"></label>
						<?php _e('Social', 'wc-frontend-manager'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_profile_form_social_expander" class="wcfm-content">
							<?php
								if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_profile_fields_social', array(  
																																								"twitter" => array('label' => __('Twitter', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $twitter ),
																																								"facebook" => array('label' => __('Facebook', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $facebook ),
																																								"instagram" => array('label' => __('Instagram', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $instagram ),
																																								"youtube" => array('label' => __('Youtube', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $youtube ),
																																								"linkdin" => array('label' => __('linkdin', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $linkdin ),
																																								"google_plus" => array('label' => __('Google Plus', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $google_plus ),
																																								"snapchat" => array('label' => __('Snapchat', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $snapchat ),
																																								"pinterest" => array('label' => __('Pinterest', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $pinterest ),
																																								) ) );
								} else {
									if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
										wcfmu_feature_help_text_show( __( 'Social Profile', 'wc-frontend-manager' ) );
									}
								}
							?>
						</div>
					</div>
					<div class="wcfm_clearfix"></div>
				<?php } ?>
				<!-- end collapsible -->
				
				<?php do_action( 'end_wcfm_user_profile', $user_id ); ?>
				
		  </div>
			
			<div id="wcfm_profile_submit" class="wcfm_form_simple_submit_wrapper">
			  <div class="wcfm-message" tabindex="-1"></div>
			  
				<input type="submit" name="save-data" value="<?php _e( 'Save', 'wc-frontend-manager' ); ?>" id="wcfmprofile_save_button" class="wcfm_submit_button" />
			</div>
			
		</form>
		<?php
		do_action( 'after_wcfm_wcvendors_profile' );
		?>
	</div>
</div>