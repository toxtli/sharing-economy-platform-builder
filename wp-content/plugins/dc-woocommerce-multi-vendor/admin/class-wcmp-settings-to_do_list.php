<?php

class WCMp_Settings_To_Do_List {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;

    /**
     * Start up
     */
    public function __construct($tab) {
        $this->tab = $tab;
        $this->options = get_option("wcmp_{$this->tab}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp;
        do_action('before_wcmp_to_do_list');
        //pending vendor
        $get_pending_vendors = get_users('role=dc_pending_vendor');
        if (!empty($get_pending_vendors)) {
            ?>
            <h3><?php echo apply_filters('to_do_pending_vendor_text', __('Pending Vendor Approval', 'dc-woocommerce-multi-vendor')); ?></h3>
            <table class="form-table" id="to_do_list">
                <tbody>
                    <tr>
                        <th style="width:50%" ><?php _e('Pending User', 'dc-woocommerce-multi-vendor'); ?> </th>
                        <th><?php _e('Edit', 'dc-woocommerce-multi-vendor'); ?></th>
                        <th><?php _e('Activate', 'dc-woocommerce-multi-vendor'); ?></th>
                        <th><?php _e('Reject', 'dc-woocommerce-multi-vendor'); ?></th>
                        <th><?php _e('Dismiss', 'dc-woocommerce-multi-vendor'); ?></th>
                    </tr>
                    <?php
                    foreach ($get_pending_vendors as $pending_vendor) {
                        $dismiss = get_user_meta($pending_vendor->ID, '_dismiss_to_do_list', true);
                        if ($dismiss)
                            continue;
                        ?>
                        <tr>
                            <td style="width:50%" class="username column-username"><img alt="" src="<?php echo $WCMp->plugin_url . 'assets/images/wp-avatar-frau.jpg'; ?>" class="avatar avatar-32 photo" height="32" width="32"><?php echo $pending_vendor->user_login; ?></td>
                            <td class="edit"><a target="_blank" href="user-edit.php?user_id=<?php echo $pending_vendor->ID; ?>&amp;wp_http_referer=%2Fwordpress%2Fdc_vendor%2Fwp-admin%2Fusers.php%3Frole%3Ddc_pending_vendor"><input type="button" class="vendor_edit_button" value="Edit" /> </a> </td>
                            <td class="activate"><input class="activate_vendor" type="button" class="activate_vendor" data-id="<?php echo $pending_vendor->ID; ?>" value="Activate" ></td>
                            <td class="reject"><input class="reject_vendor" type="button" class="reject_vendor" data-id="<?php echo $pending_vendor->ID; ?>" value="Reject"></td>
                            <td class="dismiss"><input class="vendor_dismiss_button" type="button" data-type="user" data-id="<?php echo $pending_vendor->ID; ?>" id="dismiss_request" name="dismiss_request" value="Dismiss"></td>
                        </tr>
            <?php } ?>
                </tbody>
            </table>
        <?php
        }
        $vendor_ids = array();
        $vendors = get_wcmp_vendors();
        if (!empty($vendors) && is_array($vendors)) {
            foreach ($vendors as $vendor) {
                $vendor_ids[] = $vendor->id;
            }
        }
        //coupon
        $args = array(
            'posts_per_page' => -1,
            'author__in' => $vendor_ids,
            'post_type' => 'shop_coupon',
            'post_status' => 'pending',
        );
        $get_pending_coupons = new WP_Query($args);
        $get_pending_coupons = $get_pending_coupons->get_posts();
        if (!empty($get_pending_coupons)) {
            ?>
            <h3><?php _e('Pending Coupons Approval', 'dc-woocommerce-multi-vendor'); ?></h3>
            <table class="form-table" id="to_do_list">
                <tbody>
                    <tr>
                        <th><?php _e('Vendor Name', 'dc-woocommerce-multi-vendor'); ?> </th>
                        <th><?php _e('Coupon Name', 'dc-woocommerce-multi-vendor'); ?></th>
                        <th><?php _e('Edit', 'dc-woocommerce-multi-vendor'); ?></th>
                        <th><?php _e('Dismiss', 'dc-woocommerce-multi-vendor'); ?></th>
                    </tr>
                        <?php
                        foreach ($get_pending_coupons as $get_pending_coupon) {
                            $dismiss = get_post_meta($get_pending_coupon->ID, '_dismiss_to_do_list', true);
                            if ($dismiss)
                                continue;
                            ?>
                        <tr>
                        <?php $currentvendor = get_userdata($get_pending_coupon->post_author); ?>
                            <td class="coupon column-coupon"><a href="user-edit.php?user_id=<?php echo $get_pending_coupon->post_author; ?>&amp;wp_http_referer=%2Fwordpress%2Fdc_vendor%2Fwp-admin%2Fusers.php%3Frole%3Ddc_vendor" target="_blank"><?php echo $currentvendor->display_name; ?></a></td>
                            <td class="coupon column-coupon"><?php echo $get_pending_coupon->post_title; ?></td>
                            <td class="edit"><a target="_blank" href="post.php?post=<?php echo $get_pending_coupon->ID; ?>&action=edit"><input type="button" class="vendor_edit_button" value="Edit" /> </a> </td>
                            <td class="dismiss"><input class="vendor_dismiss_button" type="button" data-type="shop_coupon" data-id="<?php echo $get_pending_coupon->ID; ?>" id="dismiss_request" name="dismiss_request" value="Dismiss"></td>
                        </tr>
            <?php } ?>
                </tbody>
            </table>
        <?php
        }

        //product
        $args = array(
            'posts_per_page' => -1,
            'author__in' => $vendor_ids,
            'post_type' => 'product',
            'post_status' => 'pending',
        );
        $get_pending_products = new WP_Query($args);
        $get_pending_products = $get_pending_products->get_posts();
        if (!empty($get_pending_products)) {
            ?>
            <h3><?php _e('Pending Products Approval', 'dc-woocommerce-multi-vendor'); ?></h3>
            <table class="form-table" id="to_do_list">
                <tbody>
                    <tr>
                        <th><?php _e('Vendor Name', 'dc-woocommerce-multi-vendor'); ?></th>
                        <th><?php _e('Product Name', 'dc-woocommerce-multi-vendor'); ?></th>
                        <th><?php _e('Edit', 'dc-woocommerce-multi-vendor'); ?></th>
                        <th><?php _e('Dismiss', 'dc-woocommerce-multi-vendor'); ?></th>
                    </tr>
            <?php
            foreach ($get_pending_products as $get_pending_product) {
                $dismiss = get_post_meta($get_pending_product->ID, '_dismiss_to_do_list', true);
                if ($dismiss)
                    continue;
                ?>
                        <tr>
                <?php $currentvendor = get_userdata($get_pending_product->post_author); ?>
                            <td class="vendor column-coupon"><a href="user-edit.php?user_id=<?php echo $get_pending_product->post_author; ?>&amp;wp_http_referer=%2Fwordpress%2Fdc_vendor%2Fwp-admin%2Fusers.php%3Frole%3Ddc_vendor" target="_blank"><?php echo $currentvendor->display_name; ?></a></td>
                            <td class="coupon column-coupon"><?php echo $get_pending_product->post_title; ?></td>
                            <td class="edit"><a target="_blank" href="post.php?post=<?php echo $get_pending_product->ID; ?>&action=edit"><input type="button" class="vendor_edit_button" value="Edit" /> </a> </td>
                            <td class="dismiss"><input class="vendor_dismiss_button" data-type="product" data-id="<?php echo $get_pending_product->ID; ?>"  type="button" id="dismiss_request" name="dismiss_request" value="Dismiss"></td>
                        </tr>
            <?php } ?>
                </tbody>
            </table>
        <?php
        }


        //commission
        $args = array(
            'post_type' => 'wcmp_transaction',
            'post_status' => 'wcmp_processing',
            'meta_key' => 'transaction_mode',
            'meta_value' => 'direct_bank',
            'posts_per_page' => -1
        );
        $transactions = get_posts($args);

        if (!empty($transactions)) {
            ?>
            <h3><?php _e('Pending Bank Transfer', 'dc-woocommerce-multi-vendor'); ?></h3>
            <table class="form-table" id="to_do_list">
                <tbody>
                    <tr>
                        <th><?php _e('Vendor', 'dc-woocommerce-multi-vendor'); ?> </th>
                        <th><?php _e('Commission', 'dc-woocommerce-multi-vendor'); ?> </th>
                        <th><?php _e('Amount', 'dc-woocommerce-multi-vendor'); ?></th>
                        <th><?php _e('Account Detail', 'dc-woocommerce-multi-vendor'); ?></th>
                        <th><?php _e('Notify the Vendor', 'dc-woocommerce-multi-vendor'); ?></th>
                        <th><?php _e('Dismiss', 'dc-woocommerce-multi-vendor'); ?></th>
                    </tr>
                    <?php
                    foreach ($transactions as $transaction) {
                        $dismiss = get_post_meta($transaction->ID, '_dismiss_to_do_list', true);
                        $vendor_term_id = $transaction->post_author;
                        $currentvendor = get_wcmp_vendor_by_term($vendor_term_id);
                        if ($dismiss || !$currentvendor) {
                            continue;
                        }
                        $account_name = get_user_meta($currentvendor->id, '_vendor_account_holder_name', true);
                        $account_no = get_user_meta($currentvendor->id, '_vendor_bank_account_number', true);
                        $bank_name = get_user_meta($currentvendor->id, '_vendor_bank_name', true);
                        $iban = get_user_meta($currentvendor->id, '_vendor_iban', true);
                        $amount = get_post_meta($transaction->ID, 'amount', true) - get_post_meta($transaction->ID, 'transfer_charge', true) - get_post_meta($transaction->ID, 'gateway_charge', true);
                        ?>
                        <tr>
                <?php
                ?>
                            <td class="vendor column-coupon"><a href="user-edit.php?user_id=<?php echo $currentvendor->id; ?>&amp;wp_http_referer=%2Fwordpress%2Fdc_vendor%2Fwp-admin%2Fusers.php%3Frole%3Ddc_vendor" target="_blank"><?php echo $currentvendor->user_data->display_name; ?></a></td>
                            <td class="commission column-coupon"><?php echo $transaction->post_title; ?></td>
                            <td class="commission_val column-coupon"><?php echo wc_price($amount); ?></td>
                            <td class="account_detail"><?php echo __('Account Name- ', 'dc-woocommerce-multi-vendor') . ' ' . $account_name . '<br>' . __('Account No - ', 'dc-woocommerce-multi-vendor') . $account_no . '<br>' . __('Bank Name - ', 'dc-woocommerce-multi-vendor') . $bank_name . '<br>' . __('IBAN - ', 'dc-woocommerce-multi-vendor') . $iban; ?></td>
                            <td class="done"><input class="vendor_transaction_done_button" data-transid="<?php echo $transaction->ID; ?>" data-vendorid="<?php echo $vendor_term_id; ?>" type="button" id="done_request" name="done_request" value="Done"></td>
                            <td class="dismiss"><input class="vendor_dismiss_button" data-type="dc_commission" data-id="<?php echo $transaction->ID; ?>" type="button" id="dismiss_request" name="dismiss_request" value="Dismiss"></td>
                        </tr>
            <?php } ?>
                </tbody>
            </table>
        <?php
        }
        do_action('after_wcmp_to_do_list');
    }

}
