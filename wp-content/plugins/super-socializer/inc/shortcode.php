<?php
defined('ABSPATH') or die("Cheating........Uh!!");
/** 
 * Shortcode for Social Sharing.
 */ 
function the_champ_sharing_shortcode($params){
	// notify if sharing is disabled
	if(the_champ_social_sharing_enabled()){
		global $theChampSharingOptions;
		extract(shortcode_atts(array(
			'style' => '',
			'type' => 'horizontal',
			'left' => '0',
			'right' => '0',
			'top' => '100',
			'url' => '',
			'count' => 0,
			'align' => 'left',
			'title' => '',
			'total_shares' => 'OFF'
		), $params));
		if(($type == 'horizontal' && !the_champ_horizontal_sharing_enabled()) || ($type == 'vertical' && !the_champ_vertical_sharing_enabled())){
			return;
		}
		global $post;
		$customUrl = apply_filters('heateor_ss_custom_share_url', '', $post);
		if($customUrl){
			$targetUrl = $customUrl;
			$postId = 0;
		}elseif($url){
			$targetUrl = $url;
			$postId = 0;
		}elseif(is_front_page()){
			$targetUrl = esc_url(home_url());
			$postId = 0;
		}elseif(!is_singular() && $type == 'vertical'){
			$targetUrl = html_entity_decode(esc_url(the_champ_get_http().$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]));
			$postId = 0;
		}elseif(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']){
			$targetUrl = html_entity_decode(esc_url(the_champ_get_http().$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]));
			$postId = $post -> ID;
		}elseif(get_permalink($post -> ID)){
			$targetUrl = get_permalink($post -> ID);
			$postId = $post -> ID;
		}else{
			$targetUrl = html_entity_decode(esc_url(the_champ_get_http().$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]));
			$postId = 0;
		}

		$targetUrl = heateor_ss_apply_target_share_url_filter($targetUrl, $type, false);
		// if bit.ly url shortener enabled, generate bit.ly short url
		$shortUrl = '';
		if(isset($theChampSharingOptions['use_shortlinks']) && function_exists('wp_get_shortlink')){
			$shortUrl = wp_get_shortlink();
			// if bit.ly integration enabled, generate bit.ly short url
		}elseif(isset($theChampSharingOptions['bitly_enable']) && isset($theChampSharingOptions['bitly_username']) && $theChampSharingOptions['bitly_username'] != '' && isset($theChampSharingOptions['bitly_key']) && $theChampSharingOptions['bitly_key'] != ''){
			$shortUrl = the_champ_generate_sharing_bitly_url($targetUrl, $postId);
		}
		$alignmentOffset = 0;
		if($left){
			$alignmentOffset = $left;
		}elseif($right){
			$alignmentOffset = $right;
		}
		$shareCountTransientId = heateor_ss_get_share_count_transient_id($targetUrl);
		$cachedShareCount = heateor_ss_get_cached_share_count($shareCountTransientId);
		$html = '<div class="the_champ_sharing_container the_champ_'.$type.'_sharing' . ( $type == 'vertical' && isset( $theChampSharingOptions['hide_mobile_sharing'] ) ? ' the_champ_hide_sharing' : '' ) . ( $type == 'vertical' && isset( $theChampSharingOptions['bottom_mobile_sharing'] ) ? ' the_champ_bottom_sharing' : '' ) . '" ss-offset="' . $alignmentOffset . '" super-socializer-data-href="'.$targetUrl.'" ' . ( $cachedShareCount === false ? "" : 'super-socializer-no-counts="1" ' );
		$verticalOffsets = '';
		if($type == 'vertical'){
			$verticalOffsets = $align . ': '.$$align.'px; top: '.$top.'px;width:' . ((isset($theChampSharingOptions['vertical_sharing_size']) ? $theChampSharingOptions['vertical_sharing_size'] : '35') + 4) . "px;";
		}
		// style 
		if($style != "" || $verticalOffsets != ''){
			$html .= 'style="';
			if(strpos($style, 'background') === false){ $html .= '-webkit-box-shadow:none;box-shadow:none;'; }
			$html .= $verticalOffsets;
			$html .= $style;
			$html .= '"';
		}
		$html .= '>';
		if( $type == 'horizontal' && $title != '' ) {
			$html .= '<div style="font-weight:bold" class="the_champ_sharing_title">' . ucfirst( $title ) . '</div>';
		}
		$html .= the_champ_prepare_sharing_html($shortUrl == '' ? $targetUrl : $shortUrl, $type, $count, $total_shares == 'ON' ? 1 : 0, $shareCountTransientId);
		$html .= '</div>';
		if(($count || $total_shares == 'ON') && $cachedShareCount === false){
			$html .= '<script>theChampLoadEvent(function(){theChampCallAjax(function(){theChampGetSharingCounts();});});</script>';
		}
		return $html;
	}
}
add_shortcode('TheChamp-Sharing', 'the_champ_sharing_shortcode');

