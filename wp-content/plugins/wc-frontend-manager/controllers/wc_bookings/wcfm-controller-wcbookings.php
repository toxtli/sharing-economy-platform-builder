<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Booking Dashboard Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.0.0
 */

class WCFM_WCBookings_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$wc_get_booking_status_name = array( 'paid' => __('Paid & Confirmed', 'wc-frontend-manager' ), 'pending-confirmation' => __('Pending Confirmation', 'wc-frontend-manager' ), 'unpaid' => __('Un-paid', 'wc-frontend-manager' ), 'cancelled' => __('Cancelled', 'wc-frontend-manager' ), 'complete' => __('Complete', 'wc-frontend-manager' ), 'confirmed' => __('Confirmed', 'wc-frontend-manager' ) );
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$include_bookings = apply_filters( 'wcfm_wcb_include_bookings', '' );
		
		$args = array(
							'posts_per_page'   => $length,
							'offset'           => $offset,
							'category'         => '',
							'category_name'    => '',
							'bookingby'          => 'date',
							'booking'            => 'DESC',
							'include'          => $include_bookings,
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'wc_booking',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array( 'complete', 'paid', 'confirmed', 'pending-confirmation', 'cancelled', 'unpaid' ),
							//'suppress_filters' => 0 
						);
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) $args['s'] = $_POST['search']['value'];
		if( isset( $_POST['booking_status'] ) && !empty( $_POST['booking_status'] ) ) { $args['post_status'] = $_POST['booking_status']; }
		if( isset( $_POST['booking_filter'] ) && !empty( $_POST['booking_filter'] ) ) { $args['meta_key'] = '_booking_product_id'; $args['meta_value'] = $_POST['booking_filter']; }
		
		$args = apply_filters( 'wcfm_bookings_args', $args );
		
		$wcfm_bookings_array = get_posts( $args );
		
		// Get Product Count
		$booking_count = 0;
		$filtered_booking_count = 0;
		$wcfm_bookings_count = wp_count_posts('wc_booking');
		$booking_count = count($wcfm_bookings_array);
		// Get Filtered Post Count
		$args['posts_per_page'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_bookings_array = get_posts( $args );
		$filtered_booking_count = count($wcfm_filterd_bookings_array);
		
		
		// Generate Products JSON
		$wcfm_bookings_json = '';
		$wcfm_bookings_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $booking_count . ',
															"recordsFiltered": ' . $filtered_booking_count . ',
															"data": ';
		if(!empty($wcfm_bookings_array)) {
			$index = 0;
			$wcfm_bookings_json_arr = array();
			foreach($wcfm_bookings_array as $wcfm_bookings_single) {
				$the_booking = new WC_Booking( $wcfm_bookings_single->ID );
				$product_id  = $the_booking->get_product_id( 'edit' );
				$product     = $the_booking->get_product( $product_id );
				$the_order   = $the_booking->get_order();
				
				if ( $the_booking->has_status( array( 'was-in-cart', 'in-cart' ) ) ) continue;
				
				// Status
				$wcfm_bookings_json_arr[$index][] =  '<span class="booking-status tips wcicon-status-' . sanitize_title( $the_booking->get_status( ) ) . ' text_tip" data-tip="' . $wc_get_booking_status_name[$the_booking->get_status()] . '"></span>';
				
				// Booking
				$booking_label =  '<a href="' . get_wcfm_view_booking_url($wcfm_bookings_single->ID, $the_booking) . '" class="wcfm_booking_title">' . __( '#', 'wc-frontend-manager' ) . $wcfm_bookings_single->ID . '</a>';
				
				if( apply_filters( 'wcfm_allow_order_customer_details', true ) ) {
					$customer = $the_booking->get_customer();
					if ( ! isset( $customer->user_id ) || 0 == $customer->user_id ) {
						$booking_label .= ' by ';
						if( $the_order ) {
							$guest_name = $the_order->get_meta('_billing_first_name') . ' ' . $the_order->get_meta('_billing_last_name');
							$guest_name = apply_filters( 'wcfm_booking_by_user', $guest_name, $wcfm_bookings_single->ID, $the_order->get_order_number() ); 
							$booking_label .= sprintf( _x( 'Guest(%s)', 'Guest string with name from booking order in brackets', 'wc-frontend-manager' ), $guest_name );
						} else {
							$booking_label .= sprintf( _x( 'Guest(%s)', 'Guest string with name from booking order in brackets', 'wc-frontend-manager' ), '&ndash;' );
						}
					} elseif ( $customer ) {
						if( $the_order ) {
							$guest_name = apply_filters( 'wcfm_booking_by_user', $customer->name, $wcfm_bookings_single->ID, $the_order->get_order_number() );
							$booking_label .= ' by ';
							$booking_label .= '<a href="mailto:' .  $customer->email . '">' . $guest_name . '</a>';
						} else {
							$guest_name = $customer->name;
							$booking_label .= ' by ';
							$booking_label .= '<a href="mailto:' .  $customer->email . '">' . $guest_name . '</a>';
						}
					}
				}
				$wcfm_bookings_json_arr[$index][] = $booking_label;
				
				// Product
				//$resource = $the_booking->get_resource();

				if ( $product ) {
					$product_post = get_post($product->get_ID());
					$wcfm_bookings_json_arr[$index][] = $product_post->post_title;
					//if ( $resource ) {
						//$wcfm_bookings_json_arr[$index][] = $resource->post_title;
					//}
				} else {
					$wcfm_bookings_json_arr[$index][] = '&ndash;';
				}
				
				// #of Persons
				/*$persons = get_post_meta( $wcfm_bookings_single->ID, '_booking_persons', true );
				$total_persons = 0;
				if ( ! empty( $persons ) && is_array( $persons ) ) {
					foreach ( $persons as $person_count ) {
						$total_persons = $total_persons + $person_count;
					}
				}

				$wcfm_bookings_json_arr[$index][] =  esc_html( $total_persons );*/
				
				// Order
				if ( $the_order ) {
					if( $is_allow_order_status_details = apply_filters( 'wcfm_allow_order_details', true ) ) {
						$wcfm_bookings_json_arr[$index][] = '<span class="booking-orderno">#' . $the_order->get_order_number() . '</span><br /> ' . esc_html( wc_get_order_status_name( $the_order->get_status() ) );
					} else {
						$wcfm_bookings_json_arr[$index][] = '<span class="booking-orderno"><a href="' . get_wcfm_view_order_url( $the_order->get_order_number(), $order ) . '">#' . $the_order->get_order_number() . '</a></span><br />' . esc_html( wc_get_order_status_name( $the_order->get_status() ) );
					}
				} else {
					$wcfm_bookings_json_arr[$index][] = '&ndash;';
				}
				
				// Start Date
				$wcfm_bookings_json_arr[$index][] = date_i18n( wc_date_format() . ' ' . wc_time_format(), $the_booking->get_start( 'edit' ) );
				
				// End Date
				$wcfm_bookings_json_arr[$index][] = date_i18n( wc_date_format() . ' ' . wc_time_format(), $the_booking->get_end( 'edit' ) );
				
				// Action
				$actions = '';
				if ( current_user_can( 'manage_bookings' ) ) {
					if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					 if ( in_array( $the_booking->get_status(), array( 'pending-confirmation' ) ) ) $actions = '<a class="wcfm_booking_mark_confirm wcfm-action-icon" href="#" data-bookingid="' . $wcfm_bookings_single->ID . '"><span class="fa fa-check-square-o text_tip" data-tip="' . esc_attr__( 'Mark as Confirmed', 'wc-frontend-manager' ) . '"></span></a>';
					}
				}
				$actions .= apply_filters ( 'wcfm_bookings_actions', '<a class="wcfm-action-icon" href="' . get_wcfm_view_booking_url( $wcfm_bookings_single->ID, $the_booking ) . '"><span class="fa fa-eye text_tip" data-tip="' . esc_attr__( 'View Details', 'wc-frontend-manager' ) . '"></span></a>', $wcfm_bookings_single, $the_booking );
				$wcfm_bookings_json_arr[$index][] = $actions;  
				
				
				$index++;
			}												
		}
		if( !empty($wcfm_bookings_json_arr) ) $wcfm_bookings_json .= json_encode($wcfm_bookings_json_arr);
		else $wcfm_bookings_json .= '[]';
		$wcfm_bookings_json .= '
													}';
													
		echo $wcfm_bookings_json;
	}
}