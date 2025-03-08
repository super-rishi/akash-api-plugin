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
    <p class="loading-text">
        <?php echo esc_html__($default_text); ?>
    </p>
    <div class="api-data-table-container">
        <!-- table content will come here here -->
    </div>
</div>