<?php
    error_reporting(E_ALL ^ E_NOTICE); // for debugging
    require '../../lib/functions.php';

    session_name('sid');
    session_start();

    $err_list = '';
    if (isset($_GET['logout'])) {
        $_SESSION = array();
        session_destroy();
    } else {
        $error = array();
        if (isset($_POST['password'])) {
            if (in_array(md5(stripslashes($_POST['password'])), explode(':', getenv('YSH_ADMIN_PASSWORDS')))) {
                $_SESSION['auth'] = true;
            } else {
                $error['password'] = 'The password was incorrect.';
            }
        }

        if (count($error) > 0) {
            $err_list = get_errors($error);
        }

        session_write_close();
    }

    $title = 'Admin';

    require('../../lib/header.php');
?>
            <h2>Homes Administration</h2>
<?php

    if (isset($_SESSION['auth']) && $_SESSION['auth']) {

        $db = db_connect();
        $error = array();

        if (isset($_POST['year'])) {
            $year = intval($_POST['year']);

            if (0 === $year) {
                $error['year'] = 'The year was invalid.';
            }

            if (count($error) == 0) {
                $academic_year = $year;
                $db->query('UPDATE ' . TABLE_SETTING . ' SET `value` = "' . $db->real_escape_string($year) . '" WHERE `setting` = "year"');
            }
        }

        if (isset($_POST['house_id'])) {
            $house_id = intval($_POST['house_id']);
            $is_rented = $_POST['is_rented'];
            $house_type = $db->real_escape_string($_POST['house_type']);
            $house_desc = $db->real_escape_string($_POST['house_desc']);

            if (empty($house_type)) {
                $error['house_type'] = 'The property must have a tag line.';
            }

            if (empty($house_desc)) {
                $error['house_desc'] = 'The property must have a description.';
            }

            $bill_values = array();
            $bills = $_POST['bill'];
            foreach ($_POST['bill'] as $bill_id => $bill) {
                if (!preg_match('/\d+(?:-\d+)?/', $bill['price'])) {
                    $error['bill'][$bill_id]['price'] = 'The price is in the wrong format.';
                } else {
                    $bill_values[] = '(' . intval($bill_id) . ", $house_id, '" . $db->real_escape_string($bill['price']) . "', '" . $db->real_escape_string($bill['desc']) . "')";
                }
            }

            $epc = array();
            foreach ($_POST['epc'] as $type => $value) {
                $epc[$type] = (int)$value;
                if ($epc[$type] < 0 || $epc[$type] > 100) { $error['epc'][$type] = 'EPC values must be a percentage value, between 0 and 100.'; }
            }

            $feature_values = array();
            $features = $_POST['feature'];
            foreach ($features as $feature_id => $feature_text) {
                $feature_values[] = '(' . intval($feature_id) . ", $house_id, '" . $db->real_escape_string(trim($feature_text)) . "')";
            }

            if (count($error) == 0) {
                $db->query("UPDATE " . TABLE_DESC . " SET `type` = '" . $house_type . "', `description` = '" . $house_desc . "', `rented` = $is_rented WHERE `house_id` = $house_id");
                $db->query("DELETE FROM " . TABLE_BILL . " WHERE `house_id` = $house_id");
                $db->query("INSERT INTO " . TABLE_BILL . " (`bill_id`, `house_id`, `room_price`, `room_description`) VALUES " . implode(', ', $bill_values));
                $db->query("DELETE FROM " . TABLE_FEATURE . " WHERE `house_id` = $house_id");
                $db->query("INSERT INTO " . TABLE_FEATURE . " (`feature_id`, `house_id`, `feature`) VALUES " . implode(', ', $feature_values));
                $db->query("UPDATE " . TABLE_EPC . " SET `eer_current` = " . $_POST['epc']['ec'] . ", `eer_potential` = " . $_POST['epc']['ep'] . ", `eir_current` = " . $_POST['epc']['ic'] . ", `eir_potential` = " . $_POST['epc']['ip'] . " WHERE `house_id` = $house_id");
            }
        }

        if (isset($_REQUEST['house_id']) && is_numeric($_REQUEST['house_id'])) {
            $house_id = intval($_REQUEST['house_id']);
            $tables = array();

            $results = $db->query("SHOW TABLE STATUS LIKE '%'");
            while ($row = $results->fetch_assoc()) {
                $tables[$row['Name']] = $row;
            }

            $house_results = $db->query("SELECT `house_address`, `rented`, `type`, `description` FROM " . TABLE_DESC . " WHERE `house_id` = $house_id");
            $house = $house_results->fetch_assoc();
            echo "\t\t\t<h3>" . $house['house_address'] . "</h3>\n";
            $is_rented = $house['rented'];
            $house_type = $house['type'];
            $house_desc = $house['description'];

            if (isset($_GET['house_id'])) {
                $bills = $features = array();

                $bill_results = $db->query("SELECT `bill_id`, `room_price`, `room_description` FROM " . TABLE_BILL . " WHERE `house_id` = $house_id");
                while ($row = $bill_results->fetch_assoc()) {
                    $bills[$row['bill_id']] = array('price' => $row['room_price'], 'desc' => $row['room_description']);
                }

                $feature_results = $db->query("SELECT `feature_id`, `feature` FROM " . TABLE_FEATURE . " WHERE `house_id` = $house_id ORDER BY `feature_id`");
                while ($row = $feature_results->fetch_assoc()) {
                    $features[$row['feature_id']] = $row['feature'];
                }

                $epc_results = $db->query("SELECT `eer_current`, `eer_potential`, `eir_current`, `eir_potential` FROM " . TABLE_EPC . " WHERE `house_id` = $house_id");
                foreach ($epc_results->fetch_assoc() as $field_name => $field) {
                    $epc[$field_name[1] . $field_name[4]] = $field;
                }
            }

            echo "\t\t\t<p><a href=\"/admin/\">Back to homes administration</a></p>\n";

            if (count($error) == 0 && isset($_POST['house_id'])) {
                echo "\t\t\t<div class=\"success\">\n\t\t\t\t<h4>Details Saved</h4>\n\t\t\t\t<p>The details have been updated.</p>\n\t\t\t</div>\n";
            }
?>
            <form id="details" method="post" action="/admin/" enctype="multipart/form-data">
                <fieldset>
                    <legend>Property Details</legend>
<?php
            if (count($error) > 0) {
                $err_list = get_errors($error);
                echo $err_list;
            }

            echo "\t\t\t\t\t<div id=\"rented\"" . ($is_rented ? ' class="info"' : '') . ">\n\t\t\t\t\t\t";
            if ($is_rented) {
                echo '<h4>Property is rented</h4><p>This property is marked as rented.</p> <input id="set_available" type="submit" value="Make available again" />';
            } else {
                echo '<input id="set_rented" type="button" value="Mark this property as rented" />';
            }
            echo "\n\t\t\t\t\t</div>\n";
?>
                    <input type="hidden" name="house_id" value="<?php echo $house_id; ?>" />
                    <input type="hidden" id="is_rented" name="is_rented" value="<?php echo ($is_rented ? 1 : 0); ?>" />

                    <fieldset id="description">
                        <legend>Description</legend>
                        <em>Enter the tagline and main description or the property.</em>

                        <textarea id="house_type" name="house_type" rows="6" cols="35"<?php if (isset($error['house_type'])) { echo ' class="error"'; } ?>><?php echo $house_type; ?></textarea>
                        <span class="feature" id="preview_house_type"><?php echo markup($house_type); ?></span><br />

                        <textarea id="house_desc" name="house_desc" rows="6" cols="35"<?php if (isset($error['house_desc'])) { echo ' class="error"'; } ?>><?php echo $house_desc; ?></textarea>
                        <span class="feature" id="preview_house_desc"><?php echo markup($house_desc); ?></span>
                    </fieldset>

                    <fieldset id="epc" class="hidden">
                        <legend>EPC Details</legend>
                        <em>Enter the EPC details for the property here.</em>

                        <label for="ec">EER Current</label>
                        <input id="ec" name="epc[ec]" value="<?php echo $epc['ec']; ?>" />

                        <label for="ep">EER Potential</label>
                        <input id="ep" name="epc[ep]" value="<?php echo $epc['ep']; ?>" /><br />

                        <label for="ic">EIR Current</label>
                        <input id="ic" name="epc[ic]" value="<?php echo $epc['ic']; ?>" />

                        <label for="ip">EER Potential</label>
                        <input id="ip" name="epc[ip]" value="<?php echo $epc['ip']; ?>" />
                    </fieldset>

                    <fieldset id="bills">
                        <legend>Bills</legend>
                        <em>Leave the text blank to simply show that price. Enter a text description to describe more than one bill for a particular house, e.g. &ldquo;Room with en-suite&rdquo;. For a range of prices, enter the prices separated by a hyphen, e.g. &ldquo;55-65&rdquo;.</em>

                        <input type="hidden" name="next_bill" id="next_bill" value="<?php echo $tables['bill']['Auto_increment']; ?>" />
<?php
            $display_remove_link = false;
            foreach ($bills as $bill_id => $bill) {

                echo "<span class=\"billrow\"><label class=\"forprice\" for=\"bill_{$bill_id}_price\">&pound;</label><input id=\"bill_{$bill_id}_price\" class=\"bill_price" . (isset($error['bill'][$bill_id]['price']) ? ' error' : '') . "\" type=\"text\" name=\"bill[$bill_id][price]\" value=\"{$bill['price']}\" /> <label class=\"bill_desc\" for=\"bill_{$bill_id}_desc\">Description</label><input id=\"bill_{$bill_id}_desc\" type=\"text\" name=\"bill[$bill_id][desc]\" value=\"{$bill['desc']}\" />" , ($display_remove_link ? "<a class=\"remove_link\" href=\"#\" onclick=\"return removeRow(this)\">remove</a>" : '') , "<br /></span>\n";
                $display_remove_link = true;
            }
?>
                        <a id="bill_link" href="#">add</a>
                    </fieldset>

                    <fieldset>
                        <legend>Features</legend>
                        <em>Write the feature then click &ldquo;Preview features&rdquo; to see the result on the right-hand side. Also have a look at the <a href="/admin/markup.php">available markup</a>.</em>

                        <input type="hidden" name="next_feature" id="next_feature" value="<?php echo $tables['feature']['Auto_increment']; ?>" />
<?php
            $display_remove_link = false;
            foreach ($features as $feature_id => $feature) {
                echo "<span class=\"featurerow\"><textarea rows=\"4\" cols=\"35\" name=\"feature[$feature_id]\">" , htmlentities($feature) , "</textarea> <span class=\"feature\" id=\"preview_$feature_id\">" , markup($feature) , "</span>" , ($display_remove_link ? "<a class=\"remove_link\" href=\"#\" onclick=\"return removeRow(this)\">remove</a>" : '' ) , "<br /></span>\n";
                $display_remove_link = true;
            }
            echo "<p><a id=\"feature_link\" href=\"#\">add</a></p><input disabled=\"disabled\" id=\"preview\" type=\"button\" value=\"Preview features\" /></fieldset><input id=\"save\" type=\"submit\" value=\"Save all data\" /><br /><input id=\"reset_form\" type=\"reset\" value=\"Reset data\" /></fieldset>\n</form>\n";
        } else {
?>
            <form method="post" action="/admin/" enctype="multipart/form-data">
                <fieldset>
                    <legend>Site settings</legend>

                    <?php
                    if (count($error) > 0) {
                        $err_list = get_errors($error);
                        echo $err_list;
                    }
                    ?>

                    <label for="year">Academic year</label>
                    <input id="year" name="year" type="text" value="<?php echo $academic_year; ?>" /><br />

                    <label for="submit"></label>
                    <input type="submit" name="submit" value="Save" id="submit" /><br />
                </fieldset>
            </form>
<?php
            echo "<ul>\n";

            $results = $db->query("SELECT `house_id`, `house_address` FROM " . TABLE_DESC);
            while ($row = $results->fetch_assoc()) {
                echo "<li><a href=\"/admin/?house_id=" . $row['house_id'] . '">' . $row['house_address'] . "</a></li>\n";
            }
            echo "</ul>\n<p><a href=\"http://mail.yorkstudenthomes.com/\">Go to Mailbox</a><br /><a href=\"http://calendar.yorkstudenthomes.com/\">Go to Calendar</a></p>\n";
        }
    } else {
?>
                <form method="post" action="/admin/" enctype="multipart/form-data">
                    <fieldset id="login">
                        <legend>Please enter your password</legend>

                        <?php echo $err_list; ?>

                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" value="" /><br />

                        <label for="submit"></label>
                        <input type="submit" name="submit" value="Log in" id="submit" /><br />
                    </fieldset>
                </form>

<?php
    }

    require('../../lib/footer.php');
?>
