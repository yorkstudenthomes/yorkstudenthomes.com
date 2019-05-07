<?php
    include_once('config.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="en" />
        <meta http-equiv="last-modified" content="<?php echo gmdate('D, d M Y H:i:s', filemtime($_SERVER['SCRIPT_FILENAME'])) ?> GMT" />
<?php
    if (isset($house_address) || '/index.php' === $_SERVER['SCRIPT_NAME']) {
?>
    <meta name="description" content="<?php echo (isset($house_address) ? $house_address . ': ' . $house['description'] . ' ' : '') . $company_name; ?> - <?php echo cms('meta_desc'); ?>" />
<?php
    }
?>
        <meta name="keywords" content="<?php echo cms('meta_keywords'); ?>" />
        <meta name="copyright" content="<?php echo $copyright; ?>" />
        <title><?php echo (empty($title) ? '' : "$title — ") . $company_name; ?></title>
        <script type="text/javascript" src="/js/ga.js"></script>
<?php
    if (strpos($_SERVER['SCRIPT_NAME'], 'admin') !== false) {
        echo "\t\t<script type=\"text/javascript\" src=\"/js/admin.js\"></script>\n";
    }
?>
        <link rel="stylesheet" title="Default Stylesheet" type="text/css" href="/css/default.css" />
        <!--[if IE]>
        <link rel="stylesheet" type="text/css" href="/css/ie.css" />
        <![endif]-->
    </head>
    <body>
        <div id="header">
        <h1><a href="/"><?php echo $company_name; ?></a></h1>
        <p><?php echo cms('tagline'); ?></p>
        </div>

        <div id="nav">
            <div id="navright">
                <ul>
<?php
    $links = array(
        '/homes/22-kyme-st/'           => '22 Kyme St',
        '/homes/74-eldon-st/'           => '74 Eldon St',
        '/homes/54-trafalgar-st/'       => '54 Trafalgar St',
        '/homes/25-lamel-st/'           => '25 Lamel St',
        '/homes/23-moss-st/'           => '23 Moss St',
        '/homes/52-moss-st/'           => '52 Moss St',
        '/homes/88-queen-victoria-st/' => '88 Queen Victoria St',
        '/maps/'                       => 'Maps',
        '/contact/'                       => 'Contact'
    );

    foreach ($links as $url => $link_title) {
        $location = dirname($_SERVER['SCRIPT_FILENAME']) . '/';
        $class = '';

        if ('image.php' === basename($_SERVER['SCRIPT_FILENAME'])) {
            $location .= 'homes' . dirname($_SERVER['PATH_INFO']) . '/';
            $class = ' class="link"';
        }

        $match = (strpos($location, $_SERVER['DOCUMENT_ROOT'] . $url) === 0);

        echo "\t\t\t\t\t<li" , ($match ? ' id="selected"' . $class : '') , '><a href="' , $url , '"><b>' , $link_title , "</b></a></li>\n";
    }
?>
                </ul>
            </div>
        </div>

        <hr />

        <div id="banner">
            <div id="bannerright">
                <div id="bannerimg">&nbsp;</div>
            </div>
        </div>

        <div id="main">
