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
    CONSTRAINT FK_media_id FOREIGN KEY (media_id) REFERENCES {$prefix}bp_media (id),
    CONSTRAINT FK_user_id FOREIGN KEY (user_id) REFERENCES {$prefix}users (ID)
  );";

    $tables[
        'photo_collections'
    ] = "CREATE TABLE IF NOT EXISTS {$prefix}bp_photos_collections (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        owner_id BIGINT(20) UNSIGNED NOT NULL,
        title VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'latin1_swedish_ci',

        PRIMARY KEY (id) USING BTREE,
        KEY FK_user_id (owner_id) USING BTREE,
        CONSTRAINT FK_owner_id FOREIGN KEY (owner_id) REFERENCES {$prefix}users (ID)
    );";

    $tables[
        'photo_collections_items'
    ] = "CREATE TABLE IF NOT EXISTS {$prefix}bp_photos_collections_items (
        collection_id BIGINT(20) NOT NULL,
        media_id      bigint(20) NOT NULL,

        PRIMARY KEY (collection_id, media_id) USING BTREE,
        KEY FK_collection_id (collection_id),
        KEY FK_collection_media_id (media_id),
        CONSTRAINT FK_collection_id FOREIGN KEY (collection_id)
            REFERENCES {$prefix}bp_photos_collections (id),
        CONSTRAINT FK_collection_media_id FOREIGN KEY (media_id)
            REFERENCES {$prefix}bp_media (id)
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

function PHOTOCAT_delete_saved_categories_for_medias($params)
{
    if (!is_array($params) || !count($params) > 0) {
        return;
    }

    global $wpdb;
    $prefix = $wpdb->prefix;
    $last_id = count($params) - 1;

    $sql = "DELETE FROM {$prefix}bp_photos_categories WHERE media_id IN (";
    for ($i = 0; $i < $last_id; $i++) {
        $sql .= "{$params[$i]->id}, ";
    }
    $sql .= " {$params[$last_id]->id})";

    $wpdb->query($sql);
}

function PHOTOCAT_get_media_ids_for_categories($tags, $limit = 20, $page = 1)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $tag_count = count($tags);
    $offset = $limit * ($page - 1);

    $sql = "SELECT media_id FROM {$prefix}bp_photos_categories";

    if (count($tags) > 0) {
        $sql .= " WHERE category_tag IN (";
        $lastId = count($tags) - 1;
        for ($i = 0; $i < $lastId; $i++) {
            $sql .= "'" . $tags[$i] . "', ";
        }
        $sql .= "'" . $tags[$lastId] . "')";
    }

    $sql .= " GROUP BY media_id HAVING COUNT(*) >= $tag_count";
    $sql .= " ORDER BY media_id DESC";
    $sql_count = "SELECT count(*) as count FROM ($sql) AS results";
    $sql .= " LIMIT $limit OFFSET $offset";
    $rows = $wpdb->get_results($sql);
    $count = $wpdb->get_results($sql_count);

    $results['count'] = $count[0]->count;
    $results['medias'] = $rows;
    $results = (object) $results;

    return $results;
}

function PHOTOCAT_get_user_collections($user_id)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $sql = "SELECT * FROM {$prefix}bp_photos_collections WHERE owner_id=$user_id";
    return $wpdb->get_results($sql);
}

function PHOTOCAT_get_collection($collection_id, $limit = 2, $offset = 0)
{
    global $wpdb;
    $prefix = $wpdb->prefix;
    $sql = "SELECT * FROM {$prefix}bp_photos_collections_items
    WHERE collection_id=$collection_id
    ORDER BY media_id DESC
    LIMIT $limit OFFSET $offset";
    return $wpdb->get_results($sql);
}

?>
