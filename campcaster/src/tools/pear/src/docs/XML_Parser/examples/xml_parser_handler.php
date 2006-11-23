<?PHP
/**
 * example for XML_Parser_Simple
 *
 * @author      Stephan Schmidt <schst@php-tools.net>
 * @package     XML_Parser
 * @subpackage  Examples
 */

/**
 * require the parser
 */
require_once 'XML/Parser.php';

class myHandler
{
   /**
    * handle start element
    *
    * @access   private
    * @param    resource    xml parser resource
    * @param    string      name of the element
    * @param    array       attributes
    */
    function startHandler($xp, $name, $attribs)
    {
        printf('handle start tag: %s<br />', $name);
    }

   /**
    * handle start element
    *
    * @access   private
    * @param    resource    xml parser resource
    * @param    string      name of the element
    * @param    array       attributes
    */
    function endHandler($xp, $name)
    {
        printf('handle end tag: %s<br />', $name);
    }
}

$p = &new XML_Parser();
$h = &new myHandler();

$result = $p->setInputFile('xml_parser_file.xml');
$result = $p->setHandlerObj($h);
$result = $p->parse();
?>