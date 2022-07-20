<?php // guide content - Admin menu

if (!defined('ABSPATH')) exit;


// add sub-level administrative menu
function guide_content_add_sublevel_menu() {
	/*
	add_submenu_page(
		string   $parent_slug,
		string   $page_title,
		string   $menu_title,
		string   $capability,
		string   $menu_slug,
		callable $function = ''
	);
	*/
	add_submenu_page(
		'options-general.php',
		'The Guide Settings',
		'The Guide',
		'manage_options',
		'guide-content',
		'guide_content_display_settings_page'
	);
}
add_action( 'admin_menu', 'guide_content_add_sublevel_menu' );
