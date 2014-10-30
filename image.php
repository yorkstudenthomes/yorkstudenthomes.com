<?php
    require('.include/header.php');

    if (strpos($_SERVER['PATH_INFO'], 'epc.php') !== false || is_file($_SERVER['DOCUMENT_ROOT'] . '/images' . $_SERVER['PATH_INFO'])) {

        $images = array_values(array_filter(image_list(dirname($_SERVER['DOCUMENT_ROOT'] . '/images/' . $_SERVER['PATH_INFO']) . '/'), 'thumbnail_filter'));
        $key = array_search(basename($_SERVER['PATH_INFO']), $images);
        $prev_img = $images[$key - 1];
        $next_img = $images[$key + 1];

        if (!empty($prev_img)) {
            echo "<span style=\"float: left\"><a href=\"$prev_img\">&laquo; Previous image</a></span>";
        }

        if (!empty($next_img)) {
            echo "<span style=\"float: right\"><a href=\"$next_img\">Next image &raquo;</a></span>";
        }

        $title = (strpos($_SERVER['PATH_INFO'], 'epc.php') !== false ? 'EPC Graph' : 'Image from ' . house_name(substr(dirname($_SERVER['PATH_INFO']), 1)));
        echo "\n\t\t\t<div id=\"img\">\n\t\t\t\t<h2>$title</h2>\n\t\t\t\t<img src=\"/images" . $_SERVER['PATH_INFO'] . "\" alt=\"house image\" />\n\t\t\t</div>\n";

        require('.include/footer.php');
    } else {
        header("Location: /");
    }
?>
