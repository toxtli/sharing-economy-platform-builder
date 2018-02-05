<?php if(!defined('ABSPATH')) die('!'); ?><div class="w3eden">
    <div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<b>My dashboard</b>
					</div>
					<div class="panel-body np">
						<?php if (count($agent_forms) > 0) { ?>
							<table class="table table-striped" style="margin-bottom: 0">
								<thead>
								<th>Form ID</th>
								<th>Form Title</th>
								<th><div class='pull-right'>Action</div></th>
								</thead>
								<tbody>
									<?php
									$url = get_permalink(get_the_ID());
									$sap = strpos($url, "?") ? "&" : "?";

									foreach ($agent_forms as $form) {
										?>
										<tr>
											<td><?php echo $form['ID'] ?></td>
											<td><?php echo $form['post_title'] ?></td>
											<td><a href="<?php echo $url . $sap . "section=requests&form_id={$form['ID']}" ?>" class="btn btn-danger btn-xs ttip pull-right" title="Manage From"><i class='fa fa-desktop'></i></a></td>
										</tr>
									<?php } ?>

								</tbody>
							</table>
						<?php } else {
							?>
							No forms have been assigned to you
							<?php
						}
						?>
					</div>
					<div class="panel-footer">
						<a class="btn btn-xs btn-primary" id="prof-edit-button" href="#" rel="<?php echo get_current_user_id() ?>"><span class="fa fa-cog"></span> Edit profile</a>
					</div>
				</div>
			</div>
		</div>
    </div>
	<div class="modal fade" id="modal-agent-editor">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">
						My info
					</h4>
				</div>
				<div class="modal-body">
					<?php 
						// Prepopulating current user info
						global $current_user;
						get_currentuserinfo();
					?>

					<form id='agent-update-form' class='form' method='post' action=''>
						<fieldset>
						<div class='form-group'>
								<label>Username:</label>
								<input type='text' class='form-control' value="<?php echo $current_user->user_login ?>" disabled="disabled" name='agentinfoshow[username]'/>
						</div>
						<div class='form-group'>
								<label>Display name:</label>
								<input type='text' id="agp-display" class='form-control' value="<?php echo $current_user->display_name ?>" name='agentinfo[display_name]'/>
						</div>
						<div class='form-group'>
								<label>Password:</label>
								<input type='password' id="agp-pass" class='form-control' name='agentinfo[password]'/>
						</div>
						<div class='form-group'>
								<label>Confirm password:</label>
								<input type='password' id="agp-passcon" class='form-control' name='agentinfo[confirm_password]'/>
						</div>
						<div class='form-group'>
								<label>Email:</label>
								<input type='email' id="agp-email" class='form-control' value="<?php echo $current_user->user_email ?>" name='agentinfo[email]'/>
						</div>
						</fieldset>
						<div class='form-group'>
								<button id='prof-update-button' class='btn btn-info' type='submit'><span class='fa fa-floppy-o'></span> Save</button>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button"  class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</div>

<script>
	jQuery(function($) {
		$('.ttip').tooltip({placement: 'bottom'});
		$('#prof-edit-button').on('click', function(e){
			e.preventDefault();	
			$('#modal-agent-editor .modal-title').html('Edit my profile');
			$('#modal-agent-editor').modal('show');
			return false;
		});
		var prof_form_validator = $('#agent-update-form').validate();
			$('#agp-display').rules('add', {
				minlength: 5,
				messages: {
					minlength: $.format('You must enter at least {0} characters'),
				}
			});
			$('#agp-pass').rules('add', {
				minlength: 6,
				equalTo: '#agp-passcon',
				messages: {
					minlength: $.format('You must enter at least {0} characters'),
					equalTo: 'Both the password fields must match'
				}
			});
			$('#agp-passcon').rules('add', {
				minlength: 6,
				equalTo: '#agp-pass',
				messages: {
					minlength: $.format('You must enter at least {0} characters'),
					equalTo: 'Both the password fields must match'
				}
			});
			$('#agp-email').rules('add', {
				required: true,
				email: true,
				messages: {
					required: 'You must enter and email address',
					email: 'A valid email address is required'
				}
			});
		var submit_button_html = '';
		$('#agent-update-form').on('submit', function(){
			var msgs = new Array();
			if (prof_form_validator.valid()) {
				var agent_update_options = {
					url: '<?php echo get_permalink(get_the_ID()); ?>&section=update_agent',
					beforeSubmit: function() {
						submit_button_html = $('#prof-update-button').html();
						$('#prof-update-button').html("<i id='spinner' class='fa fa-spinner fa-spin'></i> Please wait");
					},
					success: function(response) {
						$('#prof-update-button').html(submit_button_html);
						var response_vars;
						try {
							response_vars = JSON.parse(response);
						} catch(e) {
							console.log(e);
						}
						msgs.push(response_vars.message);
						showAlerts(msgs, response_vars.action);
						location.reload();
					}
				};
				$(this).ajaxSubmit(agent_update_options);
				return false;
			} else {
				msgs.push('Please fill the fields correctly');
				showAlerts(msgs, 'danger');
			}
		});
		function showAlerts(msgs, type) {
            jQuery('.formnotice').slideUp();
            alert_box = '<div style="margin-top: 20px" class="alert formnotice alert-' + type + ' disappear"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
            for (i = 0; i < msgs.length; i++) {
                alert_box += '' + msgs[i] + '<br/>';
            }
            alert_box += '</div>';
            jQuery('#agent-update-form').append(alert_box);

        }
	});

</script>