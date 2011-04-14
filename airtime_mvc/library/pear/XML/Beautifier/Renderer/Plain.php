<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * XML_Beautifier
 *
 * XML Beautifier's Plain Renderer 
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
 * @version   CVS: $Id: Plain.php,v 1.7 2008/08/24 19:44:14 ashnazg Exp $
 * @link      http://pear.php.net/package/XML_Beautifier
 */

/**
 * XML_Util is needed to create the tags
 */
require_once 'XML/Util.php';

/**
 * Renderer base class
 */
require_once XML_BEAUTIFIER_INCLUDE_PATH . '/Renderer.php';

/**
 * Basic XML Renderer for XML Beautifier
 *
 * @category  XML
 * @package   XML_Beautifier
 * @author    Stephan Schmidt <schst@php.net>
 * @copyright 2003-2008 Stephan Schmidt <schst@php.net>
 * @license   http://opensource.org/licenses/bsd-license New BSD License
 * @version   Release: 1.2.0
 * @link      http://pear.php.net/package/XML_Beautifier
 * @todo      option to specify inline tags
 * @todo      option to specify treatment of whitespace in data sections
 */
class XML_Beautifier_Renderer_Plain extends XML_Beautifier_Renderer
{
    /**
     * Serialize the XML tokens
     *
     * @param array $tokens XML tokens
     *
     * @return string XML document
     * @access public
     */
    function serialize($tokens)
    {
        $tokens = $this->normalize($tokens);
        
        $xml = '';
        $cnt = count($tokens);
        for ($i = 0; $i < $cnt; $i++) {
            $xml .= $this->_serializeToken($tokens[$i]);
        }
        return $xml;
    }

