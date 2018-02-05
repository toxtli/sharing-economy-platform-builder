<?php

/**
 * WCMp Report Sales By Product
 *
 * @author      WC Marketplace
 * @category    Vendor
 * @package     WCMp/Reports
 * @version     2.2.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WCMp_Report_Product extends WC_Admin_Report {

    /**
     * Output the report
     */
    public function output_report() {
        global $wpdb, $woocommerce, $WCMp;

        $ranges = array(
            'year' => __('Year', 'dc-woocommerce-multi-vendor'),
            'last_month' => __('Last Month', 'dc-woocommerce-multi-vendor'),
            'month' => __('This Month', 'dc-woocommerce-multi-vendor'),
            '7day' => __('Last 7 Days', 'dc-woocommerce-multi-vendor')
        );

        $current_range = !empty($_GET['range']) ? sanitize_text_field($_GET['range']) : '7day';

        if (!in_array($current_range, array('custom', 'year', 'last_month', 'month', '7day'))) {
            $current_range = '7day';
        }

        $this->calculate_current_range($current_range);

        if (isset($_POST['search_product'])) {
            $is_variation = false;
            $product_id = $_POST['search_product'];

            $_product = wc_get_product($product_id);

            if ($_product->is_type('variation')) {
                $title = $_product->get_formatted_name();
                $is_variation = true;
            } else {
                $title = $_product->get_title();
            }
        }

        if (isset($product_id)) {
            $option = '<option value="' . $product_id . '" selected="selected">' . $title . '</option>';
        } else {
            $option = '<option></option>';
        }

        $start_date = $this->start_date;
        $end_date = $this->end_date;
        $end_date = strtotime('+1 day', $end_date);

        $total_sales = $admin_earnings = array();
        $max_total_sales = $index = 0;
        $product_report = $report_bk = array();


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
                'inclusive' => true,
                'after' => array(
                    'year' => date('Y', $this->start_date),
                    'month' => date('n', $this->start_date),
                    'day' => date('j', $this->start_date),
                ),
                'before' => array(
                    'year' => date('Y', $this->end_date),
                    'month' => date('n', $this->end_date),
                    'day' => date('j', $this->end_date),
                ),
            )
        );

        $qry = new WP_Query($args);

        $orders = apply_filters('wcmp_filter_orders_report_product', $qry->get_posts());

        if (!empty($orders)) {

            $gross_sales = $my_earning = $vendor_earning = 0;
            foreach ($orders as $order_obj) {
                $order = wc_get_order($order_obj->ID);
                if ($vendor = get_wcmp_vendor(get_current_vendor_id())) {
                    $vendors_orders = get_wcmp_vendor_orders(array('order_id' => $order->get_id(), 'vendor_id' => get_current_vendor_id()));
                } else {
                    $vendors_orders = get_wcmp_vendor_orders(array('order_id' => $order->get_id()));
                }

                foreach ($vendors_orders as $key => $v_order) {
                    try {
                        $item = new WC_Order_Item_Product($v_order->order_item_id);
                        $gross_sales += $item->get_subtotal();
                        $total_sales[$v_order->product_id] = isset($total_sales[$v_order->product_id]) ? ( $total_sales[$v_order->product_id] + $item->get_subtotal() ) : $item->get_subtotal();
                        $vendors_orders_amount = get_wcmp_vendor_order_amount(array('order_id' => $order->get_id(), 'product_id' => $v_order->product_id));

                        $vendor_earning = $vendors_orders_amount['commission_amount'];
                        if ($vendor = get_wcmp_vendor(get_current_vendor_id())) {
                            $admin_earnings[$v_order->product_id] = isset($admin_earnings[$v_order->product_id]) ? ( $admin_earnings[$v_order->product_id] + $vendor_earning ) : $vendor_earning;
                        } else {
                            $admin_earnings[$v_order->product_id] = isset($admin_earnings[$v_order->product_id]) ? ( $admin_earnings[$v_order->product_id] + $item->get_subtotal() - $vendor_earning ) : $item->get_subtotal() - $vendor_earning;
                        }

                        if ($total_sales[$v_order->product_id] > $max_total_sales) {
                            $max_total_sales = $total_sales[$v_order->product_id];
                        }

                        if (!empty($total_sales[$v_order->product_id]) && !empty($admin_earnings[$v_order->product_id])) {
                            $product_report[$index]['product_id'] = $v_order->product_id;
                            $product_report[$index]['total_sales'] = $total_sales[$v_order->product_id];
                            $product_report[$index++]['admin_earning'] = $admin_earnings[$v_order->product_id];

                            $report_bk[$v_order->product_id]['total_sales'] = $total_sales[$v_order->product_id];
                            $report_bk[$v_order->product_id]['admin_earning'] = $admin_earnings[$v_order->product_id];
                        }
                    } catch (Exception $ex) {
                        
                    }
                }
            }


            $i = 0;
            $max_value = 10;
            $report_sort_arr = array();
            $total_sales_sort = $admin_earning_sort = array();
            if (!empty($product_report) && !empty($report_bk)) {
                $total_sales_sort = wp_list_pluck($product_report, 'total_sales', 'product_id');
                $admin_earning_sort = wp_list_pluck($product_report, 'admin_earning', 'product_id');

                foreach ($total_sales_sort as $key => $value) {
                    $total_sales_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
                    $total_sales_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
                }

                arsort($total_sales_sort);
                foreach ($total_sales_sort as $product_id => $value) {
                    if ($i++ < $max_value) {
                        $report_sort_arr[$product_id]['total_sales'] = $report_bk[$product_id]['total_sales'];
                        $report_sort_arr[$product_id]['admin_earning'] = $report_bk[$product_id]['admin_earning'];
                    }
                }
            }

            wp_localize_script('wcmp_report_js', 'wcmp_report_product', array('product_report' => $product_report,
                'report_bk' => $report_bk,
                'total_sales_sort' => $total_sales_sort,
                'admin_earning_sort' => $admin_earning_sort,
                'max_total_sales' => $max_total_sales,
                'start_date' => $start_date,
                'end_date' => $end_date
            ));

            $report_chart = $report_html = '';
            if (sizeof($report_sort_arr) > 0) {
                foreach ($report_sort_arr as $product_id => $sales_report) {
                    $width = ( $sales_report['total_sales'] > 0 ) ? ( round($sales_report['total_sales']) / round($max_total_sales) ) * 100 : 0;
                    $width2 = ( $sales_report['admin_earning'] > 0 ) ? ( round($sales_report['admin_earning']) / round($max_total_sales) ) * 100 : 0;

                    if ($product = wc_get_product($product_id)) {

                        $product_url = admin_url('post.php?post=' . $product_id . '&action=edit');

                        $report_chart .= '<tr><th><a href="' . $product_url . '">' . $product->get_title() . '</a></th>
						<td width="1%"><span>' . wc_price($sales_report['total_sales']) . '</span><span class="alt">' . wc_price($sales_report['admin_earning']) . '</span></td>
						<td class="bars">
							<span style="width:' . esc_attr($width) . '%">&nbsp;</span>
							<span class="alt" style="width:' . esc_attr($width2) . '%">&nbsp;</span>
						</td></tr>';
                    }
                }

                $report_html = '
					<h4>' . __("Sales and Earnings", 'dc-woocommerce-multi-vendor') . '</h4>
					<div class="bar_indecator">
						<div class="bar1">&nbsp;</div>
						<span class="">' . __("Gross Sales", 'dc-woocommerce-multi-vendor') . '</span>
						<div class="bar2">&nbsp;</div>
						<span class="">' . __("My Earnings", 'dc-woocommerce-multi-vendor') . '</span>
					</div>
					<table class="bar_chart">
						<thead>
							<tr>
								<th>' . __("Month", 'dc-woocommerce-multi-vendor') . '</th>
								<th colspan="2">' . __("Sales Report", 'dc-woocommerce-multi-vendor') . '</th>
							</tr>
						</thead>
						<tbody>
							' . $report_chart . '
						</tbody>
					</table>
				';
            } else {
                $report_html = '<tr><td colspan="3">' . __('No product was sold in the given period.', 'dc-woocommerce-multi-vendor') . '</td></tr>';
            }
        } else {
            $report_html = '<tr><td colspan="3">' . __('Your store has no products.', 'dc-woocommerce-multi-vendor') . '</td></tr>';
        }

        include( $WCMp->plugin_path . '/classes/reports/views/html-wcmp-report-by-product.php');
    }

}

?>
