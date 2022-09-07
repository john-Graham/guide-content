<?php  // The guide - Admin page Settings

if (!defined('ABSPATH')) exit;



// display the plugin settings page
function guide_content_display_settings_page() {
	
	// check if user is allowed access
	if ( ! current_user_can( 'manage_options' ) ) return;
	
	?>
	
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			
			<?php
			
			// output security fields
			settings_fields( 'guide_content_options' );
			
			// output setting sections
			do_settings_sections( 'guide_content' );
			
			// submit button
			submit_button();
			
			?>
			
		</form>
	</div>
	
	<?php
	
}