<?php if(!defined('ABSPATH')) die('!'); ?><div class='w3eden' style="padding: 10px">
<div class="form-group">
	<label>
		<input type="hidden" name="contact[support_token]" value="0">
		<input type="checkbox" name="contact[support_token]" value="1" <?php checked($support_token, 1); ?> > Send Support Token
	</label>
	<p style="font-size: 11px;color: #888;font-style: italic;margin-bottom: 15px;display: block;letter-spacing: 0.6px">
		If you check the following option, form submitter will received a token number to get access to the replies for their query.
	</p>
</div><div class="form-group">

		<label>Assign Form Handler:</label>
	<select name='contact[agent]' class='form-control'>
		<option value=''>Myself</option>
		<?php foreach ($agents as $agent) { ?>
			<?php $agent_data = $agent->data; ?>
			<option <?php echo ($agent_data->ID == $agent_id ? 'selected="selected"' : '') ?> value='<?php echo $agent_data->ID ?>'><?php echo $agent_data->display_name ?></option>
		<?php } ?>
	</select>
	<p style="font-size: 11px;color: #888;font-style: italic;display: block;letter-spacing: 0.6px;margin-top: 5px">
		You may assign this form to any member of your team, who will also have access to this form, can see the form submissions and manage them.
	</p>
</div>
</div>