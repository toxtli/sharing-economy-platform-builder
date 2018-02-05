<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/dashboard/vendor-dashboard-sales-item.php
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
$current_user = apply_filters('wcmp_dashboard_sales_vendor', $current_user);
$current_user_id = $current_user->ID;
$today_date = @date('Y-m-d');
$curent_week_range = wcmp_rangeWeek($today_date);
if ($today_or_weekly == 'today') {
    $sale_orders = $wpdb->get_results("SELECT `order_id` FROM " . $prefix . "wcmp_vendor_orders WHERE vendor_id = " . $current_user_id . " and `created` like '" . $today_date . "%' and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1  group by order_id order by order_id desc LIMIT " . $start . "," . $to . " ", OBJECT);
} elseif ($today_or_weekly == 'weekly') {
    $sale_orders = $wpdb->get_results("SELECT `order_id` FROM " . $prefix . "wcmp_vendor_orders WHERE vendor_id = " . $current_user_id . " and `created` >= '" . $curent_week_range['start'] . "' and `created` <= '" . $curent_week_range['end'] . "' and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1  group by order_id order by order_id desc LIMIT " . $start . "," . $to . " ", OBJECT);
}
foreach ($sale_orders as $sale_order) {
    $sale_results = get_wcmp_vendor_orders(array('vendor_id' => $current_user_id, 'order_id' => $sale_order->order_id, 'is_trashed' => ''));
    $sales_amount = get_wcmp_vendor_order_amount(array('vendor_id' => $current_user_id, 'order_id' => $sale_order->order_id));
    $sku = array();
    $item_total = 0;
    $item_sub_total = 0;
    $vendor_earning = 0;
    foreach ($sale_results as $sale_result) {
        try {
            $product = wc_get_product($sale_result->product_id);
            if ($product) {
                if ($product->get_sku()) {
                    $sku[] = '#' . $product->get_sku();
                } else {
                    $sku[] = '---';
                }
                $item_total += get_metadata('order_item', $sale_result->order_item_id, '_line_total', true);
                $item_sub_total += get_metadata('order_item', $sale_result->order_item_id, '_line_subtotal', true);
            }
        } catch (Exception $ex) {
            
        }
    }
    $discount = $item_sub_total - $item_total;
    $item_total += ($sales_amount['shipping_amount'] + $sales_amount['tax_amount'] + $sales_amount['shipping_tax_amount']);
    $vendor_earnings = $sales_amount['total'];
    ?>
    <tr>
        <td align="center" >#<?php echo $sale_order->order_id; ?> </td>
        <td align="center" ><?php echo implode(', ', $sku) ?> </td>
        <td align="center" class="no_display" ><?php echo wc_price($item_total); ?></td>
        <td align="center" class="no_display" ><?php echo wc_price($discount); ?> </td>
        <td align="center" ><?php echo wc_price($vendor_earnings); ?></td>
    </tr>
    <?php
}
?>
