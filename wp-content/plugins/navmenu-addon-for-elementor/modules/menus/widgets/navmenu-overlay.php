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
 * Elementor Overlay NavMenu
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Navmenu_Overlay extends Widget_Base {

	protected $_has_template_content = false;

	public function get_name() {
		return 'navmenu-overlay';
	}

	public function get_title() {
		return __( 'Overlay NavMenu', 'navmenu-addon-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return [ 'branding-elements' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'overlay_content',
			[
				'label' => __( 'Menu', 'navmenu-addon-for-elementor' ),
			]
		);

		$this->add_control(
			'el_overlay_menu',
			[
				'label'   => __( 'Select Menu', 'navmenu-addon-for-elementor' ),
				'type'    => Controls_Manager::SELECT, 'options' => navmenu_navbar_menu_choices(),
				'default' => '',
			]
		);

		$this->add_responsive_control(
			'item_align',
			[
				'label'     => __( 'Item Alignment', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
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
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .overlay-navigation ul li, .overlay-navigation ul ul li' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'search_typography',
				'label'    => __( 'Typography', 'navmenu-addon-for-elementor' ),
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .overlay-navigation a',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Overlay Style', 'navmenu-addon-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_color',
			[
				'label'     => __( 'Color', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .overlay-navigation a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_hover',
			[
				'label'     => __( 'Hover', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .overlay-navigation a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'overlay_bg_color',
			[
				'label'     => __( 'Overlay Background', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .overlay-navigation' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_padding',
			[
				'label'      => __( 'Padding', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .overlay-navigation ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'toggle_style',
			[
				'label' => __( 'Toggle Style', 'navmenu-addon-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'toggle_color',
			[
				'label'     => __( 'Color', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} #touch-menu span,#touch-menu span:after,#touch-menu span:before,#touch-menu.on span:before,#touch-menu.on span:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_bg_color',
			[
				'label'     => __( 'Background', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} #touch-menu' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #touch-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings();
		// Get menu
		$overlay_menu = ! empty( $settings['el_overlay_menu'] ) ? wp_get_nav_menu_object( $settings['el_overlay_menu'] ) : false;

		if ( ! $overlay_menu ) {
			return;
		}

		$overlay_menu_args = array(
			'fallback_cb'    => false,
			'container'      => false,
			'menu_id'        => '',
			'menu_class'     => '',
			'theme_location' => 'nav_overlay_menu', // creating a fake location for better functional control
			'menu'           => $overlay_menu,
			'echo'           => true,
			'depth'          => 1,
			'walker'         => '',
		);
	?>
	<!--<div class="navmenu-overlay">-->
		<span id="touch-menu" class="mobile-menu"><span></span></span>
		<nav itemtype="http://schema.org/SiteNavigationElement" itemscope="itemscope" class="overlay-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Elementor Menu', 'navmenu-addon-for-elementor' ); ?>">
			<?php
				wp_nav_menu(
					apply_filters(
						'widget_nav_menu_args',
						$overlay_menu_args,
						$overlay_menu,
						$settings
					)
				);
			?>
		</nav>
	<!--</div>-->
	<?php
	}

	protected function _content_template() {}
}
