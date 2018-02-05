<?php
class advancedfields {
    public static function file($params = array()) {         
        $input_field = "<input type='file' name='upload[{$params['id']}]'".required($params).">";
        return $input_field;
    }

    public static function captcha($params = array()) {
		$validation = "<script type='text/javascript'>"
						. "jQuery(document).ready(function(){\n"
						. "jQuery.validator.addMethod('captcha', function(value, element) {\n"
							. "return value == jQuery.session.get('captcha');\n"
						. "}, 'The captcha entry was incorrect');\n"
						. "jQuery('#custom_validation_rules').html(jQuery('#custom_validation_rules').html() + ':' + 'captcha');\n"
						. "});"
					. "</script>";
		$url = get_post_permalink();
		return '<div class="row"><div class="col-md-12 row-bottom-buffer"><img src="'.$url.'&show_captcha=1'.'"/></div></div><div class="row"><div class="col-md-12"><input type="text" placeholder="Enter captcha" class="form-control" required="required" data-required-message="You must enter the captcha" id="recaptcha_entry" name="recaptcha_entry"/></div></div>';
    }

    public static function fullname($params = array()) {
		$title = "<label for='title'>Title: </label><input class='form-control' type='text' name='submitform[".e($params['id'],"")."][title]' id='title' ".required($params)." /><br/>";
        $first_name = "<label for='first-name'>First name: </label><input class='form-control' type='text' name='submitform[".e($params['id'],"")."][first_name]' id='first_name' ".required($params)." /><br/>";
        $last_name = "<label for='last-name'>Last name: </label><input class='form-control' type='text' name='submitform[".e($params['id'],"")."][last_name]' id='last_name' ".required($params)." />";

        return "<div class='form-group'><div class='row'><div class='col-md-2'>".$title."</div><div class='col-md-5'>".$first_name."</div><div class='col-md-5'>".$last_name."</div></div>";
    }

    public static function address($params = array()) {
        $street1 = "<div class='row'><div class='col-md-12'><input class='form-control row-bottom-buffer' type='text' name='submitform[".e($params['id'],"")."][address1]' id='street1'".ph("Address 1").required($params)." /></div></div>";
        $street2 = "<div class='row'><div class='col-md-12'><input class='form-control row-bottom-buffer' type='text' name='submitform[".e($params['id'],"")."][address2]' id='street2'".ph("Address 2").required($params)." /></div></div>";
        $city = "<div class='row'><div class='col-md-12'><input class='form-control row-bottom-buffer' type='text' name='submitform[".e($params['id'],"")."][city]' id='city'".ph("City").required($params)." /></div></div>";

		$locations_file = file_get_contents(LF_BASE_DIR . '/libs/locations.json');
		$locations_array = json_decode($locations_file, true);
		$countries = $locations_array['countries'];
		$states = $locations_array['states'];
		
		$all_countries = array();
		foreach($countries as $region => $reg_conts) {
			$all_countries = array_merge($all_countries, $reg_conts);
		}

		
		
		$countries_option_html = "";
		foreach($all_countries as $country) {
			$countries_option_html .= "<option value='".$country."'>".$country."</option>";
		}
		$countries_select = ""
				. "<select data-placeholder='Choose a country' style='width: 100%' class='select2element' id='{$params['id']}_selector_country' name='submitform[{$params['id']}][country]'".required($params).">"
				. "<option value='none'>Choose a country</option>"
				. $countries_option_html 
				. "</select>";

		$state_select = "<select data-placeholder='Choose a state' style='width: 100%' class='select2element' id='{$params['id']}_selector_state' name='submitform[{$params['id']}][state]'".required($params).">"
				. "<option value='none'>Choose a state</option>"
				. "</select>";
	    
		
		$countries = json_encode($locations_array['countries']);
		$states = json_encode($locations_array['states']);

		$countries_hidden_div = "<div class='hidden' id='liveform_json_countries'>{$countries}</div>";
		$states_hidden_div = "<div class='hidden' id='liveform_json_states'>{$states}</div>";

		$hidden_divs = $countries_hidden_div . $states_hidden_div;
		
		$show_states = true;

		$ret_html = ""
				. "<div class='form-gruop'>"
				. $street1
				. $street2
				. $city
				. "<div class='row'>"
				. ($show_states == true ? "<div class='col-md-6'>" : "<div class='col-md-12'>") 
				. $countries_select . "</div>"
				. ($show_states == true ? ("<div class='col-md-6'>".$state_select."</div>") : "")
				. "</div></div>"
				. $hidden_divs;

		$state_populate_js = "<script type='text/javascript'>"
				. "jQuery('#{$params['id']}_selector_country').on('change',function(){"
					. "var sel_country = jQuery(this).val();"
					. "var json_state = JSON.parse(jQuery('#liveform_json_states').html());"
					. "json_state['none'] = [];"
					. "jQuery('#{$params['id']}_selector_state').html(get_selections(json_state[sel_country]));"
					. "jQuery('.chosen-select').chosen();"
				. "});"
				. "function get_selections(states) {"
					. "options_html = '<option selected=\'selected\' value=\'\'>Choose a state</option>';"
					. "for (i = 0 ; i<states.length ; i++) {"
						. "options_html += ('<option value=\''+states[i]+'\'>'+states[i]+'</option>')"
					. "};"
					. "return options_html;"
				. "};"
				. "</script>";

		return $ret_html 
		. "<script type='text/javascript'>jQuery(document).ready(function(){jQuery('.select2element').select2();});</script>"
		. ($show_states == true ? $state_populate_js : "");

        return "<div class='form-group'>".$label.$street1.$street2.$city.$country."</div>";
    }

