<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * XML_Beautifier/Tokenizer
 *
 * XML Beautifier package's Tokenizer
 *
 * PHP versions 4 and 5
 *
 * LICENSE:
 *
 * Copyright (c) 2003-2008 Stephan Schmidt <schst@php.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * The name of the author may not be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  XML
 * @package   XML_Beautifier
 * @author    Stephan Schmidt <schst@php.net>
 * @copyright 2003-2008 Stephan Schmidt <schst@php.net>
 * @license   http://opensource.org/licenses/bsd-license New BSD License
 * @version   CVS: $Id: Tokenizer.php,v 1.10 2008/08/24 19:44:14 ashnazg Exp $
 * @link      http://pear.php.net/package/XML_Beautifier
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
 * @category  XML
 * @package   XML_Beautifier
 * @author    Stephan Schmidt <schst@php.net>
 * @copyright 2003-2008 Stephan Schmidt <schst@php.net>
 * @license   http://opensource.org/licenses/bsd-license New BSD License
 * @version   Release: 1.2.0
 * @link      http://pear.php.net/package/XML_Beautifier
 * @todo      tokenize DTD
 * @todo      check for xml:space attribute
 */
class XML_Beautifier_Tokenizer extends XML_Parser
{
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
     * indicates, whether parser is in cdata section
     * @var    boolean
     * @access private  
     */
    var $_inCDataSection = false;

    /**
     * Tokenize a document
     *
     * @param string  $document filename or XML document
     * @param boolean $isFile   flag to indicate whether 
     *                          the first parameter is a file
     *
     * @return mixed
     */
    function tokenize($document, $isFile = true)
    {
        $this->folding = false;
        $this->XML_Parser();
        $this->_resetVars();
        
        if ($isFile === true) {
            $this->setInputFile($document);
            $result = $this->parse();
        } else {
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
     * @param object $parser  XML parser object
     * @param string $element XML element
     * @param array  $attribs attributes of XML tag
     *
     * @return void
     * @access protected
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

        array_push($this->_struct, $struct);
    }

    /**
     * End element handler for XML parser
     *
     * @param object $parser  XML parser object
     * @param string $element element
     *
     * @return void
     * @access protected
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
     * @param object $parser XML parser object
     * @param string $cdata  CDATA
     *
     * @return void
     * @access protected
     */
    function cdataHandler($parser, $cdata)
    {
        if ((string)$cdata === '') {
            return true;
        }

        if ($this->_inCDataSection === true) {
            $type = XML_BEAUTIFIER_CDATA_SECTION;
        } else {
            $type = XML_BEAUTIFIER_CDATA;
        }

        $struct = array(
            "type"  => $type,
            "data"  => $cdata,
            "depth" => $this->_depth
        );

        $this->_appendToParent($struct);
    }

    /**
     * Handler for processing instructions
     *
     * @param object $parser XML parser object
     * @param string $target target
     * @param string $data   data
     *
     * @return void
     * @access protected
     */
    function piHandler($parser, $target, $data)
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
     * @param object $parser            XML parser object
     * @param string $open_entity_names entity name
     * @param string $base              ?? (unused?)
     * @param string $system_id         ?? (unused?)
     * @param string $public_id         ?? (unused?)
     *
     * @return bool
     * @access protected
     * @todo revisit parameter signature... doesn't seem to be correct
     * @todo PEAR CS - need to shorten arg list for 85-char rule
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
     * @param object $parser XML parser object
     * @param string $data   data
     *
     * @return void
     * @access protected
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
     * @param string $data data
     *
     * @return void
     * @access private
     * @todo improve doctype parsing to split the declaration into seperate tokens
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
     * @param string $data data
     *
     * @return bool
     * @access private
     */    
    function _handleXMLDefault($data)
    {
        if (strncmp("<!--", $data, 4) == 0) {

            /*
             * handle comment
             */
            $regs = array();
            eregi("<!--(.+)-->", $data, $regs);
            $comment = trim($regs[1]);
            
            $struct = array(
                "type"    => XML_BEAUTIFIER_COMMENT,
                "data"    => $comment,
                "depth"   => $this->_depth
            );

        } elseif ($data == "<![CDATA[") {
            /*
             * handle start of cdata section
             */
            $this->_inCDataSection = true;
            $struct                = null;

        } elseif ($data == "]]>") {
            /*
             * handle end of cdata section
             */
            $this->_inCDataSection = false;
            $struct                = null;

        } elseif (strncmp("<?", $data, 2) == 0) {
            /*
             * handle XML declaration
             */
            preg_match_all('/([a-zA-Z_]+)="((?:\\\.|[^"\\\])*)"/', $data, $match);
            $cnt     = count($match[1]);
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
            } else {
                if ($attribs["standalone"] === 'yes') {
                    $attribs["standalone"] = true;
                } else {
                    $attribs["standalone"] = false;
                }
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
            $struct      = array(
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
        
        if (!is_null($struct)) {
            $this->_appendToParent($struct);
        }
        return true;
    }
    
    /**
     * append a struct to the last struct on the stack
     *
     * @param array $struct structure to append
     *
     * @return bool
     * @access private
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
     * @return void
     * @access private
     */
    function _resetVars()
    {
        $this->_depth          = 0;
        $this->_struct         = array();
        $this->_mode           = "xml";
        $this->_inCDataSection = false;
    }
}
?>
