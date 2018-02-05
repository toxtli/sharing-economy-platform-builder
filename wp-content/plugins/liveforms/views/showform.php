<?php
	/**
	 * @variable    $formsetting
	 * @uses        Contains form element configurations
	 * @origin      Controller: contactforms, Method: showform()
	 */
	if(!defined('ABSPATH')) die('!');
	$url = get_permalink(isset($form_id) ? $form_id : get_the_ID());
	$sap = strpos($url, "?") ? "&" : "?";
	$purl = $url . $sap;
	$id = uniqid();
	?>

	<!-- Start form -->
    <div class="w3eden">

        <div id="liveform_container_<?php echo get_the_ID() ?>">

			<div class="row">
				<div class="col-md-12">


						<?php if (count($form_parts_names) > 1) { ?>
					<ul class="nav nav-wizard liveforms-nav-wizard nav-justified">
						<?php foreach ($form_parts_names as $index => $crumb_text) { ?>
						<li id='<?php echo $index . "_crumb" ?>' class="<?php if ($index == "form_part_0") echo "active visited"; else echo "disabled"; ?>" data-part="<?php echo $index ?>">
							<a href="#<?php echo $index ?>" data-toggle="tab"><?php echo $crumb_text ?></a>
						</li>

							<?php } ?>
					</ul>
							<div style="margin-bottom: 10px;clear: both"></div>
						<?php } ?>




					<div class="btn-group btn-breadcrumb hide">
						<?php if (count($form_parts_names) > 1) { ?>
							<?php foreach ($form_parts_names as $index => $crumb_text) { ?>
								<li id='<?php echo $index . "_crumb" ?>' class='btn btn-default <?php if ($index == "form_part_0") echo "active visited" ?>' data-part="<?php echo $index ?>"><a disabled='disabled' class="breadcrumbs" href="" onclick='return false'><?php echo $crumb_text ?></a></li>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
			<div id="formarea">
				<form id="form-<?php echo $id; ?>"  action="" method="post" enctype="multipart/form-data" >
					<?php wp_nonce_field(NONCE_KEY, '__isliveforms'); ?>
					<input type="hidden" id="formid" name="form_id" value="<?php echo $form_id ?>" />
					<?php

					//do something
					do_action('liveform-showform_before_form_fields', $form_id);
					$part_count = 0;
					echo "<div class='tab-content'>";
					foreach ($form_parts_html as $part) {
						?> <!-- part <?php echo $part_count ?> start --> <?php
						echo $part;
						?> <!-- part <?php echo $part_count ?> end --> <?php
						$part_count++;
					}

					echo "</div>";

					//do something
					do_action('liveform-showform_after_form_fields', $form_id);
					?>
				</form>
			</div>
        </div>
    </div>
	<!-- End form -->

    <script type='text/javascript'>

		function validateForm() {
			var validator = jQuery('#form-<?php echo $id; ?>').validate();
			var $form = jQuery('#form-<?php echo $id; ?>').find('input,select,textarea');
			var validForm = true;
			$form.each(function() {
				if (!validator.element(this)) {
					validForm = false;
					jQuery(this).parent('.form-group').removeClass('has-success').addClass('has-error');
				} else {
					jQuery(this).parent('.form-group').removeClass('has-error').addClass('has-success');
				}
			});
			return validForm;
		}

		jQuery(document).ready(function($){
		jQuery(function($) {
			var submit_btn_text;
			var next_part_id;
			var this_part_id;
			$(document).ready(function() { //code 
				// Show hard form partitions
				var set_show = {display: 'block'};
				var set_hide = {display: 'none'};
				var validator = $('#form-<?php echo $id; ?>').validate();
				var validInput = true;

				$('.nav-wizard li a').on('click', function () {
					if($(this).parent('li').hasClass('disabled'))
						return false;
				});

				$('input,select,textarea').on('change', function () {
					var validator = jQuery('#form-<?php echo $id; ?>').validate();
					if (!validator.element(this)) {
						$(this).parent('.form-group').removeClass('has-success').addClass('has-error');
					} else {
						$(this).parent('.form-group').removeClass('has-error').addClass('has-success');
					}
				});

				//$('#form_part_0').css(set_show);
				$('.change-part').on('click', function() {
					next_part_id = $(this).attr('data-next');
					this_part_id = $(this).attr('data-parent');


					// Pre validate
					validInput = true;
					var $inputs = $('#' + this_part_id).find("input,select,textarea");
                    $inputs.each(function() {
                        if (!validator.element(this)) {
                            validInput = false;
                            $(this).parent('.form-group').removeClass('has-success').addClass('has-error');
                        } else {
								$(this).parent('.form-group').removeClass('has-error').addClass('has-success');
                        }
                    });

					if (validInput == true) {
						$('.liveforms-nav-wizard li').removeClass('active');
						$('#' + next_part_id + '_crumb').removeClass('disabled').addClass('active');
						$(this).parent('.form-group').removeClass('has-error').addClass('has-success');
						$('#' + this_part_id).removeClass('active');
						$('#' + next_part_id).addClass('active');

					} else {
						//msgs = new Array();
						//msgs.push("Please fill this section properly before proceeding");
						//showAlerts(msgs,'danger');
					}
				});


				// ajax submit
				var options = {
					url: '<?php echo $purl ?>action=submit_form',
					resetForm: true,
					beforeSubmit: function() {
						submit_btn_text = $('#submit').html();
						$('#submit').html("<i id='spinner' class='fa fa-refresh fa-spin'></i> Please Wait...");
					}, // pre-submit callback
					success: function(response) {
						console.log(response);
						var h = $('#formarea').height();
						var checkmark = '<div class="text-center"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg></div>';
						var creditcard = '<div class="text-center">  <svg width="100%" height="100%" viewBox="0 0 400 400" class="cc"><path id="wallet" d="M359.324,142.277c-1.872,-9.056 -7.494,-16.715 -15.134,-21.216c-4.654,-2.741 -10.057,-4.311 -15.817,-4.311l-260.175,0l-16.571,0c-17.455,0 -31.627,14.415 -31.627,32.169l0,188.963c0,15.932 15.005,28.868 33.487,28.868l293.026,0c18.482,0 33.487,-12.936 33.487,-28.868l0,-166.402c0,-11.784 -8.208,-21.929 -19.961,-26.412c-4.137,-1.579 -8.714,-2.456 -13.526,-2.456"/><clipPath id="_clip1"><rect x="30" y="8.25" width="340" height="130"/></clipPath><g clip-path="url(#_clip1)"><g id="card2"><path d="M240.5,59.128c0,-14.283 -11.595,-25.878 -25.878,-25.878l-118.244,0c-14.283,0 -25.878,11.595 -25.878,25.878l0,238.244c0,14.283 11.595,25.878 25.878,25.878l118.244,0c14.283,0 25.878,-11.595 25.878,-25.878l0,-238.244Z"/><path d="M101,33.25l0,290"/><path d="M121,33.25l0,290"/></g><g id="card1"><path d="M335,83.628c0,-14.283 -11.595,-25.878 -25.878,-25.878l-118.244,0c-14.283,0 -25.878,11.595 -25.878,25.878l0,238.244c0,14.283 11.595,25.878 25.878,25.878l118.244,0c14.283,0 25.878,-11.595 25.878,-25.878l0,-238.244Z"/><path d="M195,58.75l0,290"/><path d="M215,58.75l0,290"/></g></g><path id="slot" d="M346.513,142.612l-293.026,0"/> </svg></div>';
						var $formarea = $('#formarea').css({minHeight: h, width: '100%', display: 'flex'}).css('justify-content','center').css('align-items','center');
						msgs = new Array();
						$('#spinner').remove();
						$('#submit').html(submit_btn_text);
						if($('#formarea .tab-pane').size() > 1) {
							$('#' + this_part_id).css(set_hide);
							$('#form_part_0').css(set_show);
						}
						try {
							response_vars = JSON.parse(response);
						} catch (e) {
							console.log(e);
						}
						if (response_vars.action == 'success' && validInput === true) {
							//msgs.push(response_vars.message);
							//showAlerts(msgs, 'success');

							$formarea.html("<div class='alert alert-success text-center' style='display:inline-block;padding: 10px 30px 35px !important;box-shadow: none'>"+checkmark+response_vars.message+"</div>");
						} else {
							if(typeof(response_vars) != 'undefined' && response_vars.action=='payment'){
								//msgs.push('<?php echo str_replace("'", "\'", ($formsetting['thankyou'] == '' ? 'Form submitted succesfully' : $formsetting['thankyou'])) ?>');
								//showAlerts(msgs, 'success');
								$formarea.html("<div class='alert alert-success text-center' style='display:inline-block;padding: 10px 30px 35px !important;box-shadow: none'>"+creditcard+response_vars.paymentform+"</div>");
								//$formarea.html(response_vars.paymentform);
							} else {

								//msgs.push(response_vars.message == '' ? 'Form submission failed, please check the entries again' : response_vars.message);
								var msg = response_vars.message == '' ? 'Form submission failed, please check the entries again' : response_vars.message;
								//$formarea.css('align-items','center').html(msg);
								$formarea.html("<div class='alert alert-success text-center' style='display:inline-block;padding: 10px 30px 35px !important;box-shadow: none'>"+checkmark+msg+"</div>");
								//showAlerts(msgs, 'danger');
							}
						}
					}
				};
				$('#form-<?php echo $id; ?>').on('submit', function() {
					if(validateForm())
						$(this).ajaxSubmit(options);
					return false;
				});
			});
		});

		jQuery(document).ready(function($){
			$('.conditioned').each(function(){
				var cur_field_id = $(this).attr('id');
				cur_conditioned_fields = $(this).attr('data-cond-fields');
				cur_cond_fields = cur_conditioned_fields.split('|');
				for (i=0 ; i<cur_cond_fields.length ; i++) {
					var cond_field = cur_cond_fields[i].split(':');
					addConditionClass(jQuery('#'+cond_field[0]), cur_field_id);
//					$('#'+cond_field[0]).each(function(){
//						$(this).addClass('cond_filler_'+cur_field_id);
//						$(this).children().each(function(){
//							$(this).addClass('cond_filler_'+cur_field_id);
//						})
//					});
					
				}
				$('.cond_filler_'+cur_field_id).each(function(){
						if ($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio')
							$(this).on('change', function(){
								applyRule(cur_field_id);
							});
						else if ($(this).attr('type') == 'text')
							$(this).on('keyup', function(){
								applyRule(cur_field_id);
							});
						else
							$(this).on('change', function(){
								applyRule(cur_field_id);
							});
					});
			});
		});

        function showAlerts(msgs, type) {
            jQuery('.formnotice').slideUp();
            alert_box = '<div style="margin-top: 20px" class="alert formnotice alert-' + type + ' disappear"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
            for (i = 0; i < msgs.length; i++) {
                alert_box += '' + msgs[i] + '<br/>';
            }
            alert_box += '</div>';
            jQuery('#form-<?php echo $id; ?>').append(alert_box);

        }

		function addConditionClass(field_id, cond_class) {
			jQuery(field_id).each(function(){
				if (jQuery(this).is('input') || jQuery(this).is('select'))
					jQuery(this).addClass('cond_filler_'+cond_class);
				jQuery(this).children().each(function(){
					addConditionClass(jQuery(this), cond_class);
				})
			});
			return false;
		}

		function compareRule(objs, cmp_operator, cmp_value) {
			var comp_res = false;
			switch(cmp_operator) {
				case 'is':
					jQuery(objs).each(function(){
						if (jQuery(this).attr('type') == cmp_value) {
							comp_res = true;
							return;
						}
					});
					break;
				case 'is-not':
					jQuery(objs).each(function(){
						if (jQuery(this).attr('type') != cmp_value) {
							comp_res = true;
							return;
						}
					});
					break;
				case 'less-than':
					jQuery(objs).each(function(){
						if (jQuery(this).val() < cmp_value) {
							comp_res = true;
							return;
						}
					});
					break;
				case 'greater-than':
					jQuery(objs).each(function(){
						if (jQuery(this).val() > cmp_value) {
							comp_res = true;
							return;
						}
					});
					break;
				case 'starts-with':
					jQuery(objs).each(function(){
						if (jQuery(this).val().indexOf(cmp_value) == 0) {
							comp_res = true;
							return;
						}
					});
					break;
				case 'contains':
					jQuery(objs).each(function(){
						if (jQuery(this).val().indexOf(cmp_value) != -1) {
							comp_res = true;
							return;
						}
					});
					break;
				case 'ends-with':
					jQuery(objs).each(function(){
						indexPoint = (jQuery(this).val().length - cmp_value.length);
						if (jQuery(this).val().indexOf(cmp_value, indexPoint) == indexPoint) {
							comp_res = true;
							return;
						}
					});
					break;
				default:
					comp_res = false;
					break;

			}

			return comp_res;
		}

		function applyRule(field_id) {
			jQuery('.cond_filler_'+field_id).each(function(){
				var this_conditions = jQuery('#'+field_id).attr('data-cond-fields').split('|');
				var this_action = jQuery('#'+field_id).attr('data-cond-action').split(':');
				var cmp_res = this_action[1] == 'all' ? true : false;
				for (i=0 ; i<this_conditions.length ; i++) {
					var this_condition = this_conditions[i].split(':');
					cmp_id = this_condition[0];
					cmp_objs = null;
					if (cmp_id.indexOf('Checkbox_') == 0 || cmp_id.indexOf('Radio_') == 0) {
						cmp_objs = jQuery('#'+cmp_id).find(':checked');
					} else {
						cmp_objs = jQuery('#'+cmp_id).children();
					}
					cmp_operator = this_condition[1];
					cmp_value = this_condition[2];
					tmp_res = compareRule(cmp_objs, cmp_operator, cmp_value);
					if (this_action[1] == 'all') cmp_res = cmp_res && tmp_res;
					else cmp_res = cmp_res || tmp_res;
				}
				if (cmp_res == true) {
					jQuery('#'+field_id).removeClass('hide');
				} else {
					jQuery('#'+field_id).addClass('hide');
				}
			});
			
		}
		});
    </script>
	