/** 
 * Shortcode for Social Counter.
 */ 
function the_champ_counter_shortcode($params){
	// notify if counter is disabled
	if(the_champ_social_counter_enabled()){
		extract(shortcode_atts(array(
			'style' => '',
			'type' => 'horizontal',
			'left' => '0',
			'right' => '0',
			'top' => '100',
			'url' => '',
			'align' => 'left',
			'title' => ''
		), $params));
		if(($type == 'horizontal' && !the_champ_horizontal_counter_enabled()) || ($type == 'vertical' && !the_champ_vertical_counter_enabled())){
			return;
		}
		global $post;
		$customUrl = apply_filters('heateor_ss_custom_share_url', '', $post);
		if($customUrl){
			$targetUrl = $customUrl;
			$postId = 0;
		}elseif($url){
			$targetUrl = $url;
			$postId = 0;
		}elseif(is_front_page()){
			$targetUrl = esc_url(home_url());
			$postId = 0;
		}elseif(!is_singular() && $type == 'vertical'){
			$targetUrl = html_entity_decode(esc_url(the_champ_get_http().$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]));
			$postId = 0;
		}elseif(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']){
			$targetUrl = html_entity_decode(esc_url(the_champ_get_http().$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]));
			$postId = $post -> ID;
		}elseif(get_permalink($post -> ID)){
			$targetUrl = get_permalink($post -> ID);
			$postId = $post -> ID;
		}else{
			$targetUrl = html_entity_decode(esc_url(the_champ_get_http().$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]));
			$postId = 0;
		}
		$targetUrl = heateor_ss_apply_target_like_button_url_filter($targetUrl, $type, false);
		$alignmentOffset = 0;
		if($left){
			$alignmentOffset = $left;
		}elseif($right){
			$alignmentOffset = $right;
		}
		global $theChampCounterOptions;
		$html = '<div class="the_champ_counter_container the_champ_'.$type.'_counter' . ( $type == 'vertical' && isset( $theChampCounterOptions['hide_mobile_likeb'] ) ? ' the_champ_hide_sharing' : '' ) . '" ss-offset="' . $alignmentOffset . '" ';
		$verticalOffsets = '';
		if($type == 'vertical'){
			$verticalOffsets = $align . ': '.$$align.'px; top: '.$top.'px;';
		}
		// style 
		if($style != "" || $verticalOffsets != ''){
			$html .= 'style="';
			if(strpos($style, 'background') === false){ $html .= '-webkit-box-shadow:none;box-shadow:none;'; }
			$html .= $verticalOffsets;
			$html .= $style;
			$html .= '"';
		}
		$html .= '>';
		if( $type == 'horizontal' && $title != '' ) {
			$html .= '<div style="font-weight:bold" class="the_champ_counter_title">' . ucfirst( $title ) . '</div>';
		}
		$counterUrl = $targetUrl;
		if(isset($theChampCounterOptions['use_shortlinks']) && function_exists('wp_get_shortlink')){
			$counterUrl = wp_get_shortlink();
			// if bit.ly integration enabled, generate bit.ly short url
		}elseif(isset($theChampCounterOptions['bitly_enable']) && isset($theChampCounterOptions['bitly_username']) && isset($theChampCounterOptions['bitly_username']) && $theChampCounterOptions['bitly_username'] != '' && isset($theChampCounterOptions['bitly_key']) && $theChampCounterOptions['bitly_key'] != ''){
			$shortUrl = the_champ_generate_counter_bitly_url($targetUrl, $postId);
			if($shortUrl){
				$counterUrl = $shortUrl;
			}
		}
		$html .= the_champ_prepare_counter_html($targetUrl, $type, $counterUrl);
		$html .= '</div>';
		return $html;
	}
}
add_shortcode('TheChamp-Counter', 'the_champ_counter_shortcode');

/** 
 * Shortcode for Social Login.
 */ 
