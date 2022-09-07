<?php //guide-content - Settings callbacks

if (!defined('ABSPATH')) exit;

// register plugin settings
function guide_content_register_settings() {
	/*
	register_setting( 
		string   $option_group, 
		string   $option_name, 
		callable $sanitize_callback
	);
	*/
	
	register_setting( 
		'guide_content_options', 
		'guide_content_options', 
		'guide_content_callback_validate_options' 
	); 

    	/*
	
	add_settings_section( 
		string   $id, 
		string   $title, 
		callable $callback, 
		string   $page
	);
	
	*/

    add_settings_section( 
		'guide_content_section_tables', 
		'Add CSS Classes to table tags', 
		'guide_content_callback_section_tables', 
		'guide_content'
	);
	
	add_settings_section( 
		'guide_content_section_custom_css', 
		'Add CSS to Head', 
		'guide_content_callback_section_custom_css', 
		'guide_content'
	);

	/*

	add_settings_field(
    	string   $id,
		string   $title,
		callable $callback,
		string   $page,
		string   $section = 'default',
		array    $args = []
	);

	*/

	add_settings_field(
		'custom_table_Class',
		'Additional table class',
		'guide_content_callback_field_text',
		'guide_content',
		'guide_content_section_tables',
		[ 'id' => 'custom_table_Class', 'label' => 'Custom classes to add to tables' ]
	);


    add_settings_field(
		'custom_css_Class',
		'Custom CSS for Head',
		'guide_content_callback_field_textarea',
		'guide_content',
		'guide_content_section_custom_css',
		[ 'id' => 'custom_css_Class', 'label' => 'Custom css to add to head.' ]
	);

}
add_action( 'admin_init', 'guide_content_register_settings' );

