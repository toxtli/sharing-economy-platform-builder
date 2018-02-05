<?php
class Paginator {

    var $items_per_page;
    var $mid_range;
    var $current_page;
    var $target_page;
    var $items;
    var $pages;
    var $styles;
    var $prev_page_html;
    var $next_page_html;
    var $page_list_html;
    var $fixed_url;
    var $page_count;
    var $starting_page;
    var $ending_page;


    /** constructor */
    function Paginator() {

    }


    /**
     * @param array $items
     * @param string $link_fix
     * @param int $items_per_page
     * @param int $mid_range
     * @param array $stylings
     * @uses to generate the whole paginated data set
     */
    function paginate($items = array(), $link_fix='', $items_per_page = 5, $mid_range = 3, $stylings=array()) {

        /**
         * default styles for the page links
         */
        if (count($stylings) == 0) { // no specification suppplied
            $stylings = array();
            $stylings['normal'] = 'btn btn-xs';      // for all
            $stylings['active'] = 'btn-primary';     // for active
            $stylings['inactive'] = 'btn-inverse';     // for inactive
            $stylings['disabled'] = 'disabled="disabled"';
            $stylings['previous_text'] = 'Previous';
            $stylings['next_text'] = 'Next';
        }

        $this->styles = $stylings;
        $this->fixed_url = $link_fix; // default is blank

        $this->items_per_page = $items_per_page;
        $this->mid_range = $mid_range;
        $this->list_items($items);
        $this->page_links(count($items));
        $this->generate_page_links_html();
    }

    /**
     * @param $items
     * @uses generate the array of items to be displayed
     * @uses to list up all the items that should be on the current page
     */
    function list_items($items) {
        $all_items_count = count($items);
        $current_page = (int)request('from_page');    // passed through $_GET
        $wanted_page = (int)request('to_page');
        $page_count = ceil($all_items_count/$this->items_per_page);
        $this->page_count = $page_count;

        if (!is_numeric($wanted_page) || $wanted_page>$page_count || $wanted_page<1) // errorenous input
            $wanted_page = 1;

        if ( ($page_count < 2) || ($wanted_page == 'all') ) {  // all items fit into one page
            $result_items = $items;
        } else {
            $starting_item = ($wanted_page-1)*$this->items_per_page;
            $result_items = array_slice($items, $starting_item, $this->items_per_page);
        }
        $this->items = $result_items;
        $this->current_page = $wanted_page;
    }

    /**
     * @param int $all_items_count
     * @uses generates the list of pages to be added to the page list
     * @uses create a list (array) of all the pages that should be in the
     *      page links list
     */
    function page_links($all_items_count = 0) { // create page links
        $page_count = ceil($all_items_count/$this->items_per_page);

        if ($page_count == 0) $page_count = 1;

        $current_page = $this->current_page;

        if ($page_count <= 10) {
            $starting_page = 1;
            $ending_page = $page_count;
        } else {
            $starting_page = max( $current_page - ($this->mid_range), 1 );
            $ending_page = min( $current_page + ($this->mid_range), $page_count );
        }

        $this->starting_page = $starting_page;
        $this->ending_page = $ending_page;

        $page_list = range($starting_page, $ending_page);
        $this->pages = $page_list;
    }

    /**
     * @uses generate all the htmls for different parts of the view
     *
     */
    function generate_page_links_html() {
        $page_list = $this->pages;

        $page_list_html = '';
        $current_page = $this->current_page;

        $next_page = ( $current_page >= $this->page_count ? '#' : $current_page+1 );
        $prev_page = ( $current_page <= 1 ? '#' : $current_page-1 );

        $starting_page = $this->starting_page;
        $ending_page = $this->ending_page;

        // html for the starting (head) of list
        $begin_list_html = '';
        if ($starting_page > 1) $begin_list_html = ('<a '.'href="'.$this->fixed_url.'&from_page='.$current_page."&to_page=".'1'.'"'.' class="'.$this->styles['normal'].' '.($this->styles['active'].'"').'>'.'1'.'</a>'."\n");
        if ($starting_page > 2) $begin_list_html .= ('<a '.'href="'.$this->fixed_url.'&from_page='.$current_page."&to_page=".'2'.'"'.' class="'.$this->styles['normal'].' '.($this->styles['active'].'"').'>'.'2'.'</a>'."\n");
        if ($begin_list_html != '') $begin_list_html .= '  . . .  ';

        // html for the ending (tail) of list
        $end_list_html = '';
        if ($ending_page < $this->page_count-1) $end_list_html = ('<a '.'href="'.$this->fixed_url.'&from_page='.$current_page."&to_page=".($this->page_count-1).'"'.' class="'.$this->styles['normal'].' '.($this->styles['active'].'"').'>'.($this->page_count-1).'</a>'."\n");
        if ($ending_page < $this->page_count) $end_list_html .= ('<a '.'href="'.$this->fixed_url.'&from_page='.$current_page."&to_page=".($this->page_count).'"'.' class="'.$this->styles['normal'].' '.($this->styles['active'].'"').'>'.($this->page_count).'</a>'."\n");
        if ($end_list_html != '') $end_list_html = '  . . .  '.$end_list_html;


        // html for navigating to the previous page
        $this->prev_page_html = '<a class="'.$this->styles['normal'].' '.( $current_page == 1 ? $this->styles['inactive'].'" disabled="disabled"' : $this->styles['active'].'"' ).'href="'.$this->fixed_url.'&from_page='.$current_page."&to_page=".$prev_page.'">'.$this->styles['previous_text'].'</a>';
        foreach ($page_list as $page) {
            // html for all the pages in the middle of the list
            $page_list_html .= ('<a '.'href="'.$this->fixed_url.'&from_page='.$current_page."&to_page=".$page.'"'.' class="'.$this->styles['normal'].' '.($page == $current_page ? $this->styles['inactive'].'" disabled="disabled"' : $this->styles['active'].'"').'>'.$page.'</a>'."\n");
        }

        $this->page_list_html = ''; // main page links parts init
        $this->page_list_html = ($begin_list_html); // the first 2 pages of the whole set
        $this->page_list_html .= $page_list_html; // all the pages in the middle of the list
        $this->page_list_html .= ($end_list_html); // the last 2 pages of the list

        // html for navigating to the next page
        $this->next_page_html = '<a class="'.$this->styles['normal'].' '.( $current_page >= $this->page_count ? $this->styles['inactive'].'" disabled="disabled"' : $this->styles['active'].'"' ).'href="'.$this->fixed_url.'&from_page='.$current_page."&to_page=".$next_page.'">'.$this->styles['next_text'].'</a>';
    }
};
?>