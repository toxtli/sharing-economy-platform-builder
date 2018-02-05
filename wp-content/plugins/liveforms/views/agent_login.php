<?php if(!defined('ABSPATH')) die('!'); ?><div class="w3eden">
	<div class="row">
		<div class="col-md-6">
			<?php echo wp_login_form() ?>
		</div>
	</div>
</div>

<script typej="text/javascript">
	jQuery(document).ready(function($) {
		$('.input').addClass('form-control');
		$('.form-control').removeClass('input');
	});
</script>