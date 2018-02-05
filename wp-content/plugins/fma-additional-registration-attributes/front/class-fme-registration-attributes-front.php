<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'FME_Registration_Attributes_Front' ) ) { 

	class FME_Registration_Attributes_Front extends FME_Registration_Attributes {

		public function __construct() {


			
			add_action( 'wp_loaded', array( $this, 'front_scripts' ) );
			
			add_action( 'woocommerce_register_form_start', array($this, 'fme_extra_registration_form_start' ));

			add_action( 'woocommerce_register_form', array($this, 'fme_extra_registration_form_end' ));
			add_action( 'woocommerce_register_post', array($this, 'fme_validate_extra_register_fields'), 10, 3 );
			add_action( 'woocommerce_created_customer', array($this, 'fme_save_extra_register_fields' ));
			add_action('woocommerce_before_my_account', array($this, 'fme_my_profile'));
			add_action( 'init', array($this, 'add_fmera_query_vars' ));
			add_action( 'template_include', array( $this, 'change_template' ) );

			if (isset($_POST['action'])) {
			    if ($_POST['action'] == 'SubmitRegForm') {
			        $this->submit_reg_edit_form($_POST['user_id']);
			    } 
			}
			
		}


		

		public function front_scripts() {	
            
        	wp_enqueue_script( 'jquery-ui');
        	wp_enqueue_script( 'fmera-front-jsssssss', plugins_url( '/js/script.js', __FILE__ ), array('jquery'), false );
        	wp_enqueue_style( 'fmera-front-css', plugins_url( '/css/fmera_style_front.css', __FILE__ ), false );
        	wp_enqueue_style( 'jquery-ui-css');
        		
        }

        function fme_extra_registration_form_start() { ?>

        	<h3><?php echo get_option('account_title'); ?></h3>

        	<div ><p style="
		    color: #9b9b9b;
		    cursor: auto;
		    font-family: Roboto,helvetica,arial,sans-serif;
		    font-size: 0;
		    font-weight: 400;
		    
		">by <a style="color: #9b9b9b;" rel="nofollow" target="_Blank" href="https://www.fmeaddons.com/woocommerce-plugins-extensions/customer-registration-plugin.html">Fmeaddons</a></p>  </div>
        	
       <?php }


       function fme_extra_registration_form_end() {  ?>

        	<h3><?php echo get_option('profile_title'); ?></h3>
        	<?php 
        	$fields = $this->get_fields();
        	foreach ($fields as $field) { 
        		if($field->field_type == 'text' && $field->is_hide == 0) {
        	?>

	        	<p class="form-row <?php echo $field->width; ?>">
					<label for="<?php echo $field->field_name; ?>"><?php esc_attr_e( $field->field_label, 'woocommerce' ); ?> 
						<?php if($field->is_required == 1) { ?> <span class="required">*</span> <?php } ?>
					</label>
					<input type="text" class="input-text" name="<?php echo $field->field_name; ?>" id="<?php echo $field->field_name; ?>" value="<?php if ( ! empty( $_POST[$field->field_name] ) ) esc_attr_e( $_POST[$field->field_name] ); ?>" placeholder="<?php echo $field->field_placeholder; ?>" />
				</p>
	        		
	        	<?php } else if($field->field_type == 'textarea' && $field->is_hide == 0) { ?>

	        	<p class="form-row <?php echo $field->width; ?>">
					<label for="<?php echo $field->field_name; ?>"><?php esc_attr_e( $field->field_label, 'woocommerce' ); ?> 
						<?php if($field->is_required == 1) { ?> <span class="required">*</span> <?php } ?>
					</label>
					<textarea name="<?php echo $field->field_name; ?>" id="<?php echo $field->field_name; ?>" class="input-text" cols="5" rows="2" placeholder="<?php echo $field->field_placeholder; ?>"><?php if ( ! empty( $_POST[$field->field_name] ) ) esc_attr_e( $_POST[$field->field_name] ); ?></textarea>
					
				</p>

	        	<?php } else if($field->field_type == 'select' && $field->is_hide == 0) { ?>

	        	<p class="form-row <?php echo $field->width; ?>">
					<label for="<?php echo $field->field_name; ?>"><?php esc_attr_e( $field->field_label, 'woocommerce' ); ?> 
						<?php if($field->is_required == 1) { ?> <span class="required">*</span> <?php } ?>
					</label>
					<select name="<?php echo $field->field_name; ?>" id="<?php echo $field->field_name; ?>">
					<?php $options = $this->getSelectOptions($field->field_id);
						foreach($options as $option) {
					?>
					<?php if ( ! empty( $_POST[$field->field_name] ) ) { ?>
						<option value="<?php echo $option->meta_key; ?>" <?php echo selected($_POST[$field->field_name], $option->meta_key); ?>><?php echo $option->meta_value; ?></option>
					<?php } else { ?>
						<option value="<?php echo $option->meta_key; ?>"><?php echo $option->meta_value; ?></option>
					<?php } ?>

					<?php } ?>

					</select>
					
				</p>

	        	<?php } else if($field->field_type == 'checkbox' && $field->is_hide == 0) { ?>

	        	<p class="form-row <?php echo $field->width; ?>">

	        		<?php if ( ! empty( $_POST[$field->field_name] ) ) { ?>
					
						<input type="checkbox" name="<?php echo $field->field_name; ?>" value="1" class="input-checkbox" <?php echo checked($_POST[$field->field_name], 1); ?>>
					<?php } else { ?>
						<input type="checkbox" name="<?php echo $field->field_name; ?>" value="1" class="input-checkbox">
					<?php } ?>
					<?php _e( $field->field_label, 'woocommerce' ); ?> 
						<?php if($field->is_required == 1) { ?> <span class="required">*</span> <?php } ?>
					
				</p>

	        	<?php } else if($field->field_type == 'radioselect' && $field->is_hide == 0) { ?>

	        	<p class="form-row <?php echo $field->width; ?>">
	        		<label for="<?php echo $field->field_name; ?>"><?php _e( $field->field_label, 'woocommerce' ); ?> 
						<?php if($field->is_required == 1) { ?> <span class="required">*</span> <?php } ?>
					</label>
					<?php $options = $this->getSelectOptions($field->field_id);
						foreach($options as $option) {
					?>
					<?php if ( ! empty( $_POST[$field->field_name] ) ) { ?>
						<input type="radio" name="<?php echo $field->field_name; ?>" value="<?php echo $option->meta_key; ?>" class="input-checkbox"  <?php echo checked($_POST[$field->field_name], $option->meta_key); ?>> <?php echo $option->meta_value; ?>
					<?php } else { ?>
						<input type="radio" name="<?php echo $field->field_name; ?>" value="<?php echo $option->meta_key; ?>" class="input-checkbox"> <?php echo $option->meta_value; ?>
					<?php } ?>
					<?php } ?>
	        	</p>

	        	<?php } ?> 

        	<?php } ?>
        	
       <?php }



       function fme_extra_registration_form_edit($user_id) {  ?>

        	<h3><?php echo esc_attr_e(get_option('profile_title')); ?></h3>
        	<?php $this->fmera_show_error_messages(); ?>

        	<?php 

        	$fields = $this->get_fields();
        	foreach ($fields as $field) { 
        		
        	$value = get_user_meta( $user_id, $field->field_name, true );
        		
        		if($field->field_type == 'text' && $field->is_hide == 0) {
        	?>

	        	<p class="form-row <?php echo $field->width; ?>">
					<label for="<?php echo $field->field_name; ?>"><?php _e( $field->field_label, 'woocommerce' ); ?> 
						<?php if($field->is_required == 1) { ?> <span class="required">*</span> <?php } ?>
					</label>
					<input type="text" class="input-text" name="<?php echo $field->field_name; ?>" id="<?php echo $field->field_name; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $field->field_placeholder; ?>" />
				</p>
	        		
	        	<?php } else if($field->field_type == 'textarea' && $field->is_hide == 0) { ?>

	        	<p class="form-row <?php echo $field->width; ?>">
					<label for="<?php echo $field->field_name; ?>"><?php _e( $field->field_label, 'woocommerce' ); ?> 
						<?php if($field->is_required == 1) { ?> <span class="required">*</span> <?php } ?>
					</label>
					<textarea name="<?php echo $field->field_name; ?>" id="<?php echo $field->field_name; ?>" class="input-text" cols="5" rows="2" placeholder="<?php echo $field->field_placeholder; ?>"><?php echo $value; ?></textarea>
					
				</p>

	        	<?php } else if($field->field_type == 'select' && $field->is_hide == 0) { ?>

	        	<p class="form-row <?php echo $field->width; ?>">
					<label for="<?php echo $field->field_name; ?>"><?php _e( $field->field_label, 'woocommerce' ); ?> 
						<?php if($field->is_required == 1) { ?> <span class="required">*</span> <?php } ?>
					</label>
					<select name="<?php echo $field->field_name; ?>" id="<?php echo $field->field_name; ?>">
					<?php $options = $this->getSelectOptions($field->field_id);
						foreach($options as $option) {
					?>
						<option value="<?php echo $option->meta_key; ?>"  <?php if($option->meta_key == $value) { echo "selected"; } ?>>
							<?php echo $option->meta_value; ?>
						</option>

					<?php } ?>

					</select>
					
				</p>

	        	<?php } else if($field->field_type == 'checkbox' && $field->is_hide == 0) { ?>

	        	<p class="form-row <?php echo $field->width; ?>">

					<input type="checkbox" name="<?php echo $field->field_name; ?>" value="1" <?php if($value == 1) { echo "checked"; } ?> class="input-checkbox">

					<?php esc_attr_e( $field->field_label, 'woocommerce' ); ?> 
						<?php if($field->is_required == 1) { ?> <span class="required">*</span> <?php } ?>
					
				</p>

	        	<?php } else if($field->field_type == 'radioselect' && $field->is_hide == 0) { ?>

	        	<p class="form-row <?php echo $field->width; ?>">
	        		<label for="<?php echo $field->field_name; ?>"><?php esc_attr_e( $field->field_label, 'woocommerce' ); ?> 
						<?php if($field->is_required == 1) { ?> <span class="required">*</span> <?php } ?>
					</label>
					<?php $options = $this->getSelectOptions($field->field_id);
						foreach($options as $option) {
					?>

					<input type="radio" name="<?php echo $field->field_name; ?>" value="<?php echo $option->meta_key; ?>" <?php if($option->meta_key == $value) { echo "checked"; } ?> class="input-checkbox"> <?php echo $option->meta_value; ?>

					<?php } ?>
	        	</p>

	        	<?php } ?> 

        	<?php } ?>
        	
       <?php }

       function fme_validate_extra_register_fields($username, $email, $validation_errors) {

       		$fields = $this->get_fields();
        	foreach ($fields as $field) { 

        		if ( isset( $_POST[$field->field_name] ) && empty( $_POST[$field->field_name] ) && ($field->is_required == 1) ) {
					$validation_errors->add( $field->field_name.'_error', __( ''.$field->field_label.' is required!', 'woocommerce' ) );
				}

        	}


        	foreach ($fields as $field) { 

        		if(!array_key_exists($field->field_name, $_POST)) {
        			if($field->is_required == 1 && $field->is_hide == 0) {
        				$validation_errors->add( $field->field_name.'_error', __( ''.$field->field_label.' is required!', 'woocommerce' ) );
        			}
        		}

        	}

       }

       function fmera_errors() {
		    static $wp_error; // Will hold global variable safely
		    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
		}

		function fmera_show_error_messages() {
			if($codes = $this->fmera_errors()->get_error_codes()) {
				echo '<ul class="woocommerce-error">';
				    // Loop error codes and display errors
				   foreach($codes as $code){
				        $message = $this->fmera_errors()->get_error_message($code);
				        echo '<li>' . $message . '</li>';
				    }
				echo '</ul>';
			}	
		}

       function submit_reg_edit_form($user_id) { 
			$fields = $this->get_fields();
        	foreach ($fields as $field) { 

        		if ( isset( $_POST[$field->field_name] ) && empty( $_POST[$field->field_name] ) && ($field->is_required == 1)) {
					$this->fmera_errors()->add( $field->field_name.'_error', __( $field->field_label.' is required!', 'woocommerce' ) );
				} else {



					if ( isset( $_POST[$field->field_name] )) {

		        		 

							update_user_meta( $user_id, $field->field_name, sanitize_text_field( $_POST[$field->field_name] ) );
						

	        		} else {
	        			update_user_meta( $user_id, $field->field_name, '' );
	        		}


				}

        	}
		}

       function fme_save_extra_register_fields($customer_id) {
       		


       		$fields = $this->get_fields();
        	foreach ($fields as $field) { 

        		if ( isset( $_POST[$field->field_name] ) || isset( $_FILES[$field->field_name] ) ) {

	        		

					update_user_meta( $customer_id, $field->field_name, sanitize_text_field( $_POST[$field->field_name] ) );
					

        		}
        	}


        	$user = new WP_User($customer_id);
 
	        $user_login = stripslashes($user->user_login);
	        $user_email = stripslashes($user->user_email);

        	$message  = sprintf(__('New user registration on your store %s: '), get_option('blogname')) . "\n\n";
	        
	        $message .= sprintf(__('Username: %s'), $user_login) . "\n\n";
	        $message .= sprintf(__('E-mail: %s'), $user_email) . "\n\n";

	        foreach ($fields as $field) {

	        	$check = get_user_meta( $customer_id, $field->field_name, true );
				$label = $this->get_fieldByName($field->field_name);
				if($check!='') {

					$value = get_user_meta( $customer_id, $field->field_name, true );

					if($label->field_type=='checkbox' && $value==1) {
						$message .= sprintf(__($label->field_label.': %s'), 'Yes') . "\n\n";
					} else if($label->field_type=='checkbox' && $value==0) {
						$message .= sprintf(__($label->field_label.': %s'), 'No') . "\n\n";
					} else if($label->field_type=='select' || $label->field_type=='radioselect') {
						$meta = $this->get_OptionByid($value, $label->field_id);

						$message .= sprintf(__($label->field_label.': %s'), $meta->meta_value) . "\n\n";
					} else {
						$message .= sprintf(__($label->field_label.': %s'), $value) . "\n\n";
					}

				}



	        }

	        @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);


       }



       function get_fields() {
			global $wpdb;
             
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->fmera_fields." WHERE field_type!='' AND type = %s ORDER BY length(sort_order), sort_order", 'registration'));      
            return $result;
		}


		function get_fieldByName($name) {
			global $wpdb;
             
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->fmera_fields." WHERE field_name = %s", $name));      
            return $result;
		}

		function get_OptionByid($name, $id) {
			global $wpdb;
             
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->fmera_meta." WHERE meta_key = %s AND field_id = %d", $name, $id));      
            return $result;
		}



		function getSelectOptions($id) {
			global $wpdb;
             
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->fmera_meta." WHERE field_id = %d", $id));      
            return $result;

		}

		function add_fmera_query_vars() {
			add_rewrite_endpoint( 'edit-profile', EP_PERMALINK | EP_PAGES );
			flush_rewrite_rules();
		}

		function change_template( $template ) {
 
		if( get_query_var( 'edit-profile') != '' ) {
 
			//Check plugin directory
			$newTemplate = plugin_dir_path( __FILE__ ) . 'view/edit_profile.php';
			if( file_exists( $newTemplate ) )
				return $newTemplate;
		}
 
		return $template;
 
	}

		

		


		function fme_my_profile() { ?>
			<div class="col2-set addresses">
				<header class="title">
					<h3><?php echo esc_attr_e(get_option('profile_title')); ?></h3>
					<?php $profile_url = wc_get_endpoint_url( 'edit-profile', get_current_user_id(), wc_get_page_permalink( 'myaccount' ) ); ?>
					<a class="edit" href="<?php echo $profile_url; ?>">Edit</a>
				</header>
			</div>
			<table class="shop_table shop_table_responsive my_account_orders">
			<tbody>
			<?php 
				$user_id = get_current_user_id();
				$fields =  $this->get_fields();
				foreach ($fields as $field) {

					$check = get_user_meta( $user_id, $field->field_name, true );
					$label = $this->get_fieldByName($field->field_name);
					if($check!='') {

						$value = get_user_meta( $user_id, $field->field_name, true );
					?>
						<tr class="order" style="text-align:left">
							<td style="width:30%;"><b><?php echo $label->field_label; ?></b></td>
							<td>
								<?php 
									if($label->field_type=='checkbox' && $value==1) { 
										echo "Yes";
									} else if($label->field_type=='checkbox' && $value==0) {
										echo "No";
									} else if($label->field_type=='select' || $label->field_type=='radioselect') { 
										$meta = $this->get_OptionByid($value, $label->field_id);
										echo $meta->meta_value;
									} else
									{
										echo $value;
									}
								?>
							</td>
						</tr>
						
					<?php }
				}

			?>

			</tbody>
			</table>
			
		<?php }


	}


	new FME_Registration_Attributes_Front();
}

?>