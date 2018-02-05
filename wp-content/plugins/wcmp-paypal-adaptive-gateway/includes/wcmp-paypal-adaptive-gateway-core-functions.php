<?php
if (!function_exists('get_paypal_adaptive_gateway_settings')) {

    function get_paypal_adaptive_gateway_settings($name = '', $tab = '') {
        if (empty($tab) && empty($name))
            return '';
        if (empty($tab))
            return get_option($name);
        if (empty($name))
            return get_option("dc_{$tab}_settings_name");
        $settings = get_option("dc_{$tab}_settings_name");
        if (!isset($settings[$name]))
            return '';
        return $settings[$name];
    }

}

if (!function_exists('woocommerce_inactive_notice')) {

    function woocommerce_inactive_notice() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__('%sWCMp Paypal Adaptive Gateway is inactive.%s The %sWooCommerce plugin%s must be active for the WCMp Paypal Adaptive Gateway to work. Please %sinstall & activate WooCommerce%s', 'wcmp-paypal-adaptive-gateway'), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url('plugins.php') . '">', '&nbsp;&raquo;</a>'); ?></p>
        </div>
        <?php
    }

}

if (!function_exists('doPaypalAdaptiveLog')) {

    /**
     * Write to log file
     */
    function doPaypalAdaptiveLog($str) {
        global $WCMP_Paypal_Adaptive_Gateway;
        $file = $WCMP_Paypal_Adaptive_Gateway->plugin_path . 'log/wcmp-paypal-adaptive-gateway.log';
        if (file_exists($file)) {
            // Open the file to get existing content
            $current = file_get_contents($file);
            if ($current) {
                // Append a new content to the file
                $current .= "$str" . "\r\n";
                $current .= "-------------------------------------\r\n";
            } else {
                $current = "$str" . "\r\n";
                $current .= "-------------------------------------\r\n";
            }
            // Write the contents back to the file
            file_put_contents($file, $current);
        }
    }

}