<?php
define('VAL_ROOT', 110);
define('VAL_NOREQE', 111);
define('VAL_NOONEOF', 112);
define('VAL_UNKNOWNE', 113);
define('VAL_UNKNOWNA', 114);
define('VAL_NOTDEF', 115);
define('VAL_UNEXPONEOF', 116);
define('VAL_FORMAT', 117);
define('VAL_CONTENT', 118);
define('VAL_NOREQA', 119);
define('VAL_ATTRIB', 120);
define('VAL_PREDXML', 121);

/**
 *  Simple XML validator against structure stored in PHP hash-array hierarchy.
 *
 *  Basic format files:
 *  <ul>
 *      <li>audioClipFormat.php</li>
 *      <li>webstreamFormat.php</li>
 *      <li>playlistFormat.php</li>
 *  </ul>
 *  It probably should be replaced by XML schema validation in the future.
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class Validator {
    /**
     * Format type of validated document
     * @var string
     */
    private $format = NULL;

    /**
     * Preloaded format tree structure
     * @var array
     */
    private $formTree = NULL;

    /**
     * Gunid of validated file for identification in mass input
     * @var string
     */
    private $gunid = NULL;


    /**
     * Constructor
     *
     * @param string $format
     * 		format type of validated document
     * @param string $gunid
     * 		gunid of validated file for identification in mass input
     */
    public function __construct($format, $gunid)
    {
        $format = strtolower($format);
        $this->format = $format;
        $this->gunid = $gunid;
        $formats = array(
            'audioclip' => "audioClipFormat",
            'playlist'  => "playlistFormat",
            'webstream' => "webstreamFormat",
        );
        if (!isset($formats[$format])) {
        	return $this->_err(VAL_FORMAT);
        }
        $formatName = $formats[$format];
        $formatFile = dirname(__FILE__)."/$formatName.php";
        if (!file_exists($formatFile)) {
        	return $this->_err(VAL_FORMAT);
        }
        require($formatFile);
        $this->formTree = $$formatName;
    }


    /**
     * Validate document - only wrapper for validateNode method
     *
     * @param object $data
     * 		validated object tree
     * @return mixed
     * 		TRUE or PEAR::error
     */
    function validate(&$data)
    {
        $r = $this->validateNode($data, $this->formTree['_root']);
        return $r;
    }


    /**
     * Validate one metadata value (on insert/update)
     *
     * @param string $fname
     * 		parent element name
     * @param string $category
     * 		qualif.category name
     * @param string $predxml
     * 		'A' | 'T' (attr or tag)
     * @param string $value
     * 		validated element value
     * @return TRUE|PEAR_Error
     */
    function validateOneValue($fname, $category, $predxml, $value)
    {
        $formTree =& $this->formTree;
        switch ($predxml) {
            case 'T':
                if (!$this->isChildInFormat($fname, $category)) {
                    return $this->_err(VAL_UNKNOWNE, "$category in $fname");
                }
                break;
            case 'A':
                if (!$this->isAttrInFormat($fname, $category)) {
                    return $this->_err(VAL_UNKNOWNA, "$category in $fname");
                }
                break;
            case 'N':
                return TRUE;
                break;
            default:
                return $this->_err(VAL_PREDXML, $predxml);
        }
        if (isset($formTree[$category]['regexp'])) {
            // echo "XXX {$formTree[$fname]['regexp']} / ".$node->content."\n";
            if (!preg_match("|{$formTree[$category]['regexp']}|", $value)) {
                return $this->_err(VAL_CONTENT, "$category/$value");
            }
        }
    }


    /**
     * Validation of one element node from object tree
     *
     * @param object $node
     * 		validated node
     * @param string $fname
     * 		actual name in format structure
     * @return mixed
     * 		TRUE or PEAR::error
     */
    function validateNode(&$node, $fname)
    {
        $dname = (($node->ns? $node->ns.":" : '').$node->name);
        $formTree =& $this->formTree;
        if (DEBUG) {
        	echo"\nVAL::validateNode: 1 $dname/$fname\n";
        }
        // check root node name:
        if ($dname != $fname) {
        	return $this->_err(VAL_ROOT, $fname);
        }
        // check if this element is defined in format:
        if (!isset($formTree[$fname])) {
        	return $this->_err(VAL_NOTDEF, $fname);
        }
        // check element content
        if (isset($formTree[$fname]['regexp'])) {
            // echo "XXX {$formTree[$fname]['regexp']} / ".$node->content."\n";
            if (!preg_match("|{$formTree[$fname]['regexp']}|", $node->content)) {
                return $this->_err(VAL_CONTENT, "$fname/{$node->content}");
            }
        }
        // validate attributes:
        $ra = $this->validateAttributes($node, $fname);
        if (PEAR::isError($ra)) {
        	return $ra;
        }
        // validate children:
        $r = $this->validateChildren($node, $fname);
        if (PEAR::isError($r)) {
        	return $r;
        }
        return TRUE;
    }


    /**
     * Validation of attributes
     *
     * @param object $node
     * 		validated node
     * @param string $fname
     * 		actual name in format structure
     * @return mixed
     * 		TRUE or PEAR::error
     */
    function validateAttributes(&$node, $fname)
    {
        $formTree =& $this->formTree;
        $attrs = array();
        // check if all attrs are permitted here:
        foreach ($node->attrs as $i => $attr) {
            $aname = (($attr->ns? $attr->ns.":" : '').$attr->name);
            $attrs[$aname] =& $node->attrs[$i];
            if (!$this->isAttrInFormat($fname, $aname)) {
                return $this->_err(VAL_UNKNOWNA, $aname);
            }
            // check attribute format
            // echo "XXA $aname\n";
            if (isset($formTree[$aname]['regexp'])) {
                // echo "XAR {$formTree[$fname]['regexp']} / ".$node->content."\n";
                if (!preg_match("|{$formTree[$aname]['regexp']}|", $attr->val)) {
                    return $this->_err(VAL_ATTRIB, "$aname [".var_export($attr->val,TRUE)."]");
                }
            }
        }
        // check if all required attrs are here:
        if (isset($formTree[$fname]['attrs'])) {
            $fattrs =& $formTree[$fname]['attrs'];
            if (isset($fattrs['required'])) {
                foreach ($fattrs['required'] as $i => $attr) {
                    if (!isset($attrs[$attr])) {
                        return $this->_err(VAL_NOREQA, $attr);
                    }
                }
            }
        }
        return TRUE;
    }


    /**
     * Validation children nodes
     *
     * @param object $node
     * 		validated node
     * @param string $fname
     * 		actual name in format structure
     * @return mixed
     * 		TRUE or PEAR::error
     */
    function validateChildren(&$node, $fname)
    {
        $formTree =& $this->formTree;
        $childs = array();
        // check if all children are permitted here:
        foreach ($node->children as $i => $ch) {
            $chname = (($ch->ns? $ch->ns.":" : '').$ch->name);
            // echo "XXE $chname\n";
            if (!$this->isChildInFormat($fname, $chname)) {
                return $this->_err(VAL_UNKNOWNE, $chname);
            }
            // call children recursive:
            $r = $this->validateNode($node->children[$i], $chname);
            if (PEAR::isError($r)) {
            	return $r;
            }
            $childs[$chname] = TRUE;
        }
        // check if all required children are here:
        if (isset($formTree[$fname]['childs'])) {
            $fchilds =& $formTree[$fname]['childs'];
            if (isset($fchilds['required'])) {
                foreach ($fchilds['required'] as $i => $ch) {
                    if (!isset($childs[$ch])) return $this->_err(VAL_NOREQE, $ch);
                }
            }
            // required one from set
            if (isset($fchilds['oneof'])) {
                $one = FALSE;
                foreach ($fchilds['oneof'] as $i => $ch) {
                    if (isset($childs[$ch])) {
                        if ($one) {
                        	return $this->_err(VAL_UNEXPONEOF, "$ch in $fname");
                        }
                        $one = TRUE;
                    }
                }
                if (!$one) {
                	return $this->_err(VAL_NOONEOF);
                }
            }
        }
        return TRUE;
    }


    /**
     * Test if child is presented in format structure
     *
     * @param string $fname
     * 		node name in format structure
     * @param string $chname
     * 		child node name
     * @return boolean
     */
    function isChildInFormat($fname, $chname)
    {
        $listo = $this->isInFormatAs($fname, $chname, 'childs', 'optional');
        $listr = $this->isInFormatAs($fname, $chname, 'childs', 'required');
        $list1 = $this->isInFormatAs($fname, $chname, 'childs', 'oneof');
        return ($listo!==FALSE || $listr!==FALSE || $list1!==FALSE);
    }


    /**
     * Test if attribute is presented in format structure
     *
     * @param string $fname
     * 		node name in format structure
     * @param string $aname
     * 		attribute name
     * @return boolean
     */
    function isAttrInFormat($fname, $aname)
    {
        $listr = $this->isInFormatAs($fname, $aname, 'attrs', 'required');
        $listi = $this->isInFormatAs($fname, $aname, 'attrs', 'implied');
        $listn = $this->isInFormatAs($fname, $aname, 'attrs', 'normal');
        return ($listr!==FALSE || $listi!==FALSE || $listn!==FALSE);
    }


    /**
     * Check if node/attribute is presented in format structure
     *
     * @param string $fname
     * 		node name in format structure
     * @param string $chname
     * 		node/attribute name
     * @param string $nType
     * 		'childs' | 'attrs'
     * @param string $reqType
     * 		<ul>
     *          <li>for elements: 'required' | 'optional' | 'oneof'</li>
     *          <li>for attributes: 'required' | 'implied' | 'normal'</li>
     *      </ul>
     * @return mixed
     * 		boolean/int (index int format array returned if found)
     */
    function isInFormatAs($fname, $chname, $nType='childs', $reqType='required')
    {
        $formTree =& $this->formTree;
        $listed = (
            isset($formTree[$fname][$nType][$reqType]) ?
            array_search($chname, $formTree[$fname][$nType][$reqType]) :
            FALSE
        );
        return $listed;
    }


    /**
     * Error exception generator
     *
     * @param int $errno
     * 		erron code
     * @param string $par
     * 		optional string for more descriptive  error messages
     * @return PEAR_Error
     */
    function _err($errno, $par='')
    {
        $msg = array(
            VAL_ROOT        => 'Wrong root element',
            VAL_NOREQE      => 'Required element missing',
            VAL_NOONEOF     => 'One-of element missing',
            VAL_UNKNOWNE    => 'Unknown element',
            VAL_UNKNOWNA    => 'Unknown attribute',
            VAL_NOTDEF      => 'Not defined',
            VAL_UNEXPONEOF  => 'Unexpected second object from one-of set',
            VAL_FORMAT      => 'Unknown format',
            VAL_CONTENT     => 'Invalid content',
            VAL_NOREQA      => 'Required attribute missing',
            VAL_ATTRIB      => 'Invalid attribute format',
            VAL_PREDXML     => 'Invalid predicate type',
        );
        return PEAR::raiseError(
            "Validator: {$msg[$errno]} #$errno ($par, gunid={$this->gunid})",
            $errno
        );
    }


} // class Validator

?>