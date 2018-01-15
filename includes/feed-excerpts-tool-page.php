<?php
/**
 * Feed Excerpts For Divi
 *
 * @package     feed-excerpts-for-divi
 * @author      Jerry Simmons <jerry@ferventsolutions.com>
 * @copyright   2017 Jerry Simmons
 * @license     GPL-2.0+
 *
 **/

if ( ! defined( 'ABSPATH' ) ) { exit; }


/**
 * Register Tool Page
 **/
function wj_feed_excerpts_register_toolpage() {
	$hook = add_management_page(
		'Feed Excerpts For Divi Settings',			// Page Title
		'Feed Excerpts For Divi',					// Menu Title
		'manage_options',							// Capability
		'wj_feed_excerpts_toolpage',				// Menu Slug
		'wj_feed_excerpts_display_toolpage',		// Callback Function
		''
	);
}
add_action( 'admin_menu', 'wj_feed_excerpts_register_toolpage' );


/**
 * Display Tool Page
 **/
function wj_feed_excerpts_display_toolpage() {

	echo '<h1>Feed Excerpts For Divi</h1>';

	if( isset( $_POST['wj_feed_excerpts_settings_form'] ) ) {
		wj_feed_excerpts_save_toolpage( $_POST );
	}

	/**
	 * Load Plugin Settings
	 **/
	if( false == get_option('wj_feed_excerpts_settings') ) { wj_feed_excerpts_activate(); }
	$wj_fe = get_option('wj_feed_excerpts_settings');

	/**
	 * Get Posts For Sample Excerpts
	 **/
	$args = array(
		'post_type'			=> 'post',
		'posts_per_page'	=> 20,
	);
	$wj_fe_sample_query = new WP_Query( $args );
	$posts = $wj_fe_sample_query->posts;
	$sample_excerpts = array();

	foreach( $posts as $post ) {
		if( 'on' == get_post_meta( $post->ID, '_et_pb_use_builder', true ) ) {

			$text = wj_feed_excerpts_get_text_module_text( $post->post_content );
			if( false === $text ) { continue; }

			$excerpt_length = $wj_fe['excerpt_length'];
			$more_string = $wj_fe['more_string'];

			$excerpt = wp_trim_words( $text, $excerpt_length, $more_string );
			$sample_excerpts[] = array(
				'ID'		=> $post->ID,
				'title'		=> $post->post_title,
				'excerpt'	=> $excerpt,
			);

		} // END if _et_pb_use_builder
	} // END foreach $posts


	/**
	 * Display Options Form
	 **/
	echo '<div class="wj_feed_excerpts_getsettings_form">';
		echo '<form action="" method="POST">';
			echo '<input type="hidden" value="true" name="wj_feed_excerpts_settings_form" id="wj_feed_excerpts_settings_form" />';
			echo '<table class="form-table">';

				// Excerpt Length Row
				echo '<tr>';
					echo '<th style="text-align: right"><label for="wj_feed_excerpts_length">Excerpt Length</label></th>';
					echo '<td>';

						echo '<input name="wj_feed_excerpts_length" value="'
							. $wj_fe['excerpt_length'] . '"> words<br>';

					echo '</td>';
				echo '</tr>';

				// More String Row
				echo '<tr>';
					echo '<th style="text-align: right"><label for="wj_feed_excerpts_length">More String</label></th>';
					echo '<td>';

						echo '<input name="wj_feed_excerpts_more_string" value="'
							. $wj_fe['more_string'] . '"> <br>';

					echo '</td>';
				echo '</tr>';

				// Sample Excerpts Row
				echo '<tr>';
					echo '<th style="text-align: right"><label for="wj_feed_excerpts_length">Sample Excerpts<br>'
						. '(' . $wj_fe['excerpt_length'] . ' words)</label></th>';
					echo '<td>';

						if( empty( $sample_excerpts ) ) {
							echo '<h4>Could not find any posts using the Divi Builder</h4>';
						} else {
							foreach( $sample_excerpts as $sample_excerpt ) {
								echo '<p><strong> Post ' . $sample_excerpt['ID'] . ': </strong>' . $sample_excerpt['title'] . '<br>';
								echo '<strong>Excerpt: </strong>' . $sample_excerpt['excerpt'] . '</p><br>';
							}
						}

					echo '</td>';
				echo '</tr>';

			echo '</table>';
			submit_button('Save Settings');
		echo '</form>';
	echo '</div>';

} // END wj_feed_excerpts_display_toolpage()


/**
 * Save Tool Page
 **/
function wj_feed_excerpts_save_toolpage( $post_data ) {
	$wj_fe = get_option('wj_feed_excerpts_settings');

	$wj_fe['excerpt_length'] = $post_data['wj_feed_excerpts_length'];
	$wj_fe['more_string'] = $post_data['wj_feed_excerpts_more_string'];

	update_option( 'wj_feed_excerpts_settings', $wj_fe );
} // END wj_feed_excerpts_save_toolpage()
