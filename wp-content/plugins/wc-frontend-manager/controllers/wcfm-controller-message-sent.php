<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Message Sent Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.3.2
 */

class WCFM_Message_Sent_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$wcfm_messages = ! empty( $_POST['wcfm_messages'] ) ? apply_filters( 'wcfm_editor_content_before_save', wp_kses_post( esc_attr ( stripslashes( $_POST['wcfm_messages'] ) ) ) ) : '';
		$author_id = apply_filters( 'wcfm_message_author', get_current_user_id() );
		
		if( wcfm_is_vendor() ) { 
			$author_is_admin = 0;
			$author_is_vendor = 1;
			$message_to = 0; // Receive to Only Store Admin
		} else {
			$author_is_admin = 1;
			$author_is_vendor = 0;
			$message_to = -1; // Receive to all
			
			if( isset( $_POST['direct_to'] ) ) {
				$direct_to = absint( $_POST['direct_to'] );
				if( $direct_to != 0 ) {
					$message_to = $direct_to; // Receive to specific vendor
				}
			}
		}
		
		$WCFM->frontend->wcfm_send_direct_message( $author_id, $message_to, $author_is_admin, $author_is_vendor, $wcfm_messages );
		
		do_action( 'wcfm_messages_update', $wcfm_messages );
		
		echo '{"status": true, "message": "' . __( 'Message sent successfully', 'wc-frontend-manager' ) . '"}';
		 
		die;
	}
}