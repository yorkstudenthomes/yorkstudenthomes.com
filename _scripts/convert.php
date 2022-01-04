<?php

echo "Password: ";
system('stty -echo');
$password = trim(fgets(STDIN));
system('stty echo');
// add a new line since the users CR didn't echo
echo "\n";

$db = new mysqli('localhost', 'chris', $password, 'test');
$houses = $db->query('select * from description')->fetch_all(MYSQLI_ASSOC);
$epcs = $db->query('select * from epc')->fetch_all(MYSQLI_ASSOC);
$bills = $db->query('select * from bill')->fetch_all(MYSQLI_ASSOC);
$features = $db->query('select * from feature order by feature_id')->fetch_all(MYSQLI_ASSOC);

foreach ($houses as $house) {
    $epc = current(array_filter($epcs, function ($row) use ($house) {
        return $row['house_id'] == $house['house_id'];
    }));

    $houseBills = array_map(function ($row) {
        return [
            'amount'      => intval($row['room_price']),
            'description' => $row['room_description'],
        ];
    }, array_filter($bills, function ($row) use ($house) {
        return $row['house_id'] == $house['house_id'];
    }));

    $houseFeatures = array_map(function ($row) {
        return $row['feature'];
    }, array_filter($features, function ($row) use ($house) {
        return $row['house_id'] == $house['house_id'];
    }));

    $data = [
        'name'        => str_replace('Street', 'St', $house['house_address']),
        'rented'      => $house['rented'] == 1,
        'position'    => intval($house['house_order']),
        'postcode'    => $house['postcode'],
        'tag'         => utf8_decode($house['type']),
        'description' => utf8_decode($house['description']),
        'epc' => [
            'current'   => ['eer' => intval($epc['eer_current']),   'eir' => intval($epc['eir_current'])],
            'potential' => ['eer' => intval($epc['eer_potential']), 'eir' => intval($epc['eir_potential'])],
        ],
        'bills' => array_values($houseBills),
    ];

    yaml_emit_file('_homes/' . $house['slug'] . '.md', $data);
    file_put_contents('_homes/' . $house['slug'] . '.md', "\n* " . implode("\n* ", $houseFeatures) . "\n", FILE_APPEND);
}

$db->close();
