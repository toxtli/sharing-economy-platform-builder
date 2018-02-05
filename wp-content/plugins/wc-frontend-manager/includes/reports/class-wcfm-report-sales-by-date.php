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

class WCFM_Report_Sales_By_Date extends WC_Admin_Report {

	/**
	 * Chart colors.
	 *
	 * @var array
	 */
	public $chart_colours = array();

	/**
	 * The report data.
	 *
	 * @var stdClass
	 */
	private $report_data;
	
	public $current_range;
	
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
	 * Get report data.
	 * @return stdClass
	 */
	public function get_report_data() {
		if ( empty( $this->report_data ) ) {
			$this->query_report_data();
		}
		return $this->report_data;
	}

	/**
	 * Get all data needed for this report and store in the class.
	 */
	private function query_report_data() {
		$this->report_data = new stdClass;

		$this->report_data->order_counts = (array) $this->get_order_report_data( array(
			'data' => array(
				'ID' => array(
					'type'     => 'post_data',
					'function' => 'COUNT',
					'name'     => 'count',
					'distinct' => true,
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
			),
			'group_by'            => $this->group_by_query,
			'order_by'            => 'post_date ASC',
			'query_type'          => 'get_results',
			'filter_range'        => true,
			'order_types'         => wc_get_order_types( 'order-count' ),
			'order_status'        => array( 'completed', 'processing', 'on-hold', 'refunded' ),
		) );

		$this->report_data->coupons = (array) $this->get_order_report_data( array(
			'data' => array(
				'order_item_name' => array(
					'type'     => 'order_item',
					'function' => '',
					'name'     => 'order_item_name',
				),
				'discount_amount' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'coupon',
					'function'        => 'SUM',
					'name'            => 'discount_amount',
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
			),
			'where' => array(
				array(
					'key'      => 'order_items.order_item_type',
					'value'    => 'coupon',
					'operator' => '=',
				),
			),
			'group_by'     => $this->group_by_query . ', order_item_name',
			'order_by'     => 'post_date ASC',
			'query_type'   => 'get_results',
			'filter_range' => true,
			'order_types'  => wc_get_order_types( 'order-count' ),
			'order_status' => array( 'completed', 'processing', 'on-hold', 'refunded' ),
		) );

		// All items from orders - even those refunded
		$this->report_data->order_items = (array) $this->get_order_report_data( array(
			'data' => array(
				'_qty' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => 'SUM',
					'name'            => 'order_item_count',
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
			),
			'where' => array(
				array(
					'key'      => 'order_items.order_item_type',
					'value'    => 'line_item',
					'operator' => '=',
				),
			),
			'group_by'            => $this->group_by_query,
			'order_by'            => 'post_date ASC',
			'query_type'          => 'get_results',
			'filter_range'        => true,
			'order_types'         => wc_get_order_types( 'order-count' ),
			'order_status'        => array( 'completed', 'processing', 'on-hold', 'refunded' ),
		) );

		/**
		 * Get total of fully refunded items.
		 */
		$this->report_data->refunded_order_items = absint( $this->get_order_report_data( array(
			'data' => array(
				'_qty' => array(
					'type'            => 'order_item_meta',
					'order_item_type' => 'line_item',
					'function'        => 'SUM',
					'name'            => 'order_item_count',
				),
			),
			'where' => array(
				array(
					'key'      => 'order_items.order_item_type',
					'value'    => 'line_item',
					'operator' => '=',
				),
			),
			'query_type'          => 'get_var',
			'filter_range'        => true,
			'order_types'         => wc_get_order_types( 'order-count' ),
			'order_status'        => array( 'refunded' ),
		) ) );

		/**
		 * Order totals by date. Charts should show GROSS amounts to avoid going -ve.
		 */
		$this->report_data->orders = (array) $this->get_order_report_data( array(
			'data' => array(
				'_order_total' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_sales',
				),
				'_order_shipping' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_shipping',
				),
				'_order_tax' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_tax',
				),
				'_order_shipping_tax' => array(
					'type'     => 'meta',
					'function' => 'SUM',
					'name'     => 'total_shipping_tax',
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
			),
			'group_by'            => $this->group_by_query,
			'order_by'            => 'post_date ASC',
			'query_type'          => 'get_results',
			'filter_range'        => true,
			'order_types'         => wc_get_order_types( 'sales-reports' ),
			'order_status'        => array( 'completed', 'processing', 'on-hold', 'refunded' ),
		) );

