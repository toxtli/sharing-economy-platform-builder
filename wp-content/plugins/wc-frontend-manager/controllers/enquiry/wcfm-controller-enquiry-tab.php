<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Enquiry Tab Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers/enquiry
 * @version   3.2.8
 */

class WCFM_Enquiry_Tab_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb;
		
		$wcfm_enquiry_tab_form_data = array();
	  parse_str($_POST['wcfm_enquiry_tab_form'], $wcfm_enquiry_tab_form_data);
	  
	  $wcfm_enquiry_messages = get_wcfm_enquiry_manage_messages();
	  $has_error = false;
	  
	  if(isset($wcfm_enquiry_tab_form_data['enquiry']) && !empty($wcfm_enquiry_tab_form_data['enquiry'])) {
	  	
	  	$enquiry = $wcfm_enquiry_tab_form_data['enquiry'];
	  	$reply = '';
	  	$product_id = $wcfm_enquiry_tab_form_data['product_id'];
	  	
	  	$product_post = get_post( $product_id );
	  	$author_id = $product_post->post_author;
	  	
	  	$vendor_id = 0;
	  	if( wcfm_is_marketplace() ) {
	  		$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
	  	}
	  	
	  	if( !is_user_logged_in() ) {
	  		$customer_id = 0;
	  		$customer_name = $wcfm_enquiry_tab_form_data['customer_name'];
	  		$customer_email = $wcfm_enquiry_tab_form_data['customer_email'];
	  	} else {
	  		$customer_id = get_current_user_id();
	  		$userdata = get_userdata( $customer_id );
				$first_name = $userdata->first_name;
				$last_name  = $userdata->last_name;
				$display_name  = $userdata->display_name;
				if( $first_name ) {
					$customer_name = $first_name . ' ' . $last_name;
				} else {
					$customer_name = $display_name;
				}
	  		$customer_email = $userdata->user_email;
	  	}
	  	
	  	define( 'DOING_WCFM_EMAIL', true );
	  	
	  	$reply_by = 0;
	  	$is_private = 0;
	  	$replied = date('Y-m-d H:i:s');
	  	
	  	$wcfm_create_enquiry    = "INSERT into {$wpdb->prefix}wcfm_enquiries 
																(`enquiry`, `reply`, `author_id`, `product_id`, `vendor_id`, `customer_id`, `customer_name`, `customer_email`, `reply_by`, `is_private`, `replied`)
																VALUES
																('{$enquiry}', '{$reply}', {$author_id}, {$product_id}, {$vendor_id}, {$customer_id}, '{$customer_name}', '{$customer_email}', {$reply_by}, {$is_private}, '{$replied}')";
															
			$wpdb->query($wcfm_create_enquiry);
			
			// Send mail to admin
			$mail_to = get_bloginfo( 'admin_email' );
			$reply_mail_subject = "{site_name}: " . __( "New enquiry for", "wc-frontend-manager" ) . " - {product_title}";
			$reply_mail_body    = __( 'Hi', 'wc-frontend-manager' ) .
															 ',<br/><br/>' . 
															 __( 'You recently have a enquiry for "{product_title}". Please check below for the details: ', 'wc-frontend-manager' ) .
															 '<br/><br/><strong><i>' . 
															 '"{enquiry}"' . 
															 '</i></strong><br/><br/>' .
															 __( 'Check more details here', 'wc-frontend-manager' ) . ': {enquiry_url}' .
															 '<br /><br/>' . __( 'Thank You', 'wc-frontend-manager' );
			
			//$headers[] = 'From: ' . get_bloginfo( 'name' ) . ' <' . $mail_to . '>';
		  $headers[] = 'Cc: ' . $customer_email;
			$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $reply_mail_subject );
			$subject = str_replace( '{product_title}', get_the_title( $product_id ), $subject );
			$message = str_replace( '{product_title}', get_the_title( $product_id ), $reply_mail_body );
			$message = str_replace( '{enquiry_url}', get_wcfm_enquiry_url(), $message );
			$message = str_replace( '{enquiry}', $enquiry, $message );
			
			wp_mail( $mail_to, $subject, $message, $headers );
			
			// Direct message
			$wcfm_messages = sprintf( __( 'You have received an Enquiry for <b>%s</b>', 'wc-frontend-manager' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_enquiry_url() . '">' . get_the_title( $product_id ) . '</a>' );
			$WCFM->frontend->wcfm_send_direct_message( -2, 0, 1, 0, $wcfm_messages, 'enquiry' );
			
			// Semd email to vendor
			if( wcfm_is_marketplace() ) {
				if( $vendor_id ) {
					$vendor_email = $WCFM->wcfm_vendor_support->wcfm_get_vendor_email_from_product( $product_id );
					if( $vendor_email ) {
						wp_mail( $vendor_email, $subject, $message, $headers );
					}
					
					// Direct message
					$wcfm_messages = sprintf( __( 'You have received an Enquiry for <b>%s</b>', 'wc-frontend-manager' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_enquiry_url() . '">' . get_the_title( $product_id ) . '</a>' );
					$WCFM->frontend->wcfm_send_direct_message( -1, $vendor_id, 1, 0, $wcfm_messages, 'enquiry' );
				}
	  	}
			
			echo '{"status": true, "message": "' . $wcfm_enquiry_messages['enquiry_saved'] . '"}';
		} else {
			echo '{"status": false, "message": "' . $wcfm_enquiry_messages['no_enquiry'] . '"}';
		}
		
		die;
	}
}