<?PHP
/**
 * Example that creates tags with a namespace
 *
 * @author Stephan Schmidt <schst@php.net>
 */
    error_reporting(E_ALL);

    require_once '../Serializer.php';

    $options = array(
                        'indent'         => '  ',
                        'linebreak'      => "\n",
                        'defaultTagName' => 'item',
                        'namespace'      => 'foo'
                    );
    
    $foo    =   new stdClass;
    $foo->value = 'My value';
    $foo->xml   = 'cool';

    $foo->obj	= new stdClass;
    $foo->arr   = array();
    $foo->zero  = 0;
    
    $serializer = &new XML_Serializer($options);
    
    $result = $serializer->serialize($foo);
    
    if( $result === true ) {
		$xml = $serializer->getSerializedData();
    }

    echo	'<pre>';
    print_r( htmlspecialchars($xml) );
    echo	'</pre>';
    
    // also pass the URI
    $serializer->setOption('namespace', array('bar', 'http://pear.php.net/package/XML_Serializer'));
    
    $result = $serializer->serialize($foo);
    
    if( $result === true ) {
		$xml = $serializer->getSerializedData();
    }

    echo	'<pre>';
    print_r( htmlspecialchars($xml) );
    echo	'</pre>';
?>