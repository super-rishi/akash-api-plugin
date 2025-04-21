<?php

namespace Akash\ApiPlugin\Core;

use Akash\ApiPlugin\Admin\AkashApiPluginAdminPage;
use Akash\ApiPlugin\Ajax\AkashApiPluginAjaxEndpoint;
use Akash\ApiPlugin\Blocks\AkashApiPluginTableBlock;
use Akash\ApiPlugin\Cli\AkashApiPluginCliCommand;

class AkashApiPlugin
{
    public function __construct()
    {
        // Create Plugin Constants
        $this->plugin_constants();

        // Do initialization stuff
        new AkashApiPluginAdminPage();
        new AkashApiPluginAjaxEndpoint();
        new AkashApiPluginTableBlock();

        // Register CLI Command(s)
        new AkashApiPluginCliCommand();
    }

    private function plugin_constants()
    {

        // Define plugin version
        define('AKASH_API_PLUGIN_VERSION', '1.0.0');

        // Define plugin name
        define('AKASH_API_PLUGIN_NAME', 'Akash API Plugin');

        // Define plugin text domain
        define('AKASH_API_PLUGIN_TEXT_DOMAIN', 'akash-api-plugin');

        // Define plugin nonce strings
        define('AKASH_API_PLUGIN_TABLE_BLOCK_NONCE', 'akash_api_plugin_table_block_nonce');
        define('AKASH_API_PLUGIN_TABLE_DATA_NONCE', 'akash_api_plugin_table_data_nonce');
        define('AKASH_API_PLUGIN_API_DATA_NONCE', 'akash_api_plugin_api_data_nonce');

        // Define transient names
        define('AKASH_API_PLUGIN_API_DATA_TRANSIENT', 'akash_api_plugin_api_data');

        // Define API endpoint
        define('AWESOME_MOTIVE_API_URL', 'https://miusage.com/v1/challenge/1/');
    }
}
