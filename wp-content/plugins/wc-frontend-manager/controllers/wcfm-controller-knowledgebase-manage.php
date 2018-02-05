<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Knowledgebase Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.0.0
 */

class wcfm_Knowledgebase_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $wcfm_knowledgebase_manager_form_data;
		
		$wcfm_knowledgebase_manager_form_data = array();
	  parse_str($_POST['wcfm_knowledgebase_manage_form'], $wcfm_knowledgebase_manager_form_data);
	  
	  $wcfm_knowledgebase_messages = get_wcfm_knowledgebase_manage_messages();
	  $has_error = false;
	  
	  if(isset($wcfm_knowledgebase_manager_form_data['title']) && !empty($wcfm_knowledgebase_manager_form_data['title'])) {
	  	$is_update = false;
	  	$is_publish = false;
	  	$current_user_id = get_current_user_id();
	  	
	  	$knowledgebase_status = 'publish';
	  	
	  	// Creating new knowledgebase
			$new_knowledgebase = array(
				'post_title'   => wc_clean( $wcfm_knowledgebase_manager_form_data['title'] ),
				'post_status'  => $knowledgebase_status,
				'post_type'    => 'wcfm_knowledgebase',
				'post_content' => stripslashes( html_entity_decode( $_POST['content'], ENT_QUOTES, 'UTF-8' ) ),
				'post_author'  => $current_user_id
			);
			
			if(isset($wcfm_knowledgebase_manager_form_data['knowledgebase_id']) && $wcfm_knowledgebase_manager_form_data['knowledgebase_id'] == 0) {
				$new_knowledgebase_id = wp_insert_post( $new_knowledgebase, true );
			} else { // For Update
				$is_update = true;
				$new_knowledgebase['ID'] = $wcfm_knowledgebase_manager_form_data['knowledgebase_id'];
				$new_knowledgebase_id = wp_update_post( $new_knowledgebase, true );
			}
			
			if(!is_wp_error($new_knowledgebase_id)) {
				// For Update
				if($is_update) $new_knowledgebase_id = $wcfm_knowledgebase_manager_form_data['knowledgebase_id'];
				
				echo '{"status": true, "message": "' . $wcfm_knowledgebase_messages['knowledgebase_saved'] . '", "redirect": "' . get_wcfm_knowledgebase_url() . '"}';
				die;
			} else {
				echo '{"status": false, "message": "' . $wcfm_knowledgebase_messages['knowledgebase_failed'] . '"}';
			}
		} else {
			echo '{"status": false, "message": "' . $wcfm_knowledgebase_messages['no_title'] . '"}';
		}
		
		die;
	}
}