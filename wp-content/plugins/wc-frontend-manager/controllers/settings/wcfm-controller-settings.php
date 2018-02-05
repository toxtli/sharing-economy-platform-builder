<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Setings Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.1.6
 */

class WCFM_Settings_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$wcfm_settings_form_data = array();
	  parse_str($_POST['wcfm_settings_form'], $wcfm_settings_form);
	  
	  $options = get_option( 'wcfm_options' );
	  
	  // Quick Access Disabled
	  if( !isset($wcfm_settings_form['quick_access_disabled']) ) $options['quick_access_disabled'] = 'no';
	  else $options['quick_access_disabled'] = 'yes';
	  
	  // Dashboard Logo Disabled
	  if( !isset($wcfm_settings_form['dashboard_logo_disabled']) ) $options['dashboard_logo_disabled'] = 'no';
	  else $options['dashboard_logo_disabled'] = 'yes';
	  
	  // Welcome Box Disabled
	  if( !isset($wcfm_settings_form['welcome_box_disabled']) ) $options['welcome_box_disabled'] = 'no';
	  else $options['welcome_box_disabled'] = 'yes';
	  
	  // Menu Disabled
	  if( !isset($wcfm_settings_form['menu_disabled']) ) $options['menu_disabled'] = 'no';
	  else $options['menu_disabled'] = 'yes';
	  
	  // Theme Header Disabled
	  if( !isset($wcfm_settings_form['dashboard_theme_header_disabled']) ) $options['dashboard_theme_header_disabled'] = 'no';
	  else $options['dashboard_theme_header_disabled'] = 'yes';
	  
	  // Full View Disabled
	  if( !isset($wcfm_settings_form['dashboard_full_view_disabled']) ) $options['dashboard_full_view_disabled'] = 'no';
	  else $options['dashboard_full_view_disabled'] = 'yes';
	  
	  // Slick Menu Disabled
	  if( !isset($wcfm_settings_form['slick_menu_disabled']) ) $options['slick_menu_disabled'] = 'no';
	  else $options['slick_menu_disabled'] = 'yes';
	  
	  // Float Button Disabled
	  if( !isset($wcfm_settings_form['float_button_disabled']) ) $options['float_button_disabled'] = 'no';
	  else $options['float_button_disabled'] = 'yes';
	  
	  // Header Panel Disabled
	  if( !isset($wcfm_settings_form['headpanel_disabled']) ) $options['headpanel_disabled'] = 'no';
	  else $options['headpanel_disabled'] = 'yes';
	  
	  // Taxonomy Checklist vew Disabled
	  if( !isset($wcfm_settings_form['checklist_view_disabled']) ) $options['checklist_view_disabled'] = 'no';
	  else $options['checklist_view_disabled'] = 'yes';
	  
	  // Hover sub-menu vew Disabled
	  if( !isset($wcfm_settings_form['hover_submenu_disabled']) ) $options['hover_submenu_disabled'] = 'no';
	  else $options['hover_submenu_disabled'] = 'yes';
	  
	  // Ultimate Notice Disabled
	  if( !isset($wcfm_settings_form['ultimate_notice_disabled']) ) $options['ultimate_notice_disabled'] = 'no';
	  else $options['ultimate_notice_disabled'] = 'yes';
	  
	  // Loader Disabled
	  if( !isset($wcfm_settings_form['noloader']) ) $options['noloader'] = 'no';
	  else $options['noloader'] = 'yes';
	  
	  // Set Site Logo
		if(isset($wcfm_settings_form['wcfm_logo']) && !empty($wcfm_settings_form['wcfm_logo'])) {
			$options['site_logo'] = $WCFM->wcfm_get_attachment_id($wcfm_settings_form['wcfm_logo']);
			update_option( 'wcfm_site_logo', $options['site_logo'] );
		} else {
			update_option( 'wcfm_site_logo', '' );
		}
		
		// Quick Access Icon
		if(isset($wcfm_settings_form['wcfm_quick_access_icon']) && !empty($wcfm_settings_form['wcfm_quick_access_icon'])) {
			$options['wcfm_quick_access_icon'] = $wcfm_settings_form['wcfm_quick_access_icon'];
		} else {
			$options['wcfm_quick_access_icon'] = $WCFM->plugin_url . '/assets/images/wcfm-30x30.png'; 
		}
		
		// Module Options
		if( isset($wcfm_settings_form['module_options']) ) {
			$options['module_options'] = $wcfm_settings_form['module_options'];
		} else {
			$options['module_options'] = array();
		}
		/*$wcfm_modules = $WCFM->get_wcfm_modules();
		foreach( $wcfm_modules as $wcfm_module => $wcfm_module_data ) {
			if( isset( $wcfm_settings_form['module_options'] ) && isset( $wcfm_settings_form['module_options'][$wcfm_module] ) ) {
				$options[$wcfm_module] = 'yes';
			} else {
				$options[$wcfm_module] = 'no';
			}
		}*/
		
		// Analytics Disabled
	  if( !isset($wcfm_settings_form['analytics_disabled']) ) $options['analytics_disabled'] = 'no';
	  else $options['analytics_disabled'] = 'yes';
	  
	  $color_options = $WCFM->wcfm_color_setting_options();
		foreach( $color_options as $color_option_key => $color_option ) {
			if( isset( $wcfm_settings_form[ $color_option['name'] ] ) ) { $options[$color_option['name']] = $wcfm_settings_form[ $color_option['name'] ]; } else { $options[$color_option['name']] = $color_option['default']; }
		}
		
		// Save WCFM page option
		if( isset( $wcfm_settings_form['wcfm_page_options'] ) ) {
			update_option( 'wcfm_page_options', $wcfm_settings_form['wcfm_page_options'] );
		}
		
		// Save Product Type wise categories
		if( isset( $wcfm_settings_form['wcfm_product_type_categories'] ) ) {
			update_option( 'wcfm_product_type_categories', $wcfm_settings_form['wcfm_product_type_categories'] );
		} else {
			update_option( 'wcfm_product_type_categories', array() );
		}
		
	  update_option( 'wcfm_options', $options );
	  
		// Init WCFM Custom CSS file
		$wcfm_style_custom = $WCFM->wcfm_create_custom_css();
		 
		$upload_dir      = wp_upload_dir();
		echo '{"status": true, "message": "' . __( 'Settings saved successfully', 'wc-frontend-manager' ) . '", "file": "' . trailingslashit( $upload_dir['baseurl'] ) . '/wcfm/' . $wcfm_style_custom . '"}';
		
		do_action( 'wcfm_settings_update', $wcfm_settings_form );
		 
		die;
	}
}