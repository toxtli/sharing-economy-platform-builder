<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once FMERA_PLUGIN_DIR . 'admin/class-fme-registration-attributes-admin.php';
$manage_fields = new FME_Registration_Attributes_Admin();

$billing_fields = $manage_fields->get_reg_fields();

?>
<div class="wrap">
	<h2><?php _e('Registration Attributes','fmera'); ?></h2>

	<h3><?php _e('Registration Form','fmera'); ?></h3>

	<div class="div.widget-liquid-left">
		<div class="form-left">
			<h3>Form Fields</h3>
			<div class="shop-container" id="bdrag">
				<ul>
				
					<!--Text Field-->
					<li id="bf" class="bf ui-state-default widget draggable">
					  	<div id="bwt" class="widget-top">
					  		<div class="widget-title-action">
								<a href="#available-widgets" class="widget-action"></a>
							</div>
					  		<div class="widget-title ui-sortable-handle"><h4>Text<span class="in-widget-title"></span></h4></div>
					  		<input type="hidden" name="fieldtype" value="text" id="fieldtype" />
					  		<input type="hidden" name="type" value="registration" id="type" />
					  		<input type="hidden" name="label" value="Text" id="label" />
					  		<input type="hidden" name="name" value="registration_text" id="name" />
					  		<input type="hidden" name="mode" value="registration_additional" id="mode" />
					  	</div>
					  	<div id="bw" class="widget-inside win">

					  		<p><label for="label">Label:</label>
					  		<input type="text" value="" name="fieldlabel[]" class="widefat"></p>

					  		<p><label for="required">Required:</label>
					  		<input type="checkbox" value="1" name="fieldrequired[]" class="widefat"></p>

					  		<p><label for="hide">Hide:</label>
					  		<input  type="checkbox" value="1" name="fieldhide[]" class="widefat"></p>

					  		<p><label for="placeholder">Placeholder:</label>
					  		<input type="text" value="" name="fieldplaceholder[]" class="widefat"></p>

					  		<p><label for="width">Field Width:</label> 
					  			<select name="fieldwidth[]" class="widefat">
					  				<option  value="full">Full Width</option>
					  				<option  value="half">Half Width</option>
					  			</select>
					  		</p>

					  		<p id="textapp"></p>
					  		<input type="hidden" name="fieldids[]" value="" id="fieldids" />

					  		

					  	</div>
					</li>

					<!-- Text Area Field-->
					<li id="bf" class="bf ui-state-default widget draggable">
					  	<div id="bwt" class="widget-top">
					  		<div class="widget-title-action">
								<a href="#available-widgets" class="widget-action"></a>
							</div>
					  		<div class="widget-title ui-sortable-handle"><h4>Textarea<span class="in-widget-title"></span></h4></div>
					  		<input type="hidden" name="fieldtype" value="textarea" id="fieldtype" />
					  		<input type="hidden" name="type" value="registration" id="type" />
					  		<input type="hidden" name="label" value="Textarea" id="label" />
					  		<input type="hidden" name="name" value="registration_textarea" id="name" />
					  		<input type="hidden" name="mode" value="registration_additional" id="mode" />
					  	</div>
					  	<div id="bw" class="widget-inside win">

					  		<p><label for="label">Label:</label>
					  		<input type="text" value="" name="fieldlabel[]" class="widefat"></p>

					  		<p><label for="required">Required:</label>
					  		<input type="checkbox" value="1" name="fieldrequired[]" class="widefat"></p>

					  		<p><label for="hide">Hide:</label>
					  		<input  type="checkbox" value="1" name="fieldhide[]" class="widefat"></p>

					  		<p><label for="placeholder">Placeholder:</label>
					  		<input type="text" value="" name="fieldplaceholder[]" class="widefat"></p>

					  		<p><label for="width">Field Width:</label> 
					  			<select name="fieldwidth[]" class="widefat">
					  				<option  value="full">Full Width</option>
					  				<option  value="half">Half Width</option>
					  			</select>
					  		</p>

					  		<p id="textapp"></p>
					  		<input type="hidden" name="fieldids[]" value="" id="fieldids" />

					  		

					  	</div>
					</li>

					<!-- Select Box-->
					<li id="bf" class="bf ui-state-default widget draggable">
					  	<div id="bwt" class="widget-top">
					  		<div class="widget-title-action">
								<a href="#available-widgets" class="widget-action"></a>
							</div>
					  		<div class="widget-title ui-sortable-handle"><h4>Select Box<span class="in-widget-title"></span></h4></div>
					  		<input type="hidden" name="fieldtype" value="select" id="fieldtype" />
					  		<input type="hidden" name="type" value="registration" id="type" />
					  		<input type="hidden" name="label" value="Select Box" id="label" />
					  		<input type="hidden" name="name" value="registration_select" id="name" />
					  		<input type="hidden" name="mode" value="registration_additional" id="mode" />
					  	</div>
					  	<div id="bw" class="widget-inside win">

					  		<p><label for="label">Label:</label>
					  		<input type="text" value="" name="fieldlabel[]" class="widefat"></p>

					  		<p><label for="required">Required:</label>
					  		<input type="checkbox" value="1" name="fieldrequired[]" class="widefat"></p>

					  		<p><label for="hide">Hide:</label>
					  		<input  type="checkbox" value="1" name="fieldhide[]" class="widefat"></p>


					  		<p>
					  		<label for="options">Options:</label>

					  		<div class="field_wrapper">
								<div>
							    	<input class="opval" placeholder="Option Value" type="text" name="option_value[]" value=""/>
							    	<input class="opval" placeholder="Option Text" type="text" name="option_text[]" value=""/>
							    	<input id="option_field_ids" class="opval" placeholder="" type="hidden" name="option_field_ids[]" value=""/>
							        <a href="javascript:void(0);"  title="Add Option">
							        <img onClick="" class="add_button" src="<?php echo FMERA_URL; ?>images/add-icon.png"/></a>
							    </div>
							</div>

					  		
					  		</p>

					  		<p><label for="width">Field Width:</label> 
					  			<select name="fieldwidth[]" class="widefat">
					  				<option  value="full">Full Width</option>
					  				<option  value="half">Half Width</option>
					  			</select>
					  		</p>

					  		<p id="textapp"></p>
					  		<input type="hidden" value="noplaceholder" name="fieldplaceholder[]" class="widefat"></p>
					  		<input type="hidden" name="fieldids[]" value="" id="fieldids" />

					  		

					  	</div>
					 </li>

					 <!-- Multi Select Box-->
					 <li id="bf" class="bf ui-state-default widget">
					  	<div><h4 style="width:50%; float: left;">Multi Select Box<span class="in-widget-title"></span></h4>
						 <span style="width:35%; float: right; margin-top:16px; font-weight: bold; text-decoration: none;">
						 	<a href="https://www.fmeaddons.com/woocommerce-plugins-extensions/customer-registration-plugin.html" target="_blank">Upgrade to Premium</a>
						 </span>
					  	</div>
					 </li>

					 <!-- Check Box-->
					 <li id="bf" class="bf ui-state-default widget draggable">
					  	<div id="bwt" class="widget-top">
					  		<div class="widget-title-action">
								<a href="#available-widgets" class="widget-action"></a>
							</div>
					  		<div class="widget-title ui-sortable-handle"><h4>Checkbox<span class="in-widget-title"></span></h4></div>
					  		<input type="hidden" name="fieldtype" value="checkbox" id="fieldtype" />
					  		<input type="hidden" name="type" value="registration" id="type" />
					  		<input type="hidden" name="label" value="Checkbox" id="label" />
					  		<input type="hidden" name="name" value="registration_checkbox" id="name" />
					  		<input type="hidden" name="mode" value="registration_additional" id="mode" />
					  	</div>
					  	<div id="bw" class="widget-inside win">

					  		<p><label for="label">Label:</label>
					  		<input type="text" value="" name="fieldlabel[]" class="widefat"></p>

					  		<p><label for="required">Required:</label>
					  		<input type="checkbox" value="1" name="fieldrequired[]" class="widefat"></p>

					  		<p><label for="hide">Hide:</label>
					  		<input  type="checkbox" value="1" name="fieldhide[]" class="widefat"></p>


					  		<p><label for="width">Field Width:</label> 
					  			<select name="fieldwidth[]" class="widefat">
					  				<option  value="full">Full Width</option>
					  				<option  value="half">Half Width</option>
					  			</select>
					  		</p>

					  		<p id="textapp"></p>
					  		<input type="hidden" value="noplaceholder" name="fieldplaceholder[]" class="widefat"></p>
					  		<input type="hidden" name="fieldids[]" value="" id="fieldids" />

					  		

					  	</div>
					</li>

					<!-- Radio Button-->

					<li id="bf" class="bf ui-state-default widget draggable">
					  	<div id="bwt" class="widget-top">
					  		<div class="widget-title-action">
								<a href="#available-widgets" class="widget-action"></a>
							</div>
					  		<div class="widget-title ui-sortable-handle"><h4>Radio Button<span class="in-widget-title"></span></h4></div>
					  		<input type="hidden" name="fieldtype" value="radioselect" id="fieldtype" />
					  		<input type="hidden" name="type" value="registration" id="type" />
					  		<input type="hidden" name="label" value="Radio Button" id="label" />
					  		<input type="hidden" name="name" value="registration_radio_select" id="name" />
					  		<input type="hidden" name="mode" value="registration_additional" id="mode" />
					  	</div>
					  	<div id="bw" class="widget-inside win">

					  		<p><label for="label">Label:</label>
					  		<input type="text" value="" name="fieldlabel[]" class="widefat"></p>

					  		<p><label for="required">Required:</label>
					  		<input type="checkbox" value="1" name="fieldrequired[]" class="widefat"></p>

					  		<p><label for="hide">Hide:</label>
					  		<input  type="checkbox" value="1" name="fieldhide[]" class="widefat"></p>

					  		<p>
				  				<label for="options">Options:</label>
				  				<div class="field_wrapper">
									<div style="width:100%; float:left">
					  					<input class="opval" placeholder="Option Value" type="text" name="option_value[]" value=""/>
								    	<input class="opval" placeholder="Option Text" type="text" name="option_text[]" value=""/>
								    	<input id="option_field_ids" class="opval" placeholder="" type="hidden" name="option_field_ids[]" value=""/>
					  				</div>
					  				<div style="width:100%; float:left">
					  					<input class="opval" placeholder="Option Value" type="text" name="option_value[]" value=""/>
								    	<input class="opval" placeholder="Option Text" type="text" name="option_text[]" value=""/>
								    	<input id="option_field_ids" class="opval" placeholder="" type="hidden" name="option_field_ids[]" value=""/>
					  				</div>

				  				</div>
				  			</p>


					  		<p><label for="width">Field Width:</label> 
					  			<select name="fieldwidth[]" class="widefat">
					  				<option  value="full">Full Width</option>
					  				<option  value="half">Half Width</option>
					  			</select>
					  		</p>

					  		<p id="textapp"></p>
					  		<input type="hidden" value="noplaceholder" name="fieldplaceholder[]" class="widefat"></p>
					  		<input type="hidden" name="fieldids[]" value="" id="fieldids" />

					  		

					  	</div>
					 </li>

					 <!-- Date Picker-->
					 <li id="bf" class="bf ui-state-default widget">
					  	<div><h4 style="width:50%; float: left;">Date Picker<span class="in-widget-title"></span></h4>
						 <span style="width:35%; float: right; margin-top:16px; font-weight: bold; text-decoration: none;">
						 	<a href="https://www.fmeaddons.com/woocommerce-plugins-extensions/customer-registration-plugin.html" target="_blank">Upgrade to Premium</a>
						 </span>
					  	</div>
					 </li>

					 <!-- Time Picker-->
					 <li id="bf" class="bf ui-state-default widget">
					  	<div><h4 style="width:50%; float: left;">Time Picker<span class="in-widget-title"></span></h4>
						 <span style="width:35%; float: right; margin-top:16px; font-weight: bold; text-decoration: none;">
						 	<a href="https://www.fmeaddons.com/woocommerce-plugins-extensions/customer-registration-plugin.html" target="_blank">Upgrade to Premium</a>
						 </span>
					  	</div>
					 </li>

					 <!-- Password -->
					 <li id="bf" class="bf ui-state-default widget">
					  	<div><h4 style="width:50%; float: left;">Password<span class="in-widget-title"></span></h4>
						 <span style="width:35%; float: right; margin-top:16px; font-weight: bold; text-decoration: none;">
						 	<a href="https://www.fmeaddons.com/woocommerce-plugins-extensions/customer-registration-plugin.html" target="_blank">Upgrade to Premium</a>
						 </span>
					  	</div>
					 </li>

					 <!-- Image / File Upload -->
					 <li id="bf" class="bf ui-state-default widget">
					  	<div><h4 style="width:50%; float: left;">File / Image Upload<span class="in-widget-title"></span></h4>
						 <span style="width:35%; float: right; margin-top:16px; font-weight: bold; text-decoration: none;">
						 	<a href="https://www.fmeaddons.com/woocommerce-plugins-extensions/customer-registration-plugin.html" target="_blank">Upgrade to Premium</a>
						 </span>
					  	</div>
					 </li>

				</ul>
			</div>	
		</div>

		<div class="form-right">
            <h3>Registration Form Fields</h3>
            <div id="bfields">
            	<form method="post" action="" id="savefields" accept-charset="utf-8">
            		<ul id="sortable" class="sortable">
        				<?php foreach ($billing_fields as $billing_field) { ?>
					  	
					  <li id="<?php echo $billing_field->field_id; ?>" class="ui-state-default widget">
					  	<div id="bwt<?php echo $billing_field->field_id; ?>" class="widget-top">
					  		<div class="widget-title-action">
								<a href="#available-widgets" class="widget-action"></a>
							</div>
					  		<div class="widget-title ui-sortable-handle"><h4><?php echo $billing_field->field_label; ?><span class="in-widget-title"></span></h4></div>
					  	</div>
					  	<div id="bw<?php echo $billing_field->field_id; ?>" class="widget-inside win">

					  		<p><label for="label">Label:</label>
					  		<input type="text" value="<?php echo $billing_field->field_label; ?>" name="fieldlabel[]" class="widefat"></p>

					  		<p><label for="required">Required:</label>
					  		<input <?php checked($billing_field->is_required,1); ?> type="checkbox" value="1" name="fieldrequired[]" class="widefat"></p>

					  		<p><label for="hide">Hide:</label>
					  		<input <?php checked($billing_field->is_hide,1); ?> type="checkbox" value="1" name="fieldhidden[]" class="widefat"></p>

					  		<?php if(($billing_field->field_type == 'select') && ($billing_field->field_mode == 'registration_additional')) { ?>
					  		
					  			<p>
						  		<label for="options">Options:</label>
						  		<div class="field_wrapper">
									<div style="width:100%; float:left">

								        <a href="javascript:void(0);"  title="Add Option">
								        <img style="float:right; clear:both" onClick="getdata('<?php echo $billing_field->field_id; ?>')" id="<?php echo $billing_field->field_id; ?>" class="add_button" src="<?php echo FMERA_URL; ?>images/add-icon.png"/></a>
								    </div>

										<?php 
											$options = $manage_fields->getOptions($billing_field->field_id);
											$a = 1;
											foreach ($options as $option) {
												

										?>
								  		 <div style="width:100%; float:left" id="b<?php echo $a; ?>">
								    	<input class="opval" placeholder="Option Value" type="text" name="option_value[]" value="<?php echo $option->meta_key; ?>"/>
								    	<input class="opval" placeholder="Option Text" type="text" name="option_text[]" value="<?php echo $option->meta_value; ?>"/>
								    	<input id="option_field_ids" class="opval" placeholder="" type="hidden" name="option_field_ids[]" value="<?php echo $billing_field->field_id; ?>"/>
								        <a href="javascript:void(0);" class="remove_bt"  title="Remove Option">
								        <img onClick="deldata('b<?php echo $a; ?>')"  class="remove_button" src="<?php echo FMERA_URL; ?>images/remove-icon.png"/></a>
								        </div>
								        <?php $a++;  } ?>
								    
								</div>
						  		</p>
						  		<input type="hidden" value="noplaceholder" name="fieldplaceholder[]" class="widefat"></p>

					  		<?php } else if($billing_field->field_type == 'radioselect' && $billing_field->field_mode == 'registration_additional') { ?>

					  			<p>
					  				<label for="options">Options:</label>
					  				<div class="field_wrapper">
					  					<?php 
											$options = $manage_fields->getOptions($billing_field->field_id);
											$a = 1;
											foreach ($options as $option) {
												

										?>
										<div style="width:100%; float:left">
						  					<input class="opval" placeholder="Option Value" type="text" name="option_value[]" value="<?php echo $option->meta_key; ?>"/>
									    	<input class="opval" placeholder="Option Text" type="text" name="option_text[]" value="<?php echo $option->meta_value; ?>"/>
									    	<input id="option_field_ids" class="opval" placeholder="" type="hidden" name="option_field_ids[]" value="<?php echo $billing_field->field_id; ?>"/>
						  				</div>
						  				<?php } ?>
						  				
					  				</div>
					  				<input type="hidden" value="noplaceholder" name="fieldplaceholder[]" class="widefat">
					  			</p>

					  		<?php } else if($billing_field->field_type == 'checkbox' && $billing_field->field_mode == 'registration_additional') { ?>

					  		<input type="text" value="noplaceholder" name="fieldplaceholder[]" class="widefat"></p>

					  		<?php } else { ?>

					  			<p><label for="placeholder">Placeholder:</label>
					  			<input type="text" value="<?php echo $billing_field->field_placeholder; ?>" name="fieldplaceholder[]" class="widefat"></p>

					  		<?php } ?>


					  		<p><label for="width">Field Width:</label> 
					  			<select name="fieldwidth[]" class="widefat">
					  				<option <?php selected($billing_field->width,'full'); ?> value="full">Full Width</option>
					  				<option <?php selected($billing_field->width,'half'); ?> value="half">Half Width</option>
					  			</select>
					  		</p>


					  		<p>
					  			<?php if($billing_field->field_mode == 'registration_additional') { ?>
					  			<a onClick="deleteDiv('<?php echo $billing_field->field_id; ?>','<?php echo $billing_field->field_label; ?>')" class="widget-control-remove" href="javascript:void(0)">Delete</a>
									|
								<?php } ?>
								<a onClick="closeDiv('<?php echo $billing_field->field_id; ?>')" class="widget-control-close" href="javascript:void(0)">Close</a>
					  		</p>

					  		<input type="hidden" value="<?php echo $billing_field->field_id; ?>" name="fieldids[]" class="widefat"></p>
					  		

					  	</div>
					  </li>
					  <?php } ?>
        			</ul>
            	</form>
            </div>
        </div>


	</div>

	<div class="savebt">
		<input type="button" onClick="savedata()" value="Save Changes" class="button button-primary widget-control-save right" id="widget-archives-2-savewidget" name="savewidget"><span class="spinner"></span>
	</div>
	
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		$( "#sortable" ).sortable({ revert: true, update: function( event, ui ) {

				var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
				var order = $(this).sortable('toArray');
				jQuery.ajax({
				type: 'POST',   // Adding Post method
				url: ajaxurl, // Including ajax file
					data: {"action": "update_sortorder","fieldids":order}, // Sending data dname to post_word_count function.
					success: function(data){ 
					}
				});                                                            

			}  

		});

	});

	jQuery(document).ready(function($) {

		$( "#bdrag .draggable" ).draggable({ 

			connectToSortable: "#sortable",
            helper: "clone",
            revert: "invalid",
            start : function(event, ui) {
			      ui.helper.width('300');
			  },
            stop: function(event, ui) {

            	$("#sortable .draggable").attr( "style", "" );
            	$("#sortable .draggable").attr( "id", "newfield" );

            	$('#newfield #bf').removeClass('ui-state-default widget').addClass('ui-state-default widget open');
			    $("#newfield  #bw").slideDown('slow');
            	$('#newfield #bwt').toggle(function(){
			       $('#newfield #bf').removeClass('ui-state-default widget').addClass('ui-state-default widget open');
			       $("#newfield  #bw").slideDown('slow');
			   	},function(){
			   	$('#newfield  #bf').removeClass('ui-state-default widget open').addClass('ui-state-default widget');
			       $("#newfield  #bw").slideUp('slow');
			   	});

			   	var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
				var fieldtype = $("#newfield #fieldtype").val();
				var type = $("#newfield #type").val();
				var label = $("#newfield #label").val();
				var name = $("#newfield #name").val();
				var mode = $("#newfield #mode").val();

				

				jQuery.ajax({
				type: 'POST',   // Adding Post method
				url: ajaxurl, // Including ajax file
				data: {"action": "insert_field","fieldtype":fieldtype,"type":type,"label":label,"name":name,"mode":mode}, // Sending data dname to post_word_count function.
				dataType: 'json',
				success: function(data) {

					if($("#sortable .draggable").attr( "id" ) == 'newfield') {
						$('#sortable #newfield').attr( 'id', data );
					}
					$('#sortable #'+data).attr('class', '');
					$('#sortable #'+data).attr('class', 'ui-state-default widget ui-sortable-handle');
					$('#sortable #'+data+' #textapp').html('');
					$('#sortable #'+data+' #textapp').append("<a onClick='deleteDiv("+data+","+data+")' class='widget-control-remove' href='javascript:void(0)'>Delete</a> | <a onClick='closeDiv("+data+")' class='widget-control-close' href='javascript:void(0)'>Close</a>");
					$('#sortable #'+data+' #bwt').attr('id','bwt'+data);
					$('#sortable #'+data+' #bw').attr('id','bw'+data);
					$('#sortable #'+data+' img').attr('id','ai'+data);
					$('#sortable #'+data+' img').attr('onClick','getdata('+data+')');
					$('#sortable #'+data+' #option_field_ids').val(data);
					$('#sortable #'+data+' #fieldids').val(data);


					$('#bwt'+data).toggle(function(){ 
				       $('#'+data).removeClass('ui-state-default widget').addClass('ui-state-default widget open');
				       $("#bw"+data).slideDown('slow');
				       
				   },function(){
				   	$('#'+data).removeClass('ui-state-default widget open').addClass('ui-state-default widget');
				       $("#bw"+data).slideUp('slow');
				   });




				}
				});
            }
		});
	});

	jQuery(document).ready(function($) { 
    	
    	<?php foreach ($billing_fields as $billing_field) { ?>
	   $('#bwt<?php echo $billing_field->field_id; ?>').toggle(function(){ 
	       $('#<?php echo $billing_field->field_id; ?>').removeClass('ui-state-default widget').addClass('ui-state-default widget open');
	       $("#bw<?php echo $billing_field->field_id; ?>").slideDown('slow');
	       
	   },function(){
	   	$('#<?php echo $billing_field->field_id; ?>').removeClass('ui-state-default widget open').addClass('ui-state-default widget');
	       $("#bw<?php echo $billing_field->field_id; ?>").slideUp('slow');
	   });

	   <?php } ?>
	});

	function deleteDiv(field_id,field_label) { 
	var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
	if(confirm("Are you sure to delete "+field_label+" field?"))
		{
			jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: {"action": "del_field", "field_id":field_id, "delsecurity":'<?php echo wp_create_nonce( "fmeradel-ajax-nonce" ); ?>'},
			success: function() {

				jQuery("#"+field_id).fadeOut('slow');
				jQuery("#"+field_id).remove();

			}
			});

		}
	return false;
	}

	function closeDiv(field_id) { 

		jQuery('#'+field_id).removeClass('ui-state-default widget open').addClass('ui-state-default widget');
	    jQuery("#bw"+field_id).slideUp('slow');
	}

	function savedata() { 
		jQuery('#savefields').find(':checkbox:not(:checked)').attr('value', '0').prop('checked', true);
		jQuery('#ssavefields').find(':checkbox:not(:checked)').attr('value', '0').prop('checked', true);
		jQuery('#asavefields').find(':checkbox:not(:checked)').attr('value', '0').prop('checked', true);
		var data2 = jQuery('#savefields').serialize();
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';

		jQuery.ajax({
		    type: 'POST',
		    url: ajaxurl,
		    data: data2 + '&action=save_all_data&security=<?php echo wp_create_nonce( "fmera-ajax-nonce" ); ?>',
		    success: function() {
		        window.location.reload(true);

		    }
		});
	}

	function getdata(id) {
		var maxField = 10000; //Input fields increment limitation
	
	
		var x = 1; //Initial field counter is 1
	 	//Once add button is clicked
		//var id = this.id; alert(id);
		var wrapper = jQuery('#'+id+' .field_wrapper'); //Input field wrapper
		var fieldHTML = '<div><input class="opval" placeholder="Option Value" type="text" name="option_value[]" value=""/><input class="opval opval2" placeholder="Option Text" type="text" name="option_text[]" value=""/><a href="javascript:void(0);" class="remove_bt"  title="Remove Option"><input class="opval" placeholder="" type="hidden" name="option_field_ids[]" value="'+id+'"/><img class="remove_button" src="<?php echo FMERA_URL; ?>images/remove-icon.png"/></a></div>'; //New input field html 
		if(x < maxField){ //Check maximum number of input fields
			x++; //Increment field counter
			jQuery(wrapper).append(fieldHTML); // Add field html
		}
		jQuery(wrapper).on('click', '.remove_bt', function(e){ //Once remove button is clicked
		e.preventDefault();
		jQuery(this).parent('div').remove(); //Remove field html
		x--; //Decrement field counter
		});
		

	}

	function deldata(id) {
		jQuery("#"+id).remove();	
	}

</script>