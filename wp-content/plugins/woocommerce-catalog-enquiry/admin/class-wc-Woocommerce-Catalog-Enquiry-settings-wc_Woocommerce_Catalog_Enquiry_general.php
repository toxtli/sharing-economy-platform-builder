<?php
class WC_Woocommerce_Catalog_Enquiry_Settings_Gneral {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  private $tab;
  public $all_users;
  public $page_array = array();

  /**
   * Start up
   */
  public function __construct($tab) {
    $this->tab = $tab;
    $users = get_users();
    $this->all_users = array();
    $this->get_all_pages();
    foreach($users as $user) {					
			$this->all_users[$user->data->ID] = $user->data->display_name;	 			
		}
    $this->options = get_option( "dc_{$this->tab}_settings_name" );
    $this->settings_page_init();	
  }
  
  public function get_all_pages() {
  	global $WC_Woocommerce_Catalog_Enquiry;
  	$args = array( 'posts_per_page' => -1, 'post_type' => 'page', 'orderby' => 'title', 'order' => 'ASC' );
		$myposts = get_posts( $args );
		foreach ( $myposts as $post ) : setup_postdata( $post ); 	
		$this->page_array[$post->ID] = $post->post_title;		
		endforeach; 
		wp_reset_postdata();
  	
  }
  