    public static function payment( $params = array() ) {
        $selected_methods = isset( $params['payment'] ) ? $params['payment'] : array() ;  // get the list of selected types of methods
				$label = "<div class='form-group'><label>Payable amount: </label> ".get_currency_symbolised_amount($params['amount'], $params['currency'])."</div>";
        $methods_set = set_methods();
        $methods = "<select class='form-control' name=submitform[".e($params['id'],"")."] id='payment' ".required($params).">";
        $methods .= "<option selected='selected' disabled='disabled' value=''>Select a Payment Method</option>";
        foreach ( $selected_methods as $method_name ) {
            if ( isset( $methods_set[$method_name] ) ) {
                $methods .= "<option value='{$method_name}'>{$methods_set[$method_name]}</option>";
            }
        }
        $methods.= '</select>';

        return "<div class='form-group'>".$label.$methods."</div>";
    }

	public static function pageseparator( $params = array() ) {
		if (is_array($params))	
			$separator_html = $params['label'];
		else
			$separator_html = $params;
        return $separator_html;
    }

	public static function url( $params = array() ) {
		$url_value = isset($params['url']) ? $params['url'] : "";
		$website_url_html = "";
		$website_url_html .= "<input type='text' class='form-control' name=submitform[".e($params['id'],"")."] id='urlfield_{$params['id']}' ".required($params)." placeholder='http://www.website.com'/>";
		return "<div class='form-group'>".$website_url_html."</div>";
	}

