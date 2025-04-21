<?php

namespace Akash\ApiPlugin\Blocks;

/**
 * AkashApiPluginTableBlock Class
 *
 * Handles the registration, rendering, and AJAX functionality for the table block
 * in the WordPress block editor (Gutenberg).
 *
 * @package AkashApiPlugin
 * @subpackage Blocks
 * @since 1.0.0
 */
class AkashApiPluginTableBlock
{
    /**
     * Constructor for the AkashApiPluginTableBlock class.
     *
     * Registers all necessary WordPress hooks for the table block functionality,
     * including block registration, asset enqueuing, and AJAX handlers.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        add_action('init', [$this, 'table_block_register']);
        add_action('enqueue_block_assets', [$this, 'enqueue_frontend_assets']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
        add_action('wp_ajax_akash_api_plugin_get_table_data', [$this, 'ajax_render_table_block']);
        add_action('wp_ajax_nopriv_akash_api_plugin_get_table_data', [$this, 'ajax_render_table_block']);
    }

    /**
     * Registers the table block with WordPress.
     *
     * Defines the block type with its associated assets and render callback.
     *
     * @since 1.0.0
     * @return void
     */
    public function table_block_register()
    {
        register_block_type(__DIR__ . '/table-block', [
            'style' => 'akash-api-plugin-table-block-style',
            'editor_style' => 'akash-api-plugin-table-block-style',
            'script' => 'akash-api-plugin-table-block-script',
            'editor_script' => 'akash-api-plugin-table-block-editor-script',
            'render_callback' => [$this, 'render_table_block']
        ]);
    }

    /**
     * Enqueues frontend assets for the table block.
     *
     * Registers and enqueues CSS and JavaScript files needed for the frontend
     * display of the table block. Also localizes the script with necessary data.
     *
     * @since 1.0.0
     * @return void
     */
    public function enqueue_frontend_assets()
    {
        wp_register_style(
            'akash-api-plugin-table-block-style',
            plugins_url('table-block/css/style.css', __FILE__),
            [],
            AKASH_API_PLUGIN_VERSION
        );

        wp_register_script(
            'akash-api-plugin-table-block-script',
            plugins_url('table-block/js/table-block.js', __FILE__),
            [
                'jquery'
            ],
            AKASH_API_PLUGIN_VERSION,
            true
        );

        wp_localize_script('akash-api-plugin-table-block-script', 'akashApiPluginTableData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'action' => 'akash_api_plugin_table_data_ajax',
            'nonce' => wp_create_nonce(AKASH_API_PLUGIN_TABLE_BLOCK_NONCE),
            'errorText' => __('Error fetching table data', AKASH_API_PLUGIN_TEXT_DOMAIN)
        ]);
    }

    /**
     * Enqueues editor assets for the table block.
     *
     * Registers and enqueues JavaScript files needed for the block editor
     * functionality. Also localizes the script with necessary data for the editor.
     *
     * @since 1.0.0
     * @return void
     */
    public function enqueue_editor_assets()
    {
        wp_register_script(
            'akash-api-plugin-table-block-editor-script',
            plugins_url('table-block/js/table-block-editor.js', __FILE__),
            [
                'wp-blocks',    // Required for block registration
                'wp-element',   // Required for createElement
                'wp-editor',    // Required for editor components
                'wp-components' // Required for WordPress UI components
            ],
            AKASH_API_PLUGIN_VERSION,
            true
        );

        wp_localize_script('akash-api-plugin-table-block-editor-script', 'akashApiPluginData', [
            'pluginBlocksFolderUrl' => plugins_url('table-block/', __FILE__),
            'ajaxUrl'   => admin_url('admin-ajax.php'),
            'action'    => 'akash_api_plugin_get_table_data',
            'action1' => 'akash_api_plugin_table_data_ajax',
            'nonce' => wp_create_nonce(AKASH_API_PLUGIN_TABLE_BLOCK_NONCE),
            'errorText' => __('Error fetching table data', AKASH_API_PLUGIN_TEXT_DOMAIN)
        ]);
    }

    /**
     * Render callback for server-side rendering of the table block content.
     *
     * Processes block attributes and includes the template file for rendering
     * the table block HTML.
     *
     * @since 1.0.0
     * @param mixed $attributes Block attributes either as an array or object.
     * @return string The rendered HTML for the table block.
     */
    public function render_table_block($attributes)
    {
        if (is_array($attributes)) {
            $attributes = (object) $attributes;
        }

        $className = $attributes->className ?? '';
        $id = $attributes->uniqueId ?? '';
        $default_text = esc_html__($attributes->content, AKASH_API_PLUGIN_TEXT_DOMAIN) // Default text loading from block.json
            ?? __('Loading Table...', AKASH_API_PLUGIN_TEXT_DOMAIN); // If default text is not provided, return this text

        // Render table block
        ob_start();
        include __DIR__ . '/table-block/templates/table-block.php'; // Block template
        return ob_get_clean();
    }

    /**
     * AJAX handler for rendering the table block.
     *
     * Processes AJAX requests to render the table block dynamically.
     * Verifies the nonce for security and returns the rendered HTML.
     *
     * @since 1.0.0
     * @return void Sends a JSON response and exits.
     */
    public function ajax_render_table_block()
    {
        check_ajax_referer(AKASH_API_PLUGIN_TABLE_BLOCK_NONCE, 'security');
        wp_send_json_success(['html' => $this->render_table_block(json_decode(stripslashes($_POST['attributes'])))]);
        exit;
    }
}