		/**
		 * If an order is 100% refunded we should look at the parent's totals, but the refunds dates.
		 * We also need to ensure each parent order's values are only counted/summed once.
		 */
		$this->report_data->full_refunds = (array) $this->get_order_report_data( array(
			'data' => array(
				'_order_total' => array(
					'type'     => 'parent_meta',
					'function' => '',
					'name'     => 'total_refund',
				),
				'_order_shipping' => array(
					'type'     => 'parent_meta',
					'function' => '',
					'name'     => 'total_shipping',
				),
				'_order_tax' => array(
					'type'     => 'parent_meta',
					'function' => '',
					'name'     => 'total_tax',
				),
				'_order_shipping_tax' => array(
					'type'     => 'parent_meta',
					'function' => '',
					'name'     => 'total_shipping_tax',
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
			),
			'group_by'            => 'posts.post_parent',
			'query_type'          => 'get_results',
			'filter_range'        => true,
			'order_status'        => false,
			'parent_order_status' => array( 'refunded' ),
		) );

		/**
		 * Partial refunds. This includes line items, shipping and taxes. Not grouped by date.
		 */
		$this->report_data->partial_refunds = (array) $this->get_order_report_data( array(
			'data' => array(
				'ID' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'refund_id',
				),
				'_refund_amount' => array(
					'type'     => 'meta',
					'function' => '',
					'name'     => 'total_refund',
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
				'order_item_type' => array(
					'type'      => 'order_item',
					'function'  => '',
					'name'      => 'item_type',
					'join_type' => 'LEFT',
				),
				'_order_total' => array(
					'type'     => 'meta',
					'function' => '',
					'name'     => 'total_sales',
				),
				'_order_shipping' => array(
					'type'      => 'meta',
					'function'  => '',
					'name'      => 'total_shipping',
					'join_type' => 'LEFT',
				),
				'_order_tax' => array(
					'type'      => 'meta',
					'function'  => '',
					'name'      => 'total_tax',
					'join_type' => 'LEFT',
				),
				'_order_shipping_tax' => array(
					'type'      => 'meta',
					'function'  => '',
					'name'      => 'total_shipping_tax',
					'join_type' => 'LEFT',
				),
				'_qty' => array(
					'type'            => 'order_item_meta',
					'function'        => 'SUM',
					'name'            => 'order_item_count',
					'join_type'       => 'LEFT',
				),
			),
			'group_by'            => 'refund_id',
			'order_by'            => 'post_date ASC',
			'query_type'          => 'get_results',
			'filter_range'        => true,
			'order_status'        => false,
			'parent_order_status' => array( 'completed', 'processing', 'on-hold' ),
		) );

		/**
		 * Refund lines - all partial refunds on all order types so we can plot full AND partial refunds on the chart.
		 */
		$this->report_data->refund_lines = (array) $this->get_order_report_data( array(
			'data' => array(
				'ID' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'refund_id',
				),
				'_refund_amount' => array(
					'type'     => 'meta',
					'function' => '',
					'name'     => 'total_refund',
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
				'order_item_type' => array(
					'type'      => 'order_item',
					'function'  => '',
					'name'      => 'item_type',
					'join_type' => 'LEFT',
				),
				'_order_total' => array(
					'type'     => 'meta',
					'function' => '',
					'name'     => 'total_sales',
				),
				'_order_shipping' => array(
					'type'      => 'meta',
					'function'  => '',
					'name'      => 'total_shipping',
					'join_type' => 'LEFT',
				),
				'_order_tax' => array(
					'type'      => 'meta',
					'function'  => '',
					'name'      => 'total_tax',
					'join_type' => 'LEFT',
				),
				'_order_shipping_tax' => array(
					'type'      => 'meta',
					'function'  => '',
					'name'      => 'total_shipping_tax',
					'join_type' => 'LEFT',
				),
				'_qty' => array(
					'type'            => 'order_item_meta',
					'function'        => 'SUM',
					'name'            => 'order_item_count',
					'join_type'       => 'LEFT',
				),
			),
			'group_by'            => 'refund_id',
			'order_by'            => 'post_date ASC',
			'query_type'          => 'get_results',
			'filter_range'        => true,
			'order_status'        => false,
			'parent_order_status' => array( 'completed', 'processing', 'on-hold', 'refunded' ),
		) );

		/**
		 * Total up refunds. Note: when an order is fully refunded, a refund line will be added.
		 */
		$this->report_data->total_tax_refunded          = 0;
		$this->report_data->total_shipping_refunded     = 0;
		$this->report_data->total_shipping_tax_refunded = 0;
		$this->report_data->total_refunds               = 0;

		$refunded_orders = array_merge( $this->report_data->partial_refunds, $this->report_data->full_refunds );

		foreach ( $refunded_orders as $key => $value ) {
			$this->report_data->total_tax_refunded          += floatval( $value->total_tax < 0 ? $value->total_tax * -1 : $value->total_tax );
			$this->report_data->total_refunds               += floatval( $value->total_refund );
			$this->report_data->total_shipping_tax_refunded += floatval( $value->total_shipping_tax < 0 ? $value->total_shipping_tax * -1 : $value->total_shipping_tax );
			$this->report_data->total_shipping_refunded     += floatval( $value->total_shipping < 0 ? $value->total_shipping * -1 : $value->total_shipping );

			// Only applies to parial.
			if ( isset( $value->order_item_count ) ) {
				$this->report_data->refunded_order_items    += floatval( $value->order_item_count < 0 ? $value->order_item_count * -1 : $value->order_item_count );
			}
		}

		// Totals from all orders - including those refunded. Subtract refunded amounts.
		$this->report_data->total_tax          = wc_format_decimal( array_sum( wp_list_pluck( $this->report_data->orders, 'total_tax' ) ) - $this->report_data->total_tax_refunded, 2 );
		$this->report_data->total_shipping     = wc_format_decimal( array_sum( wp_list_pluck( $this->report_data->orders, 'total_shipping' ) ) - $this->report_data->total_shipping_refunded, 2 );
		$this->report_data->total_shipping_tax = wc_format_decimal( array_sum( wp_list_pluck( $this->report_data->orders, 'total_shipping_tax' ) ) - $this->report_data->total_shipping_tax_refunded, 2 );

		// Total the refunds and sales amounts. Sales subract refunds. Note - total_sales also includes shipping costs.
		$this->report_data->total_sales = wc_format_decimal( array_sum( wp_list_pluck( $this->report_data->orders, 'total_sales' ) ) - $this->report_data->total_refunds, 2 );
		$this->report_data->net_sales   = wc_format_decimal( $this->report_data->total_sales - $this->report_data->total_shipping - max( 0, $this->report_data->total_tax ) - max( 0, $this->report_data->total_shipping_tax ), 2 );

		// Calculate average based on net
		$this->report_data->average_sales       = wc_format_decimal( $this->report_data->net_sales / ( $this->chart_interval + 1 ), 2 );
		$this->report_data->average_total_sales = wc_format_decimal( $this->report_data->total_sales / ( $this->chart_interval + 1 ), 2 );

		// Total orders and discounts also includes those which have been refunded at some point
		$this->report_data->total_coupons         = number_format( array_sum( wp_list_pluck( $this->report_data->coupons, 'discount_amount' ) ), 2, '.', '' );
		$this->report_data->total_refunded_orders = absint( count( $this->report_data->full_refunds ) );

		// Total orders in this period, even if refunded.
		$this->report_data->total_orders          = absint( array_sum( wp_list_pluck( $this->report_data->order_counts, 'count' ) ) );

		// Item items ordered in this period, even if refunded.
		$this->report_data->total_items = absint( array_sum( wp_list_pluck( $this->report_data->order_items, 'order_item_count' ) ) );

		// 3rd party filtering of report data
		$this->report_data = apply_filters( 'woocommerce_admin_report_data', $this->report_data );
	}