function the_champ_login_shortcode($params){
	if(the_champ_social_login_enabled()){
		extract(shortcode_atts(array(
			'style' => '',
			'title' => '',
			'show_username' => 'OFF'
		), $params));
		if($show_username == 'ON' && is_user_logged_in()){
			global $user_ID;
			$userInfo = get_userdata($user_ID);
			$html = "<div style='height:80px;width:180px'><div style='width:63px;float:left;'>";
			$html .= @get_avatar($user_ID, 60, $default, $alt);
			$html .= "</div><div style='float:left; margin-left:10px'>";
			$html .= str_replace('-', ' ', $userInfo -> user_login);
			//do_action('the_champ_login_widget_hook', $userInfo -> user_login);
			$html .= '<br/><a href="' . wp_logout_url(esc_url(home_url())) . '">' .__('Log Out', 'Super-Socializer') . '</a></div></div>';
		}else{
			$html = '<div ';
			// style 
			if($style != ""){
				if(strpos($style, 'float') === false){
					$style = 'float: left;' . $style;
				}
				$html .= 'style="'.$style.'"';
			}
			$html .= '>';
			if( !is_user_logged_in() && $title != '' ) {
				$html .= '<div style="font-weight:bold" class="the_champ_social_login_title">' . ucfirst( $title ) . '</div>';
			}
			$html .= the_champ_login_button(true);
			$html .= '</div><div style="clear:both"></div>';
		}
		return $html;
	}
}
add_shortcode('TheChamp-Login', 'the_champ_login_shortcode');

/** 
 * Shortcode for Facebook Comments.
 */ 
function the_champ_fb_commenting_shortcode($params){
	extract(shortcode_atts(array(
		'style' => '',
		'url' => get_permalink(),
		'num_posts' => '',
		'width' => '',
		'language' => 'en_US',
		'title' => ''
	), $params));
	$html = '<div style="'. $style .'" id="the_champ_fb_commenting">';
	if( $title != '' ) {
		$html .= '<div style="font-weight:bold">' . ucfirst( $title ) . '</div>';
	}
	$html .= '<div class="fb-comments" data-href="' .($url == '' ? html_entity_decode(esc_url(the_champ_get_http().$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"])) : $url). '"';
    $html .= ' data-numposts="' . $num_posts . '"';
    $html .= ' data-width="' . ($width == '' ? '100%' : $width) . '"';
    $html .= ' ></div></div><script type="text/javascript" src="//connect.facebook.net/' . $language . '/sdk.js
    "></script><script>FB.init({xfbml:1,version: "v2.8"});</script>';
	return $html;
}
add_shortcode('TheChamp-FB-Comments', 'the_champ_fb_commenting_shortcode');

/** 
 * Shortcode for GooglePlus Comments.
 */ 
function the_champ_gp_commenting_shortcode($params){
	extract(shortcode_atts(array(
		'style' => '',
		'url' => get_permalink(),
		'width' => '',
		'title' => ''
	), $params));
	$html = '<div style="'. $style .'" id="the_champ_gp_commenting">';
	if( $title != '' ) {
		$html .= '<div style="font-weight:bold">' . ucfirst( $title ) . '</div>';
	}
    $html .= "<div class='g-comments' data-href='" . ($url == '' ? html_entity_decode(esc_url(the_champ_get_http().$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"])) : $url) . "' ". ($width ? "data-width='" .$width. "'" : "" ) ." data-first_party_property='BLOGGER' data-view_type='FILTERED_POSTMOD' ></div>";
    $html .= '</div><script type="text/javascript" src="//apis.google.com/js/plusone.js"></script>';
	return $html;
}
add_shortcode('TheChamp-GP-Comments', 'the_champ_gp_commenting_shortcode');

/** 
 * Shortcode for Social account linking
 */ 
function the_champ_social_linking_shortcode($params){
	if(the_champ_social_login_enabled()){
		extract(shortcode_atts(array(
			'style' => '',
			'title' => ''
		), $params));
		$html = '<div style="'. $style .'">';
		if( $title != '' ) {
			$html .= '<div style="font-weight:bold">' . ucfirst( $title ) . '</div>';
		}
		$html .= the_champ_account_linking();
		$html .= '</div>';
		return $html;
	}
	return '<h3>' . __('Enable Social Login from "Basic Configuration" section at "Super Socializer > Social Login" page in admin panel', 'Super-Socializer') . '</h3>';
}
add_shortcode('TheChamp-Social-Linking', 'the_champ_social_linking_shortcode');