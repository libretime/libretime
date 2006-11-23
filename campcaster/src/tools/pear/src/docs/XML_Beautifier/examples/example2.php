<?PHP
/**
* XML_Beautifier example 2
*
* This example displays the formatString()
* method which can be used to beautify an XML string instead
* of a file.
*
* @author	Stephan Schmidt <schst@php.net>
*/
	error_reporting( E_ALL );

    $xmlString = '<xml><foo bar="tomato &amp; Cheese"/><argh>foobar</argh></xml>';

    require_once 'XML/Beautifier.php';
    $fmt = new XML_Beautifier();
    $result = $fmt->formatString($xmlString);

    echo "<h3>Original string</h3>";
    echo "<pre>";
    echo htmlspecialchars($xmlString);
    echo "</pre>";
        
    echo    "<br><br>";
    
    echo "<h3>Beautified string</h3>";
    echo "<pre>";
    echo htmlspecialchars($result);
    echo "</pre>";
?>
