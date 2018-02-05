<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_dashboard.php
 *
 * @author 	WC Marketplace
 * @package 	WCMp/Templates
 * @version     2.3.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMp;
do_action('before_wcmp_vendor_dashboard');
wc_print_notices();
?>
<div class="wcmp_remove_div <?php echo $WCMp->endpoints->get_current_endpoint(); ?>">
    <div class="wcmp_main_page"> 
        <?php do_action('wcmp_vendor_dashboard_navigation', array()); ?>
        <div class="popup-overlay"></div>
        <div class="wcmp_main_holder toside_fix">
            <div class="wcmp_headding1">
                <?php do_action('wcmp_vendor_dashboard_header'); ?>
                <div class="clear"></div>
            </div>
            <div class="wcmp_vendor_dashboard_content">
                <?php do_action('wcmp_vendor_dashboard_content'); ?>
            </div>
        </div>
    </div>
</div>
<?php do_action('after_wcmp_vendor_dashboard'); ?>
