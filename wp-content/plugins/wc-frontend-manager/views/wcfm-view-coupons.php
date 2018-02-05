<?php
global $WCFM;

$wcfm_is_allow_manage_coupons = apply_filters( 'wcfm_is_allow_manage_coupons', true );
if( !current_user_can( 'edit_shop_coupons' ) || !$wcfm_is_allow_manage_coupons ) {
	wcfm_restriction_message_show( "Coupons" );
	return;
}

?>

<div class="collapse wcfm-collapse" id="wcfm_coupons_listing">

  <div class="wcfm-page-headig">
		<span class="fa fa-gift"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Coupons', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		<?php do_action( 'before_wcfm_coupons' ); ?>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('Coupons Listing', 'wc-frontend-manager' ); ?></h2>
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
						?>
						<span class="wcfm_screen_manager_dummy text_tip" data-tip="<?php wcfmu_feature_help_text_show( 'Screen Manager', false, true ); ?>"><span class="fa fa-television"></span></span>
						<?php
					}
				} else {
					?>
					<a class="wcfm_screen_manager text_tip" href="#" data-screen="coupon" data-tip="<?php _e( 'Screen Manager', 'wc-frontend-manager' ); ?>"><span class="fa fa-television"></span></a>
					<?php
				}
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=shop_coupon'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fa fa-wordpress"></span></a>
				<?php
			}
			if( $has_new = apply_filters( 'wcfm_add_new_coupon_sub_menu', true ) ) {
				echo '<a id="add_new_coupon_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_coupons_manage_url().'" data-tip="' . __('Add New Coupon', 'wc-frontend-manager') . '"><span class="fa fa-gift"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager') . '</span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
		<div class="wcfm-container">
			<div id="wcfm_coupons_listing_expander" class="wcfm-content">
				<table id="wcfm-coupons" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php _e( 'Code', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Type', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Amt', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Usage Limit', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Expiry date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Action', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Code', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Type', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Amt', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Usage Limit', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Expiry date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Action', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_coupons' );
		?>
	</div>
</div>