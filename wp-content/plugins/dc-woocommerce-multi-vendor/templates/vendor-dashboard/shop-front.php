<?php
/*
 * The template for displaying vendor dashboard
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/shop-front.php
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
$user = wp_get_current_user();
$vendor = get_wcmp_vendor($user->ID);
if ($vendor) :
    $vendor_hide_description = get_user_meta($user->ID, '_vendor_hide_description', true);
    $vendor_hide_email = get_user_meta($user->ID, '_vendor_hide_email', true);
    $vendor_hide_address = get_user_meta($user->ID, '_vendor_hide_address', true);
    $vendor_hide_phone = get_user_meta($user->ID, '_vendor_hide_phone', true);
    $vendor_hide_message_to_buyers = get_user_meta($user->ID, '_vendor_hide_message_to_buyers', true);

    $is_hide_option_show_enable = apply_filters('is_hide_option_show_enable', true);
    ?>
    <div class="wcmp_headding2"><?php _e('General', 'dc-woocommerce-multi-vendor'); ?></div>
    <form method="post" name="shop_settings_form" class="wcmp_shop_settings_form">
        <?php do_action('wcmp_before_shop_front'); ?>
        <div class="wcmp_form1">
            <p><?php _e('Store Name *', 'dc-woocommerce-multi-vendor'); ?></p>
            <input class="no_input" readonly type="text" name="vendor_page_title" value="<?php echo isset($vendor_page_title['value']) ? $vendor_page_title['value'] : ''; ?>"  placeholder="<?php _e('Enter your Store Name here', 'dc-woocommerce-multi-vendor'); ?>">
            <p><?php _e(' Store Slug *', 'dc-woocommerce-multi-vendor'); ?></p>
            <span style="display:block;" class="txt"><?php
                $dc_vendors_permalinks_array = get_option('dc_vendors_permalinks');
                if (isset($dc_vendors_permalinks_array['vendor_shop_base']) && !empty($dc_vendors_permalinks_array['vendor_shop_base'])) {
                    $store_slug = trailingslashit($dc_vendors_permalinks_array['vendor_shop_base']);
                } else {
                    $store_slug = trailingslashit('vendor');
                } echo $shop_page_url = trailingslashit(get_home_url());
                echo $store_slug;
                ?><input class="small no_input" readonly type="text" name="vendor_page_slug" readonly value="<?php echo isset($vendor_page_slug['value']) ? $vendor_page_slug['value'] : ''; ?>" placeholder="<?php _e('Enter your Store Name here', 'dc-woocommerce-multi-vendor'); ?>">
            </span>				
            <p> <?php _e('Shop Description', 'dc-woocommerce-multi-vendor'); ?>
                <?php if (get_wcmp_vendor_settings('is_hide_option_show', 'capabilities', 'miscellaneous') && $is_hide_option_show_enable) { ?>
                    <span class="input-group-addon beautiful" ><input type="checkbox" name="vendor_hide_description"  value="Enable" <?php if ($vendor_hide_description == 'Enable') echo 'checked=checked'; ?>><span>  <?php _e('Hide from user', 'dc-woocommerce-multi-vendor'); ?></span></span></p>
            <?php } ?>
            <textarea class="no_input" readonly name="vendor_description" cols="" rows="" placeholder="It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. "><?php echo isset($vendor_description['value']) ? $vendor_description['value'] : ''; ?></textarea>
            <?php if (isset($vendor_message_to_buyers)) { ?>
                <p> <?php _e('Message to Buyers', 'dc-woocommerce-multi-vendor'); ?></p>
                <textarea class="no_input" readonly name="vendor_message_to_buyers" cols="" rows="" placeholder="It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. "><?php echo isset($vendor_message_to_buyers['value']) ? $vendor_message_to_buyers['value'] : ''; ?></textarea>
            <?php } ?>
            <div class="half_part">
                <p><?php _e('Phone', 'dc-woocommerce-multi-vendor'); ?>
                    <?php if (get_wcmp_vendor_settings('is_hide_option_show', 'capabilities', 'miscellaneous') && $is_hide_option_show_enable) { ?>
                        <span class="input-group-addon beautiful" >
                            <input type="checkbox" name="vendor_hide_phone"  value="Enable" <?php if ($vendor_hide_phone == 'Enable') echo 'checked=checked'; ?> >
                            <span> <?php _e('Hide from user', 'dc-woocommerce-multi-vendor'); ?></span> </span> 
                    <?php } ?>	
                </p>
                <input class="no_input" readonly type="text" name="vendor_phone" placeholder="" value="<?php echo isset($vendor_phone['value']) ? $vendor_phone['value'] : ''; ?>">
            </div>
            <div class="half_part">
                <p><?php _e('Email *', 'dc-woocommerce-multi-vendor'); ?> 
                    <?php if (get_wcmp_vendor_settings('is_hide_option_show', 'capabilities', 'miscellaneous') && $is_hide_option_show_enable) { ?>
                        <span class="input-group-addon beautiful" >
                            <input type="checkbox"  name="vendor_hide_email"  value="Enable" <?php if ($vendor_hide_email == 'Enable') echo 'checked=checked'; ?>>
                            <span><?php _e('Hide from user', 'dc-woocommerce-multi-vendor'); ?></span> </span>
                    <?php } ?>
                </p>
                <input class="no_input vendor_email" readonly type="text" disabled placeholder=""  value="<?php echo isset($vendor->user_data->user_email) ? $vendor->user_data->user_email : ''; ?>">
            </div>
            <div class="clear"></div>
            <p><?php _e('Address', 'dc-woocommerce-multi-vendor'); ?> 
                <?php if (get_wcmp_vendor_settings('is_hide_option_show', 'capabilities', 'miscellaneous') && $is_hide_option_show_enable) { ?>
                    <span class="input-group-addon beautiful" >
                        <input type="checkbox" name="vendor_hide_address"  value="Enable" <?php if ($vendor_hide_address == 'Enable') echo 'checked=checked'; ?>>
                        <span><?php _e(' Hide from user', 'dc-woocommerce-multi-vendor'); ?></span> </span> 
                <?php } ?>	
            </p>
            <input class="no_input" readonly type="text" placeholder="Address line 1" name="vendor_address_1"  value="<?php echo isset($vendor_address_1['value']) ? $vendor_address_1['value'] : ''; ?>">
            <input class="no_input" readonly type="text" placeholder="Address line 2" name="vendor_address_2"  value="<?php echo isset($vendor_address_2['value']) ? $vendor_address_2['value'] : ''; ?>">
            <div class="one_third_part">
                <input class="no_input" readonly type="text" placeholder="Country" name="vendor_country" value="<?php echo isset($vendor_country['value']) ? $vendor_country['value'] : ''; ?>">
            </div>
            <div class="one_third_part">
                <input class="no_input" readonly  type="text" placeholder="state"  name="vendor_state" value="<?php echo isset($vendor_state['value']) ? $vendor_state['value'] : ''; ?>">
            </div>
            <div class="one_third_part">
                <input class="no_input" readonly type="text" placeholder="city"  name="vendor_city" value="<?php echo isset($vendor_city['value']) ? $vendor_city['value'] : ''; ?>">
            </div>
            <input class="no_input" readonly type="text" placeholder="Zipcode" style="width:50%;" name="vendor_postcode" value="<?php echo isset($vendor_postcode['value']) ? $vendor_postcode['value'] : ''; ?>">
            <?php
            $is_vendor_add_external_url_field = apply_filters('is_vendor_add_external_url_field', true);
            if ($WCMp->vendor_caps->vendor_capabilities_settings('is_vendor_add_external_url') && $is_vendor_add_external_url_field) {
                ?>
                <div class="half_part">
                    <input class="no_input" readonly type="text" placeholder="External store URL" name="vendor_external_store_url" value="<?php echo isset($vendor_external_store_url['value']) ? $vendor_external_store_url['value'] : ''; ?>">
                </div>
                <div class="half_part">
                    <input class="no_input" readonly type="text" placeholder="External store URL Label" name="vendor_external_store_label" value="<?php echo isset($vendor_external_store_label['value']) ? $vendor_external_store_label['value'] : ''; ?>">
                </div>
                <?php
            }
            ?>
        </div>
        <div class="wcmp_headding2 moregap"><?php _e('Media Files', 'dc-woocommerce-multi-vendor'); ?></div>
        <div class="wcmp_media_block">

            <span class="dc-wp-fields-uploader">
                <img class="one_third_part" id="vendor_image_display" width="300" src="<?php echo (isset($vendor_image['value']) && (!empty($vendor_image['value']))) ? $vendor_image['value'] : $WCMp->plugin_url . 'assets/images/logo_placeholder.jpg'; ?>" class="placeHolder" />
                <input type="text" name="vendor_image" id="vendor_image" style="display: none;" class="user-profile-fields" readonly value="<?php echo (isset($vendor_image['value']) && (!empty($vendor_image['value']))) ? $vendor_image['value'] : $WCMp->plugin_url . 'assets/images/logo_placeholder.jpg'; ?>"  />
                <input type="button" class="upload_button wcmp_black_btn moregap two_third_part" name="vendor_image_button" id="vendor_image_button" value="<?php _e('Upload', 'dc-woocommerce-multi-vendor') ?>" />
                <input type="button" class="remove_button wcmp_black_btn moregap two_third_part" name="vendor_image_remove_button" id="vendor_image_remove_button" value="<?php _e('Replace', 'dc-woocommerce-multi-vendor') ?>" />
            </span>
            <div class="clear"></div>
        </div>
        <div class="wcmp_media_block">
            <span class="dc-wp-fields-uploader">
                <img class="one_third_part" id="vendor_banner_display" width="300" src="<?php echo (isset($vendor_banner['value']) && (!empty($vendor_banner['value'])) ) ? $vendor_banner['value'] : $WCMp->plugin_url . 'assets/images/banner_placeholder.jpg'; ?>" class="placeHolder" />
                <input type="text" name="vendor_banner" id="vendor_banner" style="display: none;" class="user-profile-fields" readonly value="<?php echo (isset($vendor_banner['value']) && (!empty($vendor_banner['value'])) ) ? $vendor_banner['value'] : $WCMp->plugin_url . 'assets/images/banner_placeholder.jpg'; ?>"  />
                <input type="button" class="upload_button wcmp_black_btn moregap two_third_part" name="vendor_banner_button" id="vendor_banner_button" value="<?php _e('Upload', 'dc-woocommerce-multi-vendor') ?>" />
                <input type="button" class="remove_button wcmp_black_btn moregap two_third_part" name="vendor_banner_remove_button" id="vendor_banner_remove_button" value="<?php _e('Replace', 'dc-woocommerce-multi-vendor') ?>" />
            </span>
            <div class="clear"></div>
        </div>
        <div class="wcmp_headding2 moregap"><?php _e('Social Media', 'dc-woocommerce-multi-vendor'); ?></div>
        <div class="wcmp_media_block">
            <p><?php _e('Enter your Social Media profile URL below:', 'dc-woocommerce-multi-vendor'); ?></p>
            <div class="full_part"><img src="<?php echo $WCMp->plugin_url . 'assets/images/facebook.png' ?>" alt="" class="social_icon" >
                <input class="long no_input" readonly type="text"   name="vendor_fb_profile" value="<?php echo isset($vendor_fb_profile['value']) ? $vendor_fb_profile['value'] : ''; ?>">
            </div>
            <div class="full_part"><img src="<?php echo $WCMp->plugin_url . 'assets/images/twitter.png' ?>" alt="" class="social_icon">
                <input class="long no_input" readonly type="text"   name="vendor_twitter_profile" value="<?php echo isset($vendor_twitter_profile['value']) ? $vendor_twitter_profile['value'] : ''; ?>">
            </div>
            <div class="full_part"><img src="<?php echo $WCMp->plugin_url . 'assets/images/linkedin_33x35.png' ?>" alt="" class="social_icon">
                <input class="long no_input" readonly type="text"  name="vendor_linkdin_profile" value="<?php echo isset($vendor_linkdin_profile['value']) ? $vendor_linkdin_profile['value'] : ''; ?>">
            </div>
            <div class="full_part"><img src="<?php echo $WCMp->plugin_url . 'assets/images/googleplus.png' ?>" alt="" class="social_icon">
                <input class="long no_input" readonly type="text"   name="vendor_google_plus_profile" value="<?php echo isset($vendor_google_plus_profile['value']) ? $vendor_google_plus_profile['value'] : ''; ?>">
            </div>
            <div class="full_part"><img src="<?php echo $WCMp->plugin_url . 'assets/images/youtube.png' ?>" alt="" class="social_icon wcmp_to_disable">
                <input class="long no_input" readonly type="text"   name="vendor_youtube" value="<?php echo isset($vendor_youtube['value']) ? $vendor_youtube['value'] : ''; ?>">
            </div>
            <div class="full_part"><img src="<?php echo $WCMp->plugin_url . 'assets/images/instagram.png' ?>" alt="" class="social_icon wcmp_to_disable">
                <input class="long no_input" readonly type="text"   name="vendor_instagram" value="<?php echo isset($vendor_instagram['value']) ? $vendor_instagram['value'] : ''; ?>">
            </div>
            <div class="clear"></div>
        </div>
        <?php if(get_wcmp_vendor_settings('can_vendor_edit_shop_template', 'vendor', 'dashboard', false)): ?>
        <div class="wcmp_headding2 moregap"><?php _e('Shop Template', 'dc-woocommerce-multi-vendor'); ?></div>
        <ul class="wcmp_template_list">
        <?php
        $template_options = apply_filters('wcmp_vendor_shop_template_options', array('template1' => $WCMp->plugin_url.'assets/images/template1.png', 'template2' => $WCMp->plugin_url.'assets/images/template2.png', 'template3' => $WCMp->plugin_url.'assets/images/template3.png'));
        $shop_template = get_wcmp_vendor_settings('wcmp_vendor_shop_template', 'vendor', 'dashboard', 'template1');
        $shop_template = get_wcmp_vendor_settings('can_vendor_edit_shop_template', 'vendor', 'dashboard', false) && get_user_meta($user->ID, '_shop_template', true) ? get_user_meta($user->ID, '_shop_template', true) : $shop_template;
        foreach ($template_options as $template => $template_image): ?>
            <li>
                <label>
                    <input type="radio" <?php checked($template, $shop_template); ?> name="_shop_template" value="<?php echo $template; ?>" />  
                    <i class="fa fa-square-o" aria-hidden="true"></i>
                    <img src="<?php echo $template_image; ?>" />
                </label>
            </li>
        <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <?php do_action('wcmp_after_shop_front'); ?>
        <?php do_action('other_exta_field_dcmv'); ?>
        <div class="action_div_space"> </div>
        <p class="error_wcmp"><?php _e('* This field is required, you must fill some information.', 'dc-woocommerce-multi-vendor'); ?></p>
        <div class="action_div">
            <button class="wcmp_orange_btn" name="store_save"><?php _e('Save Options', 'dc-woocommerce-multi-vendor'); ?></button>
            <div class="clear"></div>
        </div>
    </form>
<?php endif; ?>