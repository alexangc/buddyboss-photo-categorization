<?php
require_once dirname(__FILE__) . '/../functions.php';

function PHOTOCAT_create_tables() {
  global $wpdb;
  $prefix = $wpdb->prefix;

  $tables['photo_categories'] =
    "CREATE TABLE IF NOT EXISTS {$prefix}bp_photos_categories (
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
      $str = 'Exception caught : ' .  $e->getMessage() . "\n";
      PHOTOCAT_f_log('db-errors', $str);
  }
}
?>