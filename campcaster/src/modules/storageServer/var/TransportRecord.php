<?php

define('TR_LEAVE_CLOSED', TRUE);

/**
 * Auxiliary class for transport records
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision: 1946 $
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class TransportRecord
{
	/**
	 * @var DB
	 */
    //public $dbc;

    /**
     * @var GreenBox
     */
    private $gb;

    /**
     * @var array
     */
    //private $config;

    /**
     * @var Transport
     */
    private $tr;

    /**
     * @var boolean
     */
    private $recalled = FALSE;

    /**
     * @var boolean
     */
    private $dropped = FALSE;


    /**
     * @param Transport $tr
     * @return TransportRecord
     */
    public function __construct(&$tr)
    {
        $this->tr =& $tr;
        $this->gb =& $tr->gb;
    }


    /**
     * Factory method
     *
     * @param Transport $tr
     * @param string $trtype
     * 		transport type (see Transport::install)
     * @param string $direction
     * 		'up' | 'down'
     * @param array $defaults
     * 		default parameters (optional, internal use)
     * @return TransportRecord
     */
    function create(&$tr, $trtype, $direction='up', $defaults=array())
    {
        global $CC_DBC, $CC_CONFIG;
        $trec = new TransportRecord($tr);
        $trec->trtok = $trtok = $tr->_createTransportToken();
        $trec->row = array_merge($defaults,
            array('trtype'=>$trtype, 'direction'=>$direction));
        $trec->recalled = TRUE;
        if (!isset($defaults['title'])) {
            $defaults['title'] = $trec->getTitle();
            if (PEAR::isError($defaults['title'])) {
            	return $defaults['title'];
            }
        }
        $id = $CC_DBC->nextId($CC_CONFIG['transTable']."_id_seq");
        $names  = "id, trtok, direction, state, trtype, start, ts";
        $values = "$id, '$trtok', '$direction', 'init', '$trtype', now(), now()";
        foreach ($defaults as $k => $v) {
            $sqlVal = $trec->_getSqlVal($k, $v);
            $names .= ", $k";
            $values .= ", $sqlVal";
        }
        $query = "
            INSERT INTO ".$CC_CONFIG['transTable']."
                ($names)
            VALUES
                ($values)
        ";
        $res = $CC_DBC->query($query);
        if (PEAR::isError($res)) {
        	return $res;
        }
        return $trec;
    }


    /**
     * Recall transport record from DB
     *
     * @param Transport $tr
     * @param string $trtok
     * 		transport token
     * @return TransportRecord
     */
    function recall(&$tr, $trtok)
    {
        global $CC_DBC, $CC_CONFIG;
        $trec = new TransportRecord($tr);
        $trec->trtok = $trtok;
        $row = $CC_DBC->getRow("
            SELECT
                id, trtok, state, trtype, direction,
                to_hex(gunid)as gunid, to_hex(pdtoken)as pdtoken,
                fname, localfile, url, rtrtok, mdtrtok, uid,
                expectedsize, realsize, expectedsum, realsum,
                errmsg, title, jobpid
            FROM ".$CC_CONFIG['transTable']."
            WHERE trtok='$trtok'
        ");
        if (PEAR::isError($row)) {
        	return $row;
        }
        if (is_null($row)) {
            return PEAR::raiseError("TransportRecord::recall:".
                " invalid transport token ($trtok)", TRERR_TOK
            );
        }
        $row['pdtoken'] = StoredFile::NormalizeGunid($row['pdtoken']);
        $row['gunid'] = StoredFile::NormalizeGunid($row['gunid']);
        $trec->row = $row;
        $trec->recalled = TRUE;
        return $trec;
    }


    /**
     * Set state of transport record
     *
     * @param string $newState
     * @param array $data
     * 		other data fields to set
     * @param string $oldState
     * 		check old state and do nothing if differ
     * @param boolean $lock
     * 		check lock and do nothing if differ
     * @return boolean success
     */
    function setState($newState, $data=array(), $oldState=NULL, $lock=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        $set = " state='$newState', ts=now()";
        if (!is_null($lock)) {
            $slock = ($lock ? 'Y' : 'N');
            $nlock = (!$lock);
            $snlock = ($nlock ? 'Y' : 'N');
            $set .= ", lock='$snlock'";
        }
        foreach ($data as $k => $v) {
            $set .= ", $k=".$this->_getSqlVal($k, $v);
        }
        $r = $CC_DBC->query("
            UPDATE ".$CC_CONFIG['transTable']."
            SET $set
            WHERE trtok='{$this->trtok}'".
            (is_null($oldState) ? '' : " AND state='$oldState'").
            (is_null($lock) ? '' : " AND lock = '$slock'")
        );
        if (PEAR::isError($r)) {
        	return $r;
        }
        // return TRUE;
        $affRows = $CC_DBC->affectedRows();
        if (PEAR::isError($affRows)) {
        	return $affRows;
        }
        return ($affRows == 1);
    }


    /**
     * Return state of transport record
     *
     * @return string
     * 		state
     */
    function getState()
    {
        if (!$this->recalled) {
            return PEAR::raiseError("TransportRecord::getState:".
                " not recalled ({$this->trtok})", TRERR_TOK
            );
        }
        return $this->row['state'];
    }


    /**
     * Set lock on transport record and save/clear process id
     *
     * @param boolean $lock
     * 		lock if true, release lock if false
     * @param int $pid
     * 		process id
     * @return mixed
     * 		true or error
     */
    function setLock($lock, $pid=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        $pidsql = (is_null($pid) ? "NULL" : "$pid" );
        if ($this->dropped) {
        	return TRUE;
        }
        $slock = ($lock ? 'Y' : 'N');
        $nlock = (!$lock);
        $snlock = ($nlock ? 'Y' : 'N');
        $r = $CC_DBC->query("
            UPDATE ".$CC_CONFIG['transTable']."
            SET lock='$slock', jobpid=$pidsql, ts=now()
            WHERE trtok='{$this->trtok}' AND lock = '$snlock'"
        );
        if (PEAR::isError($r)) {
        	return $r;
        }
        $affRows = $CC_DBC->affectedRows();
        if (PEAR::isError($affRows)) {
        	return $affRows;
        }
        if ($affRows === 0) {
            $ltxt = ($lock ? 'lock' : 'unlock' );
            return PEAR::raiseError(
                "TransportRecord::setLock: can't $ltxt ({$this->trtok})"
            );
        }
        return TRUE;
    }


    /**
     * Return type of transport
     *
     * @return string
     * 		Transport type
     */
    function getTransportType()
    {
        if (!$this->recalled) {
            return PEAR::raiseError("TransportRecord::getTransportType:".
                " not recalled ({$this->trtok})", TRERR_TOK
            );
        }
        return $this->row['trtype'];
    }


    /**
     * Kill transport job (on pause or cancel)
     *
     * @return string
     * 		Transport type
     */
    function killJob()
    {
        if (!$this->recalled) {
            return PEAR::raiseError("TransportRecord::getTransportType:".
                " not recalled ({$this->trtok})", TRERR_TOK
            );
        }
        $jobpid = $this->row['jobpid'];
        $res = system("pkill -P $jobpid", $status);
    }


    /**
     * Set state to failed and set error message in transport record
     *
     * @param string $txt
     * 		base part of error message
     * @param PEAR_Error $eo
     * 		(opt.) error msg can be construct from it
     * @return mixed
     * 		boolean true or error
     */
    function fail($txt='', $eo=NULL)
    {
        if (!$this->recalled) {
            return PEAR::raiseError("TransportRecord::fail:".
                " not recalled ({$this->trtok})", TRERR_TOK
            );
        }
        $msg = $txt;
        if (!is_null($eo)) {
            $msg .= $eo->getMessage()." ".$eo->getUserInfo().
            " [".$eo->getCode()."]";
        }
        $r = $this->setState('failed', array('errmsg'=>$msg));
        if (PEAR::isError($r)) {
        	return $r;
        }
        return TRUE;
    }


    /**
     * Close transport record
     *
     * @return mixed
     * 		boolean true or error
     */
    function close()
    {
        global $CC_CONFIG, $CC_DBC;
        if (!$this->recalled) {
            return PEAR::raiseError("TransportRecord::close:".
                " not recalled ({$this->trtok})", TRERR_TOK
            );
        }
        if (TR_LEAVE_CLOSED) {
            $r = $this->setState('closed');
            if (PEAR::isError($r)) {
            	return $r;
            }
        } else {
            $r = $CC_DBC->query("
                DELETE FROM ".$CC_CONFIG['transTable']."
                WHERE trtok='{$this->trtok}'
            ");
            if (PEAR::isError($r)) {
            	return $r;
            }
            $this->recalled = FALSE;
            $this->dropped  = TRUE;
        }
        return TRUE;
    }


    /**
     * Add field specific envelopes to values (e.g. ' around strings)
     *
     * @param string $fldName
     * 		field name
     * @param mixed $fldVal
     * 		field value
     * @return string
     */
    function _getSqlVal($fldName, $fldVal)
    {
        switch ($fldName) {
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
                $fldVal = pg_escape_string($fldVal);
                return "'$fldVal'";
                break;
        }
    }


    /**
     * Get title from transported object's metadata (if exists)
     *
     * @return string
     * 		the title or descriptive string
     */
    function getTitle()
    {
        $defStr = 'unknown';
        $trtype = $this->getTransportType();   //contains recall check
        if (PEAR::isError($trtype)) {
        	return $trtype;
        }
        switch ($trtype) {
            case "audioclip":
            case "playlist":
            case "playlistPkg":
            case "metadata":
                $title = $this->gb->bsGetTitle(NULL, $this->row['gunid']);
                if (is_null($title)) {
                    $title = $defStr;
                }
                if (PEAR::isError($title)) {
                    if ($title->getCode() == GBERR_FOBJNEX) {
                    	$title = $defStr;
                    } else {
                    	return $title;
                    }
                }
                break;
            case "searchjob":
                $title = 'searchjob';
                break;
            case "file":
                $title = ( isset($this->row['localfile']) ?
                    basename($this->row['localfile']) : 'regular file');
                break;
            default:
            	$title = $defStr;
        }
        return $title;
    }

} // class TransportRecord
?>