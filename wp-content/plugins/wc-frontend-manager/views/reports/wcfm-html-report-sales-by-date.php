<?php
/**
 * Admin View: Report by Date (with date filters)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp, $WCFM, $wpdb;

?>

<div id="poststuff" class="woocommerce-reports-wide">
	<div class="postbox">

		<div class="stats_range">
		  <?php $wcfm_report_sales_by_date->get_export_button(); ?>
			<ul>
				<?php
					foreach ( $ranges as $range => $name ) {
						echo '<li class="' . ( $current_range == $range ? 'active' : '' ) . '"><a href="' . esc_url( remove_query_arg( array( 'start_date', 'end_date' ), add_query_arg( 'range', $range ) ) ) . '">' . $name . '</a></li>';
					}
					do_action( 'wcfm_report_sales_by_date_filters' );
				?>
			</ul>
		</div>
		<?php if ( empty( $hide_sidebar ) ) : ?>
			<div class="inside chart-with-sidebar">
				<div class="chart-sidebar">
					<?php if ( $legends = $wcfm_report_sales_by_date->get_chart_legend() ) : ?>
						<ul class="chart-legend">
							<?php foreach ( $legends as $legend ) : ?>
								<li style="border-color: <?php echo $legend['color']; ?>" <?php if ( isset( $legend['highlight_series'] ) ) echo 'class="highlight_series ' . ( isset( $legend['placeholder'] ) ? 'tips' : '' ) . '" data-series="' . esc_attr( $legend['highlight_series'] ) . '"'; ?> data-tip="<?php echo isset( $legend['placeholder'] ) ? $legend['placeholder'] : ''; ?>">
									<?php echo $legend['title']; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<ul class="chart-widgets">
						<?php foreach ( $wcfm_report_sales_by_date->get_chart_widgets() as $widget ) : ?>
							<li class="chart-widget">
								<?php if ( $widget['title'] ) : ?><h4><?php echo $widget['title']; ?></h4><?php endif; ?>
								<?php call_user_func( $widget['callback'] ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="main">
					<?php $wcfm_report_sales_by_date->get_main_chart(); ?>
				</div>
			</div>
		<?php else : ?>
			<div class="inside">
				<?php $wcfm_report_sales_by_date->get_main_chart(); ?>
			</div>                   
		<?php endif; ?>
	</div>
</div>
