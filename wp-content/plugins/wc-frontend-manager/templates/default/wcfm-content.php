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
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php
global $WCFM;
$default_path = $WCFM->plugin_path . 'templates/default/';
include_once( $default_path . 'header.php' );
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

include_once( $default_path . 'footer.php' );
?>
</body>

<?php
wp_footer();
?>