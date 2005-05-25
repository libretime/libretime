<?php
/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the LiveSupport project.
    http://livesupport.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    LiveSupport is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    LiveSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with LiveSupport; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author: tomas $
    Version  : $Revision: 1.8 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/Validator.php,v $

------------------------------------------------------------------------------*/
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
 */
class Validator{
    /**
     *  string - format type of validated document
     */
    var $format = NULL;
    /**
     *  Preloaded format tree structure
     */
    var $formTree = NULL;
    /**
     *  string - gunid of validated file for identification in mass input
     */
    var $gunid = NULL;
    /**
     *  Constructor
     *
     *  @param format string - format type of validated document
     *  @param gunid string - gunid of validated file for identification
     *          in mass input
     */
    function Validator($format, $gunid)
    {
        $this->format   = $format;
        $this->gunid        = $gunid;
        $formats = array(
            'audioclip' => "audioClipFormat",
            'playlist'  => "playlistFormat",
            'webstream' => "webstreamFormat",
        );
        if(!isset($formats[$format])) return $this->_err(VAL_FORMAT);
        $formatName = $formats[$format];
        $formatFile = dirname(__FILE__)."/$formatName.php";
        if(!file_exists($formatFile)) return $this->_err(VAL_FORMAT);
        require $formatFile;
        $this->formTree = $$formatName;
    }

    /**
     *  Validate document - only wrapper for validateNode method
     *
     *  @param data object, validated object tree
     *  @return TRUE or PEAR::error
     */
    function validate(&$data)
    {
        $r = $this->validateNode($data, $this->formTree['_root']);
        return $r;
    }

    /**
     *  Validate one metadata value (on insert/update)
     *
     *  @param fname string - parent element name
     *  @param category string - qualif.category name
     *  @param predxml string - 'A' | 'T' (attr or tag)
     *  @param value string - validated element value
     *  @return TRUE or PEAR::error
     */
    function validateOneValue($fname, $category, $predxml, $value)
    {
        $formTree =& $this->formTree;
        switch($predxml){
            case'T':
                if(!$this->isChildInFormat($fname, $category))
                    return $this->_err(VAL_UNKNOWNE, "$category in $fname");
                break;
            case'A':
                if(!$this->isAttrInFormat($fname, $category))
                    return $this->_err(VAL_UNKNOWNA, "$category in $fname");
                break;
            case'N':
                return TRUE;
                break;
            default:
                return $this->_err(VAL_PREDXML, $predxml);
        }
        if(isset($formTree[$category]['regexp'])){
            // echo "XXX {$formTree[$fname]['regexp']} / ".$node->content."\n";
            if(!preg_match("|{$formTree[$category]['regexp']}|", $value))
                return $this->_err(VAL_CONTENT, $category);
        }
        
    }

    /**
     *  Validation of one element node from object tree
     *
     *  @param node object - validated node
     *  @param fname string - aktual name in format structure
     *  @return TRUE or PEAR::error
     */
    function validateNode(&$node, $fname)
    {
        $dname = (($node->ns? $node->ns.":" : '').$node->name);
        $formTree =& $this->formTree;
        if(DEBUG) echo"\nVAL::validateNode: 1 $dname/$fname\n";
        // check root node name:
        if($dname != $fname) return $this->_err(VAL_ROOT, $fname);
        // check if this element is defined in format:
        if(!isset($formTree[$fname])) return $this->_err(VAL_NOTDEF, $fname);
        // check element content
        if(isset($formTree[$fname]['regexp'])){
            // echo "XXX {$formTree[$fname]['regexp']} / ".$node->content."\n";
            if(!preg_match("|{$formTree[$fname]['regexp']}|", $node->content))
                return $this->_err(VAL_CONTENT, $fname);
        }
        // validate attributes:
        $ra = $this->validateAttributes($node, $fname);
        if(PEAR::isError($ra)) return $ra;
        // validate children:
        $r = $this->validateChildren($node, $fname);
        if(PEAR::isError($r)) return $r;
        return TRUE;
    }

