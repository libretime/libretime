<?PHP
/**
 * This example shows different methods how
 * XML_Unserializer can be used to create data structures
 * from XML documents.
 *
 * @author  Stephan Schmidt <schst@php.net>
 */
    error_reporting(E_ALL);

    // this is a simple XML document
    $xml = '<users>' .
           '  <user handle="schst">Stephan Schmidt</user>' .
           '  <user handle="mj">Martin Jansen</user>' .
           '  <group name="qa">PEAR QA Team</group>' .
           '  <foo id="test">This is handled by the default keyAttribute</foo>' .
           '  <foo id="test2">Another foo tag</foo>' .
           '</users>';

    require_once 'XML/Unserializer.php';

    // complex structures are arrays, the key is the attribute 'handle' or 'name', if handle is not present
    $options = array(
                     "complexType" => "array",
                     "keyAttribute" => array(
                                              'user'  => 'handle',
                                              'group' => 'name',
                                              '__default' => 'id'
                                            )
                    );
 
    //  be careful to always use the ampersand in front of the new operator 
    $unserializer = &new XML_Unserializer($options);

    // userialize the document
    $status = $unserializer->unserialize($xml, false);    

    if (PEAR::isError($status)) {
        echo    "Error: " . $status->getMessage();
    } else {
        $data = $unserializer->getUnserializedData();

        echo	"<pre>";
        print_r( $data );
        echo	"</pre>";
    }


    // unserialize it again and change the complexType option
    // but leave other options untouched
    // now complex types will be an object, and the property name will be in the
    // attribute 'handle'
    $status = $unserializer->unserialize($xml, false, array("complexType" => "object"));    

    if (PEAR::isError($status)) {
        echo    "Error: " . $status->getMessage();
    } else {
        $data = $unserializer->getUnserializedData();

        echo	"<pre>";
        print_r( $data );
        echo	"</pre>";
    }


    // unserialize it again and change the complexType option
    // and reset all other options
    // Now, there's no key so the tags are stored in an array
    $status = $unserializer->unserialize($xml, false, array("overrideOptions" => true, "complexType" => "object"));    

    if (PEAR::isError($status)) {
        echo    "Error: " . $status->getMessage();
    } else {
        $data = $unserializer->getUnserializedData();

        echo	"<pre>";
        print_r( $data );
        echo	"</pre>";
    }

?>