	/**
	 * Get the legend for the main chart sidebar.
	 * @return array
	 */
	public function get_chart_legend() {
		$legend = array();
		$data   = $this->get_report_data();

		switch ( $this->chart_groupby ) {
			case 'day' :
				/* translators: %s: average total sales */
				$average_total_sales_title = sprintf(
					__( '%s average gross daily sales', 'woocommerce' ),
					'<strong>' . wc_price( $data->average_total_sales ) . '</strong>'
				);
				/* translators: %s: average sales */
				$average_sales_title = sprintf(
					__( '%s average net daily sales', 'woocommerce' ),
					'<strong>' . wc_price( $data->average_sales ) . '</strong>'
				);
			break;
			case 'month' :
			default :
				/* translators: %s: average total sales */
				$average_total_sales_title = sprintf(
					__( '%s average gross monthly sales', 'woocommerce' ),
					'<strong>' . wc_price( $data->average_total_sales ) . '</strong>'
				);
				/* translators: %s: average sales */
				$average_sales_title = sprintf(
					__( '%s average net monthly sales', 'woocommerce' ),
					'<strong>' . wc_price( $data->average_sales ) . '</strong>'
				);
			break;
		}

		$legend[] = array(
			/* translators: %s: total sales */
			'title' => sprintf(
				__( '%s gross sales in this period', 'woocommerce' ),
				'<strong>' . wc_price( $data->total_sales ) . '</strong>'
			),
			'placeholder'      => __( 'This is the sum of the order totals after any refunds and including shipping and taxes.', 'woocommerce' ),
			'color'            => $this->chart_colours['sales_amount'],
			'highlight_series' => 6,
		);
		if ( $data->average_total_sales > 0 ) {
			$legend[] = array(
				'title' => $average_total_sales_title,
				'color' => $this->chart_colours['average'],
				'highlight_series' => 2,
			);
		}

		$legend[] = array(
			/* translators: %s: net sales */
			'title' => sprintf(
				__( '%s net sales in this period', 'woocommerce' ),
				'<strong>' . wc_price( $data->net_sales ) . '</strong>'
			),
			'placeholder'      => __( 'This is the sum of the order totals after any refunds and excluding shipping and taxes.', 'woocommerce' ),
			'color'            => $this->chart_colours['net_sales_amount'],
			'highlight_series' => 7,
		);
		if ( $data->average_sales > 0 ) {
			$legend[] = array(
				'title' => $average_sales_title,
				'color' => $this->chart_colours['net_average'],
				'highlight_series' => 3,
			);
		}

		$legend[] = array(
			/* translators: %s: total orders */
			'title' => sprintf(
				__( '%s orders placed', 'woocommerce' ),
				'<strong>' . $data->total_orders . '</strong>'
			),
			'color' => $this->chart_colours['order_count'],
			'highlight_series' => 1,
		);

		$legend[] = array(
			/* translators: %s: total items */
			'title' => sprintf(
				__( '%s items purchased', 'woocommerce' ),
				'<strong>' . $data->total_items . '</strong>'
			),
			'color' => $this->chart_colours['item_count'],
			'highlight_series' => 0,
		);
		$legend[] = array(
			/* translators: 1: total refunds 2: total refunded orders 3: refunded items */
			'title' => sprintf(
				_n( '%1$s refunded %2$d order (%3$d item)', '%1$s refunded %2$d orders (%3$d items)', $this->report_data->total_refunded_orders, 'woocommerce' ),
				'<strong>' . wc_price( $data->total_refunds ) . '</strong>',
				$this->report_data->total_refunded_orders,
				$this->report_data->refunded_order_items
			),
			'color' => $this->chart_colours['refund_amount'],
			'highlight_series' => 8,
		);
		$legend[] = array(
			/* translators: %s: total shipping */
			'title' => sprintf(
				__( '%s charged for shipping', 'woocommerce' ),
				'<strong>' . wc_price( $data->total_shipping ) . '</strong>'
			),
			'color' => $this->chart_colours['shipping_amount'],
			'highlight_series' => 5,
		);
		$legend[] = array(
			/* translators: %s: total coupons */
			'title' => sprintf(
				__( '%s worth of coupons used', 'woocommerce' ),
				'<strong>' . wc_price( $data->total_coupons ) . '</strong>'
			),
			'color' => $this->chart_colours['coupon_amount'],
			'highlight_series' => 4,
		);

		return $legend;
	}

