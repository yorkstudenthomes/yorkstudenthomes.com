<?php
    error_reporting(E_ALL);
    include '../jpgraph/src/jpgraph.php';
    include '../jpgraph/src/jpgraph_canvas.php';
    include '../jpgraph/src/jpgraph_canvtools.php';

    $colours = array(
        'ee' => array('#ff0000', '#ff6600', '#ff9c34', '#fecb34', '#99cc67', '#339967', '#669835'),
        'ei' => array('#666666', '#999999', '#cccccc', '#0066cb', '#3284d6', '#65a3e0', '#98c2ea')
    );
    $rating_ranges = array(21, 18, 16, 14, 12, 11, 9);
    $bottom = 363;
    $range_height = 31;
    $range_gap = 7;

    function shift($type, $pc) {
        global $colours;
        global $rating_ranges;
        $bottom = 363;
        $range_height = 31;
        $range_gap = 7;

        $count = $total = 0;
        foreach ($rating_ranges as $key => $rating_range) {
            if ($pc <= ($total + $rating_range - 1)) {
                $range_size = $rating_range;
                break;
            }
            $count++;
            $total += $rating_range;
        }

        return array($colours[$type][$count], intval($bottom - ((($range_height / $range_size) * ($pc - $total)) + ($count * ($range_height + $range_gap)))));
    }

    $values = array('ee', 0, 0);
    $size = array(480, 448);
    if (isset($_SERVER['PATH_INFO'])) {
        $path_values = explode('/', substr($_SERVER['PATH_INFO'], 1));
        if (count($path_values) >= 3) {
            if (in_array(strtolower($path_values[0]), array_keys($colours))) { $values[0] = strtolower($path_values[0]); }
            if (is_numeric($path_values[1]) && $path_values[1] >= 0 && $path_values[1] <= 100) { $values[1] = $path_values[1]; }
            if (is_numeric($path_values[2]) && $path_values[2] >= 0 && $path_values[2] <= 100) { $values[2] = $path_values[2]; }
            if (isset($path_values[3]) && $path_values[3] == 'thumb') { $size = array(150, 140); }
        }
    }
    list($type, $c, $p) = $values;
    list($c_colour, $c_shift) = shift($type, $c);
    list($p_colour, $p_shift) = shift($type, $p);

    $graph = new CanvasGraph($size[0], $size[1], 'auto');
    $graph->SetBackgroundImage("$type.png", BGIMG_FILLFRAME);
    $graph->InitFrame();

    $scale = new CanvasScale($graph);
    $scale->Set(0, 480, 0, 448);

    $shape = new Shape($graph, $scale);

    $shape->SetColor($c_colour);
    $shape->FilledPolygon(array(391, $c_shift + 15, 333, $c_shift + 15, 318, $c_shift, 333, $c_shift - 15, 391, $c_shift - 15));

    $shape->SetColor($p_colour);
    $shape->FilledPolygon(array(474, $p_shift + 15, 416, $p_shift + 15, 401, $p_shift, 416, $p_shift - 15, 474, $p_shift - 15));

    $c_text = new Text(strval($c));
    $c_text->SetPos(338, $c_shift - 12);
    $c_text->SetFont(FF_ARIAL, FS_BOLD, 24);
    $c_text->SetColor('white');
    $graph->AddText($c_text);

    $p_text = new Text(strval($p));
    $p_text->SetPos(421, $p_shift - 12);
    $p_text->SetFont(FF_ARIAL, FS_BOLD, 24);
    $p_text->SetColor('white');
    $graph->AddText($p_text);

    $graph->stroke();
?>
