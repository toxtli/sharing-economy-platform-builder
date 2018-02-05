<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Notice Reply Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   3.1.1
 */

class WCFM_Notice_Reply_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
	  $wcfm_notice_messages = get_wcfm_notice_view_messages();
	  $has_error = false;
	  
	  if(isset($_POST['topic_id']) && !empty($_POST['topic_id'])) {
	  	$current_user_id = get_current_user_id();
	  	
	  	$notice_status = 'publish';
	  	
	  	// Creating new notice
			$new_notice = array(
				'post_title'   => 'Reply for #' . wc_clean( $_POST['topic_id'] ),
				'post_parent'  => wc_clean( $_POST['topic_id'] ),
				'post_status'  => $notice_status,
				'post_type'    => 'wcfm_notice',
				'post_content' => apply_filters( 'wcfm_editor_content_before_save', stripslashes( html_entity_decode( $_POST['topic_reply'], ENT_QUOTES, 'UTF-8' ) ) ),
				'post_author'  => $current_user_id
			);
			
			$new_notice_id = wp_insert_post( $new_notice, true );
			
			if(!is_wp_error($new_notice_id)) {
				
				echo '{"status": true, "message": "' . $wcfm_notice_messages['reply_published'] . '", "redirect": "' . get_wcfm_notice_view_url( $_POST['topic_id'] ) . '#topic_reply_' . $new_notice_id . '"}';
				die;
			} else {
				echo '{"status": false, "message": "' . $wcfm_notice_messages['notice_failed'] . '"}';
			}
		} else {
			echo '{"status": false, "message": "' . $wcfm_notice_messages['no_title'] . '"}';
		}
		
		die;
	}
}