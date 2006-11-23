<?PHP
/**
* XML_Beautifier example 5
*
* This example displays the 'maxCommentLine'
* option. It will split long comments into seperate lines
* with a maximum length of 'maxCommentLine'.
*
* For the best results, you should always use this in
* conjunction with normalizeComments.
*
* @author	Stephan Schmidt <schst@php.net>
*/
	error_reporting( E_ALL );

    require_once 'XML/Beautifier.php';
    
    $options = array(
                        "normalizeComments" => true,
						"maxCommentLine"	=> 10
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
