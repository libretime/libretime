<?PHP
/**
* XML_Beautifier example 4
*
* This example displays the 'normalizeComments'
* option. If it is set to true, multiline comments will
* be replaced by a single line comment.
*
* @author	Stephan Schmidt <schst@php.net>
*/
	error_reporting( E_ALL );

    require_once 'XML/Beautifier.php';
    
    $options = array(
                        "normalizeComments" => true,
                    );
    
    $fmt = new XML_Beautifier($options);
    $result = $fmt->formatFile('test.xml', 'test2.xml');

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
