<?php
/**
 * The template for displaying vendor orders item band called from vendor_orders.php template
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-withdrawal/vendor-withdrawal-items.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $woocommerce, $WCMp;
$commission_threshold_time = isset($WCMp->vendor_caps->payment_cap['commission_threshold_time']) && !empty($WCMp->vendor_caps->payment_cap['commission_threshold_time']) ? $WCMp->vendor_caps->payment_cap['commission_threshold_time'] : 0;
if (!empty($commissions)) {
    foreach ($commissions as $commission_id => $order_id) {
        $order_obj = new WC_Order($order_id);
        $commission_create_date = get_the_date('U', $commission_id);
        $current_date = date('U');
        $diff = intval(($current_date - $commission_create_date) / (3600 * 24));
        if ($diff < $commission_threshold_time) {
            continue;
        }
        ?>
        <tr>
            <td align="left"  width="20" class="extra_padding">
                <span class="input-group-addon beautiful">
                    <input name="commissions[]" value="<?php echo $commission_id; ?>" class="select_withdrawal" type="checkbox" >
                </span>
            </td>
            <td align="left"  class="extra_padding">#<?php echo $order_id; ?></td>
            <td align="right" valign="middle" class="extra_ending" >
                <?php
                $vendor_share = get_wcmp_vendor_order_amount(array('vendor_id' => $vendor->id, 'order_id' => $order_obj->get_id()));
                if (!isset($vendor_share['total'])) {
                    $vendor_share['total'] = 0;
                }
                echo wc_price($vendor_share['total']);
                ?>
            </td>
        </tr>
        <?php
    }
}
?>
