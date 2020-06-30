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
    $data = json_decode(file_get_contents('php://input'), true);
    $categories = $data['categories'];
    $limit = $data['limit'] ? $data['limit'] : 20;
    $page = $data['page'] ? $data['page'] : 1;

    $tags = [];
    foreach ($categories as $cat) {
        $tags[] = PHOTOCAT_tagify(sanitize_text_field($cat));
    }

    $fetched = PHOTOCAT_get_media_ids_for_categories($tags, $limit, $page);
    $media_ids = [];
    foreach ($fetched->medias as $media) {
        $media_ids[] = $media->media_id;
    }

    $bp_args = [
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

    $res['total_count'] = $fetched->count;
    $res['offset'] = $limit * ($page - 1);
    $res['limit'] = $limit;

    $res['categories'] = $tags;
    $res['photos'] =
        count($media_ids) > 0
            ? (object) bp_media_get_specific($bp_args)
            : (object) ['medias' => []];

    // Filtering out non-mandatory or possibly sensitive data
    for ($i = 0; $i < count($res['photos']->medias); $i++) {
        // Only using public photos
        if ($res['photos']->medias[$i]->privacy != 'public') {
            continue;
        }
        $res['photos']->medias[$i] = PHOTOCAT_filter_media_data(
            $res['photos']->medias[$i]
        );
    }

    PHOTOCAT_return_json($res);
}
?>
