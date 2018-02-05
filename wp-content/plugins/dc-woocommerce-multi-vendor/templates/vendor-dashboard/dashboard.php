<?php
/*
 * The template for displaying vendor dashboard
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/dashboard.php
 *
 * @author 	WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.4.5
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $woocommerce, $WCMp;
$user = wp_get_current_user();
$vendor = get_wcmp_vendor($user->ID);
if (is_user_wcmp_vendor($user->ID)) :
    //if (isset($WCMp->vendor_caps->wcmp_capability['notify_configure_vendor_store'])) {
        $user_meta_data = get_user_meta($user->ID);
        if (!isset($user_meta_data['_vendor_image']) || !isset($user_meta_data['_vendor_banner']) || !isset($user_meta_data['_vendor_address_1']) || !isset($user_meta_data['_vendor_city']) ||
                !isset($user_meta_data['_vendor_state']) || !isset($user_meta_data['_vendor_country']) || !isset($user_meta_data['_vendor_phone']) || !isset($user_meta_data['_vendor_postcode'])) {
            ?>
            <div class="vendor_non_configuration_msg">
                <?php _e('<h4>You have not configured your store properly missing some required fields!</h4>', 'dc-woocommerce-multi-vendor'); ?>
            </div>

            <?php
        }
    //}
    $notice_data = get_option('wcmp_notices_settings_name');
    $notice_to_be_display = '';

    $dismiss_notices_ids_array = array();
    $dismiss_notices_ids = get_user_meta($user->ID, '_wcmp_vendor_message_deleted', true);
    if (!empty($dismiss_notices_ids)) {
        $dismiss_notices_ids_array = explode(',', $dismiss_notices_ids);
    }
    $args_msg = array(
        'posts_per_page' => 1,
        'offset' => 0,
        'post__not_in' => $dismiss_notices_ids_array,
        'orderby' => 'date',
        'order' => 'DESC',
        'post_type' => 'wcmp_vendor_notice',
        'post_status' => 'publish',
        'suppress_filters' => true
    );
    $msgs_array = get_posts($args_msg);
    if (is_array($msgs_array) && !empty($msgs_array) && count($msgs_array) > 0) {
        $msg = $msgs_array[0];
        ?>
        <div class="ajax_loader_class_msg"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
        <div class="wcmp_admin_massege" id="admin-massege">
            <h2><?php echo __('Admin Message:', 'dc-woocommerce-multi-vendor'); ?> </h2>
            <span> <?php echo $msg->post_title; ?> </span><br/>
            <span class="mormaltext" style="font-weight:normal;"> <?php
                echo $short_content = substr(stripslashes(strip_tags($msg->post_content)), 0, 155);
                if (strlen(stripslashes(strip_tags($msg->post_content))) > 155) {
                    echo '...';
                }
                ?> </span><br/>
            <a href="<?php echo wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_announcements_endpoint', 'vendor', 'general', 'vendor-announcements')) ?>"><button><?php echo __('DETAILS', 'dc-woocommerce-multi-vendor'); ?></button></a>
            <div class="clear"></div>
            <a href="#" id="cross-admin" data-element = "<?php echo $msg->ID; ?>"  class="wcmp_cross wcmp_delate_announcements_dashboard"><i class="fa fa-times-circle"></i></a> 
        </div>		
    <?php } ?>
    <div class="wcmp_tab">
        <ul>
            <li><a href="#today" id="today_click" class="active"><?php echo __('Today', 'dc-woocommerce-multi-vendor'); ?></a></li>
            <li><a href="#theweek" id="theweek_click" ><?php echo __(' This Week', 'dc-woocommerce-multi-vendor'); ?></a></li>
        </ul>
        <div class="wcmp_tabbody"  id="today" >
            <?php
            global $wpdb;
            $prefix = $wpdb->prefix;
            $current_user = wp_get_current_user();
            $current_user = apply_filters('wcmp_dashboard_vendor', $current_user);
            $current_user_id = $current_user->ID;
            $today_date = @date('Y-m-d');

            $sale_results_whole_today = $wpdb->get_results("SELECT * FROM " . $prefix . "wcmp_vendor_orders WHERE vendor_id = " . $current_user_id . " and `created` like '" . $today_date . "%' and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1 ", OBJECT);
            $sale_results_whole_today_row_show = $wpdb->get_results("SELECT * FROM " . $prefix . "wcmp_vendor_orders WHERE vendor_id = " . $current_user_id . " and `created` like '" . $today_date . "%' and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1 group by order_id ", OBJECT);
            $shipping_pending_results_whole_today = $wpdb->get_results("SELECT * FROM " . $prefix . "wcmp_vendor_orders WHERE vendor_id = " . $current_user_id . " and `created` like '" . $today_date . "%' and `commission_id` != 0 and `commission_id` != '' and `shipping_status` != 1 and `is_trashed` != 1 ", OBJECT);
            $number_of_pending_shipping_whole_today = count($shipping_pending_results_whole_today);
            if ($number_of_pending_shipping_whole_today <= 6) {
                $number_of_pending_shipping_show_today = $number_of_pending_shipping_whole_today;
            } else {
                $number_of_pending_shipping_show_today = 6;
            }
            $total_page_pending_shipping_today = ceil($number_of_pending_shipping_whole_today / 6);
            $whole_row_today = count($sale_results_whole_today_row_show);
            if ($whole_row_today > 0 && $whole_row_today <= 6) {
                $displayed_row = $whole_row_today;
            } else if ($whole_row_today > 6) {
                $displayed_row = 6;
            } else {
                $displayed_row = 0;
            }
            $total_page_sale_today = ceil($no_of_pege_today_sale = $whole_row_today / 6);

            $item_total = 0;
            $comission_total_arr = array();
            $total_comission = 0;
            $shipping_total = 0;
            $tax_total = 0;
            $net_balance_today = 0;
            $vendor_comission = 0;
            foreach ($sale_results_whole_today as $sale_row) {
                $order_item_id = $sale_row->order_item_id;
                $item_total += get_metadata('order_item', $sale_row->order_item_id, '_line_total', true);
                if (!in_array($sale_row->commission_id, $comission_total_arr)) {
                    $comission_total_arr[] = $sale_row->commission_id;
                }
            }
            foreach ($comission_total_arr as $comission_id){
                $amount = get_wcmp_vendor_order_amount(array('commission_id' => $comission_id));
                $total_comission += $amount['total'];
                $shipping_total += $amount['shipping_amount'];
                $tax_total += $amount['tax_amount'] + $amount['shipping_tax_amount'];
                $paid_status = get_metadata('post', $comission_id, '_paid_status', true);
                if ($paid_status == "unpaid") {
                    $net_balance_today += $amount['total'];
                }
            }
            $item_total += ($shipping_total + $tax_total);
            ?>
            <input type = "hidden" name="today_sale_current_page" id="today_sale_current_page" value="1">
            <input type = "hidden" name="today_sale_next_page" id="today_sale_next_page" value="<?php
            if ($total_page_sale_today > 1) {
                echo 2;
            } else {
                echo 1;
            }
            ?>">
            <input type = "hidden" name="today_sale_total_page" id="today_sale_total_page" value="<?php echo $total_page_sale_today; ?>">
            <div class="wcmp_dashboard_display_box">
                <h4><?php echo __('Todays Sales', 'dc-woocommerce-multi-vendor'); ?></h4>
                <h3><sup><?php list($before, $after) = explode(".", number_format((float)$item_total,2)); echo get_woocommerce_currency_symbol(); ?></sup><?php echo $before; ?><span>.<?php echo $after; ?></span></h3>
            </div>
            <div class="wcmp_dashboard_display_box">
                <h4><?php echo __('Todays Earnings', 'dc-woocommerce-multi-vendor'); ?></h4>
                <h3><sup><?php list($before, $after) = explode(".", number_format((float)$total_comission,2)); echo get_woocommerce_currency_symbol(); ?></sup><?php echo $before; ?><span>.<?php echo $after; ?></span></h3>
            </div>
            <div class="wcmp_dashboard_display_box">
                <h4><?php echo __('Net Balance', 'dc-woocommerce-multi-vendor'); ?></h4>
                <h3><sup><?php list($before, $after) = explode(".", number_format((float)$net_balance_today,2)); echo get_woocommerce_currency_symbol(); ?></sup><?php echo $before; ?><span>.<?php echo $after; ?></span></h3>
            </div>
            <div class="clear"></div>
            <h3 class="wcmp_black_headding"><?php echo __('Sales', 'dc-woocommerce-multi-vendor'); ?></h3>
            <div class="wcmp_table_holder">
                <table id="wcmp_sale_report_table_today" width="100%" border="0" cellspacing="0" cellpadding="0">
                    <?php
                    //show sales items
                    $WCMp->template->get_template('vendor-dashboard/dashboard/vendor-dashboard-sales-item-header.php');
                    //show sales items
                    $WCMp->template->get_template('vendor-dashboard/dashboard/vendor-dashboard-sales-item.php', array('vendor' => $vendor, 'today_or_weekly' => 'today', 'start' => 0, 'to' => 6));
                    ?>
                </table>
            </div>



            <div class="wcmp_table_loader">
                <div class="ajax_loader_class_msg"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
                <?php echo __('Showing Results', 'dc-woocommerce-multi-vendor'); ?><span> <span class="wcmp_front_count_first_num_today"><?php echo $displayed_row; ?></span> <?php
                echo __('  out of  ', 'dc-woocommerce-multi-vendor');
                echo $whole_row_today;
                ?></span>
                <?php if ($whole_row_today > 6) { ?><button class="wcmp_black_btn wcmp_frontend_sale_show_more_button" element-data="sale_today_more" style="float:right"><?php echo __('Show More', 'dc-woocommerce-multi-vendor'); ?></button><?php } ?>
                <div class="clear"></div>
            </div>
            
            <?php if($vendor->is_shipping_enable()): ?>
            <h3 class="wcmp_black_headding"><?php echo __('Pending Shipping', 'dc-woocommerce-multi-vendor'); ?></h3>
            <div class="wcmp_table_holder">
                <table id="wcmp_pending_shipping_report_table_today" width="100%" border="0" cellspacing="0" cellpadding="0">
                    <?php
                    //show pending shipping items
                    $WCMp->template->get_template('vendor-dashboard/dashboard/vendor-dasboard-pending-shipping-items-header.php');
                    $WCMp->template->get_template('vendor-dashboard/dashboard/vendor-dasboard-pending-shipping-items.php', array('vendor' => $vendor, 'today_or_weekly' => 'today', 'start' => 0, 'to' => 6));
                    ?>
                </table>
            </div>
            <input type = "hidden" name="today_pending_shipping_current_page" id="today_pending_shipping_current_page" value="1">
            <input type = "hidden" name="today_pending_shipping_next_page" id="today_pending_shipping_next_page" value="<?php
            if ($total_page_pending_shipping_today > 1) {
                echo 2;
            } else {
                echo 1;
            }
            ?>">
            <input type = "hidden" name="today_pending_shipping_total_page" id="today_pending_shipping_total_page" value="<?php echo $total_page_pending_shipping_today; ?>">
            <div class="wcmp_table_loader">
                <div class="ajax_loader_class_msg"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
                <?php echo __('Showing Results', 'dc-woocommerce-multi-vendor'); ?> <span> <span class="wcmp_front_count_first_num_today_ps"><?php echo $number_of_pending_shipping_show_today; ?></span> <?php echo __(' out of ', 'dc-woocommerce-multi-vendor'); ?> <?php echo $number_of_pending_shipping_whole_today; ?></span>
                <?php if ($number_of_pending_shipping_whole_today > 6) { ?><button class="wcmp_black_btn wcmp_frontend_pending_shipping_show_more_button" element-data="pending_shipping_today_more" style="float:right"><?php echo __('Show More', 'dc-woocommerce-multi-vendor'); ?></button><?php } ?>
                <div class="clear"></div>
            </div>
            <?php endif; ?>
        </div>

        <div class="wcmp_tabbody" id="theweek" >
            <?php
            $curent_week_range = wcmp_rangeWeek($today_date);
            $sale_results_whole_week = $wpdb->get_results("SELECT * FROM " . $prefix . "wcmp_vendor_orders WHERE vendor_id = " . $current_user_id . " and `created` >= '" . $curent_week_range['start'] . "' and  `created` <= '" . $curent_week_range['end'] . "' and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1 ", OBJECT);
            $sale_results_whole_week_row_show = $wpdb->get_results("SELECT * FROM " . $prefix . "wcmp_vendor_orders WHERE vendor_id = " . $current_user_id . " and `created` >= '" . $curent_week_range['start'] . "' and `created` <= '" . $curent_week_range['end'] . "' and  `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1 group by order_id ", OBJECT);
            $pending_shipping_results_whole_week_row = $wpdb->get_results("SELECT * FROM " . $prefix . "wcmp_vendor_orders WHERE vendor_id = " . $current_user_id . " and `created` >= '" . $curent_week_range['start'] . "' and `created` <= '" . $curent_week_range['end'] . "' and  `commission_id` != 0 and `commission_id` != '' and `shipping_status` != 1 and `is_trashed` != 1 ", OBJECT);

            $week_pending_shipping_whole = count($pending_shipping_results_whole_week_row);
            if ($week_pending_shipping_whole <= 6) {
                $week_pending_shipping_show = $week_pending_shipping_whole;
            } else {
                $week_pending_shipping_show = 6;
            }

            $total_page_pending_shipping_week = ceil($week_pending_shipping_whole / 6);

            $whole_row_week = count($sale_results_whole_week_row_show);
            if ($whole_row_week > 0 && $whole_row_week <= 6) {
                $displayed_row_week = $whole_row_week;
            } else if ($whole_row_week > 6) {
                $displayed_row_week = 6;
            } else {
                $displayed_row_week = 0;
            }
            $total_page_sale_week = ceil($no_of_pege_week_sale = $whole_row_week / 6);

            $item_total_week = 0;
            $comission_total_arr_week = array();
            $total_comission_week = 0;
            $shipping_total_week = 0;
            $tax_total_week = 0;
            $net_balance_week = 0;
            $vendor_comission_week = 0;
            foreach ($sale_results_whole_week as $sale_row_week) {
                $order_item_id_week = $sale_row_week->order_item_id;
                $item_total_week += get_metadata('order_item', $sale_row_week->order_item_id, '_line_total', true);
                if (!in_array($sale_row_week->commission_id, $comission_total_arr_week)) {
                    $comission_total_arr_week[] = $sale_row_week->commission_id;
                }
            }
            
            foreach ($comission_total_arr_week as $comission_id_week) {
                $amount = get_wcmp_vendor_order_amount(array('commission_id' => $comission_id_week));
                $total_comission_week += $amount['total'];
                $shipping_total_week += $amount['shipping_amount'];
                $tax_total_week += $amount['tax_amount'] + $amount['shipping_tax_amount'];
                $paid_status_week = get_metadata('post', $comission_id_week, '_paid_status', true);
                if ($paid_status_week == "unpaid") {
                    $net_balance_week += $amount['total'];
                }
            }
            $item_total_week += ($shipping_total_week + $tax_total_week);
            ?>
            <input type = "hidden" name="week_sale_current_page" id="week_sale_current_page" value="1">
            <input type = "hidden" name="week_sale_next_page" id="week_sale_next_page" value="<?php
            if ($total_page_sale_week > 1) {
                echo '2';
            }
            ?>">
            <input type = "hidden" name="week_sale_total_page" id="week_sale_total_page" value="<?php echo $total_page_sale_week; ?>">
            <div class="wcmp_dashboard_display_box">
                <h4><?php echo __('Weekly Sales', 'dc-woocommerce-multi-vendor'); ?></h4>
                <h3><sup><?php list($before, $after) = explode(".", number_format((float)$item_total_week,2)); echo get_woocommerce_currency_symbol(); ?></sup><?php echo $before; ?><span>.<?php echo $after; ?></span></h3>
            </div>
            <div class="wcmp_dashboard_display_box">
                <h4><?php echo __('Weekly Earnings', 'dc-woocommerce-multi-vendor'); ?></h4>
                <h3><sup><?php list($before, $after) = explode(".", number_format((float)$total_comission_week,2)); echo get_woocommerce_currency_symbol(); ?></sup><?php echo $before; ?><span>.<?php echo $after; ?></span></h3>
            </div>
            <div class="wcmp_dashboard_display_box">
                <h4><?php echo __('Weekly Balance', 'dc-woocommerce-multi-vendor'); ?></h4>
                <h3><sup><?php list($before, $after) = explode(".", number_format((float)$net_balance_week,2)); echo get_woocommerce_currency_symbol(); ?></sup><?php echo $before; ?><span>.<?php echo $after; ?></span></h3>
            </div>
            <div class="clear"></div>
            <h3 class="wcmp_black_headding"><?php echo __('Sales', 'dc-woocommerce-multi-vendor'); ?></h3>
            <div class="wcmp_table_holder">
                <table id="wcmp_sale_report_table_week" width="100%" border="0" cellspacing="0" cellpadding="0">
                    <?php
                    //show sales items
                    $WCMp->template->get_template('vendor-dashboard/dashboard/vendor-dashboard-sales-item-header.php');
                    ?>
                    <?php
                    //show sales items
                    $WCMp->template->get_template('vendor-dashboard/dashboard/vendor-dashboard-sales-item.php', array('vendor' => $vendor, 'today_or_weekly' => 'weekly', 'start' => 0, 'to' => 6));
                    ?>
                </table>
            </div>
            <div class="wcmp_table_loader">
                <div class="ajax_loader_class_msg"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
                <?php echo __('Showing Results', 'dc-woocommerce-multi-vendor'); ?><span> <span class="wcmp_front_count_first_num_week"><?php echo $displayed_row_week; ?></span> <?php
                echo __('  out of  ', 'dc-woocommerce-multi-vendor');
                echo $whole_row_week;
                ?></span>
                <?php if ($whole_row_week > 6) { ?><button class="wcmp_black_btn wcmp_frontend_sale_show_more_button" element-data="sale_weekly_more" style="float:right"><?php echo __('Show More', 'dc-woocommerce-multi-vendor'); ?></button><?php } ?>
                <div class="clear"></div>
            </div>
            <?php if($vendor->is_shipping_enable()): ?>
            <h3 class="wcmp_black_headding"><?php echo __('Pending Shipping', 'dc-woocommerce-multi-vendor'); ?></h3>
            <div class="wcmp_table_holder">
                <table id="wcmp_pending_shipping_report_table_week" width="100%" border="0" cellspacing="0" cellpadding="0">
                    <?php
                    $WCMp->template->get_template('vendor-dashboard/dashboard/vendor-dasboard-pending-shipping-items-header.php');
                    //show pending shipping items
                    $WCMp->template->get_template('vendor-dashboard/dashboard/vendor-dasboard-pending-shipping-items.php', array('vendor' => $vendor, 'today_or_weekly' => 'weekly', 'start' => 0, 'to' => 6));
                    ?>
                </table>
            </div>
            <input type = "hidden" name="week_pending_shipping_current_page" id="week_pending_shipping_current_page" value="1">
            <input type = "hidden" name="week_pending_shipping_next_page" id="week_pending_shipping_next_page" value="<?php
            if ($total_page_pending_shipping_week > 1) {
                echo '2';
            }
            ?>">
            <input type = "hidden" name="week_pending_shipping_total_page" id="week_pending_shipping_total_page" value="<?php echo $total_page_pending_shipping_week; ?>">
            <div class="wcmp_table_loader">
                <div class="ajax_loader_class_msg"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
                <?php echo __('Showing Results', 'dc-woocommerce-multi-vendor'); ?> <span> <span class="wcmp_front_count_first_num_week_ps"><?php echo $week_pending_shipping_show; ?></span> <?php echo __(' out of ', 'dc-woocommerce-multi-vendor'); ?> <?php echo $week_pending_shipping_whole; ?></span>
                <?php if ($week_pending_shipping_whole > 6) { ?><button class="wcmp_black_btn wcmp_frontend_pending_shipping_show_more_button" element-data="pending_shipping_weekly_more" style="float:right"><?php echo __('Show More', 'dc-woocommerce-multi-vendor'); ?></button><?php } ?>
                <div class="clear"></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
