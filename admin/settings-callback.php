<?php //guide-content - Settings callbacks

if (!defined('ABSPATH')) exit;


// callback: admin section
function guide_content_callback_section_custom_css()
{
    echo '<p>This allows to you to add CSS to the page where the plugin is used.  The CSS is only added on pages where the plugin is in use. do not include the script tag.</p>';
}

// callback: admin section
function guide_content_callback_section_tables()
{
    echo '<p>This setting allows you to add CSS classes to the table tags.  For those using a bootstrap based theme, adding (table table-striped) will add bootstrap formatting to the tables.</p>';
}

// callback: text field
function guide_content_callback_field_text( $args ) {
	
	$options = get_option( 'guide_content_options', guide_content_options_default() );
	
	$id    = isset( $args['id'] )    ? $args['id']    : '';
	$label = isset( $args['label'] ) ? $args['label'] : '';
	
	$value = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : '';
	
	echo '<input id="guide_content_options_'. $id .'" name="guide_content_options['. $id .']" type="text" size="40" value="'. $value .'"><br />';
	echo '<label for="guide_content_options_'. $id .'">'. $label .'</label>';
	
}

// callback: textarea field
function guide_content_callback_field_textarea( $args ) {
	
	$options = get_option( 'guide_content_options', guide_content_options_default() );
	
	$id    = isset( $args['id'] )    ? $args['id']    : '';
	$label = isset( $args['label'] ) ? $args['label'] : '';
	
	$allowed_tags = wp_kses_allowed_html( 'post' );
	
	$value = isset( $options[$id] ) ? wp_kses( stripslashes_deep( $options[$id] ), $allowed_tags ) : '';
	
	echo '<textarea id="guide_content_options_'. $id .'" name="guide_content_options['. $id .']" rows="5" cols="50">'. $value .'</textarea><br />';
	echo '<label for="guide_content_options_'. $id .'">'. $label .'</label>';
	
}
