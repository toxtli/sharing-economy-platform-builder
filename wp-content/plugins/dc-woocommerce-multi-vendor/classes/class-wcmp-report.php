<?php
/**
 * WCMp Report Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
 
class WCMp_Report {

	public function __construct() {
		
		add_action( 'woocommerce_admin_reports', array( $this, 'wcmp_report_tabs' ) );
		if ( is_user_wcmp_vendor(get_current_vendor_id()) ) {
			add_filter( 'woocommerce_reports_charts', array( $this, 'filter_tabs' ), 99 );
			add_filter( 'wcmp_filter_orders_report_overview', array( $this, 'filter_orders_report_overview' ), 99);
		}
	}
	
	/**
	 * Filter orders report for vendor
	 *
	 * @param object $orders
	 */
	public function filter_orders_report_overview($orders) {
		foreach( $orders as $order_key => $order ) {
			$vendor_item = false;
			$order_obj = new WC_Order( $order->ID );
			$items = $order_obj->get_items( 'line_item' );
			foreach( $items as $item_id => $item ) {
				$product_id = wc_get_order_item_meta( $item_id, '_product_id', true );
				$vendor_id = wc_get_order_item_meta( $item_id, '_vendor_id', true );
				$current_user = get_current_vendor_id();
				if( $vendor_id ) {
					if( $vendor_id == $current_user ) {
						$existsids[] = $product_id;
						$vendor_item = true;
					}
				} else {
					//for vendor logged in only
					if ( is_user_wcmp_vendor($current_user) ) {
						$vendor = get_wcmp_vendor($current_user);
						$vendor_products = $vendor->get_products();
						$existsids = array();
						foreach ( $vendor_products as $vendor_product ) {
							$existsids[] = ( $vendor_product->ID );
						}
						if ( in_array( $product_id, $existsids ) ) {
							$vendor_item = true;
						} 
					}
				}
			}
			if(!$vendor_item) unset($orders[$order_key]);
		}
		return $orders;
	}
	
	/**
	 * Show only reports that are useful to a vendor
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function filter_tabs( $tabs ){
		global $woocommerce;
		unset( $tabs[ 'wcmp_vendors' ]['reports']['vendor'] );		
		$return = array(
			'wcmp_vendors' => $tabs[ 'wcmp_vendors' ],
		);
		return $return;
	}

	/** 
	 * WCMp reports tab options
	 */
	function wcmp_report_tabs( $reports ) {
		global $WCMp;		
		$reports['wcmp_vendors'] = array(
			'title'  => __( 'WCMp', 'dc-woocommerce-multi-vendor' ),
			'reports' => array(
				"overview" => array(
					'title'       => __( 'Overview', 'dc-woocommerce-multi-vendor' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'wcmp_get_report' )
				),
				"vendor" => array(
					'title'       => __( 'Vendor', 'dc-woocommerce-multi-vendor' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'wcmp_get_report' )
				),
				"product" => array(
					'title'       => __( 'Product', 'dc-woocommerce-multi-vendor' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'wcmp_get_report' )
				)
			)
		);
		
		return $reports;
	}
	
	/**
	 * Get a report from our reports subfolder
	 */
	public static function wcmp_get_report( $name ) {
		$name  = sanitize_title( str_replace( '_', '-', $name ) );
		$class = 'WCMp_Report_' . ucfirst( str_replace( '-', '_', $name ) );
		include_once( apply_filters( 'wcmp_admin_reports_path', 'reports/class-wcmp-report-' . $name . '.php', $name, $class ) );
		if ( ! class_exists( $class ) )
			return;
		$report = new $class();
		$report->output_report();
	}



	
	
	/**
	* get vendor commission by date
	*
	* @access public
	* @param mixed $vars
	* @return array
	*/
	public function vendor_sales_stat_overview( $vendor, $start_date = false, $end_date = false) {
		global $WCMp;
		$total_sales = 0;
		$total_vendor_earnings = 0;
		$total_order_count = 0;
		$total_purchased_products = 0;
		$total_coupon_used = 0;
		$total_coupon_discount_value = 0;
		$total_earnings = 0;
		$total_customers = array();
		$vendor = get_wcmp_vendor(get_current_vendor_id());
                $vendor = apply_filters( 'wcmp_dashboard_sale_stats_vendor', $vendor);
		for( $date = strtotime($start_date); $date <= strtotime( '+1 day', strtotime($end_date)); $date = strtotime( '+1 day', $date ) ) {
			
			$year = date( 'Y', $date );
			$month = date( 'n', $date );
			$day = date( 'j', $date );
			
			$line_total = $sales = $comm_amount = $vendor_earnings = $earnings = 0;
			
			$args = array(
				'post_type' => 'shop_order',
				'posts_per_page' => -1,
				'post_status' => array( 'wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-cancelled', 'wc-refunded','wc-failed'),
				'meta_query' => array(
					array(
						'key' => '_commissions_processed',
						'value' => 'yes',
						'compare' => '='
					)
				),
				'date_query' => array(
					array(
						'year'  => $year,
						'month' => $month,
						'day'   => $day,
					),
				)
			);
			
			$qry = new WP_Query( $args );
			
			$orders = apply_filters('wcmp_filter_orders_report_overview' , $qry->get_posts(),  $vendor->id);
			if ( !empty($orders) ) {
				foreach($orders as $order_obj) {
					
					$order = new WC_Order( $order_obj->ID );
					$vendors_orders = get_wcmp_vendor_orders(array('order_id' => $order->get_id()));
					if(is_user_wcmp_vendor(get_current_vendor_id())){
                        $vendors_orders_amount = get_wcmp_vendor_order_amount(array('order_id' => $order->get_id()),get_current_vendor_id());

                        $total_sales += $vendors_orders_amount['total'] - $vendors_orders_amount['commission_amount'];
                        $total_vendor_earnings += $vendors_orders_amount['total'];
                        $current_vendor_orders = wp_list_filter($vendors_orders, array('vendor_id'=>get_current_vendor_id()));
                        foreach ($current_vendor_orders as $key => $vendor_order) { 
                            $item = new WC_Order_Item_Product($vendor_order->order_item_id);
                            $total_sales += $item->get_subtotal();
                            $sales += $item->get_subtotal();
                            $total_purchased_products++;
                        }
					}

					//coupons count
					$coupon_used = array();
					$coupons = $order->get_items( 'coupon' );
					foreach ( $coupons as $coupon_item_id => $item ) {
						$coupon = new WC_Coupon( trim( $item['name'] ));
						$coupon_post = get_post($coupon->get_id());
						$author_id = $coupon_post->post_author;
						if($vendor->id == $author_id) {
							$total_coupon_used++ ;
							$total_coupon_discount_value += (float)wc_get_order_item_meta( $coupon_item_id, 'discount_amount', true);
						} 
					}
					++$total_order_count;
					
					//user count
					if( $order->get_customer_id() != 0 && $order->get_customer_id() != 1) array_push($total_customers, $order->get_customer_id());
				}
			}			
		}
		
		return apply_filters('wcmp_vendor_dashboard_report_data', array('total_order_count' => $total_order_count, 'total_vendor_sales' => $total_sales, 'total_vendor_earning' => $total_vendor_earnings, 'total_coupon_discount_value' => $total_coupon_discount_value, 'total_coupon_used' => $total_coupon_used, 'total_customers' => array_unique($total_customers), 'total_purchased_products' => $total_purchased_products), $vendor);
	}
	
}
?>