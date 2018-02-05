<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Orders Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.0.0
 */

class WCFM_Orders_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$args = array(
							'posts_per_page'   => $length,
							'offset'           => $offset,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'DESC',
							'include'          => '',
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'shop_order',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => 'any',
							'suppress_filters' => 0 
						);
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) {
			$wc_order_ids = wc_order_search( $_POST['search']['value'] );
			$args['post__in'] = $wc_order_ids;
		} else {
			if ( ! empty( $_POST['m'] ) ) {
				$year  = absint( substr( $_POST['m'], 0, 4 ) );
				$month = absint( substr( $_POST['m'], 4, 2 ) );
				
				$args['date_query'] = array(
																		array(
																			'year'  => $year,
																			'month' => $month,
																		),
																);
			}
		}
		
		$args = apply_filters( 'wcfm_orders_args', $args );
		
		$wcfm_orders_array = get_posts( $args );
		
		// Get Product Count
		$order_count = 0;
		$filtered_order_count = 0;
		$wcfm_orders_counts = wp_count_posts('shop_order');
		foreach($wcfm_orders_counts as $wcfm_orders_count ) {
			$order_count += $wcfm_orders_count;
		}
		
		$order_status = ! empty( $_POST['order_status'] ) ? sanitize_text_field( $_POST['order_status'] ) : 'all';
		if( $order_status == 'all' ) {
			$filtered_order_count = $order_count;
		} else {
			foreach($wcfm_orders_counts as $wcfm_orders_count_status => $wcfm_orders_count ) {
				if( $wcfm_orders_count_status == 'wc-' . $order_status ) {
					$filtered_order_count = $wcfm_orders_count;
				}
			}
		}
		
		$admin_fee_mode = apply_filters( 'wcfm_is_admin_fee_mode', false );
		
		// Generate Products JSON
		$wcfm_orders_json = '';
		$wcfm_orders_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $order_count . ',
															"recordsFiltered": ' . $filtered_order_count . ',
															"data": ';
		if(!empty($wcfm_orders_array)) {
			$index = 0;
			$wcfm_orders_json_arr = array();
			foreach($wcfm_orders_array as $wcfm_orders_single) {
				$the_order = wc_get_order( $wcfm_orders_single );
				$order_currency = $the_order->get_currency();
				
				// Status
				$wcfm_orders_json_arr[$index][] =  '<span class="order-status tips wcicon-status-' . sanitize_title( $the_order->get_status() ) . ' text_tip" data-tip="' . wc_get_order_status_name( $the_order->get_status() ) . '"></span>';
				
				// Order
				if( apply_filters( 'wcfm_allow_order_customer_details', true ) ) {
					$user_info = array();
					if ( $the_order->get_user_id() ) {
						$user_info = get_userdata( $the_order->get_user_id() );
					}
	
					if ( ! empty( $user_info ) ) {
	
						$username = '';
	
						if ( $user_info->first_name || $user_info->last_name ) {
							$username .= esc_html( sprintf( _x( '%1$s %2$s', 'full name', 'wc-frontend-manager' ), ucfirst( $user_info->first_name ), ucfirst( $user_info->last_name ) ) );
						} else {
							$username .= esc_html( ucfirst( $user_info->display_name ) );
						}
	
					} else {
						if ( $the_order->billing_first_name || $the_order->billing_last_name ) {
							$username = trim( sprintf( _x( '%1$s %2$s', 'full name', 'wc-frontend-manager' ), $the_order->billing_first_name, $the_order->billing_last_name ) );
						} else if ( $the_order->billing_company ) {
							$username = trim( $the_order->billing_company );
						} else {
							$username = __( 'Guest', 'wc-frontend-manager' );
						}
					}
					
					$username = apply_filters( 'wcfm_order_by_user', $username, $wcfm_orders_single->ID );
				} else {
					$username = __( 'Guest', 'wc-frontend-manager' );
				}

				if( $wcfm_is_allow_order_details = apply_filters( 'wcfm_is_allow_order_details', true ) ) {
					$wcfm_orders_json_arr[$index][] =  '<a href="' . get_wcfm_view_order_url($wcfm_orders_single->ID, $the_order) . '" class="wcfm_dashboard_item_title">#' . esc_attr( $the_order->get_order_number() ) . '</a>' . ' ' . __( 'by', 'wc-frontend-manage' ) . ' ' . $username;
				} else {
					$wcfm_orders_json_arr[$index][] =  '<span class="wcfm_dashboard_item_title">#' . esc_attr( $the_order->get_order_number() ) . '</span>' . ' ' . __( 'by', 'wc-frontend-manage' ) . ' ' . $username;
				}
				
				// Purchased
				$order_item_details = '<div class="order_items" cellspacing="0">';
				$items = $the_order->get_items();
				foreach ($items as $key => $item) {
					$product        = $the_order->get_product_from_item( $item );
					$item_meta_html = strip_tags( wc_display_item_meta( $item, array(
																																					'before'    => "\n- ",
																																					'separator' => "\n- ",
																																					'after'     => "",
																																					'echo'      => false,
																																					'autop'     => false,
																																				) ) );
				
					$order_item_details .= '<div class=""><span class="qty">' . $item->get_quantity() . 'x</span><span class="name">' . $item->get_name();
					if ( ! empty( $item_meta_html ) ) $order_item_details .= '<span class="img_tip" data-tip="' . $item_meta_html . '"></span>';
					$order_item_details .= '</td></div>';
				}
				$order_item_details .= '</div>';
				$wcfm_orders_json_arr[$index][] =  '<a href="#" class="show_order_items">' . apply_filters( 'woocommerce_admin_order_item_count', sprintf( _n( '%d item', '%d items', $the_order->get_item_count(), 'wc-frontend-manager' ), $the_order->get_item_count() ), $the_order ) . '</a>' . $order_item_details;
				
				// Gross Sales
				$gross_sales = $the_order->get_total();
				$total = '<span class="order_total">' . $the_order->get_formatted_order_total() . '</span>';

				if ( $the_order->get_payment_method_title() ) {
					$total .= '<br /><small class="meta">' . __( 'Via', 'wc-frontend-manager' ) . ' ' . esc_html( $the_order->get_payment_method_title() ) . '</small>';
				}
				$wcfm_orders_json_arr[$index][] =  $total;
				
				// Commission
				$commission = 0;
				if( $marketplece = wcfm_is_marketplace() ) {
					$commission = $WCFM->wcfm_vendor_support->wcfm_get_commission_by_order( $wcfm_orders_single->ID );
					if( $commission ) {
						if( $admin_fee_mode || ( $marketplece == 'dokan' ) ) {
							$commission = $gross_sales - $commission;
						}
						$wcfm_orders_json_arr[$index][] =  wc_price( $commission, array( 'currency' => $order_currency ) );
					} else {
						$wcfm_orders_json_arr[$index][] =  __( 'N/A', 'wc-frontend-manager' );
					}
				} else {
					$wcfm_orders_json_arr[$index][] =  wc_price( $commission, array( 'currency' => $order_currency ) );
				}
				
				// Date
				$order_date = ( version_compare( WC_VERSION, '2.7', '<' ) ) ? $the_order->order_date : $the_order->get_date_created();
				$wcfm_orders_json_arr[$index][] = date_i18n( wc_date_format(), strtotime( $order_date ) );
				
				// Action
				$actions = '';
				if( $wcfm_is_allow_order_status_update = apply_filters( 'wcfm_is_allow_order_status_update', true ) ) {
					$order_status = sanitize_title( $the_order->get_status() );
					if( !in_array( $order_status, array( 'failed', 'cancelled', 'refunded', 'completed' ) ) ) $actions = '<a class="wcfm_order_mark_complete wcfm-action-icon" href="#" data-orderid="' . $wcfm_orders_single->ID . '"><span class="fa fa-check-square-o text_tip" data-tip="' . esc_attr__( 'Mark as Complete', 'wc-frontend-manager' ) . '"></span></a>';
				}
  	
				if( $wcfm_is_allow_order_details = apply_filters( 'wcfm_is_allow_order_details', true ) ) {
					$actions .= '<a class="wcfm-action-icon" href="' . get_wcfm_view_order_url($wcfm_orders_single->ID, $the_order) . '"><span class="fa fa-eye text_tip" data-tip="' . esc_attr__( 'View Details', 'wc-frontend-manager' ) . '"></span></a>';
				}
				
				if( WCFM_Dependencies::wcfmu_plugin_active_check() && WCFM_Dependencies::wcfm_wc_pdf_invoices_packing_slips_plugin_active_check() ) {
					$actions .= '<a class="wcfm_pdf_invoice wcfm-action-icon" href="#" data-orderid="' . $wcfm_orders_single->ID . '"><span class="fa fa-file-pdf-o text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
					$actions .= '<a class="wcfm_pdf_packing_slip wcfm-action-icon" href="#" data-orderid="' . $wcfm_orders_single->ID . '"><span class="fa fa-file-powerpoint-o text_tip" data-tip="' . esc_attr__( 'PDF Packing Slip', 'wc-frontend-manager' ) . '"></span></a>';
				} else {
					if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
						$actions .= '<a class="wcfm_pdf_invoice_dummy wcfm-action-icon" href="#" data-orderid="' . $wcfm_orders_single->ID . '"><span class="fa fa-file-pdf-o text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
					}
				}
				
				$wcfm_orders_json_arr[$index][] =  apply_filters ( 'wcfm_orders_actions', $actions, $wcfm_orders_single, $the_order );
				
				$index++;
			}												
		}
		if( !empty($wcfm_orders_json_arr) ) $wcfm_orders_json .= json_encode($wcfm_orders_json_arr);
		else $wcfm_orders_json .= '[]';
		$wcfm_orders_json .= '
													}';
													
		echo $wcfm_orders_json;
	}
}