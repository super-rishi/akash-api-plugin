<?php

namespace Akash\ApiPlugin\Cli;

use WP_CLI;


class AkashApiPluginCliCommand
{
    public function __construct()
    {
        // Register CLI Command
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::add_command('akash-api-plugin', array($this, 'refresh_data'));
        }
    }
    /**
     * Refreshes the cached API table data by deleting the transient.
     *
     * ## EXAMPLE
     *
     *     wp akash-api-plugin refresh-data
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
