<?php

namespace Akash\ApiPlugin\Blocks;

class AkashApiPluginTableBlock
{
    public function __construct()
    {
        add_action('init', [$this, 'table_block_register']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_scripts']);
        add_action('wp_ajax_akash_api_plugin_get_table_data', [$this, 'ajax_render_table_block']);
        add_action('wp_ajax_nopriv_akash_api_plugin_get_table_data', [$this, 'ajax_render_table_block']);
    }

    public function table_block_register()
    {
        register_block_type(__DIR__ . '/table-block', [
            'editor_script' => 'akash-api-plugin-table-block-editor-script',
            'render_callback' => [$this, 'render_table_block']
        ]);
    }

    public function enqueue_editor_scripts()
    {
        wp_register_script(
            'akash-api-plugin-table-block-editor-script',
            plugins_url('table-block/js/index.js', __FILE__),
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
            'nonce'     => wp_create_nonce(AKASH_API_PLUGIN_TABLE_BLOCK_NONCE)
        ]);
    }

    // Render callback for server-side rendering of block content
    public function render_table_block($attributes)
    {
        if (is_array($attributes)) {
            $attributes = (object) $attributes;
        }

        $show_name = $attributes->showName ?? true;
        $show_email = $attributes->showEmail ?? true;
        $show_phone = $attributes->showPhone ?? true;
        $className = $attributes->className ?? '';
        $default_text = esc_html__($attributes->content, AKASH_API_PLUGIN_TEXT_DOMAIN) /** Default text loading from block.json*/
        ?? __('Loading Table Data...', AKASH_API_PLUGIN_TEXT_DOMAIN); /** If default text is not provided, return this text */

        ob_start();
        include __DIR__ . '/table-block/templates/table-block.php'; // Block template
        return ob_get_clean();
    }

    public function ajax_render_table_block()
    {
        check_ajax_referer(AKASH_API_PLUGIN_TABLE_BLOCK_NONCE, 'security');
        wp_send_json_success(['html' => $this->render_table_block(json_decode(stripslashes($_POST['attributes'])))]);
        exit;
    }
}
