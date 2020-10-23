<?php
    require('../lib/header.php');

    if (!$db = db_connect()) {
        echo "\t\t\t\t<div class=\"error\">\n\t\t\t\t\t<h2>Sorry!</h2>\n\t\t\t\t\t<p>There seems to have been a problem with the site. We&rsquo;re looking into it and it should be fixed soon.</p>\n\t\t\t\t</div>\n\n";
    } else {
        $result = $db->query("SELECT COUNT(`rented`) AS `numAvailable` FROM " . TABLE_DESC . " WHERE `rented` = 0");

        if ($result && !$result->fetch_object()->numAvailable) {
            echo "\t\t\t\t<div class=\"info\">\n\t\t\t\t\t<h2>All houses unavailable</h2>\n\t\t\t\t\t<p>All of our houses have been taken for this year.</p>\n\t\t\t\t</div>\n\n";
        }
    }
?>
                <h2>York Student Homes &mdash; <?php echo $academic_year_range; ?> academic year</h2>

                <p>If you are looking for a house to live in, with your friends, while you are studying at York University or York St John in York then you&rsquo;ve just found a great place to find one!</p>

                <p>We&rsquo;re a family business and have been renting houses to university students in York for more than 20 years. We have houses for groups of 4, 5 and 6 in various areas of York to suit your needs!</p>

                <p>Take a look at our web site and just give us a call, or email us, if you wish to view any or all of our properties!</p>

                <p>We rent the houses by the <strong>academic year</strong> (beginning of July to end of June the following year). All of our houses are well-maintained and economical to run.</p>

                <h2>Accommodation for Students</h2>

                <p>We are now listed on <a href="/accommodation-for-students/">Accommodation for Students</a>, the number 1 student accommodation search engine.</p>
<?php
    require('../lib/footer.php');
?>
