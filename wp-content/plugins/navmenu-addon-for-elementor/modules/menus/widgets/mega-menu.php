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
 * Elementor Starter Widget
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Mega_Menu extends Widget_Base {

	protected $_has_template_content = false;

	public function get_name() {
		return 'mega-menu';
	}

	public function get_title() {
		return __( 'Mega Menu', 'navmenu-addon-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return [ 'branding-elements' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'navbar_content',
			[
				'label' => __( 'Navbar Content', 'navmenu-addon-for-elementor' ),
			]
		);

		$this->add_control(
			'el_mega_menu',
			[
				'label'   => __( 'Select Menu', 'navmenu-addon-for-elementor' ),
				'type'    => Controls_Manager::SELECT, 'options' => navmenu_navbar_menu_choices(),
				'default' => '',
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'navbar_typography',
				'label'    => __( 'Typography', 'navmenu-addon-for-elementor' ),
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .cbp-hsmenu',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'megamenu_content',
			[
				'label' => __( 'MegaMenu Content', 'navmenu-addon-for-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'megamenu_typography',
				'label'    => __( 'Typography', 'navmenu-addon-for-elementor' ),
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .cbp-hssubmenu',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'navbar_style',
			[
				'label' => __( 'NavBar', 'navmenu-addon-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'navbar_color',
			[
				'label'     => __( 'Color', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .cbp-hsmenu > li > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'navbar_bg_color',
			[
				'label'     => __( 'Background', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#00275e',
				'selectors' => [
					'{{WRAPPER}} .cbp-hsinner' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'navbar_padding',
			[
				'label'      => __( 'Padding', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .cbp-hsmenu > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'megamenu_style',
			[
				'label' => __( 'MegaMenu', 'navmenu-addon-for-elementor' ), 'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'megamenu_color',
			[
				'label'     => __( 'Color', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#a2a2a2',
				'selectors' => [
					'{{WRAPPER}} #cbp-hsmenu-wrapper ul.sub-menu > li a, .cbp-hssubmenu > li a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'megamenu_hover',
			[
				'label'     => __( 'hover', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#a2a2a2',
				'selectors' => [
					'{{WRAPPER}} #cbp-hsmenu-wrapper ul.sub-menu > li a:hover, #cbp-hsmenu-wrapper ul.sub-menu li a:focus, .no-touch .cbp-hssubmenu > li a:hover, .no-touch .cbp-hssubmenu > li a:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'megamenu_bg_color',
			[
				'label'     => __( 'Background', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#f7f7f7',
				'selectors' => [
					'{{WRAPPER}} .cbp-hsmenubg, .cbp-hsmenu > li.cbp-hsitem-open .cbp-hssubmenu' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings();
		// Get menu
		$mega_menu = ! empty( $settings['el_mega_menu'] ) ? wp_get_nav_menu_object( $settings['el_mega_menu'] ) : false;

		if ( ! $mega_menu ) {
			return;
		}

		$args = array(
			'fallback_cb'    => false,
			'container'      => false,
			'before'         => '',
			'after'          => '',
			'menu_id'        => 'mega-menu',
			'menu_class'     => 'cbp-hsmenu',
			'theme_location' => 'nav_mega_menu', // creating a fake location for better functional control
			'menu'           => $mega_menu,
			'echo'           => true,
			'depth'          => 0,
			'walker'         => '',
		);
		?>
		
		<nav itemtype="http://schema.org/SiteNavigationElement" itemscope="itemscope" class="cbp-hsmenu-wrapper" id="cbp-hsmenu-wrapper">
			<div class="cbp-hsinner">
				<?php
					wp_nav_menu(
						apply_filters(
							'widget_nav_menu_args',
							$args,
							$mega_menu,
							$settings
						)
					);
				?>
			</div>
		</nav>
	<?php
	}

	protected function _content_template() {}
}
