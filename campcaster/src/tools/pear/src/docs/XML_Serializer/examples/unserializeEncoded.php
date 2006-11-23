<?PHP
/**
 * This example shows how to set a different target encoding
 *
 * @author  Stephan Schmidt <schst@php.net>
 */
    error_reporting(E_ALL);

    require_once 'XML/Unserializer.php';

$xml = '<dict>
   <word>
     <en>chickenwings</en>
     <de>'.utf8_encode('Hähnchenflügel').'</de>
   </word>
 </dict>';
    
    // specify different source and target encodings
    $options = array(
                      'encoding'       => 'UTF-8',
                      'targetEncoding' => 'ISO-8859-1'
                    );
    
                    
    //  be careful to always use the ampersand in front of the new operator 
    $unserializer = &new XML_Unserializer($options);

    // userialize the document
    $status = $unserializer->unserialize($xml);

    if (PEAR::isError($status)) {
        echo    "Error: " . $status->getMessage();
    } else {
        $data = $unserializer->getUnserializedData();

        echo	"<pre>";
        print_r( $data );
        echo	"</pre>";
    }
?>