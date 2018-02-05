<?php
/**
 * WCFM plugin views
 *
 * Plugin WC Booking Details Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views
 * @version   2.2.1
 */
 
global $wp, $WCFM, $WCFMu, $thebooking, $wpdb;

if( !current_user_can( 'manage_bookings' ) ) {
	wcfm_restriction_message_show( "Bookings" );
	return;
}

if ( ! is_object( $thebooking ) ) {
	if( isset( $wp->query_vars['wcfm-bookings-details'] ) && !empty( $wp->query_vars['wcfm-bookings-details'] ) ) {
		$thebooking = get_wc_booking( $wp->query_vars['wcfm-bookings-details'] );
	}
}

$booking_id = $wp->query_vars['wcfm-bookings-details'];
$post = get_post($booking_id);
$booking = new WC_Booking( $post->ID );
$order             = $booking->get_order();
$product_id        = $booking->get_product_id( 'edit' );
$resource_id       = $booking->get_resource_id( 'edit' );
$customer_id       = $booking->get_customer_id( 'edit' );
$product           = $booking->get_product( $product_id );
$resource          = new WC_Product_Booking_Resource( $resource_id );
$customer          = $booking->get_customer();
$statuses          = array_unique( array_merge( get_wc_booking_statuses( null, true ), get_wc_booking_statuses( 'user', true ), get_wc_booking_statuses( 'cancel', true ) ) );

do_action( 'before_wcfm_bookings_details' );
?>

