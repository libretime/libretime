<?PHP
/**
 * several examples for the methods of XML_Util
 *
 * $Id: example.php,v 1.12 2004/12/23 13:22:00 schst Exp $
 *
 * @author      Stephan Schmidt
 * @package     XML_Util
 * @subpackage  examples
 * @category    XML
 */
    error_reporting(E_ALL);

    require_once 'XML/Util.php';
    
    /**
    * replacing XML entities
    */
    print "replace XML entities:<br>\n";
    print XML_Util::replaceEntities("This string contains < & >.");
    print "\n<br><br>\n";

    /**
    * reversing XML entities
    */
    print "replace XML entities:<br>\n";
    print XML_Util::reverseEntities("This string contains &lt; &amp; &gt;.");
    print "\n<br><br>\n";

    /**
    * building XML declaration
    */
    print "building XML declaration:<br>\n";
    print htmlspecialchars(XML_Util::getXMLDeclaration());
    print "\n<br><br>\n";

    print "building XML declaration with additional attributes:<br>";
    print htmlspecialchars(XML_Util::getXMLDeclaration("1.0", "UTF-8", true));
    print "\n<br><br>\n";

    /**
    * building document type declaration
    */
    print "building DocType declaration:<br>\n";
    print htmlspecialchars(XML_Util::getDocTypeDeclaration('package', 'http://pear.php.net/dtd/package-1.0'));
    print "\n<br><br>\n";

    print "building DocType declaration with public ID (does not exist):<br>\n";
    print htmlspecialchars(XML_Util::getDocTypeDeclaration('package', array('uri' => 'http://pear.php.net/dtd/package-1.0', 'id' => '-//PHP//PEAR/DTD PACKAGE 0.1')));
    print "\n<br><br>\n";

    print "building DocType declaration with internal DTD:<br>\n";
    print "<pre>";
    print htmlspecialchars(XML_Util::getDocTypeDeclaration('package', 'http://pear.php.net/dtd/package-1.0', '<!ELEMENT additionalInfo (#PCDATA)>'));
    print "</pre>";
    print "\n<br><br>\n";

    /**
    * creating an attribute string
    */
    $att = array(
                  "foo"   =>  "bar",
                  "argh"  =>  "tomato"
                );

    print "converting array to string:<br>\n";
    print XML_Util::attributesToString($att);
    print "\n<br><br>\n";


    /**
    * creating an attribute string with linebreaks
    */
    $att = array(
                  "foo"   =>  "bar",
                  "argh"  =>  "tomato"
                );

    print "converting array to string (including line breaks):<br>\n";
    print "<pre>";
    print XML_Util::attributesToString($att, true, true);
    print "</pre>";
    print "\n<br><br>\n";


    /**
    * splitting a qualified tag name
    */
    print "splitting qualified tag name:<br>\n";
    print "<pre>";
    print_r(XML_Util::splitQualifiedName("xslt:stylesheet"));
    print "</pre>";
    print "\n<br>\n";


    /**
    * splitting a qualified tag name (no namespace)
    */
    print "splitting qualified tag name (no namespace):<br>\n";
    print "<pre>";
    print_r(XML_Util::splitQualifiedName("foo"));
    print "</pre>";
    print "\n<br>\n";

    /**
    * splitting a qualified tag name (no namespace, but default namespace specified)
    */
    print "splitting qualified tag name (no namespace, but default namespace specified):<br>\n";
    print "<pre>";
    print_r(XML_Util::splitQualifiedName("foo", "bar"));
    print "</pre>";
    print "\n<br>\n";

    /**
    * verifying XML names
    */
    print "verifying 'My private tag':<br>\n";
    print "<pre>";
    print_r(XML_Util::isValidname('My Private Tag'));
    print "</pre>";
    print "\n<br><br>\n";
    
    print "verifying '-MyTag':<br>\n";
    print "<pre>";
    print_r(XML_Util::isValidname('-MyTag'));
    print "</pre>";
    print "\n<br><br>\n";

    /**
    * creating an XML tag
    */
    $tag = array(
                  "namespace"   => "foo",
                  "localPart"   => "bar",
                  "attributes"  => array( "key" => "value", "argh" => "fruit&vegetable" ),
                  "content"     => "I'm inside the tag"
                );

    print "creating a tag with namespace and local part:<br>";
    print htmlentities(XML_Util::createTagFromArray($tag));
    print "\n<br><br>\n";

    /**
    * creating an XML tag
    */
    $tag = array(
                  "qname"        => "foo:bar",
                  "namespaceUri" => "http://foo.com",
                  "attributes"   => array( "key" => "value", "argh" => "fruit&vegetable" ),
                  "content"      => "I'm inside the tag"
                );

    print "creating a tag with qualified name and namespaceUri:<br>\n";
    print htmlentities(XML_Util::createTagFromArray($tag));
    print "\n<br><br>\n";

    /**
    * creating an XML tag
    */
    $tag = array(
                  "qname"        => "bar",
                  "namespaceUri" => "http://foo.com",
                  "attributes"   => array( "key" => "value", "argh" => "fruit&vegetable" )
                );

    print "creating an empty tag without namespace but namespace Uri:<br>\n";
    print htmlentities(XML_Util::createTagFromArray($tag));
    print "\n<br><br>\n";

    /**
    * creating an XML tag with a CData Section
    */
    $tag = array(
                  "qname"        => "foo",
                  "attributes"   => array( "key" => "value", "argh" => "fruit&vegetable" ),
                  "content"      => "I'm inside the tag"
                );

    print "creating a tag with CData section:<br>\n";
    print htmlentities(XML_Util::createTagFromArray($tag, XML_UTIL_CDATA_SECTION));
    print "\n<br><br>\n";

    /**
    * creating an XML tag with a CData Section
    */
    $tag = array(
                  "qname"        => "foo",
                  "attributes"   => array( "key" => "value", "argh" => "tütü" ),
                  "content"      => "Also XHTML-tags can be created and HTML entities can be replaced Ä ä Ü ö <>."
                );

    print "creating a tag with HTML entities:<br>\n";
    print htmlentities(XML_Util::createTagFromArray($tag, XML_UTIL_ENTITIES_HTML));
    print "\n<br><br>\n";

    /**
    * creating an XML tag with createTag
    */
    print "creating a tag with createTag:<br>";
    print htmlentities(XML_Util::createTag("myNs:myTag", array("foo" => "bar"), "This is inside the tag", "http://www.w3c.org/myNs#"));
    print "\n<br><br>\n";

    
    /**
    * trying to create an XML tag with an array as content
    */
    $tag = array(
                  "qname"        => "bar",
                  "content"      => array( "foo" => "bar" )
                );
    print "trying to create an XML tag with an array as content:<br>\n";
    print "<pre>";
    print_r(XML_Util::createTagFromArray($tag));
    print "</pre>";
    print "\n<br><br>\n";
    
    /**
    * trying to create an XML tag without a name
    */
    $tag = array(
                  "attributes"   => array( "foo" => "bar" ),
                );
    print "trying to create an XML tag without a name:<br>\n";
    print "<pre>";
    print_r(XML_Util::createTagFromArray($tag));
    print "</pre>";
    print "\n<br><br>\n";
?>