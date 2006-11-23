<?PHP
/**
 * XML_Beautifier example 6
 *
 * This example displays the multilineTags option.
 * Furthermore it shows what happens, when no output file
 * is specified.
 *
 * @author	Stephan Schmidt <schst@php.net>
 */
	error_reporting( E_ALL );

    require_once 'XML/Beautifier.php';

    $fmt = new XML_Beautifier( array( "multilineTags" => true ) );
    $result = $fmt->formatFile('test.xml');

    echo "<h3>Original file</h3>";
    echo "<pre>";
    echo htmlspecialchars(implode("",file('test.xml')));
    echo "</pre>";
        
    echo    "<br><br>";
    
    echo "<h3>Beautified file</h3>";
    echo "<pre>";
    echo htmlspecialchars($result);
    echo "</pre>";
?>
