<?php
require_once dirname(__FILE__) . '/../functions.php';
require_once dirname(__FILE__) . '/../database.php';
require_once dirname(__FILE__) . '/../../third-party/smarty/Smarty.class.php';

function PHOTOCAT_gallery()
{
    $css = PHOTOCAT_get_dir_relative_path(
        dirname(__FILE__) . '/photo-gallery.css'
    );
    $js = PHOTOCAT_get_dir_relative_path(
        dirname(__FILE__) . '/photo-gallery.js'
    );
    wp_enqueue_style('photo-gallery', $css);
    wp_enqueue_script('photo-gallery', $js);

    $smarty = new Smarty();

    $categories = PHOTOCAT_get_categories();
    $smarty->assign('categories', $categories);

    $smarty->display(dirname(__FILE__) . '/photo-gallery.tpl');
}

function PHOTOCAT_ajax_fetch_photos()
{
    // TODO: add pagination parameter for offset in query
    $data = json_decode(file_get_contents('php://input'), true);
    $categories = $data['categories'];

    $tags = [];
    foreach ($categories as $cat) {
        $tags[] = PHOTOCAT_tagify(sanitize_text_field($cat));
    }

    $medias_cat = PHOTOCAT_get_media_ids_with_categories($tags);
    $media_ids = [];
    foreach ($medias_cat as $media_id => $categories) {
        $media_ids[] = $media_id;
    }

    $args = [
        'media_ids' => $media_ids,
        'max' => false,
        'count_total' => false,
        'page' => 1,
        'per_page' => 20,
        'sort' => 'DESC',
        'order_by' => 'date_created',
        // 'user_id' => 7,
        'album_id' => false,
    ];

    $res['categories'] = $medias_cat;
    $res['photos'] = (object) bp_media_get_specific($args);

    // Filtering out non-mandatory or possibly sensitive data
    for ($i = 0; $i < count($res['photos']->medias); $i++) {
        $media = $res['photos']->medias[$i];
        if ($media->privacy != 'public') {
            continue;
        }
        if (isset($media->attachment_data->meta['image_meta'])) {
            unset($media->attachment_data->meta['image_meta']);
        }

        $media = [
            'id' => $media->id,
            'title' => $media->title,
            'user_id' => $media->user_id,
            'date_created' => $media->date_created,
            'attachment_data' => $media->attachment_data,
        ];

        $res['photos']->medias[$i] = $media;
    }

    // PHOTOCAT_f_log('test', var_export($res['photos']->medias, true));
    PHOTOCAT_return_json($res);
}
?>
