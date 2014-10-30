<?php
    function image_list($dir) {
        $il = array();
        if (is_dir($dir)) {
            $hi = opendir($dir);

            while ($sz = readdir($hi)) {
                if ($sz[0] != '.' && is_file($dir . $sz)) {
                    $il[] = $sz;
                }
            }
            closedir($hi);
        }
        sort($il);
        return $il;
    }

    function thumbnail_filter($img) {
        return stripos($img, 'thumb') === false;
    }

    function house_name($house_path) {
        return ucwords(str_replace(array('-', 'st'), array(' ', 'street'), $house_path));
    }

    function errors_only($errors) {
        $ret = array();
        foreach ($errors as $error) {
            $ret += (is_array($error) ? errors_only($error) : array($error));
        }
        return array_unique($ret);
    }

    function get_errors($error) {
        $output = "<div class=\"error\"><strong>There were errors with your details:</strong>\n<ul>\n";

        foreach (errors_only($error) as $message) {
            $output .= "<li>$message</li>\n";
        }

        return $output . "</ul>\n</div>\n";
    }

    function myhtmlentities($str) {
        return htmlentities($str, ENT_NOQUOTES, 'UTF-8');
    }

    function absolute_url($str) {
        // Doesn't yet handle ../ or ./
        if ($str == '' || $str == null) { return false; }
        if (substr($str, 0, 7) == 'http://' || substr($str, 0, 8) == 'https://' || substr($str, 0, 6) == 'ftp://') { return $str; }
        if ($str{0} != '/') { $str = dirname($_SERVER['SCRIPT_NAME']) . '/' . $str; }
        if (strpos($str, $_SERVER['HTTP_HOST']) === false) { $str = $_SERVER['HTTP_HOST'] . $str; }
        if (strpos($str, '://') === false) { $str = 'http://' . $str; }
        return $str;
    }

    function replace_quotes($curl) {
        $replace = array(
            '---' => '&mdash;',
            '--'  => '&ndash;',
            '``'  => '&ldquo;',
            "`'"  => '&quot;',

            "'tain't" => '&rsquo;tain&rsquo;t',
            "'twere"  => '&rsquo;twere',
            "'twas"   => '&rsquo;twas',
            "'tis"    => '&rsquo;tis',
            "'twill"  => '&rsquo;twill',
            "'til"    => '&rsquo;til',
            "'bout"   => '&rsquo;bout',
            "'nuff"   => '&rsquo;nuff',
            "'round"  => '&rsquo;round',
            "'em"     => '&rsquo;em'
        );

        $curl = str_replace(array_keys($replace), array_values($replace), $curl);

        $preg = array(
            "/'s/"                                 => "&rsquo;s",
            "/'(\d\d(?:&rsquo;|')?s)/"             => "&rsquo;$1",
            '/(\s|\A|")\'/'                        => '$1&lsquo;',
            "/(\d+)\"/"                            => "$1&Prime;",
            "/(\d+)'/"                             => "$1&prime;",
            "/(\S)'([^'\s])/"                      => "$1&rsquo;$2",
            '/"([\s.!])/'                         => '&rdquo;$1',
            '/(\S)"/'                              => '$1&rdquo;',
            '/(\s|\A)"/'                           => '$1&ldquo;',
            "/'([\s.])/"                           => '&rsquo;$1',
            "/\(tm\)/i"                            => '&trade;',
            "/\(c\)/i"                            => '&copy;',
            "/\(r\)/i"                            => '&reg;',
            '/\.{3,}/'                            => '&hellip;',
            "/''/"                                => '&rdquo;',
            '/&amp;(#\d{2,4};)/'                => '&$1'
        );

        return preg_replace(array_keys($preg), array_values($preg), $curl);
    }

    function markup($message) {
        $preg = array(
            '/\[b(?::\w+)?\](.*?)\[\/b(?::\w+)?\]/si'                => "<strong>$1</strong>",
            '/\[i(?::\w+)?\](.*?)\[\/i(?::\w+)?\]/si'                => "<em>$1</em>",
            '/\[abbr=(.*?)(?::\w+)?\](.*?)\[\/abbr(?::\w+)?\]/si'    => "<acronym title=\"$1\">$2</acronym>",
            '/\[url(?::\w+)?\]www\.(.*?)\[\/url(?::\w+)?\]/si'        => "<a class=\"plainlinks\" href=\"http://www.$1\">$1</a>",
            '/\[url(?::\w+)?\](.*?)\[\/url(?::\w+)?\]/si'            => "<a href=\"$1\">$1</a>",
            '/([0-9]+)\h*-{1,2}\h*([0-9]+)/'                        => '$1&ndash;$2',
            '/ - /si'                                                => '&nbsp;&mdash; ',
            '/(\d+\s*\)?\s*)x(\s*\(?\s*\d+)/'                        => '$1&times;$2'
            // '/(\d+\s*\)?\s*)&mdash;(\s*\(?\s*\d+)/'                => '$1&minus;$2'
        );

        $message = preg_replace(array_keys($preg), array_values($preg), nl2br(myhtmlentities($message)));
        $message = preg_replace_callback('/\[url=(.*?)(?::\w+)?\](.*?)\[\/url(?::\w+)?\]/si', function ($matches) {
            return '<a href="' . absolute_url($matches[1]) . '">' . $matches[2] . '</a>';
        }, $message);

        $ret = ' ' . $message;
        $ret = preg_replace("#([\t\r\n ])([a-z0-9]+?){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="\2://\3">\2://\3</a>', $ret);
        $ret = preg_replace("#([\t\r\n ])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="http://\2.\3">\2.\3</a>', $ret);
        $ret = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $ret);
        $ret = substr($ret, 1);
        $output = '';

        $textarr = preg_split("/(<.*>)/U", $ret, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between
        $codeopen = false; // loop stuff
        foreach ($textarr as $curl) {
            if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Gecko')) {
                $curl = str_replace('<q>', '&ldquo;', $curl);
                $curl = str_replace('</q>', '&rdquo;', $curl);
            }
            if ('<' != substr($curl, 0, 1) && !$codeopen) { // If it's not a tag

                $curl = replace_quotes($curl);

            } elseif (strstr($curl, 'title="') || strstr($curl, 'alt="')) {
                $curl = preg_replace_callback('/(code|alt|title)=\"([^\"]+)\"/i', function ($matches) {
                    return $matches[1] . '="' . replace_quotes($matches[2]) . '"';
                }, $curl);
            }

            if($curl != "<br />" || !$codeopen) { $output .= $curl; }
        }

        return $output;
    }

    function copyright($year, $company = '', $reserved = true) {
        return '&copy; ' . $company . ($company ? ' ' : '')
            . $year . (date('Y') > $year ? '&ndash;' . date('Y') : '') . '.'
            . ($reserved ? ' All rights reserved.' : '');
    }

    function db_connect() {
        static $db;

        if ($db === null) {
            $url = parse_url(getenv('CLEARDB_DATABASE_URL'));
            $db = new mysqli($url['host'], $url['user'], $url['pass'], substr($url['path'], 1));

            if ($db->connect_error) {
                echo '<!-- ' . $db->connect_error . ' -->';
                $db = false;
            }
        }

        return $db;
    }

    function get_setting($key) {
        if ($db = db_connect()) {
            $result = $db->query('SELECT value FROM ' . TABLE_SETTING . ' WHERE `setting` = "' . $db->real_escape_string($key) . '"');

            if ($row = $result->fetch_assoc()) {
                return $row['value'];
            }
        }

        return null;
    }

    function cms($key) {
        static $content = null;

        if ($content === null) {
            $content = require_once('cms.php');
        }

        return (isset($content[$key]) ? $content[$key] : null);
    }

?>
