<?php
/**
 * WCFM plugin views
 *
 * Plugin WC Bookings List Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views
 * @version   2.2.1
 */
 
global $WCFM, $WCFMu;

if( !current_user_can( 'manage_bookings' ) ) {
	wcfm_restriction_message_show( "Bookings" );
	return;
}

$wcfmu_bookings_menus = apply_filters( 'wcfmu_bookings_menus', array( 'all' => __( 'All', 'wc-frontend-manager'), 
																																			'complete' => __('Complete', 'wc-frontend-manager' ), 
																																			'paid' => __('Paid & Confirmed', 'wc-frontend-manager' ),
																																			'confirmed' => __('Confirmed', 'wc-frontend-manager' ),
																																			'pending-confirmation' => __('Pending Confirmation', 'wc-frontend-manager' ),
																																			'cancelled' => __('Cancelled', 'wc-frontend-manager' ),
																																			//'unpaid' => __('Un-paid', 'wc-frontend-manager' ), 
																																			) );

$booking_status = ! empty( $_GET['booking_status'] ) ? sanitize_text_field( $_GET['booking_status'] ) : 'all';

include_once( WC_BOOKINGS_ABSPATH . 'includes/admin/class-wc-bookings-admin.php' );

?>
<div class="collapse wcfm-collapse" id="wcfm_bookings_listing">
  <div class="wcfm-page-headig">
		<span class="fa fa-calendar"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Bookings List', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
	  <div class="wcfm-container wcfm-top-element-container">
			<ul class="wcfm_bookings_menus">
				<?php
				$is_first = true;
				foreach( $wcfmu_bookings_menus as $wcfmu_bookings_menu_key => $wcfmu_bookings_menu) {
					?>
					<li class="wcfm_bookings_menu_item">
						<?php
						if($is_first) $is_first = false;
						else echo " | ";
						?>
						<a class="<?php echo ( $wcfmu_bookings_menu_key == $booking_status ) ? 'active' : ''; ?>" href="<?php echo get_wcfm_bookings_url( $wcfmu_bookings_menu_key ); ?>"><?php echo $wcfmu_bookings_menu; ?></a>
					</li>
					<?php
				}
				?>
			</ul>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a class="wcfm_screen_manager text_tip" href="#" data-screen="booking" data-tip="<?php _e( 'Screen Manager', 'wc-frontend-manager' ); ?>"><span class="fa fa-television"></span></a>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=wc_booking'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fa fa-wordpress"></span></a>
				<?php
			}
			
			if( $wcfm_is_allow_manual_booking = apply_filters( 'wcfm_is_allow_manual_booking', true ) ) {
				if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_create_bookings_url().'" data-tip="' . __( 'Create Booking', 'wc-frontend-manager' ) . '"><span class="fa fa-calendar-plus-o"></span></a>';
				}
			}
			
			if( $wcfm_is_allow_manage_resource = apply_filters( 'wcfm_is_allow_manage_resource', true ) ) {
				if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_bookings_resources_url().'" data-tip="' . __( 'Manage Resources', 'wc-frontend-manager' ) . '"><span class="fa fa-briefcase"></span></a>';
				}
			}
			
			if( $has_new = apply_filters( 'wcfm_add_new_product_sub_menu', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Create Bookable', 'wc-frontend-manager') . '"><span class="fa fa-cube"></span></a>';
			}
			
			if( $wcfm_is_allow_booking_calendar = apply_filters( 'wcfm_is_allow_booking_calendar', true ) ) {
				if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_bookings_calendar_url().'" data-tip="'. __('Calendar View', 'wc-frontend-manager') .'"><span class="fa fa-calendar-o"></span></a>';
				}
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
		
		<div class="wcfm_bookings_filter_wrap wcfm_filters_wrap">
		  <select id="dropdown_booking_filter" name="filter_bookings" style="width:200px">
				<option value=""><?php _e( 'Filter Bookings', 'woocommerce-bookings' ); ?></option>
				<?php if ( $product_filters = WC_Bookings_Admin::get_booking_products() ) : ?>
					<optgroup label="<?php _e( 'By appointable product', 'woocommerce-bookings' ); ?>">
						<?php foreach ( $product_filters as $product_filter ) : ?>
							<option value="<?php echo $product_filter->get_id(); ?>"><?php echo $product_filter->get_name(); ?></option>
						<?php endforeach; ?>
					</optgroup>
				<?php endif; ?>
			</select>
		</div>
		
		<?php do_action( 'before_wcfm_bookings' ); ?>
	
		<div class="wcfm-container">
			<div id="wwcfm_bookings_listing_expander" class="wcfm-content">
				<table id="wcfm-bookings" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Status', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( 'Booking', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Product', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Order', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Start Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'End Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Status', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( 'Booking', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Product', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Order', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Start Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'End Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_bookings' );
		?>
	</div>
</div>