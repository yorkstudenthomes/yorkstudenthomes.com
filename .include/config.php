<?php
    if (empty($debug)) { $debug = false; }
    error_reporting($debug ? E_ALL : 0);

    $prefix_folder = '';
    $prefix = "http://yorkstudenthomes.com{$prefix_folder}";

    /*header("Vary: Accept");
    $content_type = (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') ? 'application/xhtml+xml' : 'text/html');
    header("Content-Type: $content_type; charset=utf-8");*/

    if (strpos($_SERVER['SCRIPT_FILENAME'], 'homes') !== false) {
        $house_path = basename(dirname($_SERVER['SCRIPT_FILENAME']));
        $email_address = $house_path . '@yorkstudenthomes.com';
    }

    include_once('functions.php');

    $date_start = 2008;

    define('YSH_HOST', '');
    define('YSH_USER', '');
    define('YSH_PASS', '');
    define('YSH_DB',   '');

    define('TABLE_PREFIX',  '');
    define('TABLE_BILL',    '`' . TABLE_PREFIX . 'bill`');
    define('TABLE_DESC',    '`' . TABLE_PREFIX . 'description`');
    define('TABLE_EPC',     '`' . TABLE_PREFIX . 'epc`');
    define('TABLE_FEATURE', '`' . TABLE_PREFIX . 'feature`');
    define('TABLE_SETTING', '`' . TABLE_PREFIX . 'setting`');

    $academic_year = get_setting('year');

    $company_name = 'York Student Homes';
    $copyright = "$company_name $date_start" . (date('Y') > $date_start ? '&ndash;' . date('Y') : '') . '. All rights reserved.';
    $academic_year_range = sprintf('%s&ndash;%s', $academic_year, $academic_year + 1);
?>
