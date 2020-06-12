<?php
require_once dirname(__FILE__) . '/../database.php';

function PHOTOCAT_on_photo_upload($params) {

  if (count($_POST['medias']) > 0) {
    $categories = $_POST['medias'][0]['categories'];
    $tags = array();

    foreach ($categories as $cat) {
      $tags[] = PHOTOCAT_tagify(sanitize_text_field($cat['value']));
    }

    foreach ($params as $media_id) {
      PHOTOCAT_insert_photo_categories($media_id, $tags);
    }
  }

  return $params;
}

function PHOTOCAT_tagify($string) {
  // TODO
  return $string;
}
?>
