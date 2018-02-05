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

class WC_Marketplace_Report_Sales_By_Date extends WC_Admin_Report {
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
		global $wpdb, $WCFM, $WCMp;

		$this->report_data = new stdClass;

		$sql = "SELECT * FROM {$wpdb->prefix}wcmp_vendor_orders AS commission";

		$sql .= " WHERE 1=1";
		$sql .= " AND `line_item_type` = 'product'";
		$sql .= " AND commission.vendor_id = %d";
		$sql .= " AND `commission_id` != 0 AND `commission_id` != ''";
		$sql .= " AND commission.is_trashed != -1";
		$sql = wcfm_query_time_range_filter( $sql, 'created', $this->current_range );

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
			
			if( $data->order_item_id ) {
				try {
					$line_item = new WC_Order_Item_Product( $data->order_item_id );
					$gross_sales_amount += (float) sanitize_text_field( $line_item->get_total() );
					if($WCMp->vendor_caps->vendor_payment_settings('give_tax')) {
						$gross_sales_amount += (float) sanitize_text_field( $line_item->get_total_tax() );
						$gross_sales_amount += (float) $data->shipping_tax_amount;
					}
					if($WCMp->vendor_caps->vendor_payment_settings('give_shipping')) {
						$gross_sales_amount += (float) $data->shipping;
					}
				} catch (Exception $e) {
					continue;
				}
			}
			
			$total_tax_amount               += (float) sanitize_text_field( ($data->tax == 'NAN') ? 0 : $data->tax ) + (float) sanitize_text_field( ($data->shipping_tax_amount == 'NAN') ? 0 : $data->shipping_tax_amount );
			$total_shipping_amount          += (float) sanitize_text_field( ($data->shipping == 'NAN') ? 0 : $data->shipping );
			$total_earned_commission_amount += (float) sanitize_text_field( $data->commission_amount );
			$total_items                    += (int)   sanitize_text_field( $data->quantity );
			
