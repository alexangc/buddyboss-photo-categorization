<?php
require_once dirname(__FILE__) . '/../database.php';

function PHOTOCAT_on_photo_upload($params)
{
    if (count($_POST['medias']) > 0) {
        $categories = $_POST['medias'][0]['categories'];
        $tags = [];

        foreach ($categories as $cat) {
            $tags[] = PHOTOCAT_tagify(sanitize_text_field($cat['value']));
        }

        foreach ($params as $media_id) {
            PHOTOCAT_insert_photo_categories($media_id, $tags);
        }
    }

    return $params;
}

function PHOTOCAT_tagify($string)
{
    $string = strtr(
        utf8_decode($string),
        utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
        'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
    );
    return strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $string));
}
?>
