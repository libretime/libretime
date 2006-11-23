<?PHP
/**
 * XML Serializer example
 *
 * This example demonstrates, how XML_Serializer is able
 * to serialize scalar values as an attribute instead of a nested tag.
 *
 * In this example tags with more than one attribute become
 * multiline tags, as each attribute gets written to a
 * separate line as 'indentAttributes' is set to '_auto'.
 *
 * @author  Stephan Schmidt <schst@php.net>
 */
    error_reporting(E_ALL);
    require_once 'XML/Serializer.php';

    $options = array(
                        "indent"             => "    ",
                        "linebreak"          => "\n",
                        "typeHints"          => false,
                        "defaultTagName"     => "unnamedItem",
                        "scalarAsAttributes" => true,
                        "indentAttributes"   => "_auto"
                    );
    
    // this is just to get a nested object
    $pearError = PEAR::raiseError('This is just an error object',123);
	
    $foo    =   new stdClass;
    
    $foo->value = "My value";
    $foo->error = $pearError;
    $foo->xml   = "cool";
    
    $serializer = new XML_Serializer($options);
    
    $result = $serializer->serialize($foo);
    
    if( $result === true ) {
		$xml = $serializer->getSerializedData();

	    echo	"<pre>";
	    print_r( htmlspecialchars($xml) );
	    echo	"</pre>";
    } else {
		echo	"<pre>";
		print_r($result);
		echo	"</pre>";
	}

?>