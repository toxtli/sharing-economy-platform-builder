<?php
/**
 * WCMp Report Sales By Vendor
 *
 * @author      WC Marketplace
 * @category    Vendor
 * @package     WCMp/Reports
 * @version     2.2.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WCMp_Report_Vendor extends WC_Admin_Report {

	/**
	 * Output the report
	 */
	public function output_report() {
		global $wpdb, $woocommerce, $WCMp;
		
		$vendor = $vendor_id = $order_items = false;
		
		$ranges = array(
			'year'         => __( 'Year', 'dc-woocommerce-multi-vendor' ),
			'last_month'   => __( 'Last Month', 'dc-woocommerce-multi-vendor' ),
			'month'        => __( 'This Month', 'dc-woocommerce-multi-vendor' ),
			'7day'         => __( 'Last 7 Days', 'dc-woocommerce-multi-vendor' )
		);
		
		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->calculate_current_range( $current_range );
		
		if( isset( $_POST['vendor'] ) ) {
			$vendor_id = $_POST['vendor'];
			$vendor = get_wcmp_vendor_by_term( $vendor_id );
			if($vendor) $products = $vendor->get_products();
			if(!empty($products)) {
				foreach( $products as $product ) {
					$chosen_product_ids[] = $product->ID;
				}
			}
		}
		
		if( $vendor_id && $vendor ) {
			$option = '<option value="' . $vendor_id. '" selected="selected">' . $vendor->user_data->display_name . '</option>';
		} else {
			$option = '<option></option>';
		}
		
		$all_vendors = get_wcmp_vendors();

		$start_date = $this->start_date;
		$end_date = $this->end_date;
		
		$total_sales = $admin_earning = $vendor_report = $report_bk = array();
		$max_total_sales = $i = 0;
		
		if(!empty($all_vendors) && is_array($all_vendors) ) {
			foreach( $all_vendors as $all_vendor ) {
				$gross_sales = $my_earning = $vendor_earning = 0;
				$chosen_product_ids = array();
				$vendor_id = $all_vendor->id;
				$vendor = get_wcmp_vendor($vendor_id);

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
	                    'after' =>array(
	                        'year' => date('Y', $this->start_date),
	                        'month' => date('n', $this->start_date),
	                        'day' => date('j', $this->start_date),
	     
	                    ),
	                    'before'=>array(
	                        'year' => date('Y', $this->end_date),
	                        'month' => date('n', $this->end_date),
	                        'day' => date('j', $this->end_date),
	      
	                    ),
	                )
	            );

	            $qry = new WP_Query($args);
	            
	            $orders = apply_filters('wcmp_filter_orders_report_vendor', $qry->get_posts());

	            if (!empty($orders)) {
                foreach ($orders as $order_obj) { 
                    $order = new WC_Order($order_obj->ID);
            		$vendors_orders = get_wcmp_vendor_orders(array('order_id' => $order->get_id()));
                	$vendors_orders_amount = get_wcmp_vendor_order_amount(array('order_id' => $order->get_id()),$vendor_id);
                	$current_vendor_orders = wp_list_filter($vendors_orders, array('vendor_id'=>$vendor_id));
                	$gross_sales += $vendors_orders_amount['total'] - $vendors_orders_amount['commission_amount'];
                	$vendor_earning += $vendors_orders_amount['total'];
                	foreach ($current_vendor_orders as $key => $vendor_order) { 
                        $item = new WC_Order_Item_Product($vendor_order->order_item_id);
                        $gross_sales += $item->get_subtotal();
                    }
                
					
					$total_sales[$vendor_id] = $gross_sales;
					$admin_earning[$vendor_id] = $gross_sales - $vendor_earning;
					
					if ( $total_sales[ $vendor_id ] > $max_total_sales )
						$max_total_sales = $total_sales[ $vendor_id ];

	                }
	            }
					
				if( isset( $total_sales[$vendor_id] ) && isset( $admin_earning[$vendor_id] ) ) {
					$vendor_report[$i]['vendor_id'] = $vendor_id;
					$vendor_report[$i]['total_sales'] = $total_sales[ $vendor_id ];
					$vendor_report[$i++]['admin_earning'] = $admin_earning[ $vendor_id ];
					
					$report_bk[$vendor_id]['total_sales'] = $total_sales[ $vendor_id ];
					$report_bk[$vendor_id]['admin_earning'] = $admin_earning[ $vendor_id ];
				}
			}
			
			$i = 0;
			$max_value = 10;
			$report_sort_arr = array();
			if( isset($vendor_report) && isset($report_bk) ) {
				$total_sales_sort = wp_list_pluck( $vendor_report, 'total_sales', 'vendor_id' );
				$admin_earning_sort = wp_list_pluck( $vendor_report, 'admin_earning', 'vendor_id' );
				
				foreach( $total_sales_sort as $key => $value ) {
					$total_sales_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
					$total_sales_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
				}
			
				
				arsort($total_sales_sort);
				foreach( $total_sales_sort as $key => $value ) {
					if( $i++ < $max_value ) {
						$report_sort_arr[$key]['total_sales'] = $report_bk[$key]['total_sales'];
						$report_sort_arr[$key]['admin_earning'] = $report_bk[$key]['admin_earning'];
					}
				}
			}
			
			wp_localize_script('wcmp_report_js', 'wcmp_report_vendor', array('vendor_report' => $vendor_report, 
																			'report_bk' => $report_bk,
																			'total_sales_sort' => $total_sales_sort,
																			'admin_earning_sort' => $admin_earning_sort,
																			'max_total_sales' => $max_total_sales,
																			'start_date' => $start_date,
																			'end_date' => $end_date
																			));
			
			$chart_arr = $html_chart = '';
			if ( count( $report_sort_arr ) > 0 ) {
				foreach ( $report_sort_arr as $vendor_id => $sales_report ) {
					$total_sales_width = ( $sales_report['total_sales'] > 0 ) ? $sales_report['total_sales'] / round($max_total_sales) * 100 : 0;
					$admin_earning_width = ( $sales_report['admin_earning'] > 0 ) ? ( $sales_report['admin_earning'] / round($max_total_sales) ) * 100 : 0;
					
					$user = get_userdata($vendor_id);
					$user_name = $user->data->display_name;
					
					$chart_arr .= '<tr><th><a href="user-edit.php?user_id='.$vendor_id.'">' . $user_name . '</a></th>
					<td width="1%"><span>' . wc_price( $sales_report['total_sales'] ) . '</span><span class="alt">' . wc_price($sales_report['admin_earning']) . '</span></td>
					<td class="bars">
						<span style="width:' . esc_attr( $total_sales_width ) . '%">&nbsp;</span>
						<span class="alt" style="width:' . esc_attr( $admin_earning_width ) . '%">&nbsp;</span>
					</td></tr>';
				}
				
				$html_chart = '
					<h4>' . __( "Sales and Earnings", 'dc-woocommerce-multi-vendor' ) . '</h4>
					<div class="bar_indecator">
						<div class="bar1">&nbsp;</div>
						<span class="">' . __( 'Gross Sales', 'dc-woocommerce-multi-vendor' ) . '</span>
						<div class="bar2">&nbsp;</div>
						<span class="">' . __( 'My Earnings', 'dc-woocommerce-multi-vendor' ) . '</span>
					</div>
					<table class="bar_chart">
						<thead>
							<tr>
								<th>' . __( "Vendors", 'dc-woocommerce-multi-vendor' ) . '</th>
								<th colspan="2">' . __( "Sales Report", 'dc-woocommerce-multi-vendor' ) . '</th>
							</tr>
						</thead>
						<tbody>
							' . $chart_arr . '
						</tbody>
					</table>
				';
			} else {
				$html_chart = '<tr><td colspan="3">' . __( 'Any vendor did not generate any sales in the given period.', 'dc-woocommerce-multi-vendor' ) . '</td></tr>';
			}
		} else {
			$html_chart = '<tr><td colspan="3">' . __( 'Your store has no vendors.', 'dc-woocommerce-multi-vendor' ) . '</td></tr>';
		}
		
		include( $WCMp->plugin_path . '/classes/reports/views/html-wcmp-report-by-vendor.php');
	}

}
