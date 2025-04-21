<?php

namespace Akash\ApiPlugin\Api;

/**
 * Class AkashApiPluginApiEndpoint
 * 
 * Handles API endpoint functionality for the Akash API Plugin.
 * Responsible for fetching, caching, and providing access to API data.
 */
class AkashApiPluginApiEndpoint
{
    /**
     * Stores the API data retrieved from the remote endpoint or cache.
     *
     * @var mixed
     */
    public $data = '';

    /**
     * Constructor for the AkashApiPluginApiEndpoint class.
     * 
     * Initializes the class by loading API data on instantiation.
     */
    public function __construct()
    {
        $this->data = $this->get_table_data();
    }

    /**
     * Retrieves API data from transient cache or fresh from the API.
     * 
     * Checks if valid data exists in the transient cache. If not, fetches
     * fresh data from the API endpoint.
     *
     * @return mixed The API data or false on failure.
     */
    private function get_table_data()
    {
        $data = get_transient(AKASH_API_PLUGIN_API_DATA_TRANSIENT);
        if (false === $data || empty($data)) {
            $data = $this->fetch_data_from_api(true);
        }
        return $data;
    }

    /**
     * Fetches data from the remote API endpoint.
     * 
     * Makes a request to the API endpoint defined by AWESOME_MOTIVE_API_URL,
     * processes the response, and caches the result in a transient for 1 hour.
     *
     * @return mixed The API data as an array or false on failure.
     */
    private function fetch_data_from_api()
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
        return $api_data;
    }
}
