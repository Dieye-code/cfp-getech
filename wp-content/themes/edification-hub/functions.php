<?php
/**
 * Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Edification_Hub
 */

if ( ! function_exists( 'edification_hub_setup' ) ) :
	/**
	 * Loads the child theme textdomain and update notifier.
	 */
	function edification_hub_setup() {
	    load_child_theme_textdomain( 'edification-hub', get_stylesheet_directory() . '/languages' );
	}
endif;
add_action( 'after_setup_theme', 'edification_hub_setup', 11 );

if ( ! function_exists( 'edification_hub_scripts' ) ) :
	/**
	 * Enqueue scripts and styles.
	 */
	function edification_hub_scripts() {
		$theme_version = wp_get_theme()->get( 'Version' );
		$parent_theme_version = wp_get_theme(get_template())->get( 'Version' );

		/* If using a child theme, auto-load the parent theme style. */
		if ( is_child_theme() ) {
			wp_enqueue_style( 'education-hub-style', get_template_directory_uri() . '/style.css', array(), $parent_theme_version );
		}

		/* Always load active theme's style.css. */
		wp_enqueue_style( 'edification-hub-style', get_stylesheet_uri(), array(), $theme_version );
	}
endif;
add_action( 'wp_enqueue_scripts', 'edification_hub_scripts' );

/**
 * Parent theme override functions
 */
require trailingslashit( get_stylesheet_directory() ) . 'inc/override-parent.php';
