<?php
if(!defined('ABSPATH')) die('!');
// setup wordpress url prefix
$purl = '?';
$params = array('post_type', 'page', 'page_id', 'form_id', 'post_id', 'status', 'ipp', 'paged');
foreach ($params as $param) {
	if (isset($_REQUEST[$param])) {
		$purl .= "{$param}=".esc_attr($_REQUEST[$param])."&";
	}

}
?>
<div class='row'>
	<div class='col-md-12'>
		<table class='table table-striped table-hover'>
			<thead><tr><th><input id="fic" type='checkbox' /></th><th>Action</th><th>Token</th>
					<?php
					foreach ($form_fields as $id => $field) {
						$fieldids[] = $id;
						echo "<th>{$field['label']}</th>";
					}
					?>
				</tr></thead><tbody>
				<?php
				foreach ($reqlist as $req) {
					echo "<tr id='fer_{$id}'><td><input type='checkbox' class='fic' name='ids[]' value='{$req['id']}' /></td><td><a href='{$purl}section=request&post_id={$form['id']}&req_id={$req['id']}' class='btn btn-info btn-xs'><i class='fa fa-eye'></i> View</a></td><td>{$req['token']}</td>";
					$req = maybe_unserialize($req['data']);
					foreach ($fieldids as $id) {
						$value = isset($req[$id]) ? $req[$id] : '';
						$value = is_array($value) ? implode(", ", $value) : $value;
						$value = esc_attr($value);
						echo "<td>{$value}&nbsp;</td>";
					}
					echo "</tr>";
				}
				?>

			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-md-8">
		<?php
		$cp = isset($_GET['paged']) ? (int)$_GET['paged'] : 1;
		$ipp = isset($_GET['ipp']) ? (int)$_GET['ipp'] : 20;
		$total = ceil($total_request / $ipp);

		$args = array(
			'base' => @add_query_arg(array('section' => 'requests', 'paged' => '%#%', 'form_id' => (int)$_REQUEST['form_id'])),
			'format' => '',
			'total' => intval($total),
			'current' => $cp,
			'prev_next' => True,
			'prev_text' => __('« Previous'),
			'next_text' => __('Next »'),
			'type' => 'list',
		);

		$pagination_html = preg_replace("/<ul[\s]*class='page-numbers/i", "<ul class='pagination", paginate_links($args));
		echo $pagination_html
		?> 
	</div>
	<div class="col-md-4">
		<div class="pagination">
			<div class="row">
				<div class="col-md-8">
					<input type="text" class="form-control" id="per-page" placeholder="Items per page (default: 5)"/>
				</div>
				<div class="col-md-4 pull-right">
					<span class="add-on"><a class="btn btn-danger" href="#" onclick="return false" id="per-page-confirmed">Load</a></span>
				</div>
			</div>
		</div>
	</div>
	<script type='text/javascript'>
		jQuery(document).ready(function($) {
			jQuery('#per-page-confirmed').on('click', function() {
				success = true;
				errors = '';
				if ($('#per-page').val() != "") {
					var value = $('#per-page').val().replace(/^\s\s*/, '').replace(/\s\s*$/, '');
					var intRegex = /^\d+$/;
					if (!intRegex.test(value)) {
						errors += "Field must be numeric.<br/>";
						success = false;
					}
				} else {
					errors += "Field is blank.</br />";
					success = false;
				}

				if (success == true) {
					window.location.href = "<?php echo $purl ?>section=requests&ipp=" + value + "&paged=1";
				} else {
				}


			});
		});

	</script>
</div>