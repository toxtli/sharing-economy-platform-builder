<?php
/**
 * Admin View: Report by Vendor (with date filters)
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $WCMp;

?>

<div id="poststuff" class="woocommerce-reports-wide">
	<div class="postbox">
		<h3 class="stats_range">
			<ul>
				<?php
					foreach ( $ranges as $range => $name ) {
						echo '<li class="' . ( $current_range == $range ? 'active' : '' ) . '"><a href="' . esc_url( remove_query_arg( array( 'start_date', 'end_date' ), add_query_arg( 'range', $range ) ) ) . '">' . $name . '</a></li>';
					}
				?>
				<li class="custom <?php echo $current_range == 'custom' ? 'active' : ''; ?>">
					<?php _e( 'Custom', 'dc-woocommerce-multi-vendor' ); ?>
					<form method="GET">
						<div>
							<?php
								// Maintain query string
								foreach ( $_GET as $key => $value ) {
									if ( is_array( $value ) ) {
										foreach ( $value as $v ) {
											echo '<input type="hidden" name="' . esc_attr( sanitize_text_field( $key ) ) . '[]" value="' . esc_attr( sanitize_text_field( $v ) ) . '" />';
										}
									} else {
										echo '<input type="hidden" name="' . esc_attr( sanitize_text_field( $key ) ) . '" value="' . esc_attr( sanitize_text_field( $value ) ) . '" />';
									}
								}
							?>
							<input type="hidden" name="range" value="custom" />
							<input type="text" size="9" placeholder="yyyy-mm-dd" value="<?php if ( ! empty( $_GET['start_date'] ) ) echo esc_attr( $_GET['start_date'] ); ?>" name="start_date" class="range_datepicker from" />
							<input type="text" size="9" placeholder="yyyy-mm-dd" value="<?php if ( ! empty( $_GET['end_date'] ) ) echo esc_attr( $_GET['end_date'] ); ?>" name="end_date" class="range_datepicker to" />
							<input type="submit" class="button" value="<?php esc_attr_e( 'Go', 'dc-woocommerce-multi-vendor' ); ?>" />
						</div>
					</form>
				</li>
			</ul>
		</h3>
		<div class="left_align pad_left" style="float: left">
			<form method="post" action="">
				<p>
					<select id="vendor" name="vendor" class="ajax_chosen_select_vendor vendor_info" data-placeholder="<?php _e( 'Search for a vendor...', 'dc-woocommerce-multi-vendor' ); ?>" style="min-width:210px;">
						<?php echo $option; ?>
					</select>
					<input type="button" style="vertical-align: top;" class="vendor_report_search" value="<?php _e( 'Show', 'dc-woocommerce-multi-vendor' ); ?>" />
				</p>
			</form>
		</div>
	</div>
	<div class="postbox box_data">
		<?php if( !empty($report_sort_arr) ) { ?>
			<div class="sorting_box">
				<span><b><?php _e( 'Sort By : ', 'dc-woocommerce-multi-vendor' ); ?></b></span>
				<select name="vendor_report_sort" class="vendor_report_sort">
					<option value="total_sales"><?php _e( 'Total Sales', 'dc-woocommerce-multi-vendor' ); ?></option>
					<option value="admin_earning"><?php _e( 'Total Earnings', 'dc-woocommerce-multi-vendor' ); ?></option>
				</select>
				<input type="checkbox" class="low_to_high" name="low_to_high" value="checked" />
				<button class="low_to_high_btn_vendor"><i class="fa fa-arrow-up"></i></button>
				<input type="checkbox" class="high_to_low" name="high_to_low" value="checked" checked />
				<button class="high_to_low_btn_vendor"><i class="fa fa-arrow-down"></i></button>
			</div>
		<?php } ?>
		<div class="sort_chart">
			<?php echo $html_chart; ?>
		</div>
	</div>
</div>
