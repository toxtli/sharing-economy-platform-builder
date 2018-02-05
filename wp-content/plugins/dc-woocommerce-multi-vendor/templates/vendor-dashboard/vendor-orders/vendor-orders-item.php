<?php
/**
 * The template for displaying vendor orders item band called from vendor_orders.php template
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-orders/vendor-orders-item.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $woocommerce, $WCMp;

if (!empty($orders)) {
    foreach ($orders as $order) {
        $order_obj = new WC_Order($order);
        //$order_obj->get_id()
        $mark_ship = $WCMp->vendor_dashboard->is_order_shipped($order, get_wcmp_vendor(get_current_vendor_id()));
        $user_id = get_current_vendor_id();
        $user_id = apply_filters('wcmp_shipping_vendor', $user_id);
        ?>
        <tr>
            <td align="center"  width="20" ><span class="input-group-addon beautiful">
                    <input type="checkbox" class="select_<?php echo $order_status; ?>" name="select_<?php echo $order_status; ?>[<?php echo $order; ?>]" >
                </span></td>
            <td align="center" ><?php echo $order; ?> </td>
            <td align="center" ><?php echo date('d/m', strtotime($order_obj->get_date_created())); ?></td>
            <td class="no_display" align="center" >
                <?php
                //$vendor_share = $vendor->wcmp_get_vendor_part_from_order($order_obj, $vendor->term_id);
                $vendor_share = get_wcmp_vendor_order_amount(array('vendor_id' => $vendor->id, 'order_id' => $order_obj->get_id()));
                if (!isset($vendor_share['total'])) {
                    $vendor_share['total'] = 0;
                }
                echo wc_price($vendor_share['total']);
                ?>
            </td>
            <td class="no_display" align="center" ><?php echo $order_obj->get_status(); ?></td>
            <td align="center" valign="middle" >
                <?php
                $actions = array();
                $is_shipped = get_post_meta($order, 'dc_pv_shipped', true);
                if ($is_shipped) {
                    $mark_ship_title = __('Shipped', 'dc-woocommerce-multi-vendor');
                } else {
                    $mark_ship_title = __('Mark as shipped', 'dc-woocommerce-multi-vendor');
                }
                $actions['view'] = array(
                    'url' => esc_url(wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders'), $order)),
                    'img' => $WCMp->plugin_url . 'assets/images/view.png',
                    'title' => __('View', 'dc-woocommerce-multi-vendor'),
                );

                $actions['wcmp_vendor_csv_download_per_order'] = array(
                    'url' => admin_url('admin-ajax.php?action=wcmp_vendor_csv_download_per_order&order_id=' . $order . '&nonce=' . wp_create_nonce('wcmp_vendor_csv_download_per_order')),
                    'img' => $WCMp->plugin_url . 'assets/images/download.png',
                    'title' => __('Download', 'dc-woocommerce-multi-vendor'),
                );
                if ($vendor->is_shipping_enable()) {
                    $actions['mark_ship'] = array(
                        'url' => '#',
                        'title' => $mark_ship_title,
                    );
                }

                $actions = apply_filters('wcmp_my_account_my_orders_actions', $actions, $order);

                if ($actions) {
                    foreach ($actions as $key => $action) {
                        ?>
                        <?php if ($key == 'view') { ?> 
                            <a title="<?php echo $action['title']; ?>" href="<?php echo $action['url']; ?>"><i><img src="<?php echo $action['img']; ?>" alt=""></i></a>&nbsp; 
                        <?php } elseif ($key == 'mark_ship') { ?>
                            <a id="popup-window" data-popup-target="#inline-<?php echo $order_status; ?>-<?php echo $order; ?>" href="javascript:void(0);" data-id="<?php echo $order; ?>" data-user="<?php echo $user_id; ?>" class="fancybox mark_ship_<?php echo $order; ?>" <?php if ($mark_ship) { ?> title="Shipped" style="pointer-events: none; cursor: default;" <?php } else { ?> title="mark as shipped" <?php } ?> ><i><img src="<?php if (!$mark_ship)
                        echo $WCMp->plugin_url . 'assets/images/roket_deep.png';
                    else
                        echo $WCMp->plugin_url . 'assets/images/roket-green.png';
                    ?>"  alt=""></i></a>                                                                                                                                
                            <input type="hidden" name="shipping_tracking_url" id="shipping_tracking_url_<?php echo $order; ?>" >
                            <input type="hidden" name="shipping_tracking_id" id="shipping_tracking_id_<?php echo $order; ?>" >
                            <div id="inline-<?php echo $order_status; ?>-<?php echo $order; ?>" class="popup">
                                <div class="popup-body"> 
                                    <span class="popup-exit"></span>    
                                    <div class="popup-content">
                                        <div class="shipping_msg_<?php echo $order; ?>" style="color: green;"></div>
                                        <div class="wcmp_headding2"><?php _e('Shipment Tracking Details', 'dc-woocommerce-multi-vendor'); ?></div>
                                        <p><?php _e('Enter Tracking Url', 'dc-woocommerce-multi-vendor'); ?> *</p>
                                        <input  class="long" onkeyup="geturlvalue(this, '<?php echo $order; ?>')" required type="text" name="shipping_tracking_url" placeholder="<?php _e('http://example.com/tracking/', 'dc-woocommerce-multi-vendor'); ?>">
                                        <p><?php _e('Enter Tracking ID', 'dc-woocommerce-multi-vendor'); ?> *</p>
                                        <input  class="long" onkeyup="getidvalue(this, '<?php echo $order; ?>')" required type="text" name="shipping_tracking_id" placeholder="<?php _e('XXXXXXXXXXXXX', 'dc-woocommerce-multi-vendor'); ?>">
                                        <div class="action_div_space"> </div>
                                        <div class="action_div">
                                            <button class="wcmp_orange_btn submit_tracking" name="submit_tracking" data-id="<?php echo $order; ?>" id="submit_tracking"><?php _e('Submit', 'dc-woocommerce-multi-vendor'); ?></button>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <a title="<?php echo $action['title']; ?>" href="<?php echo $action['url']; ?>" data-id="<?php echo $order; ?>" class="<?php echo sanitize_html_class($key); ?>" href="#"><i><img src="<?php echo $action['img']; ?>" alt=""></i></a>&nbsp;
                            <?php
                        }
                    }
                }
                ?>
            </td>
        </tr>
        <?php
    }
}