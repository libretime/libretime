<?php
/**
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 */
require_once "XML/Util.php";

/* ================================================================== Element */

/**
 * Object representation of one XML element
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @see MetaData
 */
class XmlElement {
    /**
     * Namespace prefix
     * @var string
     */
    public $ns;

    /**
     * Element name
     * @var string
     */
    public $name;

    /**
     * Attributes
     * @var array
     */
    public $attrs = array();

    /**
     * Namespace definitions
     * @var array
     */
    public $nSpaces = array();

    /**
     * Child nodes
     * @var array
     */
    public $children = array();

    /**
     * Text content of element
     * @var string
     */
    public $content = '';


    /**
     * @param string $fullname
     *		Fully qualified name of element
     * @param array $attrs
     * 		hash of attributes
     * @param array $nSpaces
     * 		hash of namespace definitions
     * @param array $children
     * 		hash of child nodes
     */
    public function __construct($fullname, $attrs, $nSpaces=array(), $children=array())
    {
        $a = XML_Util::splitQualifiedName($fullname);
        $this->ns = $a['namespace'];
        $this->name = $a['localPart'];
        $this->attrs = $attrs;
        $this->nSpaces = $nSpaces;
        $this->children = $children;
    }
} // class XmlElement


/* ================================================================ Attribute */
/**
 * Object representation of one XML attribute
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @see MetaData
 */
class XmlAttrib {
    /**
     * Namespace prefix
     * @var string
     */
    public $ns;

    /**
     * Attribute name
     * @var string
     */
    public $name;

    /**
     * Attribute value
     * @var string
     */
    public $val;

    /**
     * @param string $atns
     * 		namespace prefix
     * @param string $atnm
     * 		attribute name
     * @param string $atv
     * 		attribute value
     */
    public function __construct($atns, $atnm, $atv)
    {
        $this->ns = $atns;
        $this->name = $atnm;
        $this->val = $atv;
    }
} // fn XmlAttrib


/* =================================================================== Parser */
/**
 *  XML parser object encapsulation
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @see MetaData
 */
class XmlParser {
    /**
     * Tree of nodes
     * @var array
     */
    private $tree = NULL;

    /**
     * Parse stack
     * @var array
     */
    private $stack = array();

    /**
     * Error structure
     * @var array
     */
    private $err = array(FALSE, '');

