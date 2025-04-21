<?php

/**
 * Template for displaying table block with dynamic data
 */

$classes = 'wp-block-akash-api-plugin-table-block';

if (!empty($className)) {
    $classes .= ' ' . esc_attr($className);
}
?>

<div class="<?php echo esc_attr($classes); ?>">
    <div class="akash-api-plugin-table-container" id="<?php echo $id; ?>">
        <p class="loading-text">
            <?php echo esc_html__($default_text); ?>
        </p>
        <div class="akash-api-plugin-table-content"></div>
    </div>
</div>

<script>
    setTimeout(function() {
        akash_api_plugin_fetch_table_data(<?php echo json_encode((array) $attributes); ?>);
    }, 100);
</script>