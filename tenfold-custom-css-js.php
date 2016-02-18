<?php
/******************************************************************
Plugin Name:       Tenfold Custom CSS & JS
Plugin URI:        http://tenfold.media
Description:       Renders CSS and JS added to pages/posts using the 'wp_head' and 'wp_footer' hooks. Note that ACF must be active and the field group must be set up - this plugin does not do that. Also enqueues sitewide CSS and JS files if they exist in the current theme directory (as tf-custom.css and tf-custom.js).
Author:            Tim Rye
Author URI:        http://tenfold.media/tim
Version:           1.0.0
GitHub Plugin URI: TenfoldMedia/tenfold-custom-css-js
GitHub Branch:     master
******************************************************************/

$priority = 1e6;


function tf_render_sitewide_css() {
	if (file_exists(get_stylesheet_directory() . '/tf-custom.css')) {
		wp_enqueue_style('tf-sitewide-css', get_stylesheet_directory_uri() . '/tf-custom.css');
	}
}
add_action('wp_head', 'tf_render_sitewide_css', $priority - 1);

function tf_render_sitewide_js() {
	if (file_exists(get_stylesheet_directory() . '/tf-custom.js')) {
		wp_enqueue_script('tf-sitewide-js', get_stylesheet_directory_uri() . '/tf-custom.js', array('jquery'), '', true);
	}
}
add_action('wp_footer', 'tf_render_sitewide_js', $priority - 1);


function tf_render_custom_css() {
	if (!is_archive() /* otherwise get_field will get the CSS from the first post in the loop, which we don't want */ && get_field('custom_css')) {
		the_field('custom_css');
	}
}
add_action('wp_head', 'tf_render_custom_css', $priority);

function tf_render_custom_js() {
	if (!is_archive() /* otherwise get_field will get the CSS from the first post in the loop, which we don't want */ && get_field('custom_js')) {
		the_field('custom_js');
	}
}
add_action('wp_footer', 'tf_render_custom_js', $priority);
