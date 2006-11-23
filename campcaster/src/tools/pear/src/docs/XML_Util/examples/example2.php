<?PHP
/**
 * several examples for the methods of XML_Util
 *
 * $Id: example2.php,v 1.8 2004/06/27 18:17:39 schst Exp $
 *
 * @author      Stephan Schmidt
 * @package     XML_Util
 * @subpackage  examples
 * @category    XML
 */
    error_reporting(E_ALL);

    require_once 'XML/Util.php';

    /**
    * creating a start element
    */
    print "creating a start element:<br>";
    print htmlentities(XML_Util::createStartElement("myNs:myTag", array("foo" => "bar"), "http://www.w3c.org/myNs#"));
    print "\n<br><br>\n";


    /**
    * creating a start element
    */
    print "creating a start element:<br>";
    print htmlentities(XML_Util::createStartElement("myTag", array(), "http://www.w3c.org/myNs#"));
    print "\n<br><br>\n";

    /**
    * creating a start element
    */
    print "creating a start element:<br>";
    print "<pre>";
    print htmlentities(XML_Util::createStartElement("myTag", array( "foo" => "bar", "argh" => "tomato" ), "http://www.w3c.org/myNs#", true));
    print "</pre>";
    print "\n<br><br>\n";


    /**
    * creating an end element
    */
    print "creating an end element:<br>";
    print htmlentities(XML_Util::createEndElement("myNs:myTag"));
    print "\n<br><br>\n";

    /**
    * creating a CData section
    */
    print "creating a CData section:<br>";
    print htmlentities(XML_Util::createCDataSection("I am content."));
    print "\n<br><br>\n";

    /**
    * creating a comment
    */
    print "creating a comment:<br>";
    print htmlentities(XML_Util::createComment("I am a comment."));
    print "\n<br><br>\n";

    /**
    * creating an XML tag with multiline mode
    */
    $tag = array(
                  "qname"        => "foo:bar",
                  "namespaceUri" => "http://foo.com",
                  "attributes"   => array( "key" => "value", "argh" => "fruit&vegetable" ),
                  "content"      => "I'm inside the tag & contain dangerous chars"
                );

    print "creating a tag with qualified name and namespaceUri:<br>\n";
    print "<pre>";
    print htmlentities(XML_Util::createTagFromArray($tag, XML_UTIL_REPLACE_ENTITIES, true));
    print "</pre>";
    print "\n<br><br>\n";

    /**
    * create an attribute string without replacing the entities
    */
    $atts = array( 'series' => 'Starsky &amp; Hutch', 'channel' => 'ABC' );
    print "creating a attribute string, entities in values already had been replaced:<br>";
    print htmlentities(XML_Util::attributesToString($atts, true, false, false, false, XML_UTIL_ENTITIES_NONE));
    print "\n<br><br>\n";

    /**
    * using the array-syntax for attributesToString()
    */
    $atts = array( 'series' => 'Starsky &amp; Hutch', 'channel' => 'ABC' );
    print "using the array-syntax for attributesToString()<br>";
    print htmlentities(XML_Util::attributesToString($atts, array('entities' => XML_UTIL_ENTITIES_NONE)));
    print "\n<br><br>\n";


?>