	/**
	 * Output the report.
	 */
	public function output_report() {
		$ranges = array(
			'year'         => __( 'Year', 'woocommerce' ),
			'last_month'   => __( 'Last month', 'woocommerce' ),
			'month'        => __( 'This month', 'woocommerce' ),
			'7day'         => __( 'Last 7 days', 'woocommerce' ),
		);

		$this->chart_colours = array(
			'sales_amount'     => '#b1d4ea',
			'net_sales_amount' => '#3498db',
			'average'          => '#b1d4ea',
			'net_average'      => '#3498db',
			'order_count'      => '#dbe1e3',
			'item_count'       => '#ecf0f1',
			'shipping_amount'  => '#5cc488',
			'coupon_amount'    => '#f1c40f',
			'refund_amount'    => '#e74c3c',
		);

		$current_range = $this->current_range;
		
		$this->calculate_current_range( $this->current_range );

		include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php' );
	}

	/**
	 * Output an export link.
	 */
	public function get_export_button() {
		return;
		?>
		<a
			href="#"
			download="report-<?php echo esc_attr( $this->current_range ); ?>-<?php echo date_i18n( 'Y-m-d', current_time( 'timestamp' ) ); ?>.csv"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php esc_attr_e( 'Date', 'woocommerce' ); ?>"
			data-exclude_series="2"
			data-groupby="<?php echo $this->chart_groupby; ?>"
		>
			<?php _e( 'Export CSV', 'woocommerce' ); ?>
		</a>
		<?php
	}

