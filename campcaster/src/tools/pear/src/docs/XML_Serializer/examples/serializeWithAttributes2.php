<?PHP
/**
 * XML Serializer example
 *
 * This example demonstrates, how XML_Serializer is able
 * to serialize predefined values as the attributes of a tag
 *
 * @author  Stephan Schmidt <schst@php.net>
 */
    error_reporting(E_ALL);

    require_once 'XML/Serializer.php';

    $options = array(
                        "indent"             => "    ",
                        "linebreak"          => "\n",
                        "defaultTagName"     => "unnamedItem",
						"scalarAsAttributes" => false,
                        "attributesArray"    => '_attributes',
                        "contentName"        => '_content'
                    );

    $data = array(
                    'foo' => array(
                                    '_attributes' => array( 'version' => '1.0', 'foo' => 'bar' ),
                                    '_content'    => 'test'
                                  ),
                    'schst' => 'Stephan Schmidt'
                );    
    
    $serializer = new XML_Serializer($options);
    
    $result = $serializer->serialize($data);
    
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