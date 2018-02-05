<?php

class WCMp_Payment_Stripe_Gateway_Settings_Gneral {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $subsection;

    /**
     * Start up
     */
    public function __construct($tab, $subsection) {
        $this->tab = $tab;
        $this->subsection = $subsection;
        $this->options = get_option("wcmp_{$this->tab}_{$this->subsection}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp, $WCMp_Stripe_Gateway;

        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "subsection" => "{$this->subsection}",
            "sections" => array(
                "default_settings_section" => array("title" => __('', 'marketplace-stripe-gateway'), // Section one
                    "fields" => array(
                        "is_enable_stripe_connect" => array('title' => __('Enable Stripe Connect', 'marketplace-stripe-gateway'), 'type' => 'checkbox', 'value' => 'Enable'), // Checkbox
                        "test_client_id" => array('title' => __('Test client id', 'marketplace-stripe-gateway'), 'type' => 'text', 'id' => 'test_client_id', 'label_for' => 'test_client_id', 'name' => 'test_client_id', 'hints' => __('Get your development Client id from your stripe account', 'marketplace-stripe-gateway'), 'placeholder' => __('Development client id', 'marketplace-stripe-gateway')),
                        "live_client_id" => array('title' => __('Live client id', 'marketplace-stripe-gateway'), 'type' => 'text', 'id' => 'live_client_id', 'label_for' => 'live_client_id', 'name' => 'live_client_id', 'hints' => __('Get your production Client id from your stripe account', 'marketplace-stripe-gateway'), 'placeholder' => __('Production client id', 'marketplace-stripe-gateway')),
                    ),
                )
            )
        );

        $WCMp->admin->settings->settings_field_withsubtab_init(apply_filters("settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_payment_stripe_gateway_settings_sanitize($input) {
        global $WCMp_Stripe_Gateway;
        $new_input = array();

        $hasError = false;

        if (isset($input['is_enable_stripe_connect'])) {
            $new_input['is_enable_stripe_connect'] = sanitize_text_field($input['is_enable_stripe_connect']);
        }
        
        if(isset($input['test_client_id'])){
            $new_input['test_client_id'] = sanitize_text_field($input['test_client_id']);
        }
        
        if(isset($input['live_client_id'])){
            $new_input['live_client_id'] = sanitize_text_field($input['live_client_id']);
        }

        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmp_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Stripe Gateway Settings Updated', 'marketplace-stripe-gateway'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }

    /**
     * Print the Section text
     */
    public function default_settings_section_info() {
        global $WCMp_Stripe_Gateway;
        printf(__('', 'marketplace-stripe-gateway'));
    }

    /**
     * Print the Section text
     */
    public function WCMp_Stripe_Gateway_store_policies_admin_details_section_info() {
        global $WCMp_Stripe_Gateway;
    }

}
