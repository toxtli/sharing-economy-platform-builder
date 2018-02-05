<?php
if (!function_exists('get_stripe_gateway_settings')) {

    function get_stripe_gateway_settings($name = '', $tab = '') {
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

if (!function_exists('get_wcmp_stripe_gateway_settings')) {

    function get_wcmp_stripe_gateway_settings($name = '', $tab = '', $subtab = '') {
        if (empty($tab) && empty($name))
            return '';
        if (empty($tab))
            return get_option($name);
        if (empty($name))
            return get_option("dc_{$tab}_settings_name");
        if (!empty($subtab)) {
            $settings = get_option("wcmp_{$tab}_{$subtab}_settings_name");
        } else {
            $settings = get_option("dc_{$tab}_settings_name");
        }
        if (!isset($settings[$name]))
            return '';
        return $settings[$name];
    }

}

if (!function_exists('woocommerce_inactive_notice_stripe')) {

    function woocommerce_inactive_notice_stripe() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__('%sMarketplace Stripe Gateway is inactive.%s The %sWooCommerce plugin%s must be active for the Marketplace Stripe Gateway to work. Please %sinstall & activate WooCommerce%s', 'saved-cards'), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url('plugin-install.php?tab=search&s=woocommerce') . '">', '&nbsp;&raquo;</a>'); ?></p>
        </div>
        <?php
    }

}

if (!function_exists('wcmp_inactive_notice_stripe')) {

    function wcmp_inactive_notice_stripe() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__('%sMarketplace Stripe Gateway is inactive.%s The %sWC-Marketplace plugin%s must be active for the Marketplace Stripe Gateway to work. Please %sinstall & activate WC-Marketplace%s', 'saved-cards'), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/dc-woocommerce-multi-vendor/">', '</a>', '<a href="' . admin_url('plugin-install.php?tab=search&s=dc-woocommerce-multi-vendor') . '">', '&nbsp;&raquo;</a>'); ?></p>
        </div>
        <?php
    }

}

if (!function_exists('wc_stripe_inactive_notice_stripe')) {

    function wc_stripe_inactive_notice_stripe() {
        ?>
        <div id="message" class="error">
            <p><?php printf(__('%sMarketplace Stripe Gateway is inactive.%s The %sWooCommerce Stripe Gateway plugin%s must be active for the Marketplace Stripe Gateway to work. Please %sinstall & activate WooCommerce Stripe Gateway%s', 'saved-cards'), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce-gateway-stripe/">', '</a>', '<a href="' . admin_url('plugin-install.php?tab=search&s=woocommerce-gateway-stripe') . '">', '&nbsp;&raquo;</a>'); ?></p>
        </div>
        <?php
    }

}



/**
 * Get commission id from order id
 *
 */
if (!function_exists('get_commission_id')) {

    function get_commission_id($order_id) {
        global $wpdb;
        $commission_ids = array();

        $args = array(
            'post_type' => 'dc_commission',
            'post_status' => array('publish', 'private'),
            'posts_per_page' => -1,
            'order' => 'asc',
            'meta_key' => '_commission_order_id',
            'meta_value' => $order_id
        );

        $commissions = get_posts($args);

        foreach ($commissions as $commission) {
            $commission_ids[] = $commission->ID;
        }
        return $commission_ids;
    }

}

/**
 * Write to log file
 */
if (!function_exists('doWooStripeLOG')) {

    function doWooStripeLOG($str) {
        //global $DC_Woo_Api_Integration;
        $file = plugin_dir_path(__FILE__) . 'woo_api.log';
        if (file_exists($file)) {
            // Open the file to get existing content
            $current = file_get_contents($file);
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
?>
