<?php
/******************************************************************
Plugin Name:       Tenfold Custom CSS & JS
Plugin URI:        https://tenfold.co.uk
Description:       Renders CSS and JS added to pages/posts using the 'wp_head' and 'wp_footer' hooks. Note that ACF must be active and the field group must be set up - this plugin does not do that. Also enqueues sitewide CSS and JS files if they exist in the current theme directory (as tf-custom.css and tf-custom.js).
Author:            Tim Rye
Author URI:        https://tenfold.co.uk/tim
Version:           1.0.7
GitHub Plugin URI: TenfoldMedia/tenfold-custom-css-js
GitHub Branch:     master
******************************************************************/

$priority = 1e6;


function tf_enqueue_sitewide_css() {
	if (get_template_directory() !== get_stylesheet_directory()) { // a child theme is being used; enqueue parent tf-custom.css too if it exists; before child
		if (file_exists(get_template_directory() . '/tf-custom.css')) {
			wp_enqueue_style('tf-sitewide-css-parent', get_template_directory_uri() . '/tf-custom.css', false, filemtime(get_template_directory() . '/tf-custom.css'));
		}
	}

	if (file_exists(get_stylesheet_directory() . '/tf-custom.css')) {
		wp_enqueue_style('tf-sitewide-css', get_stylesheet_directory_uri() . '/tf-custom.css', false, filemtime(get_stylesheet_directory() . '/tf-custom.css'));
	}
}
add_action('wp_print_styles', 'tf_enqueue_sitewide_css', $priority);
// theory behind using wp_print_styles action:
//   this is as late as we can reliably get it without it falling to the footer
//   this even gets it below Jetpack's 'super late priority' css
//   the only things it wont override are inline styles in the body

function tf_enqueue_sitewide_js() {
	if (get_template_directory() !== get_stylesheet_directory()) { // a child theme is being used; enqueue parent tf-custom.css too if it exists; before child
		if (file_exists(get_template_directory() . '/tf-custom.js')) {
			wp_enqueue_script('tf-sitewide-js-parent', get_template_directory_uri() . '/tf-custom.js', array('jquery'), filemtime(get_template_directory() . '/tf-custom.js'), true);
		}
	}

	if (file_exists(get_stylesheet_directory() . '/tf-custom.js')) {
		wp_enqueue_script('tf-sitewide-js', get_stylesheet_directory_uri() . '/tf-custom.js', array('jquery'), filemtime(get_stylesheet_directory() . '/tf-custom.js'), true);
	}
}
add_action('get_footer', 'tf_enqueue_sitewide_js');
// theory behind using get_footer action:
//   get_footer is the action that runs during get_footer()
//   get_footer() is the function that calls the footer.php template file
//   by the time we get to footer.php, all other plugins and stuff should have enqueued their scripts, even if they enqueue them during the body render
//   hence this action should enqueue our site-wide script after every other script, but before the page-specific JS below


function tf_render_custom_css() {
	if (!is_archive() /* otherwise get_field will get the CSS from the first post in the loop, which we don't want */ && function_exists('get_field') && function_exists('the_field') && get_field('custom_css')) { ?>
		<!--noptimize-->
		<?php the_field('custom_css'); ?>
		<!--/noptimize-->
	<?php }
}
add_action('wp_head', 'tf_render_custom_css', $priority);

function tf_render_custom_js() {
	if (!is_archive() /* otherwise get_field will get the CSS from the first post in the loop, which we don't want */ && function_exists('get_field') && function_exists('the_field') && get_field('custom_js')) { ?>
		<?php the_field('custom_js'); ?>
	<?php }
}
add_action('wp_footer', 'tf_render_custom_js', $priority);