			// show only paid commissions
			if ( 'paid' === $data->commission_status ) {
				$total_commission_amount   += (float) sanitize_text_field( $data->commission_amount );
				if ( $WCMp->vendor_caps->vendor_payment_settings('give_tax') ) { $total_commission_amount += (float) sanitize_text_field( $data->tax ) + (float) sanitize_text_field( $data->shipping_tax_amount ); } 
				if ( $WCMp->vendor_caps->vendor_payment_settings('give_shipping') ) { $total_commission_amount += (float) sanitize_text_field( $data->shipping ); }
			}
		}

		$total_orders = count( array_unique( $total_orders ) );
		$total_sales = $total_earned_commission_amount;
		if($WCMp->vendor_caps->vendor_payment_settings('give_tax')) { $total_sales += $total_tax_amount; } 
		if($WCMp->vendor_caps->vendor_payment_settings('give_shipping')) { $total_sales += $total_shipping_amount; }
		
		$this->report_data->average_sales         = wc_format_decimal( $total_sales / ( $this->chart_interval + 1 ), 2 );
		$this->report_data->total_orders          = $total_orders;
		$this->report_data->total_items           = $total_items;
		$this->report_data->total_shipping        = wc_format_decimal( $total_shipping_amount );
		$this->report_data->total_earned          = wc_format_decimal( $total_sales );
		$this->report_data->total_commission      = wc_format_decimal( $total_commission_amount );
		$this->report_data->gross_sales             = wc_format_decimal( $gross_sales_amount );
		$this->report_data->total_tax             = wc_format_decimal( $total_tax_amount );
		
		// Admin Fee Mode Commission
		$admin_fee_mode = apply_filters( 'wcfm_is_admin_fee_mode', false );
		if( $admin_fee_mode ) {
		  $this->report_data->total_earned = $gross_sales_amount - $total_sales;
		  $net_paid_sales_amount = $WCFM->wcfm_vendor_support->wcfm_get_gross_sales_by_vendor( apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ), $this->current_range, true );
		  $this->report_data->total_commission = $net_paid_sales_amount - $total_commission_amount;
		}
	}

	/**
	 * Get the legend for the main chart sidebar
	 * @return array
	 */
	public function get_chart_legend() {
		global $WCFM, $WCMp;
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
		
		// Admin Fee Mode Commission
		$admin_fee_mode = apply_filters( 'wcfm_is_admin_fee_mode', false );
		
		if( $admin_fee_mode ) {
			$legend[] = array(
				'title'            => sprintf( __( '%s total admin fees', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->total_earned ) . '</strong>' ),
				'placeholder'      => __( 'This is the sum of the admin fees including shipping and taxes if applicable.', 'wc-frontend-manager' ),
				'color'            => $this->chart_colors['earned'],
				'highlight_series' => 3
			);
			
			$legend[] = array(
				'title'            => sprintf( __( '%s total paid fees', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->total_commission ) . '</strong>' ),
				'placeholder'      => __( 'This is the sum of the admin fees paid including shipping and taxes if applicable.', 'wc-frontend-manager' ),
				'color'            => $this->chart_colors['commission'],
				'highlight_series' => 4
			);
		} else {
			$legend[] = array(
				'title'            => sprintf( __( '%s total earnings', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->total_earned ) . '</strong>' ),
				'placeholder'      => __( 'This is the sum of the earned commission including shipping and taxes if applicable.', 'wc-frontend-manager' ),
				'color'            => $this->chart_colors['earned'],
				'highlight_series' => 3
			);
			
			$legend[] = array(
				'title'            => sprintf( __( '%s total withdrawal', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->total_commission ) . '</strong>' ),
				'placeholder'      => __( 'This is the sum of the commission paid including shipping and taxes if applicable.', 'wc-frontend-manager' ),
				'color'            => $this->chart_colors['commission'],
				'highlight_series' => 4
			);
		}

		if ( $data->average_sales > 0 ) {
			$legend[] = array(
				'title'            => $average_sales_title,
				'color'            => $this->chart_colors['average'],
				'highlight_series' => 2
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

		if( $WCMp->vendor_caps->vendor_payment_settings('give_shipping') && apply_filters( 'wcfm_sales_report_is_allow_shipping', true ) ) {
			$legend[] = array(
				'title'            => sprintf( __( '%s charged for shipping', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->total_shipping ) . '</strong>' ),
				'color'            => $this->chart_colors['shipping_amount'],
				'highlight_series' => 1
			);
		}

		return apply_filters( 'wcfm_wcmarketplace_sales_report_legends', $legend );
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
		global $wp_locale, $wpdb, $WCFM, $WCMp;
		
		// Admin Fee Mode Commission
		$admin_fee_mode = false;
		if (isset($WCMp->vendor_caps->payment_cap['revenue_sharing_mode'])) {
			if ($WCMp->vendor_caps->payment_cap['revenue_sharing_mode'] == 'admin') {
				$admin_fee_mode = true;
			}
		}
		
		$select = "SELECT GROUP_CONCAT(order_item_id) order_item_ids, COUNT( DISTINCT commission.order_id ) AS count, SUM( commission.quantity ) AS order_item_count, COALESCE( SUM( commission.shipping ), 0 ) AS total_shipping, COALESCE( SUM( commission.tax ), 0 ) AS total_tax, COALESCE( SUM( commission.shipping_tax_amount ), 0 ) AS total_shipping_tax_amount, COALESCE( SUM( commission.commission_amount ), 0 ) AS total_commission, commission.created AS time";

		$sql = $select;
		$sql .= " FROM {$wpdb->prefix}wcmp_vendor_orders AS commission";
		$sql .= " WHERE 1=1";
		$sql .= " AND `line_item_type` = 'product'";
		$sql .= " AND commission.vendor_id = %d";
		$sql .= " AND `commission_id` != 0 AND `commission_id` != ''";
		$sql .= " AND commission.is_trashed != -1";
		$sql = wcfm_query_time_range_filter( $sql, 'created', $this->current_range );

		$sql .= " GROUP BY DATE( commission.created )";
			
		// Enable big selects for reports
		$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );
		
		$results = $wpdb->get_results( $wpdb->prepare( $sql, apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ) ) );
		
		// Prepare net sales data
		if( !empty( $results ) ) {
			foreach( $results as $result ) {
				$gross_sales = 0.00;
				if( $result->order_item_ids ) {
					$order_item_ids = explode( ",", $result->order_item_ids );
					if( !empty( $order_item_ids ) && is_array( $order_item_ids ) ) {
						foreach( $order_item_ids as $order_item_id ) {
							try {
								$line_item = new WC_Order_Item_Product( $order_item_id );
								$gross_sales += (float) sanitize_text_field( $line_item->get_total() );
								if($WCMp->vendor_caps->vendor_payment_settings('give_tax')) {
									$gross_sales += (float) sanitize_text_field( $line_item->get_total_tax() );
								}
							} catch (Exception $e) {
								continue;
							}
						}
					}
				}
				if($WCMp->vendor_caps->vendor_payment_settings('give_tax')) {
					$gross_sales += (float) $result->total_shipping_tax_amount;
				}
				if($WCMp->vendor_caps->vendor_payment_settings('give_shipping')) {
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
		
		$shipping_tax_amounts = $this->prepare_chart_data( $results, 'time', 'total_shipping_tax_amount', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$total_commission     = $this->prepare_chart_data( $results, 'time', 'total_commission', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$total_gross_sales      = $this->prepare_chart_data( $results, 'time', 'gross_sales', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$total_earned_commission = array();
		foreach ( $total_commission as $order_amount_key => $order_amount_value ) {
			$total_earned_commission[ $order_amount_key ] = $order_amount_value;
			if($WCMp->vendor_caps->vendor_payment_settings('give_tax')) {
				$tax_amounts[ $order_amount_key ][1] += $shipping_tax_amounts[ $order_amount_key ][1];
			  $total_earned_commission[ $order_amount_key ][1] += $tax_amounts[ $order_amount_key ][1]; 
			} 
			if($WCMp->vendor_caps->vendor_payment_settings('give_shipping')) {
			  $total_earned_commission[ $order_amount_key ][1] += $shipping_amounts[ $order_amount_key ][1]; 
			}
			
			if( $admin_fee_mode && isset ( $total_gross_sales[ $order_amount_key ] ) && isset ( $total_gross_sales[ $order_amount_key ][1] ) ) {
				$total_earned_commission[ $order_amount_key ][1] = $total_gross_sales[ $order_amount_key ][1] - $total_earned_commission[ $order_amount_key ][1];
			}
		}
		
		// Total Paid Commission
		$select = "SELECT GROUP_CONCAT(order_item_id) order_item_ids, COUNT( DISTINCT commission.order_id ) AS count, SUM( commission.quantity ) AS order_item_count, COALESCE( SUM( commission.shipping ), 0 ) AS total_shipping, COALESCE( SUM( commission.tax ), 0 ) AS total_tax, COALESCE( SUM( commission.shipping_tax_amount ), 0 ) AS total_shipping_tax_amount, COALESCE( SUM( commission.commission_amount ), 0 ) AS total_commission, commission.commission_paid_date AS time";

		$sql = $select;
		$sql .= " FROM {$wpdb->prefix}wcmp_vendor_orders AS commission";
		$sql .= " WHERE 1=1";
		$sql .= " AND `line_item_type` = 'product'";
		$sql .= " AND commission.vendor_id = %d";
		$sql .= " AND commission.is_trashed != -1";
		$sql .= " AND commission.commission_status = 'paid'";
		$sql = wcfm_query_time_range_filter( $sql, 'commission_paid_date', $this->current_range );
		
		$sql .= " GROUP BY DATE( commission.commission_paid_date )";
		
		// Enable big selects for reports
		$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );
		
		$results = $wpdb->get_results( $wpdb->prepare( $sql, apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ) ) );
		
		if( $admin_fee_mode ) {
		// Prepare paid net sales data
			if( !empty( $results ) ) {
				foreach( $results as $result ) {
					$net_paid_sales = 0.00;
					if( $result->order_item_ids ) {
						$order_item_ids = explode( ",", $result->order_item_ids );
						if( !empty( $order_item_ids ) && is_array( $order_item_ids ) ) {
							foreach( $order_item_ids as $order_item_id ) {
								try {
									$line_item = new WC_Order_Item_Product( $order_item_id );
									$net_paid_sales += (float) sanitize_text_field( $line_item->get_total() );
									if($WCMp->vendor_caps->vendor_payment_settings('give_tax')) {
										$net_paid_sales += (float) sanitize_text_field( $line_item->get_total_tax() );
									}
								} catch (Exception $e) {
									continue;
								}
							}
						}
					}
					if($WCMp->vendor_caps->vendor_payment_settings('give_tax')) {
						$net_paid_sales += (float) $result->total_shipping_tax_amount;
					}
					if($WCMp->vendor_caps->vendor_payment_settings('give_shipping')) {
						$net_paid_sales += (float) $result->total_shipping;
					}
					$result->net_paid_sales = $net_paid_sales;
				}
			}
			
			$total_net_paid_sales = $this->prepare_chart_data( $results, 'time', 'net_paid_sales', $this->chart_interval, $this->start_date, $this->chart_groupby );
		}
		
		$shipping_amounts            = $this->prepare_chart_data( $results, 'time', 'total_shipping', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$tax_amounts                 = $this->prepare_chart_data( $results, 'time', 'total_tax', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$total_shipping_tax_amount   = $this->prepare_chart_data( $results, 'time', 'total_shipping_tax_amount', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$total_commission            = $this->prepare_chart_data( $results, 'time', 'total_commission', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$total_paid_commission = array();
		foreach ( $total_commission as $order_amount_key => $order_amount_value ) {
			$total_paid_commission[ $order_amount_key ] = $order_amount_value;
			if ( $WCMp->vendor_caps->vendor_payment_settings('give_tax') ) {
				$tax_amounts[ $order_amount_key ][1] += $total_shipping_tax_amount[ $order_amount_key ][1];
				$total_paid_commission[ $order_amount_key ][1] += $tax_amounts[ $order_amount_key ][1];
			} 
			if ( $WCMp->vendor_caps->vendor_payment_settings('give_shipping') ) { 
				$total_paid_commission[ $order_amount_key ][1] += $shipping_amounts[ $order_amount_key ][1];
			}
			
			if( $admin_fee_mode && isset ( $total_net_paid_sales[ $order_amount_key ] ) && isset ( $total_net_paid_sales[ $order_amount_key ][1] ) ) {
				$total_paid_commission[ $order_amount_key ][1] = $total_net_paid_sales[ $order_amount_key ][1] - $total_paid_commission[ $order_amount_key ][1];
			}
		}
		
		// Encode in json format
		$chart_data = '{'
			. '  "order_counts"             : ' . $WCFM->wcfm_prepare_chart_data( $order_counts )
			. ', "order_item_counts"        : ' . $WCFM->wcfm_prepare_chart_data( $order_item_counts )
			. ', "shipping_amounts"         : ' . $WCFM->wcfm_prepare_chart_data( $shipping_amounts )
			. ', "total_earned_commission"  : ' . $WCFM->wcfm_prepare_chart_data( $total_earned_commission )
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
												label: "<?php if( $admin_fee_mode ) { _e( 'Admin Fees', 'wc-frontend-manager' ); } else { _e( 'Earning', 'wc-frontend-manager' ); } ?>",
												backgroundColor: color(window.chartColors.green).alpha(0.2).rgbString(),
												borderColor: window.chartColors.green,
												fill: true,
												data: sales_data.total_earned_commission.datas,
											},
											{
												type: 'bar',
												label: "<?php if( $admin_fee_mode ) { _e( 'Paid Fees', 'wc-frontend-manager' ); } else { _e( 'Withdrawal', 'wc-frontend-manager' ); } ?>",
												backgroundColor: color(window.chartColors.grey).alpha(0.2).rgbString(),
												borderColor: window.chartColors.grey,
												borderWidth: 2,
												data: sales_data.total_paid_commission.datas,
											},
											<?php if( $WCMp->vendor_caps->vendor_payment_settings('give_shipping') && apply_filters( 'wcfm_sales_report_is_allow_shipping', true ) ) { ?>
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
