<?php
namespace ElementorMenus\Modules\Search;

use Elementor\Plugin;
use ElementorMenus\Base\Module_Base;

class Module extends Module_Base {

	public function __construct() {
		parent::__construct();

		// $this->add_actions();
	}

	public function get_name() {
		return 'elementor-search';
	}

	public function get_widgets() {
		return [
			'Elementor_Search',
		];
	}

}
