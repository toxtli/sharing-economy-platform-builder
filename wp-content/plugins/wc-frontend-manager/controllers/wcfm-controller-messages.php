<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Messages Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.3.2
 */

class WCFM_Messages_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST, $start_date, $end_date;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$message_to = apply_filters( 'wcfm_message_author', get_current_user_id() );
		
		$message_status = '';
		if ( ! empty( $_POST['message_status'] ) ) {
			$message_status = esc_sql( $_POST['message_status'] );
		}
		
		$message_type = '';
		$message_types = get_wcfm_message_types();
		if ( ! empty( $_POST['message_type'] ) ) {
			$message_type = esc_sql( $_POST['message_type'] );
		}
		
		$the_orderby = ! empty( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'ID';
		$the_order   = ( ! empty( $_POST['order'] ) && 'asc' === $_POST['order'] ) ? 'ASC' : 'DESC';

		$items_per_page = $length;

		$sql = 'SELECT COUNT(wcfm_messages.ID) FROM ' . $wpdb->prefix . 'wcfm_messages AS wcfm_messages';
		
		$left_join_query = '';
		if( $message_status == 'read' ) {
			$left_join_query  = ' LEFT JOIN ' . $wpdb->prefix . 'wcfm_messages_modifier as wcfm_messages_modifier';
			$left_join_query .= ' ON wcfm_messages.ID = wcfm_messages_modifier.message';
		}
		
		$sql .= $left_join_query;

		$sql .= ' WHERE 1=1';
		
		$status_filter = " AND `is_direct_message` = 1";
		
		$sql .= $status_filter;
		
		$type_filter = '';
		if( $message_type != 'all' ) {
		  $type_filter = " AND `message_type` = '" . $message_type . "'";
		}
		
		$sql .= $type_filter;

		if( wcfm_is_vendor() ) { 
			//$vendor_filter = " AND `author_is_admin` = 1";
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
		
		
		$message_status_filter = '';
		if( $message_status == 'read' ) {
			$message_status_filter = " AND wcfm_messages_modifier.is_read = 1 AND wcfm_messages_modifier.read_by = {$message_to}";
		} elseif( $message_status == 'unread' ) {
			$message_status_filter = " AND NOT EXISTS (SELECT * FROM {$wpdb->prefix}wcfm_messages_modifier as wcfm_messages_modifier_2 WHERE wcfm_messages.ID = wcfm_messages_modifier_2.message AND wcfm_messages_modifier_2.read_by={$message_to})";
		}
		$sql .= $message_status_filter;
		
		$total_mesaages = $wpdb->get_var( $sql );

		$sql = 'SELECT wcfm_messages.* FROM ' . $wpdb->prefix . 'wcfm_messages AS wcfm_messages';
		
		$sql .= $left_join_query;

		$sql .= ' WHERE 1=1';

		$sql .= $status_filter;
		
		$sql .= $type_filter;
		
		if( wcfm_is_vendor() ) { 
			$sql .= $vendor_filter;
		} else {
			$group_manager_filter = apply_filters( 'wcfm_notification_group_manager_filter', '' );
			if( $group_manager_filter ) {
				$sql .= $group_manager_filter;
			} else {
				$sql .= " AND `author_id` != -1";
			}
		}
		
		$sql .= $message_status_filter;

		$sql .= " ORDER BY wcfm_messages.`{$the_orderby}` {$the_order}";

		$sql .= " LIMIT {$items_per_page}";

		$sql .= " OFFSET {$offset}";
		
		$wcfm_messages = $wpdb->get_results( $sql );
		
		// Generate Products JSON
		$wcfm_messages_json = '';
		$wcfm_messages_json = '{
														"draw": ' . $_POST['draw'] . ',
														"recordsTotal": ' . $total_mesaages . ',
														"recordsFiltered": ' . $total_mesaages . ',
														"data": ';
		
		$index = 0;
		$wcfm_messages_json_arr = array();
		if ( !empty( $wcfm_messages ) ) {
			foreach ( $wcfm_messages as $wcfm_message ) {
				
				// Acton Checkbox
				if( ($message_status == 'unread') && !in_array( $wcfm_message->message_type, array( 'verification', 'vendor_approval' ) ) ) {
					$wcfm_messages_json_arr[$index][] =  '<input type="checkbox" class="wcfm-checkbox bulk_action_checkbox_single" name="bulk_action_checkbox[]" value="' . $wcfm_message->ID . '" />';
				} else {
					$wcfm_messages_json_arr[$index][] =  '';
				}
				
				// Type
				if( !$wcfm_message->message_type ) $wcfm_message->message_type = 'direct';
				$message_type = isset( $message_types[$wcfm_message->message_type] ) ? $message_types[$wcfm_message->message_type] : ucfirst($wcfm_message->message_type);
				$wcfm_messages_json_arr[$index][] = $WCFM->wcfm_notification->get_wcfm_notification_icon( $wcfm_message->message_type ); //'<span class="wcfm-message-type wcfm-message-type-' . $wcfm_message->message_type . '">' . $message_type . '</span>';
	
				// Message
				$wcfm_messages_json_arr[$index][] =  htmlspecialchars_decode($wcfm_message->message);
				
				// From
				if( $wcfm_message->author_is_admin ) {
					if( $wcfm_message->author_id == -1 || $wcfm_message->author_id == -2 ) {
						$wcfm_messages_json_arr[$index][] =  __( 'System', 'wc-frontend-manager' );
					} else {
						$wcfm_messages_json_arr[$index][] =  __( 'Store', 'wc-frontend-manager' );
					}
				} else {
					$is_marketplace = wcfm_is_marketplace();
					if( $is_marketplace == 'wcpvendors' ) {
						if( !wcfm_is_vendor() ) {
							$vendor_data = WC_Product_Vendors_Utils::get_vendor_data_by_id( $wcfm_message->author_id );
							$wcfm_messages_json_arr[$index][] = ! empty( $vendor_data['shop_name'] ) ? $vendor_data['shop_name'] : '';
						} else {
							$wcfm_messages_json_arr[$index][] =  __( 'You', 'wc-frontend-manager' );
						}
					} else {
							if( !wcfm_is_vendor() ) {
								$wcfm_messages_json_arr[$index][] =  get_user_by( 'ID', $wcfm_message->author_id )->display_name;
							} else {
								$wcfm_messages_json_arr[$index][] =  __( 'You', 'wc-frontend-manager' );
							}
					}
				}
				
				// TO
				if( $wcfm_message->message_to == -1 ) {
					$wcfm_messages_json_arr[$index][] =  __( 'All', 'wc-frontend-manager' );
				} else if( $wcfm_message->message_to == 0 ) {
					$wcfm_messages_json_arr[$index][] =  __( 'Store', 'wc-frontend-manager' );
				} else {
					$is_marketplace = wcfm_is_marketplace();
					if( $is_marketplace == 'wcpvendors' ) {
						if( !wcfm_is_vendor() ) {
							$vendor_data = WC_Product_Vendors_Utils::get_vendor_data_by_id( $wcfm_message->message_to );
							$wcfm_messages_json_arr[$index][] = ! empty( $vendor_data['shop_name'] ) ? $vendor_data['shop_name'] : '';
						} else {
							$wcfm_messages_json_arr[$index][] =  __( 'You', 'wc-frontend-manager' );
						}
					} else {
						if( !wcfm_is_vendor() ) {
							$wcfm_messages_json_arr[$index][] =  get_user_by( 'ID', $wcfm_message->message_to )->display_name;
						} else {
							$wcfm_messages_json_arr[$index][] =  __( 'You', 'wc-frontend-manager' );
						}
					}
				}
				
				// Date
				$wcfm_messages_json_arr[$index][] = date_i18n( wc_date_format(), strtotime( $wcfm_message->created ) );
				
				// Action
				$actions = '&ndash;';
				if( $message_status == 'unread' ) {
					if( ( !wcfm_is_vendor() && $wcfm_message->message_type == 'verification' ) ) {
						$actions = '<a class="wcfm_messages_seller_verification wcfm-action-icon" href="#" data-vendorid="' . $wcfm_message->author_id . '" data-messageid="' . $wcfm_message->ID . '"><span class="fa fa-check-circle-o text_tip" data-tip="' . esc_attr__( 'Approve / Reject', 'wc-frontend-manager' ) . '"></span></a>';
					} elseif( ( !wcfm_is_vendor() && $wcfm_message->message_type == 'vendor_approval' ) ) {
						$actions = '<a class="wcfm_messages_vendor_approval wcfm-action-icon" href="#" data-vendorid="' . $wcfm_message->author_id . '" data-messageid="' . $wcfm_message->ID . '"><span class="fa fa-check-circle-o text_tip" data-tip="' . esc_attr__( 'Approve / Reject', 'wc-frontend-manager' ) . '"></span></a>';
					} else {
						$actions = '<a class="wcfm_messages_mark_read wcfm-action-icon" href="#" data-messageid="' . $wcfm_message->ID . '"><span class="fa fa-check-square-o text_tip" data-tip="' . esc_attr__( 'Mark Read', 'wc-frontend-manager' ) . '"></span></a>';
					}
				}
				
				if( current_user_can( 'administrator' ) ) {
					if( $message_status != 'unread' ) { $actions = ''; }
					$actions .= '<a class="wcfm_messages_delete wcfm-action-icon" href="#" data-messageid="' . $wcfm_message->ID . '"><span class="fa fa-trash-o text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager' ) . '"></span></a>';
				}
				
				/*if( $wcfm_is_allow_pdf_invoice = apply_filters( 'wcfm_is_allow_pdf_invoice', true ) ) {
					if( WCFM_Dependencies::wcfmu_plugin_active_check() && WCFM_Dependencies::wcfm_wc_pdf_invoices_packing_slips_plugin_active_check() ) {
						$actions .= '<a class="wcfm_pdf_invoice wcfm-action-icon" href="#" data-orderid="' . $the_order->get_id() . '"><span class="fa fa-file-pdf-o text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
					} else {
						if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
							$actions .= '<a class="wcfm_pdf_invoice_vendor_dummy wcfm-action-icon" href="#" data-orderid="' . $wcfm_orders_single->ID . '"><span class="fa fa-file-pdf-o text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
						}
					}
				}*/
				
				$wcfm_messages_json_arr[$index][] =  apply_filters ( 'wcfm_messages_actions', $actions );
				
				$index++;
			}
		}
		if( !empty($wcfm_messages_json_arr) ) $wcfm_messages_json .= json_encode($wcfm_messages_json_arr);
		else $wcfm_messages_json .= '[]';
		$wcfm_messages_json .= '
													}';
													
		echo $wcfm_messages_json;
	}
}