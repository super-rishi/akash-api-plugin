<?php

/**
 * Plugin Name: Akash API Plugin
 * Plugin URI: https://github.com/super-rishi/akash-api-plugin
 * Description: A simple plugin that retrieves data from a remote API endpoint, and makes that data accessible/retrievable from an API endpoint on the WordPress site your plugin is installed on. The data will be displayed via a custom block and on an admin WordPress page.
 * Version: 1.0.0
 * Requires at least: 6.7
 * Requires PHP: 7.4
 * Author: Akash Sharma
 * Author URI: #
 * License: GPLv2 or later
 * Text Domain: akash-api-plugin
 * Domain Path: /languages
 */


if (! defined('ABSPATH')) {
	die(__('Direct access not allowed.', 'akash-api-plugin'));
}

// Define plugin directory
define('AKASH_API_PLUGIN_DIR', __DIR__);


// Load Composer Autoloader
require_once AKASH_API_PLUGIN_DIR . '/vendor/autoload.php';

use Akash\ApiPlugin\Core\AkashApiPlugin;

add_action('plugins_loaded', 'initiate_plugin');

function initiate_plugin()
{
	new AkashApiPlugin();
}
