<?php
class Pageseparator {

	public function control_button() {
		ob_start();
		?>
		<li class="list-group-item" data-type="<?php echo __CLASS__ ?>" for="Pageseparator">
			<span class="lfi lfi-name"><i class="fa fa-files-o"></i></span> Page Break
			<a title="Pageseparator" rel="Pageseparator" class="add pull-right add-form-field" data-template='Pageseparator' href="#"><i class="fa fa-plus-circle ttipf" title=""></i></a>
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
			<span id="label_<?php echo $fieldindex; ?>"><?php echo "[Separator] " . $field_infos[$fieldindex]['label'] ?>:</span>
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
						<input class="form-control form-field-label" data-target="#label_<?php echo $fieldindex; ?>" type="text" value="<?php echo $field_infos[$fieldindex]['label'] ?>" data-field-type="separator" name="contact[fieldsinfo][<?php echo $fieldindex ?>][label]" />
					</div>
					<div class="form-group">
						<label>Note</label>
						<textarea class="form-control" type="text" value="" name="contact[fieldsinfo][<?php echo $fieldindex ?>][note]"><?php echo $field_infos[$fieldindex]['note'] ?></textarea>
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
		[Separator] 
		<?php
		$field_render_html = ob_get_clean();
		return $field_render_html;
	}

	public function field_render_html($params = array()) {
		ob_start();
		if (isset($params['label']))	
			$separator_html = $params['label'];
		else if (is_string($params))
			$separator_html = $params;
		?>
		<?php echo $separator_html ?>
		<?php
		$field_render_html = ob_get_clean();
		return $field_render_html;
	}

	public function configuration_template() {
		ob_start();
		?>
	<script type="text/x-mustache" id="template-Pageseparator">
		<li class="list-group-item" data-type="<?php echo __CLASS__ ?>" id="field_{{ID}}"><input type="hidden" name="contact[fields][{{ID}}]" value="{{value}}">
			<span id="label_{{ID}}">[Separator] Page separator</span>
			<a href="#" rel="field_{{ID}}" class="remove"><i class="fa fa-trash-o pull-right"></i></a>
			<a href="#" class="cog-trigger" rel="#cog_{{ID}}"><i class="fa fa-wrench pull-right button-buffer-right"></i></a>

			
			<div class="cog" id="cog_{{ID}}" style="display: none;">
				<fieldset>
					<h5>Settings</h5>

					<div class="form-group">
						<label>Label:</label>
						<input class="form-control form-field-label" data-field-type="separator" data-target="#label_{{ID}}" type="text"
							   value="{{title}}"
							   name="contact[fieldsinfo][{{ID}}][label]"/>
					</div>
					<div class="form-group">
						<label>Note</label>
						<textarea class="form-control" type="text" value=""
								  name="contact[fieldsinfo][{{ID}}][note]"></textarea>
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
		<?php
		$field_configuration_template = ob_get_clean();
		return $field_configuration_template;
	}

	function process_field() {
		
	}

}
