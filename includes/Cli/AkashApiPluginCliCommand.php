<?php

namespace Akash\ApiPlugin\Cli;

use WP_CLI;

/**
 * Provides WP-CLI commands for the Akash API Plugin.
 * 
 * This class registers and implements CLI commands that allow administrators
 * to manage the Akash API Plugin from the command line interface.
 * 
 * @since 1.0.0
 */
class AkashApiPluginCliCommand
{
    /**
     * Initializes the CLI command by registering it with WP-CLI.
     * 
     * @since 1.0.0
     */
    public function __construct()
    {
        // Register CLI Command
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::add_command('akash-api-plugin', [$this, 'refresh_data']);
        }
    }
    /**
     * Refreshes the cached API table data by deleting the transient.
     * 
     * This command clears the cached API data stored in WordPress transients,
     * forcing the plugin to fetch fresh data on the next AJAX request.
     *
     * ## EXAMPLE
     *
     *     wp akash-api-plugin refresh-data
     * 
     * @since 1.0.0
     * @return void
     */
    public function refresh_data()
    {
        if (delete_transient(AKASH_API_PLUGIN_API_DATA_TRANSIENT)) {
            WP_CLI::success(__('API table data cache cleared. Fresh data will be loaded on next AJAX call.', AKASH_API_PLUGIN_TEXT_DOMAIN));
        } else {
            WP_CLI::warning(__('No cached data found or already cleared.', AKASH_API_PLUGIN_TEXT_DOMAIN));
        }
    }
}
