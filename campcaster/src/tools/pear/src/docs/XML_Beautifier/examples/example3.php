<?PHP
/**
* XML_Beautifier example 3
*
* This example displays the 'caseFolding'
* option. It can be used to convert all tags
* and attribute names to upper case or lower
* case. Use the 'caseFoldingTo' options to
* to specify the case.
*
* @author	Stephan Schmidt <schst@php.net>
*/
	error_reporting( E_ALL );

    require_once 'XML/Beautifier.php';
    
    $options = array(
                        "caseFolding" => true,
                        "caseFoldingTo" => "uppercase"
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
