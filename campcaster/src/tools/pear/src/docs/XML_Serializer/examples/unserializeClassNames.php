<?PHP
/**
 * Example that shows how to influence the classes that are created.
 *
 * @author      Stephan Schmidt <schst@php.net>
 * @category    XML
 * @package     XML_Serializer
 * @subpackage  Examples
 */
error_reporting(E_ALL);

/**
 * uses XML_Unserializer
 */
require_once 'XML/Unserializer.php';

$xml = <<<EOT
<root>
   <foo>
     <type>bar</type>
   </foo>
   <bar type="foo"/>
</root>
EOT;

class foo
{
}
class bar
{
}

echo '<pre>';

//  be careful to always use the ampersand in front of the new operator 
$unserializer = &new XML_Unserializer();
$unserializer->setOption('complexType', 'object');
$unserializer->setOption('parseAttributes', true);
$unserializer->setOption('returnResult', true);


$data = $unserializer->unserialize($xml);    
var_dump( $data );


echo "Do not use tagname as class name\n";
$unserializer->setOption('tagAsClass', false);
$data = $unserializer->unserialize($xml);    
var_dump( $data );

echo "Use a different default class\n";
$unserializer->setOption('defaultClass', 'foo');
$data = $unserializer->unserialize($xml);    
var_dump( $data );

echo '</pre>';
?>