  /**
   * Register and add settings
   */
  public function settings_page_init() {
    global $WC_Woocommerce_Catalog_Enquiry;
     
    $settings_tab_options = array("tab" => "{$this->tab}",
                                  "ref" => &$this,
                                  "sections" => array(
                                                      "default_settings_section" => array("title" =>  __('Woocommerce Catalog Enquiry Settings', 'woocommerce-catalog-enquiry'), // Section one
                                                                                         "fields" => array("is_enable" => array('title' => __('Catalog Enable?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_enable', 'label_for' => 'is_enable', 'name' => 'is_enable', 'desc' => __('Just Checked this checkbox for woocommerce catalog mode on', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this for active the catalog functionality.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'), // is catalog enable
                                                                                         	 								 "load_wp_js" => array('title' => __('Load Wordpress JS Library?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'load_wp_js', 'label_for' => 'load_wp_js', 'name' => 'load_wp_js', 'desc' => __('Just Checked this checkbox for load Wordpress JS Library, if your theme has own JS lib then ingnore it', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this for load Wordpress js lib.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'), // is js lib enable	
                                                                                                           "for_user_type" => array('title' => __('Applicable For', 'woocommerce-catalog-enquiry') , 'type' => 'select', 'id' => 'for_user_type', 'label_for' => 'for_user_type', 'name' => 'for_user_type', 'options' => array('0' =>'Please Select', '1' => 'Only for logout user', '2' => 'Only for logged in user', '3' => 'Either logged in or logged out'), 'hints' => __('Method applicable for only secleted user group default all.', 'woocommerce-catalog-enquiry'),   'desc' => __('Select the user type where this catalog is applicable.', 'woocommerce-catalog-enquiry')), // user_type
                                                                                                           "top_content_form" => array('title' => __('Enquiry Top content', 'woocommerce-catalog-enquiry'), 'type' => 'wpeditor', 'id' => 'top_content_form', 'label_for' => 'top_content_form', 'desc' => __('Put your content if you want to top of enquiry form', 'woocommerce-catalog-enquiry'), 'name' => 'top_content_form'), //Top Content
                                                                                                           "bottom_content_form" => array('title' => __('Enquiry Bottom content', 'woocommerce-catalog-enquiry'), 'type' => 'wpeditor', 'id' => 'bottom_content_form', 'label_for' => 'bottom_content_form', 'desc' => __('Put your content if you want to bottom of enquiry form', 'woocommerce-catalog-enquiry'), 'name' => 'bottom_content_form'), //Bottom Content
                                                                                                           "is_enable_enquiry" => array('title' => __('Product Enquiry Enable?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_enable_enquiry', 'label_for' => 'is_enable_enquiry', 'name' => 'is_enable_enquiry', 'desc' => __('Just Checked this checkbox for product page enquiry form enable', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this for active the form functionality.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'), // is catalog enable
                                                                                                           "is_disable_popup_backdrop" => array('title' => __('Enquiry Popup Backdrop Disable?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_disable_popup_backdrop', 'label_for' => 'is_disable_popup_backdrop', 'name' => 'is_disable_popup_backdrop', 'desc' => __('Just Checked this checkbox if you want to disable popup backdrop', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this for deactive the popup backdrop.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'), // is catalog enable
                                                                                                           "is_disable_popup" => array('title' => __('Enquiry Popup Disable?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_disable_popup', 'label_for' => 'is_disable_popup', 'name' => 'is_disable_popup', 'desc' => __('Just Checked this checkbox if you want to enquiry form in the same page below the short description', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this for deactive the popup.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'), // is catalog enable
                                                                                                           "is_override_form_heading" => array('title' => __('Override Form Heading?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_override_form_heading', 'label_for' => 'is_override_form_heading', 'name' => 'is_override_form_heading', 'desc' => __('Just Checked this checkbox if you want to override dynamic heading with your static heading', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this for override dynamic heading.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'), // is catalog enable
                                                                                                           "custom_static_heading" => array('title' => __('Custom Form Heading', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'custom_static_heading', 'name' => 'custom_static_heading', 'hints' => __('Please Enter your custom Heading insteed of dynamic form heading', 'woocommerce-catalog-enquiry')),
                                                                                                           "is_remove_price" => array('title' => __('Remove Price?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_remove_price', 'label_for' => 'is_remove_price', 'name' => 'is_remove_price', 'desc' => __('Just Checked this checkbox for remove the price from catalog', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this for remove the price from product list.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'), // is catalog enable
                                                                                                           "custom_css_product_page" => array('title' => __('Custom CSS', 'woocommerce-catalog-enquiry'), 'type' => 'textarea', 'label_for' => 'custom_css_product_page', 'name' => 'custom_css_product_page', 'desc' => __('Put your custom css in this box for product page there is no need to put the style Tag', 'woocommerce-catalog-enquiry'), 'rows' => 10, 'cols' => 100),
                                                                                                           "is_custom_button" => array('title' => __('Want a custom Button?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_custom_button', 'label_for' => 'is_custom_button', 'name' => 'is_custom_button', 'desc' => __('Do you want a custom Button at the Place of add to cart button then checked here', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this if you want custom button at the place of add to cart.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'), // is button enable
                                                                                                           "is_hide_cart_checkout" => array('title' => __('Hide Cart Chackout Page?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_hide_cart_checkout', 'label_for' => 'is_hide_cart_checkout', 'name' => 'is_hide_cart_checkout', 'desc' => __('Do you want to redirect to home if any one click on the cart or checkout page link', 'woocommerce-catalog-enquiry'), 'hints' => __('Do you want to redirect to home page if any one click on the cart or checkout page link.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'), // is button enable
                                                                                                                                                                                                                      
                                                                                                           "button_type" => array('title' => __('Choose your button type', 'woocommerce-catalog-enquiry'), 'type' => 'select', 'id' => 'button_type', 'label_for' => 'button_type', 'name' => 'button_type',  'options' => array('0' => 'Please Select', '1' => 'Read More', '2' => 'Custom Link For All Products', '3' => 'Individual link in all products', '4' => 'No Link Just #'), 'hints' => __('Choose your preferred button type.', 'woocommerce-catalog-enquiry'), 'desc' => __('By default Read More Button.', 'woocommerce-catalog-enquiry')), // Button Type
                                                                                                           "button_link" => array('title' => __('Button Link', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'button_link', 'name' => 'button_link', 'desc' => __('Applicable only when you choose custom link for all products in button type', 'woocommerce-catalog-enquiry'), 'hints' => __('Button link applicable only if you choose button type Custom Link For All Products ', 'woocommerce-catalog-enquiry'))
                                                                                                           
                                                                                                           
                                                                                                          
                                                                                                           )
                                                                                         ), 
                                                      "custom_settings_section" => array("title" => "Custom Button Layout Settings", // Another section
                                                                                         "fields" => array("button_text" => array('title' => __('Custom button label', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'button_text', 'name' => 'button_text', 'hints' => __('Give your custom button Text', 'woocommerce-catalog-enquiry')),
																																												 "button_text_color" => array('title' => __('Choose Button Text Color', 'woocommerce-catalog-enquiry'), 'type' => 'colorpicker', 'id' => 'button_text_color', 'label_for' => 'button_text_color', 'name' => 'button_text_color', 'default' => '#000000', 'hints' => __('Choose your button text color here.', 'woocommerce-catalog-enquiry'), 'desc' => __('This is button text color will be appear in the custom button .', 'woocommerce-catalog-enquiry')),
																																												 "button_background_color" => array('title' => __('Choose Button Background Color', 'woocommerce-catalog-enquiry'), 'type' => 'colorpicker', 'id' => 'button_background_color', 'label_for' => 'button_background_color', 'name' => 'button_background_color', 'default' => '#999999', 'hints' => __('Choose your button background color here.', 'woocommerce-catalog-enquiry'), 'desc' => __('This is button background color will be appear in the custom button .', 'woocommerce-catalog-enquiry')),
																																												 "button_text_color_hover" => array('title' => __('Choose Button Text Color Hover', 'woocommerce-catalog-enquiry'), 'type' => 'colorpicker', 'id' => 'button_text_color_hover', 'label_for' => 'button_text_color_hover', 'name' => 'button_text_color_hover', 'default' => '#ffffff', 'hints' => __('Choose your button text color on hover here.', 'woocommerce-catalog-enquiry'), 'desc' => __('This is button text color on hover will be appear in the custom button .', 'woocommerce-catalog-enquiry')),
																																												 "button_background_color_hover" => array('title' => __('Choose Button background Color Hover', 'woocommerce-catalog-enquiry'), 'type' => 'colorpicker', 'id' => 'button_background_color_hover', 'label_for' => 'button_background_color_hover', 'name' => 'button_background_color_hover', 'default' => '#000000', 'hints' => __('Choose your button background color on hover here.', 'woocommerce-catalog-enquiry'), 'desc' => __('This is button background color on hover will be appear in the custom button .', 'woocommerce-catalog-enquiry')),
																																												 "button_width" => array('title' => __('Custom button Width', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'button_width', 'name' => 'button_width', 'hints' => __('Give your custom button Width in px', 'woocommerce-catalog-enquiry')),
																																												 "button_height" => array('title' => __('Custom button Height', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'button_height', 'name' => 'button_height', 'hints' => __('Give your custom button Height', 'woocommerce-catalog-enquiry')),
																																												 "button_padding" => array('title' => __('Custom button Padding', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'button_padding', 'name' => 'button_padding', 'hints' => __('Give your custom button Padding', 'woocommerce-catalog-enquiry')),
																																												 "button_border_size" => array('title' => __('Custom button Border', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'button_border_size', 'name' => 'button_border_size', 'hints' => __('Give your custom button border size', 'woocommerce-catalog-enquiry')),
																																												 "button_fornt_size" => array('title' => __('Custom button Font size', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'button_fornt_size', 'name' => 'button_fornt_size', 'hints' => __('Give your custom button Font Size', 'woocommerce-catalog-enquiry')),
																																												 "button_border_redius" => array('title' => __('Custom button border redius', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'button_border_redius', 'name' => 'button_border_redius', 'hints' => __('Give your custom button border redius', 'woocommerce-catalog-enquiry')),
																																												 "button_border_color" => array('title' => __('Choose Button Border Color', 'woocommerce-catalog-enquiry'), 'type' => 'colorpicker', 'id' => 'button_border_color', 'label_for' => 'button_border_color', 'name' => 'button_border_color', 'default' => '#333333', 'hints' => __('Choose your button border color.', 'woocommerce-catalog-enquiry'), 'desc' => __('This is button border color which will be appear in the custom button .', 'woocommerce-catalog-enquiry')),
																																												 "button_margin_top" => array('title' => __('Custom button margin top', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'button_margin_top', 'name' => 'button_margin_top', 'hints' => __('Give your custom button top margin', 'woocommerce-catalog-enquiry')),
																																												 "button_margin_bottom" => array('title' => __('Custom button margin bottom', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'button_margin_bottom', 'name' => 'button_margin_bottom', 'hints' => __('Give your custom button bottom margin', 'woocommerce-catalog-enquiry'))
                                                                                                          )
                                                                                         ),
                                                      "enquiry_settings_section_form" => array("title" => "Enquiry Form Settings", // Another section
                                                      																					"fields" => array("is_captcha" => array('title' => __('Captcha Enable ?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_captcha', 'label_for' => 'is_captcha', 'name' => 'is_captcha', 'desc' => __('Do you want Captcha in for enquiry form.', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this if you want captcha in the enquiry form.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'),
                                                      																														"is_subject" => array('title' => __('Subject Field Enable ?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_subject', 'label_for' => 'is_subject', 'name' => 'is_subject', 'desc' => __('Do you want Subject field in for enquiry form.', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this if you want subject field in the enquiry form.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'),
                                                      																														"is_phone" => array('title' => __('Phone Field Enable ?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_phone', 'label_for' => 'is_phone', 'name' => 'is_phone', 'desc' => __('Do you want Phone field in for enquiry form.', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this if you want to Phone field in the enquiry form.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'),
                                                      																														"is_address" => array('title' => __('Address Field Enable ?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_address', 'label_for' => 'is_address', 'name' => 'is_address', 'desc' => __('Do you want Address field in for enquiry form.', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this if you want address field in the enquiry form.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'),
                                                      																														"is_comment" => array('title' => __('Comment Field Enable ?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_comment', 'label_for' => 'is_comment', 'name' => 'is_comment', 'desc' => __('Do you want Comment field in for enquiry form.', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this if you want comment field in the enquiry form.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'),
                                                                                                                  "is_fileupload" => array('title' => __('File Upload Field Enable ?', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_fileupload', 'label_for' => 'is_fileupload', 'name' => 'is_fileupload', 'desc' => __('Do you want File Upload field in enquiry form.', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this if you want file upload field in the enquiry form.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'),
                                                                                                                  "filesize_limit" => array('title' => __('File Upload Size Limit', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'filesize_limit', 'label_for' => 'filesize_limit', 'name' => 'filesize_limit', 'placeholder'=>'1', 'desc' => __('Enter File Upload size limit in MB.', 'woocommerce-catalog-enquiry'), 'hints' => __('add maximum upload file size limit in MB.', 'woocommerce-catalog-enquiry'), 'value' => '')
                                                      																														
                                                      																						
                                                      																					)),
                                                      "enquiry_settings_section_form_label" => array("title" => "Enquiry Form Label Settings", // Another section
                                                      																					"fields" => array("name_label" => array('title' => __('Custom label for name', 'woocommerce-catalog-enquiry'), 'type' => 'text',   'id' => 'name_label', 'label_for' => 'name_label', 'name' => 'name_label', 'desc' => __('Given label will shown in the frontend form field in the place of Name.', 'woocommerce-catalog-enquiry'), 'hints' => __('Enter custom label for Name.', 'woocommerce-catalog-enquiry')), // is button enable
                                                      																														"email_label" => array('title' => __('Custom label for email', 'woocommerce-catalog-enquiry'), 'type' => 'text',  'id' => 'email_label', 'label_for' => 'email_label', 'name' => 'email_label', 'desc' => __('Given label will shown in the frontend form field in the place of Email.', 'woocommerce-catalog-enquiry'), 'hints' => __('Enter custom label for Email.', 'woocommerce-catalog-enquiry')),
                                                      																														"subject_label" => array('title' => __('Custom label for subject', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'subject_label', 'label_for' => 'subject_label', 'name' => 'subject_label', 'desc' => __('Given label will shown in the frontend form field in the place of Subject.', 'woocommerce-catalog-enquiry'), 'hints' => __('Enter custom label for Subject.', 'woocommerce-catalog-enquiry')),
                                                      																														"phone_label" => array('title' => __('Custom label for phone', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'phone_label', 'label_for' => 'phone_label', 'name' => 'phone_label', 'desc' => __('Given label will shown in the frontend form field in the place of Phone.', 'woocommerce-catalog-enquiry'), 'hints' => __('Enter custom label for Phone.', 'woocommerce-catalog-enquiry')),
                                                      																														"address_label" => array('title' => __('Custom label for address', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'address_label', 'label_for' => 'address_label', 'name' => 'address_label', 'desc' => __('Given label will shown in the frontend form field in the place of Address.', 'woocommerce-catalog-enquiry'), 'hints' => __('Enter custom label for Phone.', 'woocommerce-catalog-enquiry')),
                                                      																														"comment_label" => array('title' => __('Custom label for comment', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'comment_label', 'label_for' => 'comment_label', 'name' => 'comment_label', 'desc' => __('Given label will shown in the frontend form field in the place of Comment.', 'woocommerce-catalog-enquiry'), 'hints' => __('Enter custom label for Comment.', 'woocommerce-catalog-enquiry')),
                                                      																														"fileupload_label" => array('title' => __('Custom label for file upload', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'fileupload_label', 'label_for' => 'fileupload_label', 'name' => 'fileupload_label', 'desc' => __('Given label will shown in the frontend form field in the place of File Upload.', 'woocommerce-catalog-enquiry'), 'hints' => __('Enter custom label for File Upload.', 'woocommerce-catalog-enquiry')),
                                                                                                                  "captcha_label" => array('title' => __('Custom label for captcha', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'captcha_label', 'label_for' => 'captcha_label', 'name' => 'captcha_label', 'desc' => __('Given label will shown in the frontend form field in the place of Captcha.', 'woocommerce-catalog-enquiry'), 'hints' => __('Enter custom label for Captcha.', 'woocommerce-catalog-enquiry')),
                                                      																														"captcha_input_label" => array('title' => __('Custom label for captcha Input', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'captcha_input_label', 'label_for' => 'captcha_input_label', 'name' => 'captcha_input_label', 'desc' => __('Given label will shown in the frontend form field in the place of Captcha Input.', 'woocommerce-catalog-enquiry'), 'hints' => __('Enter custom label for Captcha.', 'woocommerce-catalog-enquiry'))   																													
                                                      																					)),
                                                      "enquiry_settings_section_override" => array("title" => "Enquiry Mail Override", // Another section
                                                      																					"fields" => array("other_emails" => array('title' => __('Other Emails (commma seperated)', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'other_emails', 'label_for' => 'other_emails', 'name' => 'other_emails', 'desc' => __('Enter email address if you want to receive enquiry mail along with admin mail.', 'woocommerce-catalog-enquiry'), 'hints' => __('if you add more than one email then you have to put comma seperated email id no space please.', 'woocommerce-catalog-enquiry'), 'value' => ''),
                                                                                                                  "is_other_admin_mail" => array('title' => __('Remove admin email', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_other_admin_mail', 'label_for' => 'is_other_admin_mail', 'name' => 'is_other_admin_mail', 'desc' => __('Do you want remove admin email from reciever list.', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this if you want to remove admin email from reciever list.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'),
                                                      																														"other_admin_mail" => array('title' => __('Enter mail id who get the email', 'woocommerce-catalog-enquiry'), 'type' => 'text', 'id' => 'other_admin_mail', 'label_for' => 'other_admin_mail', 'name' => 'other_admin_mail', 'desc' => __('Enter mail id who get the email insteed of admin.', 'woocommerce-catalog-enquiry'), 'hints' => __('Enter mail id who get the email insteed of admin.', 'woocommerce-catalog-enquiry'))
                                                      																					)),
                                                      "enquiry_settings_section_redirected" => array("title" => "Redirect after enquiry success", // Another section
                                                      																					"fields" => array("is_page_redirect" => array('title' => __('Redirect to other page', 'woocommerce-catalog-enquiry'), 'type' => 'checkbox', 'id' => 'is_page_redirect', 'label_for' => 'is_page_redirect', 'name' => 'is_page_redirect', 'desc' => __('Do you want to redirect to other page after enquiry successful.', 'woocommerce-catalog-enquiry'), 'hints' => __('Check this if you want to redirect to other page after successful enquiry.', 'woocommerce-catalog-enquiry'), 'value' => 'Enable'),                                                      																														
                                                      																														"redirect_page_id" => array('title' => __('Select Redirect page', 'woocommerce-catalog-enquiry'), 'type' => 'select', 'id' => 'redirect_page_id', 'label_for' => 'redirect_page_id', 'options' => $this->page_array, 'name' => 'redirect_page_id', 'desc' => __('Select page where will be redirected after enquiry successful.', 'woocommerce-catalog-enquiry'), 'hints' => __('Select Redirection page.', 'woocommerce-catalog-enquiry'))
                                                      																					))
                                                      )
                                  );
    
    $WC_Woocommerce_Catalog_Enquiry->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function dc_wc_Woocommerce_Catalog_Enquiry_general_settings_sanitize( $input ) {
    global $WC_Woocommerce_Catalog_Enquiry;
    $new_input = array();
   
    
    $hasError = false;
    
    if( isset( $input['is_page_redirect'] ) )
		$new_input['is_page_redirect'] = sanitize_text_field( $input['is_page_redirect'] );
		if( isset( $input['redirect_page_id'] ) )
		$new_input['redirect_page_id'] = sanitize_text_field( $input['redirect_page_id'] );
    
		if( isset( $input['is_override_form_heading'] ) )
		$new_input['is_override_form_heading'] = sanitize_text_field( $input['is_override_form_heading'] );
		if( isset( $input['custom_static_heading'] ) )
		$new_input['custom_static_heading'] = sanitize_text_field( $input['custom_static_heading'] );
	
    if( isset( $input['name_label'] ) )
      $new_input['name_label'] = sanitize_text_field( $input['name_label'] );
    if( isset( $input['email_label'] ) )
      $new_input['email_label'] = sanitize_text_field( $input['email_label'] );
    if( isset( $input['subject_label'] ) )
      $new_input['subject_label'] = sanitize_text_field( $input['subject_label'] );
    if( isset( $input['phone_label'] ) )
      $new_input['phone_label'] = sanitize_text_field( $input['phone_label'] );
    if( isset( $input['address_label'] ) )
      $new_input['address_label'] = sanitize_text_field( $input['address_label'] );
    if( isset( $input['comment_label'] ) )
      $new_input['comment_label'] = sanitize_text_field( $input['comment_label'] );
    if( isset( $input['fileupload_label'] ) )
      $new_input['fileupload_label'] = sanitize_text_field( $input['fileupload_label'] );
    if( isset( $input['captcha_label'] ) )
      $new_input['captcha_label'] = sanitize_text_field( $input['captcha_label'] );
    if( isset( $input['captcha_input_label'] ) )
      $new_input['captcha_input_label'] = sanitize_text_field( $input['captcha_input_label'] );
    if( isset( $input['is_disable_popup_backdrop'] ) )
      $new_input['is_disable_popup_backdrop'] = sanitize_text_field( $input['is_disable_popup_backdrop'] );
    if( isset( $input['is_disable_popup'] ) )
      $new_input['is_disable_popup'] = sanitize_text_field( $input['is_disable_popup'] );
    
    if( isset( $input['is_captcha'] ) )
      $new_input['is_captcha'] = sanitize_text_field( $input['is_captcha'] );
    
    if( isset( $input['is_enable'] ) )
      $new_input['is_enable'] = sanitize_text_field( $input['is_enable'] );
    
    if( isset( $input['load_wp_js'] ) )
      $new_input['load_wp_js'] = sanitize_text_field( $input['load_wp_js'] );
    
    if( isset( $input['for_user_type'] ) ) {
      $new_input['for_user_type'] = sanitize_text_field( $input['for_user_type'] );
    }
    
     if( isset( $input['other_emails'] ) ) {
      $new_input['other_emails'] = sanitize_text_field( $input['other_emails'] );
    }
    
    if( isset( $input['top_content_form'] ) )
      $new_input['top_content_form'] =  $input['top_content_form'];

    if( isset( $input['bottom_content_form'] ) )
      $new_input['bottom_content_form'] = $input['bottom_content_form'];
    
    if( isset( $input['is_enable_enquiry'] ) )
      $new_input['is_enable_enquiry'] = sanitize_text_field( $input['is_enable_enquiry'] );
    
    if( isset( $input['is_remove_price'] ) )
      $new_input['is_remove_price'] = sanitize_text_field( $input['is_remove_price'] );
    
    if( isset( $input['custom_css_product_page'] ) && !empty($input['custom_css_product_page']) ) {
      $new_input['custom_css_product_page'] = sanitize_text_field( $input['custom_css_product_page'] );
    } 
    
    if( isset( $input['is_custom_button'] ) )
      $new_input['is_custom_button'] = sanitize_text_field( $input['is_custom_button'] );
    
    if( isset( $input['button_type'] ) )
      $new_input['button_type'] = sanitize_text_field( $input['button_type'] );
    
    if( isset( $input['button_link'] ) )
      $new_input['button_link'] = sanitize_text_field( $input['button_link'] );
    
    if( isset( $input['button_text'] ) )
      $new_input['button_text'] = sanitize_text_field( $input['button_text'] );
    
    if( isset( $input['button_text_color'] ) )
      $new_input['button_text_color'] = sanitize_text_field( $input['button_text_color'] );
    
    if( isset( $input['button_background_color'] ) )
      $new_input['button_background_color'] = ( $input['button_background_color'] );
    
    if( isset( $input['button_text_color_hover'] ) )
      $new_input['button_text_color_hover'] = sanitize_text_field( $input['button_text_color_hover'] );
    
    if( isset( $input['button_background_color_hover'] ) )
      $new_input['button_background_color_hover'] = sanitize_text_field( $input['button_background_color_hover'] );
    
    if( isset( $input['button_width'] ) && absint( $input['button_width'] ) != 0 ) {
      $new_input['button_width'] = absint( $input['button_width'] );
    } else {
    }
    
    if( isset( $input['button_height'] ) && absint($input['button_height']) != 0 ) {
      $new_input['button_height'] = absint( $input['button_height'] );
    } else {
    }
    
    if( isset( $input['button_padding'] ) && absint($input['button_padding']) != 0 ) {
      $new_input['button_padding'] = absint( $input['button_padding'] );
    } else {	 
    }
    
    if( isset( $input['button_border_size'] ) && absint($input['button_border_size']) != 0 ) {
      $new_input['button_border_size'] = absint( $input['button_border_size'] );
    } else {	
    }
    
    if( isset( $input['button_fornt_size'] ) && absint($input['button_fornt_size']) != 0 ) {
      $new_input['button_fornt_size'] = absint( $input['button_fornt_size'] );
    } else {  	
    }
    
    if( isset( $input['button_border_redius'] ) && absint($input['button_border_redius']) != 0 ) {
      $new_input['button_border_redius'] = absint( $input['button_border_redius'] );
    } else {
    }
      
    if( isset( $input['button_border_color'] ) )
      $new_input['button_border_color'] = sanitize_text_field( $input['button_border_color'] );
    
    if( isset( $input['button_margin_top'] ) &&  absint($input['button_margin_top']) != 0 ) {
      $new_input['button_margin_top'] = absint( $input['button_margin_top'] );
    } else {
    }
    
    if( isset( $input['button_margin_bottom'] ) && absint($input['button_margin_bottom']) != 0 ) {
      $new_input['button_margin_bottom'] = absint( $input['button_margin_bottom'] );
    }else {
    }
    
    if( isset( $input['is_name'] ) )
      $new_input['is_name'] = sanitize_text_field( $input['is_name'] );
    
    if( isset( $input['is_email'] ) )
      $new_input['is_email'] = sanitize_text_field( $input['is_email'] );
    
    if( isset( $input['is_subject'] ) )
      $new_input['is_subject'] = ( $input['is_subject'] );
    
    if( isset( $input['is_phone'] ) )
      $new_input['is_phone'] = sanitize_text_field( $input['is_phone'] );
    
    if( isset( $input['is_address'] ) )
      $new_input['is_address'] = ( $input['is_address'] );
    
    if( isset( $input['is_comment'] ) )
      $new_input['is_comment'] = sanitize_text_field( $input['is_comment'] );

    if( isset( $input['is_fileupload'] ) )
      $new_input['is_fileupload'] = sanitize_text_field( $input['is_fileupload'] );
    
    if( isset( $input['filesize_limit'] ) )
      $new_input['filesize_limit'] = sanitize_text_field( $input['filesize_limit'] );
    
    if( isset( $input['is_hide_cart_checkout'] ) )
    	$new_input['is_hide_cart_checkout'] = sanitize_text_field( $input['is_hide_cart_checkout'] );
    
		if( isset( $input['is_other_admin_mail'] ) )
		$new_input['is_other_admin_mail'] = sanitize_text_field( $input['is_other_admin_mail'] );
		if( isset( $input['other_admin_mail'] ) )
		$new_input['other_admin_mail'] = sanitize_text_field( $input['other_admin_mail'] );    
    
    if(!$hasError) {
      add_settings_error(
        "dc_{$this->tab}_settings_name",
        esc_attr( "dc_{$this->tab}_settings_admin_updated" ),
        __('General settings updated', 'woocommerce-catalog-enquiry'),
        'updated'
      );
    }

    return $new_input;
  }

  /** 
   * Print the Section text
   */
  public function default_settings_section_info() {
    global $WC_Woocommerce_Catalog_Enquiry;
    _e('Enter your default settings below', 'woocommerce-catalog-enquiry');
  }
  
  /** 
   * Print the Section text
   */
  public function custom_settings_section_info() {
    global $WC_Woocommerce_Catalog_Enquiry;
    _e('Configure your button layout settings below', 'woocommerce-catalog-enquiry');
  }
  
  /** 
   * Print the Section text
   */
  public function enquiry_settings_section_form_info() {
    global $WC_Woocommerce_Catalog_Enquiry;
    _e('Configure your enquiry form settings below', 'woocommerce-catalog-enquiry');
  }
  
  public function enquiry_settings_section_override_info() {
    global $WC_Woocommerce_Catalog_Enquiry;
    _e('Override the email settings for enquiry email reciever', 'woocommerce-catalog-enquiry');
  }
  
  public function enquiry_settings_section_redirected_info() {
    global $WC_Woocommerce_Catalog_Enquiry;
    _e('Redirect page settings.', 'woocommerce-catalog-enquiry');
  }
  
  public function enquiry_settings_section_form_label_info() {
  	global $WC_Woocommerce_Catalog_Enquiry;
    _e('Configure your enquiry form Labels', 'woocommerce-catalog-enquiry');
  	
  }
  
}