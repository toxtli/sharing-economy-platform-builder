<?php
/**
 * The template for displaying vendor lists
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_lists.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $WCMp;
?>
<div class="wcmp_vendor_list">
    <form name="vendor_sort" method="get">
        <div class="vendor_sort">
            <select class="select short" id="vendor_sort_type" name="vendor_sort_type">
                <option value="registered" <?php if ($sort_type == 'registered') {
    echo 'selected="selected"';
} ?> ><?php echo __('By date', 'dc-woocommerce-multi-vendor'); ?></option>
                <option value="name" <?php if ($sort_type == 'name') {
    echo 'selected="selected"';
} ?> ><?php echo __('By Alphabetically', 'dc-woocommerce-multi-vendor'); ?></option>
                <option value="category" <?php if ($sort_type == 'category') {
                echo 'selected="selected"';
            } ?> ><?php echo __('By Category', 'dc-woocommerce-multi-vendor'); ?></option>
            </select>
            <?php
            $product_category = get_terms('product_cat');
            $options_html = '';
            foreach ($product_category as $category) {
                if ($category->term_id == $selected_category) {
                    $options_html .= '<option value="' . esc_attr($category->term_id) . '" selected="selected">' . esc_html($category->name) . '</option>';
                } else {
                    $options_html .= '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
                }
            }
            ?>
            <select name="vendor_sort_category" id="vendor_sort_category" class="select"><?php echo $options_html; ?></select>					
            <input value="<?php echo __('Sort', 'dc-woocommerce-multi-vendor'); ?>" type="submit">
        </div>
    </form>
    <div class="wcmp_vendor_list_wrap">
<?php
if ($vendor_info && is_array($vendor_info)) {
    foreach ($vendor_info as $vendor) {
        ?>
                <div class="wcmp_sorted_vendors">
                    <?php do_action('wcmp_vendor_lists_single_before_image', $vendor['term_id'], $vendor['ID']); ?>
                    <a href="<?php echo $vendor['vendor_permalink']; ?>">
                        <img class="vendor_img" src="<?php echo $vendor['vendor_image']; ?>" id="vendor_image_display" width="125">
                    </a> 

                    <?php
                    $rating_info = wcmp_get_vendor_review_info($vendor['term_id']);
                    $WCMp->template->get_template('review/rating_vendor_lists.php', array('rating_val_array' => $rating_info));
                    ?>
                <?php do_action('wcmp_vendor_lists_single_after_image', $vendor['term_id'], $vendor['ID']); ?>
                <?php $button_text = apply_filters('wcmp_vendor_lists_single_button_text', $vendor['vendor_name']); ?>
                    <a href="<?php echo $vendor['vendor_permalink']; ?>" class="button"><?php echo $button_text; ?></a> 
        <?php do_action('wcmp_vendor_lists_single_after_button', $vendor['term_id'], $vendor['ID']); ?>
                </div>
    <?php
    }
} else {
    _e('No vendor found!', 'dc-woocommerce-multi-vendor');
}
?>
    </div>
</div>

<style>
    .vendor_address p img{height:12px;margin-right:14px;width:12px;display:inline-block}.vendor_description_background{background-color:#fff;background-size:cover;background-position:center center}.vendor_description{box-sizing:border-box;width:100%;clear:both;display:inline-block;padding-left:20px;padding-top:15px;background-color:rgba(0,0,0,.5);height:245px;font-weight:700}.vendor_description .vendor_img_add{width:50%;float:left}.vendor_description .description{width:50%;float:right;clear:right;padding-top:20px;padding-right:5px;top:78%;position:relative}.vendor_address p{margin:0 0 10px;text-align:left}.img_div img{height:auto;max-width:100px}.social_profile{float:right}.social_profile a{padding:2px;display:inline-block}.dc-wpv-quick-info-wrapper #respond{padding:0}.vendor_address label{font-size:14px;display:inline;color:#fff}.error_review_msg,.success_review_msg{border:1px solid;margin:10px 0;padding:15px 10px 15px 50px;background-position:10px center}.success_review_msg{color:#4F8A10;background-color:#DFF2BF}.error_review_msg{color:#D8000C;background-color:#FFBABA}.wocommerce #wcmp_vendor_reviews{margin-top:20px}@media screen and (max-width:640px){.vendor_description .vendor_img_add{width:auto}}.wcmp_vendor_list form[name=vendor_sort]{margin-bottom:25px}.wcmp_vendor_list_wrap{display: inline-block;width: 100%}.wcmp_sorted_vendors{width:22%;float:left;margin:0 4% 20px 0;border:1px solid #ccc;text-align:center;padding:15px;position: relative;background:#f7f7f7}.wcmp_vendor_list_wrap .wcmp_sorted_vendors:nth-child(4n+4){margin-right:0}.wcmp_rating_wrap{width:100%;min-height:30px;margin:15px 0}.wcmp_vendor_list_wrap img#vendor_image_display{margin:0 auto;border-radius:50%}.wcmp_rating_wrap .star-rating{margin:0 auto}.wcmp_sorted_vendors .button{background-color:#d8d8d8;padding: 8px 25px}@media screen and (max-width:768px){.wcmp_sorted_vendors{width:46%;margin-right:8%}.wcmp_vendor_list_wrap .wcmp_sorted_vendors:nth-child(4n+4){margin-right:8%}.wcmp_vendor_list_wrap .wcmp_sorted_vendors:nth-child(2n+2){margin-right:0}}@media screen and (max-width:480px){.wcmp_sorted_vendors{width:100%;margin:0 0 15px!important;text-align:center;padding-bottom:15px;border-bottom:1px solid #ccc}.wcmp_vendor_list_wrap .wcmp_sorted_vendors:last-child{margin-bottom:0}}
</style>