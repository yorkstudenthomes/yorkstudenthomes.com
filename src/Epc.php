<?php

namespace Starsquare;

class Epc {

    protected $rating;
    protected $current;
    protected $potential;
    protected $size;

    protected $colors = array(
        'ee' => array(
            20  => '#ff0000',
            38  => '#ff6600',
            54  => '#ff9c34',
            68  => '#fecb34',
            80  => '#99cc67',
            91  => '#339967',
            100 => '#669835',
        ),
        'ei' => array(
            20  => '#666666',
            38  => '#999999',
            54  => '#cccccc',
            68  => '#0066cb',
            80  => '#3284d6',
            91  => '#65a3e0',
            100 => '#98c2ea',
        ),
    );

    public function __construct($rating, $current, $potential, $size = 'regular') {
        $this->rating = substr($rating, 0, 2);
        $this->size = $size;

        $this->current = $current;
        $this->potential = $potential;
    }

    public function draw($response) {
        $image = new \Imagick("images/$this->rating.png");

        $image->drawImage($this->drawPolygon('current'));
        $image->drawImage($this->drawPolygon('potential'));

        $response->headers->set('Content-Type', 'image/png');
        $response->setBody($image->getImageBlob());
    }

    protected function drawPolygon($type) {
        $value = $this->$type;
        $color = $this->getColor($value);

        $draw = new \ImagickDraw();
        $draw->setFillColor($color);
        $draw->setStrokeColor($color);
        $draw->polygon($this->getPoints($type, $value));

        return $draw;
    }

    protected function getColor($value) {
        foreach ($this->colors[$this->rating] as $threshold => $color) {
            if ($value <= $threshold) {
                return $color;
            }
        }

        return 'black';
    }

    protected function getPoints($type, $value) {
        // polygon points; (0, 0) is the arrow tip
        $xs = array(0, 15, 73,  73,  15);
        $ys = array(0, 15, 15, -15, -15);

        // offsets
        $xOffset = ($type == 'current' ? 318 : 401);
        $yOffset = -363; // for 0

        // rating range sizes (G-A) (todo use color ranges)
        $sizes = array(20, 18, 16, 14, 12, 11, 9);
        $rangeHeight = 31;
        $rangeGap = 7;
        $total = 0;

        foreach ($sizes as $size) {
            if ($value <= $total + $size) {
                $yOffset += ($rangeHeight * ($value - $total) / $size);
                break;
            } else {
                $yOffset += $rangeHeight + $rangeGap;
            }

            $total += $size;
        }

        $points = array();

        foreach ($xs as $key => $x) {
            $points[] = array('x' => $x + $xOffset, 'y' => $ys[$key] - $yOffset);
        }

        return $points;
    }
}
