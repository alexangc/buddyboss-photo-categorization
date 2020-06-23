<?php
defined('ABSPATH') || exit();
require_once dirname(__FILE__) .
    '/bp-photo-cat-admin-settings/categories-editor.php';
require_once dirname(__FILE__) .
    '/bp-photo-cat-upload-categories/upload-categories.php';
require_once dirname(__FILE__) .
    '/bp-photo-cat-on-photo-upload/upload-actions.php';
require_once dirname(__FILE__) . '/bp-photo-cat-gallery/photo-gallery.php';
require_once dirname(__FILE__) .
    '/bp-photo-cat-collections/photo-collections.php';

/**
 * Central file importing the different components of the plugin.
 */

// Plugin's global CSS
if (!function_exists('PHOTOCAT_admin_enqueue_script')) {
    function PHOTOCAT_admin_enqueue_script()
    {
        wp_enqueue_style(
            'buddyboss-addon-admin-css',
            plugin_dir_url(__FILE__) . 'style.css'
        );
    }

    add_action('admin_enqueue_scripts', 'PHOTOCAT_admin_enqueue_script');
}

// BuddyBoss's admin panel integration
function PHOTOCAT_register_integration()
{
    require_once dirname(__FILE__) .
        '/bp-photo-cat-integration/buddyboss-integration.php';
    buddypress()->integrations['addon'] = new PHOTOCAT_BuddyBoss_Integration();
}

wp_enqueue_script(
    'axios',
    'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js'
);

add_action('bp_setup_integrations', 'PHOTOCAT_register_integration');
add_action('bp_media_add_handler', 'PHOTOCAT_on_photo_upload');
add_filter('bp_media_before_delete', 'PHOTOCAT_on_photo_delete');
add_action('wp_ajax_get_photos', 'PHOTOCAT_ajax_fetch_photos');
add_action('wp_ajax_get_collection', 'PHOTOCAT_ajax_fetch_collection');

add_filter('bp_get_template_part', 'PHOTOCAT_uploader_categories', 10, 3);

add_shortcode('PHOTOCAT_gallery_shortcode', 'PHOTOCAT_gallery');
add_shortcode('PHOTOCAT_collections_shortcode', 'PHOTOCAT_collections');
