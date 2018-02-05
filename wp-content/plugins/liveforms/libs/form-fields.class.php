<?php

class formfields{

    public static  function text($params = array('id'=>'','required'=>'')){
        return "<input type='text' name='submitform[{$params['id']}]' class='form-control' value=''".required($params)." />";
    }
	
	public static  function email($params = array('id'=>'','required'=>'')){
        return "<input type='text' name='submitform[{$params['id']}]' class='form-control' value=''".required($params)." />";
    }

    public static  function radio($params = array('id'=>'','required'=>'','options'=>array('name'=>array(),'value'=>array()))) {
        $html = "<div class='radiobuttons'>";

        $option_names = $params['options']['name'];
        $option_values = $params['options']['value'];
        foreach ($option_names as $id => $label) {
            $html.= " <label><input type='radio' value='{$option_values[$id]}' name='submitform[{$params['id']}]'".required($params)." /> {$label}</label>";
        }
        return $html."</div>";
    }

    public static  function checkbox($params = array('id'=>'','required'=>'','options'=>array('name'=>array(),'value'=>array()))) {
        $html = "<div class='checkboxes'>";

        $option_names = $params['options']['name'];
        $option_values = $params['options']['value'];
        foreach ($option_names as $id => $label) {
            $html.= " <label><input type='checkbox' value='{$option_values[$id]}' name='submitform[{$params['id']}][]'".required($params)." /> {$label}</label>";
        }
        return $html."</div>";
    }

    public static  function select($params = array('id'=>'','required'=>'','options'=>array('name'=>array(),'value'=>array()))) {
        $option_names = $params['options']['name'];
        $option_values = $params['options']['value'];
        $options = "<option selected='selected' disabled='disabled' value=''>Please Select</option>";
        $multiple = isset($params['multi'])?'multiple':'';
        foreach ($option_names as $id => $label) {
            $options .= "<option value='{$option_values[$id]}'>{$label}</option>";
        }
        return "<select name='submitform[{$params['id']}]'  {$multiple} ".required($params)." >{$options}</select>";
    }

    public static  function textarea($params = array('id'=>'','required'=>'','lines'=>3)) {
        $params['lines'] = '4';
        $params['validation'] = 'text';
        $class = isset($params['class'])?$params['class']:'col-md-12';
		
        return "<textarea class='form-control' name='submitform[{$params['id']}]' rows='{$params['lines']}' class='{$class}'".required($params)." > </textarea>";
    }

    public static  function password($params = array('id'=>'','required'=>'')) {
		$password_field_html = "<label style='display: block; clear: both;'>Password</label><input id='password' type='password' name='submitform[{$params['id']}]' class='form-control password'".required($params)."/>";
		$password_confirm_field_html = "<label style='display: block; clear: both;'>Confirm password</label><input id='password_confirm' type='password' data-rule-equalto='#password' data-msg-equalto='Did not match' name='submitform[{$params['id']}]_confirm' class='form-control password_confirm'".required($params)."/>";
        return $password_field_html . $password_confirm_field_html;
    }

}