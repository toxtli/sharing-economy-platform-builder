<?php
/**
 * WCFM plugin view
 *
 * WCFM Orders Dashboard View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   1.0.0
 */

global $WCFM;

$wcfm_is_allow_orders = apply_filters( 'wcfm_is_allow_orders', true );
if( !$wcfm_is_allow_orders ) {
	wcfm_restriction_message_show( "Orders" );
	return;
}

//require_once( $WCFM->plugin_path . 'controllers/orders/wcfm-controller-wcmarketplace-orders.php' );
//new WCFM_Orders_WCMarketplace_Controller();

$admin_fee_mode = apply_filters( 'wcfm_is_admin_fee_mode', false );
?>

<div class="collapse wcfm-collapse" id="wcfm_orders_listing">

  <div class="wcfm-page-headig">
		<span class="fa fa-shopping-cart"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Orders', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<?php
			if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
				?>
				<h2><?php _e('Orders Listing', 'wc-frontend-manager' ); ?></h2>
				<?php
				if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
					?>
					<div class="wcfm_orders_filter_wrap wcfm_filters_wrap">
						<select name="m" id="dummy-filter-by-date" disabled="disabled" title="<?php wcfmu_feature_help_text_show( 'Order Filter', false, true ); ?>">
							<option value='0'><?php esc_html_e( 'Show all dates', 'wc-frontend-manager' ); ?></option>
						</select>
					</div>
					<?php
				}
			}
			
			do_action( 'before_wcfm_orders' );
			
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
						?>
						<span class="wcfm_screen_manager_dummy text_tip" data-tip="<?php wcfmu_feature_help_text_show( 'Screen Manager', false, true ); ?>"><span class="fa fa-television"></span></span>
						<?php
					}
				} else {
					?>
					<a class="wcfm_screen_manager text_tip" href="#" data-screen="order" data-tip="<?php _e( 'Screen Manager', 'wc-frontend-manager' ); ?>"><span class="fa fa-television"></span></a>
					<?php
				}
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=shop_order'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fa fa-wordpress"></span></a>
				<?php
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
		<div class="wcfm-container">
			<div id="wwcfm_orders_listing_expander" class="wcfm-content">
				<table id="wcfm-orders" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>                                                                                      
							<th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Status', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( 'Order', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Purchased', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Gross Sales', 'wc-frontend-manager' ); ?></th>
							<?php if( $admin_fee_mode ) { ?>
								<th><?php _e( 'Fees', 'wc-frontend-manager' ); ?></th>
							<?php } elseif( wcfm_is_vendor() ) { ?>
								<th><?php _e( apply_filters( 'wcfm_vednors_earned_label', __( 'Earnings', 'wc-frontend-manager' ) ) ); ?></th>
							<?php } else { ?>
								<th><?php _e( apply_filters( 'wcfm_vednors_earned_commission_label', __( 'Commission', 'wc-frontend-manager' ) ) ); ?></th>
							<?php } ?>
							<th><?php _e( 'Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Status', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( 'Order', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Purchased', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Gross Sales', 'wc-frontend-manager' ); ?></th>
							<?php if( $admin_fee_mode ) { ?>
								<th><?php _e( 'Fees', 'wc-frontend-manager' ); ?></th>
							<?php } elseif( wcfm_is_vendor() ) { ?>
								<th><?php _e( apply_filters( 'wcfm_vednors_earned_label', __( 'Earnings', 'wc-frontend-manager' ) ) ); ?></th>
							<?php } else { ?>
								<th><?php _e( apply_filters( 'wcfm_vednors_earned_commission_label', __( 'Commission', 'wc-frontend-manager' ) ) ); ?></th>
							<?php } ?>
							<th><?php _e( 'Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_orders' );
		?>
	</div>
</div>