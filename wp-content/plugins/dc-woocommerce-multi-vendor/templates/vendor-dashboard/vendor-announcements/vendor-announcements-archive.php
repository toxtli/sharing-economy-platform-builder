<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-announcements/vendor-announcements-archive.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.3.0
 */
global $WCMp;
$current_user = wp_get_current_user();
$current_user_id = $current_user->ID;
$current_user_meta = get_user_meta($current_user_id);
$dismiss_notices_ids = get_user_meta($current_user_id,'_wcmp_vendor_message_deleted', true);
if(!empty($dismiss_notices_ids)) {
	$dismiss_notices_ids_array = explode(',',$dismiss_notices_ids);
}
else {
	$dismiss_notices_ids_array = array();
	$dismiss_notices_ids_array[] = 0;
}
//print_r($dismiss_notices_ids_array);
$args = array(
	'posts_per_page'   => -1,	
	'post_type'        => 'wcmp_vendor_notice',	
	'post__in' => $dismiss_notices_ids_array,
	'post_status'      => 'publish',
	'suppress_filters' => true 
);
$posts_array = get_posts( $args );

$tab4_counter = 0;
if(isset($current_user_meta['_wcmp_vendor_message_deleted'][0])){
	$deleted_msg_ids = $current_user_meta['_wcmp_vendor_message_deleted'][0];
	$deleted_msg_ids_array = explode(',', $deleted_msg_ids);
}
if(isset($current_user_meta['_wcmp_vendor_message_readed'][0])){
	$readed_msg_ids = $current_user_meta['_wcmp_vendor_message_readed'][0];
	$readed_msg_ids_array = explode(',', $readed_msg_ids);
}	
?>
<div id="accordion-4">
<?php
foreach( $posts_array as $post_element) { 
	$post_date = 	$post_element->post_date;						
	if(isset($deleted_msg_ids_array) && is_array($deleted_msg_ids_array) && !empty($deleted_msg_ids_array)) {
		if(!in_array( $post_element->ID,$deleted_msg_ids_array)) {
			continue;
		}								
	}									
	?>
	
	<div <?php if($tab4_counter >= 6) {?> class="wcmp_hide_message4" <?php }?> >
		<div class="msg_date_box"><span><?php echo @date('d',strtotime($post_date)); ?></span><br>
			<?php echo @date('M',strtotime($post_date)); ?></div>
		<div class="msg_title_box"><span class="title"><?php echo $post_element->post_title; ?></span><br>
			<span class="mormaltext"> <?php echo $short_content = substr(stripslashes(strip_tags($post_element->post_content)),0,105); if(strlen(stripslashes(strip_tags($post_element->post_content))) > 105) {echo '...'; } ?></span> </div>
		<div class="msg_arrow_box"><a href="#" class="msg_stat_click"><i class="fa fa-caret-down"></i></a>
			<div class="msg_stat" style="display:none" >
				<ul class="wcmp_msg_deleted_ul" data-element="<?php echo $post_element->ID; ?>">
					<li class="_wcmp_vendor_message_restore"><a href="#"> <?php _e('Restore','dc-woocommerce-multi-vendor');?></a></li>															 
				</ul>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	
	<div <?php if($tab4_counter >= 6) {?> class="wcmp_hide_message" <?php }?> ><?php echo $content = apply_filters('the_content',$post_element->post_content); ?>
	<?php $url = get_post_meta($post_element->ID, '_wcmp_vendor_notices_url', true);  if(!empty($url)) { ?>
		<p style="text-align:right; width:100%;"><a href="<?php echo $url;?>" target="_blank" class="wcmp_black_btn_link"><?php echo __('Read More','dc-woocommerce-multi-vendor');?></a></p>
	<?php }?>
	</div>

	<?php $tab4_counter++;}
	if($tab4_counter <= 6) {
		$tab4_counter_show = $tab4_counter;
	}
	else {
		$tab4_counter_show = 6;
	}
	?>			
</div>
<div class="wcmp_mixed_txt" > <span> <?php _e('Showing Results', 'dc-woocommerce-multi-vendor'); ?></span>   <span class="first_nav_num"><?php echo $tab4_counter_show; ?></span> <?php _e('out of','dc-woocommerce-multi-vendor');?> <span class="second_nav_num"><?php echo  $tab4_counter; ?></span>
	<?php if($tab4_counter > 6) {?>
	<button class="wcmp_black_btn wcmp_black_btn_msg_for_nav" style="float:right"><?php _e('Show More','dc-woocommerce-multi-vendor'); ?></button>
	<?php }?>
	<div class="clear"></div>
</div>
	
