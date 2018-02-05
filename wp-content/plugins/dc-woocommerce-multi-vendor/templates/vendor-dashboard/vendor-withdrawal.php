<?php
/**
 * The template for displaying vendor orders
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-withdrawal.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $woocommerce, $WCMp;
$get_vendor_thresold = 0;
if (isset($WCMp->vendor_caps->payment_cap['commission_threshold']) && $WCMp->vendor_caps->payment_cap['commission_threshold']) {
    $get_vendor_thresold = $WCMp->vendor_caps->payment_cap['commission_threshold'];
}
?>
<div class="wcmp_orange_mix_txt"><?php _e('Your Threshold value for withdrawals is :', 'dc-woocommerce-multi-vendor'); ?><span><?php echo wc_price($get_vendor_thresold); ?></span> 
    <div class="clear"></div>
</div>
<div class="wcmp_headding3">
    <h3><?php _e('Completed Orders', 'dc-woocommerce-multi-vendor'); ?></h3>
    <p><?php _e('Showing Results', 'dc-woocommerce-multi-vendor'); ?><span> 
            <?php if ($total_orders > 6) { ?>
                <span>
                    <span class="wcmp_withdrawal_now_showing"> 
    <?php echo '6'; ?>
                    </span> 
                        <?php _e('out of ', 'dc-woocommerce-multi-vendor'); ?><?php echo $total_orders; ?>
                </span> 
                <?php
                } else {
                    echo '<span>' . $total_orders . '</span>';
                }
                ?>
    </p>
</div>
<form name="get_paid_form" method="post">
    <div class="wcmp_table_holder">
        <table class="get_paid_orders" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="left" width="20" ><span class="input-group-addon beautiful">
                        <input class="select_all_withdrawal" type="checkbox" >
                    </span></td>
                <td  align="left"><?php _e('Order ID', 'dc-woocommerce-multi-vendor'); ?></td>
                <td align="right"><?php _e('My Earnings', 'dc-woocommerce-multi-vendor'); ?></td>
            </tr>
            <?php $WCMp->template->get_template('vendor-dashboard/vendor-withdrawal/vendor-withdrawal-items.php', array('vendor' => $vendor, 'commissions' => $commissions)); ?>
        </table>
    </div>
    <div class="wcmp_table_loader">
        <input type="hidden" id="total_orders_count" value = "<?php echo $total_orders; ?>" />
        <?php if ($total_orders != 0) { ?>
            <?php
            if (isset($WCMp->vendor_caps->payment_cap['wcmp_disbursal_mode_vendor']) && $WCMp->vendor_caps->payment_cap['wcmp_disbursal_mode_vendor'] == 'Enable') {
                $total_vendor_due = $vendor->wcmp_vendor_get_total_amount_due();
                if ($total_vendor_due > $get_vendor_thresold) {
                    ?>
                    <button name="vendor_get_paid" type="submit" class="wcmp_orange_btn small"><?php _e('Request Withdrawals', 'dc-woocommerce-multi-vendor'); ?></button>
                    <?php
                }
            }
            ?>
            <?php if ($total_orders > 6) { ?><button  data-id="6" class="wcmp_black_btn more_orders" style="float:right"><?php _e('Show More', 'dc-woocommerce-multi-vendor'); ?></button> <?php }
}
        ?>
        <div class="clear"></div>
    </div>
</form>
<?php
$vendor_payment_mode = get_user_meta($vendor->id, '_vendor_payment_mode', true);
if ($vendor_payment_mode == 'paypal_masspay' && wp_next_scheduled('masspay_cron_start')) {
    ?>
    <div class="wcmp_admin_massege">
        <div class="wcmp_mixed_msg"><?php _e('Your next scheduled payment date is on:', 'dc-woocommerce-multi-vendor'); ?>	<span><?php echo date('d/m/Y g:i:s A', wp_next_scheduled('masspay_cron_start')); ?></span> </div>
    </div>
<?php }
?> 