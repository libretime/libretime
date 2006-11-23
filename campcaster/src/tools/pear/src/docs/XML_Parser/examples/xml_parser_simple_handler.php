<?PHP
/**
 * example for XML_Parser_Simple
 *
 * $Id: xml_parser_simple_handler.php,v 1.1 2004/05/25 13:26:42 schst Exp $
 *
 * @author      Stephan Schmidt <schst@php-tools.net>
 * @package     XML_Parser
 * @subpackage  Examples
 */

/**
 * require the parser
 */
require_once 'XML/Parser/Simple.php';

class myHandlerSimple
{
   /**
    * handle the category element
    *
    * The element will be handled, once it's closed
    *
    * @access   private
    * @param    string      name of the element
    * @param    array       attributes of the element
    * @param    string      character data of the element
    */
    function handleElement_category($name, $attribs, $data)
    {
        printf( 'Category is %s<br />', $data );
    }

   /**
    * handle the name element
    *
    * The element will be handled, once it's closed
    *
    * @access   private
    * @param    string      name of the element
    * @param    array       attributes of the element
    * @param    string      character data of the element
    */
    function handleElement_name($name, $attribs, $data)
    {
        printf( 'Name is %s<br />', $data );
    }
}

$p = &new XML_Parser_Simple();
$h = &new myHandlerSimple();
$p->setHandlerObj($h);
$result = $p->setInputFile('xml_parser_simple2.xml');
$p->setMode('func');
$result = $p->parse();
?>