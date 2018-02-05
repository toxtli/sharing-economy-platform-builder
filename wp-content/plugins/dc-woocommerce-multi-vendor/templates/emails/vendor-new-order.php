<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/vendor-new-order.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version   0.0.1
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly 
global $WCMp;
do_action('woocommerce_email_header', $email_heading);
?>

<p><?php printf(__('A new order was received and marked as processing from %s. Their order is as follows:', 'dc-woocommerce-multi-vendor'), $order->get_billing_first_name() . ' ' . $order->get_billing_last_name()); ?></p>

<?php do_action('woocommerce_email_before_order_table', $order, true, false); ?>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
    <thead>
        <tr>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Product', 'dc-woocommerce-multi-vendor'); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Quantity', 'dc-woocommerce-multi-vendor'); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e('Commission', 'dc-woocommerce-multi-vendor'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $vendor = new WCMp_Vendor(absint($vendor_id));
        $vendor_items_dtl = $vendor->vendor_order_item_table($order, $vendor_id);
        echo $vendor_items_dtl;
        ?>
    </tbody>
</table>
<?php
$vendor = new WCMp_Vendor(absint($vendor_id));
$show_cust_order_calulations_field = apply_filters('show_cust_order_calulations_field', true);
if ($WCMp->vendor_caps->vendor_capabilities_settings('show_cust_order_calulations') && $show_cust_order_calulations_field) {
    ?>
    <table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
        <?php
        if ($totals = $vendor->wcmp_vendor_get_order_item_totals($order, $vendor_id)) {
            foreach ($totals as $total_key => $total) {
                ?><tr>
                    <th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee;"><?php echo $total['label']; ?></th>
                    <td style="text-align:left; border: 1px solid #eee;"><?php echo $total['value']; ?></td>
                </tr><?php
            }
        }
        ?>
    </table>
    <?php
}
$show_cust_add_field = apply_filters('show_cust_add_field', true);
$show_customer_detail = $WCMp->vendor_caps->vendor_capabilities_settings('show_cust_add');
if ($show_customer_detail && $show_cust_add_field) {
    ?>
    <h2><?php _e('Customer Details', 'dc-woocommerce-multi-vendor'); ?></h2>
    <?php if ($order->get_billing_email()) { ?>
        <p><strong><?php _e('Customer Name:', 'dc-woocommerce-multi-vendor'); ?></strong> <?php echo $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(); ?></p>
        <p><strong><?php _e('Email:', 'dc-woocommerce-multi-vendor'); ?></strong> <?php echo $order->get_billing_email(); ?></p>
    <?php } ?>
    <?php if ($order->get_billing_phone()) { ?>
        <p><strong><?php _e('Telephone:', 'dc-woocommerce-multi-vendor'); ?></strong> <?php echo $order->get_billing_phone(); ?></p>
    <?php
    }
}
$show_cust_billing_add_field = apply_filters('show_cust_billing_add_field', true);
$show_cust_shipping_add_field = apply_filters('show_cust_shipping_add_field', true);
$show_cust_billing_add = $WCMp->vendor_caps->vendor_capabilities_settings('show_cust_billing_add');
$show_cust_shipping_add = $WCMp->vendor_caps->vendor_capabilities_settings('show_cust_shipping_add');
if ($show_cust_billing_add && $show_cust_billing_add_field) {
    ?>
    <table cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">
        <tr>
            <td valign="top" width="50%">
                <h3><?php _e('Billing Address', 'dc-woocommerce-multi-vendor'); ?></h3>
                <p><?php echo $order->get_formatted_billing_address(); ?></p>
            </td>
        </tr>
    </table>
    <?php }
?>

<?php if ($show_cust_shipping_add && $show_cust_shipping_add_field) { ?> 
    <?php if (( $shipping = $order->get_formatted_shipping_address())) { ?>
        <table cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">
            <tr>
                <td valign="top" width="50%">
                    <h3><?php _e('Shipping Address', 'dc-woocommerce-multi-vendor'); ?></h3>
                    <p><?php echo $shipping; ?></p>
                </td>
            </tr>
        </table>
    <?php
    }
}
?>



<?php do_action('wcmp_email_footer'); ?>