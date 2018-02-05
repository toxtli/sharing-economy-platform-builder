<?php
if(!defined('ABSPATH')) die('!');
// Setup wordpress URL prefix
$url = get_permalink(get_the_ID());
$sap = strpos($url, "?") ? "&" : "?";
$purl = $url . $sap;
$non_submit_fields = array('Pageseparator', 'Mathresult');
?>
<div class="w3eden">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						Request Details
					</div>
					<div class="panel-body">
						<?php foreach ($form_fields as $field_id => $field_pref) { ?>
							<?php if (!in_array(substr($field_id, 0, strpos($field_id, '_')), $non_submit_fields)) { ?>
								<div class="form-group">
									<label><?php echo $field_pref['label'] ?>: </label>
									<?php if(strstr($field_id, 'password_')) { ?>
									<a onclick="return false" class="btn btn-xs btn-danger spoiler-toggler notshowing" data-target="<?php echo 'password_field_'.$field_id ?>">Show</a>
									<div id="<?php echo 'password_field_'.$field_id ?>" style="display: none;"><strong><?php echo $field_values[$field_id]; ?></strong></div>
									<?php } else { ?>
									<div><strong><?php echo esc_attr($field_values[$field_id]); ?></strong></div>
									<?php } ?>
								</div>
							<?php } ?>
						<?php } ?>
						<hr/>

					</div>
				</div>
				<form id='replyform' method="post" action="">
					<div class="panel panel-default">
						<div class="panel-body">
							<textarea class="form-control" name="reply_msg"></textarea>
							<input type='hidden' name="token" value='<?php echo "request('token')" ?>'/>
							<input type="hidden" name="req_status" value="<?php echo esc_attr($req_data['status']) ?>"/>
							<input type="hidden" name="req_id" value="<?php echo (int)$req_data['id'] ?>"/>
							<input type="hidden" name="form_id" value="<?php echo (int)$req_data['fid'] ?>"/>
							<input type="hidden" name="user_name" value="<?php echo esc_attr($current_user_name) ?>"/>
							<?php if (isset($_REQUEST['token'])) { ?>
								<input type="hidden" name="token" value="<?php echo esc_attr($_REQUEST['token']) ?>"/>
							<?php } ?>
						</div>
						<div class="panel-footer text-right">
							<button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-reply"></i> &nbsp;Send Reply</button>
						</div>
					</div>
				</form>
				<div class="row">
					<div class="col-md-12" id="replies">
						<?php if (count($reply_history)) { ?>
							<?php foreach ($reply_history as $reply) { ?>
								<div class="media thumbnail">
									<div class="pull-left">
										<img src="http://www.gravatar.com/avatar/<?php echo base64_encode($reply['icon']) ?>" />
									</div>
									<div class="media-body">
										<h3 class="media-heading"><?php echo esc_attr($reply['username']); ?></h3>
										(<?php echo date('Y-m-d H:m', $reply['time']) ?>)
										<p><?php echo esc_attr($reply['data']); ?></p>
									</div>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	jQuery(function($) {
		// Password field spiler protection
		$('.spoiler-toggler').on('click', function(){
			target_div = $(this).attr('data-target');
			if ($(this).hasClass('notshowing')) {
				$(this).html('Hide');
				$(this).removeClass('notshowing');
				$(this).addClass('showing');
				$('#'+target_div).css({display: 'block'});
			} else {
				$(this).html('Show');
				$(this).removeClass('showing');
				$(this).addClass('notshowing');
				$('#'+target_div).css({display: 'none'});
			}
		});
		$(this).has

		// Reply submission
		var options = {
			//target: '#replies', // target element(s) to be updated with server response
			url: '<?php echo $purl."section=reply" ?>',
			beforeSubmit: function() {
				$('#replies').prepend("<button id='spinner'  type='button' class='btn btn-link btn-block'><i class='fa fa-spinner fa-spin'></i></button>");
			}, // pre-submit callback
			success: function(response) {
				$('#spinner').remove();
				$('#replies').prepend(response);
			}
		};

		$('#replyform').on('submit', function() {
			$(this).ajaxSubmit(options);
			return false;
		});

	});
</script>