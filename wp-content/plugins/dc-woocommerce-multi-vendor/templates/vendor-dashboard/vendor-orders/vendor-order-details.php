<?php
/**
 * The template for displaying vendor order detail and called from vendor_order_item.php template
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-orders/vendor-order-details.php
 *
 * @author 	WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly    
    exit;
} 
global $woocommerce, $WCMp;
$user = wp_get_current_user();
$vendor = apply_filters('wcmp_dashboard_order_details_vendor', get_wcmp_vendor($user->ID));
if ($vendor && $order_id) {
    $vendor_items = $vendor->get_vendor_items_from_order($order_id, $vendor->term_id);
    $order = new WC_Order($order_id);
    if ($order && sizeof($order->get_items()) > 0) {
        ?>
        <!--        <h2><?php _e('Order Details', 'dc-woocommerce-multi-vendor'); ?></h2>-->
        <table class="customer_order_dtl"> 
            <tbody>
            <th><label for="product_name"><?php _e('Product Title', 'dc-woocommerce-multi-vendor') ?></label></th>
            <th><label for="product_qty"><?php _e('Product Quantity', 'dc-woocommerce-multi-vendor') ?></label></th>
            <th><label for="product_total"><?php _e('Line Subtotal', 'dc-woocommerce-multi-vendor') ?></label></th>
            <?php if (in_array($order->get_status(), array('processing', 'completed')) && ( $purchase_note = get_post_meta($order_id, '_purchase_note', true) )) { ?>
                <th><label for="product_note"><?php _e('Purchase Note', 'dc-woocommerce-multi-vendor') ?></label></th>
            <?php } ?>
            <?php
            if (sizeof($order->get_items()) > 0) {
                foreach ($vendor_items as $item) {
                    $_product = apply_filters('dc_woocommerce_order_item_product', $order->get_product_from_item($item), $item);
                    ?>
                    <tr class="">

                        <td class="product-name">
                            <?php
                            if ($_product && !$_product->is_visible())
                                echo apply_filters('wcmp_order_item_name', $item['name'], $item);
                            else
                                echo apply_filters('wcmp_order_item_name', sprintf('<a href="%s">%s</a>', get_permalink($item['product_id']), $item['name']), $item);
                            wc_display_item_meta($item);
                            ?>
                        </td>
                        <td>	
                            <?php
                            echo $item['qty'];
                            ?>
                        </td>
                        <td>
                            <?php echo $order->get_formatted_line_subtotal($item); ?>
                        </td>
                        <?php
                        if (in_array($order->get_status(), array('processing', 'completed')) && ( $purchase_note = get_post_meta($_product->get_id(), '_purchase_note', true) )) {
                            ?>
                            <td colspan="3"><?php echo apply_filters('the_content', $purchase_note); ?></td>
                            <?php
                        }
                    }
                }
                ?>
            </tr>
        </tbody>
        </table>
        <?php
        $coupons = $order->get_used_coupons();
        if (!empty($coupons)) {
            ?>
            <div class="wcmp_headding2"><?php _e('Coupon Used :', 'dc-woocommerce-multi-vendor'); ?></div>
            <table class="coupon_used"> 
                <tbody>
                    <tr>
                        <?php
                        $coupon_used = false;
                        foreach ($coupons as $coupon_code) {
                            $coupon = new WC_Coupon($coupon_code);
                            $coupon_post = get_post($coupon->get_id());
                            $author_id = $coupon_post->post_author;
                            if (get_current_vendor_id() == $author_id) {
                                $coupon_used = true;
                                echo '<td>"' . $coupon_code . '"</td>';
                            }
                        }
                        if (!$coupon_used)
                            echo '<td>' . __("Sorry No Coupons of yours is used.", 'dc-woocommerce-multi-vendor') . '</td>'
                            ?>
                    </tr>
                </tbody>
            </table>
            <?php
        }
        ?>
        <?php $customer_note = $order->get_customer_note();
        ?>
        <div class="wcmp_headding2"><?php _e('Customer Note', 'dc-woocommerce-multi-vendor'); ?></div>
        <p class="wcmp_headding3">
            <?php echo $customer_note ? $customer_note : __('No customer note.', 'dc-woocommerce-multi-vendor'); ?>
        </p>

        <?php
        $is_vendor_view_comment_field = apply_filters('is_vendor_view_comment_field', true);
        if ($WCMp->vendor_caps->vendor_capabilities_settings('is_vendor_view_comment') && $is_vendor_view_comment_field) {
            $vendor_comments = $order->get_customer_order_notes();
            if ($vendor_comments) {
                ?>
                <div class="wcmp_headding2"><?php _e('Comments', 'dc-woocommerce-multi-vendor'); ?></div>
                <div class="wcmp_headding3">
                    <?php
                    foreach ($vendor_comments as $comment) {
                        $comment_vendor = get_comment_meta($comment->comment_ID,'_vendor_id',true);
                        if($comment_vendor && $comment_vendor != $vendor->id){
                            continue;
                        }
                        $last_added = human_time_diff(strtotime($comment->comment_date_gmt), current_time('timestamp', 1));
                        ?>
                        <p>
                            <?php printf(__('Added %s ago', 'dc-woocommerce-multi-vendor'), $last_added); ?>
                            </br>
                            <?php echo $comment->comment_content; ?>
                        </p>
                    <?php } ?>
                </div>
                <?php
            }
        }
        ?>

        <?php
        $is_vendor_submit_comment_field = apply_filters('is_vendor_submit_comment_field', true);
        if ($WCMp->vendor_caps->vendor_capabilities_settings('is_vendor_submit_comment') && $is_vendor_submit_comment_field) {
            ?>
            <div class="wcmp_headding2"><?php _e('Add Comment', 'dc-woocommerce-multi-vendor'); ?></div>
            <form method="post" name="add_comment" id="add-comment_<?php echo $order_id; ?>">
                <?php wp_nonce_field('dc-add-comment'); ?>
                <textarea name="comment_text" style="width:97%; margin-bottom: 10px;"></textarea>
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                <input class="btn btn-large btn-block" type="submit" name="wcmp_submit_comment" value="<?php _e('Add comment', 'dc-woocommerce-multi-vendor'); ?>">
            </form>
        <?php } ?>

        <?php if ($WCMp->vendor_caps->vendor_capabilities_settings('show_customer_dtl') && !$is_not_show_customer_dtl_field = apply_filters('is_not_show_customer_dtl_field', false)) { ?>
           
                <div class="wcmp_headding2"><?php _e('Customer Details', 'dc-woocommerce-multi-vendor'); ?></div>
            <div class="wcmp_headding3">
                <dl class="customer_details">
                    <?php
                    $name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
                    echo '<dt>' . __('Name:', 'dc-woocommerce-multi-vendor') . '</dt><dd>' . $name . '</dd>';
                    if ($order->get_billing_email())
                        echo '<dt>' . __('Email:', 'dc-woocommerce-multi-vendor') . '</dt><dd>' . $order->get_billing_email() . '</dd>';
                    if ($order->get_billing_phone())
                        echo '<dt>' . __('Telephone:', 'dc-woocommerce-multi-vendor') . '</dt><dd>' . $order->get_billing_phone() . '</dd>';

                    // Additional customer details hook
                    do_action('wcmp_order_details_after_customer_details', $order);
                    ?>
                </dl>
            </div>
        <?php } ?>


        <?php if ($WCMp->vendor_caps->vendor_capabilities_settings('show_customer_billing') && !$is_not_show_customer_billing_field = apply_filters('is_not_show_customer_billing_field', false)) { ?>
            <div class="col-1">
                    <div class="wcmp_headding2"><?php _e('Billing Address', 'dc-woocommerce-multi-vendor'); ?></div>
                <div class="wcmp_headding3">
                    <address><p>
                            <?php
                            if (!$order->get_formatted_billing_address())
                                _e('N/A', 'dc-woocommerce-multi-vendor');
                            else
                                echo $order->get_formatted_billing_address();
                            ?>
                        </p></address>
                </div>
            </div><!-- /.col-1 -->
            <?php
        }
        if ($WCMp->vendor_caps->vendor_capabilities_settings('show_customer_shipping') && !$is_not_show_customer_shipping_field = apply_filters('is_not_show_customer_shipping_field', false)) {
            ?>
            <?php if (!wc_ship_to_billing_address_only() && get_option('woocommerce_calc_shipping') !== 'no') { ?>
                <div class="col-2">
                        <div class="wcmp_headding2"><?php _e('Shipping Address', 'dc-woocommerce-multi-vendor'); ?></div>
                    <div class="wcmp_headding3">
                        <address><p>
                                <?php
                                if (!$order->get_formatted_shipping_address())
                                    _e('N/A', 'dc-woocommerce-multi-vendor');
                                else
                                    echo $order->get_formatted_shipping_address();
                                ?>
                            </p></address>
                    </div>

                </div><!-- /.col-2 -->
                <?php
            }
        }
    } else {
        echo __('<div class="wcmp_headding3">No such order found</div>', 'dc-woocommerce-multi-vendor');
    }
}
?>