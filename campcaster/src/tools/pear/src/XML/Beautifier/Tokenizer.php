<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Stephan Schmidt <schst@php.net>                             |
// +----------------------------------------------------------------------+

/**
 * XML/Beautifier/Tokenizer.php
 *
 * @category XML
 * @package  XML_Beautifier
 * @author   Stephan Schmidt <schst@php.net>
 * @todo     tokenize DTD
 * @todo     check for xml:space attribute
 */

/**
 * XML_Parser is needed to parse the document
 */
require_once 'XML/Parser.php';
 
/**
 * Tokenizer for XML_Beautifier
 *
 * This class breaks an XML document in seperate tokens
 * that will be rendered by an XML_Beautifier renderer.
 *
 * @category XML
 * @package  XML_Beautifier
 * @author   Stephan Schmidt <schst@php.net>
 */
class XML_Beautifier_Tokenizer extends XML_Parser {

   /**
    * current depth
    * @var    integer
    * @access private
    */
    var $_depth = 0;

   /**
    * stack for all found elements
    * @var    array
    * @access private
    */
    var $_struct = array();

   /**
    * current parsing mode
    * @var    string
    * @access private  
    */
    var $_mode = "xml";
    
   /**
    * Tokenize a document
    *
    * @param    string  $document   filename or XML document
    * @param    boolean $isFile     flag to indicate whether the first parameter is a file
    */
    function tokenize( $document, $isFile = true )
    {
        $this->folding = false;
        $this->XML_Parser();
        $this->_resetVars();
        
        if( $isFile === true ) {
            $this->setInputFile($document);
            $result = $this->parse();
        }
        else {
            $result = $this->parseString($document);
        }
        
        if ($this->isError($result)) {
            return $result;
        }

        return $this->_struct;
    }
    
    /**
     * Start element handler for XML parser
     *
     * @access protected
     * @param  object $parser  XML parser object
     * @param  string $element XML element
     * @param  array  $attribs attributes of XML tag
     * @return void
     */
    function startHandler($parser, $element, $attribs)
    {
        $struct = array(
                         "type"     => XML_BEAUTIFIER_ELEMENT,
                         "tagname"  => $element,
                         "attribs"  => $attribs,
                         "contains" => XML_BEAUTIFIER_EMPTY,
                         "depth"    => $this->_depth++,
                         "children" => array()
                      );

        array_push($this->_struct,$struct);
    }

    /**
     * End element handler for XML parser
     *
     * @access protected
     * @param  object XML parser object
     * @param  string
     * @return void
     */
    function endHandler($parser, $element)
    {
        $struct = array_pop($this->_struct);
        if ($struct["depth"] > 0) { 
            $parent = array_pop($this->_struct);
            array_push($parent["children"], $struct);
            $parent["contains"] = $parent["contains"] | XML_BEAUTIFIER_ELEMENT;
            array_push($this->_struct, $parent);
        } else {
            array_push($this->_struct, $struct);
        }
        $this->_depth--;
    }

    /**
     * Handler for character data
     *
     * @access protected
     * @param  object XML parser object
     * @param  string CDATA
     * @return void
     */
    function cdataHandler($parser, $cdata)
    {
        if ((string)$cdata === '') {
            return true;
        }

        $struct = array(
                         "type"  => XML_BEAUTIFIER_CDATA,
                         "data"  => $cdata,
                         "depth" => $this->_depth
                       );

        $this->_appendToParent($struct);
    }

    /**
     * Handler for processing instructions
     *
     * @access protected
     * @param  object XML parser object
     * @param  string target
     * @param  string data
     * @return void
     */
    function    piHandler($parser, $target, $data)
    {
        $struct = array(
                         "type"    => XML_BEAUTIFIER_PI,
                         "target"  => $target,
                         "data"    => $data,
                         "depth"   => $this->_depth
                       );

        $this->_appendToParent($struct);
    }
    
    /**
     * Handler for external entities
     *
     * @access protected
     * @param  object XML parser object
     * @param  string target
     * @param  string data
     * @return void
     */
    function entityrefHandler($parser, $open_entity_names, $base, $system_id, $public_id)
    {
        $struct = array(
                         "type"    => XML_BEAUTIFIER_ENTITY,
                         "name"    => $open_entity_names,
                         "depth"   => $this->_depth
                       );

        $this->_appendToParent($struct);
        return true;
    }

