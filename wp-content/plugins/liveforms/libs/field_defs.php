<?php

function set_commonfields() {
	$fields = array();
	$fields['name'] = array('type' => 'text', 'label' => 'Name');
	$fields['email'] = array('type' => 'email', 'label' => 'Email', 'template' => 'email');
	$fields['subject'] = array('type' => 'text', 'label' => 'Subject');
	$fields['message'] = array('type' => 'textarea', 'label' => 'Message');

	$fields = array(
		'Name', 'Email', 'Subject', 'Message'
	);

	return $fields;
}

add_filter("common_fields", "set_commonfields");

function set_genericfields() {
	$generic_fields = array();
	$generic_fields = array(
		'text' => array(
			'type' => 'text',
			'label' => 'Text'
		),
		'password' => array(
			'type' => 'password',
			'label' => 'Password'
		),
		'radio' => array(
			'type' => 'radio',
			'label' => 'Radio',
			'options' => true
		),
		'checkbox' => array(
			'type' => 'checkbox',
			'label' => 'Checkbox',
			'options' => true
		),
		'select' => array(
			'type' => 'select',
			'label' => 'Select',
			'options' => true
		),
		'textarea' => array(
			'type' => 'textarea',
			'label' => 'Textarea'
		)
	);
	return array('Text', 'Textarea', 'Number', 'Password', 'Radio', 'Select', 'Checkbox');
	return $generic_fields;
}

add_filter("generic_fields", "set_genericfields");

function set_advancedfields() {
	$advanced_fields = array();
	$advanced_fields = array(
		'file' => array(
			'type' => 'file',
			'label' => 'File Upload',
			'template' => 'file'
		),
		'captcha' => array(
			'type' => 'captcha',
			'label' => 'Captcha',
			'template' => 'captcha'
		),
		'fullname' => array(
			'type' => 'fullname',
			'label' => 'Full name'
		),
		'address' => array(
			'type' => 'address',
			'label' => 'Address'
		),
		'pageseparator' => array(
			'type' => 'pageseparator',
			'label' => 'Page Separator',
			'template' => 'separator'
		),
		'paymentmethods' => array(
			'type' => 'paymentmethods',
			'label' => 'Payment methods',
			'template' => 'paymentmethods'
		),
		'url' => array(
			'type' => 'url',
			'label' => 'Website',
			'template' => 'url'
		),
		'date' => array(
			'type' => 'date',
			'label' => 'Date',
			'template' => 'date'
		),
		'daterange' => array(
			'type' => 'daterange',
			'label' => 'Date range',
			'template' => 'daterange'
		),
		'location' => array(
			'type' => 'location',
			'label' => 'Location',
			'template' => 'location'
		),
		'phone' => array(
			'type' => 'phone',
			'label' => 'Phone',
			'template' => 'phone'
		),
		'paragraph_text' => array(
			'type' => 'paragraph_text',
			'label' => 'Paragraph text',
			'template' => 'paratext'
		),
		'rating' => array(
			'type' => 'rating',
			'label' => 'Rating',
			'template' => 'rating'
		)
	);
	return array('File', 'Captcha', 'FullName', 'Address', 'Rating', 'Url', 'Paratext', 'Phone', 'PaymentMethods', 'Location', 'Date', 'Daterange','Mathresult', 'Pageseparator');
	return $advanced_fields;
}

add_filter("advanced_fields", "set_advancedfields");

function set_methods() {
	global $methods_set;
	return $methods_set;
}

add_filter("method_set", "set_methods");

function get_validation_ops($type = '') {
	$optional_validation_ops = array(
		'url' => array('url' => 'URL'),
		'email' => array('email' => 'Email'),
		'date' => array('date' => 'Date'),
		'text' => array('text' => 'Text'),
		'numeric' => array('numeric' => 'Numeric')
	);

	$default_validation_ops = array('text' => 'Text', 'numeric' => 'Numeric', 'email' => 'Email', 'url' => 'URL', 'date' => 'Date');
	if ($type == '') $validation_ops = $default_validation_ops;
	else {
		$validation_ops = $optional_validation_ops[$type];
	}

	return $validation_ops;
}

function currencies() {
	$currencies_list = array(
		'USD' => 'Dollar',
		'EUR'	=> 'Euro'
	);
	return $currencies_list;
}
?>