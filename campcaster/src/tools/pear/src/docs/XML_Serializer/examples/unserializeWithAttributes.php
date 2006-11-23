<?PHP
/**
 * This example shows how to parse attributes from
 * XML tags.
 *
 * @author  Stephan Schmidt <schst@php.net>
 * @uses    example.xml
 */
    error_reporting(E_ALL);

    require_once 'XML/Unserializer.php';

    $options = array(
                        "parseAttributes"   =>  true,
                        "attributesArray"   =>  false
                    );
    
    //  be careful to always use the ampersand in front of the new operator 
    $unserializer = &new XML_Unserializer($options);

    // userialize the document
    $status = $unserializer->unserialize("example.xml", true);    

    if (PEAR::isError($status)) {
        echo    "Error: " . $status->getMessage();
    } else {
        $data = $unserializer->getUnserializedData();

        echo	"<pre>";
        print_r( $data );
        echo	"</pre>";
    }
?>