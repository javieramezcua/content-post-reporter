<?php
/*
Plugin Name:  Content Post Reporter
Plugin URI:   https://mandalorianscode.com 
Description:  The goal of this plugin is to create a report with an overview of the shortcodes and custom content. 
Version:      1.0
Author:       Javier Amezcua 
Author URI:   https://mandalorianscode.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  content-posr-reporter
Domain Path:  /languages
*/

add_action('rest_api_init', function () {
  register_rest_route( 'content-post-reporter/', 'basic-report',array(
		'methods'  => 'GET',
		'callback' => 'content_post_reporter_basic'
  ));
});

function content_post_reporter_basic($request) {
	$arr_return = array();
	$args = array(
		'post_type' => array('page'),
		'post_status' => array( 'publish' ),
		'nopaging' => true,

	);

	$posts = get_posts($args);
	if (empty($posts)) {
		return new WP_Error( 'empty_category', 'There are no posts to display', array('status' => 404) );

	}
	$counter = 0;
	foreach($posts as $post){
		$arr_return[$counter]['ID'] = $post->ID;
		$arr_return[$counter]['title'] = $post->post_title;
		$arr_return[$counter]['content'] = $post->post_content;
		$arr_return[$counter]['test'] = shortcodes_in_the_content($post->post_content);
		$counter++;
	}
 
	$response = new WP_REST_Response($arr_return);
	$response->set_status(200);

	return $response;
}

function shortcodes_in_the_content($content){
	$pattern = '/\[(.*?)\]/i';
	$return = '';
	if (preg_match_all( $pattern, $content, $matches)) {
		
		foreach ($matches[1] as $key => $value) {
			$return.= '['.$value.'], ';
		}
		
	}

	return $return;
}

