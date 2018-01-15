<?php
/**
 * Feed Excerpts For Divi
 *
 * @package     feed-excerpts-for-divi
 * @author      Jerry Simmons <jerry@ferventsolutions.com>
 * @copyright   2017 Jerry Simmons
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:  Feed Excerpts For Divi
 * Plugin URI:   https://ferventsolutions.com
 * Description:  Enable excerpts in the Blog Post Feed for posts that use the Divi Builder Text Module
 * Version:      1.1
 * Author:       Jerry Simmons <jerry@ferventsolutions.com>
 * Author URI:   https://ferventsolutions.com
 * Text Domain:  feed-excerpts-for-divi
 * License:      GPL-2.0+
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
 **/

if ( ! defined( 'ABSPATH' ) ) { exit; }


/**
 * Include Tool Page
 **/
require_once( plugin_dir_path( __FILE__ ) . '/includes/feed-excerpts-tool-page.php' );


/**
 * Enable excerpts in the Blog Post Feed for posts that use the Divi Builder Text Module
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
	if( false == get_option('rss_use_excerpt') ) { return $content; }

	/**
	 * Load Plugin Settings
	 **/
	if( false == get_option('wj_feed_excerpts_settings') ) { wj_feed_excerpts_activate(); }
	$wj_fe = get_option('wj_feed_excerpts_settings');

	$excerpt_length = $wj_fe['excerpt_length'];
	$more_string = $wj_fe['more_string'];

	$text = wj_feed_excerpts_get_text_module_text( $post->post_content );
	$excerpt = wp_trim_words( $text, $excerpt_length, $more_string );

	return $excerpt;

} // END wj_feed_excerpts()
add_filter('the_content', 'wj_feed_excerpts');


/**
 * Get Text From First Text Module In Post Content
 **/
function wj_feed_excerpts_get_text_module_text( $post_content ) {

	$text_module_start = strpos( $post_content, '[et_pb_text' );
	if( false === $text_module_start ) { return false; }

	$text_module_end = strpos( $post_content, ']', $text_module_start + 1 );
	$text_module_close = strpos( $post_content, '[/et_pb_text]', $text_module_end );
	$text = substr( $post_content, $text_module_end + 1, $text_module_close - $text_module_end - 1 );

	return $text;
}


/**
 * Plugin Activation - Set Defaults
 **/
function wj_feed_excerpts_activate() {

	$wj_fe = get_option('wj_feed_excerpts_settings');
	if( false === $wj_fe ) {
		$wj_feed_excerpts_settings = array();
		$wj_feed_excerpts_settings['excerpt_length'] = 55;
		$wj_feed_excerpts_settings['more_string'] = '...';
		update_option( 'wj_feed_excerpts_settings', $wj_feed_excerpts_settings );
	}
}
register_activation_hook( __FILE__, 'wj_feed_excerpts_activate' );


/**
 * Set Custom Excerpt Length
 **/
function wj_feed_excerpts_custom_length( $length ) {
	if( false == get_option('wj_feed_excerpts_settings') ) { wj_feed_excerpts_activate(); }
	$wj_fe = get_option('wj_feed_excerpts_settings');

	return intval( $wj_fe['excerpt_length'] );
}
add_filter( 'excerpt_length', 'wj_feed_excerpts_custom_length', 999 );