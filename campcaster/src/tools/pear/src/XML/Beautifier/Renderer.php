<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * XML_Beautifier/Renderer
 *
 * XML Beautifier's Rendere
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
 * @version   CVS: $Id: Renderer.php,v 1.6 2008/08/24 19:44:14 ashnazg Exp $
 * @link      http://pear.php.net/package/XML_Beautifier
 */

/**
 * Renderer base class for XML_Beautifier
 *
 * @category  XML
 * @package   XML_Beautifier
 * @author    Stephan Schmidt <schst@php.net>
 * @copyright 2003-2008 Stephan Schmidt <schst@php.net>
 * @license   http://opensource.org/licenses/bsd-license New BSD License
 * @version   Release: 1.2.0
 * @link      http://pear.php.net/package/XML_Beautifier
 */
class XML_Beautifier_Renderer
{
    /**
     * options
     * @var array
     */
    var $_options = array();

    /**
     * create a new renderer
     *
     * @param array $options for the serialization
     *
     * @access   public
     */
    function XML_Beautifier_Renderer($options = array())
    {
        $this->_options = $options;
    }
    
    /**
     * Serialize the XML tokens
     *
     * @param array $tokens XML tokens
     *
     * @return string XML document
     * @access public
     * @abstract
     */
    function serialize($tokens)
    {
        return  '';
    }

    /**
     * normalize the XML tree
     *
     * When normalizing an XML tree, adjacent data sections
     * are combined to one data section.
     *
     * @param array $tokens XML tree as returned by the tokenizer
     *
     * @return array XML tree
     * @access public
     */
    function normalize($tokens)
    {
        $tmp = array();
        foreach ($tokens as $token) {
            array_push($tmp, $this->_normalizeToken($token));
        }
        return $tmp;
    }

    /**
     * normalize one element in the XML tree
     *
     * This method will combine all data sections of an element.
     *
     * @param array $token token array
     *
     * @return array $struct
     * @access private
     */
    function _normalizeToken($token)
    {
        if ((isset($token["children"])) 
            && !is_array($token["children"]) 
            || empty($token["children"])
        ) {
            return $token;
        }

        $children          = $token["children"];
        $token["children"] = array();
        $cnt               = count($children);
        $currentMode       = 0;
        for ($i = 0; $i < $cnt; $i++ ) {
            // no data section
            if ($children[$i]["type"] != XML_BEAUTIFIER_CDATA 
                && $children[$i]["type"] != XML_BEAUTIFIER_CDATA_SECTION
            ) {
                $children[$i] = $this->_normalizeToken($children[$i]);

                $currentMode = 0;
                array_push($token["children"], $children[$i]);
                continue;
            }

            /*
             * remove whitespace
             */
            if ($this->_options['removeLineBreaks'] == true) {
                $children[$i]['data'] = trim($children[$i]['data']);
                if ($children[$i]['data'] == '') {
                    continue;
                }
            }

            if ($currentMode == $children[$i]["type"]) {
                $tmp = array_pop($token["children"]);

                if ($children[$i]['data'] != '') {
                    if ($tmp['data'] != '' 
                        && $this->_options['removeLineBreaks'] == true
                    ) {
                        $tmp['data'] .= ' ';
                    }
                    $tmp["data"] .= $children[$i]["data"];
                }
                array_push($token["children"], $tmp);
            } else {
                array_push($token["children"], $children[$i]);
            }

            $currentMode = $children[$i]["type"];
        }
        return $token;
    }

    /**
     * indent a text block consisting of several lines
     *
     * @param string  $text  textblock
     * @param integer $depth depth to indent
     * @param boolean $trim  trim the lines
     *
     * @return string indented text block
     * @access private
     */
    function _indentTextBlock($text, $depth, $trim = false)
    {
        $indent = $this->_getIndentString($depth);
        $tmp    = explode("\n", $text);
        $cnt    = count($tmp);
        $xml    = '';
        for ($i = 0; $i < $cnt; $i++ ) {
            if ($trim) {
                $tmp[$i] = trim($tmp[$i]);
            }
            if ($tmp[$i] == '') {
                continue;
            }
            $xml .= $indent.$tmp[$i].$this->_options["linebreak"];
        }
        return $xml;
    }
    
    /**
     * get the string that is used for indentation in a specific depth
     *
     * This depends on the option 'indent'.
     *
     * @param integer $depth nesting level
     *
     * @return string indent string
     * @access private
     */
    function _getIndentString($depth)
    {
        if ($depth > 0) {
            return str_repeat($this->_options["indent"], $depth);
        }
        return "";
    }
}
?>