    /**
     * @param string $data
     * 		XML string to be parsed
     */
    public function __construct($data){
        $xml_parser = xml_parser_create('UTF-8');
        xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, FALSE);
        xml_set_object($xml_parser, $this);
        xml_set_element_handler($xml_parser, "startTag", "endTag");
        xml_set_character_data_handler($xml_parser, 'characterData');
        $res = xml_parse($xml_parser, $data, TRUE);
        if (!$res) {
            $this->err = array(TRUE,
                sprintf("XML error: %s at line %d\n",
                    xml_error_string(xml_get_error_code($xml_parser)),
                    xml_get_current_line_number($xml_parser)
                )
            );
//            var_dump($data);
        }
        xml_parser_free($xml_parser);
    }


    /**
     *  Parse XML file or string
     *
     *  @param string $data
     * 		local path to XML file or XML string
     *  @param string $loc
     * 		location: 'file'|'string'
     *  @return array
     * 		reference, parse result tree (or PEAR::error)
     */
    function &parse($data='', $loc='file')
    {
        switch ($loc) {
	        case "file":
	            if (!is_file($data)) {
	                return PEAR::raiseError(
	                    "XmlParser::parse: file not found ($data)"
	                );
	            }
	            if (!is_readable($data)) {
	                return PEAR::raiseError(
	                    "XmlParser::parse: can't read file ($data)"
	                );
	            }
	            $data = file_get_contents($data);
	        case "string":
	            $parser = new XmlParser($data);
	            if ($parser->isError()) {
	                return PEAR::raiseError(
	                    "XmlParser::parse: ".$parser->getError()
	                );
	            }
	            $tree = $parser->getTree();
	            break;
	        default:
	            return PEAR::raiseError(
	                "XmlParser::parse: unsupported source location ($loc)"
	            );
        }
        return $tree;
    }


    /**
     * Start tag handler
     *
     * @param resource $parser
     * 		reference to parser resource
     * @param string $fullname
     * 		element name
     * @param array $attrs
     * 		array of attributes
     * @return none
     */
    function startTag($parser, $fullname, $attrs) {
        $nSpaces = array();
        foreach ($attrs as $atn => $atv) {
            $a    = XML_Util::splitQualifiedName($atn);
            $atns = $a['namespace'];
            $atnm = $a['localPart'];
            unset($attrs[$atn]);
            if ($atns == 'xmlns') {
            	$nSpaces[$atnm] = $atv;
            } else if ($atns == NULL && $atnm == 'xmlns') {
                $nSpaces[''] = $atv;
            } else {
                $attrs[$atn] = new XmlAttrib($atns, $atnm, $atv);
            }
        }
        $el = new XmlElement($fullname, $attrs, $nSpaces);
        array_push($this->stack, $el);
    }


    /**
     * End tag handler
     *
     * @param resource $parser
     * 		reference to parser resource
     * @param string $fullname
     * 		element name
     * @return none
     */
    function endTag($parser, $fullname) {
        $cnt = count($this->stack);
        if ($cnt > 1) {
            $this->stack[$cnt-2]->children[] = $this->stack[$cnt-1];
            $lastEl = array_pop($this->stack);
        } else {
            $this->tree = $this->stack[0];
        }
    }


    /**
     * Character data handler
     *
     * @param resource $parser
     * 		reference to parser resource
     * @param string $data
     * @return none
     */
    function characterData($parser, $data) {
        $cnt = count($this->stack);
        if (trim($data)!='') {
        	$this->stack[$cnt-1]->content .= $data;
        }
    }


    /**
     * Default handler
     *
     * @param resource $parser
     * 		reference to parser resource
     * @param string $data
     * @return none
     */
    function defaultHandler($parser, $data)
    {
        $cnt = count($this->stack);
        //if(substr($data, 0, 1) == "&" && substr($data, -1, 1) == ";"){
        //    $this->stack[$cnt-1]->content .= trim($data);
        //}else{
            $this->stack[$cnt-1]->content .= "*** $data ***";
        //}
    }


    /**
     *  Return result tree
     *
     *  @return array
     * 		tree structure
     */
    function getTree()
    {
        return $this->tree;
    }


    /**
     *  Return error string
     *
     *  @return boolean
     * 		whether error occured
     */
    function isError()
    {
        return $this->err[0];
    }


    /**
     * Return error string
     *
     * @return string
     * 		error message
     */
    function getError()
    {
        return $this->err[1];
    }


    /* ----------------------------------- auxiliary methos for serialization */
    /**
     *  Serialize metadata of one file
     *
     *  @return string, serialized XML
     */
    function serialize()
    {
        $res  = '<?xml version="1.0" encoding="utf-8"?>';
        $res .= $this->serializeEl($this->tree);
        $res .= "\n";
        return $res;
    }


    /**
     *  Serialize one metadata element
     *
     *  @param el object, element object
     *  @param lvl int, level for indentation
     *  @return string, serialized XML
     */
    function serializeEl($el, $lvl=0)
    {
        $ind    = str_repeat(" ", $lvl);
        $elNs   = $el->ns;
        $elName = $el->name;
        $attrs  = XML_Util::attributesToString($el->attrs);
        $fullName   = ($elNs=='' ? '' : "$elNs:")."$elName";
        $res  = "\n{$ind}<{$fullName}{$attrs}>";
        $haveCh = (count($el->children)>0);
        foreach ($el->children as $ch) {
            $res .= $this->serializeEl($ch, $lvl+1);
        }
        $res .= XML_Util::replaceEntities("{$el->content}");
        if ($haveCh) {
        	$res .= "\n{$ind}";
        }
        $res .= "</{$fullName}>";
        return $res;
    }


    /* -------------------------------------------------------- debug methods */
    /**
     *  Debug dump of tree
     *
     *  @return hash, tree structure
     */
    function dump()
    {
        var_dump($this->tree);
    }

}
?>