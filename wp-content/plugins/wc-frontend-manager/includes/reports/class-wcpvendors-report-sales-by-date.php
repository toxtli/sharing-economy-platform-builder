<?php
/**
 * Report class responsible for handling sales by date reports.
 *
 * @since      2.0.0
 *
 * @package    WooCommerce Product Vendors
 * @subpackage WooCommerce Product Vendors/Reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );

class WC_Product_Vendors_Vendor_Report_Sales_By_Date extends WC_Admin_Report {
	public $chart_colors = array();
	public $current_range;
	private $report_data;

	/**
	 * Constructor
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
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
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return array of objects
	 */
	public function get_report_data() {
		if ( empty( $this->report_data ) ) {
			$this->query_report_data();
		}

		return $this->report_data;
	}

	/**
	 * Get the report based on parameters
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return array of objects
	 */
	public function query_report_data() {
		global $wpdb;

		$this->report_data = new stdClass;

		// check if table exists before continuing
		if ( ! WC_Product_Vendors_Utils::commission_table_exists() ) {
			return $this->report_data;
		}

		$sql = "SELECT * FROM " . WC_PRODUCT_VENDORS_COMMISSION_TABLE . " AS commission";

		$sql .= " WHERE 1=1";
		$sql .= " AND commission.vendor_id = %d";
		$sql .= " AND commission.commission_status != 'void'";

		switch( $this->current_range ) {
			case 'year' :
				$sql .= " AND YEAR( commission.order_date ) = YEAR( CURDATE() )";
				break;

			case 'last_month' :
				$sql .= " AND MONTH( commission.order_date ) = MONTH( NOW() ) - 1";
				break;

			case 'month' :
				$sql .= " AND MONTH( commission.order_date ) = MONTH( NOW() )";
				break;

			case 'custom' :
				$start_date = ! empty( $_GET['start_date'] ) ? sanitize_text_field( $_GET['start_date'] ) : '';
				$end_date = ! empty( $_GET['end_date'] ) ? sanitize_text_field( $_GET['end_date'] ) : '';

				$sql .= " AND DATE( commission.order_date ) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
				break;

			case 'default' :
			case '7day' :
				$sql .= " AND DATE( commission.order_date ) BETWEEN DATE_SUB( NOW(), INTERVAL 7 DAY ) AND NOW()";
				break;
		}

		if ( false === ( $results = get_transient( 'wcpv_reports_legend_' . WC_Product_Vendors_Utils::get_logged_in_vendor() . '_' . $this->current_range ) ) ) {
			
			// Enable big selects for reports
			$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );

			$results = $wpdb->get_results( $wpdb->prepare( $sql, WC_Product_Vendors_Utils::get_logged_in_vendor() ) );

			set_transient( 'wcpv_reports_legend_' . WC_Product_Vendors_Utils::get_logged_in_vendor() . '_' . $this->current_range, $results, DAY_IN_SECONDS );
		}

		$total_product_amount           = 0.00;
		$total_shipping_amount          = 0.00;
		$total_shipping_tax_amount      = 0.00;
		$total_product_tax_amount       = 0.00;
		$total_earned_commission_amount = 0.00;
		$total_commission_amount        = 0.00;

		$total_orders = array();

		foreach( $results as $data ) {

			$total_orders[] = $data->order_id;
			
			$total_product_amount           += (float) sanitize_text_field( $data->product_amount );
			$total_product_tax_amount       += (float) sanitize_text_field( $data->product_tax_amount );
			$total_shipping_amount          += (float) sanitize_text_field( $data->product_shipping_amount );
			$total_shipping_tax_amount      += (float) sanitize_text_field( $data->product_shipping_tax_amount );
			$total_earned_commission_amount += (float) sanitize_text_field( $data->total_commission_amount );

			// show only paid commissions
			if ( 'paid' === $data->commission_status ) {
				$total_commission_amount   += (float) sanitize_text_field( $data->total_commission_amount );
			}
		}

		$total_orders = count( array_unique( $total_orders ) );
		$total_sales = $total_product_amount + $total_product_tax_amount + $total_shipping_amount + $total_shipping_tax_amount;
		$net_sales = $total_sales - $total_product_tax_amount - $total_shipping_amount - $total_shipping_tax_amount;
		$total_tax_amount = $total_product_tax_amount + $total_shipping_tax_amount;

		$this->report_data->total_sales           = $total_sales;
		$this->report_data->net_sales             = wc_format_decimal( $net_sales );
		$this->report_data->average_sales         = wc_format_decimal( $net_sales / ( $this->chart_interval + 1 ), 2 );
		$this->report_data->total_orders          = $total_orders;
		$this->report_data->total_items           = count( $results );
		$this->report_data->total_shipping        = wc_format_decimal( $total_shipping_amount );
		$this->report_data->total_commission      = wc_format_decimal( $total_commission_amount );
		$this->report_data->total_earned          = wc_format_decimal( $total_earned_commission_amount );
		$this->report_data->total_tax             = wc_format_decimal( $total_tax_amount );
	}

	/**
	 * Get the legend for the main chart sidebar
	 * @return array
	 */
	public function get_chart_legend() {
		$legend = array();
		$data   = $this->get_report_data();

		switch ( $this->chart_groupby ) {
			case 'day' :
				$average_sales_title = sprintf( __( '%s average daily sales', 'woocommerce-product-vendors' ), '<strong>' . wc_price( $data->average_sales ) . '</strong>' );
			break;
			case 'month' :
			default :
				$average_sales_title = sprintf( __( '%s average monthly sales', 'woocommerce-product-vendors' ), '<strong>' . wc_price( $data->average_sales ) . '</strong>' );
			break;
		}

		$legend[] = array(
			'title'            => sprintf( __( '%s gross sales in this period', 'woocommerce-product-vendors' ), '<strong>' . wc_price( $data->total_sales ) . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the order totals after any refunds and including shipping and taxes.', 'woocommerce-product-vendors' ),
			'color'            => $this->chart_colors['sales_amount'],
			'highlight_series' => 4
		);

		$legend[] = array(
			'title'            => sprintf( __( '%s net sales in this period', 'woocommerce-product-vendors' ), '<strong>' . wc_price( $data->net_sales ) . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the order totals after any refunds and excluding shipping and taxes.', 'woocommerce-product-vendors' ),
			'color'            => $this->chart_colors['net_sales_amount'],
			'highlight_series' => 5
		);

		$legend[] = array(
			'title'            => sprintf( __( '%s total earnings', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->total_earned ) . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the earned commission including shipping and taxes if applicable.', 'woocommerce-product-vendors' ),
			'color'            => $this->chart_colors['earned'],
			'highlight_series' => 6
		);

		$legend[] = array(
			'title'            => sprintf( __( '%s total withdrawal', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->total_commission ) . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the commission paid including shipping and taxes if applicable.', 'woocommerce-product-vendors' ),
			'color'            => $this->chart_colors['commission'],
			'highlight_series' => 6
		);
		
		if ( $data->average_sales > 0 ) {
			$legend[] = array(
				'title'            => $average_sales_title,
				'color'            => $this->chart_colors['average'],
				'highlight_series' => 3
			);
		}

		$legend[] = array(
			'title'            => sprintf( __( '%s orders placed', 'woocommerce-product-vendors' ), '<strong>' . $data->total_orders . '</strong>' ),
			'color'            => $this->chart_colors['order_count'],
			'highlight_series' => 0
		);

		$legend[] = array(
			'title'            => sprintf( __( '%s items purchased', 'woocommerce-product-vendors' ), '<strong>' . $data->total_items . '</strong>' ),
			'color'            => $this->chart_colors['item_count'],
			'highlight_series' => 1
		);

		if( apply_filters( 'wcfm_sales_report_is_allow_shipping', true ) ) {
			$legend[] = array(
				'title'            => sprintf( __( '%s charged for shipping', 'woocommerce-product-vendors' ), '<strong>' . wc_price( $data->total_shipping ) . '</strong>' ),
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
		$ranges = array(
			'year'         => __( 'Year', 'woocommerce-product-vendors' ),
			'last_month'   => __( 'Last Month', 'woocommerce-product-vendors' ),
			'month'        => __( 'This Month', 'woocommerce-product-vendors' ),
			'7day'         => __( 'Last 7 Days', 'woocommerce-product-vendors' ),
		);

		$this->chart_colors = array(
			'sales_amount'     => '#b1d4ea',
			'net_sales_amount' => '#3498db',
			'average'          => '#95a5a6',
			'order_count'      => '#dbe1e3',
			'item_count'       => '#ecf0f1',
			'shipping_amount'  => '#FF7400',
			'earned'           => '#4096EE',
			'commission'       => '#008C00',
		);

		$current_range = $this->current_range;

		$this->calculate_current_range( $this->current_range );

		include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php' );
	}

	/**
	 * Output an export link
	 */
	public function get_export_button() {
		return;
		?>
		<a
			href="#"
			download="report-<?php echo esc_attr( $this->current_range ); ?>-<?php echo date_i18n( 'Y-m-d', current_time('timestamp') ); ?>.csv"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php esc_attr_e( 'Date', 'woocommerce-product-vendors' ); ?>"
			data-exclude_series="2"
			data-groupby="<?php echo $this->chart_groupby; ?>"
			data-range="<?php echo $this->current_range; ?>"
			data-custom-range="<?php echo 'custom' === $this->current_range ? $this->start_date . '-' . $this->end_date : ''; ?>"
		>
			<?php esc_html_e( 'Export CSV', 'woocommerce-product-vendors' ); ?>
		</a>
		<?php
	}

	/**
	 * Round our totals correctly
	 * @param  string $amount
	 * @return string
	 */
	private function round_chart_totals( $amount ) {
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

		// check if table exists before continuing
		if ( ! WC_Product_Vendors_Utils::commission_table_exists() ) {
			return $this->report_data;
		}

		$select = "SELECT COUNT( DISTINCT commission.order_id ) AS count, COUNT( commission.order_id ) AS order_item_count, SUM( commission.product_amount + commission.product_shipping_amount + commission.product_tax_amount + commission.product_shipping_tax_amount ) AS total_sales, SUM( commission.product_shipping_amount ) AS total_shipping, SUM( commission.product_tax_amount ) AS total_tax, SUM( commission.product_shipping_tax_amount ) AS total_shipping_tax, SUM( commission.total_commission_amount ) AS total_earned, SUM( commission.total_commission_amount ) AS total_commission, commission.order_date";

		$sql = $select;
		$sql .= " FROM " . WC_PRODUCT_VENDORS_COMMISSION_TABLE . " AS commission";
		$sql .= " WHERE 1=1";
		$sql .= " AND commission.vendor_id = %d";
		$sql .= " AND commission.commission_status != 'void'";

		switch( $this->current_range ) {
			case 'year' :
				$sql .= " AND YEAR( commission.order_date ) = YEAR( CURDATE() )";
				break;

			case 'last_month' :
				$sql .= " AND MONTH( commission.order_date ) = MONTH( NOW() ) - 1";
				break;

			case 'month' :
				$sql .= " AND MONTH( commission.order_date ) = MONTH( NOW() )";
				break;

			case 'custom' :
				$start_date = ! empty( $_GET['start_date'] ) ? sanitize_text_field( $_GET['start_date'] ) : '';
				$end_date = ! empty( $_GET['end_date'] ) ? sanitize_text_field( $_GET['end_date'] ) : '';

				$sql .= " AND DATE( commission.order_date ) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
				break;

			case 'default' :
			case '7day' :
				$sql .= " AND DATE( commission.order_date ) BETWEEN DATE_SUB( NOW(), INTERVAL 7 DAY ) AND NOW()";
				break;
		}
			
		$sql .= " GROUP BY DATE( commission.order_date )";
			
		if ( false === ( $results = get_transient( 'wcpv_reports_' . WC_Product_Vendors_Utils::get_logged_in_vendor() . '_' . $this->current_range ) ) ) {

			// Enable big selects for reports
			$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );
			
			$results = $wpdb->get_results( $wpdb->prepare( $sql, WC_Product_Vendors_Utils::get_logged_in_vendor() ) );

			set_transient( 'wcpv_reports_' . WC_Product_Vendors_Utils::get_logged_in_vendor() . '_' . $this->current_range, $results, DAY_IN_SECONDS );
		}

		// Prepare data for report
		$order_counts         = $this->prepare_chart_data( $results, 'order_date', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$order_item_counts    = $this->prepare_chart_data( $results, 'order_date', 'order_item_count', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$order_amounts        = $this->prepare_chart_data( $results, 'order_date', 'total_sales', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$shipping_amounts     = $this->prepare_chart_data( $results, 'order_date', 'total_shipping', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$shipping_tax_amounts = $this->prepare_chart_data( $results, 'order_date', 'total_shipping_tax', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$tax_amounts          = $this->prepare_chart_data( $results, 'order_date', 'total_tax', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$total_earned         = $this->prepare_chart_data( $results, 'order_date', 'total_earned', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$total_commission     = $this->prepare_chart_data( $results, 'order_date', 'total_commission', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$net_order_amounts = array();

		foreach ( $order_amounts as $order_amount_key => $order_amount_value ) {
			$net_order_amounts[ $order_amount_key ]    = $order_amount_value;
			$net_order_amounts[ $order_amount_key ][1] = $net_order_amounts[ $order_amount_key ][1] - $shipping_amounts[ $order_amount_key ][1] - $shipping_tax_amounts[ $order_amount_key ][1] - $tax_amounts[ $order_amount_key ][1];
		}

		// Encode in json format
		$chart_data = '{'
			. '  "order_counts"             : ' . $WCFM->wcfm_prepare_chart_data( $order_counts )
			. ', "order_item_counts"        : ' . $WCFM->wcfm_prepare_chart_data( $order_item_counts )
			. ', "order_amounts"            : ' . $WCFM->wcfm_prepare_chart_data( $order_amounts )
			. ', "net_order_amounts"        : ' . $WCFM->wcfm_prepare_chart_data( $net_order_amounts )
			. ', "shipping_amounts"         : ' . $WCFM->wcfm_prepare_chart_data( $shipping_amounts )
			. ', "total_earned_commission"  : ' . $WCFM->wcfm_prepare_chart_data( $total_earned )
			. ', "total_paid_commission"    : ' . $WCFM->wcfm_prepare_chart_data( $total_commission )
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
												data: sales_data.order_amounts.datas,
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
											<?php if( apply_filters( 'wcfm_sales_report_is_allow_shipping', true ) ) { ?>
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
						if( show_legend ) {
							mySalesReportChart.options.legend.display=true;
						}
					}
					mySalesReportChart.update();
				}
				resizeId = setTimeout(afterResizing, 100);
			});
		</script>
		<?php
	}
}
