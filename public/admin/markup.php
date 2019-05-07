<?php
	if (count($_POST) > 0) {
		include('../../lib/functions.php');

		$ret = array();

		foreach ($_POST as $div_id => $feature) {
			$ret[] = "\t\"$div_id\": \"" . str_replace('"', '\"', markup($feature)) . '"';
		}

		echo "{\n" . implode(",\n", $ret) . "\n}\n";
	} else {
		include('../../lib/header.php');

		session_name('sid');
		session_start();

?>
		<h2>Homes Administration</h2>
		<h3>Feature markup explanation</h3>

		<p><a href="/admin/">Back to administration panel</a></p>

		<p>You can include some simple formatting of the features for a house. The following table gives some examples of how to use these formatting tags.</p>

		<p>Tags are contained in [square brackets] and you <strong>must</strong> include both the opening tag and closing tag for the formatting to work. A tag <tt>[b]</tt> is matched by a closing tag <tt>[/b]</tt> (note the forward slash).</p>

		<p>It is also possible to next tags within each other, but for it to work correctly you <strong>must</strong> close them in the correct order &mdash; inside tags must be closed before the outside tags. For instance <tt>[b]<span style="color: green">[i]text[/i]</span>[/b]</tt> is correct but <tt>[b]<span style="color: red">[i]text</span>[/b]<span style="color: red">[/i]</span></tt> is wrong.</p>

		<table>
			<tr>
				<th>Markup</th>
				<th>Produces</th>
			</tr>
<?php
		$examples = array(
			'[url]http://www.example.com[/url]',
			'[url=http://www.example.com]Example link text[/url]',
			'[b]bold text[/b]',
			'[i]italic text[/i]',
			'[abbr=Abbreviation For Something]AFS[/abbr] (hover over!)',
			'[abbr=Tags Can Be Combined!][url=http://www.example.com][b]TCBC[/b][/url][/abbr]',
		);

		foreach ($examples as $example) {
			echo "\t\t\t\t<tr>\n\t\t\t\t\t<td><tt>$example</tt></td>\n\t\t\t\t\t<td>" . markup($example) . "</td>\n\t\t\t\t</tr>";
		}

		echo "\t\t\t</table>\n";

		include('../../lib/footer.php');
	}
?>