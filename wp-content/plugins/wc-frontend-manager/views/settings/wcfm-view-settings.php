<?php
/**
 * WCFM plugin view
 *
 * WCFM Settings View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   1.1.6
 */

global $WCFM;

$wcfm_is_allow_manage_settings = apply_filters( 'wcfm_is_allow_manage_settings', true );
if( !$wcfm_is_allow_manage_settings ) {
	wcfm_restriction_message_show( "Settings" );
	return;
}

$wcfm_options = (array) get_option( 'wcfm_options' );

$quick_access_image_url = isset( $wcfm_options['wcfm_quick_access_icon'] ) ? $wcfm_options['wcfm_quick_access_icon'] : $WCFM->plugin_url . '/assets/images/wcfm-30x30.png';
$is_quick_access_disabled = isset( $wcfm_options['quick_access_disabled'] ) ? $wcfm_options['quick_access_disabled'] : 'no';
$is_dashboard_logo_disabled = isset( $wcfm_options['dashboard_logo_disabled'] ) ? $wcfm_options['dashboard_logo_disabled'] : 'no';
$is_welcome_box_disabled = isset( $wcfm_options['welcome_box_disabled'] ) ? $wcfm_options['welcome_box_disabled'] : 'no';
$is_menu_disabled = isset( $wcfm_options['menu_disabled'] ) ? $wcfm_options['menu_disabled'] : 'no';
$is_dashboard_theme_header_disabled = isset( $wcfm_options['dashboard_theme_header_disabled'] ) ? $wcfm_options['dashboard_theme_header_disabled'] : 'no';
$is_dashboard_full_view_disabled = isset( $wcfm_options['dashboard_full_view_disabled'] ) ? $wcfm_options['dashboard_full_view_disabled'] : 'no';
$is_slick_menu_disabled = isset( $wcfm_options['slick_menu_disabled'] ) ? $wcfm_options['slick_menu_disabled'] : 'no';
$is_headpanel_disabled = isset( $wcfm_options['headpanel_disabled'] ) ? $wcfm_options['headpanel_disabled'] : 'no';
$is_float_button_disabled = isset( $wcfm_options['float_button_disabled'] ) ? $wcfm_options['float_button_disabled'] : 'no';
$is_checklist_view_disabled = isset( $wcfm_options['checklist_view_disabled'] ) ? $wcfm_options['checklist_view_disabled'] : 'no';
$ultimate_notice_disabled = isset( $wcfm_options['ultimate_notice_disabled'] ) ? $wcfm_options['ultimate_notice_disabled'] : 'no';
$noloader = isset( $wcfm_options['noloader'] ) ? $wcfm_options['noloader'] : 'no';
$logo = get_option( 'wcfm_site_logo' ) ? get_option( 'wcfm_site_logo' ) : '';
$logo_image_url = wp_get_attachment_url( $logo );

if ( !$logo_image_url ) {
	$logo_image_url = '';
}

$is_analytics_disabled = isset( $wcfm_options['analytics_disabled'] ) ? $wcfm_options['analytics_disabled'] : 'no';

