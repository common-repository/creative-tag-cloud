<?php

/**
* Creative Tag Cloud bootstrap file
*
*
*
* @link        https://chattymango.com/creative-tag-cloud/
* @since       1.0
* @package     creative_tag_cloud
* @author      Christoph Amthor
* @copyright   2017 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     GPLv3
*
* @wordpress-plugin
* Plugin Name:       Creative Tag Cloud
* Plugin URI:        https://chattymango.com/creative-tag-cloud/
* Description:       Display a spiral or wavy tag cloud
* Version:           0.3.2
* Author:            Chatty Mango
* Author URI:        https://chattymango.com/
* License:           GPLv3
* License URI:       https://chattymango.com/
* Text Domain:       creative-tag-cloud
* Domain Path:       /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
*	URL of feed about realease posts
*/
define( 'CREATIVE_TAG_CLOUD_UPDATES_RSS_URL', 'https://chattymango.com/category/updates/creative-tag-cloud/feed/' );

/*
* The plugin's relative path (starting below the plugin directory), including the name of this file.
*/
define ( "CREATIVE_TAG_CLOUD_PLUGIN_BASENAME", plugin_basename( __FILE__ ) );

/*
* The plugin's absolute path with plugin file
*/
define ( "CREATIVE_TAG_CLOUD_PLUGIN_ABSOLUTE_NAME", dirname( __FILE__ ) . '/creative-tag-cloud.php' );

/**
* The core plugin class that is used to define internationalization,
* admin-specific hooks, and public-facing site hooks.
*/
require plugin_dir_path( __FILE__ ) . 'includes/class-creative-tag-cloud.php';

/**
* Begins execution of the plugin.
*
* Since everything within the plugin is registered via hooks,
* then kicking off the plugin from this point in the file does
* not affect the page life cycle.
*
* @since    1.0
*/
function run_creative_tag_cloud() {

	$plugin = new creative_tag_cloud();
	$plugin->run();

}

run_creative_tag_cloud();
