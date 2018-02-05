<?php
/**
 * Report class responsible for handling analytics reports.
 *
 * @since      2.6.3
 *
 * @package    WooCommerce Frontend Manager
 * @subpackage wcfm/includes/reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );

class WCFM_Report_Analytics extends WC_Admin_Report {
	public $chart_colors = array();
	public $current_range;
	private $report_data;

	/**
	 * Constructor
	 *
	 * @access public
	 * @since 2.1.0
	 * @version 2.1.0
	 * @return bool
	 */
	public function __construct() {
		global $WCFM;
		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->current_range = $current_range;
	}

	/**
	 * Get the report data
	 *
	 * @access public
	 * @since 2.1.0
	 * @version 2.1.0
	 * @return array of objects
	 */
	public function get_report_data() {
		global $WCFM;
		if ( empty( $this->report_data ) ) {
			$this->query_report_data();
		}

		return $this->report_data;
	}

	/**
	 * Get the report based on parameters
	 *
	 * @access public
	 * @since 2.1.0
	 * @version 2.1.0
	 * @return array of objects
	 */
	public function query_report_data() {
		global $wpdb, $WCFM;

		$this->report_data = new stdClass;

		$sql = "SELECT * FROM {$wpdb->prefix}wcfm_daily_analysis AS commission";

		$sql .= " WHERE 1=1";
		
		if( wcfm_is_vendor() ) {
			$sql .= " AND commission.author_id = %d";
			$sql .= " AND commission.is_store = 1";
		} else {
			$sql .= " AND commission.is_shop = 1";
		}
		$sql = wcfm_query_time_range_filter( $sql, 'visited', $this->current_range );	

		// Enable big selects for reports
		$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );

		if( wcfm_is_vendor() ) {
			$is_marketplace = wcfm_is_marketplace();
			if( $is_marketplace == 'wcpvendors' ) {
				$results = $wpdb->get_results( $wpdb->prepare( $sql, apply_filters( 'wcfm_current_vendor_id', WC_Product_Vendors_Utils::get_logged_in_vendor() ) ) );
			} else {
				$results = $wpdb->get_results( $wpdb->prepare( $sql, apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ) ) );
			}
		} else {
			$results = $wpdb->get_results( $sql );
		}

		$view_count                    = 0;

		foreach( $results as $data ) {
			$view_count = $data->count;
		}

		$this->report_data->view_count          = $view_count;
	}

	/**
	 * Get the legend for the main chart sidebar
	 * @return array
	 */
	public function get_chart_legend() {
		global $WCFM;
		$legend = array();
		$data   = $this->get_report_data();

		switch ( $this->chart_groupby ) {
			case 'day' :
				$average_sales_title = sprintf( __( '%s average daily sales', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->average_sales ) . '</strong>' );
			break;
			case 'month' :
			default :
				$average_sales_title = sprintf( __( '%s average monthly sales', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->average_sales ) . '</strong>' );
			break;
		}
		
		$legend[] = array(
			'title'            => sprintf( __( '%s total earned commission', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->total_earned ) . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the earned commission including shipping and taxes if applicable.', 'wc-frontend-manager' ),
			'color'            => $this->chart_colors['earned'],
			'highlight_series' => 4
		);

		return $legend;
	}

	/**
	 * Output the report
	 */
	public function output_report() {
		global $WCFM;
		$ranges = array(
			'year'         => __( 'Year', 'wc-frontend-manager' ),
			'last_month'   => __( 'Last Month', 'wc-frontend-manager' ),
			'month'        => __( 'This Month', 'wc-frontend-manager' ),
			'7day'         => __( 'Last 7 Days', 'wc-frontend-manager' ),
		);

		$this->chart_colors = array(
			'view_count'          => '#00897b',
		);

		$current_range = $this->current_range;

		$this->calculate_current_range( $this->current_range );

		include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php' );
	}

	/**
	 * Output an export link
	 */
	public function get_export_button() {
		global $WCFM;
		return;
		?>
		<a
			href="#"
			download="report-<?php echo esc_attr( $this->current_range ); ?>-<?php echo date_i18n( 'Y-m-d', current_time('timestamp') ); ?>.csv"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php esc_attr_e( 'Date', 'wc-frontend-manager' ); ?>"
			data-exclude_series="2"
			data-groupby="<?php echo $this->chart_groupby; ?>"
			data-range="<?php echo $this->current_range; ?>"
			data-custom-range="<?php echo 'custom' === $this->current_range ? $this->start_date . '-' . $this->end_date : ''; ?>"
		>
			<?php esc_html_e( 'Export CSV', 'wc-frontend-manager' ); ?>
		</a>
		<?php
	}

	/**
	 * Round our totals correctly
	 * @param  string $amount
	 * @return string
	 */
	private function round_chart_totals( $amount ) {
		global $WCFM;
		
		if ( is_array( $amount ) ) {
			return array( $amount[0], wc_format_decimal( $amount[1], wc_get_price_decimals() ) );
		} else {
			return wc_format_decimal( $amount, wc_get_price_decimals() );
		}
	}

	/**
	 * Get the main chart
	 *
	 * @return string
	 */
	public function get_main_chart( $show_legend = 0 ) {
		global $wp_locale, $wpdb, $WCFM;
		
		// Generate Data for total earned commision
		$select = "SELECT commission.count AS count, commission.visited";

		$sql = $select;
		$sql .= " FROM {$wpdb->prefix}wcfm_daily_analysis AS commission";
		$sql .= " WHERE 1=1";
		
		if( wcfm_is_vendor() ) {
			$sql .= " AND commission.author_id = %d";
			$sql .= " AND commission.is_store = 1";
		} else {
			$sql .= " AND commission.is_shop = 1";
		}
		$sql = wcfm_query_time_range_filter( $sql, 'visited', $this->current_range );

		$sql .= " GROUP BY DATE( commission.visited )";
			
		// Enable big selects for reports
		$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );
		
		if( wcfm_is_vendor() ) {
			$is_marketplace = wcfm_is_marketplace();
			if( $is_marketplace == 'wcpvendors' ) {
				$results = $wpdb->get_results( $wpdb->prepare( $sql, apply_filters( 'wcfm_current_vendor_id', WC_Product_Vendors_Utils::get_logged_in_vendor() ) ) );
			} else {
				$results = $wpdb->get_results( $wpdb->prepare( $sql, apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ) ) );
			}
		} else { 
			$results = $wpdb->get_results( $sql );
		}

		// Prepare data for report
		$view_count   = $this->prepare_chart_data( $results, 'visited', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby );
		$chart_data   = $WCFM->wcfm_prepare_chart_data( $view_count );
		?>
		<div class="chart-container">            
		  <div class="analytics-chart-placeholder main"><canvas id="analytics-chart-placeholder-canvas"></canvas></div>
		</div>
		<script type="text/javascript">

			jQuery(function() {
				var analytics_data = <?php echo $chart_data; ?>;
				var show_legend    = <?php echo $show_legend; ?>;
				
				jQuery('.analytics-chart-placeholder').css( 'width', jQuery('.analytics-chart-placeholder').outerWidth() + 'px' );
				
				var ctx = document.getElementById("analytics-chart-placeholder-canvas").getContext("2d");
				var myLineChart = new Chart(ctx, {
						type: 'line',
						data: {
							  labels: analytics_data.labels,
								datasets: [{
												label: "<?php _e( 'Daily Views', 'wc-frontend-manager' ); ?>",
												backgroundColor: color(window.chartColors.orange).alpha(0.2).rgbString(),
												borderColor: window.chartColors.orange,
												fill: true,
												data: analytics_data.datas,
											}]
						},
						options: {
							  responsive: true,
                title:{
                    text: "<?php _e( 'Store Analytics', 'wc-frontend-manager' ); ?>",
                    position: "bottom",
                },
                legend: {
									position: "bottom",
									display: show_legend,
								},
								scales: {
									xAxes: [{
										type: "time",
										time: {
											format: timeFormat,
											round: 'day',
											tooltipFormat: 'll'
										},
										scaleLabel: {
											display: false,
											labelString: 'Date'
										}
									}],
									yAxes: [{
										scaleLabel: {
											display: false,
											labelString: 'Views'
										}
									}]
								},
							}
						});
			});
		</script>
		<?php
	}
}
