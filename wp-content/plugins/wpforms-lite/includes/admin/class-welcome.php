<?php
/**
 * Welcome page class.
 *
 * This page is shown when the plugin is activated.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
class WPForms_Welcome {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'register'  )       );
		add_action( 'admin_head', array( $this, 'hide_menu' )       );
		add_action( 'admin_init', array( $this, 'redirect'  ), 9999 );
	}

	/**
	 * Register the pages to be used for the Welcome screen (and tabs).
	 *
	 * These pages will be removed from the Dashboard menu, so they will
	 * not actually show. Sneaky, sneaky.
	 *
	 * @since 1.0.0
	 */
	public function register() {

		// Getting started - shows after installation.
		add_dashboard_page(
			__( 'Welcome to WPForms', 'wpforms' ),
			__( 'Welcome to WPForms', 'wpforms' ),
			apply_filters( 'wpforms_welcome_cap', 'manage_options' ),
			'wpforms-getting-started',
			array( $this, 'output' )
		);
	}

	/**
	 * Removed the dashboard pages from the admin menu.
	 *
	 * This means the pages are still available to us, but hidden.
	 *
	 * @since 1.0.0
	 */
	public function hide_menu() {

		remove_submenu_page( 'index.php', 'wpforms-getting-started' );
	}

	/**
	 * Welcome screen redirect.
	 *
	 * This function checks if a new install or update has just occured. If so,
	 * then we redirect the user to the appropriate page.
	 *
	 * @since 1.0.0
	 */
	public function redirect() {

		// Check if we should consider redirection.
		if ( ! get_transient( 'wpforms_activation_redirect' ) ) {
			return;
		}

		// If we are redirecting, clear the transient so it only happens once.
		delete_transient( 'wpforms_activation_redirect' );

		// Check option to disable welcome redirect.
		if ( get_option( 'wpforms_activation_redirect', false ) ) {
			return;
		}

		// Only do this for single site installs.
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		// Check if this is an update or first install.
		$upgrade = get_option( 'wpforms_version_upgraded_from' );

		if ( ! $upgrade ) {
			// Initial install.
			wp_safe_redirect( admin_url( 'index.php?page=wpforms-getting-started' ) );
			exit;
		}
	}

	/**
	 * Getting Started screen. Shows after first install.
	 *
	 * @since 1.0.0
	 */
	public function output() {

		$class = wpforms()->pro ? 'pro' : 'lite';
		?>
		<div id="wpforms-welcome" class="<?php echo $class; ?>">

			<div class="container">

				<div class="intro">

					<div class="sullie">
						<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/sullie.png" alt="<?php esc_attr_e( 'Sullie the WPForms mascot' , 'wpforms' ); ?>">
					</div>

					<div class="block">
						<h1><?php _e( 'Welcome to WPForms', 'wpforms' ); ?></h1>
						<h6><?php _e( 'Thank you for choosing WPForms - the most powerful drag & drop WordPress form builder in the market.', 'wpforms' ); ?></h6>
					</div>

					<a href="#" class="play-video" title="<?php esc_attr_e( 'Watch how to create your first form', 'wpforms' ); ?>">
						<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/welcome-video.png" alt="<?php esc_attr_e( 'Watch how to create your first form', 'wpforms' ); ?>" class="video-thumbnail">
					</a>

					<div class="block">

						<h6><?php _e( 'WPForms makes it easy to create forms in WordPress. You can watch the video tutorial or read our guide on how create your first form.', 'wpforms' ); ?></h6>

						<div class="button-wrap wpforms-clear">
							<div class="left">
								<a href="<?php echo admin_url( 'admin.php?page=wpforms-builder' ); ?>" class="wpforms-btn wpforms-btn-block wpforms-btn-lg wpforms-btn-orange"><?php _e( 'Create Your First Form', 'wpforms' ); ?></a>
							</div>
							<div class="right">
								<a href="https://wpforms.com/docs/creating-first-form/?utm_source=WordPress&amp;utm_medium=link&amp;utm_campaign=liteplugin" class="wpforms-btn wpforms-btn-block wpforms-btn-lg wpforms-btn-grey" target="_blank" rel="noopener noreferrer"><?php _e( 'Read the Full Guide', 'wpforms' ); ?></a>
							</div>
						</div>

					</div>

				</div><!-- /.intro -->

				<div class="features">

					<div class="block">

						<h1><?php _e( 'Welcome Features &amp; Addons', 'wpforms' ); ?></h1>
						<h6><?php _e( 'WPForms is both easy to use and extremely powerful. We have tons of helpful features that allow us to give you everything you need from a form builder.', 'wpforms' ); ?></h6>

						<div class="feature-list wpforms-clear">

							<div class="feature-block first">
								<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/welcome-feature-icon-1.png">
								<h5><?php _e( 'Drag &amp; Drop Form Builder', 'wpforms' ); ?></h5>
								<p><?php _e( 'Easily create an amazing form in just a few minutes without writing any code.', 'wpforms' ); ?></p>
							</div>

							<div class="feature-block last">
								<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/welcome-feature-icon-2.png">
								<h5><?php _e( 'Form Templates', 'wpforms' ); ?></h5>
								<p><?php _e( 'Start with pre-built form templates to save even more time.', 'wpforms' ); ?></p>
							</div>

							<div class="feature-block first">
								<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/welcome-feature-icon-3.png">
								<h5><?php _e( 'Responsive Mobile Friendly', 'wpforms' ); ?></h5>
								<p><?php _e( 'WPForms is 100% responsive meaning it works on mobile, tablets & desktop.', 'wpforms' ); ?></p>
							</div>

							<div class="feature-block last">
								<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/welcome-feature-icon-4.png">
								<h5><?php _e( 'Smart Conditional Logic', 'wpforms' ); ?></h5>
								<p><?php _e( 'Easily create high performance forms with our smart conditional logic.', 'wpforms' ); ?></p>
							</div>

							<div class="feature-block first">
								<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/welcome-feature-icon-5.png">
								<h5><?php _e( 'Instant Notifications', 'wpforms' ); ?></h5>
								<p><?php _e( 'Respond to leads quickly with our instant form notification feature for your team.', 'wpforms' ); ?></p>
							</div>

							<div class="feature-block last">
								<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/welcome-feature-icon-6.png">
								<h5><?php _e( 'Entry Management', 'wpforms' ); ?></h5>
								<p><?php _e( 'View all your leads in one place to streamline your workflow.', 'wpforms' ); ?></p>
							</div>

							<div class="feature-block first">
								<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/welcome-feature-icon-7.png">
								<h5><?php _e( 'Payments Made Easy', 'wpforms' ); ?></h5>
								<p><?php _e( 'Easily collect payments, donations, and online orders without hiring a developer.', 'wpforms' ); ?></p>
							</div>

							<div class="feature-block last">
								<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/welcome-feature-icon-8.png">
								<h5><?php _e( 'Marketing &amp; Subscriptions', 'wpforms' ); ?></h5>
								<p><?php _e( 'Create subscription forms and connect with your email marketing service.', 'wpforms' ); ?></p>
							</div>

							<div class="feature-block first">
								<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/welcome-feature-icon-9.png">
								<h5><?php _e( 'Easy to Embed', 'wpforms' ); ?></h5>
								<p><?php _e( 'Easily embed your forms in blog posts, pages, sidebar widgets, footer, etc.', 'wpforms' ); ?></p>
							</div>

							<div class="feature-block last">
								<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/welcome-feature-icon-10.png">
								<h5><?php _e( 'Spam Protection', 'wpforms' ); ?></h5>
								<p><?php _e( 'Our smart captcha and honeypot automatically prevent spam submissions.', 'wpforms' ); ?></p>
							</div>

						</div>

						<div class="button-wrap">
							<a href="https://wpforms.com/features/?utm_source=WordPress&amp;utm_medium=link&amp;utm_campaign=liteplugin" class="wpforms-btn wpforms-btn-lg wpforms-btn-grey" rel="noopener noreferrer" target="_blank"><?php _e( 'See All Features', 'wpforms' ); ?></a>
						</div>

					</div>

				</div><!-- /.features -->

				<div class="upgrade-cta upgrade">

					<div class="block wpforms-clear">

						<div class="left">
							<h2><?php _e( 'Upgrade to PRO', 'wpforms' ); ?></h2>
							<ul>
								<li><span class="dashicons dashicons-yes"></span> <?php _e( 'PayPal', 'wpforms' ); ?></li>
								<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Post Submissions', 'wpforms' ); ?></li>
								<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Stripe', 'wpforms' ); ?></li>
								<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Signatures', 'wpforms' ); ?></li>
								<li><span class="dashicons dashicons-yes"></span> <?php _e( 'User Registration', 'wpforms' ); ?></li>
								<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Form Abandonment', 'wpforms' ); ?></li>
								<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Geolocation', 'wpforms' ); ?></li>
								<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Unlimited Sites', 'wpforms' ); ?></li>
								<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Zapier', 'wpforms' ); ?></li>
								<li><span class="dashicons dashicons-yes"></span> <?php _e( 'Priority Support', 'wpforms' ); ?></li>
							</ul>
						</div>

						<div class="right">
							<h2><span><?php _e( 'PRO', 'wpforms' ); ?></span></h2>
							<div class="price">
								<span class="amount">199</span><br>
								<span class="term"><?php _e( 'per year', 'wpforms' ); ?></span>
							</div>
							<a href="<?php echo wpforms_admin_upgrade_link(); ?>" class="wpforms-btn wpforms-btn-block wpforms-btn-lg wpforms-btn-orange wpforms-upgrade-modal" rel="noopener noreferrer" target="_blank"><?php _e( 'Upgrade Now', 'wpforms' ); ?></a>
						</div>

					</div>

				</div>

				<div class="testimonials upgrade">

					<div class="block">

						<h1><?php _e( 'Testimonials', 'wpforms' ); ?></h1>

						<div class="testimonial-block wpforms-clear">
							<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/welcome-testimonial-bill.jpg">
							<p><?php _e( 'WPForms is by far the easiest form plugin to use. My clients love it – it’s one of the few plugins they can use without any training. As a developer I appreciate how fast, modern, clean and extensible it is.', 'wpforms' ); ?><p>
							<p><strong>Bill Erickson</strong>, Erickson Web Consulting</p>
						</div>

						<div class="testimonial-block wpforms-clear">
							<img src="<?php echo WPFORMS_PLUGIN_URL; ?>assets/images/welcome-testimonial-david.jpg">
							<p><?php _e( 'As a business owner, time is my most valuable asset. WPForms allow me to create smart online forms with just a few clicks. With their pre-built form templates and the drag & drop builder, I can create a new form that works in less than 2 minutes without writing a single line of code. Well worth the investment.', 'wpforms' ); ?><p>
							<p><strong>David Henzel</strong>, MaxCDN</p>
						</div>

					</div>

				</div><!-- /.testimonials -->

				<div class="footer">

					<div class="block wpforms-clear">

						<div class="button-wrap wpforms-clear">
							<div class="left">
								<a href="<?php echo admin_url( 'admin.php?page=wpforms-builder' ); ?>" class="wpforms-btn wpforms-btn-block wpforms-btn-lg wpforms-btn-orange"><?php _e( 'Create Your First Form', 'wpforms' ); ?></a>
							</div>
							<div class="right">
								<a href="<?php echo wpforms_admin_upgrade_link(); ?>" class="wpforms-btn wpforms-btn-block wpforms-btn-lg wpforms-btn-trans-green wpforms-upgrade-modal" rel="noopener noreferrer" target="_blank"><span class="underline"><?php _e( 'Upgrade to WPForms Pro', 'wpforms' ); ?> <span class="dashicons dashicons-arrow-right"></span></span></a>
							</div>
						</div>

					</div>

				</div><!-- /.footer -->

			</div><!-- /.container -->

		</div><!-- /#wpforms-welcome -->
		<?php
	}
}

new WPForms_Welcome;
