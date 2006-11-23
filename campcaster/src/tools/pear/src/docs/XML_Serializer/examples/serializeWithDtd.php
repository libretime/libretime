<?PHP
/**
 * This example shows how to add a DocType Declaration to the XML document
 *
 * @author Stephan Schmidt <schst@php.net>
 */
    error_reporting(E_ALL);

    require_once 'XML/Serializer.php';

    $options = array(
                        "indent"     => "    ",
                        "linebreak"  => "\n",
                        "addDecl"    => true,
                        "addDoctype" => true,
                        "doctype"    => array(
                                                'uri' => 'http://pear.php.net/dtd/package-1.0',
                                                'id' => '-//PHP//PEAR/DTD PACKAGE 0.1'
                                             )
                    );
    
    $serializer = new XML_Serializer($options);

    $foo    =   PEAR::raiseError("Just a test", 1234);    
    
    $result = $serializer->serialize($foo);
    
    if( $result === true ) {
        echo    "<pre>";
        echo    htmlentities($serializer->getSerializedData());
        echo    "</pre>";
    }
?>