$product_categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0&parent=0' );
$wcfm_product_type_categories = (array) get_option( 'wcfm_product_type_categories' );

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
	  
		<?php do_action( 'before_wcfm_settings' ); ?>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('WCfM Settings', 'wc-frontend-manager' ); ?></h2>
			
			<?php if( wcfm_is_booking() ) { ?>
				<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
					<a class="wcfm_gloabl_settings text_tip" href="<?php echo get_wcfm_bookings_settings_url(); ?>" data-tip="<?php _e( 'Bookings Global Settings', 'wc-frontend-manager' ); ?>"><span class="fa fa-cog"></span></a>
				<?php } else { ?>
					<a class="wcfm_gloabl_settings text_tip" href="#" onClick="return false;" data-tip="<?php wcfmu_feature_help_text_show( 'Bookings Global Settings', false, true ); ?>"><span class="fa fa-cog"></span></a>
				<?php } ?>
			<?php } ?>
			
			<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
				<?php if( WCFMu_Dependencies::wcfm_wc_appointments_active_check() ) { ?>
					<a class="wcfm_gloabl_settings text_tip" href="<?php echo get_wcfm_appointment_settings_url(); ?>" data-tip="<?php _e( 'Appointments Global Settings', 'wc-frontend-manager' ); ?>"><span class="fa fa-cog"></span></a>
				<?php } ?>
			<?php } ?>
			
			<?php 
			if( $wcfm_is_allow_capability_controller = apply_filters( 'wcfm_is_allow_capability_controller', true ) ) {
				echo '<a id="wcfm_capability_settings" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_capability_url().'" data-tip="' . __('Capability Controller', 'wc-frontend-manager') . '"><span class="fa fa-user-times"></span><span class="text">' . __( 'Capability', 'wc-frontend-manager') . '</span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
		<div class="wcfm_clearfix"></div><br />
		
		<form id="wcfm_settings_form" class="wcfm">
	
			<?php do_action( 'begin_wcfm_settings_form' ); ?>
			
			<div class="wcfm-tabWrap">
				<!-- collapsible -->
				<div class="page_collapsible" id="wcfm_settings_dashboard_head">
					<label class="fa fa-dashboard"></label>
					<?php _e('Dashboard', 'wc-frontend-manager'); ?><span></span>
				</div>
				<div class="wcfm-container">
					<div id="wcfm_settings_dashboard_expander" class="wcfm-content">
						<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_style', array(
																																																"wcfm_logo" => array('label' => __('Logo', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'prwidth' => 150, 'value' => $logo_image_url ),
																																																"wcfm_quick_access_icon" => array('label' => __('Quick access icon', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'prwidth' => 75, 'value' => $quick_access_image_url ),
																																																"quick_access_disabled" => array('label' => __('Disable Quick Access', 'wc-frontend-manager') , 'name' => 'quick_access_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_quick_access_disabled),
																																																"dashboard_logo_disabled" => array('label' => __('Disable Sidebar Logo', 'wc-frontend-manager') , 'name' => 'dashboard_logo_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_dashboard_logo_disabled),
																																																"welcome_box_disabled" => array('label' => __('Disable Welcome Box', 'wc-frontend-manager') , 'name' => 'welcome_box_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_welcome_box_disabled),
																																																"menu_disabled" => array('label' => __('Disable WCFM Menu', 'wc-frontend-manager') , 'name' => 'menu_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_menu_disabled),
																																																"dashboard_theme_header_disabled" => array('label' => __('Disable Theme Header', 'wc-frontend-manager') , 'name' => 'dashboard_theme_header_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_dashboard_theme_header_disabled),
																																																"dashboard_full_view_disabled" => array('label' => __('Disable WCFM Full View', 'wc-frontend-manager') , 'name' => 'dashboard_full_view_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_dashboard_full_view_disabled),
																																																"slick_menu_disabled" => array('label' => __('Disable WCFM Slick Menu', 'wc-frontend-manager') , 'name' => 'slick_menu_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_slick_menu_disabled),
																																																"headpanel_disabled" => array('label' => __('Disable WCFM Header Panel', 'wc-frontend-manager') , 'name' => 'headpanel_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_headpanel_disabled),
																																																"float_button_disabled" => array('label' => __('Disable Float Button', 'wc-frontend-manager') , 'name' => 'float_button_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_float_button_disabled),
																																																"checklist_view_disabled" => array('label' => __('Disable Category Checklist View', 'wc-frontend-manager') , 'name' => 'checklist_view_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_checklist_view_disabled, 'hints' => __( 'Disable this to have Product Manager Category/Custom Taxonomy Selector - Flat View.', 'wc-frontend-manager' ) ),
																																																"ultimate_notice_disabled" => array('label' => __('Disable Ultimate Notice', 'wc-frontend-manager') , 'name' => 'ultimate_notice_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $ultimate_notice_disabled),
																																																//"noloader" => array('label' => __('Disabled WCFM Loader', 'wc-frontend-manager') , 'name' => 'noloader','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $noloader),
																																																) ) );
						?>
					</div>
				</div>
				<div class="wcfm_clearfix"></div>
				<!-- end collapsible -->
			
				<!-- collapsible -->
				<div class="page_collapsible" id="wcfm_settings_form_analytics_head">
					<label class="fa fa-modx"></label>
					<?php _e('Modules', 'wc-frontend-manager'); ?><span></span>
				</div>
				<div class="wcfm-container">
					<div id="wcfm_settings_form_analytics_expander" class="wcfm-content">
						<div class="module_head_message"><?php _e( 'Configure what to hide from your dashboard', 'wc-frontend-manager' ); ?></div>
						<?php
							$wcfm_modules = $WCFM->get_wcfm_modules();
							$wcfm_module_options = isset( $wcfm_options['module_options'] ) ? $wcfm_options['module_options'] : array();
							$wcfm_module_options = apply_filters( 'wcfm_module_options', $wcfm_module_options );
							foreach( $wcfm_modules as $wcfm_module => $wcfm_module_data ) {
								$wcfm_module_value = isset( $wcfm_module_options[$wcfm_module] ) ? $wcfm_module_options[$wcfm_module] : 'no';
								$hints = '';
								if( isset( $wcfm_module_data['hints'] ) ) { $hints = $wcfm_module_data['hints']; }
								$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																																		$wcfm_module => array( 'label' => $wcfm_module_data['label'], 'name' => 'module_options[' . $wcfm_module . ']', 'type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title module_options_title', 'dfvalue' => $wcfm_module_value, 'hints' => $hints ),
																																		) );
								
								if( isset( $wcfm_module_data['notice'] ) ) {
									if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
										if( apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
											wcfmu_feature_help_text_show( $wcfm_module_data['label'] );
										}
									}
								}
							}
							
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_analytics', array(
																																																"analytics_disabled" => array('label' => __('Analytics', 'wc-frontend-manager') , 'name' => 'analytics_disabled','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title module_options_title', 'dfvalue' => $is_analytics_disabled),
																																																) ) );
							if( WCFM_Dependencies::wcfma_plugin_active_check() ) {
								do_action( 'wcfm_analytics_settings' );
							} else {
								if( $is_wcfma_inactive_notice_show = apply_filters( 'is_wcfma_inactive_notice_show', true ) ) {
									wcfma_feature_help_text_show( __( 'Analytics', 'wc-frontend-manager' ) );
								}
							}
						?>
					</div>
				</div>
				<div class="wcfm_clearfix"></div>
				<!-- end collapsible -->
			
				<!-- collapsible -->
				<div class="page_collapsible" id="wcfm_settings_form_style_head">
					<label class="fa fa-image"></label>
					<?php _e('Style', 'wc-frontend-manager'); ?><span></span>
				</div>
				<div class="wcfm-container">
					<div id="wcfm_settings_form_style_expander" class="wcfm-content">
						<?php
							$color_options = $WCFM->wcfm_color_setting_options();
							$color_options_array = array();
			
							foreach( $color_options as $color_option_key => $color_option ) {
								$color_options_array[$color_option['name']] = array( 'label' => $color_option['label'] , 'type' => 'colorpicker', 'class' => 'wcfm-text wcfm_ele colorpicker', 'label_class' => 'wcfm_title wcfm_ele', 'value' => ( isset($wcfm_options[$color_option['name']]) ) ? $wcfm_options[$color_option['name']] : $color_option['default'] );
							}
							$WCFM->wcfm_fields->wcfm_generate_form_field( $color_options_array );
						?>
						<div class="wcfm_clearfix"></div>
						<input type="submit" name="reset-color-settings" value="<?php _e( 'Reset to Default', 'wc-frontend-manager' ); ?>" id="wcfm_color_setting_reset_button" class="wcfm_submit_button" />
						<div class="wcfm_clearfix"></div>
					</div>
				</div>
				<div class="wcfm_clearfix"></div>
				<!-- end collapsible -->
			
				<!-- collapsible -->
				<div class="page_collapsible" id="wcfm_settings_form_pages_head">
					<label class="fa fa-newspaper-o"></label>
					<?php _e('WCFM Pages', 'wc-frontend-manager'); ?><span></span>
				</div>
				<div class="wcfm-container">
					<div id="wcfm_settings_form_pages_expander" class="wcfm-content">
						<?php
							$wcfm_page_options = get_option( 'wcfm_page_options' );
							$pages = get_pages(); 
							$pages_array = array();
							$woocommerce_pages = array ( wc_get_page_id('shop'), wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('myaccount'));
							foreach ( $pages as $page ) {
								if(!in_array($page->ID, $woocommerce_pages)) {
									$pages_array[$page->ID] = $page->post_title;
								}
							}
							
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_pages', array(
																																																"wc_frontend_manager_page_id" => array( 'label' => __('Dashboard', 'wc-frontend-manager'), 'type' => 'select', 'name' => 'wcfm_page_options[wc_frontend_manager_page_id]', 'options' => $pages_array, 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $wcfm_page_options['wc_frontend_manager_page_id'], 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'This page should have shortcode - wc_frontend_manager', 'wc-frontend-manager') )
																																																) ) );
						
							if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
								if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
									wcfmu_feature_help_text_show( __( 'WCFM Endpoints', 'wc-frontend-manager' ) );
								}
							} else {
								do_action( 'wcfm_settings_endpoints' );
							}
						?>
					</div>
				</div>
				<div class="wcfm_clearfix"></div>
				<!-- end collapsible -->
			
				<!-- collapsible -->
				<div class="page_collapsible" id="wcfm_settings_form_pages_head">
					<label class="fa fa-tags"></label>
					<?php _e('Product Type Categories', 'wc-frontend-manager'); ?><span></span>
				</div>
				<div class="wcfm-container">
					<div id="wcfm_settings_form_pages_expander" class="wcfm-content">
						<?php
							$product_types = apply_filters( 'wcfm_product_types', array( 'simple' => __( 'Simple Product', 'wc-frontend-manager' ), 'variable' => __( 'Variable Product', 'wc-frontend-manager' ), 'grouped' => __( 'Grouped Product', 'wc-frontend-manager' ), 'external' => __( 'External/Affiliate Product', 'wc-frontend-manager' ) ) );
							
							if( !empty( $product_types ) ) {
								foreach( $product_types as $product_type => $product_type_label ) {
									$product_type_categories = isset( $wcfm_product_type_categories[$product_type] ) ? $wcfm_product_type_categories[$product_type] : array();
								?>
								<p class="wcfm_title catlimit_title"><strong><?php echo $product_type_label . ' '; _e( 'Categories', 'wc-frontend-manager' ); ?></strong></p><label class="screen-reader-text" for="vendor_product_cats"><?php echo $product_type_label . ' '; _e( 'Categories', 'wc-frontend-manager' ); ?></label>
								<select id="wcfm_product_type_categories<?php echo $product_type; ?>" name="wcfm_product_type_categories[<?php echo $product_type; ?>][]" class="wcfm-select wcfm_ele wcfm_product_type_categories" multiple="multiple" data-catlimit="-1" style="width: 60%; margin-bottom: 10px;">
									<?php
										if ( $product_categories ) {
											$WCFM->library->generateTaxonomyHTML( 'product_cat', $product_categories, $product_type_categories, '', false, false, true );
										}
									?>
								</select>
								<?php
								}
							}
						?>
						<p class="description"><?php _e( 'Create group of your Store Categories as per Product Types. Product Manager will work according to that.', 'wc-frontend-manager' ); ?></p>
					</div>
				</div>
				<div class="wcfm_clearfix"></div>
				<!-- end collapsible -->
			
			  <?php do_action( 'end_wcfm_settings', $wcfm_options ); ?>
			</div>
			
			<div id="wcfm_settings_submit" class="wcfm_form_simple_submit_wrapper">
			  <div class="wcfm-message" tabindex="-1"></div>
			  
				<input type="submit" name="save-data" value="<?php _e( 'Save', 'wc-frontend-manager' ); ?>" id="wcfm_settings_save_button" class="wcfm_submit_button" />
			</div>
		</form>	
		<?php
		do_action( 'after_wcfm_settings' );
		?>
	</div>
</div>