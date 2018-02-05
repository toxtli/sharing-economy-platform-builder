<?php

/**
 * WCFM Notification Class
 *
 * @version		3.3.4
 * @package		wcfm/core
 * @author 		WC Lovers
 */
class WCFM_Notification {
	
	public function __construct() {
		global $WCFM;
		
		// Order notification on WCFM Message
		add_action( 'woocommerce_order_status_on-hold', array( $this, 'wcfm_message_on_new_order' ) );
		add_action( 'woocommerce_order_status_pending', array( $this, 'wcfm_message_on_new_order' ) );
		add_action( 'woocommerce_order_status_processing', array( $this, 'wcfm_message_on_new_order' ) );
		add_action( 'woocommerce_order_status_completed', array( $this, 'wcfm_message_on_new_order' ) );
		
		// Message list in WCFM Dashboard
		add_action( 'after_wcfm_dashboard_product_stats', array( $this, 'wcfm_dashboard_notification_list' ) );
		
		// Message Auto Refresh Counter
		add_action( 'wp_ajax_wcfm_message_count', array( &$this, 'wcfm_message_count' ) );
		
		// Fetching new Message Notifications
		add_action( 'wp_ajax_wcfm_message_notification', array( &$this, 'wcfm_message_notification' ) );
		
		// Message Mark as Read
		add_action( 'wp_ajax_wcfm_messages_mark_read', array( &$this, 'wcfm_messages_mark_read' ) );
		add_action( 'wp_ajax_wcfm_messages_bulk_mark_read', array( &$this, 'wcfm_messages_bulk_mark_read' ) );
		
		// Message Delete
		add_action( 'wp_ajax_wcfm_messages_delete', array( &$this, 'wcfm_messages_delete' ) );
	}
	
