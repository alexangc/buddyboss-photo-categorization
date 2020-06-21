<?php
require_once dirname(__FILE__) . '/functions.php';

function PHOTOCAT_create_tables()
{
    global $wpdb;
    $prefix = $wpdb->prefix;

    $tables[
        'photo_categories'
    ] = "CREATE TABLE IF NOT EXISTS {$prefix}bp_photos_categories (
    media_id      bigint(20) NOT NULL,
    user_id       bigint(20) unsigned NOT NULL,
    category_tag  varchar(50) NOT NULL DEFAULT '',

    PRIMARY KEY (media_id, user_id, category_tag),
    KEY FK_media_id (media_id),
    KEY FK_user_id (user_id),
    CONSTRAINT FK_media_id FOREIGN KEY (media_id) REFERENCES ebtiafsmz_bp_media (id),
    CONSTRAINT FK_user_id FOREIGN KEY (user_id) REFERENCES ebtiafsmz_users (ID)
  );";

    try {
        foreach ($tables as $query) {
            $wpdb->query($query);
        }
    } catch (Exception $e) {
        $str = 'Exception caught : ' . $e->getMessage() . "\n";
        PHOTOCAT_f_log('db-errors', $str);
    }
}

function PHOTOCAT_insert_photo_categories($media_id, $tags)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $user_id = bp_loggedin_user_id();

    foreach ($tags as $tag) {
        $query = "INSERT INTO {$prefix}bp_photos_categories (media_id, user_id, category_tag) VALUES ('$media_id', '$user_id', '$tag')";
        $wpdb->query($query);
    }
}

function PHOTOCAT_delete_saved_categories_for_medias($params) {

    if (!is_array($params) || !count($params) > 0) {
        return;
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $last_id = count($params) - 1;

    $sql = "DELETE FROM {$prefix}bp_photos_categories WHERE media_id IN (";
    for ($i = 0; $i < $last_id; $i++) {
        $sql.= "{$params[$i]->id}, ";
    }
    $sql.= " {$params[$last_id]->id})";

    $wpdb->query($sql);
}

function PHOTOCAT_get_media_ids_with_categories($tags)
{
    global $wpdb;
    $prefix = $wpdb->prefix;

    $sql = "SELECT * FROM {$prefix}bp_photos_categories";

    // [1] Fetch the `(media_id, category_tag)` assocs for each `$tags[i]`.
    if (count($tags) > 0) {
        $sql .= " WHERE category_tag IN (";
        $lastId = count($tags) - 1;
        for ($i = 0; $i < $lastId; $i++) {
            $sql .= "'" . $tags[$i] . "', ";
        }
        $sql .= "'" . $tags[$lastId] . "')";
    }
    $lines = $wpdb->get_results($sql);

    // [2] Aggregate all the categories around the `media_id` field.
    $results = [];
    foreach ($lines as $line) {
        if ($results[$line->media_id]) {
            $results[$line->media_id]->categories[] = $line->category_tag;
        } else {
            $results[$line->media_id] = (object) [
                'categories' => [$line->category_tag],
            ];
        }
    }

    return $results;
}

?>
