<?php
    require('.include/header.php');

    if (strpos($_SERVER['PATH_INFO'], 'epc.php') !== false || is_file($_SERVER['DOCUMENT_ROOT'] . '/images' . $_SERVER['PATH_INFO'])) {

        $images = array_values(array_filter(image_list(dirname($_SERVER['DOCUMENT_ROOT'] . '/images/' . $_SERVER['PATH_INFO']) . '/'), 'thumbnail_filter'));
        $key = array_search(basename($_SERVER['PATH_INFO']), $images);

        if (count($images)) {
            if (isset($images[$key - 1])) {
                echo '<span style="float: left"><a href="' . $images[$key - 1] . '">&laquo; Previous image</a></span>';
            }

            if (isset($images[$key + 1])) {
                echo '<span style="float: right"><a href="' . $images[$key + 1] . '">Next image &raquo;</a></span>';
            }
        }

        $title = (strpos($_SERVER['PATH_INFO'], 'epc.php') !== false ? 'EPC Graph' : 'Image from ' . house_name(substr(dirname($_SERVER['PATH_INFO']), 1)));
        echo "\n\t\t\t<div id=\"img\">\n\t\t\t\t<h2>$title</h2>\n\t\t\t\t<img src=\"/images" . $_SERVER['PATH_INFO'] . "\" alt=\"house image\" />\n\t\t\t</div>\n";

        require('.include/footer.php');
    } else {
        header("Location: /");
    }
?>
