<?php
global $WCFM;

$is_allow_vendors = apply_filters( 'wcfm_is_allow_vendors', true );
if( !$is_allow_vendors ) {
	wcfm_restriction_message_show( "Vendors" );
	return;
}

$ranges = array(
	'7day'         => __( 'Last 7 Days', 'wc-frontend-manager' ),
	'month'        => __( 'This Month', 'wc-frontend-manager' ),
	'last_month'   => __( 'Last Month', 'wc-frontend-manager' ),
	'year'         => __( 'Year', 'wc-frontend-manager' ),
);

$admin_fee_mode = apply_filters( 'wcfm_is_admin_fee_mode', false );
?>

<div class="collapse wcfm-collapse" id="wcfm_coupons_listing">

  <div class="wcfm-page-headig">
		<span class="fa fa-user-o"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Vendors', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		<?php do_action( 'before_wcfm_vendors' ); ?>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('Vendors Listing', 'wc-frontend-manager' ); ?></h2>
			<?php
			if( $has_new = apply_filters( 'wcfm_add_new_coupon_sub_menu', true ) ) {
				//echo '<a id="add_new_coupon_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_coupons_manage_url().'" data-tip="' . __('Add New Coupon', 'wc-frontend-manager') . '"><span class="fa fa-gift"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager') . '</span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
		
		<div class="wcfm_vendors_filter_wrap wcfm_filters_wrap">
		  <label style="margin-left: 10px;">
				<?php
				echo '&nbsp;&nbsp;<select id="dropdown_report_filter" name="dropdown_report_filter" class="dropdown_report_filter" style="width: 150px;">';
					if ( $ranges ) {
						foreach( $ranges as $range => $range_label ) {
							echo '<option value="' . $range . '">' . $range_label . '</option>';
						}
					}
				echo '</select>';
				?>
			</label>
			<?php
			if( $wcfm_is_products_vendor_filter = apply_filters( 'wcfm_is_products_vendor_filter', true ) ) {
				$is_marketplace = wcfm_is_marketplace();
				if( $is_marketplace ) {
					if( !wcfm_is_vendor() ) {
						$vendor_arr = $WCFM->wcfm_vendor_support->wcfm_get_vendor_list();
						$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																											"dropdown_vendor" => array( 'type' => 'select', 'options' => $vendor_arr, 'attributes' => array( 'style' => 'width: 150px;' ) )
																											 ) );
					}
				}
			}
			?>
		</div>
		
		<div class="wcfm-container">
			<div id="wcfm_vendors_listing_expander" class="wcfm-content">
				<table id="wcfm-vendors" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php _e( 'Vendor', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Store', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Membership', 'wc-frontend-manager' ); ?></th>
							<th><span class="fa fa-cubes text_tip" data-tip="<?php _e( 'No. of Products', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( apply_filters( 'wcfm_vednors_gross_sales_label', __( 'Gross Sales', 'wc-frontend-manager' ) ) ); ?></th>
							<?php if( $admin_fee_mode ) { ?>
								<th><?php _e( apply_filters( 'wcfm_vednors_total_fees_label', __( 'Total Fees', 'wc-frontend-manager' ) ) ); ?></th>
								<th><?php _e( apply_filters( 'wcfm_vednors_paid_fees_label', __( 'Paid Fees', 'wc-frontend-manager' ) ) ); ?></th>
							<?php } else { ?>
								<th><?php _e( apply_filters( 'wcfm_vednors_earned_commission_label', __( 'Total Earnings', 'wc-frontend-manager' ) ) ); ?></th>
								<th><?php _e( apply_filters( 'wcfm_vednors_received_commission_label', __( 'Total Withdrawal', 'wc-frontend-manager' ) ) ); ?></th>
							<?php } ?>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Vendor', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Store', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Membership', 'wc-frontend-manager' ); ?></th>
							<th><span class="fa fa-cubes text_tip" data-tip="<?php _e( 'No. of Products', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( apply_filters( 'wcfm_vednors_gross_sales_label', __( 'Gross Sales', 'wc-frontend-manager' ) ) ); ?></th>
							<?php if( $admin_fee_mode ) { ?>
								<th><?php _e( apply_filters( 'wcfm_vednors_total_fees_label', __( 'Total Fees', 'wc-frontend-manager' ) ) ); ?></th>
								<th><?php _e( apply_filters( 'wcfm_vednors_paid_fees_label', __( 'Paid Fees', 'wc-frontend-manager' ) ) ); ?></th>
							<?php } else { ?>
								<th><?php _e( apply_filters( 'wcfm_vednors_earned_commission_label', __( 'Total Earnings', 'wc-frontend-manager' ) ) ); ?></th>
								<th><?php _e( apply_filters( 'wcfm_vednors_received_commission_label', __( 'Total Withdrawal', 'wc-frontend-manager' ) ) ); ?></th>
							<?php } ?>
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