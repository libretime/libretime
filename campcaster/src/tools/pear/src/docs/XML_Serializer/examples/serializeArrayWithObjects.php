<?PHP
/**
 * This example shows how to create an RDF document
 * with a few lines of code.
 * This can also be done with mode => simplexml
 *
 * @author Stephan Schmidt <schst@php.net>
 * @see    serializeIndexedArray.php
 */
    error_reporting(E_ALL);

    require_once 'XML/Serializer.php';

    $options = array(
                        "indent"          => "    ",
                        "linebreak"       => "\n",
                        "typeHints"       => false,
                        "addDecl"         => true,
                        "encoding"        => "UTF-8"
                    );
    
    $serializer = new XML_Serializer($options);

    $serializer->setErrorHandling(PEAR_ERROR_DIE);
    
    $array = array(
                    new stdClass(),
                    new stdClass()
                    );
    
    $result = $serializer->serialize($array);
    
    if( $result === true ) {
        echo    "<pre>";
        echo    htmlentities($serializer->getSerializedData());
        echo    "</pre>";
    }

    $result = $serializer->serialize($array, array( 'classAsTagName' => true ));
    
    if( $result === true ) {
        echo    "<pre>";
        echo    htmlentities($serializer->getSerializedData());
        echo    "</pre>";
    }
    
?>