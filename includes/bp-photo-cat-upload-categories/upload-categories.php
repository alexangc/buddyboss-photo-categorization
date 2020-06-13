<?php
defined('ABSPATH') || exit();
require_once dirname(__FILE__) . '/../functions.php';

/**
 * Somewhat hacky way to add the category selectors into the BuddyBoss upload
 * photo form.
 *
 * We hook the call to the template, and add a script dynamically adding the
 * selector fields to the main HTML element of the widget.
 */
function PHOTOCAT_uploader_categories($templates, $slug)
{
    if ('media/uploader' == $slug) {
        $inc = PHOTOCAT_get_dir_relative_path(dirname(__FILE__));
        $inc .= '/add_categories_selectors.js';
        $categories = json_encode(PHOTOCAT_get_categories());

        // Preparing the categories data before importing the script
        echo '<script>' . PHP_EOL;
        echo "  const PHOTOCAT_categories_data = $categories;";
        echo '</script>' . PHP_EOL;

        wp_enqueue_script('add_categories_selectors', $inc);
    }
    return $templates;
}

?>
