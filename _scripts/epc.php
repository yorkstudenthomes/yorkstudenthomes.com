<?php

require 'vendor/autoload.php';
require 'jpgraph/jpgraph.php';
require 'jpgraph/jpgraph_canvas.php';
require 'jpgraph/jpgraph_canvtools.php';

use Symfony\Component\Yaml\Yaml;

class Epc {
    protected $colours = [
        'eer' => [
            '#ff0000',
            '#ff6600',
            '#ff9c34',
            '#fecb34',
            '#99cc67',
            '#339967',
            '#669835',
        ],
        'eir' => [
            '#666666',
            '#999999',
            '#cccccc',
            '#0066cb',
            '#3284d6',
            '#65a3e0',
            '#98c2ea',
        ],
    ];

    protected $ratingRanges = [21, 18, 16, 14, 12, 11, 9];

    protected $type;
    protected $current;
    protected $potential;
    protected $thumb = false;

    public function __construct($type, $current, $potential, $thumb) {
        $this->type = $type;
        $this->current = $this->data($current);
        $this->potential = $this->data($potential);
        $this->thumb = $thumb;
    }

    protected function data($score) {
        $bottom = 363;
        $rangeHeight = 31;
        $rangeGap = 7;

        $count = $total = 0;
        foreach ($this->ratingRanges as $ratingRange) {
            if ($score <= ($total + $ratingRange - 1)) {
                $rangeSize = $ratingRange;
                break;
            }

            $count++;
            $total += $ratingRange;
        }

        $ypos = intval($bottom - ((($rangeHeight / $rangeSize) * ($score - $total)) + ($count * ($rangeHeight + $rangeGap))));

        return [
            'score'  => $score,
            'colour' => $this->colours[$this->type][$count],
            'ypos'   => $ypos,
        ];
    }

    protected function output($file) {
        $fullSize = ['h' => 480, 'w' => 448];
        $thumbSize = ['h' => 150, 'w' => 140];
        $size = ($this->thumb ? $thumbSize : $fullSize);

        $graph = new CanvasGraph($size['h'], $size['w'], 'auto');
        $graph->SetBackgroundImage(__DIR__ . '/' . $this->type . '.png', BGIMG_FILLFRAME);
        $graph->InitFrame();

        $scale = new CanvasScale($graph);
        $scale->Set(0, $fullSize['h'], 0, $fullSize['w']);

        $arrow = new Shape($graph, $scale);
        $xpos = ['current' => 318, 'potential' => 401];

        foreach (['current', 'potential'] as $when) {
            $data = $this->$when;
            $arrow->SetColor($data['colour']);

            $arrow->FilledPolygon([
                $xpos[$when] + 73, $data['ypos'] + 15,
                $xpos[$when] + 14, $data['ypos'] + 15,
                $xpos[$when],      $data['ypos'],
                $xpos[$when] + 14, $data['ypos'] - 15,
                $xpos[$when] + 73, $data['ypos'] - 15,
            ]);

            $text = new Text(strval($data['score']));
            $text->SetPos($xpos[$when] + 20, $data['ypos'] - 12);
            $text->SetFont(FF_ARIAL, FS_BOLD, 24);
            $text->SetColor('white');
            $graph->AddText($text);
        }

        $graph->stroke($file);
    }

    protected function path($dir) {
        $filename = implode('_', array_filter([
            $this->current['score'],
            $this->potential['score'],
            ($this->thumb ? 'thumb' : false)
        ])) . '.png';

        return implode('/', [$dir, $this->type, $filename]);
    }

    public function write($dir) {
        $this->output($this->path($dir));
    }
}

foreach (glob('_homes/*.md') as $file) {
    echo "Creating EPC for $file\n";
    $data = Yaml::parse(explode('---', file_get_contents($file))[1]);

    if (!$data) {
        exit("$file: coult not load yaml\n");
    }

    if (!isset($data['epc'])) {
        exit("$file: coult not find epc data\n");
    }

    // todo reverse this in yaml instead?
    $data = [
        'eir' => ['current' => $data['epc']['current']['eir'], 'potential' => $data['epc']['potential']['eir']],
        'eer' => ['current' => $data['epc']['current']['eer'], 'potential' => $data['epc']['potential']['eer']],
    ];

    foreach ($data as $type => $scores) {
        foreach ([true, false] as $thumb) {
            if ($scores['current'] > 0 && $scores['potential'] > 0) {
                printf("Creating %s %s: %d/%d\n", strtoupper($type), $thumb ? 'thumbnail' : 'main', $scores['current'], $scores['potential']);
                $image = new Epc($type, $scores['current'], $scores['potential'], $thumb);
                $image->write(__DIR__ . '/../images/epc');
            }
        }
    }
}
