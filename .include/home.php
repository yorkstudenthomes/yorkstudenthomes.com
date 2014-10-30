<?php
	require('config.php');
	$title = $house_address = house_name($house_path);
	if (!$db = db_connect()) {
		return;
	}

	$results = $db->query("SELECT " . TABLE_DESC . ".`house_id`, `rented`, `type`, `description`, `feature` FROM " . TABLE_DESC . " INNER JOIN " . TABLE_FEATURE . " USING(`house_id`) WHERE `house_address` = '$house_address' ORDER BY `feature_id`");

	$features = array();
	while ($row = $results->fetch_assoc()) {
		$house = array('id' => $row['house_id'], 'type' => $row['type'], 'description' => $row['description'], 'is_rented' => $row['rented']);
		$features[] = $row['feature'];
	}

	$bill_results = $db->query("SELECT `room_price`, `room_description` FROM " . TABLE_BILL . " WHERE `house_id` = {$house['id']} ORDER BY `room_price` ASC");

	$bills = array();
	while ($row = $bill_results->fetch_assoc()) {
		if (empty($row['room_description'])) {
			$bills[] = str_replace('-', '&ndash;', $row['room_price']);
		} else {
			$bills[$row['room_price']] = $row['room_description'];
		}
	}

	$epc_results = $db->query("SELECT `eer_current`, `eer_potential`, `eir_current`, `eir_potential` FROM " . TABLE_EPC . " WHERE `house_id` = {$house['id']}");
	$epc = $epc_results->fetch_assoc();

	require('header.php');

	echo "\n\t\t\t<div id=\"left\">\n\t\t\t\t<h2>$house_address</h2>\n\t\t\t\t<h3>{$house['type']}</h3>\n\n";
	if ($house['is_rented']) { echo "\t\t\t\t<div class=\"info\">\n\t\t\t\t\t<h3>House unavailable</h3>\n\t\t\t\t\t<p>This house has already been rented out for this year. Please have a look at some of our other houses!</p>\n\t\t\t\t</div>\n"; }
	echo "\t\t\t\t<p id=\"desc\">{$house['description']}</p>\n\n";

	foreach ($bills as $bill_key => $bill_value) {
		if (preg_match('/\d+(?:&ndash;\d+)?/', $bill_value)) {
			echo "\t\t\t\t<strong class=\"price\">&pound;$bill_value per room per week (plus bills)</strong>\n";
		} else {
			echo "\t\t\t\t<strong class=\"price\">&pound;$bill_key per room per week (plus bills) &mdash; $bill_value</strong>\n";
		}
	}

	echo "\n\t\t\t\t<ul>\n";

	foreach ($features as $feature) {
		echo "\t\t\t\t\t<li>" . markup($feature) . "</li>\n";
	}

	echo "\t\t\t\t</ul>\n\n";

	if (!$house['is_rented']) { echo "\t\t\t\t<p id=\"email\">Email for details about this house: <a href=\"mailto:$email_address\">$email_address</a></p>\n\n"; }

	if (is_file($_SERVER['DOCUMENT_ROOT'] . '/homes/' . $house_path . '/plans.pdf')) {
		echo "<p><a rel=\"external\" href=\"/homes/$house_path/plans.pdf\">View house plans</a> (PDF)</p>\n";
	}

	echo "\t\t\t</div>\n\t\t\t<div id=\"right\">\n";

	foreach (image_list($_SERVER['DOCUMENT_ROOT'] . '/images/' . $house_path . '/') as $image) {
		if (strpos($image, 'thumb') === false && strpos($image, 'pdf') === false) {
			echo "\t\t\t\t<a href=\"/image.php/$house_path/$image\"><img src=\"/images/$house_path/"  . substr($image, 0, -4) . '_thumb' . substr($image, -4) . "\" alt=\"Image of " . house_name($house_path) . "\" /></a>\n";
		}
	}

	foreach (array('e', 'i') as $type) {
		if ($epc["e{$type}r_current"] > 0 && $epc["e{$type}r_potential"] > 0) {
			echo "\t\t\t\t<a href=\"/image.php/epc.php/e{$type}/" , $epc["e{$type}r_current"] , '/' , $epc["e{$type}r_potential"] , "\"><img src=\"/images/epc.php/e{$type}/" , $epc["e{$type}r_current"] , '/' , $epc["e{$type}r_potential"] , "/thumb\" alt=\"E" . strtoupper($type) . "R Graph of " . house_name($house_path) . "\" /></a>\n";
		}
	}

	echo "\t\t\t</div>\n";

	require('footer.php');
?>
