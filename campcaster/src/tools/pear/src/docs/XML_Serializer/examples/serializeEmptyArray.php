<?PHP
/**
 * This shows that XML_Serializer is able to work with
 * empty arrays
 *
 * @author Stephan Schmidt <schst@php.net>
 */
error_reporting(E_ALL);
require_once 'XML/Unserializer.php';

$xml = <<< EOF
<autopage_options _class="autopage_options" 
_type="object">
<version _type="string">1.0</version>
<options _type="array" />
</autopage_options>
EOF;

$unserializer = new XML_Unserializer();
$result = $unserializer->unserialize($xml);

if( $result === true ) {
    echo '<pre>';
    print_r($unserializer->getUnserializedData());
    echo '</pre>';
}
?>