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
 * Elementor widget for Default Navmenu.
 *
 * @since 1.0.0
 */
class Default_Navmenu extends Widget_Base {

	protected $_has_template_content = false;

	public function get_name() {
		return 'default-navmenu';
	}

	public function get_title() {
		return __( 'Default Navmenu', 'navmenu-addon-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return [ 'branding-elements' ];
	}

	protected function _register_controls() {
		// $menus = $this->get_menus();

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Navigation', 'navmenu-addon-for-elementor' ),
			]
		);

		$this->add_control(
			'el_nav_menu',
			[
				'label'   => __( 'Select Menu', 'navmenu-addon-for-elementor' ),
				'type'    => Controls_Manager::SELECT, 'options' => navmenu_navbar_menu_choices(),
				'default' => '',
			]
		);

		$this->add_control(
			'el_menu_location',
			[
				'label'       => __( 'Menu Location', 'navmenu-addon-for-elementor' ),
				'description' => __( 'Select a location for your menu. This option facilitate the ability to create up to 2 mobile enabled menu locations', 'navmenu-addon-for-elementor' ),
				'type'        => Controls_Manager::SELECT, 'options' => [
					'primary'   => __( 'Primary', 'navmenu-addon-for-elementor' ),
					'secondary' => __( 'Secondary', 'navmenu-addon-for-elementor' ),
				],
				'default'     => 'primary',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'        => __( 'Navbar/Toggle Alignment', 'navmenu-addon-for-elementor' ),
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

		$this->add_responsive_control(
			'item_align',
			[
				'label'     => __( 'Mobile Item Alignment', 'navmenu-addon-for-elementor' ),
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
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-navigation ul li, .elementor-navigation ul ul li' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_padding',
			[
				'label'      => __( 'Text Padding - Default 1em', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-navigation a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'submenu_content',
			[
				'label' => __( 'Submenu', 'navmenu-addon-for-elementor' ),
			]
		);

		$this->add_responsive_control(
			'submenu_align',
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
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-navigation .sub-menu .menu-item a' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sub_padding',
			[
				'label'      => __( 'Item Padding', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-navigation .sub-menu .menu-item a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_menu_style',
			[
				'label' => __( 'Navbar', 'navmenu-addon-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'nav_bar_bg',
			[
				'label'     => __( 'Navbar Background', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#00215e',
				'selectors' => [
					'{{WRAPPER}} .elementor-menu' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_link_color',
			[
				'label'     => __( 'Link Color', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu .menu-item a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_link_bg',
			[
				'label'     => __( 'Background', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#00215e',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu .menu-item a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_link_hover_color',
			[
				'label'     => __( 'Link Color', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu .menu-item a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_hover_bg_color',
			[
				'label'     => __( 'Background Color', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu .menu-item a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'menu_border',
				'label'    => __( 'Border', 'navmenu-addon-for-elementor' ),
				'default'  => '1px',
				'selector' => '{{WRAPPER}} .elementor-nav-menu .menu-item a',
			]
		);

		$this->add_control(
			'menu_radius',
			[
				'label'      => __( 'Border Radius', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-nav-menu .menu-item a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'active_color',
			[
				'label' => __( 'Current/Active', 'navmenu-addon-for-elementor' ),
				'type'  => Controls_Manager::SECTION,
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'menu_link_active_color',
			[
				'label'     => __( 'Active Color', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu .current-menu-item > a, .elementor-nav-menu .current_page_item > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_active_bg_color',
			[
				'label'     => __( 'Active Background', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu .current-menu-item > a, .elementor-nav-menu .current_page_item > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'active_hover_color',
			[
				'label'     => __( 'Active Link', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu .current-menu-item > a:hover, .elementor-nav-menu .current_page_item > a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'active_hover_bg_color',
			[
				'label'     => __( 'Active Background', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu .current-menu-item > a:hover, .elementor-nav-menu .current_page_item > a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'active_border',
				'label'    => __( 'Border', 'navmenu-addon-for-elementor' ),
				'default'  => '1px',
				'selector' => '{{WRAPPER}} .elementor-nav-menu .current-menu-item > a, .elementor-nav-menu .current_page_item > a',
			]
		);

		$this->add_control(
			'active_radius',
			[
				'label'      => __( 'Border Radius', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-nav-menu .current-menu-item > a, .elementor-nav-menu .current_page_item > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'submenu_color',
			[
				'label' => __( 'Submenu', 'navmenu-addon-for-elementor' ),
				'type'  => Controls_Manager::SECTION,
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'submenu_link_color',
			[
				'label'     => __( 'Submenu Links', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu .sub-menu .menu-item a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'submenu_link_bg',
			[
				'label'     => __( 'Submenu Background', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#00215e',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu .sub-menu .menu-item a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'submenu_link_hover',
			[
				'label'     => __( 'Submenu Link Hover', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu .sub-menu .menu-item a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'submenu_hover_bg_color',
			[
				'label'     => __( 'Submenu Hover BG', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu .sub-menu .menu-item a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'submenu_border',
				'label'    => __( 'Border', 'navmenu-addon-for-elementor' ),
				'default'  => '1px',
				'selector' => '{{WRAPPER}} .elementor-nav-menu .sub-menu .menu-item a',
			]
		);

		$this->add_control(
			'submenu_radius',
			[
				'label'      => __( 'Border Radius', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-nav-menu .sub-menu .menu-item a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'menu_toggle',
			[
				'label' => __( 'Mobile Toggle', 'navmenu-addon-for-elementor' ),
				'type'  => Controls_Manager::SECTION,
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'toggle_icon_color',
			[
				'label'     => __( 'Icon Color', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .elementor-menu-toggle i.fa.fa-navicon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_bg_color',
			[
				'label'     => __( 'Background Color', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .elementor-menu-toggle' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_icon_hover',
			[
				'label'     => __( 'Icon Hover', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-menu-toggle:hover i.fa.fa-navicon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_bg_hover',
			[
				'label'     => __( 'Background Hover', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-menu-toggle:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_text_padding',
			[
				'label'      => __( 'Text Padding - Default 1em', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-menu-toggle i.fa.fa-navicon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'toggle_border',
				'label'    => __( 'Border', 'navmenu-addon-for-elementor' ),
				'default'  => '1px',
				'selector' => '{{WRAPPER}} .elementor-menu-toggle',
			]
		);

		$this->add_control(
			'toggle_border_radius',
			[
				'label'      => __( 'Border Radius', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-menu-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'menu_typography',
			[
				'label' => __( 'Typography', 'navmenu-addon-for-elementor' ),
				'type'  => Controls_Manager::SECTION,
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'menu_typography',
				'label'    => __( 'Typography', 'navmenu-addon-for-elementor' ),
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .elementor-nav-menu .menu-item',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		$settings      = $this->get_settings();
		$menu_location = $settings['el_menu_location'];
		// Get menu
		$nav_menu = ! empty( $settings['el_nav_menu'] ) ? wp_get_nav_menu_object( $settings['el_nav_menu'] ) : false;

		if ( ! $nav_menu ) {
			return;
		}

		$nav_menu_args = array(
			'fallback_cb'    => false,
			'container'      => false,
			'menu_id'        => 'elementor-navmenu',
			'menu_class'     => 'elementor-nav-menu',
			'theme_location' => 'default_navmenu', // creating a fake location for better functional control
			'menu'           => $nav_menu,
			'echo'           => true,
			'depth'          => 0,
			'walker'         => '',
		);

		echo '<div id="elementor-header-' . $menu_location . '" class="elementor-header">';
		?>
			<button id="elementor-menu-toggle" class="elementor-menu-toggle"><i class="fa fa-navicon"></i></button>
			<div id="elementor-menu" class="elementor-menu">
			
				<nav itemtype="http://schema.org/SiteNavigationElement" itemscope="itemscope" id="elementor-navigation" class="elementor-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Elementor Menu', 'navmenu-addon-for-elementor' ); ?>">				
				<?php
					wp_nav_menu(
						apply_filters(
							'widget_nav_menu_args',
							$nav_menu_args,
							$nav_menu,
							$settings
						)
					);
				?>
		
								</nav>
			</div>
		</div>
	<?php
	}

	protected function _content_template() {}
}
