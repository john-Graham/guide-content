<?php
/*
 * @wordpress-plugin
 * Plugin Name:  Guide Content
 * Plugin URI:    https://github.com/mack0331/guide-content
 * Description:   Access Guide (guide.wisc.edu) content via CourseLeaf API/XML. For use on UW-Madison academic program websites.
 * Version:   1.9 --Add caching--
 * Author:   Eric MacKay
 * Author URI:    https://github.com/mack0331
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
//Disallow access to altering the PHP code from anywhere outside of Wordpress


if (!defined('ABSPATH')) exit;

if ( is_admin()) {
    require_once plugin_dir_path( __FILE__) . 'admin/admin-menu.php';
    require_once plugin_dir_path( __FILE__) . 'admin/settings-page.php';
    require_once plugin_dir_path( __FILE__) . 'admin/settings-register.php';
    require_once plugin_dir_path( __FILE__) . 'admin/settings-callback.php';
    require_once plugin_dir_path( __FILE__) . 'admin/settings-validation.php';

}


// functions used in admin and public
require_once plugin_dir_path( __FILE__) . 'includes/core-functions.php';


// default plugin options
function guide_content_options_default() {

	return array(
		'custom_title'   => 'default variables if needed',

	);

}










