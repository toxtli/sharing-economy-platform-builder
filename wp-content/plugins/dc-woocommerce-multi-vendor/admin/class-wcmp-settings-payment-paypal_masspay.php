<?php

class WCMp_Settings_Payment_Paypal_Masspay {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $subsection;
    private $paypal_api_username;
    private $paypal_api_password;
    private $paypal_api_signature;

    /**
     * Start up
     */
    public function __construct($tab, $subsection) {
        $this->tab = $tab;
        $this->subsection = $subsection;
        $this->options = get_option("wcmp_{$this->tab}_{$this->subsection}_settings_name");
        $paypal_details = get_option('woocommerce_paypal_settings');
        if (isset($paypal_details['api_username']) && !empty($paypal_details['api_username'])) {
            $this->paypal_api_username = $paypal_details['api_username'];
        }
        if (isset($paypal_details['api_password']) && !empty($paypal_details['api_password'])) {
            $this->paypal_api_password = $paypal_details['api_password'];
        }
        if (isset($paypal_details['api_signature']) && !empty($paypal_details['api_signature'])) {
            $this->paypal_api_signature = $paypal_details['api_signature'];
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
                "wcmp_payment_paypal_masspay_settings_section" => array("title" => '', // Section one
                    "fields" => array(
                        "api_username" => array('title' => __('API Username', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'api_username', 'label_for' => 'api_username', 'dfvalue' => $this->paypal_api_username, 'name' => 'api_username'),
                        "api_pass" => array('title' => __('API Password', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'api_pass', 'label_for' => 'api_pass', 'name' => 'api_pass', 'dfvalue' => $this->paypal_api_password),
                        "api_signature" => array('title' => __('API Signature', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'api_signature', 'label_for' => 'api_signature', 'name' => 'api_signature', 'dfvalue' => $this->paypal_api_signature),
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
    public function wcmp_payment_paypal_masspay_settings_sanitize($input) {
        global $WCMp;
        $new_input = array();
        $hasError = false;
        if (isset($input['api_username'])) {
            $new_input['api_username'] = sanitize_text_field($input['api_username']);
        }
        if (isset($input['api_pass'])) {
            $new_input['api_pass'] = sanitize_text_field($input['api_pass']);
        }
        if (isset($input['api_signature'])) {
            $new_input['api_signature'] = sanitize_text_field($input['api_signature']);
        }
        if (isset($input['is_testmode'])) {
            $new_input['is_testmode'] = sanitize_text_field($input['is_testmode']);
        }
        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmp_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Paypal Masspay Settings Updated', 'dc-woocommerce-multi-vendor'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }

}
