<?php
namespace ElementorMenus\Modules\Menus\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Elementor Elementor Navbar
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Elementor_Branding extends Widget_Base {

	public function get_name() {
		return 'elementor-branding';
	}

	public function get_title() {
		return __( 'Branding', 'navmenu-addon-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	public function get_categories() {
		return [ 'branding-elements' ];
	}

	protected function _register_controls() {
		// $menus = $this->get_menus();

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Branding', 'navmenu-addon-for-elementor' ),
			]
		);

		$this->add_control(
			'el_site_branding',
			[
				'label'       => __( 'Branding Type', 'navmenu-addon-for-elementor' ),
				'description' => __( 'Your theme must declare the "add_theme_support( \'custom-logo\')" for the logo to work', 'navmenu-addon-for-elementor' ),
				'type'        => Controls_Manager::SELECT, 'options' => [
					'title' => __( 'Title', 'navmenu-addon-for-elementor' ),
					'logo'  => __( 'Logo', 'navmenu-addon-for-elementor' ),
				],
				'default'     => 'title',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'        => __( 'Alignment', 'navmenu-addon-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => __( 'Left', 'navmenu-addon-for-elementor' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'navmenu-addon-for-elementor' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'navmenu-addon-for-elementor' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'prefix_class' => 'elementor%s-align-',
				'default'      => '',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Brand', 'navmenu-addon-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'branding_title_color',
			[
				'label'     => __( 'Title Color', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'el_site_branding' => 'title',
				],
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .elementor-branding .site-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'branding_title_hover',
			[
				'label'     => __( 'Hover', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'el_site_branding' => 'title',
				],
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-branding .site-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_padding',
			[
				'label'      => __( 'Title Padding - Default 1em', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'condition'  => [
					'el_site_branding' => 'title',
				],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-branding .site-title a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => __( 'Typography', 'navmenu-addon-for-elementor' ),
				'condition' => [
					'el_site_branding' => 'title',
				],
				'scheme'    => Scheme_Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} .elementor-branding .site-title',
			]
		);

		$this->add_control(
			'logo_padding',
			[
				'label'      => __( 'Title Padding - Default 1em', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'condition'  => [
					'el_site_branding' => 'logo',
				],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-branding .custom-logo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_desc_style',
			[
				'label'     => __( 'Description Options', 'navmenu-addon-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'el_site_branding' => 'title',
				],
			]
		);

		$this->add_control(
			'branding_description_color',
			[
				'label'     => __( 'Description Color', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'el_site_branding' => 'title',
				],
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-branding .site-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'desc_padding',
			[
				'label'      => __( 'Description Padding - Default 1em', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'condition'  => [
					'el_site_branding' => 'title',
				],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-branding .site-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'desc_typography',
				'label'     => __( 'Typography', 'navmenu-addon-for-elementor' ),
				'condition' => [
					'el_site_branding' => 'title',
				],
				'scheme'    => Scheme_Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} .elementor-branding .site-description',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_branding_borders',
			[
				'label' => __( 'Branding Border', 'navmenu-addon-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'border',
				'label'    => __( 'Border', 'navmenu-addon-for-elementor' ),
				'default'  => '1px',
				'selector' => '{{WRAPPER}} .elementor-branding',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-branding' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function branding_output() {
		$settings = $this->get_settings();

		if ( $settings['el_site_branding'] == 'title' ) {
			$this->render_title();
		} elseif ( $settings['el_site_branding'] == 'logo' ) {
			$this->render_logo();
		}
	}

	protected function elementor_the_site_logo() {
		if ( function_exists( 'the_custom_logo' ) ) {
			the_custom_logo();
		}
	}

	protected function render_title() {
	?>
		<span class="site-title">
		<?php
			$title = get_bloginfo( 'name' );
		?>
						
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( $title ); /* WPCS: xss ok. */ ?>" alt="<?php echo esc_attr( $title ); ?>">
				<?php bloginfo( 'name' ); ?>
			</a>		
		</span>
		<?php
			$description = get_bloginfo( 'description', 'display' );
		if ( $description || is_customize_preview() ) :
		?>
				<p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
		<?php
		endif;
	}

	protected function render_logo() {
		$this->elementor_the_site_logo();
	}

	protected function render() {

		$settings = $this->get_settings();
		?>
		
		<div id="elementor-branding" class="elementor-branding">
			<div class="header-title">
			<?php
				$this->branding_output();
			?>
			
						</div>
		</div>
		<?php
	}

	protected function _content_template() {}
}
