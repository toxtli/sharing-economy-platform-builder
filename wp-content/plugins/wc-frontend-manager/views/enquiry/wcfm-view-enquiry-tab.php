<?php
/**
 * WCFM plugin view
 *
 * wcfm Enquiry Tab View
 *
 * @author 		WC Lovers
 * @package 	wcfm/views/enquiry
 * @version   3.2.8
 */
 
global $wp, $WCFM, $WCFMu, $post, $wpdb;

$product_id = $post->ID;

if( !$product_id ) return;

?>

<?php
// Fetching existing Enquries
if( apply_filters( 'wcfm_is_pref_enquiry_tab', true ) ) {
	$enquiries = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wcfm_enquiries WHERE `is_private` = 0 AND `reply` != '' AND `product_id` = {$product_id}" );
	?>
	
	<h2><?php _e( 'General Enquiries', 'wc-frontend-manager' ); ?></h2>
	
	<?php
	if( empty( $enquiries ) ) {
		?>
		<p class="woocommerce-noreviews"><?php _e( 'There are no enquiries yet.', 'wc-frontend-manager' ); ?></p>
	<?php } ?>	
	
	<p><span class="add_enquiry"><span class="fa fa-question-circle-o"></span><span class="add_enquiry_label"><?php _e( 'Ask a Question', 'wc-frontend-manager' ); ?></span></span></p>
<?php } ?>
<div class="enquiry_form_wrapper_hide">
	<div id="enquiry_form_wrapper">
		<div id="enquiry_form">
			<div id="respond" class="comment-respond">
				<form action="" method="post" id="wcfm_enquiry_form" class="enquiry-form" novalidate="">
				  <?php if( !is_user_logged_in() ) { ?>
					  <p class="comment-notes"><span id="email-notes"><?php _e( 'Your email address will not be published.', 'wc-frontend-manager' ); ?></span></p>
					<?php } ?>
					
					<p class="comment-form-comment">
						<label for="comment"><?php _e( 'Your enquiry', 'wc-frontend-manager' ); ?> <span class="required">*</span></label>
						<textarea id="enquiry_comment" name="enquiry" cols="45" rows="8" aria-required="true" required=""></textarea>
					</p>
					
					<?php if( !is_user_logged_in() ) { ?>
						<p class="comment-form-author">
							<label for="author"><?php _e( 'Name', 'wc-frontend-manager' ); ?> <span class="required">*</span></label> 
							<input id="enquiry_author" name="customer_name" type="text" value="" size="30" aria-required="true" required="">
						</p>
						
						<p class="comment-form-email">
							<label for="email"><?php _e( 'Email', 'wc-frontend-manager' ); ?> <span class="required">*</span></label> 
							<input id="enquiry_email" name="customer_email" type="email" value="" size="30" aria-required="true" required="">
						</p>
					<?php } ?>
					
					<div class="wcfm_clearfix"></div>
					<div class="wcfm-message" tabindex="-1"></div>
					<div class="wcfm_clearfix"></div><br />
					
					<p class="form-submit">
						<input name="submit" type="submit" id="wcfm_enquiry_submit_button" class="submit" value="<?php _e( 'Submit', 'wc-frontend-manager' ); ?>"> 
						<input type="hidden" name="product_id" value="<?php echo $product_id; ?>" id="enquiry_product_id">
					</p>	
				</form>
			</div><!-- #respond -->
		</div>
	</div>
</div>

<?php 
if( apply_filters( 'wcfm_is_pref_enquiry_tab', true ) ) {
	if( !empty( $enquiries ) ) {
		echo '<div id="reviews" class="enquiry_reviews"><ol class="commentlist">';
		foreach( $enquiries as $enquiry_data ) {
			?>
			<li class="comment byuser comment-author-vnd bypostauthor even thread-even depth-1" id="li-enquiry-<?php echo $enquiry_data->ID; ?>">
				<div id="enquiry-<?php echo $enquiry_data->ID; ?>" class="comment_container">
					<div class="comment-text">
						<div class="enquiry-by"><span style="width:60%"><span class="fa fa-clock-o"></span> <?php echo date_i18n( wc_date_format(), strtotime( $enquiry_data->posted ) ); ?></span></div>
						<p class="meta">
							<strong class="woocommerce-review__author"><?php echo $enquiry_data->enquiry; ?></strong> <span class="woocommerce-review__dash">&ndash;</span> <time class="woocommerce-review__published-date"><?php _e( 'by', 'wc-frontend-manager' ); ?> <?php echo $enquiry_data->customer_name; ?></time>
						</p>
						<div class="description">
							<?php echo $enquiry_data->reply; ?>
						</div>
					</div>
				</div>
			</li><!-- #comment-## -->
		<?php
		}
		echo '</ol></div>';
	}
} 
?>