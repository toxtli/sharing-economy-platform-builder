<?php
/**
 * The template for displaying vendor report
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-report.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
global $WCMp;
?>
<div class="wcmp_mixed_txt some_line"> <span><?php _e('Showing stats and reports for : ', 'dc-woocommerce-multi-vendor'); ?></span> 
    <?php
    if (!isset($_POST['wcmp_stat_start_dt']) || !isset($_POST['wcmp_stat_end_dt'])) {
        echo date('F Y');
    } else {
        echo date('d, F Y', strtotime($_POST['wcmp_stat_start_dt'])) . ' - ' . date('d, F Y', strtotime($_POST['wcmp_stat_end_dt']));
    }
    ?>
    <div class="clear"></div>
</div>
<form name="wcmp_vendor_dashboard_stat_report" method="POST" >
    <div class="wcmp_form1 ">
        <p><?php _e('Select Date Range :', 'dc-woocommerce-multi-vendor'); ?></p>
        <input type="text" name="wcmp_stat_start_dt" value="<?php echo isset($_POST['wcmp_stat_start_dt']) ? $_POST['wcmp_stat_start_dt'] : ''; ?>" class="pickdate gap1 wcmp_stat_start_dt">
        <input type="text" name="wcmp_stat_end_dt" value="<?php echo isset($_POST['wcmp_stat_end_dt']) ? $_POST['wcmp_stat_end_dt'] : ''; ?>" class="pickdate wcmp_stat_end_dt">
        <button name="submit_button" type="submit" value="Show" class="wcmp_black_btn "><?php _e('Show', 'dc-woocommerce-multi-vendor'); ?></button>
    </div>
</form>
<div class="wcmp_ass_holder_box">
    <div class="wcmp_displaybox2">
        <h4><?php _e('Total Sales', 'dc-woocommerce-multi-vendor'); ?></h4>
        <h3><sup><?php echo get_woocommerce_currency_symbol(); ?></sup><?php echo $total_vendor_sales; ?></h3>
    </div>
    <div class="wcmp_displaybox2">
        <h4><?php _e('My Earnings', 'dc-woocommerce-multi-vendor'); ?></h4>
        <h3><sup><?php echo get_woocommerce_currency_symbol(); ?></sup><?php echo $total_vendor_earning; ?></h3>
    </div>
    <div class="clear"></div>
    <p>&nbsp; </p>
    <div class="wcmp_displaybox3"><?php _e(' Total number of', 'dc-woocommerce-multi-vendor'); ?><span><?php _e('Order placed', 'dc-woocommerce-multi-vendor'); ?></span>
        <h3><?php echo $total_order_count; ?></h3>
    </div>
    <div class="wcmp_displaybox3"> <?php _e('Number of ', 'dc-woocommerce-multi-vendor'); ?><span><?php _e('Purchased Products', 'dc-woocommerce-multi-vendor'); ?></span>
        <h3><?php echo $total_purchased_products; ?></h3>
    </div>
    <div class="wcmp_displaybox3"><?php _e(' Number of ', 'dc-woocommerce-multi-vendor'); ?><span> <?php _e(' Coupons used', 'dc-woocommerce-multi-vendor'); ?></span>
        <h3><?php echo $total_coupon_used; ?></h3>
    </div>
    <div class="wcmp_displaybox3"><?php _e(' Total ', 'dc-woocommerce-multi-vendor'); ?><span><?php _e(' Coupon Discount ', 'dc-woocommerce-multi-vendor'); ?></span> <?php _e(' value ', 'dc-woocommerce-multi-vendor'); ?>
        <h3><?php echo get_woocommerce_currency_symbol(); ?><?php echo $total_coupon_discount_value; ?></h3>
    </div>
    <div class="wcmp_displaybox3"><?php _e('  Number of  ', 'dc-woocommerce-multi-vendor'); ?><span><?php _e('  Unique Customers  ', 'dc-woocommerce-multi-vendor'); ?></span>
        <h3><?php echo count($total_customers); ?></h3>
    </div>
    <div class="clear"></div>
</div>
<?php
$is_order_csv_export_button = apply_filters('is_order_csv_export_button', true);
$capabilities_settings = get_wcmp_vendor_settings('wcmp_capabilities_order_settings_name');
if (isset($capabilities_settings['is_order_csv_export'])) {
    if ($capabilities_settings['is_order_csv_export'] == 'Enable' && $is_order_csv_export_button) {
        ?>
        <div class="wcmp_mixed_txt" > <span><?php _e('Download CSV to get complete Stats & Reports', 'dc-woocommerce-multi-vendor'); ?></span>
            <form name="wcmp_vendor_dashboard_stat_export" method="post" >
                <input type="hidden" name="wcmp_stat_export_submit" value="submit" />
                <button type="submit" class="wcmp_black_btn" name="wcmp_stat_export" value="export" style="float:right"><?php _e('Download CSV', 'dc-woocommerce-multi-vendor'); ?></button>
                <div class="clear"></div>
            </form>
        </div>
        <?php
    }
}