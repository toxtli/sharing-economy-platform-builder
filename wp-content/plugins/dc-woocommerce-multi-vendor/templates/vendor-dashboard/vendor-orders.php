<?php
/**
 * The template for displaying vendor orders
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-orders.php
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
?>
<div class="wcmp_mixed_txt some_line"> <span><?php _e(' Showing stats and reports for  :', 'dc-woocommerce-multi-vendor'); ?></span>
    <?php
    if (!empty($_POST['wcmp_start_date_order']) || !empty($_POST['wcmp_end_date_order'])) {
        echo date('d, F Y', strtotime($_POST['wcmp_start_date_order']));
        if (!empty($_POST['wcmp_end_date_order'])) {
            echo ' - ' . date('d, F Y', strtotime($_POST['wcmp_end_date_order']));
        } else
            echo ' - ' . date('t, F Y');
    } else {
        echo date('F Y');
    }
    ?>
    <div class="clear"></div>
</div>
<div class="wcmp_form1 ">
    <p><?php _e('Select Date Range :', 'dc-woocommerce-multi-vendor'); ?></p>
    <form name="wcmp_vendor_dashboard_orders" method="POST" >
        <input type="text" name="wcmp_start_date_order" class="pickdate gap1 wcmp_start_date_order" placeholder="<?php _e('from', 'dc-woocommerce-multi-vendor'); ?>" value="<?php echo isset($_GET['wcmp_start_date_order']) ? $_GET['wcmp_start_date_order'] : ''; ?>" />
        <input type="text" name="wcmp_end_date_order" class="pickdate wcmp_end_date_order" placeholder="<?php _e('to', 'dc-woocommerce-multi-vendor'); ?>" value="<?php echo isset($_GET['wcmp_end_date_order']) ? $_GET['wcmp_end_date_order'] : ''; ?>" />
        <button class="wcmp_black_btn" type="submit" name="wcmp_order_submit"><?php _e('Show', 'dc-woocommerce-multi-vendor'); ?></button>
    </form>
</div>
<div class="wcmp_tab">
    <ul>
        <li><a href="#all" id="all_click"><?php _e('All', 'dc-woocommerce-multi-vendor'); ?></a></li>
        <li><a href="#processing" id="processing_click" ><?php _e('Processing', 'dc-woocommerce-multi-vendor'); ?></a></li>
        <li><a href="#completed" id="complited_click" ><?php _e('Completed', 'dc-woocommerce-multi-vendor'); ?></a></li>
    </ul>
    <div class="wcmp_tabbody"  id="all" >
        <?php if (!empty($customer_orders['all'])) { ?>
            <form name="wcmp_vendor_dashboard_all_stat_export" method="post" >
                <div class="wcmp_table_loader"> <?php _e('Showing Results', 'dc-woocommerce-multi-vendor'); ?><span> 
                        <?php if (count($customer_orders['all']) > 6) { ?>
                            <span>
                                <span class="wcmp_all_now_showing"> 
                                    <?php echo '6'; ?>
                                </span> 
                                out of <?php echo count($customer_orders['all']); ?>
                            </span> 
                            <?php
                        } else {
                            echo '<span>' . count($customer_orders['all']) . '</span>';
                        }
                        ?>
                </div>
                <div class="wcmp_table_holder">
                    <table width="100%" border="0" cellspacing="0" class="wcmp_order_all_table" cellpadding="0">
                        <tr>
                            <td align="center"  valign="top"  width="20"><span class="input-group-addon beautiful">
                                    <input type="checkbox"  class="select_all_all" >
                                </span></td>
                            <td align="center" valign="top"  ><?php _e('ID', 'dc-woocommerce-multi-vendor'); ?></td>
                            <td  align="center" valign="top" ><?php _e('Date', 'dc-woocommerce-multi-vendor'); ?><br>
                                <sub><?php _e('dd/mm', 'dc-woocommerce-multi-vendor'); ?></sub></td>
                            <td align="center" class="no_display"  valign="top" > <?php _e('Earnings', 'dc-woocommerce-multi-vendor'); ?> </td>
                            <td align="center" class="no_display" valign="top"  > <?php _e('Status', 'dc-woocommerce-multi-vendor'); ?> </td>
                            <td align="center"  valign="top" ><?php _e('Actions', 'dc-woocommerce-multi-vendor'); ?> </td>
                        </tr>
                        <?php $WCMp->template->get_template('vendor-dashboard/vendor-orders/vendor-orders-item.php', array('vendor' => $vendor, 'orders' => array_slice($customer_orders['all'], 0, 6), 'order_status' => 'all')); ?>
                    </table>
                </div>
                <div class="wcmp_table_loader">
                    <?php
                    $capabilities_settings = get_wcmp_vendor_settings('wcmp_capabilities_order_settings_name');
                    if (isset($capabilities_settings['is_order_csv_export'])) {
                        if ($capabilities_settings['is_order_csv_export'] == 'Enable') {
                            ?>
                            <button type="submit" name="download_all_all_csv" class="wcmp_black_btn"><?php _e('Download CSV', 'dc-woocommerce-multi-vendor'); ?></button>

                            <?php
                        }
                    }
                    ?>
                    <input type="hidden" id="wcmp_all_order_total_hidden" name="wcmp_submit_order_total_hidden" value="all" /> 
                    <?php if (count($customer_orders['all']) > 6) { ?><button  value="Show" data-shown="6" data-type="all" data-total="<?php echo count($customer_orders['all']); ?>" name="wcmp_download_order_submit" class="wcmp_black_btn wcmp_download_order_submit" style="float:right"><?php _e('Show More', 'dc-woocommerce-multi-vendor'); ?></button><?php } ?>
                    <div class="clear"></div>
                </div>
            </form>
        <?php } else { ?>
            <div class="wcmp_table_loader"> <?php _e('Showing Results', 'dc-woocommerce-multi-vendor'); ?><span> 0 </span></div> 
        <?php } ?>
    </div>
    <div class="wcmp_tabbody" id="processing">
        <?php if (!empty($customer_orders['processing'])) { ?>
            <form name="wcmp_vendor_dashboard_processing_stat_export" method="post" >
                <div class="wcmp_table_loader"> <?php _e('Showing Results', 'dc-woocommerce-multi-vendor'); ?><span> 
                        <?php if (count($customer_orders['processing']) > 6) { ?>
                            <span>
                                <span class="wcmp_processing_now_showing"> 
                                    <?php echo '6'; ?>
                                </span> 
                                out of <?php echo count($customer_orders['processing']); ?>
                            </span> 
                            <?php
                        } else {
                            echo '<span>' . count($customer_orders['processing']) . '</span>';
                        }
                        ?>
                </div>
                <div class="wcmp_table_holder">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="wcmp_order_processing_table">
                        <tr>
                            <td align="center"  valign="top"  width="20"><span class="input-group-addon beautiful">
                                    <input type="checkbox"  class="select_all_processing" >
                                </span></td>
                            <td align="center" valign="top"  ><?php _e('ID', 'dc-woocommerce-multi-vendor'); ?></td>
                            <td  align="center" valign="top" ><?php _e('Date', 'dc-woocommerce-multi-vendor'); ?><br>
                                <sub><?php _e('dd/mm', 'dc-woocommerce-multi-vendor'); ?></sub></td>
                            <td align="center" class="no_display"  valign="top" ><?php _e('Earnings', 'dc-woocommerce-multi-vendor'); ?></td>
                            <td align="center" class="no_display" valign="top"  ><?php _e('Status', 'dc-woocommerce-multi-vendor'); ?></td>
                            <td align="center"  valign="top" ><?php _e('Actions', 'dc-woocommerce-multi-vendor'); ?></td>
                        </tr>
                        <?php $WCMp->template->get_template('vendor-dashboard/vendor-orders/vendor-orders-item.php', array('vendor' => $vendor, 'orders' => array_slice($customer_orders['processing'], 0, 6), 'order_status' => 'processing')); ?>
                    </table>
                </div>
                <div class="wcmp_table_loader">
                    <?php
                    $capabilities_settings = get_wcmp_vendor_settings('wcmp_capabilities_order_settings_name');
                    if (isset($capabilities_settings['is_order_csv_export'])) {
                        if ($capabilities_settings['is_order_csv_export'] == 'Enable') {
                            ?>
                            <button type="submit" name="download_processing_all_csv" class="wcmp_black_btn"><?php _e('Download CSV', 'dc-woocommerce-multi-vendor'); ?></button>
                            <?php
                        }
                    }
                    ?>
                    <input type="hidden" name="wcmp_submit_order_total_hidden" value="processing" />
                    <?php if (count($customer_orders['processing']) > 6) { ?><button  value="Show" data-shown="6" data-type="processing" data-total="<?php echo count($customer_orders['processing']); ?>" name="wcmp_download_order_submit" class="wcmp_black_btn wcmp_download_order_submit" style="float:right"><?php _e('Show More', 'dc-woocommerce-multi-vendor'); ?></button><?php } ?>
                    <div class="clear"></div>
                </div>
            </form>
        <?php } else { ?>
            <div class="wcmp_table_loader"> <?php _e('Showing Results', 'dc-woocommerce-multi-vendor'); ?><span> 0 </span></div> 
        <?php } ?>
    </div>
    <div class="wcmp_tabbody" id="completed">
        <?php if (!empty($customer_orders['completed'])) { ?>
            <form name="wcmp_vendor_dashboard_completed_stat_export" method="post" >
                <div class="wcmp_table_loader"> <?php _e('Showing Results', 'dc-woocommerce-multi-vendor'); ?><span> 
                        <?php if (count($customer_orders['completed']) > 6) { ?>
                            <span>
                                <span class="wcmp_completed_now_showing"> 
                                    <?php echo '6'; ?>
                                </span> 
                                out of <?php echo count($customer_orders['completed']); ?>
                            </span> 
                            <?php
                        } else {
                            echo '<span>' . count($customer_orders['completed']) . '</span>';
                        }
                        ?>
                </div>
                <div class="wcmp_table_holder">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="wcmp_order_completed_table">
                        <tr>
                            <td align="center"  valign="top"  width="20"><span class="input-group-addon beautiful">
                                    <input type="checkbox"  class="select_all_completed" >
                                </span></td>
                            <td align="center" valign="top"  ><?php _e('ID', 'dc-woocommerce-multi-vendor'); ?></td>
                            <td  align="center" valign="top" ><?php _e('Date', 'dc-woocommerce-multi-vendor'); ?><br>
                                <sub><?php _e('dd/mm', 'dc-woocommerce-multi-vendor'); ?></sub></td>
                            <td align="center"  class="no_display" valign="top" > <?php _e('Earnings', 'dc-woocommerce-multi-vendor'); ?> </td>
                            <td align="center" class="no_display" valign="top"  ><?php _e('Status', 'dc-woocommerce-multi-vendor'); ?></td>
                            <td align="center"  valign="top" ><?php _e('Actions', 'dc-woocommerce-multi-vendor'); ?></td>
                        </tr>
                        <?php $WCMp->template->get_template('vendor-dashboard/vendor-orders/vendor-orders-item.php', array('vendor' => $vendor, 'orders' => array_slice($customer_orders['completed'], 0, 6), 'order_status' => 'completed')); ?>
                    </table>
                </div>
                <div class="wcmp_table_loader">
                    <?php
                    $capabilities_settings = get_wcmp_vendor_settings('wcmp_capabilities_order_settings_name');
                    if (isset($capabilities_settings['is_order_csv_export'])) {
                        if ($capabilities_settings['is_order_csv_export'] == 'Enable') {
                            ?>
                            <button type="submit" name="download_completed_all_csv" class="wcmp_black_btn"><?php _e('Download CSV', 'dc-woocommerce-multi-vendor'); ?></button>
                            <?php
                        }
                    }
                    ?>
                    <input type="hidden" name="wcmp_submit_order_total_hidden" value="completed" />
                    <?php if (count($customer_orders['completed']) > 6) { ?><button  value="Show" data-shown="6" data-type="completed" data-total="<?php echo count($customer_orders['completed']); ?>" name="wcmp_download_order_submit" class="wcmp_black_btn wcmp_download_order_submit" style="float:right"><?php _e('Show More', 'dc-woocommerce-multi-vendor'); ?></button><?php } ?>
                    <div class="clear"></div>
                </div>
            </form>
        <?php } else { ?>
            <div class="wcmp_table_loader"> <?php _e('Showing Results', 'dc-woocommerce-multi-vendor'); ?><span> 0 </span></div> 
        <?php } ?>
    </div>
</div>