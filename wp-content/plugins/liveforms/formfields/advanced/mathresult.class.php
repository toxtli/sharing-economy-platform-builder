<?php
class Mathresult {


	public function control_button() {
		ob_start();
		?>
		<li class="list-group-item" data-type="<?php echo __CLASS__ ?>" for="Mathresult">
			<span class="lfi lfi-name"><i class="fa fa-plus-square"></i></span> Math result
			<a title="Mathresult" rel="Mathresult" class="add pull-right add-form-field" data-template='Mathresult' href="#"><i class="fa fa-plus-circle ttipf" title=""></i></a>
		</li>
		
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

	public function field_settings($fieldindex, $fieldid, $field_infos) {
		ob_start();
?>
		<li class="list-group-item" data-type="<?php echo __CLASS__ ?>" id="field_<?php echo $fieldindex; ?>">
			<input type="hidden" name="contact[fields][<?php echo $fieldindex ?>]" value="<?php echo $fieldid; ?>">
			<span id="label_<?php echo $fieldindex; ?>"><?php echo $field_infos[$fieldindex]['label'] ?></span>
			<a href="#" rel="field_<?php echo $fieldindex; ?>" class="remove"><i class="fa fa-trash-o pull-right"></i></a>
			<a href="#" class="cog-trigger" rel="#cog_<?php echo $fieldindex; ?>"><i class="fa fa-wrench pull-right button-buffer-right"></i></a>

			<div class="field-preview">
				<?php 	
					$finfo = $field_infos[$fieldindex];
					$finfo['id'] = $fieldindex;
					echo self::field_preview_html($finfo);
				?>
			</div>
			<div class="cog" id="cog_<?php echo $fieldindex; ?>" style='display: none'>
				<fieldset>
					<h5>Settings</h5>
					<div class="form-group">
						<label>Label:</label>
						<input class="form-control form-field-label" data-target="#label_<?php echo $fieldindex; ?>" type="text" value="<?php echo $field_infos[$fieldindex]['label'] ?>" name="contact[fieldsinfo][<?php echo $fieldindex ?>][label]" />
					</div>
					<div class='form-group'>
						<label>Field 1</label>
						<select name='contact[fieldsinfo][<?php echo $fieldindex?>][fields][0]' data-selection='<?php echo $field_infos[$fieldindex]['fields'][0] ?>' class='form-control math-field-variable math-field-variable-<?php echo $fieldindex ?>'>
							<option value=''>Select first field</option>
						</select>
					</div>
					<div class='form-group'>
						<label>Field 2</label>
						<select name='contact[fieldsinfo][<?php echo $fieldindex?>][fields][1]' data-selection='<?php echo $field_infos[$fieldindex]['fields'][1] ?>'  class='form-control math-field-variable math-field-variable-<?php echo $fieldindex?>'>
							<option value=''>Select second field</option>
						</select>
					</div>
					<div class='form-group'>
						<label>Operator</label>
						<select name='contact[fieldsinfo][<?php echo $fieldindex ?>][operator]' class='form-control math-field-operator'>
							<option <?php if ($field_infos[$fieldindex]['operator'] == '') echo 'selected="selected"' ?> value=''>Select an operator</option>
							<option <?php if ($field_infos[$fieldindex]['operator'] == 'add') echo 'selected="selected"' ?> value='add'>Add</option>
							<option <?php if ($field_infos[$fieldindex]['operator'] == 'subtract') echo 'selected="selected"' ?> value='subtract'>Subtract</option>
							<option <?php if ($field_infos[$fieldindex]['operator'] == 'multiply') echo 'selected="selected"' ?> value='multiply'>Multiply</option>
							<option <?php if ($field_infos[$fieldindex]['operator'] == 'divide') echo 'selected="selected"' ?> value='divide'>Divide</option>
						</select>
					</div>
					<div class="form-group">
						<label>Note</label>
						<textarea class="form-control" type="text" value=""
								  name="contact[fieldsinfo][<?php echo $fieldindex ?>][note]"><?php echo $field_infos[$fieldindex]['note'] ?></textarea>
					</div>
					<div class='form-group'>
						<label><input rel='condition-params' class='cond' type='checkbox' name='contact[fieldsinfo][<?php echo $fieldindex ?>][conditioned]' value='1' <?php if (isset($field_infos[$fieldindex]['conditioned'])) echo 'checked="checked"'; ?>/> Conditional logic</label>
						<div id="cond_<?php echo $fieldindex ?>" class='cond-params' style='display:none'>
							<div class="form-group">
								<div class="row row-bottom-buffer">
									<div class="col-md-12">
										<select class="select" name="contact[fieldsinfo][<?php echo $fieldindex ?>][condition][action]">
											<option <?php if (isset($field_infos[$fieldindex]['condition']) and $field_infos[$fieldindex]['condition']['action'] == 'show') echo 'selected="selected"' ?> value="show">Show</option>
											<option <?php if (isset($field_infos[$fieldindex]['condition']) and $field_infos[$fieldindex]['condition']['action'] == 'hide') echo 'selected="selected"' ?> value="hide">Hide</option>
										</select>
										this field if
										<select class="select" name="contact[fieldsinfo][<?php echo $fieldindex ?>][condition][boolean_op]">
											<option <?php if (isset($field_infos[$fieldindex]['condition']) and $field_infos[$fieldindex]['condition']['boolean_op'] == 'all') echo 'selected="selected"' ?> value="all">All</option>
											<option <?php if (isset($field_infos[$fieldindex]['condition']) and $field_infos[$fieldindex]['condition']['boolean_op'] == 'any') echo 'selected="selected"' ?> value="any">Any</option>
										</select>
										of these conditions are met
									</div>
								</div>
								<?php $cond_list = isset($field_infos[$fieldindex]['condition']) ? $field_infos[$fieldindex]['condition']['value'] : array(); ?>
								<?php foreach($cond_list as $key => $value) { ?>
									<div class='row row-bottom-buffer' rel="row">
										<div class='col-md-4'>
											<select class='form-control cond-field-selector' data-selection='<?php echo isset($field_infos[$fieldindex]['condition']['field'][$key]) ? $field_infos[$fieldindex]['condition']['field'][$key] : ''  ?>' name='contact[fieldsinfo][<?php echo $fieldindex ?>][condition][field][]'>
												<option value="">Select a field</option>
											</select>
										</div>
										<div class='col-md-3'>
											<select class='form-control cond-operator' data-selection='<?php echo isset($field_infos[$fieldindex]['condition']['op'][$key]) ? $field_infos[$fieldindex]['condition']['op'][$key] : ''  ?>' name='contact[fieldsinfo][<?php echo $fieldindex ?>][condition][op][]'>
												<option value='is'>Is</option>
												<option value='is-not'>Is not</option>
												<option value='less-than'>Less than</option>
												<option value='greater-than'>Greater than</option>
												<option value='contains'>Contains</option>
												<option value='starts-with'>Starts with</option>
												<option value='ends-with'>Ends with</option>
											</select>
										</div>
										<div class='col-md-4'>
											<select class='form-control is-cond-selector' data-selection='<?php echo isset($field_infos[$fieldindex]['condition']['value'][$key]) ? $field_infos[$fieldindex]['condition']['value'][$key] : ''  ?>'>
												<option value='email'>Email</option>
												<option value='phone'>Phone</option>
											</select>
											<input type='text' class='form-control is-cond-text hide' data-selection='<?php echo isset($field_infos[$fieldindex]['condition']['value'][$key]) ? $field_infos[$fieldindex]['condition']['value'][$key] : ''  ?>' placeholder='Enter a value' value=''/>
											<input type='hidden' value='<?php echo isset($field_infos[$fieldindex]['condition']['value'][$key]) ? $field_infos[$fieldindex]['condition']['value'][$key] : ''  ?>' class="is-cond-data" name='contact[fieldsinfo][<?php echo $fieldindex ?>][condition][value][]'/>
										</div>
										<div class="col-md-1">
											<a href="#" class="add-cond-option"
											   rel="<?php echo $fieldindex ?>"><i
													class="glyphicon glyphicon-plus-sign pull-left"></i></a>
											<a href="#" class="del-cond-option"
											   rel="<?php echo $fieldindex ?>"><i
													class="glyphicon glyphicon-minus-sign pull-left"></i></a>

										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</fieldset>
				<?php do_action("form_field_".__CLASS__."_settings",$fieldindex, $fieldid, $field_infos); ?>
				<?php do_action("form_field_settings",$fieldindex, $fieldid, $field_infos); ?>
			</div>
		</li>
		<?php
		$field_settings_html = ob_get_clean();
		return $field_settings_html;
	}

	public function field_preview_html($params = array()) {
		ob_start();
		?>
		Number field sum
		<?php
		$field_render_html = ob_get_clean();
		return $field_render_html;
	}

	public function field_render_html($params = array()) {
		ob_start();
		$condition_fields = '';
		$cond_action = '';
		$cond_boolean = '';
		if (isset($params['condition']) and isset($params['conditioned'])) {
			$cond_boolean = $params['condition']['boolean_op'];
			$cond_action = $params['condition']['action'];
			foreach($params['condition']['field'] as $key => $value) {
				$field_id = $value;
				$field_op = $params['condition']['op'][$key];
				$field_value = $params['condition']['value'][$key];
				$condition_fields .= ($field_id.':'.$field_op.':'.$field_value . '|');
			}
			$condition_fields = rtrim($condition_fields, '|');
		}
		?>
		<div id="<?php echo $params['id'] ?>" class='form-group <?php if (isset($params['conditioned'])) echo " conditioned hide "?>' data-cond-fields="<?php echo $condition_fields ?>" data-cond-action="<?php echo $cond_action.':'.$cond_boolean ?>" >
			<label style="display:block; clear:both"><?php echo $params['label'] ?><span class="note"><?php echo $params['note']; ?></span></label>
			<div class='form-group' id='mathresult_<?php echo $params['id'] ?>' ></div>
		</div>
		<script type='text/javascript'>
			function calculate() {
				var math_op = '<?php echo $params['operator'] ?>';
				var mathres = 0;
				var field1 = jQuery('#<?php echo $params['fields'][0] ?> input[type=number]');
				var field2 = jQuery('#<?php echo $params['fields'][1] ?> input[type=number]');
				if (field1.length > 0 && field2.length > 0) {
					val1 = parseInt(jQuery(field1).val());
					val2 = parseInt(jQuery(field2).val());
					switch (math_op) {
						case 'add':
							mathres = val1 + val2;
							break;
						case 'subtract':
							mathres = val1 - val2;
							break;
						case 'multiply':
							mathres = val1 * val2;
							break;
						case 'divide':
							mathres = val1 / val2;
							break;
					}
				}
				jQuery('#mathresult_<?php echo $params['id'] ?>').html(mathres);
			}
			jQuery(document).ready(function($){
				jQuery('#<?php echo $params['fields'][0] ?>,#<?php echo $params['fields'][1] ?>').on('change', function($){
					calculate();
				});
				jQuery('#<?php echo $params['fields'][0] ?>,#<?php echo $params['fields'][1] ?>').on('keyup', function($){
					calculate();
				});

			});
		</script>
		<?php
		$field_render_html = ob_get_clean();
		return $field_render_html;
	}

	public function configuration_template() {
		ob_start();
		?>
	<script type='text/x-mustache' id="template-Mathresult">
		<li class="list-group-item" data-type="<?php echo __CLASS__ ?>" id="field_{{ID}}"><input type="hidden" name="contact[fields][{{ID}}]" value="{{value}}">
			<span id="label_{{ID}}">Math result</span>
			<a href="#" rel="field_{{ID}}" class="remove"><i class="fa fa-trash-o pull-right"></i></a>
			<a href="#" class="cog-trigger" rel="#cog_{{ID}}"><i class="fa fa-wrench pull-right button-buffer-right"></i></a>

			
			<div class="cog" id="cog_{{ID}}" style="display: none;">
				<fieldset>
					<h5>Settings</h5>
					<div class="form-group">
						<label>Label:</label>
						<input class="form-control form-field-label" data-target="#label_{{ID}}" type="text"
							   value="Math result"
							   name="contact[fieldsinfo][{{ID}}][label]"/>
					</div>
					<div class='form-group'>
						<label>Field 1</label>
						<select name='contact[fieldsinfo][{{ID}}][fields][0]' data-selection=''  class='form-control math-field-variable'>
							<option value=''>Select first field</option>
						</select>
					</div>
					<div class='form-group'>
						<label>Field 2</label>
						<select name='contact[fieldsinfo][{{ID}}][fields][1]' data-selection='' class='form-control math-field-variable'>
							<option value=''>Select second field</option>
						</select>
					</div>
					<div class='form-group'>
						<label>Operator</label>
						<select name='contact[fieldsinfo][{{ID}}][operator]' class='form-control math-field-operator'>
							<option value=''>Select an operator</option>
							<option value='add'>Add</option>
							<option value='subtract'>Subtract</option>
							<option value='multiply'>Multiply</option>
							<option value='divide'>Divide</option>
						</select>
					</div>
					<div class="form-group">
						<label>Note</label>
						<textarea class="form-control" type="text" value=""
								  name="contact[fieldsinfo][{{ID}}][note]"></textarea>
					</div>
					<div class='form-group'>
						<label><input rel='condition-params' class='cond' type='checkbox' name='contact[fieldsinfo][{{ID}}][conditioned]' value='1'/> Conditional logic</label>
						<div id="cond_{{ID}}" class='cond-params' style='display:none'>
							<div class='form-group'>
								<div class="row row-bottom-buffer">
									<div class="col-md-12">
										<select class="select" name="contact[fieldsinfo][{{ID}}][condition][action]">
											<option value="show">Show</option>
											<option value="hide">Hide</option>
										</select> 
										this field if
										<select class="select" name="contact[fieldsinfo][{{ID}}][condition][boolean_op]">
											<option value="show">All</option>
											<option value="hide">Any</option>
										</select>
										of these conditions are met
									</div>
								</div>
								<div class='row row-bottom-buffer' rel='row'>
									<div class='col-md-4'>
										<select class='form-control cond-field-selector' data-selection='' name='contact[fieldsinfo][{{ID}}][condition][field][]'>
											<option value="">Select a field</option>
										</select>
									</div>
									<div class='col-md-3'>
										<select class='form-control cond-operator' name='contact[fieldsinfo][{{ID}}][condition][op][]'>
											<option value='is'>Is</option>
											<option value='is-not'>Is not</option>
											<option value='less-than'>Less than</option>
											<option value='greater-than'>Greater than</option>
											<option value='contains'>Contains</option>
											<option value='starts-with'>Starts with</option>
											<option value='ends-with'>Ends with</option>
										</select>
									</div>
									<div class='col-md-4'>
										<select class='form-control is-cond-selector'>
											<option value='email'>Email</option>
											<option value='phone'>Phone</option>
										</select>
										<input type='text' class='form-control is-cond-text hide' placeholder='Enter a value' value=''/>
										<input type='hidden' value='' class='is-cond-data' name='contact[fieldsinfo][{{ID}}][condition][value][]'/>
									</div>
									<div class="col-md-1">
										<a href="#" class="add-cond-option"
										   rel="{{ID}}"><i
												class="glyphicon glyphicon-plus-sign pull-left"></i></a>
										<a href="#" class="del-cond-option"
										   rel="{{ID}}"><i
												class="glyphicon glyphicon-minus-sign pull-left"></i></a>

									</div>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
				<?php do_action("form_field_".__CLASS__."_settings_template"); ?>
				<?php do_action("form_field_settings_template"); ?>
			</div>
			<div class="field-preview">
				<?php echo self::field_preview_html() ?>
			</div>
		</li>
	</script>
	<script type='text/javascript'>
		jQuery(document).ready(function($){
			var form_selections = "<option value=''>Selected a field</option>";
			$('#selectedfields-list .fl-field').each(function(){
				this_id = $(this).attr('id');
				if (this_id != '{{INDEX}}' && $('#'+this_id+'_CLASS').attr('rel') == 'Number') {
					form_selections += 	"<option value='"+this_id+"'>"+$('#'+this_id+'_LABEL').attr('rel')+"</option>";
				}
			});
			$('.math-field-variable').each(function () {
				$(this).html(form_selections);
				var preselected_var = $(this).attr('data-selection');
				$(this).children().each(function(){
					if ($(this).attr('value') == preselected_var)
						$(this).attr('selected', 'selected');
				});
			});
			$('#selectedfields-list').bind('DOMNodeInserted DOMNodeRemoved DOMSubtreeModified', throttle( function(){
				var form_selections = "<option value=''>Selected a field</option>";
				$('#selectedfields-list .fl-field').each(function(){
					this_id = $(this).attr('id');
					if (this_id != '{{INDEX}}' && $('#'+this_id+'_CLASS').attr('rel') == 'Number') {
						form_selections += 	"<option value='"+this_id+"'>"+$('#'+this_id+'_LABEL').attr('rel')+"</option>";
					}
				});
				$('.math-field-variable').each(function () {
					$(this).html(form_selections);
					var preselected_var = $(this).attr('data-selection');
					$(this).children().each(function(){
						if ($(this).attr('value') == preselected_var)
							$(this).attr('selected', 'selected');
					});
				});
			}, 50));
		});
	</script>
		<?php
		$field_configuration_template = ob_get_clean();
		return $field_configuration_template;
	}

	function process_field() {
		
	}

}
