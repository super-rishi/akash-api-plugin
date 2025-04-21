<?php

namespace Akash\ApiPlugin\Api;

class AkashApiPluginApiEndpoint
{
    public $data = '';

    public function __construct($returnData = false)
    {
        $this->data = $this->get_table_data($returnData);
    }

    private function get_table_data($returnData)
    {
        $data = get_transient(AKASH_API_PLUGIN_API_DATA_TRANSIENT);
        if (false === $data || empty($data)) {
            $data = $this->fetch_data_from_api(true);
        }
        if ($returnData) {
            return $data;
        }
    }

    private function fetch_data_from_api($returnData)
    {
        $url = AWESOME_MOTIVE_API_URL;
        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return false;
        }
        $response_code = wp_remote_retrieve_response_code($response);
        if (200 !== $response_code) {
            return false;
        }
        $api_data = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($api_data)) {
            return false;
        }
        set_transient(AKASH_API_PLUGIN_API_DATA_TRANSIENT, $api_data, 60 * 60 * 1);
        if ($returnData) {
            return $api_data;
        }
    }
}
