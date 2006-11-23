<?PHP
/**
* XML_Beautifier example 1
*
* This example displays the basic usage.
*
* @author	Stephan Schmidt <schst@php.net>
*/
	error_reporting( E_ALL );

    require_once 'XML/Beautifier.php';
    $fmt = new XML_Beautifier();
    $result = $fmt->formatFile('test.xml', 'test2.xml');
    
    if (PEAR::isError($result)) {
        echo $result->getMessage();
        exit();
    }

    echo "<h3>Original file</h3>";
    echo "<pre>";
    echo htmlspecialchars(implode("",file('test.xml')));
    echo "</pre>";
        
    echo    "<br><br>";
    
    echo "<h3>Beautified file</h3>";
    echo "<pre>";
    echo htmlspecialchars(implode("",file('test2.xml')));
    echo "</pre>";
?>
