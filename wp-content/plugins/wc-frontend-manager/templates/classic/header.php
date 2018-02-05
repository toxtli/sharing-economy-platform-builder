<?php
/**
 * WCFM plugin templates
 *
 * Header area
 *
 * @author 		WC Lovers
 * @package 	wcfm/templates/default
 * @version   3.1.2
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="wcfm-header" class="left-logo">
	<div class="wcfm-header-container">
		<div class="wcfm-header-content">
			<?php
			$blog_title = get_bloginfo( 'name' );
			$blog_link  = get_bloginfo( 'url' );
			?>
			<div class="wcfm-site-name">
				<a href="<?php echo $blog_link; ?>"><?php echo $blog_title; ?></a>
			</div>
		</div>
	</div>
</div>
<div class="wcfm_clearfix"></div>