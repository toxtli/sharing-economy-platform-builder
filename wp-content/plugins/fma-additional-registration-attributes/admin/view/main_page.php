<script>window.twttr = (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0],
    t = window.twttr || {};
  if (d.getElementById(id)) return t;
  js = d.createElement(s);
  js.id = id;
  js.src = "https://platform.twitter.com/widgets.js";
  fjs.parentNode.insertBefore(js, fjs);

  t._e = [];
  t.ready = function(f) {
    t._e.push(f);
  };

  return t;
}(document, "script", "twitter-wjs"));</script>


<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.10";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

  <script>
  jQuery( function($) {
    $( "#tabs" ).tabs();
  } );
  </script>

<div class="fma_warp">
	<div class="fma_left">
		<h1><?php _e('Welcome to FME Addons', 'fmera'); ?></h1>
		<p class="about-text"><?php _e("FME Addons works hard to deliver perfection in its product and custom ecommerce solutions. Our team of professional programmers and software architecture experts collaborates to bring out the best solutions in terms of custom ecommerce solutions, interactive tools, and extensions.", 'fmera'); ?></p>
	
		<div class="fma_bts">
			<a class="button button-primary" href="https://wordpress.org/plugins/fma-additional-registration-attributes/#reviews" target="_blank"><?php _e('Review Us', 'fmera'); ?></a>
			<a class="button button-primary" href="https://www.paypal.com/donate/?token=k8GuBDp4X8dkrfkXXJ95M1Cdx9bXqYq2NURdPGfYNFJ8fLhZqMCLrwLD2k3tfy_q59XJ90&country.x=US&locale.x=US" target="_blank"><?php _e('Donate', 'fmera'); ?></a>
			<a class="twitter-share-button"
			  href="https://twitter.com/intent/tweet?text=https://www.fmeaddons.com/woocommerce-plugins-extensions/customer-registration-plugin.html&via=fmeaddons"
			  data-size="large">
			Tweet</a>

			<a class="fb-share-button" 
			    data-href="https://www.fmeaddons.com/woocommerce-plugins-extensions/customer-registration-plugin.html" 
			    data-layout="button" data-size="large">
			</a>
		</div>
	</div>

	<div class="fma_right">
		<div class="fma_box">
			<img src="<?php echo FMERA_URL ?>images/fme-addons.png" width="150" />
		</div>
		<div class="fma_text_box">
			<?php _e('WooCommerce Custom Registration Fields Plugin<br />Version 1.0.4', 'fmera'); ?>
		</div>
	</div>

	<div class="fma_bottom_tabs">
		<div id="tabs">
		  <ul>
		    <li><a href="#tabs-1"><?php _e("What's New", 'fmera'); ?></a></li>
		    <li><a href="#tabs-2"><?php _e("Extensions", 'fmera'); ?></a></li>
		    <li><a href="#tabs-3"><?php _e("Changelog", 'fmera'); ?></a></li>
		    <li><a href="#tabs-4"><?php _e("Support", 'fmera'); ?></a></li>
		  </ul>
		  <div id="tabs-1">
		    <h2><?php _e('New in this Release','fmera'); ?></h2>
		    <div class="release">
		    	<h3><?php _e('Fields are shown in admin user edit page!', 'fmera'); ?></h3>
		    	<p><?php _e("Now created fields along with data submitted by user are shown in admin user edit page.", "fmera"); ?></p>

		    	<h3><?php _e('Fields are sent in admin email!', 'fmera'); ?></h3>
		    	<p><?php _e("Now created fields along with data submitted by user are sent in admin email, Now admin can review user entered data without logged in admin.", "fmera"); ?></p>

		    	<h3><?php _e('Updated Admin Panel!', 'fmera'); ?></h3>
		    	<p><?php _e("We have updated our extension admin panel design, now you can send us support message from extension admin panel and you can also check all our extensions.", "fmera"); ?></p>
		    </div>
		  </div>
		  <div id="tabs-2">
		    <div class="our_modules">

		    	<?php
			    	$xml_data = simplexml_load_file("http://woocommerce.fmeaddons.net/abandondcarts/fma.xml");

			    	foreach ($xml_data as $module) { 
			    		foreach ($module as $key => $value) { 
			    	?>

		    	
				
				<div class="our_mod">
					<div class="pic">
					    <img src="<?php echo $value->image; ?>" class="pic-image" alt="<?php echo $value->name; ?>"/>
					    <span class="pic-caption rotate-in">
					        <h2><a href="<?php echo $value->url; ?>" target="_blank"><?php echo $value->name; ?></a></h2>
					        <p><?php echo $value->text; ?></p>
					    	<a href="<?php echo $value->url; ?>" target="_blank" class="button button-secendory"><?php _e('Learn More','fmera'); ?></a>
					    </span>

					</div>

					
				</div>

				<?php } } ?>




		    </div>
		  </div>
		  <div id="tabs-3">
		  	<div class="release">
		    	<h3><?php _e('1.0.4', 'fmera'); ?></h3>
		    	<ul>
		    		<li><?php _e('Now created fields along with data submitted by user are shown in admin user edit page.','fmera'); ?></li>
		    		<li><?php _e('Now created fields along with data submitted by user are sent in admin email, Now admin can review user entered data without logged in admin.','fmera'); ?></li>
		    		<li><?php _e('We have updated our extension admin panel design, now you can send us support message from extension admin panel and you can also check all our extensions.', 'fmera'); ?></li>
		    	</ul>

		    	<h3><?php _e('1.0.3', 'fmera'); ?></h3>
		    	<ul>
		    		<li><?php _e('Make Compatible with woocommerce 3.0.+.','fmera'); ?></li>
		    		<li><?php _e('Data not shown in edit account form when permalinks set to other than plan url.','fmera'); ?></li>
		    		<li><?php _e('Data not save in edit account form when permalinks set to other than plan url.','fmera'); ?></li>

		    	</ul>

		    	<h3><?php _e('1.0.2', 'fmera'); ?></h3>
		    	<ul>
		    		<li><?php _e('Issues with the edit account page (goes on 404) when permalinks set to other than plan url.','fmera'); ?></li>
		    	</ul>

		    	<h3><?php _e('1.0.1', 'fmera'); ?></h3>
		    	<ul>
		    		<li><?php _e('Remove waring undefined index url.','fmera'); ?></li>
					<li><?php _e('Remove warning shown for the prepare command.','fmera'); ?></li>
					<li><?php _e('Remove unused code from all over the moduel that is causing error and warnings.','fmera'); ?></li>
					<li><?php _e('Add post data sentization in all over the module.','fmera'); ?></li>
					<li><?php _e('Resolve prepare command issue in all over the module.','fmera'); ?></li>
					<li><?php _e('Bug Fixed: issue with the validation of radio button, checkboxes and multi select.','fmera'); ?></li>
		    	</ul>

		    	<h3><?php _e('1.0.0', 'fmera'); ?></h3>
		    	<ul>
		    		<li><?php _e('This is the initial release of plugin.','fmera'); ?></li>
		    	</ul>
		    </div>
		  </div>
		  <div id="tabs-4">
		    
				<div class="fmeaddons-support">
		
					<h3><?php _e('Welcome to FME Addons Support – We are here to help', 'fmera') ?></h3>

					<div class="about-text"><?php _e('Our customer support team is powered with enthusiasm to serve you the best in solving a technical issue or answering your queries in time. If you have got a question, please do not hesitate to ask us in this easy to fill form, and we assure you a prompt reply.', 'fmera') ?>
						
					</div>
					
					<?php 
				
					$active_plugins = (array) get_option( 'active_plugins', array() );

					if ( is_multisite() ) {
						$active_plugins = array_merge( $active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
					}


					$a = 0;
					foreach ( $active_plugins as $plugin ) {

							$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
							
							if($plugin_data['AuthorName'] == 'FME Addons') {
								$a++;
							}
							
					}

					?>

					

					<div class="fmeaddons-support-active">

							<table class="widefat" cellspacing="0" id="status">

								<thead>
									<tr>
										<th>FME Addons Active Plugin (<?php echo $a; ?>)</th>
										<th>Version</th>
										<th>Company </th>
									</tr>
								</thead>
								
								<tbody>
									<?php
									foreach ( $active_plugins as $plugin ) {

									$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
									$dirname        = dirname( $plugin );
									$version_string = '';
									$network_string = '';

									if ( in_array('FME Addons', $plugin_data)) {

										// Link the plugin name to the plugin url if available.
										if ( ! empty( $plugin_data['PluginURI'] ) ) {
											$plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . __( 'Visit plugin homepage' , 'fmera' ) . '">' . esc_html( $plugin_data['Name'] ) . '</a>';
										} else {
											$plugin_name = esc_html( $plugin_data['Name'] );
										}
										?>
										<tr>
											<td>
												<?php echo $plugin_name; ?>
											</td>
											<td>
												<?php echo $plugin_data['Version']; ?>
											</td>
											<td>
												<?php printf( esc_attr__( 'by %s', 'fmera' ), '<a href="' . esc_url( $plugin_data['AuthorURI'] ) . '" target="_blank">' . esc_html( $plugin_data['AuthorName'] ) . '</a>' ) . ' &ndash; ' . esc_html( $plugin_data['Version'] ) . $version_string . $network_string; ?>
											</td>
										</tr>
										<?php
									}
									} ?>
								</tbody>

							</table>

					</div>


					<div class="fmeaddons-support-form">

						<h4>Contact FME Addons Support Team</h4>
						
						<h5 id="fmeaddons_sup_success">Your message has been successfully sent. We will contact you very soon!</h5>
						
						<form id="fmeaddons-form-support">
							
							<div class="fmeaddons-field">
								<!-- <label>Enter First Name</label> -->
								<input data-parsley-required type="text" id="ex_customer_fname" name="ex_customer_fname" placeholder="First Name">
								<!-- <label>Enter Last Name</label> -->
								<input data-parsley-required type="text" id="ex_customer_lname" name="ex_customer_lname" placeholder="Last Name">
							</div>

							<div class="fmeaddons-field">
								<!-- <label>Enter Your Email</label> -->
								<input data-parsley-required type="email" id="ex_customer_email" name="ex_customer_email" placeholder="Your Email">
								<!-- <label>Enter Your Phone</label> -->
								<input type="number" min="0" id="ex_customer_number" name="ex_customer_number" placeholder="Phone Number">
							</div>

							<div class="fmeaddons-field">
								<!-- <label>Subject</label> -->
								<input type="text" id="ex_customer_subject" name="ex_customer_subject" placeholder="Subject">
								<!-- <label>Select Module</label> -->
								<select id="ex_support_module" name="ex_support_module">
									<option value="0" >Select Plugin</option>
									<option selected="selected" value="fmeaddons-faq"><?php echo $plugin_name; ?></option>
								</select>
							</div>

							<div class="fmeaddons-field">
								<!-- <label>Your Message</label> -->
								<textarea data-parsley-required rows="8" id="ex_customer_message" name="ex_customer_message" placeholder="Message"></textarea>
							</div>

							<div class="fmeaddons-field">
								<!-- <label></label> -->
								<input id="fmeaddons-submit" type="button" onclick="fmasupport()" name="" value="Send Request">
							</div>
						
						</form>

						<div class="fmeaddons-socials">
							<ul class="fmeaddons-social-left">
								<li>
									<a target="_blank" href="https://www.facebook.com/fmeaddons">
										<img src="<?php echo FMERA_URL.'images/fb.png'; ?>">
									</a>
								</li>
								<li>
									<a target="_blank" href="https://plus.google.com/+Fmeaddons">
										<img src="<?php echo FMERA_URL.'images/google_plus.png'; ?>">
									</a>
								</li>
								<li>
									<a target="_blank" href="https://www.fmeaddons.com/">
										<img src="<?php echo FMERA_URL.'images/fme-addons.png'; ?>" width="80" >
									</a>
								</li>
								<li>
									<a target="_blank" href="https://www.linkedin.com/company/fmeaddons">
										<img src="<?php echo FMERA_URL.'images/linkedin.png'; ?>">
									</a>
								</li>
								<li>
									<a target="_blank" href="https://twitter.com/fmeaddons">
										<img src="<?php echo FMERA_URL.'images/twitter.png'; ?>">
									</a>
								</li>
							</ul>
						</div>

					</div>

				</div>
	
		


		  </div>
		</div>
	</div>
	
</div>


<script type="text/javascript">
			
			// support email
			function fmasupport() { 
				
				jQuery('#fmeaddons-form-support').parsley().validate();	
				var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
				var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
				var condition = 'support_contact';
				var suppextfname = jQuery('#ex_customer_fname').val();
				var suppextlname = jQuery('#ex_customer_lname').val();
				var suppextemail = jQuery('#ex_customer_email').val();
				var suppextnumber = jQuery('#ex_customer_number').val();
				var suppextsubj = jQuery('#ex_customer_subject').val();
				var suppextmasg = jQuery('#ex_customer_message').val();
				if(suppextfname == '' && suppextlname == '' && suppextemail == '' && suppextmasg == '') {
					return false;
				}else if (suppextfname == '') { 
					return false;
				} else if(suppextlname == '') {
					return false;
				} else if (suppextemail == '') {
					return false;
				}else if (!pattern.test(suppextemail)) {
					return false;
				}else if (suppextmasg == '' ) {
					return false;
				}else {

					jQuery.ajax({
						url : ajaxurl,
						type : 'post',
						data : {
							action : 'support_contact',
							condition : condition,
							suppextfname : suppextfname,
							suppextlname : suppextlname,
							suppextemail : suppextemail,
							suppextnumber : suppextnumber,
							suppextsubj : suppextsubj,
							suppextmasg : suppextmasg,		

						},
						success : function(response) {
							jQuery('#fmeaddons_sup_success').show().delay(3000).fadeOut();
							jQuery('#fmeaddons-form-support').each(function() {
								this.reset(); 
							});
						}
					});
				}
			}

		</script>