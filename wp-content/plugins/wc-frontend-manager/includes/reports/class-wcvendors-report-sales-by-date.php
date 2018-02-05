<?php
/**
 * Report class responsible for handling sales by date reports.
 *
 * @since      2.1.0
 *
 * @package    WooCommerce Frontend Manager
 * @subpackage wcfm/includes/reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );

class WC_Vendors_Report_Sales_By_Date extends WC_Admin_Report {
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
	public function __construct( $current_range = '' ) {
		global $WCFM;
		
		if( !$current_range ) {
			$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';
	
			if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
				$current_range = '7day';
			}
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

		$sql = "SELECT * FROM {$wpdb->prefix}pv_commission AS commission";

		$sql .= " WHERE 1=1";
		$sql .= " AND commission.vendor_id = %d";
		$sql .= " AND commission.status != 'reversed'";
		$sql = wcfm_query_time_range_filter( $sql, 'time', $this->current_range );
		
		// Enable big selects for reports
		$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );

		$results = $wpdb->get_results( $wpdb->prepare( $sql, apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ) ) );

		$total_shipping_amount          = 0.00;
		$total_tax_amount               = 0.00;
		$total_earned_commission_amount = 0.00;
		$total_commission_amount        = 0.00;
		$gross_sales_amount               = 0.00;
		$total_items                    = 0;

		$total_orders = array();

		foreach( $results as $data ) {

			$total_orders[] = $data->order_id;
			
			if( $data->order_id ) {
				$order_post_title = get_the_title( $data->order_id );
				if( !$order_post_title ) continue;
				try {
					$order       = wc_get_order( $data->order_id );
					$line_items  = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
					
					foreach( $line_items as $key => $line_item ) {
						if ( ( $line_item->get_variation_id() == $data->product_id ) || ( $line_item->get_product_id() == $data->product_id ) ) {
							$gross_sales_amount += (float) sanitize_text_field( $line_item->get_total() );
							if(WC_Vendors::$pv_options->get_option( 'give_tax' )) {
								$gross_sales_amount += (float) sanitize_text_field( $line_item->get_total_tax() );
							}
							if(WC_Vendors::$pv_options->get_option( 'give_shipping' )) {
								$gross_sales_amount += (float) $data->total_shipping;
							}
						}
					}
				} catch (Exception $e) {
					continue;
				}
			}
			
			$total_tax_amount               += (float) sanitize_text_field( $data->tax );
			$total_shipping_amount          += (float) sanitize_text_field( $data->total_shipping );
			$total_earned_commission_amount += (float) sanitize_text_field( $data->total_due );
			$total_items                    += (int)   sanitize_text_field( $data->qty );

			// show only paid commissions
			if ( 'paid' === $data->status ) {
				$total_commission_amount   += (float) sanitize_text_field( $data->total_due );
				if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) { $total_commission_amount += (float) sanitize_text_field( $data->tax ); } 
				if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) { $total_commission_amount += (float) sanitize_text_field( $data->total_shipping ); }
			}
		}

		$total_orders = count( array_unique( $total_orders ) );
		$total_sales = $total_earned_commission_amount;
		if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) { $total_sales += $total_tax_amount; } 
		if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) { $total_sales += $total_shipping_amount; }

		$this->report_data->average_sales         = wc_format_decimal( $total_sales / ( $this->chart_interval + 1 ), 2 );
		$this->report_data->total_orders          = $total_orders;
		$this->report_data->total_items           = $total_items;
		$this->report_data->total_shipping        = wc_format_decimal( $total_shipping_amount );
		$this->report_data->total_commission      = wc_format_decimal( $total_commission_amount );
		$this->report_data->total_earned          = wc_format_decimal( $total_sales );
		$this->report_data->gross_sales             = wc_format_decimal( $gross_sales_amount );
		$this->report_data->total_tax             = wc_format_decimal( $total_tax_amount );
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
			'title'            => sprintf( __( '%s gross sales in this period', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->gross_sales ) . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the order totals after any refunds and including shipping and taxes.', 'wc-frontend-manager' ),
			'color'            => $this->chart_colors['gross_sales_amount'],
			'highlight_series' => 3
		);
		
		$legend[] = array(
			'title'            => sprintf( __( '%s total earnings', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->total_earned ) . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the earned commission including shipping and taxes if applicable.', 'wc-frontend-manager' ),
			'color'            => $this->chart_colors['earned'],
			'highlight_series' => 4
		);

		$legend[] = array(
			'title'            => sprintf( __( '%s total withdrawal', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->total_commission ) . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the commission paid including shipping and taxes if applicable.', 'wc-frontend-manager' ),
			'color'            => $this->chart_colors['commission'],
			'highlight_series' => 5
		);

		if ( $data->average_sales > 0 ) {
			$legend[] = array(
				'title'            => $average_sales_title,
				'color'            => $this->chart_colors['average'],
				'highlight_series' => 3
			);
		}

		$legend[] = array(
			'title'            => sprintf( __( '%s orders placed', 'wc-frontend-manager' ), '<strong>' . $data->total_orders . '</strong>' ),
			'color'            => $this->chart_colors['order_count'],
			'highlight_series' => 0
		);

		$legend[] = array(
			'title'            => sprintf( __( '%s items purchased', 'wc-frontend-manager' ), '<strong>' . $data->total_items . '</strong>' ),
			'color'            => $this->chart_colors['item_count'],
			'highlight_series' => 1
		);

		if( WC_Vendors::$pv_options->get_option( 'give_shipping' ) && apply_filters( 'wcfm_sales_report_is_allow_shipping', true ) ) {
			$legend[] = array(
				'title'            => sprintf( __( '%s charged for shipping', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->total_shipping ) . '</strong>' ),
				'color'            => $this->chart_colors['shipping_amount'],
				'highlight_series' => 2
			);
		}

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
			'average'          => '#95a5a6',
			'order_count'      => '#dbe1e3',
			'item_count'       => '#ecf0f1',
			'shipping_amount'  => '#FF7400',
			'earned'           => '#4bc0c0',
			'commission'       => '#b1d4ea',
			'gross_sales_amount' => '#3498db',
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
	public function get_main_chart( $show_legend = 1 ) {
		global $wp_locale, $wpdb, $WCFM;
		
		// Generate Data for total earned commision
		$select = "SELECT GROUP_CONCAT(order_id) order_ids, COUNT( DISTINCT commission.order_id ) AS count, SUM( commission.qty ) AS order_item_count, SUM( commission.total_shipping ) AS total_shipping, SUM( commission.tax ) AS total_tax, SUM( commission.total_due ) AS total_commission, commission.time";

		$sql = $select;
		$sql .= " FROM {$wpdb->prefix}pv_commission AS commission";
		$sql .= " WHERE 1=1";
		$sql .= " AND commission.vendor_id = %d";
		$sql .= " AND commission.status != 'reversed'";
		$sql = wcfm_query_time_range_filter( $sql, 'time', $this->current_range );

		$sql .= " GROUP BY DATE( commission.time )";
			
		// Enable big selects for reports
		$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );
		
		$results = $wpdb->get_results( $wpdb->prepare( $sql, apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ) ) );
		
		// Prepare net sales data
		if( !empty( $results ) ) {
			foreach( $results as $result ) {
				$gross_sales = 0.00;
				if( $result->order_ids ) {
					$order_ids = explode( ",", $result->order_ids );
					if( !empty( $order_ids ) && is_array( $order_ids ) ) {
						foreach( $order_ids as $order_id ) {                 
							$order_post_title = get_the_title( $order_id );
							if( !$order_post_title ) continue;
							try {
								$order       = wc_get_order( $order_id );
								$line_items  = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
								$valid_items = (array) WCV_Queries::get_products_for_order( $order_id );
								foreach( $line_items as $key => $line_item ) {
									if ( in_array( $line_item->get_variation_id(), $valid_items) || in_array( $line_item->get_product_id(), $valid_items ) ) {
										$gross_sales += (float) sanitize_text_field( $line_item->get_total() );
										if(WC_Vendors::$pv_options->get_option( 'give_tax' )) {
											$gross_sales += (float) sanitize_text_field( $line_item->get_total_tax() );
										}
									}
								}
							} catch (Exception $e) {
								continue;
							}
						}
					}
				}
				if(WC_Vendors::$pv_options->get_option( 'give_shipping' )) {
					$gross_sales += (float) $result->total_shipping;
				}
				$result->gross_sales = $gross_sales;
			}
		}

		// Prepare data for report
		$order_counts         = $this->prepare_chart_data( $results, 'time', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$order_item_counts    = $this->prepare_chart_data( $results, 'time', 'order_item_count', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$shipping_amounts     = $this->prepare_chart_data( $results, 'time', 'total_shipping', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$tax_amounts          = $this->prepare_chart_data( $results, 'time', 'total_tax', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$total_commission     = $this->prepare_chart_data( $results, 'time', 'total_commission', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$total_gross_sales      = $this->prepare_chart_data( $results, 'time', 'gross_sales', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$total_earned = array();
		foreach ( $total_commission as $order_amount_key => $order_amount_value ) {
			$total_earned[ $order_amount_key ] = $order_amount_value;
			if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) { 
			  $total_earned[ $order_amount_key ][1] += $tax_amounts[ $order_amount_key ][1]; 
			} 
			if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) { 
			  $total_earned[ $order_amount_key ][1] += $shipping_amounts[ $order_amount_key ][1]; 
			}
		}
		
		// Generate Data for Paid Commissions
		$select = "SELECT COUNT( DISTINCT commission.order_id ) AS count, SUM( commission.qty ) AS order_item_count, SUM( commission.total_shipping ) AS total_shipping, SUM( commission.tax ) AS total_tax, SUM( commission.total_due ) AS total_commission, commission.time";

		$sql = $select;
		$sql .= " FROM {$wpdb->prefix}pv_commission AS commission";
		$sql .= " WHERE 1=1";
		$sql .= " AND commission.vendor_id = %d";
		$sql .= " AND commission.status = 'paid'";
		$sql = wcfm_query_time_range_filter( $sql, 'time', $this->current_range );

		$sql .= " GROUP BY DATE( commission.time )";
			
		// Enable big selects for reports
		$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );
		
		$results = $wpdb->get_results( $wpdb->prepare( $sql, apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ) ) );
		
		// Prepare data for report
		$shipping_amounts     = $this->prepare_chart_data( $results, 'time', 'total_shipping', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$tax_amounts          = $this->prepare_chart_data( $results, 'time', 'total_tax', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$total_commission     = $this->prepare_chart_data( $results, 'time', 'total_commission', $this->chart_interval, $this->start_date, $this->chart_groupby );
	
		$total_paid_commission = array();
		foreach ( $total_commission as $order_amount_key => $order_amount_value ) {
			$total_paid_commission[ $order_amount_key ] = $order_amount_value;
			if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) { 
				$total_paid_commission[ $order_amount_key ][1] += $tax_amounts[ $order_amount_key ][1];
			} 
			if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) { 
				$total_paid_commission[ $order_amount_key ][1] += $shipping_amounts[ $order_amount_key ][1];
			}
		}
	
		// Encode in json format
		$chart_data = '{'
			. '  "order_counts"             : ' . $WCFM->wcfm_prepare_chart_data( $order_counts )
			. ', "order_item_counts"        : ' . $WCFM->wcfm_prepare_chart_data( $order_item_counts )
			. ', "shipping_amounts"         : ' . $WCFM->wcfm_prepare_chart_data( $shipping_amounts )
			. ', "total_earned_commission"  : ' . $WCFM->wcfm_prepare_chart_data( $total_earned )
			. ', "total_paid_commission"    : ' . $WCFM->wcfm_prepare_chart_data( $total_paid_commission )
			. ', "total_gross_sales"          : ' . $WCFM->wcfm_prepare_chart_data( $total_gross_sales )
		  . '}';
		?>
		<div class="chart-container">
			<div class="chart-placeholder main"><canvas id="chart-placeholder-canvas"></canvas></div>
		</div>
		<script type="text/javascript">

			jQuery(function(){
				var sales_data = <?php echo $chart_data; ?>;
				var show_legend    = <?php echo $show_legend; ?>;
				
				jQuery('.chart-placeholder').css( 'width', jQuery('.chart-placeholder').outerWidth() + 'px' );
				
				var ctx = document.getElementById("chart-placeholder-canvas").getContext("2d");
				var mySalesReportChart = new Chart(ctx, {
						type: 'bar',
						data: {
							  labels: sales_data.total_earned_commission.labels,
								datasets: [
											{
												type: 'line',
												label: "<?php _e( 'Gross Sales', 'wc-frontend-manager' ); ?>",
												backgroundColor: color(window.chartColors.blue).alpha(0.2).rgbString(),
												borderColor: window.chartColors.blue,
												fill: true,
												data: sales_data.total_gross_sales.datas,
											},
								      {
												type: 'line',
												label: "<?php _e( 'Earning', 'wc-frontend-manager' ); ?>",
												backgroundColor: color(window.chartColors.green).alpha(0.2).rgbString(),
												borderColor: window.chartColors.green,
												fill: true,
												data: sales_data.total_earned_commission.datas,
											},
											{
												type: 'bar',
												label: "<?php _e( 'Withdrawal', 'wc-frontend-manager' ); ?>",
												backgroundColor: color(window.chartColors.grey).alpha(0.2).rgbString(),
												borderColor: window.chartColors.grey,
												borderWidth: 2,
												data: sales_data.total_paid_commission.datas,
											},
											<?php if( WC_Vendors::$pv_options->get_option( 'give_shipping' ) && apply_filters( 'wcfm_sales_report_is_allow_shipping', true ) ) { ?>
											{
												type: 'line',
												label: "<?php _e( 'Shipping Amounts', 'wc-frontend-manager' ); ?>",
												backgroundColor: color(window.chartColors.red).alpha(0.2).rgbString(),
												borderColor: window.chartColors.red,
												fill: true,
												data: sales_data.shipping_amounts.datas,
											},
											<?php } ?>
								      {
												type: 'bar',
												label: "<?php _e( 'Order Counts', 'wc-frontend-manager' ); ?>",
												backgroundColor: color(window.chartColors.yellow).alpha(0.5).rgbString(),
												borderColor: window.chartColors.yellow,
												borderWidth: 2,
												data: sales_data.order_counts.datas,
											},
											{
												type: 'bar',
												label: "<?php _e( 'Order Item Counts', 'wc-frontend-manager' ); ?>",
												backgroundColor: color(window.chartColors.orange).alpha(0.5).rgbString(),
												borderColor: window.chartColors.orange,
												borderWidth: 2,
												data: sales_data.order_item_counts.datas,
											},
											]
						},
						options: {
							  responsive: true,
                title:{
                    text: "<?php _e( 'Sales Report by Date', 'wc-frontend-manager' ); ?>",
                    position: "bottom",
                    display: true
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
											labelString: "<?php _e( 'Date', 'wc-frontend-manager' ); ?>"
										}
									}],
									yAxes: [{
										scaleLabel: {
											display: false,
											labelString: "<?php _e( 'Amount', 'wc-frontend-manager' ); ?>"
										}
									}]
								},
							}
						});
				
				var resizeId;
        jQuery(window).resize(function() {
					clearTimeout(resizeId);
					resizeId = setTimeout(afterResizing, 100);
        });
				function afterResizing() {
					var canvasheight = document.getElementById("chart-placeholder-canvas").height;
					if(canvasheight <= 375) {
						mySalesReportChart.options.legend.display=false;
					} else {
						mySalesReportChart.options.legend.display=true;
					}
					mySalesReportChart.update();
				}
				resizeId = setTimeout(afterResizing, 100);
			});
		</script>
		<?php
	}
}
