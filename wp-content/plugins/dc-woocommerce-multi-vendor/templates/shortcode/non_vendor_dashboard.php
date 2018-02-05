<?php
/**
 * The template for displaying vendor dashboard for non-vendors
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/non_vendor_dashboard.php
 *
 * @author 		WC Marketplace
 * @package 	WCMm/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $woocommerce, $WCMp;
$user = wp_get_current_user();
if ($user && !in_array('dc_pending_vendor', $user->roles) && !in_array('administrator', $user->roles)) {
    add_filter('wcmp_vendor_registration_submit', function ($text) {
        return 'Apply to become a vendor';
    });
    echo '<div class="woocommerce">';
    echo do_shortcode('[vendor_registration]');
    echo '</div>';
}

if ($user && in_array('administrator', $user->roles)) {
    ?>
    <div class="vendor_apply">
        <p>
            <?php _e('You have logged in as Administrator. Please log out and then view this page.', 'dc-woocommerce-multi-vendor'); ?>
        </p>
    </div>
    <?php
}
if ($user && in_array('dc_pending_vendor', $user->roles)) {
    ?>
    <div class="vendor_apply">
        <p>
            <?php _e('Congratulations! You have successfully applied as a Vendor. Please wait for further notifications from the admin.', 'dc-woocommerce-multi-vendor'); ?>
        </p>
    </div>
    <?php
}