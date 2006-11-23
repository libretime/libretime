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
                        'indent'             => '    ',
                        'linebreak'          => "\n",
                        'mode'               => 'simplexml',
                        'rootName'			 => 'items'
                    );

    $data = array(
					'item' => array( array(
										'title'       => 'Foobar!',
										'description' => 'This is some text....',
										'link'        => 'http://foobar.com'
									),
									array(
										'title'       => 'Foobar2!',
										'description' => 'This is some text.ü &uuml; ä ö',
										'link'        => 'http://foobar.com'
									)
								)
	    												
    			);
                    
    
    $serializer = new XML_Serializer($options);
    
    $result = $serializer->serialize($data);
    
    if( $result === true ) {
		$xml = $serializer->getSerializedData();

	    echo	'<pre>';
	    print_r( htmlspecialchars($xml) );
	    echo	'</pre>';
    } else {
		echo	'<pre>';
		print_r($result);
		echo	'</pre>';
	}

	$newOptions = array(
						'rootName' => 'body',
						'replaceEntities' => XML_SERIALIZER_ENTITIES_HTML,
						'tagMap'   => array(
						                      'item'        => 'div',
						                      'title'       => 'h1',
						                      'description' => 'p',
						                      'link'        => 'tt'
						                  )
					);
	
    $result = $serializer->serialize($data, $newOptions);
    
    if( $result === true ) {
		$xml = $serializer->getSerializedData();

	    echo	'<pre>';
	    print_r( htmlspecialchars($xml) );
	    echo	'</pre>';
    } else {
		echo	'<pre>';
		print_r($result);
		echo	'</pre>';
	}

?>