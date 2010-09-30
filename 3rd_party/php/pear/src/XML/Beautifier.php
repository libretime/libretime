<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * XML_Beautifier
 *
 * XML Beautifier package
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
 * @version   CVS: $Id: Beautifier.php,v 1.15 2008/08/24 19:44:14 ashnazg Exp $
 * @link      http://pear.php.net/package/XML_Beautifier
 */


/**
 * define include path constant
 */
if (!defined('XML_BEAUTIFIER_INCLUDE_PATH')) {
    define('XML_BEAUTIFIER_INCLUDE_PATH', 'XML/Beautifier');
}

/**
 * element is empty
 */
define('XML_BEAUTIFIER_EMPTY', 0);

/**
 * CData
 */
define('XML_BEAUTIFIER_CDATA', 1);

/**
 * XML element
 */
define('XML_BEAUTIFIER_ELEMENT', 2);

/**
 * processing instruction
 */
define('XML_BEAUTIFIER_PI', 4);

/**
 * entity
 */
define('XML_BEAUTIFIER_ENTITY', 8);

/**
 * comment
 */
define('XML_BEAUTIFIER_COMMENT', 16);

/**
 * XML declaration
 */
define('XML_BEAUTIFIER_XML_DECLARATION', 32);

/**
 * doctype declaration
 */
define('XML_BEAUTIFIER_DT_DECLARATION', 64);

/**
 * cdata section
 */
define('XML_BEAUTIFIER_CDATA_SECTION', 128);

/**
 * default
 */
define('XML_BEAUTIFIER_DEFAULT', 256);

/**
 * overwrite the original file
 */
define('XML_BEAUTIFIER_OVERWRITE', -1);

/**
 * could not write to output file
 */
define('XML_BEAUTIFIER_ERROR_NO_OUTPUT_FILE', 151);

/**
 * could not load renderer
 */
define('XML_BEAUTIFIER_ERROR_UNKNOWN_RENDERER', 152);

/**
 * XML_Beautifier is a class that adds linebreaks and
 * indentation to your XML files. It can be used on XML
 * that looks ugly (e.g. any generated XML) to transform it 
 * to a nicely looking XML that can be read by humans.
 *
 * It removes unnecessary whitespace and adds indentation
 * depending on the nesting level.
 *
 * It is able to treat tags, data, processing instructions
 * comments, external entities and the XML prologue.
 *
 * XML_Beautifier is using XML_Beautifier_Tokenizer to parse an XML
 * document with a SAX based parser and builds tokens of tags, comments,
 * entities, data, etc.
 * These tokens will be serialized and indented by a renderer 
 * with your indent string.
 *
 * Example 1: Formatting a file
 * <code>
 * require_once 'XML/Beautifier.php';
 * $fmt = new XML_Beautifier();
 * $result = $fmt->formatFile('oldFile.xml', 'newFile.xml');
 * </code>
 *
 * Example 2: Formatting a string
 * <code>
 * require_once 'XML/Beautifier.php';
 * $xml = '<root><foo   bar = "pear"/></root>';
 * $fmt = new XML_Beautifier();
 * $result = $fmt->formatString($xml);
 * </code>
 *
 * @category  XML
 * @package   XML_Beautifier
 * @author    Stephan Schmidt <schst@php.net>
 * @copyright 2003-2008 Stephan Schmidt <schst@php.net>
 * @license   http://opensource.org/licenses/bsd-license New BSD License
 * @version   Release: 1.2.0
 * @link      http://pear.php.net/package/XML_Beautifier
 */
class XML_Beautifier
{
    /**
     * default options for the output format
     * @var    array
     * @access private
     */
     var $_defaultOptions = array(
         "removeLineBreaks"   => true,
         "removeLeadingSpace" => true,       // not implemented, yet
         "indent"             => "    ",
         "linebreak"          => "\n",
         "caseFolding"        => false,
         "caseFoldingTo"      => "uppercase",
         "normalizeComments"  => false,
         "maxCommentLine"     => -1,
         "multilineTags"      => false
      );

    /**
     * options for the output format
     * @var    array
     * @access private
     */
     var $_options = array();
    
    /**
     * Constructor
     *
     * This is only used to specify the options of the
     * beautifying process.
     *
     * @param array $options options that override default options
     *
     * @access public
     */   
    function XML_Beautifier($options = array())
    {
        $this->_options = array_merge($this->_defaultOptions, $options);
        $this->folding  = false;
    }

    /**
     * reset all options to default options
     *
     * @return void
     * @access public
     * @see setOption(), XML_Beautifier(), setOptions()
     */
    function resetOptions()
    {
        $this->_options = $this->_defaultOptions;
    }

