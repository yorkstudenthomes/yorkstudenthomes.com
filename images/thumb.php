<?php
    //if (preg_match('/\//', substr($_SERVER['PATH_INFO'], 1))) { die('Only Local!'); }

    header('Content-Type: image/png');

    /*if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/images' . $_SERVER['PATH_INFO'])) {
        $src_img = imagecreatefrompng('notfound.png');
        imagepng($src_img);
        imagedestroy($src_img);
        exit();
    }*/

    createthumb('http://yorkstudenthomes.com/images' . $_SERVER['PATH_INFO'], 150, 150);

    function createthumb($name, $new_w, $new_h) {
        $src_img = imagecreatefrompng($name);
        $old_x = imageSX($src_img);
        $old_y = imageSY($src_img);
        if ($old_x > $old_y) {
            $thumb_w = $new_w;
            $thumb_h = $old_y * ($new_h / $old_x);
        }
        if ($old_x < $old_y) {
            $thumb_w = $old_x * ($new_w / $old_y);
            $thumb_h = $new_h;
        }
        if ($old_x == $old_y) {
            $thumb_w = $new_w;
            $thumb_h = $new_h;
        }
        $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
        imagepng($dst_img);
        imagedestroy($dst_img);
        imagedestroy($src_img);
    }
?>
