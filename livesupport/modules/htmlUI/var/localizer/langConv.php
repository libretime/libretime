<?php
$f = file('languages.txt');
foreach($f as $nr=>$text) {
    list($code, $name) = explode('  ', $text);
    echo "<item>\n\t<code>".trim($code)."</code>\n\t<name>".trim($name)."</name>\n</item>\n";
}
?>