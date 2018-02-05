<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Enquiry Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers/enquiry
 * @version   3.2.8
 */

class wcfm_Enquiry_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb;
		
		$wcfm_enquiry_manager_form_data = array();
	  parse_str($_POST['wcfm_enquiry_manage_form'], $wcfm_enquiry_manager_form_data);
	  
	  $wcfm_enquiry_messages = get_wcfm_enquiry_manage_messages();
	  $has_error = false;
	  
	  if(isset($wcfm_enquiry_manager_form_data['enquiry']) && !empty($wcfm_enquiry_manager_form_data['enquiry'])) {
	  	
	  	$enquiry = $wcfm_enquiry_manager_form_data['enquiry'];
	  	$reply   = apply_filters( 'wcfm_editor_content_before_save', stripslashes( html_entity_decode( $_POST['reply'], ENT_QUOTES, 'UTF-8' ) ) );
	  	$enquiry_id = $wcfm_enquiry_manager_form_data['enquiry_id'];
	  	
	  	$reply_by = apply_filters( 'wcfm_message_author', get_current_user_id() );
	  	$is_private = 0;
	  	if(isset($wcfm_enquiry_manager_form_data['is_private']) && !empty($wcfm_enquiry_manager_form_data['is_private'])) {
	  		$is_private = 1;
	  	}
	  	$replied = date('Y-m-d H:i:s');
	  	
	  	$wcfm_update_enquiry    = "UPDATE {$wpdb->prefix}wcfm_enquiries 
																SET 
																`enquiry` = '{$enquiry}', 
																`reply` = '{$reply}',
																`reply_by` = {$reply_by},
																`is_private` = {$is_private}, 
																`replied` = '{$replied}'
																WHERE 
																`ID` = {$enquiry_id}";
															
			$wpdb->query($wcfm_update_enquiry);
			
			// Send mail to customer
			if(isset($wcfm_enquiry_manager_form_data['notify']) && !empty($wcfm_enquiry_manager_form_data['notify'])) {
				$enquiry_datas = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wcfm_enquiries WHERE `ID` = {$enquiry_id}" );
				$mail_to = '';
				$mail_name = '';
				$product_id = '';
				if( !empty( $enquiry_datas ) ) {
					foreach( $enquiry_datas as $enquiry_data ) {
						$mail_to = $enquiry_data->customer_email;
						$mail_name = $enquiry_data->customer_name;
						$product_id = $enquiry_data->product_id;
					}
				}
				
				if( $mail_to ) {
					define( 'DOING_WCFM_EMAIL', true );
					
					$reply_mail_subject = "{site_name}: " . __( "Reply for your enquiry", "wc-frontend-manager" ) . " - {product_title}";
					$reply_mail_body    = __( 'Hi', 'wc-frontend-manager' ) . ' {first_name}' .
																	 ',<br/><br/>' . 
																	 __( 'We recently have a enquiry from you regarding "{product_title}". Please check below for our input for the same: ', 'wc-frontend-manager' ) .
																	 '<br/><br/><strong><i>' . 
																	 '"{reply}"' . 
																	 '</i></strong><br/><br/>' .
																	 __( 'Check more details here', 'wc-frontend-manager' ) . ': {product_url}' .
																	 '<br /><br/>' . __( 'Thank You', 'wc-frontend-manager' );
																	 
					$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $reply_mail_subject );
					$subject = str_replace( '{product_title}', get_the_title( $product_id ), $subject );
					$message = str_replace( '{product_title}', get_the_title( $product_id ), $reply_mail_body );
					$message = str_replace( '{first_name}', $mail_name, $message );
					$message = str_replace( '{product_url}', get_permalink( $product_id ), $message );
					$message = str_replace( '{reply}', $reply, $message );
					
					wp_mail( $mail_to, $subject, $message );
				}
			}
				
			echo '{"status": true, "message": "' . $wcfm_enquiry_messages['enquiry_published'] . '"}';
		} else {
			echo '{"status": false, "message": "' . $wcfm_enquiry_messages['no_enquiry'] . '"}';
		}
		
		die;
	}
}