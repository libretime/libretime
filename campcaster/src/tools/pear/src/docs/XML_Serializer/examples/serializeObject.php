<?PHP
/**
 * This is just a basic example that shows
 * how objects can be serialized so they can
 * be fully restored later.
 *
 * @author Stephan Schmidt <schst@php.net>
 */
    error_reporting(E_ALL);

    require_once 'XML/Serializer.php';

    // this is just to get a nested object
    $pearError = PEAR::raiseError('This is just an error object',123);
    
    $options = array(
                        "indent"         => "    ",
                        "linebreak"      => "\n",
                        "defaultTagName" => "unnamedItem",
                        "typeHints"      => true
                    );
    
    $foo    =   new stdClass;
    $foo->value = "My value";
    $foo->error = $pearError;
    $foo->xml   = "cool";

    $foo->obj	= new stdClass;
    $foo->arr   = array();
    $foo->zero  = 0;
    
    $serializer = &new XML_Serializer($options);
    
    $result = $serializer->serialize($foo);
    
    if( $result === true ) {
		$xml = $serializer->getSerializedData();
    }

    echo	"<pre>";
    print_r( htmlspecialchars($xml) );
    echo	"</pre>";
?>