    /**
     * set an option
     *
     * You can use this method if you do not want 
     * to set all options in the constructor
     *
     * @param string $name  option name
     * @param mixed  $value option value
     *
     * @return void
     * @access public
     * @see resetOptions(), XML_Beautifier(), setOptions()
     */
    function setOption($name, $value)
    {
        $this->_options[$name] = $value;
    }
    
    /**
     * set several options at once
     *
     * You can use this method if you do not want 
     * to set all options in the constructor
     *
     * @param array $options an options array
     *
     * @return void
     * @access   public
     * @see      resetOptions(), XML_Beautifier()
     */
    function setOptions($options)
    {
        $this->_options = array_merge($this->_options, $options);
    }

    /**
     * format a file or URL
     *
     * @param string $file     filename
     * @param mixed  $newFile  filename for beautified XML file 
     *                         (if none is given, the XML string 
     *                         will be returned).
     *                         if you want overwrite the original file, 
     *                         use XML_BEAUTIFIER_OVERWRITE
     * @param string $renderer Renderer to use, 
     *                         default is the plain xml renderer
     *
     * @return mixed XML string of no file should be written, 
     *               true if file could be written
     * @access public
     * @throws PEAR_Error
     * @uses _loadRenderer() to load the desired renderer
     * @todo PEAR CS - should require_once be include_once?
     */   
    function formatFile($file, $newFile = null, $renderer = "Plain")
    {
        if ($newFile == XML_BEAUTIFIER_OVERWRITE) {
            $newFile = $file;
        }

        /**
         * Split the document into tokens
         * using the XML_Tokenizer
         */
        require_once XML_BEAUTIFIER_INCLUDE_PATH . '/Tokenizer.php';
        $tokenizer = new XML_Beautifier_Tokenizer();
        
        $tokens = $tokenizer->tokenize($file, true);

        if (PEAR::isError($tokens)) {
            return $tokens;
        }
        
        $renderer = $this->_loadRenderer($renderer, $this->_options);

        if (PEAR::isError($renderer)) {
            return $renderer;
        }
        
        $xml = $renderer->serialize($tokens);
        
        if ($newFile == null) {
            return $xml;
        }
        
        $fp = @fopen($newFile, "w");
        if (!$fp) {
            return PEAR::raiseError("Could not write to output file", 
                XML_BEAUTIFIER_ERROR_NO_OUTPUT_FILE);
        }
        
        flock($fp, LOCK_EX);
        fwrite($fp, $xml);
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    }

    /**
     * format an XML string
     *
     * @param string $string   XML
     * @param string $renderer the renderer type
     *
     * @return string formatted XML string
     * @access public
     * @throws PEAR_Error
     * @todo PEAR CS - should require_once be include_once?
     */   
    function formatString($string, $renderer = "Plain")
    {
        /**
         * Split the document into tokens
         * using the XML_Tokenizer
         */
        require_once XML_BEAUTIFIER_INCLUDE_PATH . '/Tokenizer.php';
        $tokenizer = new XML_Beautifier_Tokenizer();
        
        $tokens = $tokenizer->tokenize($string, false);

        if (PEAR::isError($tokens)) {
            return $tokens;
        }

        $renderer = $this->_loadRenderer($renderer, $this->_options);

        if (PEAR::isError($renderer)) {
            return $renderer;
        }
        
        $xml = $renderer->serialize($tokens);
        
        return $xml;
    }

    /**
     * load a renderer
     *
     * Renderers are used to serialize the XML tokens back 
     * to an XML string.
     *
     * Renderers are located in the XML/Beautifier/Renderer directory.
     *
     * NOTE:  the "@" error suppression is used in this method
     *
     * @param string $name    name of the renderer
     * @param array  $options options for the renderer
     *
     * @return object renderer
     * @access private
     * @throws PEAR_Error
     */
    function &_loadRenderer($name, $options = array())
    {
        $file  = XML_BEAUTIFIER_INCLUDE_PATH . "/Renderer/$name.php";
        $class = "XML_Beautifier_Renderer_$name";

        @include_once $file;
        if (!class_exists($class)) {
            return PEAR::raiseError("Could not load renderer.", 
                XML_BEAUTIFIER_ERROR_UNKNOWN_RENDERER);
        }

        $renderer = &new $class($options);
        
        return $renderer;        
    }
    
    /**
     * return API version
     *
     * @access   public
     * @static
     * @return   string  $version API version
     */
    function apiVersion()
    {
        return "1.0";
    }
}
?>
