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
 
 
    Author   : $Author: tomash $
    Version  : $Revision: 1946 $
    Location : $URL: svn+ssh://tomash@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/modules/storageServer/var/Transport.php $

------------------------------------------------------------------------------*/
define('TR_LEAVE_CLOSED', TRUE);

/**
 *  Auxiliary class for ransport records
 *
 **/
class TransportRecord
{
    var $dbc;
    var $recalled = FALSE;
    var $dropped  = FALSE;

    /**
     *  Constructor
     *
     *  @param tr Transport object reference
     *  @return TransportRecord object instance
     */
    function TransportRecord(&$tr)
    {
        $this->tr         =& $tr;
        $this->gb         =& $tr->gb;
        $this->dbc        =& $tr->gb->dbc;
        $this->config     = $tr->gb->config;
        $this->transTable = $tr->gb->config['tblNamePrefix'].'trans';
    }

    /**
     * Factory method
     *
     *  @param tr: Transport object reference
     *  @param trtype: string transport type (see Transport::install)
     *  @param direction: string - 'up' | 'down'
     *  @param defaults: array - default parameters (optional, internal use)
     *  @return TransportRecord instance
     */
    function create(&$tr, $trtype, $direction='up', $defaults=array())
    {
        $trec =& new TransportRecord($tr);
        $trec->trtok = $trtok = $tr->_createTrtok();
        $trec->row = array_merge($defaults,
            array('trtype'=>$trtype, 'direction'=>$direction));
        $trec->recalled = TRUE;
        if(!isset($defaults['title'])){
            $defaults['title'] = $r = $trec->getTitle();
            if(PEAR::isError($r)) return $r;
        }
        $id = $trec->dbc->nextId("{$trec->transTable}_id_seq");
        $names  = "id, trtok, direction, state, trtype, start, ts";
        $values = "$id, '$trtok', '$direction', 'init', '$trtype', now(), now()";
        foreach($defaults as $k=>$v){
            $sqlVal = $trec->_getSqlVal($k, $v);
            $names  .= ", $k";
            $values .= ", $sqlVal";
        }
        $res = $r = $trec->dbc->query("
            INSERT INTO {$trec->transTable}
                ($names)
            VALUES
                ($values)
        ");
        if(PEAR::isError($r)) return $r;
        return $trec;
    }
    
    /**
     *  Recall transport record from DB
     *
     *  @param tr: Transport object reference
     *  @param trtok: string - transport token
     *  @return TransportRecord instance
     */
    function recall(&$tr, $trtok)
    {
        $trec =& new TransportRecord($tr);
        $trec->trtok = $trtok;
        $row = $r = $trec->dbc->getRow("
            SELECT
                id, trtok, state, trtype, direction,
                to_hex(gunid)as gunid, to_hex(pdtoken)as pdtoken,
                fname, localfile, url, rtrtok, mdtrtok, uid,
                expectedsize, realsize, expectedsum, realsum,
                errmsg, title
            FROM {$trec->transTable}
            WHERE trtok='$trtok'
        ");
        if(PEAR::isError($r)){ return $r; }
        if(is_null($row)){
            return PEAR::raiseError("TransportRecord::recall:".
                " invalid transport token ($trtok)", TRERR_TOK
            );
        }
        $row['pdtoken'] = StoredFile::_normalizeGunid($row['pdtoken']);
        $row['gunid'] = StoredFile::_normalizeGunid($row['gunid']);
        $trec->row = $row;
        $trec->recalled = TRUE;
        return $trec;
    }
    
    /**
     *  Set state of transport record
     *  
     *  @param newState: string
     *  @param data: array - other data fields to set
     *  @param oldState: string, (opt.) check old state and do nothing if differ
     *  @param lock: boolean, (opt.) check lock and do nothing if differ
     *  @return boolean success
     */
    function setState($newState, $data=array(), $oldState=NULL, $lock=NULL)
    {
        $set = " state='$newState', ts=now()";
        if(!is_null($lock)){
            $slock = ($lock ? 'Y' : 'N');
            $nlock = (!$lock);
            $snlock = ($nlock ? 'Y' : 'N');
            $set .= ", lock='$snlock'";
        }
        foreach($data as $k=>$v){
            $set .= ", $k=".$this->_getSqlVal($k, $v);
        }
        $r = $this->dbc->query("
            UPDATE {$this->transTable}
            SET $set
            WHERE trtok='{$this->trtok}'".
            (is_null($oldState) ? '' : " AND state='$oldState'").
            (is_null($lock) ? '' : " AND lock = '$slock'")
        );
        if(PEAR::isError($r)){ return $r; }
        // return TRUE;
        $affRows = $r = $this->dbc->affectedRows();
        if(PEAR::isError($r)){ return $r; }
        return ($affRows == 1);
    }
    
    /**
     *  Return state of transport record
     *
     *  @return string - state
     */
    function getState()
    {
        if(!$this->recalled){
            return PEAR::raiseError("TransportRecord::getState:".
                " not recalled ({$this->trtok})", TRERR_TOK
            );
        }
        return $this->row['state'];
    }
    
    /**
     *  Set lock on transport record
     *
     *  @param lock: boolean - lock if true, release lock if false
     *  @return boolean true or error
     */
    function setLock($lock)
    {
        if($this->dropped) return TRUE;
        $slock = ($lock ? 'Y' : 'N');
        $nlock = (!$lock);
        $snlock = ($nlock ? 'Y' : 'N');
        $r = $this->dbc->query("
            UPDATE {$this->transTable}
            SET lock='$slock', ts=now()
            WHERE trtok='{$this->trtok}' AND lock = '$snlock'"
        );
        if(PEAR::isError($r)){ return $r; }
        $affRows = $r = $this->dbc->affectedRows();
        if(PEAR::isError($r)){ return $r; }
        if($affRows != 1){
            $ltxt = ($lock ? 'lock' : 'unlock' );
            return PEAR::raiseError(
                "TransportRecord::setLock: can't $ltxt ({$this->trtok})"
            );
        }
        return TRUE;
    }
    
    /**
     *  Return type of transport
     *
     *  @return string - trtype
     */
    function getTransportType()
    {
        if(!$this->recalled){
            return PEAR::raiseError("TransportRecord::getTransportType:".
                " not recalled ({$this->trtok})", TRERR_TOK
            );
        }
        return $this->row['trtype'];
    }
    
    /**
     *  Set state to failed and set error message in transport record
     *
     *  @param txt: string - base part of error message
     *  @param eo: PEAR error object - (opt.) error msg can be construct from it
     *  @return  boolean true or error
     */
    function fail($txt='', $eo=NULL)
    {
        if(!$this->recalled){
            return PEAR::raiseError("TransportRecord::fail:".
                " not recalled ({$this->trtok})", TRERR_TOK
            );
        }
        $msg = $txt;
        if(!is_null($eo)){
            $msg .= $eo->getMessage()." ".$eo->getUserInfo().
            " [".$eo->getCode()."]";
        }
        $r = $this->setState('failed', array('errmsg'=>$msg));
        if(PEAR::isError($r)){ return $r; }
        return TRUE;
    }

    /**
     *  Close transport record
     *
     *  @return boolean true or error
     */
    function close()
    {
        if(!$this->recalled){
            return PEAR::raiseError("TransportRecord::close:".
                " not recalled ({$this->trtok})", TRERR_TOK
            );
        }
        if(TR_LEAVE_CLOSED){
            $r = $this->setState('closed');
            if(PEAR::isError($r)){ return $r; }
        }else{
            $r = $this->dbc->query("
                DELETE FROM {$this->transTable}
                WHERE trtok='{$this->trtok}'
            ");
            if(PEAR::isError($r)){ return $r; }
            $this->recalled = FALSE;
            $this->dropped  = TRUE;
        }
        return TRUE;
    }

    /**
     *  Add field specific envelopes to values (e.g. ' around strings)
     *
     *  @param fldName: string - field name
     *  @param fldVal: mixed - field value
     *  @return string
     */
    function _getSqlVal($fldName, $fldVal)
    {
        switch($fldName){
            case 'realsize':
            case 'expectedsize':
            case 'uid':
                return ("$fldVal"!='' ? "$fldVal" : "NULL");
                break;
            case 'gunid':
            case 'pdtoken':
                return "x'$fldVal'::bigint";
                break;
            default:
                return "'$fldVal'";
                break;
        }
    }

    /**
     *  Get title from transported object's metadata (if exists)
     *
     *  @return string - the title or descriptive string
     */
    function getTitle()
    {
        $defStr = 'unknown';
        $trtype = $r = $this->getTransportType();   //contains recall check
        if(PEAR::isError($r)){ return $r; }
        switch($trtype){
            case"audioclip":
            case"playlist":
            case"playlistPkg":
            case"metadata":
                $title = $r = $this->gb->bsGetTitle(NULL, $this->row['gunid']);
                if(PEAR::isError($r)){
                    if($r->getCode()==GBERR_FOBJNEX) $title = $defStr;
                    else return $r;
                }
                break;
            case"searchjob":
                $title = 'searchjob';
                break;
            case"file":
                $title = ( isset($this->row['localfile']) ?
                    basename($this->row['localfile']) : 'regular file');
                break;
            default: $title = $defStr;
        }
        return $title;
    }
    
}
?>