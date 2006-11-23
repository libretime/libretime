<?PHP
/**
 * example for XML_Parser_Simple
 *
 * $Id: xml_parser_simple1.php,v 1.3 2004/05/28 16:09:48 schst Exp $
 *
 * @author      Stephan Schmidt <schst@php-tools.net>
 * @package     XML_Parser
 * @subpackage  Examples
 */

/**
 * require the parser
 */
require_once 'XML/Parser/Simple.php';

class myParser extends XML_Parser_Simple
{
    function myParser()
    {
        $this->XML_Parser_Simple();
    }

   /**
    * handle the element
    *
    * The element will be handled, once it's closed
    *
    * @access   private
    * @param    string      name of the element
    * @param    array       attributes of the element
    * @param    string      character data of the element
    */
    function handleElement($name, $attribs, $data)
    {
        printf('handling %s in tag depth %d<br />', $name, $this->getCurrentDepth());
        printf('character data: %s<br />', $data );
        print 'Attributes:<br />';
        print '<pre>';
        print_r( $attribs );
        print '</pre>';
        print '<br />';
    }
}

$p = &new myParser();

$result = $p->setInputFile('xml_parser_simple1.xml');
$result = $p->parse();
?>