	public static function date( $params = array() ) {
		$date_format = !isset($params['date-format']) ? "dd/mm/yy" : $params['date-format'];
		$time_format = !isset($params['time-format']) ? "hh:mm" : $params['time-format'];
		$datepicker_field_id = "datefield_{$params['id']}";
		if (isset($params['input_time'])) {
			$script_string = "<script type='text/javascript'>jQuery(document).ready(function(){jQuery('#{$datepicker_field_id}').datetimepicker({
			dateFormat: '{$date_format}',
			timeFormat: '{$time_format}'
		});});</script>";
		} else {
			$script_string = "<script type='text/javascript'>jQuery(document).ready(function(){jQuery('#{$datepicker_field_id}').datepicker({
			dateFormat: '{$date_format}'
		});});</script>";
		}
		$date_field_html = "";
				$date_field_html .= "<input class='form-control datepicker' type='text' name='submitform[{$params['id']}]' id='{$datepicker_field_id}'".required($params)."/>";
	$date_field_html .= $script_string;

	
		return "<div class='form-group'>".$date_field_html."</div>";
	}

	public static function daterange( $params = array() ) {
		$date_format = !isset($params['date-format']) ? "dd/mm/yy" : $params['date-format'];
		$time_format = !isset($params['time-format']) ? "hh:mm" : $params['time-format'];
		$datepicker_field1_id = "daterangefield1_{$params['id']}";
		$datepicker_field2_id = "daterangefield2_{$params['id']}";

		if (isset($params['input_time'])) {
			$script_string = "<script type='text/javascript'>jQuery(document).ready(function($){jQuery( '#{$datepicker_field1_id}' ).datetimepicker({
defaultDate: '+1w',
dateFormat: '{$date_format}',
timeFormat: '{$time_format}',
changeMonth: true,
numberOfMonths: 1,
onClose: function( selectedDate ) {
$( '#{$datepicker_field2_id}' ).datetimepicker( 'option', 'minDate', selectedDate );
}
});
$( '#{$datepicker_field2_id}' ).datetimepicker({
defaultDate: '+1w',
dateFormat: '{$date_format}',
timeFormat: '{$time_format}',
changeMonth: true,
numberOfMonths: 1,
onClose: function( selectedDate ) {
$( '#{$datepicker_field1_id}' ).datetimepicker( 'option', 'maxDate', selectedDate );
}
});});</script>";
		} else {
			$script_string = "<script type='text/javascript'>jQuery(document).ready(function($){jQuery( '#{$datepicker_field1_id}' ).datepicker({
defaultDate: '+1w',
dateFormat: '{$date_format}',
changeMonth: true,
numberOfMonths: 1,
onClose: function( selectedDate ) {
$( '#{$datepicker_field2_id}' ).datepicker( 'option', 'minDate', selectedDate );
}
});
$( '#{$datepicker_field2_id}' ).datepicker({
defaultDate: '+1w',
dateFormat: '{$date_format}',
changeMonth: true,
numberOfMonths: 1,
onClose: function( selectedDate ) {
$( '#{$datepicker_field1_id}' ).datepicker( 'option', 'maxDate', selectedDate );
}
});});</script>";
		}
		
		$date_format = !isset($params['date_format']) ? "dd/mm/yy" : $params['date_format'];
		$date_field_html = "";
		$date_field_html .= "<div class='row'>";
		$date_field_html .= "<div class='col-md-6'><input class='form-control datepicker' type='text' placeholder='From' name='submitform[{$params['id']}][from]' id='{$datepicker_field1_id}'".required($params)."/></div>";
		$date_field_html .= "<div class='col-md-6'><input class='form-control datepicker' type='text' placeholder='To' name='submitform[{$params['id']}][to]' id='{$datepicker_field2_id}'".required($params)."/></div>";
		$date_field_html .= "</div>";

	$date_field_html .= $script_string;

	
		return "<div class='form-group'>".$date_field_html."</div>";
	}


	public static function location( $params = array() ) {
		$locations_file = file_get_contents(LF_BASE_DIR . '/libs/locations.json');
		$locations_array = json_decode($locations_file, true);
		$countries = $locations_array['countries'];
		$states = $locations_array['states'];
		
		$all_countries = array();
		foreach($countries as $region => $reg_conts) {
			$all_countries = array_merge($all_countries, $reg_conts);
		}

		
		
		$countries_option_html = "";
		foreach($all_countries as $country) {
			$countries_option_html .= "<option value='".$country."'>".$country."</option>";
		}
		$countries_select = ""
				. "<select data-placeholder='Choose a country' style='width: 100%' class='select2element' id='{$params['id']}_selector_country' name='submitform[{$params['id']}][country]'".required($params).">"
				. "<option value='none'>Choose a country</option>"
				. $countries_option_html 
				. "</select>";

		$state_select = "<select data-placeholder='Choose a state' style='width: 100%' class='select2element' id='{$params['id']}_selector_state' name='submitform[{$params['id']}][state]'".required($params).">"
				. "<option value='none'>Choose a state</option>"
				. "</select>";
	    
		
		$countries = json_encode($locations_array['countries']);
		$states = json_encode($locations_array['states']);

		$countries_hidden_div = "<div class='hidden' id='liveform_json_countries'>{$countries}</div>";
		$states_hidden_div = "<div class='hidden' id='liveform_json_states'>{$states}</div>";

		$hidden_divs = $countries_hidden_div . $states_hidden_div;
		
		$show_states = in_array('state', $params['location_params']);

		$ret_html = "<div class='form-gruop'><div class='row'>"
				. ($show_states == true ? "<div class='col-md-6'>" : "<div class='col-md-12'>") 
				. $countries_select . "</div>"
				. ($show_states == true ? ("<div class='col-md-6'>".$state_select."</div>") : "")
				. "</div></div>"
				. $hidden_divs;

		$state_populate_js = "<script type='text/javascript'>"
				. "jQuery('#{$params['id']}_selector_country').on('change',function(){"
					. "var sel_country = jQuery(this).val();"
					. "var json_state = JSON.parse(jQuery('#liveform_json_states').html());"
					. "json_state['none'] = [];"
					. "jQuery('#{$params['id']}_selector_state').html(get_selections(json_state[sel_country]));"
					. "jQuery('.chosen-select').chosen();"
				. "});"
				. "function get_selections(states) {"
					. "options_html = '<option selected=\'selected\' value=\'\'>Choose a state</option>';"
					. "for (i = 0 ; i<states.length ; i++) {"
						. "options_html += ('<option value=\''+states[i]+'\'>'+states[i]+'</option>')"
					. "};"
					. "return options_html;"
				. "};"
				. "</script>";

		return $ret_html 
		. "<script type='text/javascript'>jQuery(document).ready(function(){jQuery('.select2element').select2();});</script>"
		. ($show_states == true ? $state_populate_js : "");
		

	}

	public static function paragraph_text ( $params = array() ) {
		return "<div class='form-group'>{$params['paragraph_text_value']}</div>";
	}

	public static function phone( $params = array() ) {
		$country_code_template = "<div class='col-md-3'><input type='text' class='form-control' name='submitform[{$params['id']}][country_code]' value='' placeholder='Country'".required($params)."/></div>";
		$area_code_template = "<div class='col-md-3'><input type='text' class='form-control' name='submitform[{$params['id']}][area_code]' value='' placeholder='Area'".required($params)."/></div>";
		$dial_code_template = "<div class='col-md-6'><input type='text' class='form-control' name='submitform[{$params['id']}][dial_code]' value='' placeholder='Number'".required($params)."/></div>";

		$phone_number_template = "<div class='form-group'><div class='row'>{$country_code_template}{$area_code_template}{$dial_code_template}</div></div>";

		return $phone_number_template;
	}
	
	public static function rating( $params = array() ) {
		$max_rating = empty($params['max_rating']) ? '0' : $params['max_rating'];
		$rating_steps = empty($params['rating_steps']) ? '0' : $params['rating_steps'];
		$input_field = "<input type='hidden' name='submitform[{$params['id']}]' id='{$params['id']}_rating_field' " . required($params) . " />";
		$visible_div = "<div class='form-group'><div id='{$params['id']}_rating_div'></div></div>";
		$embed_script = ""
				. "<script type='text/javascript'>"
					. "jQuery(function () { jQuery('#{$params['id']}_rating_div').rateit({ max: {$max_rating}, step: {$rating_steps}, backingfld: '#{$params['id']}_rating_field' }); });"
				. "</script>";

		return $input_field.$visible_div.$embed_script;

	}
	
}