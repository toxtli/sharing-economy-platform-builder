<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-shipping.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $woocommerce, $WCMp, $wpdb;

$vendor_user_id = get_current_vendor_id();
$vendor_data = get_wcmp_vendor($vendor_user_id);
if ($vendor_data) :

    $vendor_shipping_data = get_user_meta($vendor_user_id, 'vendor_shipping_data', true);
    ?>
    <form name="vendor_shipping_form" class="wcmp_shipping_form" method="post">
        <table class="shipping_table">
            <tbody>
                <?php
                if (version_compare(WC_VERSION, '2.6.0', '>=')) {
                    $shipping_class_id = get_user_meta($vendor_user_id, 'shipping_class_id', true);
                    if (!$shipping_class_id) {
                        $shipping_term = get_term_by('slug', $vendor_data->user_data->user_login . '-' . $vendor_user_id, 'product_shipping_class', ARRAY_A);
                        if (!$shipping_term) {
                            $shipping_term = wp_insert_term($vendor_data->user_data->user_login . '-' . $vendor_user_id, 'product_shipping_class');
                        }
                        if (!is_wp_error($shipping_term)) {
                            $shipping_term_id = $shipping_term['term_id'];
                            update_user_meta($vendor_user_id, 'shipping_class_id', $shipping_term['term_id']);
                            add_woocommerce_term_meta($shipping_term['term_id'], 'vendor_id', $vendor_user_id);
                            add_woocommerce_term_meta($shipping_term['term_id'], 'vendor_shipping_origin', get_option('woocommerce_default_country'));
                        }
                    }
                    $shipping_class_id = $shipping_term_id = get_user_meta($vendor_user_id, 'shipping_class_id', true);
                    $raw_zones = WC_Shipping_Zones::get_zones();
                    $raw_zones[] = array('id' => 0);
                    foreach ($raw_zones as $raw_zone) {
                        $zone = new WC_Shipping_Zone($raw_zone['id']);
                        $raw_methods = $zone->get_shipping_methods();
                        foreach ($raw_methods as $raw_method) {
                            if ($raw_method->id == 'flat_rate' && isset($raw_method->instance_form_fields["class_cost_" . $shipping_class_id])) {
                                $instance_field = $raw_method->instance_form_fields["class_cost_" . $shipping_class_id];
                                $instance_settings = $raw_method->instance_settings["class_cost_" . $shipping_class_id];
                                $option_name = 'woocommerce_' . $raw_method->id . "_" . $raw_method->instance_id . "_settings_class_cost_" . $shipping_class_id;
                                echo '<tr><td><h2>Shipping Zone : ' . $zone->get_zone_name() . '</h2></td></tr>';
                                ?>
                                <tr>
                                    <td>
                                        <label><?php echo $instance_field['title'] . ' - ' . $raw_method->title; ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input name="vendor_shipping_data[<?php echo $option_name; ?>]" type="text" class="no_input" readonly type="text" step="0.01" value='<?php echo esc_attr($instance_settings); ?>' placeholder="<?php echo $instance_field['placeholder']; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="hints">
                                        <div>
                                            <div class="aar"></div>
                                            <?php echo strip_tags($instance_field['description'], '<code>'); ?> <br><br>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    }
                }
                ?>	
            </tbody>
        </table>
        <?php do_action('wcmp_before_shipping_form_end_vendor_dashboard'); ?>
        <div class="action_div">
            <button class="wcmp_orange_btn" name="shipping_save">Save Options</button>
        </div>
        <div class="clear"></div>
    </form>
<?php endif; ?>