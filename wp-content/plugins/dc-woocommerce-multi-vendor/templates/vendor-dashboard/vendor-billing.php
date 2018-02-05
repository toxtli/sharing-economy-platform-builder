<?php
/*
 * The template for displaying vendor dashboard
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-billing.php
 *
 * @author 	WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.4.5
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMp;
?>
<form method="post" name="shop_settings_form" class="wcmp_billing_form">
    <div class="wcmp_form1"> 
        <?php do_action('wcmp_before_vendor_billing'); ?>        
        <?php
        $payment_admin_settings = get_option('wcmp_payment_settings_name');
        $payment_mode = array('' => __('Payment Mode', 'dc-woocommerce-multi-vendor'));
        if (isset($payment_admin_settings['payment_method_paypal_masspay']) && $payment_admin_settings['payment_method_paypal_masspay'] = 'Enable') {
            $payment_mode['paypal_masspay'] = __('PayPal Masspay', 'dc-woocommerce-multi-vendor');
        }
        if (isset($payment_admin_settings['payment_method_paypal_payout']) && $payment_admin_settings['payment_method_paypal_payout'] = 'Enable') {
            $payment_mode['paypal_payout'] = __('PayPal Payout', 'dc-woocommerce-multi-vendor');
        }
        if (isset($payment_admin_settings['payment_method_direct_bank']) && $payment_admin_settings['payment_method_direct_bank'] = 'Enable') {
            $payment_mode['direct_bank'] = __('Direct Bank', 'dc-woocommerce-multi-vendor');
        }
        $vendor_payment_mode_select = apply_filters('wcmp_vendor_payment_mode', $payment_mode);
        if (!empty($vendor_payment_mode_select)) {
            ?>
            <div class="wcmp_headding2"><?php _e('Payment Method', 'dc-woocommerce-multi-vendor'); ?></div>
            <div class="two_third_part">
                <div class="select_box no_input">						
                    <select id="vendor_payment_mode" disabled name="vendor_payment_mode" class="user-profile-fields">
                        <?php foreach ($vendor_payment_mode_select as $key => $value) { ?>
                            <option <?php if ($vendor_payment_mode['value'] == $key) echo 'selected' ?>  value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="clear"></div>
        <?php }
        ?>
        <?php if ($vendor_payment_mode['value'] == 'paypal_masspay' || $vendor_payment_mode['value'] == 'paypal_payout'): ?>    
            <div class="wcmp_headding2"><?php _e('Paypal', 'dc-woocommerce-multi-vendor'); ?></div>
            <p><?php _e('Enter your Paypal ID', 'dc-woocommerce-multi-vendor'); ?></p>
            <input  class="long no_input" readonly type="text" name="vendor_paypal_email" value="<?php echo isset($vendor_paypal_email['value']) ? $vendor_paypal_email['value'] : ''; ?>"  placeholder="<?php _e('Enter your Paypal ID', 'dc-woocommerce-multi-vendor'); ?>">
        <?php endif; ?>
        <?php if ($vendor_payment_mode['value'] == 'direct_bank'): ?>
            <div class="wcmp_headding2"><?php _e('Bank Transfer', 'dc-woocommerce-multi-vendor'); ?></div>
            <p><?php _e('Enter your Bank Details', 'dc-woocommerce-multi-vendor'); ?></p>
            <div class="two_third_part">
                <div class="select_box no_input">
                    <select id="vendor_bank_account_type" disabled name="vendor_bank_account_type" class="user-profile-fields">
                        <option <?php if ($vendor_bank_account_type['value'] == 'current') echo 'selected' ?> value="current"><?php _e('Current', 'dc-woocommerce-multi-vendor'); ?></option>
                        <option <?php if ($vendor_bank_account_type['value'] == 'savings') echo 'selected' ?>  value="savings"><?php _e('Savings', 'dc-woocommerce-multi-vendor'); ?></option>
                    </select>
                </div>
            </div>
            <input class="long no_input" readonly type="text" id="vendor_bank_account_number" name="vendor_bank_account_number" class="user-profile-fields" value="<?php echo isset($vendor_bank_account_number['value']) ? $vendor_bank_account_number['value'] : ''; ?>" placeholder="<?php _e('Account Number', 'dc-woocommerce-multi-vendor'); ?>">
            <div class="half_part">
                <input class="long no_input" readonly type="text" id="vendor_bank_name" name="vendor_bank_name" class="user-profile-fields" value="<?php echo isset($vendor_bank_name['value']) ? $vendor_bank_name['value'] : ''; ?>" placeholder="<?php _e('Bank Name', 'dc-woocommerce-multi-vendor'); ?>">
            </div>
            <div class="half_part">
                <input class="long no_input" readonly type="text" id="vendor_aba_routing_number" name="vendor_aba_routing_number" class="user-profile-fields" value="<?php echo isset($vendor_aba_routing_number['value']) ? $vendor_aba_routing_number['value'] : ''; ?>" placeholder="<?php _e('ABA Routing Number', 'dc-woocommerce-multi-vendor'); ?>">
            </div>
            <div class="clear"></div>
            <textarea class="long no_input" readonly name="vendor_bank_address" cols="" rows="" placeholder="<?php _e('Bank Address', 'dc-woocommerce-multi-vendor'); ?>"><?php echo isset($vendor_bank_address['value']) ? $vendor_bank_address['value'] : ''; ?></textarea>
            <div class="one_third_part">
                <input class="long no_input" readonly type="text" placeholder="<?php _e('Destination Currency', 'dc-woocommerce-multi-vendor'); ?>" name="vendor_destination_currency" value="<?php echo isset($vendor_destination_currency['value']) ? $vendor_destination_currency['value'] : ''; ?>">
            </div>
            <div class="one_third_part">
                <input class="long no_input" readonly type="text" placeholder="<?php _e('IBAN', 'dc-woocommerce-multi-vendor'); ?>"  name="vendor_iban" value="<?php echo isset($vendor_iban['value']) ? $vendor_iban['value'] : ''; ?>">
            </div>
            <div class="one_third_part">
                <input class="long no_input" readonly type="text" placeholder="<?php _e('Account Holder Name', 'dc-woocommerce-multi-vendor'); ?>"  name="vendor_account_holder_name" value="<?php echo isset($vendor_account_holder_name['value']) ? $vendor_account_holder_name['value'] : ''; ?>">
                <div class="clear"></div>
            </div>
        <?php endif; ?>
        <?php do_action('wcmp_after_vendor_billing'); ?>
        <?php do_action('other_exta_field_dcmv'); ?>
        <div class="action_div_space"> </div>
        <div class="action_div">
            <button class="wcmp_orange_btn" name="store_save_billing" ><?php _e('Save Options', 'dc-woocommerce-multi-vendor'); ?></button>
            <div class="clear"></div>
        </div>
    </div>
</form>