    /**
     * Handler for all other stuff
     *
     * @access protected
     * @param  object XML parser object
     * @param  string data
     * @return void
     */
    function defaultHandler($parser, $data)
    {
        switch ($this->_mode) {
            case "xml":
                $this->_handleXMLDefault($data);
                break;
            case "doctype":
                $this->_handleDoctype($data);
                break;
        }
    }

   /**
    * handler for all data inside the doctype declaration
    *
    * @access private
    * @param  string    data
    * @todo   improve doctype parsing to split the declaration into seperate tokens
    */
    function _handleDoctype($data)
    {
        if (eregi(">", $data)) {
            $last = $this->_getLastToken();
            if ($last["data"] == "]" ) {
                $this->_mode = "xml";
            }
        }

        $struct = array(
                         "type"    => XML_BEAUTIFIER_DT_DECLARATION,
                         "data"    => $data,
                         "depth"   => $this->_depth
                       );
        $this->_appendToParent($struct);
    }
    
   /**
    * handler for all default XML data
    *
    * @access private
    * @param  string    data
    */    
    function _handleXMLDefault($data)
    {
        /*
        * handle comment
        */
        if (strncmp("<!--", $data, 4) == 0) {
        
            $regs = array();
            eregi("<!--(.+)-->", $data, $regs);
            $comment = trim($regs[1]);
            
            $struct = array(
                             "type"    => XML_BEAUTIFIER_COMMENT,
                             "data"    => $comment,
                             "depth"   => $this->_depth
                           );
        /*
        * handle XML declaration
        */
        } elseif (strncmp("<?", $data, 2) == 0) {
            preg_match_all('/([a-zA-Z_]+)="((?:\\\.|[^"\\\])*)"/', $data, $match);
            $cnt = count($match[1]);
            $attribs = array();
            for ($i = 0; $i < $cnt; $i++) {
                $attribs[$match[1][$i]] = $match[2][$i];
            }

            if (!isset($attribs["version"])) {
                $attribs["version"] = "1.0";
            }
            if (!isset($attribs["encoding"])) {
                $attribs["encoding"] = "UTF-8";
            }
            if (!isset($attribs["standalone"])) {
                $attribs["standalone"] = true;
            }
            
            $struct = array(
                             "type"       => XML_BEAUTIFIER_XML_DECLARATION,
                             "version"    => $attribs["version"],
                             "encoding"   => $attribs["encoding"],
                             "standalone" => $attribs["standalone"],
                             "depth"      => $this->_depth
                           );
        } elseif (eregi("^<!DOCTYPE", $data)) {
            $this->_mode = "doctype";
            $struct = array(
                             "type"    => XML_BEAUTIFIER_DT_DECLARATION,
                             "data"    => $data,
                             "depth"   => $this->_depth
                           );
        } else {
        /*
        * handle all other data
        */
            $struct = array(
                             "type"    => XML_BEAUTIFIER_DEFAULT,
                             "data"    => $data,
                             "depth"   => $this->_depth
                           );
        }
        
        $this->_appendToParent($struct);
        return true;
    }
    
    /**
     * append a struct to the last struct on the stack
     *
     * @access private
     * @param  array    $struct structure to append
     */
    function _appendToParent($struct)
    {
        if ($this->_depth > 0) {
            $parent = array_pop($this->_struct);
            array_push($parent["children"], $struct);
            $parent["contains"] = $parent["contains"] | $struct["type"];
            array_push($this->_struct, $parent);
            return true;
        }
        array_push($this->_struct, $struct);
    }

   /**
    * get the last token
    *
    * @access   private
    * @return   array
    */
    function _getLastToken()
    {
        $parent = array_pop($this->_struct);
        if (isset($parent["children"]) && is_array($parent["children"])) {
            $last = array_pop($parent["children"]);
            array_push($parent["children"], $last);
        } else {
            $last = $parent;
        }
        array_push($this->_struct, $parent);
           
        return $last;
    }
    
   /**
    * reset all used object properties
    *
    * This method is called before parsing a new document
    *
    * @access private
    */
    function _resetVars()
    {
        $this->_depth  = 0;
        $this->_struct = array();
        $this->_mode   = "xml";
    }
}
?>