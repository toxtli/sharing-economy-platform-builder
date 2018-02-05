<?php
/**
 * WCFM plugin templates
 *
 * Main content area
 *
 * @author 		WC Lovers
 * @package 	wcfm/templates/default
 * @version   3.1.2
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $WCFM;

$stylesheet_path = get_stylesheet_directory() . '/';
$default_path = get_template_directory() . '/';
if( file_exists( $stylesheet_path . 'header.php' ) ) {
	include_once( $stylesheet_path . 'header.php' );
} else {
	include_once( $default_path . 'header.php' );
}	

while ( have_posts() ) : the_post(); ?>
	<div id="wcfm-main-content" class="<?php echo ''; ?>">
		<div class="wcfm-content-container">
			<div class="wcfm-main-content-wrap">
				<?php do_action( 'before_wcfm_dashboard' ); ?>
				<?php the_content(); ?>
				<?php do_action( 'after_wcfm_dashboard' ); ?>
			</div>
		</div>
	</div>

	<?php
endwhile;
wp_reset_query();

if( file_exists( $stylesheet_path . 'footer.php' ) ) {
	include_once( $stylesheet_path . 'footer.php' );
} else {
	include_once( $default_path . 'footer.php' );
}	
?>