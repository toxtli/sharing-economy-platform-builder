<?php

if (!defined('ABSPATH')) {
    exit;
}

class wf_dhl_woocommerce_shipping_method extends WC_Shipping_Method {

    private $found_rates;
    private $services;

    public function __construct() {
        $this->id = WF_DHL_ID;
        $this->method_title = __('DHL Express', 'wf-shipping-dhl');
        $this->method_description = '';
        $this->services = include( 'data-wf-service-codes.php' );
        $this->init();  
    }
    private function init() {
       
        include_once('data-wf-default-values.php');
        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables
        $this->enabled         = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'no';
        $this->title           = $this->get_option('title', $this->method_title);
        $this->availability    = isset( $this->settings['availability'] ) ? $this->settings['availability'] : 'all';
        $this->countries       = isset( $this->settings['countries'] ) ? $this->settings['countries'] : array();
        $this->origin          = apply_filters('woocommerce_dhl_origin_postal_code', str_replace(' ', '', strtoupper($this->get_option('origin'))));
        $selected_country = isset($this->settings['base_country']) ? $this->settings['base_country'] : WC()->countries->get_base_country();
        $this->origin_country  = apply_filters('woocommerce_dhl_origin_country_code', $selected_country);
        $this->account_number  = $this->get_option('account_number');
        $this->site_id         = $this->get_option('site_id');
        $this->site_password   = $this->get_option('site_password');
        $this->show_dhl_extra_charges = $this->get_option('show_dhl_extra_charges');
        $this->freight_shipper_city = $this->get_option('freight_shipper_city');
        $del_bool         =  $this->get_option( 'delivery_time' );
        $this->delivery_time   = ($del_bool == 'yes') ? true : false;

        $_stagingUrl           = 'https://xmlpitest-ea.dhl.com/XMLShippingServlet';
        $_productionUrl        = 'https://xmlpi-ea.dhl.com/XMLShippingServlet';

        $this->production      = (!empty($this->settings['production']) && $this->settings['production']  === 'yes') ? true : false;
        $this->service_url     = ($this->production == true) ? $_productionUrl : $_stagingUrl;

        $debug_bool             = $this->get_option('debug');
        $this->debug            = ($debug_bool  == 'yes') ? true : false;
        $isurance_bool          = $this->get_option('insure_contents');
        $this->insure_contents =  ($isurance_bool == 'yes') ? true : false;
        
        $this->request_type    = $this->get_option('request_type', 'LIST');
        $this->packing_method  = $this->get_option('packing_method', 'per_item');
        $this->boxes           = $this->get_option('boxes');
        $this->custom_services = $this->get_option('services', array());
        $this->offer_rates     = $this->get_option('offer_rates', 'all');

        $this->dutypayment_type = $this->get_option('dutypayment_type', '');
        $this->dutyaccount_number = $this->get_option('dutyaccount_number', '');

        $this->dimension_unit  = $this->get_option('dimension_weight_unit') == 'LBS_IN' ? 'IN' : 'CM';
        $this->weight_unit     = $this->get_option('dimension_weight_unit') == 'LBS_IN' ? 'LBS' : 'KG';

        $this->quoteapi_dimension_unit = $this->dimension_unit;
        $this->quoteapi_weight_unit = $this->weight_unit == 'LBS' ? 'LB' : 'KG';
        
        $this->conversion_rate = !empty($this->settings['conversion_rate']) ? $this->settings['conversion_rate'] : '';
        
        $this->conversion_rate = apply_filters('wf_dhl_conversion_rate',    $this->conversion_rate, $this->settings['dhl_currency_type']);
        
        //Time zone adjustment, which was configured in minutes to avoid time diff with server. Convert that in seconds to apply in date() functions.
        $this->timezone_offset = !empty($this->settings['timezone_offset']) ? intval($this->settings['timezone_offset']) * 60 : 0;
        
        if(class_exists('wf_vendor_addon_setup'))
		{
			if(isset($this->settings['vendor_check']) && $this->settings['vendor_check'] === 'yes')
			{
				$this->ship_from_address	=   'vendor_address'; 
			}
			else
			{
				$this->ship_from_address	=   'origin_address';
			}
		}else
		{
			$this->ship_from_address	=   'origin_address';
		}
		
        $this->weight_packing_process = !empty($this->settings['weight_packing_process']) ? $this->settings['weight_packing_process'] : 'pack_descending';
        $this->box_max_weight         = !empty($this->settings['box_max_weight']) ? $this->settings['box_max_weight'] : '';

        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }

