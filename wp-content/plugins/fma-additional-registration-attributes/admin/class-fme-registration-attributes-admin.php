<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'FME_Registration_Attributes_Admin' ) ) { 

	class FME_Registration_Attributes_Admin extends FME_Registration_Attributes {

		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'admin_init' ) );
			add_action('wp_ajax_update_sortorder', array($this, 'update_sortorder'));
			add_action('wp_ajax_insert_field', array($this, 'insert_field')); 
			add_action('wp_ajax_del_field', array($this, 'del_field'));
			add_action('wp_ajax_save_all_data', array($this, 'save_all_data'));
			add_action( 'edit_user_profile', array($this, 'fme_profile_fields' ));
			add_action( 'edit_user_profile_update', array($this, 'fme_update_profile_fields' ));

			add_action( 'wp_ajax_fmera_settings_opt', array($this,'fmera_settings_opt' ));
			add_action( 'wp_ajax_nopriv_fmera_settings_opt', array($this,'fmera_settings_opt' ));

			add_action( 'wp_ajax_support_contact', array($this,'support_callback' ));
			add_action( 'wp_ajax_nopriv_support_contact', array($this,'support_callback' ));
		}

		public function admin_init() {
			add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );	
		}

		public function admin_scripts() {	
            
        	wp_enqueue_style( 'fmera-admin-css', plugins_url( '/css/fmera_style.css', __FILE__ ), false );
        	wp_enqueue_script( 'jquery-ui');
        	wp_enqueue_script( 'jquery-ui-tabs');
        	wp_enqueue_style( 'jquery-ui-tabs-css');
        	wp_enqueue_script( 'jquery-ui-draggable');
        	wp_enqueue_script( 'jquery-ui-dropable');
        	wp_enqueue_script( 'jquery-ui-sortable');
        	wp_enqueue_script( 'fmera-admin-jsssssss', plugins_url( '/js/fmera_admin.js', __FILE__ ), array('jquery'), false );
        	wp_enqueue_script('parsley-js', plugins_url( '/js/parsley.min.js', __FILE__ ), false );
			wp_enqueue_style('parsley-css', plugins_url( '/css/parsley.css', __FILE__ ), false );
        }

        public function create_admin_menu() {	
			add_menu_page('Registration Form', __( 'Registration Form', 'fmera' ), apply_filters( 'fmera_capability', 'manage_options' ), 'fmeaddon-add-registration-attributes', array( $this, 'fmera_registration_main_page' ) ,plugins_url( 'images/fma.png', dirname( __FILE__ ) ), apply_filters( 'fmera_menu_position', 7 ) );
			add_submenu_page( 'fmeaddon-add-registration-attributes', __( 'Create Form Fields', 'fmera' ), __( 'Create Form Fields', 'fmera' ), 'manage_options', 'fmera_registration_fields_module', array( $this, 'fmera_registration_fields_module' ) );
			add_submenu_page( 'fmeaddon-add-registration-attributes', __( 'Settings', 'fmera' ), __( 'Settings', 'fmera' ), 'manage_options', 'fmera_settings', array( $this, 'fmera_mdoule_settings' ) );	

	        register_setting( 'fmera_settings', 'fmera_settings', array( $this, 'fmera_settings' ) );

	    }


	    function fmera_registration_main_page() {
	    	require  FMERA_PLUGIN_DIR . 'admin/view/main_page.php';
	    }

	    public function fmera_mdoule_settings() {
			require  FMERA_PLUGIN_DIR . 'admin/view/settings.php';
		}

		

		public function fmera_settings_opt() { 

			if(isset($_POST['condition']) && $_POST['condition'] == "fmera_setting") {
				
				update_option( 'account_title', $_POST['account_title'], null );
				update_option( 'profile_title', $_POST['profile_title'], null );
				
				
			}

			die();

		}

		function fmera_registration_fields_module() {
	    	require_once( FMERA_PLUGIN_DIR . 'admin/view/view.php' );
	    }

	    public function get_reg_fields() {
            
             global $wpdb;
             $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->fmera_fields." WHERE field_type!='' AND type = %s ORDER BY length(sort_order), sort_order", 'registration'));      
             return $result;
        }

        function update_sortorder() {
			global $wpdb;
			$fieldids = $_POST['fieldids'];
			$counter = 1;
			foreach ($fieldids as $fieldid) {

				$wpdb->query($wpdb->prepare( 
				            "
			    UPDATE " .$wpdb->fmera_fields." SET sort_order = %d WHERE field_id = %d
			    ",
				    $counter,
				    intval($fieldid)
				));

				
				$counter = $counter + 1;	
			}	

		}

		function insert_field() {
			global $wpdb;
			$last1 = $wpdb->get_row("SHOW TABLE STATUS LIKE '$wpdb->fmera_fields'");
        	$a = ($last1->Auto_increment);
			if(isset($_POST['fieldtype']) && $_POST['fieldtype']!='') {
				$fieldtype = sanitize_text_field($_POST['fieldtype']);
			} else { $fieldtype = '';}
			if(isset($_POST['type']) && $_POST['type']!='') {
				$type = sanitize_text_field($_POST['type']);
			} else { $type = ''; }
			if(isset($_POST['label']) && $_POST['label']!='') {
				$label = sanitize_text_field($_POST['label']);
			} else { $label = ''; }
			$name = 'registration_field_'.$a;
			if(isset($_POST['mode']) && $_POST['mode']!='') {
				$mode = sanitize_text_field($_POST['mode']);
			} else { $mode = ''; }
			if($fieldtype!='' && $type!='' && $label!='') {
				$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->fmera_fields
	            (field_name, field_label, field_type, type, field_mode)
	            VALUES (%s, %s, %s, %s, %s)
	            ",
	            $name,
	            $label, 
	            $fieldtype,
	            $type,
	            $mode
	            
	            
	            ) );
			}
			
			$last = $wpdb->get_row("SHOW TABLE STATUS LIKE '$wpdb->fmera_fields'");
        	echo json_encode(($last->Auto_increment)-1);
			exit();


		}

		function del_field() {
			if ( !current_user_can( apply_filters( 'fmepco_capability', 'manage_options' ) ) )
			die( '-1' );

			check_ajax_referer( 'fmeradel-ajax-nonce', 'delsecurity', false );
			$field_id = intval($_POST['field_id']);
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->fmera_fields . " WHERE field_id = %d", $field_id ) );
			die();
			return true;
		}


		function save_all_data() { 
			global $wpdb;

			if ( !current_user_can( apply_filters( 'fmepco_capability', 'manage_options' ) ) )
			die( '-1' );

			check_ajax_referer( 'fmera-ajax-nonce', 'security', false );


			if(isset($_POST['option_field_ids']) && $_POST['option_field_ids']!='') {
				$option_field_ids = $_POST['option_field_ids']; 			
			} else {$option_field_ids = array();}
			if(isset($_POST['option_value']) && $_POST['option_value']!='') {
				$option_value = $_POST['option_value'];	
			} else {$option_value = array();}
			if(isset($_POST['option_text']) && $_POST['option_text']!='') {
				$option_text = $_POST['option_text'];			
			} else { $option_text = array(); }


			if(isset($_POST['fieldids']) && $_POST['fieldids']!='') {
				$fieldids = $_POST['fieldids'];			
			} else { $fieldids = array(); }
			if(isset($_POST['fieldlabel']) && $_POST['fieldlabel']!='') {
				$fieldlabel = $_POST['fieldlabel'];			
			} else { $fieldlabel = array(); }
			if(isset($_POST['fieldplaceholder']) && $_POST['fieldplaceholder']!='') {
				$fieldplaceholder = $_POST['fieldplaceholder'];			
			} else { $fieldplaceholder = array(); }
			if(isset($_POST['fieldrequired']) && $_POST['fieldrequired']!='') {
				$fieldrequired = $_POST['fieldrequired'];			
			} else { $fieldrequired = array(); }
			if(isset($_POST['fieldhidden']) && $_POST['fieldhidden']!='') {
				$fieldhidden = $_POST['fieldhidden'];			
			} else { $fieldhidden = array(); }
			if(isset($_POST['fieldwidth']) && $_POST['fieldwidth']!='') {
				$fieldwidth = $_POST['fieldwidth'];			
			} else { $fieldwidth = array(); }




			$combined_array1 = array_map(function($a, $b, $c) { return $a.'-_-'.$b.'-_-'.$c; }, $option_field_ids, $option_value, $option_text);
			$wpdb->query("DELETE FROM ".$wpdb->fmera_meta );

			if($combined_array1!='') {
				foreach ($combined_array1 as $value) {

					$data = explode('-_-', $value);

					$wpdb->query($wpdb->prepare( 
		            "
		            INSERT INTO $wpdb->fmera_meta
		            (field_id, meta_key, meta_value)
		            VALUES (%s, %s, %s)
		            ",
		            intval($data[0]),
		            sanitize_text_field($data[1]), 
		            sanitize_text_field($data[2])
		            
		            ) );

				}
			}
			//print_r($_POST['fieldids']); 
			$combined_array = array_map(function($a, $b, $c, $d, $e, $f) { return $a.'-_-'.$b.'-_-'.$c.'-_-'.$d.'-_-'.$e.'-_-'.$f; }, 
				$fieldids, $fieldlabel, $fieldplaceholder, $fieldrequired, $fieldhidden, $fieldwidth);
			
			if($combined_array!='') {
				foreach ($combined_array as $value) {
					
					$data = explode('-_-', $value);
					$field_id = intval($data[0]);
					$field_label = sanitize_text_field($data[1]);
					$field_placeholder = sanitize_text_field($data[2]);
					$field_required = sanitize_text_field($data[3]);
					$field_hide = sanitize_text_field($data[4]);
					$field_width = sanitize_text_field($data[5]);

					$wpdb->query($wpdb->prepare(
						"UPDATE " .$wpdb->fmera_fields." SET field_label = %s, field_placeholder = %s, 
						is_required = %d, is_hide = %d, width = %s WHERE field_id = %d",
					    $field_label,
					    $field_placeholder,
					    $field_required,
					    $field_hide,
					    $field_width,
					    $field_id
					));

				}
			}

			die();
			return true;
		}

		public function getOptions($id) {
			global $wpdb;
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->fmera_meta." WHERE field_id = %d", $id));      
            return $result;
		}

		

		function fme_profile_fields() { ?>

			<h3><?php echo _e('Customer Profile Information:', 'fmera'); ?></h3>
			<table class="form-table">
				<tbody>
					<?php 
						$fields = $this->get_reg_fields();
						foreach ($fields as $field) { 
					
						$value = get_user_meta( $_GET['user_id'], $field->field_name, true );

						if($field->field_type == 'text' && $field->is_hide == 0) {
					?>
							<tr>
								<th><label for="<?php echo $field->field_name; ?>"><?php echo $field->field_label; ?></label></th>
								<td>
									<input type="text" class="regular-text" value="<?php echo $value; ?>" id="<?php echo $field->field_name; ?>" name="<?php echo $field->field_name; ?>">
									<br>
									<span class="description"></span>
								</td>
							</tr>

						<?php } else if($field->field_type == 'textarea' && $field->is_hide == 0) { ?>

							<tr>
								<th><label for="<?php echo $field->field_name; ?>"><?php echo $field->field_label; ?></label></th>
								<td>
									<textarea cols="30" rows="5" id="<?php echo $field->field_name; ?>" name="<?php echo $field->field_name; ?>"><?php echo $value; ?></textarea>
									<br>
									<span class="description"></span>
								</td>
							</tr>

						<?php } else if($field->field_type == 'select' && $field->is_hide == 0) { ?>

							<tr>
								<th><label for="<?php echo $field->field_name; ?>"><?php echo $field->field_label; ?></label></th>
								<td>
									<select style="min-width:200px;" id="<?php echo $field->field_name; ?>" name="<?php echo $field->field_name; ?>">
										<?php $options = $this->getOptions($field->field_id);
											foreach($options as $option) {
										?>
											<option value="<?php echo $option->meta_key; ?>" <?php if($option->meta_key == $value) { echo "selected"; } ?>>
												<?php echo $option->meta_value; ?>
											</option>

										<?php } ?>
									</select>
									<br>
									<span class="description"></span>
								</td>
							</tr>

						<?php } else if($field->field_type == 'checkbox' && $field->is_hide == 0) { ?>

							<tr> 
								<th><label for="<?php echo $field->field_name; ?>"><?php echo $field->field_label; ?></label></th>
								<td>
									<input type="checkbox" name="<?php echo $field->field_name; ?>" id="<?php echo $field->field_name; ?>" value="<?php echo $value; ?>" <?php if($value == 1) { echo "checked"; } ?> />
									<br>
									<span class="description"></span>
								</td>
							</tr>

						<?php } else if($field->field_type == 'radioselect' && $field->is_hide == 0) { ?>

							<tr> 
								<th><label for="<?php echo $field->field_name; ?>"><?php echo $field->field_label; ?></label></th>
								<td>
									<?php $options = $this->getOptions($field->field_id);
											foreach($options as $option) {
									?>

									<input type="radio" name="<?php echo $field->field_name; ?>" id="<?php echo $field->field_name; ?>" value="<?php echo $option->meta_key; ?>" <?php if($option->meta_key == $value) { echo "checked"; } ?> /> <?php echo $option->meta_value; ?>
									
									<?php } ?>
									<br>
									<span class="description"></span>
								</td>
							</tr>

						<?php } ?>



					<?php } ?>
				</tbody>
			</table>

		<?php }


		function fme_update_profile_fields($user_id) {
       		


       		$fields = $this->get_reg_fields();
        	foreach ($fields as $field) { 

        		if ( isset( $_POST[$field->field_name] )) {

					update_user_meta( $user_id, $field->field_name, sanitize_text_field( $_POST[$field->field_name] ) );
        		} else {
        			update_user_meta( $user_id, $field->field_name, '' );
        		}
        	}


       }


       function support_callback () {
			
			if(isset($_POST['condition']) && $_POST['condition'] == "support_contact") {

					$support_fname = $_POST['suppextfname'];
					$support_lname = $_POST['suppextlname'];
					$support_email = $_POST['suppextemail'];
					$support_number = $_POST['suppextnumber'];
					$support_subject = $_POST['suppextsubj'];
					$support_message = $_POST['suppextmasg'];	

					$to = "support@fmeaddons.com";
					$subject = $support_subject;

					$message = "
					<html>
					<head>
					<title>Question Woocommerece.</title>
					</head>
					<body>
					<table>
					<tr>
					<td><b>First Name:</b></td>
					<td>$support_fname</td>
					</tr>
					<tr>
					<td><b>Last Name:</b></td>
					<td>$support_lname</td>
					</tr>
					<tr>
					<td><b>Email:</b></td>
					<td>$support_email</td>
					</tr>
					<tr>
					<td><b>Phone:</b></td>
					<td>$support_number</td>
					</tr>
					<tr>
					<td><b>Subject:</b></td>
					<td>$support_subject</td>
					</tr>
					<tr>
					<td><b>Message:</b></td>
					<td>$support_message</td>
					</tr>
					</table>
					</body>
					</html>
					";
					
					$headers .= "MIME-Version: 1.0\n";
					$headers .= "Content-type: text/html; charset=iso-8859-1\n";
					// $headers .= 'From: '.$admin_email.'' . "\r\n";
					// $headers .= 'Cc: '.$admin_email.'' . "\r\n";
					
					mail($to,$subject,$message,$headers);
				
			}

			die();
		}
		

	}

	new FME_Registration_Attributes_Admin();
}

?>