    /**
     * serialize a token
     *
     * This method does the actual beautifying.
     *
     * @param array $token structure that should be serialized
     *
     * @return mixed
     * @access private 
     * @todo split this method into smaller methods
     */
    function _serializeToken($token)
    {
        switch ($token["type"]) {

        /*
         * serialize XML Element
         */
        case XML_BEAUTIFIER_ELEMENT:
            $indent = $this->_getIndentString($token["depth"]);

            // adjust tag case
            if ($this->_options["caseFolding"] === true) {
                switch ($this->_options["caseFoldingTo"]) {
                case "uppercase":
                    $token["tagname"] = strtoupper($token["tagname"]);
                    $token["attribs"] = 
                        array_change_key_case($token["attribs"], CASE_UPPER);
                    break;
                case "lowercase":
                    $token["tagname"] = strtolower($token["tagname"]);
                    $token["attribs"] = 
                        array_change_key_case($token["attribs"], CASE_LOWER);
                    break;
                }
            }
                
            if ($this->_options["multilineTags"] == true) {
                $attIndent = $indent . str_repeat(" ", 
                    (2+strlen($token["tagname"])));
            } else {
                $attIndent = null;
            }

            // check for children
            switch ($token["contains"]) {
                    
            // contains only CData or is empty
            case XML_BEAUTIFIER_CDATA:
            case XML_BEAUTIFIER_EMPTY:
                if (sizeof($token["children"]) >= 1) {
                    $data = $token["children"][0]["data"];
                } else {
                    $data = '';
                }

                if (strstr($data, "\n")) {
                    $data = "\n" 
                        . $this->_indentTextBlock($data, $token['depth']+1, true);
                } 

                $xml = $indent 
                    . XML_Util::createTag($token["tagname"], 
                    $token["attribs"], $data, null, XML_UTIL_REPLACE_ENTITIES, 
                    $this->_options["multilineTags"], $attIndent)
                    . $this->_options["linebreak"];
                break;
                // contains mixed content
            default:
                $xml = $indent . XML_Util::createStartElement($token["tagname"], 
                    $token["attribs"], null, $this->_options["multilineTags"], 
                    $attIndent) . $this->_options["linebreak"];
                        
                $cnt = count($token["children"]);
                for ($i = 0; $i < $cnt; $i++) {
                    $xml .= $this->_serializeToken($token["children"][$i]);
                }
                $xml .= $indent . XML_Util::createEndElement($token["tagname"])
                    . $this->_options["linebreak"];
                break;
            break;
            }
            break;
            
        /*
         * serialize CData
         */
        case XML_BEAUTIFIER_CDATA:
            if ($token["depth"] > 0) {
                $xml = str_repeat($this->_options["indent"], $token["depth"]);
            } else {
                $xml = "";
            }

            $xml .= XML_Util::replaceEntities($token["data"]) 
                . $this->_options["linebreak"];
            break;      

        /*
         * serialize CData section
         */
        case XML_BEAUTIFIER_CDATA_SECTION:
            if ($token["depth"] > 0) {
                $xml = str_repeat($this->_options["indent"], $token["depth"]);
            } else {
                $xml = "";
            }

            $xml .= '<![CDATA['.$token["data"].']]>' . $this->_options["linebreak"];
            break;      

        /*
         * serialize entity
         */
        case XML_BEAUTIFIER_ENTITY:
            if ($token["depth"] > 0) {
                $xml = str_repeat($this->_options["indent"], $token["depth"]);
            } else {
                $xml = "";
            }
            $xml .= "&".$token["name"].";".$this->_options["linebreak"];
            break;      


        /*
         * serialize Processing instruction
         */
        case XML_BEAUTIFIER_PI:
            $indent = $this->_getIndentString($token["depth"]);

            $xml = $indent."<?".$token["target"].$this->_options["linebreak"]
                . $this->_indentTextBlock(rtrim($token["data"]), $token["depth"])
                . $indent."?>".$this->_options["linebreak"];
            break;      

        /*
         * comments
         */
        case XML_BEAUTIFIER_COMMENT:
            $lines = count(explode("\n", $token["data"]));
                
            /*
             * normalize comment, i.e. combine it to one
             * line and remove whitespace
             */
            if ($this->_options["normalizeComments"] && $lines > 1) {
                $comment = preg_replace("/\s\s+/s", " ", 
                    str_replace("\n", " ", $token["data"]));
                $lines   = 1;
            } else {
                $comment = $token["data"];
            }
    
            /*
             * check for the maximum length of one line
             */
            if ($this->_options["maxCommentLine"] > 0) {
                if ($lines > 1) {
                    $commentLines = explode("\n", $comment);
                } else {
                    $commentLines = array($comment);
                }
    
                $comment = "";
                for ($i = 0; $i < $lines; $i++) {
                    if (strlen($commentLines[$i]) 
                        <= $this->_options["maxCommentLine"]
                    ) {
                        $comment .= $commentLines[$i];
                        continue;
                    }
                    $comment .= wordwrap($commentLines[$i], 
                        $this->_options["maxCommentLine"]);
                    if ($i != ($lines-1)) {
                        $comment .= "\n";
                    }
                }
                    $lines = count(explode("\n", $comment));
            }

            $indent = $this->_getIndentString($token["depth"]);

            if ($lines > 1) {
                $xml = $indent . "<!--" . $this->_options["linebreak"]
                    . $this->_indentTextBlock($comment, $token["depth"]+1, true)
                    . $indent . "-->" . $this->_options["linebreak"];
            } else {
                $xml = $indent . sprintf("<!-- %s -->", trim($comment)) 
                    . $this->_options["linebreak"];
            }
            break;      

        /*
         * xml declaration
         */
        case XML_BEAUTIFIER_XML_DECLARATION:
            $indent = $this->_getIndentString($token["depth"]);
            $xml    = $indent . XML_Util::getXMLDeclaration($token["version"], 
                $token["encoding"], $token["standalone"]);
            break;      

        /*
         * xml declaration
         */
        case XML_BEAUTIFIER_DT_DECLARATION:
            $xml = $token["data"];
            break;      

        /*
         * all other elements
         */
        case XML_BEAUTIFIER_DEFAULT:
        default:
            $xml = XML_Util::replaceEntities($token["data"]);
            break;      
        }
        return $xml;
    }
}
?>
