<?php
defined('ABSPATH') || exit();

if (!function_exists('PHOTOCAT_f_log')) {
    function PHOTOCAT_f_log($log_name, $str)
    {
        $log_file = $_SERVER['DOCUMENT_ROOT'] . "/logs/$log_name.log";
        $fp = fopen($log_file, 'a');
        fwrite($fp, "$str\n");
        fclose($fp);
    }
}

?>
