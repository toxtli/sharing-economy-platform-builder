<?php
global $WCFM, $wp;

$wcfm_reports_menus = apply_filters( 'wcfm_reports_menus', array( 'sales-by-date' => __( 'Sales by date', 'wc-frontend-manager'), 
																																	'out-of-stock' => __( 'Out of stock', 'wc-frontend-manager')
																																) );

?>

<ul class="wcfm_reports_menus">
	<?php
	$is_first = true;
	foreach( $wcfm_reports_menus as $wcfm_reports_menu_key => $wcfm_reports_menu) {
		?>
		<li class="wcfm_reports_menu_item">
			<?php
			if($is_first) $is_first = false;
			else echo " | ";
			?>
			<a class="<?php echo isset( $wp->query_vars['wcfm-reports-' . $wcfm_reports_menu_key] ) ? 'active' : ''; ?>" href="<?php echo get_wcfm_reports_url( '', 'wcfm-reports-' . $wcfm_reports_menu_key ); ?>"><?php echo $wcfm_reports_menu; ?></a>
		</li>
		<?php
	}
	?>
</ul>
	