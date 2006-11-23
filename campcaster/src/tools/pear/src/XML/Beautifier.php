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

if( !defined( 'XML_BEAUTIFIER_INCLUDE_PATH' ) ) {
    define( 'XML_BEAUTIFIER_INCLUDE_PATH', 'XML/Beautifier' );
}

/**
 * XML/Beautifier.php
 *
 * Package that formats your XML files, that means
 * it is able to add line breaks, and indents your tags.
 *
 * @category XML
 * @package  XML_Beautifier
 * @author   Stephan Schmidt <schst@php.net>
 */

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
 * default
 */
define('XML_BEAUTIFIER_DEFAULT', 128);

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
 * @category XML
 * @package  XML_Beautifier
 * @version  1.0
 * @author   Stephan Schmidt <schst@php.net>
 */
class XML_Beautifier {

   /**
    * default options for the output format
    * @var    array
    * @access private
    */
    var $_defaultOptions = array(
                         "removeLineBreaks"  => true,
                         "removeLeadingSpace"=> true,       // not implemented, yet
                         "indent"            => "    ",
                         "linebreak"         => "\n",
                         "caseFolding"       => false,
                         "caseFoldingTo"     => "uppercase",
                         "normalizeComments" => false,
                         "maxCommentLine"    => -1,
                         "multilineTags"     => false
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
    * @access public
    * @param  array  $options   options that override default options
    */   
    function XML_Beautifier($options = array())
    {
        $this->_options = array_merge($this->_defaultOptions, $options);
        $this->folding = false;
    }

   /**
    * reset all options to default options
    *
    * @access   public
    * @see      setOption(), XML_Beautifier(), setOptions()
    */
    function resetOptions()
    {
        $this->_options = $this->_defaultOptions;
    }

   /**
    * set an option
    *
    * You can use this method if you do not want to set all options in the constructor
    *
    * @access   public
    * @see      resetOptions(), XML_Beautifier(), setOptions()
    */
    function setOption($name, $value)
    {
        $this->_options[$name] = $value;
    }
    
   /**
    * set several options at once
    *
    * You can use this method if you do not want to set all options in the constructor
    *
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
    * @access public
    * @param  string    $file       filename
    * @param  mixed     $newFile    filename for beautified XML file (if none is given, the XML string will be returned.)
    *                               if you want overwrite the original file, use XML_BEAUTIFIER_OVERWRITE
    * @param  string    $renderer   Renderer to use, default is the plain xml renderer
    * @return mixed                 XML string of no file should be written, true if file could be written
    * @throws PEAR_Error
    * @uses   _loadRenderer() to load the desired renderer
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
        
        $tokens = $tokenizer->tokenize( $file, true );

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
            return PEAR::raiseError("Could not write to output file", XML_BEAUTIFIER_ERROR_NO_OUTPUT_FILE);
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
    * @access public
    * @param  string    $string     XML
    * @return string    formatted XML string
    * @throws PEAR_Error
    */   
    function formatString($string, $renderer = "Plain")
    {
        /**
         * Split the document into tokens
         * using the XML_Tokenizer
         */
        require_once XML_BEAUTIFIER_INCLUDE_PATH . '/Tokenizer.php';
        $tokenizer = new XML_Beautifier_Tokenizer();
        
        $tokens = $tokenizer->tokenize( $string, false );

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
    * @access   private
    * @param    string  $renderer   name of the renderer
    * @param    array   $options    options for the renderer
    * @return   object              renderer
    * @throws   PEAR_Error
    */
    function &_loadRenderer($name, $options = array())
    {
        $file = XML_BEAUTIFIER_INCLUDE_PATH . "/Renderer/$name.php";
        $class = "XML_Beautifier_Renderer_$name";

        @include_once $file;
        if (!class_exists($class)) {
            return PEAR::raiseError( "Could not load renderer.", XML_BEAUTIFIER_ERROR_UNKNOWN_RENDERER );
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