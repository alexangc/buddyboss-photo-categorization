<?php
defined('ABSPATH') || exit();

function PHOTOCAT_f_log($log_name, $str)
{
    $log_dir = $_SERVER['DOCUMENT_ROOT'] . "/logs";
    if (!is_dir($log_dir)) {
        mkdir($log_dir);
    }

    $log_file = "$log_dir/$log_name.log";
    $fp = fopen($log_file, 'a');
    fwrite($fp, "$str\n");
    fclose($fp);
}

function PHOTOCAT_get_dir_relative_path($dir_name)
{
    return substr(
        str_replace('\\', '/', realpath($dir_name)),
        strlen(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])))
    );
}

//
function PHOTOCAT_get_category_count()
{
    return get_option('PHOTOCAT_cat_number', "0");
}

function PHOTOCAT_get_categories()
{
    $cat_count = PHOTOCAT_get_category_count();
    $categories = [];

    for ($i = 1; $i <= $cat_count; $i++) {
        $label = get_option("PHOTOCAT_categories_$i", "");
        $options = explode(";", get_option("PHOTOCAT_category_options_$i", ""));

        for ($j = 0; $j < count($options); $j++) {
            $options[$j] = trim($options[$j]);
        }

        if ($label != "") {
            $categories[$i]['label'] = $label;
            $categories[$i]['options'] = $options;
        }
    }
    return $categories;
}
?>
