<?php //guide-content - Settings callbacks

if (!defined('ABSPATH')) exit;


// validate plugin settings
function guide_content_validate_options($input)
{

	// custom title
	if (isset($input['custom_table_Class'])) {

		$input['custom_table_Class'] = sanitize_text_field($input['custom_table_Class']);
	}

	// custom message
	if (isset($input['custom_css_Class'])) {

		$input['custom_css_Class'] = wp_kses_post($input['custom_css_Class']);
	}

	return $input;
}