	/**
   * New Order notification on WCFM Message board
   */
  function wcfm_message_on_new_order( $order_id ) {
  	global $WCFM, $wpdb;
  	
  	if( is_admin() ) return;
  	
  	$author_id = -2;
  	$author_is_admin = 1;
		$author_is_vendor = 0;
		$message_to = 0;
		
		// Admin Notification
		$wcfm_messages = sprintf( __( 'You have received an Order <b>#%s</b>', 'wc-frontend-manager' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_view_order_url($order_id) . '">' . $order_id . '</a>' );
    $WCFM->frontend->wcfm_send_direct_message( $author_id, $message_to, $author_is_admin, $author_is_vendor, $wcfm_messages, 'order' );
		
    // Vendor Notification
    if( $WCFM->is_marketplace ) {
			$order = wc_get_order($order_id);
			foreach ( $order->get_items() as $item_id => $item ) {
				$product      = $order->get_product_from_item( $item );
				$product_id   = 0;
				if ( is_object( $product ) ) {
					$product_id   = $item->get_product_id();
				}
				if( $product_id ) {
					$author_id = -1;
					$message_to = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );
					
					if( $message_to ) {
						$wcfm_messages = sprintf( __( 'You have received an Order <b>#%s</b> for <b>%s</b>', 'wc-frontend-manager' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_view_order_url($order_id) . '">' . $order_id . '</a>', get_the_title( $product_id ) );
						$WCFM->frontend->wcfm_send_direct_message( $author_id, $message_to, $author_is_admin, $author_is_vendor, $wcfm_messages, 'order' );
					}
				}
			}
		}
  }
  
  /**
   * WCFM Dashboard Notification List
   *
   * @since 3.3.5
   */
  function wcfm_dashboard_notification_list() {
  	global $WCFM, $wpdb;
  	
  	if( apply_filters( 'wcfm_is_pref_direct_message', true ) && apply_filters( 'wcfm_is_allow_notifications', true ) ) {
  		$message_to = apply_filters( 'wcfm_message_author', get_current_user_id() );
  		
  		$sql = 'SELECT wcfm_messages.* FROM ' . $wpdb->prefix . 'wcfm_messages AS wcfm_messages';
  		$sql .= ' WHERE 1=1';
  		$sql .= " AND `is_direct_message` = 1";
  		if( wcfm_is_vendor() ) { 
				$vendor_filter = " AND ( `author_id` = {$message_to} OR `message_to` = -1 OR `message_to` = {$message_to} )";
				$sql .= $vendor_filter;
			} else {
				$group_manager_filter = apply_filters( 'wcfm_notification_group_manager_filter', '' );
				if( $group_manager_filter ) {
					$sql .= $group_manager_filter;
				} else {
					$sql .= " AND `author_id` != -1";
				}
			}
			$sql .= " AND NOT EXISTS (SELECT * FROM {$wpdb->prefix}wcfm_messages_modifier as wcfm_messages_modifier_2 WHERE wcfm_messages.ID = wcfm_messages_modifier_2.message AND wcfm_messages_modifier_2.read_by={$message_to})";
			$sql .= " ORDER BY wcfm_messages.`ID` DESC";
			$sql .= " LIMIT 10";
			$sql .= " OFFSET 0";
			
			$wcfm_messages = $wpdb->get_results( $sql );
			
			?>
			<div class="wcfm_dashboard_notifications">
				<div class="page_collapsible" id="wcfm_dashboard_notifications"><span class="fa fa-bell-o"></span><span class="dashboard_widget_head"><?php _e('Notifications', 'wc-frontend-manager'); ?></span></div>
				<div class="wcfm-container">
					<div id="wcfm_dashboard_notifications_expander" class="wcfm-content">
					  <?php
					  if( !empty( $wcfm_messages ) ) {
					  	$counter = 0;
							foreach($wcfm_messages as $wcfm_message) {
								if( $counter == 6 ) break;
								// Type
								if( !$wcfm_message->message_type ) $wcfm_message->message_type = 'direct';
								$message_type = isset( $message_types[$wcfm_message->message_type] ) ? $message_types[$wcfm_message->message_type] : ucfirst($wcfm_message->message_type);
								$message_icon = $this->get_wcfm_notification_icon( $wcfm_message->message_type );
					
								// Message
								$message_text =  htmlspecialchars_decode($wcfm_message->message);
								if( $wcfm_message->message_type  == 'direct' ) $message_text =  substr( strip_tags( $message_text ), 0, 80 ) . ' ...';
								echo '<div class="wcfm_dashboard_notification">' . $message_icon . ' ' . $message_text . '</div>';
								$counter++;
							}
							if( count( $wcfm_messages ) > 6 ) {
								echo '<div class="wcfm_dashboard_notifications_show_all"><a class="wcfm_submit_button" href="' . get_wcfm_messages_url() . '">' . __( 'Show All', 'wc-frontend-manager' ) . ' >></a></div><div class="wcfm-clearfix"></div>';
							}
						} else {
							_e( 'There is no notification yet!!', 'wc-frontend-manager' );
						}
						?>
					</div>
				</div>
			</div>
			<?php
  	}
  }
  
  /**
   * WCFM Message Counter
   *
   * @since 2.3.4
   */
  function wcfm_message_count() {
  	global $WCFM;

		$unread_notice = $WCFM->frontend->wcfm_direct_message_count( 'notice' );
		$unread_message = $WCFM->frontend->wcfm_direct_message_count( 'message' );
		$unread_enquiry = $WCFM->frontend->wcfm_direct_message_count( 'enquiry' );
		
		echo '{"notice": ' . $unread_notice . ', "message": ' .$unread_message . ', "enquiry": ' .$unread_enquiry . '}';
		die;
  }
  
  /**
   * WCFM New message notification
   *
   * @since 3.3.4
   */
  function wcfm_message_notification() {
  	global $WCFM, $wpdb;
  	
  	if( isset( $_POST['limit'] ) && $_POST['limit'] ) {
  		$limit = $_POST['limit'];
  		
  		$message_to = apply_filters( 'wcfm_message_author', get_current_user_id() );
  		
  		$sql = 'SELECT wcfm_messages.* FROM ' . $wpdb->prefix . 'wcfm_messages AS wcfm_messages';
  		$sql .= ' WHERE 1=1';
  		
			if( wcfm_is_vendor() ) { 
				//$vendor_filter = " AND `author_is_admin` = 1";
				$vendor_filter = " AND ( `author_id` = {$message_to} OR `message_to` = -1 OR `message_to` = {$message_to} )";
				$sql .= $vendor_filter;
			} else {
				$sql .= " AND `author_id` != -1";
			}
			
			$message_status_filter = " AND NOT EXISTS (SELECT * FROM {$wpdb->prefix}wcfm_messages_modifier as wcfm_messages_modifier_2 WHERE wcfm_messages.ID = wcfm_messages_modifier_2.message AND wcfm_messages_modifier_2.read_by={$message_to})";
	
			$sql .= $message_status_filter;
			
			$sql .= " ORDER BY wcfm_messages.`ID` DESC";
	
			$sql .= " LIMIT {$limit}";
	
			$sql .= " OFFSET 0";
			
			$wcfm_messages = $wpdb->get_results( $sql );
			
			$wcfm_messages_json_arr = '';
			if ( !empty( $wcfm_messages ) ) {
				foreach ( $wcfm_messages as $wcfm_message ) {
					$wcfm_messages_json_arr .=  '<div class="wcfm_notification_box">' . $this->get_wcfm_notification_icon( $wcfm_message->message_type ) . htmlspecialchars_decode($wcfm_message->message) . '</div>';
				}
			}
			if( $wcfm_messages_json_arr ) $wcfm_messages_json_arr = '<div class="wcfm_notification_wrapper"><span class="fa fa-times-circle wcfm_notification_close"></span><div class="wcfm-clearfix"></div>' . $wcfm_messages_json_arr . '</div>';
			echo $wcfm_messages_json_arr;
  	}
  	
  	die;
  }
  
  /**
   * Handle Message mark as Read
   *
   * @since 2.3.4
   */
  function wcfm_messages_mark_read() {
  	global $WCFM, $wpdb, $_POST;
  	
  	$messageid = absint( $_POST['messageid'] );
  	$author_id = apply_filters( 'wcfm_message_author', get_current_user_id() );
  	$todate = date('Y-m-d H:i:s');
  	
  	$wcfm_read_message     = "INSERT into {$wpdb->prefix}wcfm_messages_modifier 
																(`message`, `is_read`, `read_by`, `read_on`)
																VALUES
																({$messageid}, 1, {$author_id}, '{$todate}')";
		$wpdb->query($wcfm_read_message);
		
		die;
  }
  
  /**
   * Handle Message Bulk Mark as Read
   *
   * @since 3.4.2
   */
  function wcfm_messages_bulk_mark_read() {
  	global $WCFM, $wpdb, $_POST;
  	
  	if( isset($_POST['selected_messages']) ) {
			$selected_messages = $_POST['selected_messages'];
			if( is_array( $selected_messages ) && !empty( $selected_messages ) ) {
				foreach( $selected_messages as $messageid ) {
					$author_id = apply_filters( 'wcfm_message_author', get_current_user_id() );
					$todate = date('Y-m-d H:i:s');
					
					$wcfm_read_message     = "INSERT into {$wpdb->prefix}wcfm_messages_modifier 
																			(`message`, `is_read`, `read_by`, `read_on`)
																			VALUES
																			({$messageid}, 1, {$author_id}, '{$todate}')";
					$wpdb->query($wcfm_read_message);
				}
			}
		}
		echo '{ "status": true }';
		die;
  }
  
  /**
   * Handle Message Delete
   *
   * @since 3.4.5
   */
  function wcfm_messages_delete() {
  	global $WCFM, $wpdb, $_POST;
  	
  	$messageid = absint( $_POST['messageid'] );
  	$wpdb->query( "DELETE FROM {$wpdb->prefix}wcfm_messages WHERE `ID` = {$messageid}" );
  	$wpdb->query( "DELETE FROM {$wpdb->prefix}wcfm_messages_modifier WHERE `message` = {$messageid}" );
  	
		die;
  }
  
  public function get_wcfm_notification_icon( $type ) {
  	$notification_icon = '';
  	$message_types = get_wcfm_message_types();
  	$message_type = isset( $message_types[$type] ) ? $message_types[$type] : ucfirst($type);
  	$message_type_class = 'fa wcfm-message-type-icon text_tip wcfm-message-type-' . $type . ' ';
  	
  	switch( $type ) {
  		case 'order':
  			$message_type_class .= 'fa-shopping-cart';
  		break;
  		
  		case 'direct':
  			$message_type_class .= 'fa-comment-o';
  		break;
  		
  		case 'product_review':
  			$message_type_class .= 'fa-cube';
  		break;
  		
  		case 'booking':
  			$message_type_class .= 'fa-calendar-check-o';
  		break;
  		
  		case 'appointment':
  			$message_type_class .= 'fa-calendar-check-o';
  		break;
  		
  		case 'enquiry':
  			$message_type_class .= 'fa-question-circle-o';
  		break;
  		
  		case 'verification':
  			$message_type_class .= 'fa-angellist';
  		break;
  		
  		case 'membership':
  			$message_type_class .= 'fa-user-o';
  		break;
  		
  		case 'membership-cancel':
  			$message_type_class .= 'fa-user-o';
  		break;
  		
  		case 'vendor_approval':
  			$message_type_class .= 'fa-user-plus';
  		break;
  		
  		default:
  			$message_type_class = 'fa-cart';
  		break;
  	}
  	
  	$notification_icon = '<span class="' . $message_type_class . '" data-tip="' . $message_type . '"></span>';
  	
  	return $notification_icon;
  }
}