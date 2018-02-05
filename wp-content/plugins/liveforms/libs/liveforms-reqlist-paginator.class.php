<?php
class Liveforms_Reqlist_Paginator {
	function __construct() {
	}

	function page_link($args) {
		if (isset($args['current'])) {
			$btn_class = 'btn-disabled';
		} else {
			$btn_class = 'btn-primary';
		}
		return "<a href='{$args['link']}&target_page={$args['target']}&per_page={$args['interval']}' class='btn {$btn_class}'>{$args['title']}</a>";
		
	}

	function wrapper_begin() {
		return "<div class='row'><div class='col-md-12 text-center'>";
	}

	function wrapper_end() {
		return "</div></div>";
	}

	function first_page() {
			
	}

	function last_page() {
	
	}

	public function paginate($count, $target_page, $interval, $prefix_link) {
		$last_page = $count / $interval;
		if ($target_page != 0) {
			$prev_page = $target_page - 1;
		}
		if ($target_page != $last_page) {
			$next_page = $target_page + 1;
		}

		$html = $this->page_link(
					array(
						'link' => $prefix_link,
						'title' => $target_page,
						'interval' => $interval,
						'target' => $target_page,
						'current' => true
					)
				);
		if (isset($prev_page)) {
			$html = $this->page_link(
				array(
					'link' => $prefix_link,
					'title' => $prev_page,
					'interval' => $interval,
					'target' => $prev_page
				)
				) . " {$html}";
		}
		if (isset($next_page)) {
			$html .= " {$this->page_link(
				array(
					'link' => $prefix_link,
					'title' => $next_page,
					'interval' => $interval,
					'target' => $next_page
				)
			)}";
		}
		return $this->wrapper_begin() . $html . $this->wrapper_end();
	}
}