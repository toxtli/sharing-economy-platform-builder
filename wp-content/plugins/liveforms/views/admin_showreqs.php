<?php
if(!defined('ABSPATH')) die('!');
// Setup wordpress URL prefix
//$url = get_permalink(get_the_ID());
//$sap = strpos($url, "?") ? "&" : "?";
//$purl = $url . $sap;
//// To get access to administration panel
//$purl .= "post_type={$_REQUEST['post_type']}&page={$_REQUEST['page']}&form_id={$_REQUEST['form_id']}&section={$_REQUEST['section']}&";
$purl = '?';
$params = array('post_type', 'page', 'page_id', 'form_id', 'post_id', 'status', 'ipp', 'paged', 'section',);
foreach ($params as $param) {
	if (isset($_REQUEST[$param]))
		$purl .= "{$param}=".esc_attr($_REQUEST[$param])."&";
}
$non_submit_fields = array('Pageseparator', 'Mathresult');
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-5">
			<button class="btn btn-disabled btn-bordered btn-block text-left">
				Form Name:
				<h3 style="margin: 10px 0;"><?php echo $form['title']; ?></h3>
			</button>
		</div>
		<div class="col-md-7 text-right">
			<div class="row btns">
				<div class="col-md-3"><button class="btn btn-primary btn-block showreqs" data-status="new"><h3 id="new" style="margin: 10px 0"><?php echo $counts['new'] ?></h3>New Entries</button></div>
				<div class="col-md-3"><button class="btn btn-success btn-block showreqs" data-status="inprogress"><h3 id="inprogress" style="margin: 10px 0"><?php echo $counts['inprogress'] ?></h3>In Progress</button></div>
				<div class="col-md-3"><button class="btn btn-warning btn-block showreqs" data-status="onhold"><h3 id="onhold" style="margin: 10px 0"><?php echo $counts['onhold'] ?></h3>On Hold</button></div>
				<div class="col-md-3"><button class="btn btn-default btn-block showreqs" data-status="resolved"><h3 id="resolved" style="margin: 10px 0"><?php echo $counts['resolved'] ?></h3>Resolved</button></div>
			</div>
		</div>
	</div><br/>
	<form method="post" action='' id="reqform">
		<div class="panel panel-default">
			<div class="panel-heading">
				<b>Form Entries</b>
				<div class="pull-right" style="margin-top: -2px;margin-right: -3px">
					<button type="submit" id="btn_resolved" name="action" value="resolved" class="btn btn-xs btn-success"><i class="fa fa-check"></i> &nbsp;Resolve</button>
					<button type="submit" id="btn_delete" name="action" value="delete" class="btn btn-xs btn-danger"><i class="fa fa-times"></i> &nbsp;Delete</button>
					<button type="submit" id="btn_onhold" name="action" value="onhold" class="btn btn-xs btn-warning"><i class="fa fa-clock-o"></i> &nbsp;Hold</button>
					<?php  do_action('liveform_form-entries_action_button'); ?>
				</div>
			</div>
			<div class="panel-body np" id="form-entries">
				<div class="row">
					<div class="col-md-12">
						<table class='table table-striped table-hover'>
							<thead>
								<tr>
									<th><input id="fic" type='checkbox' /></th><th>Action</th><th>Token</th><th>Time</th>
									<?php
									foreach ($form_fields as $id => $field) {
										if (!in_array(substr($id, 0, strpos($id, '_')), $non_submit_fields)) {
											$fieldids[] = $id;
											echo "<th>{$field['label']}</th>";
										}
									}
									?>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($reqlist as $req) {
									$time = date('d-m-Y', $req['time']);
									echo "<tr id='fer_{$id}'><td><input type='checkbox' class='fic' name='ids[{$req['id']}]' value='{$req['id']}' /></td><td><a href='{$purl}section=request&form_id={$form['id']}&req_id={$req['id']}' class='btn btn-info btn-xs'><i class='fa fa-eye'></i> View</a></td><td>{$req['token']}</td><td>{$time}</td>";
									$req = unserialize($req['data']);
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
							'base' => @add_query_arg(array('section' => esc_attr($_REQUEST['section']), 'paged' => '%#%', 'form_id' => (int)$_REQUEST['form_id'])),
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
							$('#per-page-confirmed').on('click', function() {
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
									window.location.href = "<?php echo $purl ?>ipp=" + value + "&paged=1";
								} else {
								}


							});
						});
					</script>
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	jQuery(function($) {
		$('#fic').on('click', function() {
			if (this.checked)
				$('.fic').prop('checked', true);
			else
				$('.fic').prop('checked', false);
		});
		$('#fef').submit(function() {
			$(this).ajaxSubmit({
				beforeSubmit: function(reqs) {
				}
			});
			return false;
		});

		var options = {
			url: '<?php echo $purl ?>action=change_req_state&form_id=<?php echo isset($_REQUEST[$param])?(int)$_REQUEST['form_id']:0 ?>&status=',
			reqstatus: 'new',
			newstatus: 'new',
			beforeSubmit: function() {
				$('#form-entries').prepend("<div class='data-loading'><i class='fa fa-spinner fa-spin'></i> &nbsp; loading...</div>");
			},
			success: function(response) {
				var jsonData = JSON.parse(response);

				if (jsonData['html'] != '') {
					$('#form-entries').html(jsonData['html']);
				}
				$('#' + this.reqstatus).html(jsonData['count']);
				if (this.reqstatus != this.newstatus) {
					// update
					$old_count = parseInt($('#' + this.newstatus).html());
					$new_count = parseInt(jsonData['changed']) + $old_count;
					$('#' + this.newstatus).html($new_count);
				}
			}
		}
		$('#reqform').on('submit', function() {
			var new_status = $('button[type=submit][clicked=true]').val();
			// Deep copy
			var current_options = jQuery.extend(true, {}, options);
			current_options.newstatus = new_status;
			current_options.url += new_status + '&query_status=' + current_options.reqstatus;
			$(this).ajaxSubmit(current_options);

			return false;
		});


		$('#reqform button[type=submit]').click(function() {
			$("button[type=submit]", $(this).parents("#reqform")).removeAttr("clicked");
			$(this).attr("clicked", "true");
		});
		$('.showreqs').on('click', function(e) {
			e.preventDefault();
			var status = $(this).attr('data-status');
			options.reqstatus = status;
			$('#form-entries').prepend("<div class='data-loading'><i class='fa fa-spinner fa-spin'></i> &nbsp; loading...</div>").load('<?php echo $purl; ?>section=stat_req&form_id=<?php echo (int)$_REQUEST['form_id']; ?>&status=' + status, function() {
				window.history.pushState("", "Title", '<?php echo $purl; ?>section=stat_req&form_id=<?php echo (int)$_REQUEST['form_id'];?>&status=' + status);
				$('#fic').on('click', function() {
					if (this.checked)
						$('.fic').prop('checked', true);
					else
						$('.fic').prop('checked', false);
				});
			});
		});
	});
</script>