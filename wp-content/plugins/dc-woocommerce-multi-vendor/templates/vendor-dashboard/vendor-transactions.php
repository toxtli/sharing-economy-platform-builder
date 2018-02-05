<?php
/**
 * The template for displaying vendor transaction details
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-transaction_detail.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $WCMp;
if (!isset($_GET['from_date'])) {
    $_GET['from_date'] = date('01-m-Y');
}

if (!isset($_GET['to_date'])) {
    $_GET['to_date'] = date('t-m-Y');
}
?>
<div class = "wcmp_mixed_txt some_line"> <span><?php _e(' Showing stats and reports for  :', 'dc-woocommerce-multi-vendor');
?> </span><?php echo $_GET['from_date'] . '-' . $_GET['to_date']; ?>
    <div class="clear"></div>
</div>
<div class="wcmp_form1">
    <?php
    if (!empty($transactions)) {
        ?>
        <div class="transaction_settings">
            <form method="get" id="wcmp_transaction_filter" class="">
                <div class="wcmp_form1 ">
                    <p><?php _e('Select Date Range :', 'dc-woocommerce-multi-vendor'); ?></p>
                    <input id="wcmp_from_date" name="from_date" class="pickdate gap1" placeholder="From" value ="<?php echo $_GET['from_date']; ?>"/>
                    <input id="wcmp_to_date" name="to_date" class="pickdate" placeholder="To" value ="<?php echo $_GET['to_date']; ?>"/>
                    <button type="submit" name="order_export_submit" id="submit"  class="wcmp_black_btn" ><?php _e('Show', 'dc-woocommerce-multi-vendor') ?></button>
                </div>
            </form>
        </div>
        <form method="post" name="export_transaction">
            <div class="wcmp_table_holder">
                <table class="get_wcmp_transactions" width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody> 
                        <tr>
                            <td align="center" valign="top"  width="20">
                                <span class="input-group-addon beautiful">
                                    <input class="select_all_transaction" type="checkbox" >
                                </span>
                            </td>
                            <td><?php _e('Date', 'dc-woocommerce-multi-vendor'); ?></td>
                            <td><?php _e('Transc.ID', 'dc-woocommerce-multi-vendor'); ?></td>
                            <td><?php _e('Commission IDs', 'dc-woocommerce-multi-vendor'); ?></td>
                            <td><?php _e('Fee', 'dc-woocommerce-multi-vendor'); ?></td>
                            <td><?php _e('Net Earnings', 'dc-woocommerce-multi-vendor'); ?></td>
                        </tr>
                        <?php $WCMp->template->get_template('vendor-dashboard/vendor-transactions/vendor-transaction-items.php', array('transactions' => array_slice($transactions, 0, 6))); ?>
                    </tbody>
                </table>
            </div>
            <div class="wcmp_table_loader">

                <input type="hidden" id="export_transaction_start_date" name="from_date" value="<?php echo $_GET['from_date']; ?>" /><input id="export_transaction_end_date" type="hidden" name="to_date" value="<?php echo $_GET['to_date']; ?>" />
                <button type="submit" name="export_transaction" class="wcmp_black_btn"><?php _e('Download CSV', 'dc-woocommerce-multi-vendor'); ?></button>

                <?php if (count($transactions) > 6) { ?><button class="wcmp_black_btn more_transactions" style="float:right" data-shown="6"  data-total="<?php echo count($transactions); ?>"><?php _e('Show More', 'dc-woocommerce-multi-vendor'); ?></button> <?php } ?>
                <div class="clear"></div>
            </div>
        </form>
        <?php
    } else {
        echo '<p style="clear: both;">' . __('Sorry. No transactions are available.', 'dc-woocommerce-multi-vendor') . '</p>';
    }
    ?>
</div>