<div class="collapse wcfm-collapse" id="wcfm_booking_details">

  <div class="wcfm-page-headig">
		<span class="fa fa-calendar-check-o"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Booking Details', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e( 'Booking #', 'wc-frontend-manager' ); echo $booking_id; ?></h2>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('post.php?post='.$booking_id.'&action=edit'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fa fa-wordpress"></span></a>
				<?php
			}
			
			if( $wcfm_is_allow_booking_calendar = apply_filters( 'wcfm_is_allow_booking_calendar', true ) ) {
				if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_bookings_calendar_url().'" data-tip="'. __('Calendar View', 'wc-frontend-manager') .'"><span class="fa fa-calendar-o"></span></a>';
				}
			}
			
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_bookings_url().'" data-tip="' . __( 'Bookings List', 'wc-frontend-manager' ) . '"><span class="fa fa-calendar"></span></a>';
			
			if( $wcfm_is_allow_manage_resource = apply_filters( 'wcfm_is_allow_manage_resource', true ) ) {
				if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_bookings_resources_url().'" data-tip="' . __( 'Manage Resources', 'wc-frontend-manager' ) . '"><span class="fa fa-briefcase"></span></a>';
				}
			}
			
			if( $has_new = apply_filters( 'wcfm_add_new_product_sub_menu', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Create Bookable', 'wc-frontend-manager') . '"><span class="fa fa-cube"></span></a>';
			}
			?>
			<div class="wcfm_clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'begin_wcfm_bookings_details' ); ?>
		
		<!-- collapsible -->
		<div class="page_collapsible bookings_details_general" id="wcfm_general_options">
			<?php _e('Overview', 'wc-frontend-manager'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="bookings_details_general_expander" class="wcfm-content">
	
				<p class="form-field form-field-wide">
					<label for="booking_date"><?php _e( 'Booking Created:', 'wc-frontend-manager' ) ?></label>
					<?php echo date_i18n( wc_date_format() . ' @' . wc_time_format(), $booking->get_date_created() ); ?>
				</p>
				
				<p class="form-field form-field-wide">
					<label for="booking_date"><?php _e( 'Order Number:', 'wc-frontend-manager' ) ?></label>
					<?php
					if ( $order ) {
						if( $is_allow_order_status_details = apply_filters( 'wcfm_allow_order_details', true ) ) {
							echo '<span class="booking-orderno"><a href="' . get_wcfm_view_order_url( $order->get_order_number(), $order ) . '">#' . $order->get_order_number() . '</a></span> &ndash; ' . esc_html( wc_get_order_status_name( $order->get_status() ) ) . '(' . date_i18n( wc_date_format(), strtotime( $order->get_date_created() ) ) . ')';
						} else {
							echo '<span class="booking-orderno">#' . $order->get_order_number() . ' - ' . esc_html( wc_get_order_status_name( $order->get_status() ) ) . '</span>';
						}
					} else {
						echo '-';
					}
					?>
				</p>
				
				<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
					<p class="form-field form-field-wide">
						<label for="wcfm_booking_status"><?php _e( 'Booking Status:', 'woocommerce-bookings' ); ?></label>
						<select id="wcfm_booking_status" name="booking_status">
							<?php
								foreach ( $statuses as $key => $value ) {
									echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $post->post_status, false ) . '>' . esc_html__( $value, 'woocommerce-bookings' ) . '</option>';
								}
							?>
						</select>
						<button class="wcfm_modify_booking_status button" id="wcfm_modify_booking_status" data-bookingid="<?php echo $booking_id; ?>"><?php _e( 'Update', 'wc-frontend-manager' ); ?></button>
					</p>
				<?php } ?>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<br />
		<!-- collapsible End -->
		
		<!-- collapsible -->
		<div class="page_collapsible bookings_details_booking" id="wcfm_booking_options">
			<?php _e('Booking', 'wc-frontend-manager'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="bookings_details_booking_expander" class="wcfm-content">
				
				<p class="form-field form-field-wide">
					<label for="booked_product"><?php _e( 'Booked Product:', 'woocommerce-bookings' ) ?></label>
					<?php
					
					if ( $product ) {
						$product_post = get_post($product->get_ID());
						echo '<a href="' . get_permalink($product->get_ID()) . '" target="_blank">' . $product_post->post_title . '</a>';
					} else {
						echo '-';
					}
					?>
				</p>
				
				<?php if( $resource_id ) { ?>
					<p class="form-field form-field-wide">
						<label for="booked_product"><?php _e( 'Resource:', 'woocommerce-bookings' ) ?></label>
						<?php
							echo esc_html( $resource->post_title );
						?>
					</p>
				<?php } ?>
				
				<?php
				if ( $product && is_callable( array( $product, 'get_person_types' ) ) ) {
					$person_types  = $product->get_person_types();
					$person_counts = $booking->get_person_counts();
					if ( ! empty( $person_types ) && is_array( $person_types ) ) {
				?>
						<p class="form-field form-field-wide">
							<label for="booked_product"><?php _e( 'Person(s):', 'woocommerce-bookings' ) ?></label>
							<?php 
							$pfirst = true;
							foreach ( $person_types as $person_type ) {
								if( !$pfirst ) echo ', ';
								echo $pfirst = false;
								echo $person_type->get_name() . ' (';
								if( isset( $person_counts[ $person_type->get_id() ] ) ) { echo $person_counts[ $person_type->get_id() ]; } else { echo '0'; }
								echo ')';
							} 
							?>
						</p>
				<?php }
				}
				?>
				
				<p class="form-field form-field-wide">
					<label for="booking_date"><?php _e( 'Booking Start Date:', 'woocommerce-bookings' ) ?></label>
					<?php echo apply_filters( 'wcfm_booking_start_date', date_i18n( wc_date_format() . ' ' . wc_time_format(), $booking->get_start( 'edit' ) ), $booking ); ?>
				</p>
				
				<p class="form-field form-field-wide">
					<label for="booking_date"><?php _e( 'Booking End Date:', 'woocommerce-bookings' ) ?></label>
					<?php echo apply_filters( 'wcfm_booking_end_date', date_i18n( wc_date_format() . ' ' . wc_time_format(), $booking->get_end( 'edit' ) ), $booking ); ?>
				</p>
				<p class="form-field form-field-wide">
					<label for="booking_date"><?php _e( 'All day booking:', 'woocommerce-bookings' ) ?></label>
					<?php echo $booking->get_all_day( 'edit' ) ? 'YES' : 'NO'; ?>
				</p>
				
				<?php do_action( 'wcfm_booking_details_block', $booking, $product ); ?>
		 </div>
		</div>
		<div class="wcfm_clearfix"></div>
		<br />
		<!-- collapsible End -->
		
		<!-- collapsible -->
		<div class="page_collapsible bookings_details_customer" id="wcfm_customer_options">
			<?php _e('Customer', 'woocommerce-bookings'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="bookings_details_customer_expander" class="wcfm-content">
				<?php
				$customer_id = get_post_meta( $post->ID, '_booking_customer_id', true );
				$order_id    = $post->post_parent;
				$has_data    = false;
		
				echo '<table class="booking-customer-details">';
				
				if ( $customer_id && ( $user = get_user_by( 'id', $customer_id ) ) ) {
					echo '<tr>';
						echo '<th>' . __( 'Name:', 'woocommerce-bookings' ) . '</th>';
						echo '<td>';
						if ( $user->last_name && $user->first_name ) {
							echo $user->first_name . ' ' . $user->last_name;
						} else {
							echo '-';
						}
						echo '</td>';
					echo '</tr>';
					
					if( apply_filters( 'wcfm_allow_order_customer_details', true ) ) {
						echo '<tr>';
							echo '<th>' . __( 'User Email:', 'woocommerce-bookings' ) . '</th>';
							echo '<td>';
							echo '<a href="mailto:' . esc_attr( $user->user_email ) . '">' . esc_html( $user->user_email ) . '</a>';
							echo '</td>';
						echo '</tr>';
					}
			
					$has_data = true;
				}
		
				if ( $order_id && ( $order = wc_get_order( $order_id ) ) ) {
					if( apply_filters( 'wcfm_allow_customer_billing_details', true ) ) {
						echo '<tr>';
							echo '<th>' . __( 'Address:', 'woocommerce-bookings' ) . '</th>';
							echo '<td>';
							if ( $order->get_formatted_billing_address() ) {
								echo wp_kses( $order->get_formatted_billing_address(), array( 'br' => array() ) );
							} else {
								echo __( 'No billing address set.', 'woocommerce-bookings' );
							}
							echo '</td>';
						echo '</tr>';
					}
					
					if( apply_filters( 'wcfm_allow_order_customer_details', true ) ) {
						echo '<tr>';
							echo '<th>' . __( 'Email:', 'wc-frontend-manager' ) . '</th>';
							echo '<td>';
							echo '<a href="mailto:' . esc_attr( $order->get_billing_email() ) . '">' . esc_html( $order->get_billing_email() ) . '</a>';
							echo '</td>';
						echo '</tr>';
						echo '<tr>';                                    
							echo '<th>' . __( 'Phone:', 'wc-frontend-manager' ) . '</th>';
							echo '<td>';
							echo esc_html( $order->get_billing_phone() );
							echo '</td>';
						echo '</tr>';
					}
					
					if( $is_allow_order_status_details = apply_filters( 'wcfm_allow_order_details', true ) ) {
						echo '<tr class="view">';
							echo '<th>&nbsp;</th>';
							echo '<td>';
							echo '<a class="button" target="_blank" href="' . get_wcfm_view_order_url( $order_id ) . '">' . __( 'View Order', 'woocommerce-bookings' ) . '</a>';
							echo '</td>';
						echo '</tr>';
					}
		
					$has_data = true;
				}
		
				if ( ! $has_data ) {
					echo '<tr>';
						echo '<td colspan="2">' . __( 'N/A', 'woocommerce-bookings' ) . '</td>';
					echo '</tr>';
				}
				do_action( 'wcfm_booking_details_customer_block' );
				echo '</table>';
				?>
			</div>
		</div>
	</div>
</div>