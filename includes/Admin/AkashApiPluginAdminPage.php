<?php

namespace Akash\ApiPlugin\Admin;

use Akash\ApiPlugin\Api\AkashApiPluginApiEndpoint;

class AkashApiPluginAdminPage
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'create_admin_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    public function enqueue_admin_scripts($hook)
    {
        if ($hook === 'toplevel_page_akash-api-plugin') {
            wp_enqueue_style('akash-api-plugin-admin-style', plugins_url('css/admin.css', __FILE__), [], AKASH_API_PLUGIN_VERSION);
            wp_enqueue_script('akash-api-plugin-admin-script', plugins_url('js/admin.js', __FILE__), ['jquery'], AKASH_API_PLUGIN_VERSION, true);
        }
    }

    public function create_admin_page()
    {
        add_menu_page(
            'Akash API Plugin',
            'Akash API Plugin',
            'manage_options',
            'akash-api-plugin',
            [$this, 'render_admin_page'],
            'dashicons-chart-bar',
            26
        );
    }

    public function render_admin_page()
    {
?>
        <div class="wrap akash-api-admin-wrap">
            <h1 class="akash-api-heading"><?php esc_html_e('Akash API Plugin', AKASH_API_PLUGIN_TEXT_DOMAIN); ?></h1>

            <div class="akash-api-tabs-wrapper">
                <h2 class="nav-tab-wrapper">
                    <a href="#" class="nav-tab nav-tab-active"><?php esc_html_e('Table Data', AKASH_API_PLUGIN_TEXT_DOMAIN); ?></a>
                </h2>
            </div>

            <div class="akash-api-section">
                <form method="post">
                    <?php submit_button('Refresh Data', 'primary', 'akash_api_refresh_data', false); ?>
                </form>

                <?php
                // Refresh logic
                if (isset($_POST['akash_api_refresh_data'])) {
                    delete_transient(AKASH_API_PLUGIN_API_DATA_TRANSIENT);
                    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Data has been refreshed.', AKASH_API_PLUGIN_TEXT_DOMAIN) . '</p></div>';
                }

                $ApiData = new AkashApiPluginApiEndpoint(true);;

                echo '<div class="akash-api-table-container">';
                echo $this->render_table_html($ApiData->data);
                echo '</div>';
                ?>
            </div>
        </div>
<?php
    }
    private function render_table_html($table_data)
    {
        // Get table data
        $data = $table_data;

        if (empty($data['data']['headers']) || empty($data['data']['rows'])) {
            return '<p>No data available to display.</p>';
        }

        // Start building HTML output
        $table_html = '';

        $table_html .= '<table class="akash-api-plugin-table">';
        $table_html .= '<caption>' . esc_html($data['title']) . '</caption>';
        $table_html .= '<thead><tr>';

        // Headers
        foreach ($data['data']['headers'] as $header) {
            $table_html .= '<th>' . esc_html($header) . '</th>';
        }

        $table_html .= '</tr></thead><tbody>';

        // Rows
        foreach ($data['data']['rows'] as $row) {
            $table_html .= '<tr>';
            $table_html .= '<td>' . esc_html($row['id']) . '</td>';
            $table_html .= '<td>' . esc_html($row['fname']) . '</td>';
            $table_html .= '<td>' . esc_html($row['lname']) . '</td>';
            $table_html .= '<td>' . esc_html($row['email']) . '</td>';
            $table_html .= '<td>' . date('Y-m-d', $row['date']) . '</td>';
            $table_html .= '</tr>';
        }

        $table_html .= '</tbody></table>';

        return $table_html;
    }
}
