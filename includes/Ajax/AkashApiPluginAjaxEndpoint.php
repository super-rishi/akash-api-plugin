<?php

namespace Akash\ApiPlugin\Ajax;

use Akash\ApiPlugin\Api\AkashApiPluginApiEndpoint;

/**
 * AkashApiPluginAjaxEndpoint Class
 *
 * Handles AJAX requests for the Akash API Plugin, specifically for rendering table data.
 * This class registers AJAX endpoints and processes incoming requests to display API data in tabular format.
 *
 * @package AkashApiPlugin
 * @subpackage Ajax
 */
class AkashApiPluginAjaxEndpoint
{
    /**
     * Constructor for the AkashApiPluginAjaxEndpoint class.
     *
     * Registers AJAX actions for both logged-in and non-logged-in users
     * to handle table data rendering requests.
     */
    public function __construct()
    {
        add_action('wp_ajax_akash_api_plugin_table_data_ajax', [$this, 'ajax_render_table_data']);
        add_action('wp_ajax_nopriv_akash_api_plugin_table_data_ajax', [$this, 'ajax_render_table_data']);
    }

    /**
     * AJAX callback to render table data.
     *
     * Processes AJAX requests for table data, verifies the nonce for security,
     * fetches data from the API endpoint, and returns the rendered HTML table.
     *
     * @return void Sends JSON response and terminates execution
     */
    public function ajax_render_table_data()
    {
        check_ajax_referer(AKASH_API_PLUGIN_TABLE_BLOCK_NONCE, 'security');

        $ApiData = new AkashApiPluginApiEndpoint();

        unset($_GET['action']);
        unset($_GET['security']);

        $showColumns = array_map(function ($value) {
            return sanitize_text_field($value);
        }, $_GET);

        $table_html = $this->render_table_html($ApiData->data, $showColumns);
        wp_send_json_success(['html' => $table_html]);
        wp_die();
    }

    /**
     * Renders HTML table from API data.
     *
     * Generates an HTML table structure based on the provided API data and column visibility settings.
     * Handles data formatting and escaping for secure output.
     *
     * @param array $table_data  The API data containing headers, rows, and title
     * @param array $showColumns Configuration array indicating which columns should be displayed
     * 
     * @return string HTML markup for the table or a message if no data is available
     */
    private function render_table_html($table_data, $showColumns)
    {
        // Get table data
        $data = $table_data;

        // Fetch headers as indexed array
        $showHeadings = array_values($showColumns);

        if (empty($data['data']['headers']) || empty($data['data']['rows'])) {
            return '<p>No data available to display.</p>';
        }

        // Start building HTML output
        $table_html = '';

        $table_html .= '<table class="akash-api-plugin-table">';
        $table_html .= '<caption>' . esc_html($data['title']) . '</caption>';
        $table_html .= '<thead><tr>';

        // Headers
        foreach ($data['data']['headers'] as $index => $header) {
            if ($showHeadings[$index] === 'true') {
                $table_html .= '<th>' . esc_html($header) . '</th>';
            }
        }

        $table_html .= '</tr></thead><tbody>';

        // Rows
        foreach ($data['data']['rows'] as $row) {
            $table_html .= '<tr>';
            if (isset($showColumns['id']) && $showColumns['id'] === 'true') {
                $table_html .= '<td>' . esc_html($row['id']) . '</td>';
            }
            if (isset($showColumns['fname']) && $showColumns['fname'] === 'true') {
                $table_html .= '<td>' . esc_html($row['fname']) . '</td>';
            }
            if (isset($showColumns['lname']) && $showColumns['lname'] === 'true') {
                $table_html .= '<td>' . esc_html($row['lname']) . '</td>';
            }
            if (isset($showColumns['email']) && $showColumns['email'] === 'true') {
                $table_html .= '<td>' . esc_html($row['email']) . '</td>';
            }
            if (isset($showColumns['date']) && $showColumns['date'] === 'true') {
                $table_html .= '<td>' . date('Y-m-d', $row['date']) . '</td>';
            }
            $table_html .= '</tr>';
        }

        $table_html .= '</tbody></table>';

        return $table_html;
    }
}
