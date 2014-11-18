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

        if ($this->size == 'thumb') {
            $image->scaleImage(150, 0);
        }

        $response->headers->set('Content-Type', 'image/png');
        $response->setBody($image->getImageBlob());
    }

    protected function drawPolygon($type) {
        $value = $this->$type;
        $color = $this->getColor($value);

        // arrow
        $draw = new \ImagickDraw();
        $draw->setFillColor($color);
        $draw->setStrokeColor($color);
        $draw->polygon($this->getPoints($type, $value));

        // label
        $draw->setStrokeColor('none');
        $draw->setFillColor('white');
        $draw->setFontSize(32);
        $draw->setFont('Arial-Bold');

        $x = ($type == 'current' ? 336 : 419);
        $y = 12 - $this->getOffset($value);
        $draw->annotation($x, $y, $value);

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

    protected function getOffset($value) {
        $sizes = array_keys($this->colors[$this->rating]);
        $rangeHeight = 31;
        $rangeGap = 7;
        $offset = -363; // for 0

        foreach ($sizes as $key => $size) {
            if ($value <= $size) {
                $lastSize = ($key > 0 ? $sizes[$key - 1] : 0);
                return $offset + ($rangeHeight * ($value - $lastSize) / ($size - $lastSize));
            }

            $offset += $rangeHeight + $rangeGap;
        }

        return $offset;
    }

    protected function getPoints($type, $value) {
        // polygon points; (0, 0) is the arrow tip
        $xs = array(0, 15, 73,  73,  15);
        $ys = array(0, 15, 15, -15, -15);

        // offsets
        $xOffset = ($type == 'current' ? 318 : 401);
        $yOffset = $this->getOffset($value);
        $points = array();

        foreach ($xs as $key => $x) {
            $points[] = array('x' => $x + $xOffset, 'y' => $ys[$key] - $yOffset);
        }

        return $points;
    }
}
