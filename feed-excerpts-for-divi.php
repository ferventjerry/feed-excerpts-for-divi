<?php
/**
 * Feed Excerpts For Divi
 *
 * @package		feed-excerpts-for-divi
 * @author		Jerry Simmons <jerry@ferventsolutions.com>
 * @copyright	2017 Jerry Simmons
 * @license		GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:	Feed Excerpts For Divi
 * Plugin URI:	https://ferventsolutions.com
 * Description:	Create Blog Post Excerpts From The First Text Module In A Post For RSS Feed
 * Version:		1.0
 * Author:		Jerry Simmons <jerry@ferventsolutions.com>
 * Author URI:	https://ferventsolutions.com
 * Text Domain:	feed-excerpts-for-divi
 * License:		GPL-2.0+
 * License URI:	http://www.gnu.org/licenses/gpl-2.0.txt
 **/

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Create Blog Post Excerpts From The First Text Module In A Post For RSS Feed
 *
 * @uses the_content filter
 **/
function wj_feed_excerpts( $content ) {

	global $post;

	/**
	 * Bail If Not A Feed, If Excerpt Exists, Or If No Text Modules Are Foun
	 **/
	if ( !is_feed() ) { return $content; }
	if( !empty( $post->post_excerpt ) ) { return $content; }
	if( false === strpos( $post->post_content, '[et_pb_text' ) ) { return $content; }

	/**
	 * Get Text From First Text Module In Post Content
	 **/
	$post_content = $post->post_content;
	$text_module_start = strpos( $post_content, '[et_pb_text' );
	$text_module_end = strpos( $post_content, ']', $text_module_start + 1 );
	$text_module_close = strpos( $post_content, '[/et_pb_text]', $text_module_end );
	$text = substr( $post_content, $text_module_end + 1, $text_module_close - $text_module_end - 1 );
	$excerpt = wp_trim_words( $text, 55, '...' );

	return $excerpt;

} // END wj_rss_excerpt()
add_filter('the_content', 'wj_feed_excerpts');


