<?php
require_once dirname(__FILE__) . '/../functions.php';
require_once dirname(__FILE__) . '/../database.php';
require_once dirname(__FILE__) . '/../../third-party/smarty/Smarty.class.php';

function PHOTOCAT_collections()
{
    $css = PHOTOCAT_get_dir_relative_path(
        dirname(__FILE__) . '/photo-collections.css'
    );
    $lb_css =
        PHOTOCAT_get_dir_relative_path(
            PHOTOCAT_BB_Platform_Addon::instance()->plugin_path()
        ) . '/third-party/simpleLightbox/simpleLightbox.min.css';
    $js = PHOTOCAT_get_dir_relative_path(
        dirname(__FILE__) . '/photo-collections.js'
    );
    $lb_js =
        PHOTOCAT_get_dir_relative_path(
            PHOTOCAT_BB_Platform_Addon::instance()->plugin_path()
        ) . '/third-party/simpleLightbox/simpleLightbox.min.js';

    wp_enqueue_style('photo-collections', $css);
    wp_enqueue_style('photocat-simple-lightbox', $lb_css);
    wp_enqueue_script('photo-collections', $js);
    wp_enqueue_script('photocat-simple-lightbox', $lb_js);

    $collections = PHOTOCAT_get_current_user_collections();

    $smarty = new Smarty();
    $smarty->assign('collections', $collections);
    $smarty->display(dirname(__FILE__) . '/photo-collections.tpl');
}

function PHOTOCAT_get_current_user_collections()
{
    $user_id = bp_loggedin_user_id();
    $collections = PHOTOCAT_get_user_collections($user_id);
    $media_ids = [];
    $medias = [];
    $media_thumbs = [];

    foreach ($collections as $collection) {
        $collection->photos = PHOTOCAT_get_collection($collection->id, 2, 0);
        foreach ($collection->photos as $photo) {
            $media_ids[] = $photo->media_id;
        }
    }

    if (count($media_ids) > 0) {
        $bp_args = [
            'media_ids' => $media_ids,
            'sort' => 'DESC',
            'order_by' => 'date_created',
        ];
        // At this point $media_ids will be an array unsorted by collection.
        $medias = bp_media_get_specific($bp_args);
    }

    // Isolating the url of the preview photos.
    foreach ($medias['medias'] as $media) {
        $media_thumbs[$media->id] = $media->attachment_data->thumb;
    }

    // Associating preview photos to their collections.
    foreach ($collections as $collection) {
        foreach ($collection->photos as $photo) {
            $photo->thumb = $media_thumbs[$photo->media_id];
        }
    }

    return $collections;
}

function PHOTOCAT_ajax_fetch_collection()
{
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];
    $media_ids = [];
    $medias = [];
    $media_thumbs = [];

    $collection = PHOTOCAT_get_collection($id, 500, 0);

    foreach ($collection as $photo) {
        $media_ids[] = $photo->media_id;
    }

    if (count($media_ids) > 0) {
        $bp_args = [
            'media_ids' => $media_ids,
            'sort' => 'DESC',
            'order_by' => 'date_created',
        ];
        // At this point $media_ids will be an array unsorted by collection.
        $medias = bp_media_get_specific($bp_args);
    }

    // Isolating the url of the preview photos.
    foreach ($medias['medias'] as $media) {
        $media_thumbs[$media->id] = $media->attachment_data->full;
    }
    // Associating preview photos to their collections.
    foreach ($collection as $photo) {
        $photo->thumb = $media_thumbs[$photo->media_id];
    }

    PHOTOCAT_return_json($collection);
}

?>
