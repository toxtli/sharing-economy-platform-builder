<?php

class WCMp_Settings_Payment {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $automatic_payment_method;
    private $withdrawal_payment_method;

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

        $this->automatic_payment_method = apply_filters('automatic_payment_method', array('paypal_masspay' => __('Paypal Masspay', 'dc-woocommerce-multi-vendor'), 'paypal_payout' => __('Paypal Payout', 'dc-woocommerce-multi-vendor'), 'direct_bank' => __('Direct Bank Transfer', 'dc-woocommerce-multi-vendor')));
        $automatic_method = array();
        $gateway_charge = array();
        $i = 0;
        foreach ($this->automatic_payment_method as $key => $val) {
            if ($i == 0) {
                $automatic_method['payment_method_' . $key] = array('title' => __('Allowed Payment Methods', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'payment_method_' . $key, 'class' => 'automatic_payment_method', 'label_for' => 'payment_method_' . $key, 'text' => $val, 'name' => 'payment_method_' . $key, 'value' => 'Enable', 'data-display-label' => $val);
            } else if ($key == 'direct_bank') {
                $automatic_method['payment_method_' . $key] = array('title' => __('', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'payment_method_' . $key, 'class' => 'automatic_payment_method', 'label_for' => 'payment_method_' . $key, 'text' => $val, 'name' => 'payment_method_' . $key, 'value' => 'Enable', 'data-display-label' => $val);
            } else {
                $automatic_method['payment_method_' . $key] = array('title' => __('', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'payment_method_' . $key, 'class' => 'automatic_payment_method', 'label_for' => 'payment_method_' . $key, 'text' => $val, 'name' => 'payment_method_' . $key, 'value' => 'Enable', 'data-display-label' => $val);
            }
            $gateway_charge['gateway_charge_' . $key] = array('title' => __('', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'gateway_charge_' . $key, 'class' => 'payment_gateway_charge regular-text', 'label_for' => 'gateway_charge_' . $key, 'name' => 'gateway_charge_' . $key, 'placeholder' => __('For ', 'dc-woocommerce-multi-vendor') . $val, 'desc' => __('Gateway Charge For ', 'dc-woocommerce-multi-vendor') . $val . __(' (in percent)', 'dc-woocommerce-multi-vendor'));
            $i++;
        }

        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "sections" => array(
                "revenue_sharing_mode_section" => array("title" => __('Revenue Sharing Mode', 'dc-woocommerce-multi-vendor'), // Section one
                    "fields" => array(
                        "revenue_sharing_mode" => array('title' => __('Mode ', 'dc-woocommerce-multi-vendor'), 'type' => 'radio', 'id' => 'revenue_sharing_mode', 'label_for' => 'revenue_sharing_mode', 'name' => 'revenue_sharing_mode', 'dfvalue' => 'vendor', 'options' => array('admin' => __('Admin fees', 'dc-woocommerce-multi-vendor'), 'vendor' => __('Vendor Commissions', 'dc-woocommerce-multi-vendor')), 'desc' => ''), // Radio
                    ),
                ),
                "what_to_pay_section" => array("title" => __('What to Pay', 'dc-woocommerce-multi-vendor'), // Section one
                    "fields" => array(
                        "commission_type" => array('title' => __('Commission Type', 'dc-woocommerce-multi-vendor'), 'type' => 'select', 'id' => 'commission_typee', 'label_for' => 'commission_typee', 'name' => 'commission_type', 'options' => array('' => __('Choose Commission Type', 'dc-woocommerce-multi-vendor'), 'fixed' => __('Fixed Amount', 'dc-woocommerce-multi-vendor'), 'percent' => __('Percentage', 'dc-woocommerce-multi-vendor'), 'fixed_with_percentage' => __('%age + Fixed (per transaction)', 'dc-woocommerce-multi-vendor'), 'fixed_with_percentage_qty' => __('%age + Fixed (per unit)', 'dc-woocommerce-multi-vendor')), 'desc' => __('Choose your preferred commission type. It will affect all commission calculations.', 'dc-woocommerce-multi-vendor')), // Select
                        "default_commission" => array('title' => __('Commission value', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'default_commissionn', 'label_for' => 'default_commissionn', 'name' => 'default_commission', 'desc' => __('This will be the default commission(in percentage or fixed) paid to vendors if product and vendor specific commission is not set. ', 'dc-woocommerce-multi-vendor')), // Text
                        "default_percentage" => array('title' => __('Commission Percentage', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'default_percentage', 'label_for' => 'default_percentage', 'name' => 'default_percentage', 'desc' => __('This will be the default percentage paid to vendors if product and vendor specific commission is not set. ', 'dc-woocommerce-multi-vendor')), // Text
                        "fixed_with_percentage" => array('title' => __('Fixed Amount', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'fixed_with_percentage', 'label_for' => 'fixed_with_percentage', 'name' => 'fixed_with_percentage', 'desc' => __('Fixed (per transaction)', 'dc-woocommerce-multi-vendor')), // Text
                        "fixed_with_percentage_qty" => array('title' => __('Fixed Amount', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'fixed_with_percentage_qty', 'label_for' => 'fixed_with_percentage_qty', 'name' => 'fixed_with_percentage_qty', 'desc' => __('Fixed (per unit)', 'dc-woocommerce-multi-vendor')), // Text
                        "commission_include_coupon" => array('title' => __('Share Coupon Discount', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'commission_include_couponn', 'label_for' => 'commission_include_couponn', 'text' => __('Vendors commission will be calculated AFTER deducting the discount, otherwise, the site owner will bear the cost of the coupon.', 'dc-woocommerce-multi-vendor'), 'name' => 'commission_include_coupon', 'value' => 'Enable'), // Checkbox
                        "give_tax" => array('title' => __('Tax', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'give_taxx', 'label_for' => 'give_taxx', 'name' => 'give_tax', 'text' => __('Transfer the tax collected (per product) to the vendor. ', 'dc-woocommerce-multi-vendor'), 'value' => 'Enable'), // Checkbox
                        "give_shipping" => array('title' => __('Shipping', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'give_shippingg', 'label_for' => 'give_shippingg', 'name' => 'give_shipping', 'text' => __('Transfer shipping charges collected (per product) to the vendor.', 'dc-woocommerce-multi-vendor'), 'value' => 'Enable'), // Checkbox
                        "commission_threshold" => array('title' => __('Disbursement threshold', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'commission_threshold', 'label_for' => 'commission_threshold', 'name' => 'commission_threshold', 'desc' => __('Threshold amount required to disburse commission.', 'dc-woocommerce-multi-vendor')), // Text
                        "commission_threshold_time" => array('title' => __('Withdrawl Locking Period', 'dc-woocommerce-multi-vendor'), 'type' => 'number', 'id' => 'commission_threshold_time', 'label_for' => 'commission_threshold_time', 'name' => 'commission_threshold_time', 'desc' => __('Minimum time required, before an individual commision is ready for withdrawl.', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'in days'), // Text
                    ),
                ),
                "wcmp_default_settings_section" => array("title" => __('How/When to Pay ', 'dc-woocommerce-multi-vendor'), // Section one
                    "fields" => array_merge($automatic_method, array(
                        "payment_gateway_charge" => array('title' => __('Payment Gateway Charge', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'payment_gateway_charge', 'label_for' => 'payment_gateway_charge', 'name' => 'payment_gateway_charge', 'text' => __('If checked, you can set payment gateway charge to vendor for commission disbursement. ', 'dc-woocommerce-multi-vendor'), 'value' => 'Enable')
                            ), $gateway_charge, array(
                        "choose_payment_mode_automatic_disbursal" => array('title' => __('Disbursal Schedule', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'wcmp_disbursal_mode_admin', 'label_for' => 'wcmp_disbursal_mode_admin', 'name' => 'wcmp_disbursal_mode_admin', 'text' => __('If checked, automatically vendors commission will disburse. ', 'dc-woocommerce-multi-vendor'), 'value' => 'Enable'), // Checkbox
                        "payment_schedule" => array('title' => __('Set Schedule', 'dc-woocommerce-multi-vendor'), 'type' => 'radio', 'id' => 'payment_schedule', 'label_for' => 'payment_schedule', 'name' => 'payment_schedule', 'dfvalue' => 'daily', 'options' => array('weekly' => __('Weekly', 'dc-woocommerce-multi-vendor'), 'daily' => __('Daily', 'dc-woocommerce-multi-vendor'), 'monthly' => __('Monthly', 'dc-woocommerce-multi-vendor'), 'fortnightly' => __('Fortnightly', 'dc-woocommerce-multi-vendor'), 'hourly' => __('Hourly', 'dc-woocommerce-multi-vendor'))), // Radio
                            ), array("choose_payment_mode_request_disbursal" => array('title' => __('Withdrawl Request', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'wcmp_disbursal_mode_vendor', 'label_for' => 'wcmp_disbursal_mode_vendor', 'name' => 'wcmp_disbursal_mode_vendor', 'text' => __('Vendors can request for commission withdrawal. ', 'dc-woocommerce-multi-vendor'), 'value' => 'Enable'), // Checkbox                                                                            
                        "commission_transfer" => array('title' => __('Withdrawal Charges', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'commission_transfer', 'label_for' => 'commission_transfer', 'name' => 'commission_transfer', 'desc' => __('Vendor will be charged this amount per withdrawal after the quota of free withdrawals is over.', 'dc-woocommerce-multi-vendor')), // Text
                        "no_of_orders" => array('title' => __('Number of Free Withdrawals', 'dc-woocommerce-multi-vendor'), 'type' => 'number', 'id' => 'no_of_orders', 'label_for' => 'no_of_orders', 'name' => 'no_of_orders', 'desc' => __('Number of Free Withdrawal Requests.', 'dc-woocommerce-multi-vendor')), // Text                                                                                                          
                            )
                    ),
                ),
            ),
        );

        $WCMp->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_payment_settings_sanitize($input) {
        global $WCMp;
        $new_input = array();
        $hasError = false;
        if (isset($input['revenue_sharing_mode'])) {
            $new_input['revenue_sharing_mode'] = sanitize_text_field($input['revenue_sharing_mode']);
        }
        if (isset($input['is_mass_pay'])) {
            $new_input['is_mass_pay'] = sanitize_text_field($input['is_mass_pay']);
        }
        if (isset($input['default_commission'])) {
            $new_input['default_commission'] = floatval(sanitize_text_field($input['default_commission']));
        }
        if (isset($input['default_percentage'])) {
            $new_input['default_percentage'] = floatval(sanitize_text_field($input['default_percentage']));
        }
        if (isset($input['fixed_with_percentage_qty'])) {
            $new_input['fixed_with_percentage_qty'] = floatval(sanitize_text_field($input['fixed_with_percentage_qty']));
        }
        if (isset($input['fixed_with_percentage'])) {
            $new_input['fixed_with_percentage'] = floatval(sanitize_text_field($input['fixed_with_percentage']));
        }
        if (isset($input['commission_threshold'])) {
            $new_input['commission_threshold'] = floatval(sanitize_text_field($input['commission_threshold']));
        }
        if (isset($input['commission_threshold_time'])) {
            $new_input['commission_threshold_time'] = intval(sanitize_text_field($input['commission_threshold_time']));
        }
        if (isset($input['commission_transfer'])) {
            $new_input['commission_transfer'] = floatval(sanitize_text_field($input['commission_transfer']));
        }
        if (isset($input['no_of_orders'])) {
            $new_input['no_of_orders'] = intval(sanitize_text_field($input['no_of_orders']));
        }
        if (isset($input['commission_type'])) {
            $new_input['commission_type'] = sanitize_text_field($input['commission_type']);
        }
        if (isset($input['commission_include_coupon'])) {
            $new_input['commission_include_coupon'] = sanitize_text_field($input['commission_include_coupon']);
        }
        if (isset($input['give_tax'])) {
            $new_input['give_tax'] = sanitize_text_field($input['give_tax']);
        }
        if (isset($input['give_shipping'])) {
            $new_input['give_shipping'] = sanitize_text_field($input['give_shipping']);
        }
        if (isset($input['wcmp_disbursal_mode_admin'])) {
            $new_input['wcmp_disbursal_mode_admin'] = sanitize_text_field($input['wcmp_disbursal_mode_admin']);
        }
        if (isset($input['wcmp_disbursal_mode_vendor'])) {
            $new_input['wcmp_disbursal_mode_vendor'] = sanitize_text_field($input['wcmp_disbursal_mode_vendor']);
        }
        foreach ($this->automatic_payment_method as $key => $val) {
            if (isset($input['payment_method_' . $key])) {
                $new_input['payment_method_' . $key] = sanitize_text_field($input['payment_method_' . $key]);
            }
            if (isset($input['gateway_charge_' . $key])) {
                $new_input['gateway_charge_' . $key] = floatval(sanitize_text_field($input['gateway_charge_' . $key]));
            }
        }
        foreach ($this->withdrawal_payment_method as $key => $val) {
            if (isset($input['payment_method_' . $key])) {
                $new_input['payment_method_' . $key] = sanitize_text_field($input['payment_method_' . $key]);
            }
        }
        if (isset($input['payment_schedule'])) {
            $new_input['payment_schedule'] = $input['payment_schedule'];
        }
        if (isset($input['wcmp_disbursal_mode_admin'])) {
            $schedule = wp_get_schedule('masspay_cron_start');
            if ($schedule != $input['payment_schedule']) {
                if (wp_next_scheduled('masspay_cron_start')) {
                    $timestamp = wp_next_scheduled('masspay_cron_start');
                    wp_unschedule_event($timestamp, 'masspay_cron_start');
                }
                wp_schedule_event(time(), $input['payment_schedule'], 'masspay_cron_start');
            }
        } else {
            if (wp_next_scheduled('masspay_cron_start')) {
                $timestamp = wp_next_scheduled('masspay_cron_start');
                wp_unschedule_event($timestamp, 'masspay_cron_start');
            }
        }
        if (isset($input['payment_gateway_charge'])) {
            $new_input['payment_gateway_charge'] = sanitize_text_field($input['payment_gateway_charge']);
        }
        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_settings_name", esc_attr("wcmp_{$this->tab}_settings_admin_updated"), __('Payment Settings Updated', 'dc-woocommerce-multi-vendor'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_tab_new_input", $new_input, $input);
    }

    /**
     * Print the Section text
     */
    public function wcmp_default_settings_section_info() {
        global $WCMp;
        _e('Payment can be done only if vendors have valid PayPal Email Id in their profile. You can add from [Users->Edit Users->PayPal Email]', 'dc-woocommerce-multi-vendor');
    }

    /**
     * Print the Section text
     */
    public function revenue_sharing_mode_section_info() {
        global $WCMp;
        echo '<div class="wcmp_payment_help">';
        _e("If you are not sure about how to setup commissions and payment options in your marketplace, kindly read this <a href='https://wc-marketplace.com/knowledgebase/setting-up-commission-and-other-payments-for-wcmp/' terget='_blank'>article</a> before proceeding.", 'dc-woocommerce-multi-vendor');
        echo '</div>';
        ?>
        <style type="text/css">
             .wcmp_payment_help {
                display: inline-block;
                padding: 10px;
                background: #ffffff;
                color: #333;
                font-style: italic;
                max-width: 300px;
                position: absolute;
                right: 20px;
                z-index: 9;
            }
            @media (max-width: 960px){
                .wcmp_payment_help {
                    position: relative;
                    right: auto;
                }
            }
        </style>
        <?php

    }

}
