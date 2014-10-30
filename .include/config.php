<?php
    error_reporting(E_ALL);

    if (strpos($_SERVER['SCRIPT_FILENAME'], 'homes') !== false) {
        $house_path = basename(dirname($_SERVER['SCRIPT_FILENAME']));
        $email_address = $house_path . '@yorkstudenthomes.com';
    }

    include_once('functions.php');

    $date_start = 2008;

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
