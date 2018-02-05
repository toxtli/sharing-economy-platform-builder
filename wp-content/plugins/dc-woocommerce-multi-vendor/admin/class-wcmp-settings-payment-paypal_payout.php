<?php

class WCMp_Settings_Payment_Paypal_Payout {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $subsection;
    private $paypal_client_id;
    private $paypal_client_secret;

    /**
     * Start up
     */
    public function __construct($tab, $subsection) {
        $this->tab = $tab;
        $this->subsection = $subsection;
        $this->options = get_option("wcmp_{$this->tab}_{$this->subsection}_settings_name");
        $paypal_details = get_option('woocommerce_paypal_settings');
        if (isset($paypal_details['client_id']) && !empty($paypal_details['client_id'])) {
            $this->paypal_client_id = $paypal_details['client_id'];
        }
        if (isset($paypal_details['client_secret']) && !empty($paypal_details['client_secret'])) {
            $this->paypal_client_secret = $paypal_details['client_secret'];
        }
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp;

        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "subsection" => "{$this->subsection}",
            "sections" => array(
                "wcmp_payment_paypal_payout_settings_section" => array("title" => '', // Section one
                    "fields" => array(
                        "client_id" => array('title' => __('Client Id', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'client_id', 'label_for' => 'client_id', 'name' => 'client_id', 'dfvalue' => $this->paypal_client_id),
                        "client_secret" => array('title' => __('Client Secret', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'client_secret', 'label_for' => 'client_secret', 'name' => 'client_secret', 'dfvalue' => $this->paypal_client_secret),
                        "is_asynchronousmode" => array('title' => __('Enable Asynchronous Mode', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'is_asynchronousmode', 'label_for' => 'is_asynchronousmode', 'name' => 'is_asynchronousmode', 'value' => 'Enable'), // Checkbox
                        "is_testmode" => array('title' => __('Enable Test Mode', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'is_testmode', 'label_for' => 'is_testmode', 'name' => 'is_testmode', 'value' => 'Enable'), // Checkbox
                    ),
                )
            ),
        );

        $WCMp->admin->settings->settings_field_withsubtab_init(apply_filters("settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_payment_paypal_payout_settings_sanitize($input) {
        global $WCMp;
        $new_input = array();
        $hasError = false;
        if (isset($input['client_id'])) {
            $new_input['client_id'] = sanitize_text_field($input['client_id']);
        }
        if (isset($input['client_secret'])) {
            $new_input['client_secret'] = sanitize_text_field($input['client_secret']);
        }
        if (isset($input['is_asynchronousmode'])) {
            $new_input['is_asynchronousmode'] = sanitize_text_field($input['is_asynchronousmode']);
        }
        if (isset($input['is_testmode'])) {
            $new_input['is_testmode'] = sanitize_text_field($input['is_testmode']);
        }
        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmp_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Paypal Payout Settings Updated', 'dc-woocommerce-multi-vendor'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }

}