    /**
     *  Validation of attributes
     *
     *  @param node object - validated node
     *  @param fname string - aktual name in format structure
     *  @return TRUE or PEAR::error
     */
    function validateAttributes(&$node, $fname)
    {
        $formTree =& $this->formTree;
        $attrs = array();
        // check if all attrs are permitted here:
        foreach($node->attrs as $i=>$attr){
            $aname = (($attr->ns? $attr->ns.":" : '').$attr->name);
            $attrs[$aname] =& $node->attrs[$i];
            if(!$this->isAttrInFormat($fname, $aname))
                return $this->_err(VAL_UNKNOWNA, $aname);
            // check attribute format
            // echo "XXA $aname\n";
            if(isset($formTree[$aname]['regexp'])){
                // echo "XAR {$formTree[$fname]['regexp']} / ".$node->content."\n";
                if(!preg_match("|{$formTree[$aname]['regexp']}|", $attr->val))
                    return $this->_err(VAL_ATTRIB, $aname);
            }
        }
        // check if all required attrs are here:
        if(isset($formTree[$fname]['attrs'])){
            $fattrs =& $formTree[$fname]['attrs'];
            if(isset($fattrs['required'])){
                foreach($fattrs['required'] as $i=>$attr){
                    if(!isset($attrs[$attr]))
                        return $this->_err(VAL_NOREQA, $attr);
                }
            }
        }
        return TRUE;
    }

    /**
     *  Validation children nodes
     *
     *  @param node object - validated node
     *  @param fname string - aktual name in format structure
     *  @return TRUE or PEAR::error
     */
    function validateChildren(&$node, $fname)
    {
        $formTree =& $this->formTree;
        $childs = array();
        // check if all children are permitted here:
        foreach($node->children as $i=>$ch){
            $chname = (($ch->ns? $ch->ns.":" : '').$ch->name);
            // echo "XXE $chname\n";
            if(!$this->isChildInFormat($fname, $chname))
                return $this->_err(VAL_UNKNOWNE, $chname);
            // call children recursive:
            $r = $this->validateNode($node->children[$i], $chname);
            if(PEAR::isError($r)) return $r;
            $childs[$chname] = TRUE;
        }
        // check if all required children are here:
        if(isset($formTree[$fname]['childs'])){
            $fchilds =& $formTree[$fname]['childs'];
            if(isset($fchilds['required'])){
                foreach($fchilds['required'] as $i=>$ch){
                    if(!isset($childs[$ch])) return $this->_err(VAL_NOREQE, $ch);
                }
            }
            // required one from set
            if(isset($fchilds['oneof'])){
                $one = FALSE;
                foreach($fchilds['oneof'] as $i=>$ch){
                    if(isset($childs[$ch])){
                        if($one) return $this->_err(
                            VAL_UNEXPONEOF, "$ch in $fname");
                        $one = TRUE;
                    }
                }
                if(!$one) return $this->_err(VAL_NOONEOF);
            }
        }
        return TRUE;
    }

    /**
     *  Test if child is presented in format structure
     *
     *  @param fname string - node name in format structure
     *  @param chname string - child node name
     *  @return boolean
     */
    function isChildInFormat($fname, $chname)
    {
        $listo = $this->isInFormatAs($fname, $chname, 'childs', 'optional');
        $listr = $this->isInFormatAs($fname, $chname, 'childs', 'required');
        $list1 = $this->isInFormatAs($fname, $chname, 'childs', 'oneof');
        return ($listo!==FALSE || $listr!==FALSE || $list1!==FALSE);
    }

    /**
     *  Test if attribute is presented in format structure
     *
     *  @param fname string - node name in format structure
     *  @param aname string - attribute name
     *  @return boolean
     */
    function isAttrInFormat($fname, $aname)
    {
        $listr = $this->isInFormatAs($fname, $aname, 'attrs', 'required');
        $listi = $this->isInFormatAs($fname, $aname, 'attrs', 'implied');
        $listn = $this->isInFormatAs($fname, $aname, 'attrs', 'normal');
        return ($listr!==FALSE || $listi!==FALSE || $listn!==FALSE);
    }

    /**
     *  Check if node/attribute is presented in format structure
     *
     *  @param fname string - node name in format structure
     *  @param chname string - node/attribute name
     *  @param nType string - 'childs' | 'attrs'
     *  @param reqType string - <ul>
     *          <li>for elements: 'required' | 'optional' | 'oneof'</li>
     *          <li>for attributes: 'required' | 'implied' | 'normal'</li>
     *      </ul>
     *  @return boolean/int (index int format array returned if found)
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
     *  Error exception generator
     *
     *  @param errno int - erron code
     *  @param par string - optional string for more descriptive  error messages
     *  @return PEAR::error
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

    /**
     *
     *
     *  @param
     *  @return
     * /
    function ()
    {
    }
    */
}

?>