    /**
     * is_available function.
     *
     * @param array $package
     * @return bool
     */
    public function is_available( $package ) {
        if ( "no" === $this->enabled || empty($this->enabled ) ) {
            return false;
        }

        if ( 'specific' === $this->availability ) {
            if ( is_array( $this->countries ) && ! in_array( $package['destination']['country'], $this->countries ) ) {
                return false;
            }
        } elseif ( 'excluding' === $this->availability ) {
            if ( is_array( $this->countries ) && ( in_array( $package['destination']['country'], $this->countries ) || ! $package['destination']['country'] ) ) {
                return false;
            }
        }
        return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package );
    }

    public function debug($message, $type = 'notice') {
        if ($this->debug) {
            wc_add_notice($message, $type);
        }
    }

    public function admin_options() {
       
        // Show settings
        parent::admin_options();
    }

    public function init_form_fields() {
		if(isset($_GET['page']) && $_GET['page'] === 'wc-settings')
		{
			$this->form_fields = include( 'data-wf-settings.php' );
		}
    }

    public function generate_activate_box_html() {
        // ob_start();
        // $plugin_name = 'dhl';
        // include( WF_DHL_PAKET_EXPRESS_ROOT_PATH . 'wf_api_manager/html/html-wf-activation-window.php' );
        // return ob_get_clean();
    }
    public function generate_wf_dhl_tab_box_html() {

        $tab = (!empty($_GET['subtab'])) ? esc_attr($_GET['subtab']) : 'general';

                echo '
                <div class="wrap">
                    <style>
                        .woocommerce-help-tip{color:darkgray !important;}
                        .woocommerce-save-button{display:none !important;}
                        <style>
                        .woocommerce-help-tip {
                            position: relative;
                            display: inline-block;
                            border-bottom: 1px dotted black;
                        }

                        .woocommerce-help-tip .tooltiptext {
                            visibility: hidden;
                            width: 120px;
                            background-color: black;
                            color: #fff;
                            text-align: center;
                            border-radius: 6px;
                            padding: 5px 0;

                            /* Position the tooltip */
                            position: absolute;
                            z-index: 1;
                        }

                        .woocommerce-help-tip:hover .tooltiptext {
                            visibility: visible;
                        }
                        </style>
                    </style>
                    <hr class="wp-header-end">';
                $this->wf_dhl_shipping_page_tabs($tab);
                switch ($tab) {
                    case "general":
                        echo '<div class="table-box table-box-main" id="general_section" style="margin-top: 0px;
    border: 1px solid #ccc;border-top: unset !important;padding: 5px;">';
                        require_once('settings/dhl_general_settings.php');
                        echo '</div>';
                        break;
                    case "rates":
                        echo '<div class="table-box table-box-main" id="rates_section" style="margin-top: 0px;
    border: 1px solid #ccc;border-top: unset !important;padding: 5px;">';
                        require_once('settings/dhl_rates_settings.php');
                        echo '</div>';
                        break;
                    case "labels":
                        echo '<div class="table-box table-box-main" id="labels_section" style="margin-top: 0px;
    border: 1px solid #ccc;border-top: unset !important;padding: 5px;">';
                        require_once('settings/dhl_labels_settings.php');
                        echo '</div>';
                        break;
                    case "packing":
                        echo '<div class="table-box table-box-main" id="packing_section" style="margin-top: 0px;
    border: 1px solid #ccc;border-top: unset !important;padding: 5px;">';
                        require_once('settings/dhl_packing_settings.php');
                        echo '</div>';
                        break;
                    case "licence":
                        echo '<div class="table-box table-box-main" id="licence_section" style="margin-top: 0px;
    border: 1px solid #ccc;border-top: unset !important;padding: 5px;">';
                                    $plugin_name = 'dhl';
                                    include( WF_DHL_PAKET_EXPRESS_ROOT_PATH . 'wf_api_manager/html/html-wf-activation-window.php' );
                        echo '</div>';
                        break;
                }
                echo '
                </div>';


       }
       public function wf_dhl_shipping_page_tabs($current = 'general')
       {
        $activation_check = get_option('dhl_activation_status');
        if(!empty($activation_check) && $activation_check === 'active')
        {
            $acivated_tab_html =  "<small style='color:green;font-size:xx-small;'>(Activated)</small>";
            
        }
        else
        {
            $acivated_tab_html =  "<small style='color:red;font-size:xx-small;'>(Activate)</small>";
        }
        $tabs = array(
                    'general' => __("General", 'wf-shipping-dhl'),
                    'rates' => __("Rates & Services", 'wf-shipping-dhl'),
                    'labels' => __("Label & Tracking", 'wf-shipping-dhl'),
                    'packing' => __("Packaging", 'wf-shipping-dhl'),
                    'licence' => __("License ".$acivated_tab_html, 'wf-shipping-dhl')
                );
                $html = '<h2 class="nav-tab-wrapper">';
                foreach ($tabs as $tab => $name) {
                    $class = ($tab == $current) ? 'nav-tab-active' : '';
                    $style = ($tab == $current) ? 'border-bottom: 1px solid transparent !important;' : '';
                    $html .= '<a style="text-decoration:none !important;' . $style . '" class="nav-tab ' . $class . '" href="?page='.wf_get_settings_url().'&tab=shipping&section=wf_dhl_shipping&subtab=' . $tab . '">' . $name . '</a>';
                }
                $html .= '</h2>';
                echo $html;
            }
    

    public function generate_services_html() {
        ob_start();
        include( 'html-wf-services.php' );
        return ob_get_clean();
    }

    public function generate_box_packing_html() {
        ob_start();
        include( 'html-wf-box-packing.php' );
        return ob_get_clean();
    }

    public function validate_box_packing_field($key) {
        $boxes_id           = isset($_POST['boxes_id']) ? $_POST['boxes_id'] : array();
        $boxes_name         = isset($_POST['boxes_name']) ? $_POST['boxes_name'] : array();
        
        $boxes_length       = isset($_POST['boxes_length']) ? $_POST['boxes_length'] : array();
        $boxes_width        = isset($_POST['boxes_width']) ? $_POST['boxes_width'] : array();
        $boxes_height       = isset($_POST['boxes_height']) ? $_POST['boxes_height'] : array();
        $boxes_inner_length = isset($_POST['boxes_inner_length']) ? $_POST['boxes_inner_length'] : array();
        $boxes_inner_width  = isset($_POST['boxes_inner_width']) ? $_POST['boxes_inner_width'] : array();
        $boxes_inner_height = isset($_POST['boxes_inner_height']) ? $_POST['boxes_inner_height'] : array();
        
        $boxes_box_weight   = isset($_POST['boxes_box_weight']) ? $_POST['boxes_box_weight'] : array();
        $boxes_max_weight   = isset($_POST['boxes_max_weight']) ? $_POST['boxes_max_weight'] : array();
        $boxes_enabled      = isset($_POST['boxes_enabled']) ? $_POST['boxes_enabled'] : array();
        $boxes_pack_type    = isset($_POST['boxes_pack_type']) ? $_POST['boxes_pack_type'] : array();

        $boxes = array();

        if (!empty($boxes_length) && sizeof($boxes_length) > 0) {
            for ($i = 0; $i <= max(array_keys($boxes_length)); $i ++) {

                if (!isset($boxes_length[$i]))
                    continue;

                if ($boxes_length[$i] && $boxes_width[$i] && $boxes_height[$i]) {

                    $boxes[] = array(
                        'id' => $boxes_id[$i],
                        'name' => $boxes_name[$i],
                        'length' => floatval($boxes_length[$i]),
                        'width' => floatval($boxes_width[$i]),
                        'height' => floatval($boxes_height[$i]),
                        'inner_length' => floatval($boxes_inner_length[$i]),
                        'inner_width' => floatval($boxes_inner_width[$i]),
                        'inner_height' => floatval($boxes_inner_height[$i]),
                        'box_weight' => floatval($boxes_box_weight[$i]),
                        'max_weight' => floatval($boxes_max_weight[$i]),
                        'enabled' => isset($boxes_enabled[$i]) ? true : false,
                        'pack_type' => $boxes_pack_type[$i]
                    );
                }
            }
        }
        return $boxes;
    }

    public function validate_services_field($key) {
        $services = array();
        $posted_services = $_POST['dhl_service'];

        foreach ($posted_services as $code => $settings) {
            $services[$code] = array(
                'name' => wc_clean($settings['name']),
                'order' => wc_clean($settings['order']),
                'enabled' => isset($settings['enabled']) ? true : false,
                'adjustment' => wc_clean($settings['adjustment']),
                'adjustment_percent' => str_replace('%', '', wc_clean($settings['adjustment_percent']))
            );
        }

        return $services;
    }

    public function get_dhl_packages($package) {
        switch ($this->packing_method) {
            case 'box_packing' :
                return $this->box_shipping($package);
                break;
            case 'weight_based' :
                return $this->weight_based_shipping($package);
                break;
            case 'per_item' :
            default :
                return $this->per_item_shipping($package);
                break;
        }
    }

    /**
     * weight_based_shipping function.
     *
     * @access private
     * @param mixed $package
     * @return void
    **/
    private function weight_based_shipping($package) {
        global $woocommerce;
        if ( ! class_exists( 'WeightPack' ) ) {
            include_once 'weight_pack/class-wf-weight-packing.php';
        }
        $weight_pack=new WeightPack($this->weight_packing_process);
        $weight_pack->set_max_weight($this->box_max_weight);
        
        $package_total_weight = 0;
        $insured_value = 0;
        
        
        $ctr = 0;
        foreach ($package['contents'] as $item_id => $values) {
            $ctr++;
            
            $skip_product = apply_filters('wf_shipping_skip_product_from_dhl_label',false, $values, $package['contents']);
            if($skip_product){
                continue;
            }
            
            if (!($values['quantity'] > 0 && $values['data']->needs_shipping())) {
                $this->debug(sprintf(__('Product #%d is virtual. Skipping.', 'wf-shipping-dhl'), $ctr));
                continue;
            }

            if (!$values['data']->get_weight()) {
                $this->debug(sprintf(__('Product #%d is missing weight.', 'wf-shipping-dhl'), $ctr), 'error');
                return;
            }
            $weight_pack->add_item(wc_get_weight( $values['data']->get_weight(), $this->weight_unit ), $values['data'], $values['quantity']);
        }
        
        $pack   =   $weight_pack->pack_items();  
        $errors =   $pack->get_errors();
        if( !empty($errors) ){
            //do nothing
            return;
        } else {
            $boxes    =   $pack->get_packed_boxes();
            $unpacked_items =   $pack->get_unpacked_items();
            
            $insured_value        =   0;
            
            $packages      =   array_merge( $boxes, $unpacked_items ); // merge items if unpacked are allowed
            $package_count  =   sizeof($packages);
            // get all items to pass if item info in box is not distinguished
            $packable_items =   $weight_pack->get_packable_items();
            $all_items    =   array();
            if(is_array($packable_items)){
                foreach($packable_items as $packable_item){
                    $all_items[]    =   $packable_item['data'];
                }
            }
            //pre($packable_items);
            $order_total = '';
            if(isset($this->order)){
                $order_total    =   $this->order->get_total();
            }
            
            $to_ship  = array();
            $group_id = 1;
            foreach($packages as $package){//pre($package);
            
                $packed_products = array();
                if(($package_count  ==  1) && isset($order_total)){
                    $insured_value  =   $order_total;
                }else{
                    $insured_value  =   0;
                    if(!empty($package['items'])){
                        foreach($package['items'] as $item){                        
                            $insured_value        =   $insured_value+$item->get_price();
                            
                        }
                    }else{
                        if( isset($order_total) && $package_count){
                            $insured_value  =   $order_total/$package_count;
                        }
                    }
                }
                $packed_products    =   isset($package['items']) ? $package['items'] : $all_items;
                // Creating package request
                $package_total_weight   =   $package['weight'];
                
                $insurance_array = array(
                    'Amount' => round($values['data']->get_price()),
                    'Currency' => get_woocommerce_currency()
                );
                if ($this->settings['insure_contents'] == 'yes' && !empty($this->conversion_rate)) {
                    $crate = 1 / $this->conversion_rate;
                    $insurance_array['Amount']      = round($values['data']->get_price() * $crate, 2);
                    $insurance_array['Currency']    = $this->settings['dhl_currency_type'];
                }
                $group = array(
                    'GroupNumber' => $group_id,
                    'GroupPackageCount' => 1,
                    'Weight' => array(
                        'Value' => round($package_total_weight, 3),
                        'Units' => $this->weight_unit
                    ),
                    'packed_products' => $packed_products,
                );
                $group['InsuredValue'] = $insurance_array;
                $group['packtype'] = isset($this->settings['shp_pack_type'])?$this->settings['shp_pack_type'] : 'OD';
                
                $to_ship[] = $group;
                $group_id++;
            }
        }
        return $to_ship;
    }


    private function per_item_shipping($package) {
        $to_ship = array();
        $group_id = 1;

        // Get weight of order
        foreach ($package['contents'] as $item_id => $values) {

            if (!$values['data']->needs_shipping()) {
                $this->debug(sprintf(__('Product # is virtual. Skipping.', 'wf-shipping-dhl'), $item_id), 'error');
                continue;
            }

            $skip_product = apply_filters('wf_shipping_skip_product_from_dhl_rate',false, $values, $package['contents']);
            if($skip_product){
                continue;
            }

            if (!$values['data']->get_weight()) {
                $this->debug(sprintf(__('Product # is missing weight. Aborting.', 'wf-shipping-dhl'), $item_id), 'error');
                return;
            }

            $group = array();
            $insurance_array = array(
                'Amount' => round($values['data']->get_price()),
                'Currency' => get_woocommerce_currency()
            );
            if ($this->settings['insure_contents'] == 'yes' && !empty($this->conversion_rate)) {
                $crate = 1 / $this->conversion_rate;
                $insurance_array['Amount'] = round($values['data']->get_price() * $crate , 2);
                $insurance_array['Currency'] = $this->settings['dhl_currency_type'];
            }
            $group = array(
                'GroupNumber' => $group_id,
                'GroupPackageCount' => 1,
                'Weight' => array(
                    'Value' => round(wc_get_weight($values['data']->get_weight(), $this->weight_unit), 3),
                    'Units' => $this->weight_unit
                ),
                'packed_products' => array($values['data'])
            );

            if ( wf_get_product_length( $values['data'] ) && wf_get_product_height( $values['data'] ) && wf_get_product_width( $values['data'] )) {

                $dimensions = array( wf_get_product_length( $values['data'] ), wf_get_product_width( $values['data'] ), wf_get_product_height( $values['data'] ));

                sort($dimensions);

                $group['Dimensions'] = array(
                    'Length' => max(1, round(wc_get_dimension($dimensions[2], $this->dimension_unit), 0)),
                    'Width' => max(1, round(wc_get_dimension($dimensions[1], $this->dimension_unit), 0)),
                    'Height' => max(1, round(wc_get_dimension($dimensions[0], $this->dimension_unit), 0)),
                    'Units' => $this->dimension_unit
                );
            }
            $group['packtype'] = isset($this->settings['shp_pack_type'])?$this->settings['shp_pack_type'] : 'BOX';
            $group['InsuredValue'] = $insurance_array;

            for ($i = 0; $i < $values['quantity']; $i++)
                $to_ship[] = $group;

            $group_id++;
        }

        return $to_ship;
    }

    private function box_shipping($package) {
        if (!class_exists('WF_Boxpack')) {
            include_once 'class-wf-packing.php';
        }

        $boxpack = new WF_Boxpack();

        // Define boxes
        foreach ($this->boxes as $key => $box) {
            if (!$box['enabled']) {
                continue;
            }
			$box['pack_type'] = !empty($box['pack_type']) ? $box['pack_type'] : 'BOX' ;
			
            $newbox = $boxpack->add_box($box['length'], $box['width'], $box['height'], $box['box_weight'], $box['pack_type']);

            if (isset($box['id'])) {
                $newbox->set_id(current(explode(':', $box['id'])));
            }

            if ($box['max_weight']) {
                $newbox->set_max_weight($box['max_weight']);
            }
            if ($box['pack_type']) {
                $newbox->set_packtype($box['pack_type']);
            }
        }

        // Add items
        foreach ($package['contents'] as $item_id => $values) {

            if (!$values['data']->needs_shipping()) {
                $this->debug(sprintf(__('Product # is virtual. Skipping.', 'wf-shipping-dhl'), $item_id), 'error');
                continue;
            }

            $skip_product = apply_filters('wf_shipping_skip_product_from_dhl_rate',false, $values, $package['contents']);
            if($skip_product){
                continue;
            }

            if ( wf_get_product_length( $values['data'] ) && wf_get_product_height( $values['data'] ) && wf_get_product_width( $values['data'] ) && wf_get_product_weight( $values['data'] )) {

                $dimensions = array( wf_get_product_length( $values['data'] ), wf_get_product_height( $values['data'] ), wf_get_product_width( $values['data'] ));

                for ($i = 0; $i < $values['quantity']; $i ++) {
                    $boxpack->add_item(
                            wc_get_dimension($dimensions[2], $this->dimension_unit), wc_get_dimension($dimensions[1], $this->dimension_unit), wc_get_dimension($dimensions[0], $this->dimension_unit), wc_get_weight($values['data']->get_weight(), $this->weight_unit), $values['data']->get_price(), array(
                        'data' => $values['data']
                            )
                    );
                }
            } else {
                $this->debug(sprintf(__('Product #%s is missing dimensions. Aborting.', 'wf-shipping-dhl'), $item_id), 'error');
                return;
            }
        }

        // Pack it
        $boxpack->pack();
        $packages = $boxpack->get_packages();
        $to_ship = array();
        $group_id = 1;

        foreach ($packages as $package) {
            if ($package->unpacked === true) {
                $this->debug('Unpacked Item');
            } else {
                $this->debug('Packed ' . $package->id);
            }

            $dimensions = array($package->length, $package->width, $package->height);

            sort($dimensions);
            $insurance_array = array(
                'Amount' => round($package->value),
                'Currency' => get_woocommerce_currency()
            );
            if ($this->settings['insure_contents'] == 'yes' && !empty($this->conversion_rate)) {
                $crate = 1 / $this->conversion_rate;
                $insurance_array['Amount'] = round($package->value * $crate  , 2);
                $insurance_array['Currency'] = $this->settings['dhl_currency_type'];
            }
            
            $group = array(
                'GroupNumber' => $group_id,
                'GroupPackageCount' => 1,
                'Weight' => array(
                    'Value' => round($package->weight, 3),
                    'Units' => $this->weight_unit
                ),
                'Dimensions' => array(
                    'Length' => max(1, round($dimensions[2], 0)),
                    'Width' => max(1, round($dimensions[1], 0)),
                    'Height' => max(1, round($dimensions[0], 0)),
                    'Units' => $this->dimension_unit
                ),
                'InsuredValue' => $insurance_array,
                'packed_products' => array(),
                'package_id' => $package->id,
                'packtype' => isset($package->packtype)?$package->packtype:'BOX'
            );

            if (!empty($package->packed) && is_array($package->packed)) {
                foreach ($package->packed as $packed) {
                    $group['packed_products'][] = $packed->get_meta('data');
                }
            }

            $to_ship[] = $group;

            $group_id++;
        }

        return $to_ship;
    }

    private function get_dhl_requests($dhl_packages, $package) {
        // Time is modified to avoid date diff with server.
        $mailing_date = date('Y-m-d', time() + $this->timezone_offset);
        $mailing_datetime = date('Y-m-d', time() + $this->timezone_offset) . 'T' . date('H:i:s', time() + $this->timezone_offset);
        $destination_postcode = str_replace(' ', '', strtoupper($package['destination']['postcode']));
        $pieces = $this->wf_get_package_piece($dhl_packages);
        $fetch_accountrates = $this->request_type == "ACCOUNT" ? "<PaymentAccountNumber>" . $this->account_number . "</PaymentAccountNumber>" : "";

        $total_value = $this->wf_get_package_total_value($dhl_packages);
        if ($this->settings['insure_contents'] == 'yes' && !empty($this->conversion_rate))
            $currency = $this->settings['dhl_currency_type'];
        else $currency = get_woocommerce_currency();

        $insurance_details = $this->insure_contents ? "<InsuredValue>{$total_value}</InsuredValue><InsuredCurrency>{$currency}</InsuredCurrency>" : "";
        $additional_insurance_details = ($this->insure_contents && $this->conversion_rate) ? "<QtdShp><QtdShpExChrg><SpecialServiceType>II</SpecialServiceType><LocalSpecialServiceType>XCH</LocalSpecialServiceType></QtdShpExChrg></QtdShp>" : ""; //insurance type
        
        //If vendor country set, then use vendor address
        if(isset($package['origin'])){
            if(isset($package['origin']['country'])){
                $this->origin_country =   $package['origin']['country'];
                $this->origin         =   $package['origin']['postcode'];
            }
        }        

        $is_dutiable = ($package['destination']['country'] == $this->origin_country || wf_dhl_is_eu_country($this->origin_country, $package['destination']['country'])) ? "N" : "Y";
        $dutiable_content = $is_dutiable == "Y" ? "<Dutiable><DeclaredCurrency>{$currency}</DeclaredCurrency><DeclaredValue>{$total_value}</DeclaredValue></Dutiable>" : "";

        $destination_city = strtoupper($package['destination']['city']);

        $origin_postcode_city = $this->wf_get_postcode_city($this->origin_country, $this->freight_shipper_city, $this->origin);
        $destination_postcode_city = $this->wf_get_postcode_city($package['destination']['country'], $destination_city, $destination_postcode);

        $paymentCountryCode = $this->origin_country;
       /* if( $this->dutypayment_type == 'R' ){
            $paymentCountryCode = $package['destination']['country'] ;
        }*/


$xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<p:DCTRequest xmlns:p="http://www.dhl.com" xmlns:p1="http://www.dhl.com/datatypes" xmlns:p2="http://www.dhl.com/DCTRequestdatatypes" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.dhl.com DCT-req.xsd ">
  <GetQuote>
    <Request>
		<ServiceHeader>
			<MessageTime>{$mailing_datetime}</MessageTime>
			<MessageReference>1234567890123456789012345678901</MessageReference>
			<SiteID>{$this->site_id}</SiteID>
			<Password>{$this->site_password}</Password>
		</ServiceHeader>
    </Request>
    <From>
	  <CountryCode>{$this->origin_country}</CountryCode>
	  {$origin_postcode_city}
    </From>
    <BkgDetails>
      <PaymentCountryCode>{$paymentCountryCode}</PaymentCountryCode>
      <Date>{$mailing_date}</Date>
      <ReadyTime>PT10H21M</ReadyTime>
      <DimensionUnit>{$this->quoteapi_dimension_unit}</DimensionUnit>
      <WeightUnit>{$this->quoteapi_weight_unit}</WeightUnit>
      <Pieces>
		{$pieces}
	  </Pieces>
	  {$fetch_accountrates}
	  <IsDutiable>{$is_dutiable}</IsDutiable>
	  <NetworkTypeCode>AL</NetworkTypeCode>
          {$additional_insurance_details}
	  {$insurance_details}
	  </BkgDetails>
    <To>
      <CountryCode>{$package['destination']['country']}</CountryCode>
	  {$destination_postcode_city}
    </To>
	{$dutiable_content}
  </GetQuote>
</p:DCTRequest>
XML;
        $xmlRequest = apply_filters('wf_dhl_rate_request', $xmlRequest, $package);
        return $xmlRequest;
    }

    private function wf_get_package_piece($dhl_packages) {
        $pieces = "";
        if ($dhl_packages) {
            foreach ($dhl_packages as $key => $parcel) {
                $pack_type = $this->wf_get_pack_type($parcel['packtype']);
                $index = $key + 1;
                $pieces .= '<Piece><PieceID>' . $index . '</PieceID>';
                $pieces .= '<PackageTypeCode>'.$pack_type.'</PackageTypeCode>';
                if( !empty($parcel['Dimensions']['Height']) && !empty($parcel['Dimensions']['Length']) && !empty($parcel['Dimensions']['Width']) ){
                    $pieces .= '<Height>' . $parcel['Dimensions']['Height'] . '</Height>';
                    $pieces .= '<Depth>' . $parcel['Dimensions']['Length'] . '</Depth>';
                    $pieces .= '<Width>' . $parcel['Dimensions']['Width'] . '</Width>';
                }
                $pieces .= '<Weight>' . $parcel['Weight']['Value'] . '</Weight></Piece>';
            }
        }
        return $pieces;
    }

    private function wf_get_postcode_city($country, $city, $postcode) {
        $no_postcode_country = array('AE', 'AF', 'AG', 'AI', 'AL', 'AN', 'AO', 'AW', 'BB', 'BF', 'BH', 'BI', 'BJ', 'BM', 'BO', 'BS', 'BT', 'BW', 'BZ', 'CD', 'CF', 'CG', 'CI', 'CK',
            'CL', 'CM', 'CO', 'CR', 'CV', 'DJ', 'DM', 'DO', 'EC', 'EG', 'ER', 'ET', 'FJ', 'FK', 'GA', 'GD', 'GH', 'GI', 'GM', 'GN', 'GQ', 'GT', 'GW', 'GY', 'HK', 'HN', 'HT', 'IE', 'IQ', 'IR',
            'JM', 'JO', 'KE', 'KH', 'KI', 'KM', 'KN', 'KP', 'KW', 'KY', 'LA', 'LB', 'LC', 'LK', 'LR', 'LS', 'LY', 'ML', 'MM', 'MO', 'MR', 'MS', 'MT', 'MU', 'MW', 'MZ', 'NA', 'NE', 'NG', 'NI',
            'NP', 'NR', 'NU', 'OM', 'PA', 'PE', 'PF', 'PY', 'QA', 'RW', 'SA', 'SB', 'SC', 'SD', 'SL', 'SN', 'SO', 'SR', 'SS', 'ST', 'SV', 'SY', 'TC', 'TD', 'TG', 'TL', 'TO', 'TT', 'TV', 'TZ',
            'UG', 'UY', 'VC', 'VE', 'VG', 'VN', 'VU', 'WS', 'XA', 'XB', 'XC', 'XE', 'XL', 'XM', 'XN', 'XS', 'YE', 'ZM', 'ZW');

        $postcode_city = !in_array( $country, $no_postcode_country ) ? $postcode_city = "<Postalcode>{$postcode}</Postalcode>" : '';
        if( !empty($city) ){
            $postcode_city .= "<City>{$city}</City>";
        }
        return $postcode_city;
    }

    private function wf_get_package_total_value($dhl_packages) {
        $total_value = 0;
        if ($dhl_packages) {
            foreach ($dhl_packages as $key => $parcel) {
                $total_value += $parcel['InsuredValue']['Amount'] * $parcel['GroupPackageCount'];
            }
        }
        return $total_value;
    }

    public function calculate_shipping( $package=array() ) {
        // Clear rates
        $this->found_rates = array();

        // Debugging
        $this->debug(__('dhl debug mode is on - to hide these messages, turn debug mode off in the settings.', 'wf-shipping-dhl'));

		// Packages returned ahould be an array regardless of filter added or not 
		$packages = apply_filters('wf_filter_package_address', array($package) , $this->ship_from_address);
        // Get requests
        
		$dhl_requests	=	array();
		
		foreach($packages as $package){
			$dhl_packs		=	$this->get_dhl_packages( $package );
			$dhl_reqs		=	$this->get_dhl_requests( $dhl_packs, $package );	
			$dhl_requests[]	=	$dhl_reqs;
		}

        if ($dhl_requests) {
            $this->run_package_request($dhl_requests);
        }


        // Ensure rates were found for all packages
        $packages_to_quote_count = sizeof($dhl_requests);
        
        if ($this->found_rates) {
            foreach ($this->found_rates as $key => $value) {
                if ($value['packages'] < $packages_to_quote_count) {
                    unset($this->found_rates[$key]);
                }
            }
        }
        // Rate conversion
        if ($this->conversion_rate) {
            foreach ($this->found_rates as $key => $rate) {
                $this->found_rates[$key]['cost'] = $rate['cost'] * $this->conversion_rate;
            }
        }
        $this->add_found_rates();
       
    }

    public function run_package_request($requests) {
        try {
			foreach ( $requests as $key => $request ) {
				$this->process_result($this->get_result($request), $request);
			}            
        } catch (Exception $e) {
            $this->debug(print_r($e, true), 'error');
            return false;
        }
    }

    private function get_result($request) {
        $this->debug('DHL REQUEST: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r(htmlspecialchars($request), true) . '</pre>');

        $result = wp_remote_post($this->service_url, array(
            'method' => 'POST',
            'timeout' => 70,
            'sslverify' => 0,
            //'headers'          => $this->wf_get_request_header('application/vnd.cpc.shipment-v7+xml','application/vnd.cpc.shipment-v7+xml'),
            'body' => $request
                )
        );

        wc_enqueue_js("
			jQuery('a.debug_reveal').on('click', function(){
				jQuery(this).closest('div').find('.debug_info').slideDown();
				jQuery(this).remove();
				return false;
			});
			jQuery('pre.debug_info').hide();
		");

        if ( is_wp_error( $result ) ) {
            $error_message = $result->get_error_message();
            $this->debug('DHL WP ERROR: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r(htmlspecialchars($error_message), true) . '</pre>');
        }
        elseif (is_array($result) && !empty($result['body'])) {
            $result = $result['body'];
        } else {
            $result = '';
        }

        $this->debug('DHL RESPONSE: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r(htmlspecialchars($result), true) . '</pre>');
        
        libxml_use_internal_errors(true);
        if(!empty($result))
        {
            $xml = simplexml_load_string(utf8_encode($result));
        }
        if ($xml) {
            return $xml;
        } else {
            return null;
        }
    }

    private function wf_get_cost_based_on_currency($qtdsinadcur, $default_charge) {
        if (!empty($qtdsinadcur)) {
            foreach ($qtdsinadcur as $multiple_currencies) {
                if ((string) $multiple_currencies->CurrencyCode == get_woocommerce_currency() && !empty($multiple_currencies->TotalAmount))
                    return $multiple_currencies->TotalAmount;
            }
        }
        return $default_charge;
    }

    private function process_result($result = '') {
        $processed_ratecode = array();
        $rate_compain = '';
        $rate_local_code ='';
        if ($result && !empty($result->GetQuoteResponse->BkgDetails->QtdShp)) {
            foreach ($result->GetQuoteResponse->BkgDetails->QtdShp as $quote) {
                $rate_code = strval((string) $quote->GlobalProductCode);
                $rate_local_code = strval((string) (isset($quote->LocalProductCode) ? $quote->LocalProductCode : ''));
                if (!in_array($rate_code,$processed_ratecode)) {
                    if ((string) $quote->CurrencyCode == get_woocommerce_currency()) {
                        $rate_cost = floatval((string) $quote->ShippingCharge);
                        $rate_compain = floatval((string) $quote->WeightCharge);
                    } else {
                        $rate_cost = floatval((string) $this->wf_get_cost_based_on_currency($quote->QtdSInAdCur, $quote->ShippingCharge));
                        $rate_compain = floatval((string) $this->wf_get_cost_based_on_currency($quote->QtdSInAdCur, $quote->WeightCharge));
                    }
                    $processed_ratecode[] = $rate_code;
                    $rate_id = $this->id . ':' . $rate_code.'|'.$rate_local_code;
                    
                    $delivery_time = new DateInterval($quote->DeliveryTime);
                    $delivery_time = $delivery_time->format('%h:%I');
                    $delivery_date_time = date("M-d", strtotime($quote->DeliveryDate)).' '.$delivery_time;
                    $rate_name = strval( (string) $quote->ProductShortName );
                    if($rate_cost > 0) $this->prepare_rate($rate_code, $rate_id, $rate_name, $rate_cost,$delivery_date_time,$rate_compain);
                }
            }
        }
    }

    private function prepare_rate($rate_code, $rate_id, $rate_name, $rate_cost, $delivery_time, $rate_compain = '0') {

        // Name adjustment
        if (!empty($this->custom_services[$rate_code]['name'])) {
            $rate_name = $this->custom_services[$rate_code]['name'];
        }

        // Cost adjustment %
        if (!empty($this->custom_services[$rate_code]['adjustment_percent'])) {
            $rate_cost = $rate_cost + ( $rate_cost * ( floatval($this->custom_services[$rate_code]['adjustment_percent']) / 100 ) );
        }
        // Cost adjustment
        if (!empty($this->custom_services[$rate_code]['adjustment'])) {
            $rate_cost = $rate_cost + floatval($this->custom_services[$rate_code]['adjustment']);
        }

        // Enabled check
        if (isset($this->custom_services[$rate_code]) && empty($this->custom_services[$rate_code]['enabled'])) {
            return;
        }

        // Merging
        if (isset($this->found_rates[$rate_id])) {
            $rate_cost = $rate_cost + $this->found_rates[$rate_id]['cost'];
            $packages = 1 + $this->found_rates[$rate_id]['packages'];
        } else {
            $packages = 1;
        }

        // Sort
        if (isset($this->custom_services[$rate_code]['order'])) {
            $sort = $this->custom_services[$rate_code]['order'];
        } else {
            $sort = 999;
        }
        
        $extra_charge = $rate_cost - $rate_compain;

        if ($this->conversion_rate) {
                $extra_charge = $extra_charge * $this->conversion_rate;
                $rate_compain = $rate_compain * $this->conversion_rate;
        }



        $this->found_rates[$rate_id] = array(
            'id' => $rate_id,
            'label' => $rate_name,
            'cost' => $rate_cost,
            'sort' => $sort,
            'packages' => $packages,
            'meta_data' => array('dhl_delivery_time'=>$delivery_time,'weight_charge'=>floatval($rate_compain),'extra_charge'=>$extra_charge)
        );
    }

    public function add_found_rates() {
        if ($this->found_rates) {

            if ($this->offer_rates == 'all') {

                uasort($this->found_rates, array($this, 'sort_rates'));

                foreach ($this->found_rates as $key => $rate) {
                    $this->add_rate($rate);
                }
            } else {
                $cheapest_rate = '';

                foreach ($this->found_rates as $key => $rate) {
                    if (!$cheapest_rate || $cheapest_rate['cost'] > $rate['cost']) {
                        $cheapest_rate = $rate;
                    }
                }

                $cheapest_rate['label'] = $this->title;

                $this->add_rate($cheapest_rate);
            }
        }
    }

    public function sort_rates($a, $b) {
        if ($a['sort'] == $b['sort'])
            return 0;
        return ( $a['sort'] < $b['sort'] ) ? -1 : 1;
    }
    private function wf_get_pack_type($selected) {
            $pack_type = 'BOX';
            if ($selected == 'FLY') {
                $pack_type = 'FLY';
            } 
        return $pack_type;    
    }

}
