<?php
/**
 * WCMp Report Sales By Date
 *
 * @author      WC Marketplace
 * @category    Vendor
 * @package     WCMp/Reports
 * @version     2.2.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WCMp_Report_Overview extends WC_Admin_Report {

    public $chart_colours = array();
    private $report_data;

    /**
     * Get report data
     * @return array
     */
    public function get_report_data() {
        if (empty($this->report_data)) {
            $this->query_report_data();
        }
        return $this->report_data;
    }

    /**
     * Get all data needed for this report and store in the class
     */
    private function query_report_data() {
        global $WCMp;
        $this->report_data = new stdClass;

        $start_date = $this->start_date;
        $end_date = $this->end_date;
        $chart_data_order_count = array();

        $total_order_count = 0;
        $total_earnings = 0;
        $total_sales = 0;
        $total_vendor_earnings = 0;

        for ($date = $start_date; $date <= strtotime('+1 day', $end_date); $date = strtotime('+1 day', $date)) {

            $year = date('Y', $date);
            $month = date('n', $date);
            $day = date('j', $date);

            $line_total = $sales = $comm_amount = $vendor_earnings = $earnings = $order_count = 0;

            $args = array(
                'post_type' => 'shop_order',
                'posts_per_page' => -1,
                'post_status' => array('wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-cancelled', 'wc-refunded', 'wc-failed'),
                'meta_query' => array(
                    array(
                        'key' => '_commissions_processed',
                        'value' => 'yes',
                        'compare' => '='
                    )
                ),
                'date_query' => array(
                    array(
                        'year' => $year,
                        'month' => $month,
                        'day' => $day,
                    ),
                )
            );

            $qry = new WP_Query($args);

            $orders = apply_filters('wcmp_filter_orders_report_overview', $qry->get_posts());

            if (!empty($orders)) {
                foreach ($orders as $order_obj) { 

                    $order = new WC_Order($order_obj->ID);
                    $items = $order->get_items('line_item');
                    if (is_user_wcmp_vendor(get_current_vendor_id())) {
                        $vendor_id = get_user_meta(get_current_vendor_id(), '_vendor_term_id', true);
                        if (!in_array($vendor_id, get_vendor_from_an_order($order))) {
                            continue;
                        }
                    }

                    $vendors_orders = get_wcmp_vendor_orders(array('order_id' => $order->get_id()));
                    if(is_user_wcmp_vendor(get_current_vendor_id()))
                        $vendors_orders_amount = get_wcmp_vendor_order_amount(array('order_id' => $order->get_id()),get_current_vendor_id());
                    else
                        $vendors_orders_amount = get_wcmp_vendor_order_amount(array('order_id' => $order->get_id()));
                    
                    if(is_user_wcmp_vendor(get_current_vendor_id())){
                       
                        $sales += $vendors_orders_amount['total'] - $vendors_orders_amount['commission_amount'];
                        $total_sales += $vendors_orders_amount['total'] - $vendors_orders_amount['commission_amount'];
                        $vendor_earnings += $vendors_orders_amount['total'];
                        $total_vendor_earnings += $vendors_orders_amount['total'];
                        $total_earnings += $vendors_orders_amount['total'];
                        $earnings += $vendors_orders_amount['total'];
                        $vendor_id = get_current_vendor_id();
                        $current_vendor_orders = wp_list_filter($vendors_orders, array('vendor_id'=>$vendor_id));

                        foreach ($current_vendor_orders as $key => $vendor_order) { 
                            $item = new WC_Order_Item_Product($vendor_order->order_item_id);
                            $total_sales += $item->get_subtotal();
                            $sales += $item->get_subtotal();
                        }
                        
                    }else{
                        $total_sales += $order->get_total();
                        $sales += $order->get_total();
                        $vendor_earnings += $vendors_orders_amount['commission_amount'];
                        $total_vendor_earnings += $vendors_orders_amount['commission_amount'];
                        $earnings += $order->get_total() - $vendors_orders_amount['total'];
                        $total_earnings += $order->get_total() - $vendors_orders_amount['total'];

                    }

                    ++$order_count;
                    ++$total_order_count; 
                }
                

            }

            //print_r($vendor_earnings);
            if ($order_count > 0)
                $avg_sales = $sales / ($this->chart_interval + 1);
            else
                $avg_sales = 0;

            $chart_data_order_count[] = wcmpArrayToObject(array('post_date' => date("Y-m-d H:i:s", $date), 'count' => $order_count));
            $chart_data_sales[] = wcmpArrayToObject(array('post_date' => date("Y-m-d H:i:s", $date), 'sales' => $sales));
            $chart_data_vendor_earnings[] = wcmpArrayToObject(array('post_date' => date("Y-m-d H:i:s", $date), 'vendor_earnings' => $vendor_earnings));
            $chart_data_earnings[] = wcmpArrayToObject(array('post_date' => date("Y-m-d H:i:s", $date), 'earnings' => $earnings));
            $chart_data_avg_sales[] = wcmpArrayToObject(array('post_date' => date("Y-m-d H:i:s", $date), 'avg_sales' => $avg_sales));
        }

        $this->report_data->order_count = $chart_data_order_count;
        $this->report_data->sales = $chart_data_sales;

        $this->report_data->earnings = $chart_data_earnings;
        $this->report_data->avg_sales = $chart_data_avg_sales;

        $this->report_data->vendor_earnings = $chart_data_vendor_earnings;

        // Total order_count
        $this->report_data->total_orders = $total_order_count;

        // Total the refunds and sales amounts. Sales subract refunds.
        $this->report_data->total_sales = wc_format_decimal($total_sales);

        // Calculate average based on net
        $this->report_data->average_sales = wc_format_decimal($total_sales / ( $this->chart_interval + 1 ));


        $this->report_data->total_earned = wc_format_decimal($total_earnings);

        $this->report_data->vendor_total_earned = wc_format_decimal($total_vendor_earnings);

    }

    /**
     * Get the legend for the main chart sidebar
     * @return array
     */
    public function get_chart_legend() {
        global $WCMp;

        $legend = array();
        $data = $this->get_report_data();

        switch ($this->chart_groupby) {
            case 'day' :
                $average_sales_title = sprintf(__('%s Average Daily Sales', 'dc-woocommerce-multi-vendor'), '<strong>' . wc_price($data->average_sales) . '</strong>');
                break;
            case 'month' :
            default :
                $average_sales_title = sprintf(__('%s Average Monthly Sales', 'dc-woocommerce-multi-vendor'), '<strong>' . wc_price($data->average_sales) . '</strong>');
                break;
        }

        $legend[] = array(
            'title' => sprintf(__('%s Gross Sales in this Period', 'dc-woocommerce-multi-vendor'), '<strong>' . wc_price($data->total_sales) . '</strong>'),
            'placeholder' => __('This is the sum of the order totals', 'dc-woocommerce-multi-vendor'),
            'color' => $this->chart_colours['total_sales'],
            'highlight_series' => 1
        );

        if ($data->average_sales > 0) {
            $legend[] = array(
                'title' => $average_sales_title,
                'color' => $this->chart_colours['average_sales'],
                'highlight_series' => 2
            );
        }
        $legend[] = array(
            'title' => sprintf(__('%s Number of Orders Placed', 'dc-woocommerce-multi-vendor'), '<strong>' . $data->total_orders . '</strong>'),
            'color' => $this->chart_colours['total_orders'],
            'highlight_series' => 0
        );

        $legend[] = array(
            'title' => sprintf(__('%s Net Earnings in this Period', 'dc-woocommerce-multi-vendor'), '<strong>' . $data->total_earned . '</strong>'),
            'color' => $this->chart_colours['total_earned'],
            'highlight_series' => 3
        );

        if (!is_user_wcmp_vendor(get_current_vendor_id())) {
            $legend[] = array(
                'title' => sprintf(__('%s Net Vendor Commission', 'dc-woocommerce-multi-vendor'), '<strong>' . $data->vendor_total_earned . '</strong>'),
                'color' => $this->chart_colours['vendor_total_earned'],
                'highlight_series' => 4
            );
        }


        return $legend;
    }

    /**
     * Output the report
     */
    public function output_report() {
        global $WCMp;
        $ranges = array(
            'year' => __('Year', 'dc-woocommerce-multi-vendor'),
            'last_month' => __('Last Month', 'dc-woocommerce-multi-vendor'),
            'month' => __('This Month', 'dc-woocommerce-multi-vendor'),
            '7day' => __('Last 7 Days', 'dc-woocommerce-multi-vendor')
        );

        $this->chart_colours = array(
            'total_sales' => '#FF0000',
            'average_sales' => '#FF9B00',
            'total_orders' => '#E4D8EF',
            'total_earned' => '#01CCFF',
            'vendor_total_earned' => '#5cc488',
        );

        $current_range = !empty($_GET['range']) ? sanitize_text_field($_GET['range']) : '7day';

        if (!in_array($current_range, array('custom', 'year', 'last_month', 'month', '7day'))) {
            $current_range = '7day';
        }

        $this->calculate_current_range($current_range);

        include( $WCMp->plugin_path . '/classes/reports/views/html-report-by-date.php');
    }

    /**
     * Output an export link
     */
    public function get_export_button() {
        global $WCMp;

        $current_range = !empty($_GET['range']) ? sanitize_text_field($_GET['range']) : '7day';
        ?>
        <a
            href="#"
            download="report-<?php echo esc_attr($current_range); ?>-<?php echo date_i18n('Y-m-d', current_time('timestamp')); ?>.csv"
            class="export_csv"
            data-export="chart"
            data-xaxes="<?php esc_attr_e('Date', 'dc-woocommerce-multi-vendor'); ?>"
            data-exclude_series="2"
            data-groupby="<?php echo $this->chart_groupby; ?>"
            >
                <?php _e('Export CSV', 'dc-woocommerce-multi-vendor'); ?>
        </a>
        <?php
    }

    /**
     * Round our totals correctly
     * @param  string $amount
     * @return string
     */
    private function round_chart_totals($amount) {
        if (is_array($amount)) {
            return array($amount[0], wc_format_decimal($amount[1], wc_get_price_decimals()));
        } else {
            return wc_format_decimal($amount, wc_get_price_decimals());
        }
    }

    /**
     * Get the main chart
     *
     * @return string
     */
    public function get_main_chart() {
        global $wp_locale, $WCMp;
        // Prepare data for report

        $total_orders = $this->prepare_chart_data($this->report_data->order_count, 'post_date', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby);
        $total_sales = $this->prepare_chart_data($this->report_data->sales, 'post_date', 'sales', $this->chart_interval, $this->start_date, $this->chart_groupby);
        $average_sales = $this->prepare_chart_data($this->report_data->avg_sales, 'post_date', 'avg_sales', $this->chart_interval, $this->start_date, $this->chart_groupby);
        $total_earned = $this->prepare_chart_data($this->report_data->earnings, 'post_date', 'earnings', $this->chart_interval, $this->start_date, $this->chart_groupby);
        
        $vendor_total_earned = $this->prepare_chart_data($this->report_data->vendor_earnings, 'post_date', 'vendor_earnings', $this->chart_interval, $this->start_date, $this->chart_groupby);


        // Encode in json format
        $chart_data = array(
            'total_orders' => array_values($total_orders),
            'total_sales' => array_map(array($this, 'round_chart_totals'), array_values($total_sales)),
            'average_sales' => array_map(array($this, 'round_chart_totals'), array_values($average_sales)),
            'total_earned' => array_map(array($this, 'round_chart_totals'), array_values($total_earned)),
        );
        if(!is_user_wcmp_vendor(get_current_vendor_id())){
            $chart_data['vendor_total_earned'] = array_map(array($this, 'round_chart_totals'), array_values($vendor_total_earned));
        }
        
        $chart_data = json_encode($chart_data);
        ?>
        <div class="chart-container">
            <div class="chart-placeholder main"></div>
        </div>
        <script type="text/javascript">

            var main_chart;
            jQuery(function(){
            var order_data = jQuery.parseJSON('<?php echo $chart_data; ?>');
            var drawGraph = function(highlight) {
            var series = [
            {
            label: "<?php echo esc_js(__('Number of Orders', 'dc-woocommerce-multi-vendor')) ?>",
                    data: order_data.total_orders,
                    color: '<?php echo $this->chart_colours['total_orders']; ?>',
                    bars: { fillColor: '<?php echo $this->chart_colours['total_orders']; ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'center' },
                    shadowSize: 0,
            },
            {
            label: "<?php echo esc_js(__('Total Sales', 'dc-woocommerce-multi-vendor')) ?>",
                    data: order_data.total_sales,
                    yaxis: 2,
                    color: '<?php echo $this->chart_colours['total_sales']; ?>',
                    points: { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
                    lines: { show: true, lineWidth: 2, fill: false },
                    shadowSize: 0,
        <?php echo $this->get_currency_tooltip(); ?>
            },
            {
            label: "<?php echo esc_js(__('Average Order Value', 'dc-woocommerce-multi-vendor')) ?>",
                    data: order_data.average_sales,
                    yaxis: 2,
                    color: '<?php echo $this->chart_colours['average_sales']; ?>',
                    points: { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
                    lines: { show: true, lineWidth: 2, fill: false },
                    shadowSize: 0,
        <?php echo $this->get_currency_tooltip(); ?>
            },
            {
            label: "<?php echo esc_js(__('Total Earnings', 'dc-woocommerce-multi-vendor')) ?>",
                    data: order_data.total_earned,
                    yaxis: 2,
                    color: '<?php echo $this->chart_colours['total_earned']; ?>',
                    points: { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
                    lines: { show: true, lineWidth: 2, fill: false },
                    shadowSize: 0,
                    prepend_tooltip: "<?php echo get_woocommerce_currency_symbol(); ?>"
            },
        <?php if(!is_user_wcmp_vendor(get_current_vendor_id())){ ?>
            {
            label: "<?php echo esc_js(__('Total Earnings by Vendor', 'dc-woocommerce-multi-vendor')) ?>",
                    data: order_data.vendor_total_earned,
                    yaxis: 2,
                    color: '<?php echo $this->chart_colours['vendor_total_earned']; ?>',
                    points: { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
                    lines: { show: true, lineWidth: 2, fill: false },
                    shadowSize: 0,
        <?php echo $this->get_currency_tooltip(); ?>
            },
        <?php } ?>
            ];
            if (highlight !== 'undefined' && series[ highlight ]) {
            highlight_series = series[ highlight ];
            highlight_series.color = '#9c5d90';
            if (highlight_series.bars) {
            highlight_series.bars.fillColor = '#9c5d90';
            }

            if (highlight_series.lines) {
            highlight_series.lines.lineWidth = 5;
            }
            }

            main_chart = jQuery.plot(
                    jQuery('.chart-placeholder.main'),
                    series,
            {
            legend: {
            show: false
            },
                    grid: {
                    color: '#aaa',
                            borderColor: 'transparent',
                            borderWidth: 0,
                            hoverable: true
                    },
                    xaxes: [ {
                    color: '#aaa',
                            position: "bottom",
                            tickColor: 'transparent',
                            mode: "time",
                            timeformat: "<?php
        if ($this->chart_groupby == 'day')
            echo '%d %b';
        else
            echo '%b';
        ?>",
                            monthNames: <?php echo json_encode(array_values($wp_locale->month_abbrev)) ?>,
                            tickLength: 1,
                            minTickSize: [1, "<?php echo $this->chart_groupby; ?>"],
                            font: {
                            color: "#aaa"
                            }
                    } ],
                    yaxes: [
                    {
                    min: 0,
                            minTickSize: 1,
                            tickDecimals: 0,
                            color: '#d4d9dc',
                            font: { color: "#aaa" }
                    },
                    {
                    position: "right",
                            min: 0,
                            tickDecimals: 2,
                            alignTicksWithAxis: 1,
                            color: 'transparent',
                            font: { color: "#aaa" }
                    }
                    ],
            }
            );
            jQuery('.chart-placeholder').resize();
            }

            drawGraph();
            jQuery('.highlight_series').hover(
                    function() {
                    drawGraph(jQuery(this).data('series'));
                    },
                    function() {
                    drawGraph();
                    }
            );
            });
        </script>
        <?php
    }

}
?>
