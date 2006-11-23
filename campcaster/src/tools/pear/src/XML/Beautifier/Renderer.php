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
 * XML/Beautifier/Renderer.php
 *
 * @category XML
 * @package  XML_Beautifier
 * @author   Stephan Schmidt <schst@php.net>
 */

/**
 * Renderer base class for XML_Beautifier
 *
 * @category XML
 * @package  XML_Beautifier
 * @author   Stephan Schmidt <schst@php.net>
 */
class XML_Beautifier_Renderer {

   /**
    * options
    * @var array
    */
    var $_options = array();

   /**
    * create a new renderer
    *
    * @access   public
    * @param    array   options for the serialization
    */
    function XML_Beautifier_Renderer($options = array())
    {
        $this->_options = $options;
    }
    
   /**
    * Serialize the XML tokens
    *
    * @access   public
    * @param    array   XML tokens
    * @return   string  XML document
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
    * @access  public
    * @param   array       XML tree as returned by the tokenizer
    * @return  array       XML tree
    */
    function normalize($tokens)
    {
        $tmp    =   array();
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
    * @access   private
    * @param    array   $struct
    * @return   array   $struct
    */
    function _normalizeToken($token)
    {
        if ((isset($token["children"])) && !is_array($token["children"]) || empty($token["children"])) {
            return $token;
        }

        $children = $token["children"];
        $token["children"] = array();
        $cnt = count($children);
        $inCData = false;
        for ($i = 0; $i < $cnt; $i++ )
        {
            // no data section
            if ($children[$i]["type"] != XML_BEAUTIFIER_CDATA) {
                $children[$i] = $this->_normalizeToken($children[$i]);

                $inCData = false;
                array_push($token["children"], $children[$i]);
                continue;
            }

            /**
            * remove whitespace
            */
            if( $this->_options['removeLineBreaks'] == true )
            {
                $children[$i]['data'] = trim($children[$i]['data']);
                if( $children[$i]['data'] == '' ) {
                    continue;
                }
            }

            if ($inCData) {
                $tmp = array_pop($token["children"]);

                if( $children[$i]['data'] != '' ) {
                    if( $tmp['data'] != '' && $this->_options['removeLineBreaks'] == true ) {
                        $tmp['data'] .= ' ';
                    }
                    $tmp["data"] .= $children[$i]["data"];
                }
                array_push($token["children"], $tmp);
            } else {
                array_push($token["children"], $children[$i]);
            }

            $inCData = true;
        }
        return $token;
    }

   /**
    * indent a text block consisting of several lines
    *
    * @access private
    * @param  string    $text   textblock
    * @param  integer   $depth  depth to indent
    * @param  boolean   $trim   trim the lines
    * @return string            indented text block
    */
    function _indentTextBlock($text, $depth, $trim = false)
    {
        $indent = $this->_getIndentString($depth);
        $tmp = explode("\n", $text);
        $cnt = count($tmp);
		$xml = '';
        for ($i = 0; $i < $cnt; $i++ ) {
            if ($trim) {
                $tmp[$i] = trim($tmp[$i]);
            }
            if( $tmp[$i] == '' )
                continue;
            $xml .= $indent.$tmp[$i].$this->_options["linebreak"];
        }
        return $xml;
    }
    
   /**
    * get the string that is used for indentation in a specific depth
    *
    * This depends on the option 'indent'.
    *
    * @access private
    * @param  integer   $depth  nesting level
    * @return string            indent string
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