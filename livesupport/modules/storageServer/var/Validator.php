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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/Validator.php,v $

------------------------------------------------------------------------------*/
define('VAL_ROOT', 110);
define('VAL_NOREQ', 111);
define('VAL_NOONEOF', 112);
define('VAL_UNKNOWNE', 113);
define('VAL_UNKNOWNA', 114);
define('VAL_NOTDEF', 115);

#require_once "";

/**
 *  Simple XML validator against structure stored in PHP hash-array hierarchy.
 *
 *  It should be replaced by XML schema validation in the future.
 */
class Validator{
    /**
     *
     */
    var $format = '';
    /**
     *
     */
    var $data = '';
    /**
     *
     */
    var $anode = '';
    /**
     *
     */
    function Validator($format)
    {
        $this->format = $format;
    }

    /**
     *
     */
    function validate(&$data)
    {
        $this->data =& $data;
        $r = $this->validateNode($data, $this->format['_root']);
        return $r;
    }

    /**
     *
     */
    function validateNode(&$node, $fname)
    {
        $dname = strtolower(($node->ns? $node->ns.":" : '').$node->name);
        $format =& $this->format;
        if(DEBUG) echo"\nVAL::validateNode: 1 $dname/$fname\n";
        if($dname != $fname) return $this->_err(VAL_ROOT);
        if(!isset($format[$fname])) return $this->_err(VAL_NOTDEF, $fname);
        $attrs = array();
        foreach($node->attrs as $i=>$attr){
            $attrs[$attr->name] =& $node->attrs[$i];
            $listr = $this->isListedAs($fname, $attr->name, 'attrs', 'required');
            $listi = $this->isListedAs($fname, $attr->name, 'attrs', 'implied');
            $listn = $this->isListedAs($fname, $attr->name, 'attrs', 'normal');
            if($listr===FALSE && $listi===FALSE && $listn===FALSE)
                return $this->_err(VAL_UNKNOWNA, $attr->name);
        }
        if(isset($format[$fname]['attrs'])){
            $fattrs =& $format[$fname]['attrs'];
            if(isset($fattrs['required'])){
                foreach($fattrs['required'] as $i=>$attr){
                    if(!isset($childs[$attr])) return $this->_err(VAL_NOREQ, $attr);
                }
            }
        }
        $childs = array();
        foreach($node->children as $i=>$ch){
            $chname = strtolower(($ch->ns? $ch->ns.":" : '').$ch->name);
            $childs[$chname] =& $node->children[$i];
            $listo = $this->isListedAs($fname, $chname, 'childs', 'optional');
            $listr = $this->isListedAs($fname, $chname, 'childs', 'required');
            $list1 = $this->isListedAs($fname, $chname, 'childs', 'oneof');
            if($listo===FALSE && $listr===FALSE && $list1===FALSE)
                return $this->_err(VAL_UNKNOWNE, $chname);
        }
        //var_dump($childs);
        if(isset($format[$fname]['childs'])){
            $fchilds =& $format[$fname]['childs'];
            if(isset($fchilds['required'])){
                foreach($fchilds['required'] as $i=>$ch){
                    if(!isset($childs[$ch])) return $this->_err(VAL_NOREQ, $ch);
                }
            }
            if(isset($fchilds['oneof'])){
                $one = FALSE;
                foreach($fchilds['oneof'] as $i=>$ch){
                    if(isset($childs[$ch])) $one = TRUE;
                }
                if(!$one) return $this->_err(VAL_NOONEOF);
            }
        }
        foreach($childs as $chname=>$ch){
            $r = $this->validateNode($childs[$chname], $chname);
            if(PEAR::isError($r)) return $r;
        }
        return TRUE;
    }

    /**
     *
     */
    function isListedAs($fname, $chname, $nType='childs', $reqType='required')
    {
        $format =& $this->format;
        $listed = (
            isset($format[$fname][$nType][$reqType]) ?
            array_search($chname, $format[$fname][$nType][$reqType]) :
            FALSE
        );
        return $listed;
    }

    /**
     *
     */
    function _err($errno, $par='')
    {
        $msg = array(
            110=>'Wrong root element',
            111=>'Required object missing',
            112=>'One-of object missing',
            113=>'Unknown element',
            114=>'Unknown attribute',
            115=>'Not defined',
        );
        return PEAR::raiseError(
            "Validator: {$msg[$errno]} #$errno ($par)"
        );
    }

    /**
     *
     * /
    function ()
    {
    }
    */
}

?>
