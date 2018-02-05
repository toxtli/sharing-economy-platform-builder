<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/dashboard/vendor-dasboard-pending-shipping-items.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $woocommerce, $WCMp, $wpdb;
$prefix = $wpdb->prefix;
$current_user = wp_get_current_user();
$current_user = apply_filters('wcmp_dashboard_pending_shipping_vendor', $current_user);
$current_user_id = $current_user->ID;
$today_date = @date('Y-m-d');
$curent_week_range = wcmp_rangeWeek($today_date);

if ($today_or_weekly == 'today') {
    $pending_orders_items = $wpdb->get_results("SELECT order_id FROM " . $prefix . "wcmp_vendor_orders WHERE vendor_id = " . $current_user_id . " and `created` like '" . $today_date . "%' and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1 and `shipping_status` != 1 group by order_id order by order_id desc LIMIT " . $start . "," . $to . " ", OBJECT);
} elseif ($today_or_weekly == 'weekly') {
    $pending_orders_items = $wpdb->get_results("SELECT order_id FROM " . $prefix . "wcmp_vendor_orders WHERE vendor_id = " . $current_user_id . " and `created` >= '" . $curent_week_range['start'] . "' and `created` <= '" . $curent_week_range['end'] . "' and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1  and `shipping_status` != 1 group by order_id order by order_id desc LIMIT " . $start . "," . $to . " ", OBJECT);
}

foreach ($pending_orders_items as $pending_orders_item) {
    $vendor = get_wcmp_vendor(get_current_vendor_id());
    try {
        $order = new WC_Order($pending_orders_item->order_id);
        $pending_shipping_products = get_wcmp_vendor_orders(array('vendor_id' => $vendor->id, 'order_id' => $order->get_id(), 'shipping_status' => 0, 'is_trashed' => ''));
        $pending_shipping_amount = get_wcmp_vendor_order_amount(array('vendor_id' => $vendor->id, 'order_id' => $order->get_id(), 'shipping_status' => 0));
        $product_sku = array();
        $product_name = array();
        $product_dimention = array();
        foreach ($pending_shipping_products as $pending_shipping_product) {
            $product = wc_get_product($pending_shipping_product->product_id);
            if ($product) {
                $product_sku[] = $product->get_sku() ? $product->get_sku() : '---';
                $product_name[] = $product->get_title();
                if ($pending_shipping_product->variation_id != 0) {
                    $product = wc_get_product($pending_shipping_product->variation_id);
                }
                $product_dimention[] = array(
                    'width' => $product->get_width() ? $product->get_width() : '..',
                    'height' => $product->get_height() ? $product->get_height() : '..',
                    'length' => $product->get_length() ? $product->get_length() : '..',
                    'weight' => $product->get_weight() ? $product->get_weight() : '..'
                );
            }
        }
        $dimentions = array();
        foreach ($product_dimention as $dimension) {
            $output = implode('/ ', array_map(
                            function ($v, $k) {
                        return sprintf("%s", $v);
                    }, $dimension, array_keys($dimension)
            ));
            $dimentions[] = $output;
        }
        ?>
        <tr>
            <td align="center" ><?php echo implode(' , ', $product_name); ?> </td>
            <td align="center" class="no_display" ><?php echo @date('d/m', strtotime($order->get_date_created())); ?></td>
            <td align="center" class="no_display" >( <?php echo implode(' ) , ( ', $dimentions) ?> )</td>
            <td align="left" ><?php echo $order->get_formatted_shipping_address(); ?></td>
            <td align="center" class="no_display" ><?php echo wc_price($pending_shipping_amount['shipping_amount']); ?></td>
        </tr>

        <?php
    } catch (Exception $ex) {
        
    }
}