	/**
	 * Round our totals correctly.
	 *
	 * @param array|string $amount
	 *
	 * @return array|string
	 */
	private function round_chart_totals( $amount ) {
		if ( is_array( $amount ) ) {
			return array( $amount[0], wc_format_decimal( $amount[1], wc_get_price_decimals() ) );
		} else {
			return wc_format_decimal( $amount, wc_get_price_decimals() );
		}
	}

	/**
	 * Get the main chart.
	 *
	 * @return string
	 */
	public function get_main_chart( $show_legend = 1 ) {
		global $wp_locale, $WCFM;
		
		// Prepare data for report
		$data = array(
			'order_counts'         => $this->prepare_chart_data( $this->report_data->order_counts, 'post_date', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby ),
			'order_item_counts'    => $this->prepare_chart_data( $this->report_data->order_items, 'post_date', 'order_item_count', $this->chart_interval, $this->start_date, $this->chart_groupby ),
			'order_amounts'        => $this->prepare_chart_data( $this->report_data->orders, 'post_date', 'total_sales', $this->chart_interval, $this->start_date, $this->chart_groupby ),
			'coupon_amounts'       => $this->prepare_chart_data( $this->report_data->coupons, 'post_date', 'discount_amount', $this->chart_interval, $this->start_date, $this->chart_groupby ),
			'shipping_amounts'     => $this->prepare_chart_data( $this->report_data->orders, 'post_date', 'total_shipping', $this->chart_interval, $this->start_date, $this->chart_groupby ),
			'refund_amounts'       => $this->prepare_chart_data( $this->report_data->refund_lines, 'post_date', 'total_refund', $this->chart_interval, $this->start_date, $this->chart_groupby ),
			'shipping_tax_amounts' => $this->prepare_chart_data( $this->report_data->orders, 'post_date', 'total_shipping_tax', $this->chart_interval, $this->start_date, $this->chart_groupby ),
			'tax_amounts'          => $this->prepare_chart_data( $this->report_data->orders, 'post_date', 'total_tax', $this->chart_interval, $this->start_date, $this->chart_groupby ),
			'net_order_amounts'    => array(),
			'gross_order_amounts'  => array(),
		);

		foreach ( $data['order_amounts'] as $order_amount_key => $order_amount_value ) {
			$data['gross_order_amounts'][ $order_amount_key ]    = $order_amount_value;
			$data['gross_order_amounts'][ $order_amount_key ][1] -= $data['refund_amounts'][ $order_amount_key ][1];

			$data['net_order_amounts'][ $order_amount_key ]    = $order_amount_value;
			// subtract the sum of the values from net order amounts
			$data['net_order_amounts'][ $order_amount_key ][1] -=
				$data['refund_amounts'][ $order_amount_key ][1] +
				$data['shipping_amounts'][ $order_amount_key ][1] +
				$data['shipping_tax_amounts'][ $order_amount_key ][1] +
				$data['tax_amounts'][ $order_amount_key ][1];
		}

		// 3rd party filtering of report data
		$data = apply_filters( 'woocommerce_admin_report_chart_data', $data );

		// Encode in json format
		$chart_data = '{'
			. '  "order_counts"             : ' . $WCFM->wcfm_prepare_chart_data( $data['order_counts'] )
			. ', "order_item_counts"        : ' . $WCFM->wcfm_prepare_chart_data( $data['order_item_counts'] )
			. ', "shipping_amounts"         : ' . $WCFM->wcfm_prepare_chart_data( $data['shipping_amounts'] )
			. ', "refund_amounts"           : ' . $WCFM->wcfm_prepare_chart_data( $data['refund_amounts'] )
			. ', "order_amounts"            : ' . $WCFM->wcfm_prepare_chart_data( $data['order_amounts'] )
			. ', "gross_order_amounts"      : ' . $WCFM->wcfm_prepare_chart_data( $data['gross_order_amounts'] )
			. ', "net_order_amounts"        : ' . $WCFM->wcfm_prepare_chart_data( $data['net_order_amounts'] )
			. ', "coupon_amounts"           : ' . $WCFM->wcfm_prepare_chart_data( $data['coupon_amounts'] )
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
							  labels: sales_data.gross_order_amounts.labels,
								datasets: [
								      {
												type: 'line',
												label: "<?php _e( 'Gross Sales', 'wc-frontend-manager' ); ?>",
												backgroundColor: color(window.chartColors.blue).alpha(0.2).rgbString(),
												borderColor: window.chartColors.blue,
												fill: true,
												data: sales_data.gross_order_amounts.datas,
											},
											{
												type: 'line',
												label: "<?php _e( 'Shipping', 'wc-frontend-manager' ); ?>",
												backgroundColor: color(window.chartColors.red).alpha(0.2).rgbString(),
												borderColor: window.chartColors.red,
												fill: true,
												data: sales_data.shipping_amounts.datas,
											},
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
											{
												type: 'line',
												label: "<?php _e( 'Net Sales', 'wc-frontend-manager' ); ?>",
												backgroundColor: color(window.chartColors.green).alpha(0.2).rgbString(),
												borderColor: window.chartColors.green,
												fill: true,
												data: sales_data.net_order_amounts.datas,
											},
											{
												type: 'line',
												label: "<?php _e( 'Coupon Amounts', 'wc-frontend-manager' ); ?>",
												backgroundColor: color(window.chartColors.purple).alpha(0.2).rgbString(),
												borderColor: window.chartColors.purple,
												fill: true,
												data: sales_data.coupon_amounts.datas,
											},
											{
												type: 'line',
												label: "<?php _e( 'Refund Amounts', 'wc-frontend-manager' ); ?>",
												backgroundColor: color(window.chartColors.grey).alpha(0.2).rgbString(),
												borderColor: window.chartColors.grey,
												fill: true,
												data: sales_data.refund_amounts.datas,
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
