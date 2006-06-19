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


    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/

/* ================================================================= includes */
#require_once 'DB.php';
#require_once "XML/RPC.php";
#include_once "../conf.php";

/* ================================================== method definition array */
/**
 *  Array with methods description
 *
 *  Each element has method name as key and contains four subfields:
 *  <ul>
 *   <li>m</li> full method name (include optional prefix)
 *   <li>p</li> array of input parameter names
 *   <li>t</li> array of input parameter types
 *   <li>r</li> array of result element names (not used there at present)
 *   <li>e</li> array of error codes/messages (not used there at present)
 *  </ul>
 */
$mdefs = array(
    "listMethods"       => array('m'=>"system.listMethods", 'p'=>NULL, 't'=>NULL),

    "DisplayScheduleMethod" => array(
        'm'=>'displaySchedule',
        'p'=>array('sessionId'/*string*/, 'from'/*datetime*/, 'to'/*datetime*/),
        't'=>array('string', 'dateTime.iso8601', 'dateTime.iso8601'),
        'r'=>array(array('id'/*int*/, 'playlistId'/*string*/, 'start'/*datetime*/, 'end'/*datetime*/)),
        'e'=>array(
            '1101'=>'invalid argument format',
            '1102'=>"missing or invalid 'from' argument",
            '1103'=>"missing or invalid 'to' argument",
            '1120'=>'missing session ID argument',
        )
    ),
    "GeneratePlayReportMethod" => array(
        'm'=>'generatePlayReport',
        'p'=>array('sessionId'/*string*/, 'from'/*datetime*/, 'to'/*datetime*/),
        't'=>array('string', 'dateTime.iso8601', 'dateTime.iso8601'),
        'r'=>array(array('audioClipId'/*string*/, 'timestamp'/*datetime*/)),
        'e'=>array(
            '1501'=>'invalid argument format',
            '1502'=>"missing or invalid 'from' argument",
            '1503'=>"missing or invalid 'to' argument",
            '1520'=>'missing session ID argument',
        )
    ),
    "GetSchedulerTimeMethod" => array(
        'm'=>'getSchedulerTime',
        'p'=>array(),
        't'=>array(),
        'r'=>array('schedulerTime'/*datetime*/),
        'e'=>array()
    ),
    "RemoveFromScheduleMethod" => array(
        'm'=>'removeFromSchedule',
        'p'=>array('sessionId'/*string*/, 'scheduleEntryId'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array(),
        'e'=>array(
            '1201'=>'invalid argument format',
            '1202'=>'missing schedule entry ID argument',
            '1203'=>'schedule entry not found',
            '1220'=>'missing session ID argument',
        )
    ),
    "RescheduleMethod" => array(
        'm'=>'reschedule',
        'p'=>array('sessionId'/*string*/, 'scheduleEntryId'/*string*/, 'playtime'/*datetime*/),
        't'=>array('string', 'string', 'dateTime.iso8601'),
        'r'=>array(),
        'e'=>array(
            '1301'=>'invalid argument format',
            '1302'=>'missing schedule entry ID argument',
            '1303'=>'missing playtime argument',
            '1304'=>'schedule entry not found',
            '1305'=>'could not reschedule entry',
            '1320'=>'missing session ID argument',
        )
    ),
    "UploadPlaylistMethod" => array(
        'm'=>'uploadPlaylist',
        'p'=>array('sessionId'/*string*/, 'playlistId'/*string*/, 'playtime'/*datetime*/),
        't'=>array('string', 'string', 'dateTime.iso8601'),
        'r'=>array('scheduleEntryId'/*string*/),
        'e'=>array(
            '1401'=>'invalid argument format',
            '1402'=>'missing playlist ID argument',
            '1403'=>'missing playtime argument',
            '1404'=>'playlist not found',
            '1405'=>'timeframe not aaaaavailable',
            '1406'=>'could not schedule playlist',
            '1420'=>'missing session ID argument',
        )
    ),
    "ExportOpenMethod" => array(
        'm'=>'exportOpen',
        'p'=>array('sessionId'/*string*/, 'from'/*datetime*/, 'to'/*datetime*/, 'criteria'/*struct*/),
        't'=>array('string', 'dateTime.iso8601', 'dateTime.iso8601', 'struct'),
        'r'=>array('schedulerExportToken'/*string*/),
        'e'=>array(
            '1601'=>'invalid argument format',
            '1602'=>"missing or invalid 'from' argument",
            '1603'=>"missing or invalid 'to' argument",
            '1604'=>"missing or invalid 'criteria' argument",
            '1620'=>'missing session ID argument',
        )
    ),
    "ExportCheckMethod" => array(
        'm'=>'exportCheck',
        'p'=>array('sessionId'/*string*/, 'token'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('status'/*string*/),
        'e'=>array(
            '1701'=>'invalid argument format',
            '1702'=>"missing or invalid 'token' argument",
            '1720'=>'missing session ID argument',
        )
    ),
    "ExportCloseMethod" => array(
        'm'=>'exportClose',
        'p'=>array('sessionId'/*string*/, 'token'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('status'/*boolean*/),
        'e'=>array(
            '1801'=>'invalid argument format',
            '1802'=>"missing or invalid 'token' argument",
            '1820'=>'missing session ID argument',
        )
    ),
    "ImportOpenMethod" => array(
        'm'=>'importOpen',
        'p'=>array('sessionId'/*string*/, 'filename'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('schedulerImportToken'/*string*/),
        'e'=>array(
            '1901'=>'invalid argument format',
            '1902'=>"missing or invalid 'filename' argument",
            '1920'=>'missing session ID argument',
        )
    ),
    "ImportCheckMethod" => array(
        'm'=>'importCheck',
        'p'=>array('sessionId'/*string*/, 'token'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('status'/*string*/),
        'e'=>array(
            '2001'=>'invalid argument format',
            '2002'=>"missing or invalid 'token' argument",
            '2020'=>'missing session ID argument',
        )
    ),
    "ImportCloseMethod" => array(
        'm'=>'importClose',
        'p'=>array('sessionId'/*string*/, 'token'/*string*/),
        't'=>array('string', 'string'),
        'r'=>array('status'/*boolean*/),
        'e'=>array(
            '2101'=>'invalid argument format',
            '2102'=>"missing or invalid 'token' argument",
            '2120'=>'missing session ID argument',
        )
    ),
);

/* ======================================================== class definitions */

class SchedulerPhpClient{
    /**
     *  Databases object reference
     */
    var $dbc = NULL;
    /**
     *  Array with methods description
     */
    var $mdefs = array();
    /**
     *  Confiduration array from ../conf.php
     */
    var $config = array();
    /**
     *  XMLRPC client object reference
     */
    var $client = NULL;
    /**
     *  Verbosity flag
     */
    var $verbose = FALSE;
    /**
     *  XMLRPC debug flag
     */
    var $debug = 0;
    /**
     *  Constructor - pelase DON'T CALL IT, use factory method instead
     *
     *  @param dbc object, database object reference
     *  @param mdefs array, hash array with methods description
     *  @param config array, hash array with configuration
     *  @param debug int, XMLRPC debug flag
     *  @param verbose boolean, verbosity flag
     *  @return this
     */
    function SchedulerPhpClient(
        &$dbc, $mdefs, $config, $debug=0, $verbose=FALSE)
    {
        $this->dbc = $dbc;
        $this->mdefs = $mdefs;
        $this->config = $config;
        $this->debug = $debug;
        $this->verbose = $verbose;
        $confPrefix = "scheduler";
        # $confPrefix = "storage";
        $serverPath =
          "http://{$config["{$confPrefix}UrlHost"]}:{$config["{$confPrefix}UrlPort"]}".
          "{$config["{$confPrefix}UrlPath"]}/{$config["{$confPrefix}XMLRPC"]}";
        #$serverPath = "http://localhost:80/livesupportStorageServerCVS/xmlrpc/xrLocStor.php";
        if($this->verbose) echo "serverPath: $serverPath\n";
        $url = parse_url($serverPath);
        $this->client = new XML_RPC_Client($url['path'], $url['host'], $url['port']);
    }

    /**
     *  Factory, create object instance
     *
     *  In fact it doesn't create instance of SchedulerPhpClient, but
     *  dynamically extend this class with set of methods based on $mdefs array
     *  (using eval function) and instantiate resulting class
     *  SchedulerPhpClientCore instead.
     *  Each new method in this subclass accepts parameters according to $mdefs
     *  array, call wrapper callMethod(methodname, parameters) and return its
     *  result.
     *
     *  @param dbc object, database object reference
     *  @param mdefs array, hash array with methods description
     *  @param config array, hash array with configuration
     *  @param debug int, XMLRPC debug flag
     *  @param verbose boolean, verbosity flag
     *  @return object, created object instance
     */
    function &factory(&$dbc, $mdefs, $config, $debug=0, $verbose=FALSE){
        $f = '';
        foreach($mdefs as $fn=>$farr){
            $f .=
                '    function '.$fn.'(){'."\n".
                '        $pars = func_get_args();'."\n".
                '        $r = $this->callMethod("'.$fn.'", $pars);'."\n".
                '        return $r;'."\n".
                '    }'."\n";
        }
        $e =
            "class SchedulerPhpClientCore extends SchedulerPhpClient{\n".
            "$f\n".
            "}\n";
#        echo $e;
        if(FALSE === eval($e)) return $dbc->raiseError("Eval failed");
        $spc =& new SchedulerPhpClientCore(
            $dbc, $mdefs, $config, $debug, $verbose);
        return $spc;
    }

    /**
     *  XMLRPC methods wrapper
     *  Encode XMLRPC request message, send it, receive and decode response.
     *
     *  @param method string, method name
     *  @param gettedPars array, returned by func_get_args() in called method
     *  @return array, PHP hash with response
     */
    function callMethod($method, $gettedPars)
    {
        $parr = array();
        $XML_RPC_val = new XML_RPC_Value;
        foreach($this->mdefs[$method]['p'] as $i=>$p){
            $parr[$p] = new XML_RPC_Value;
            switch ($this->mdefs[$method]['t'][$i]) { // switch ($parr[$p]->kindOf($gettedPars[$i])) {
                /* array type: normal array */
                case 'array':
                    $parr[$p]->addArray($gettedPars[$i]);
                    break;
                /* stuct type: assoc. array */
                case 'struct':
                    $parr[$p]->addStruct($gettedPars[$i]);
                    break;
                /* scalar types: 'int' | 'boolean' | 'string' | 'double' | 'dateTime.iso8601' | 'base64'*/
                default: 
                    $parr[$p]->addScalar($gettedPars[$i], $this->mdefs[$method]['t'][$i]);
            } 
        }
        $XML_RPC_val->addStruct($parr);
        $fullmethod = $this->mdefs[$method]['m'];
        $msg = new XML_RPC_Message($fullmethod, array($XML_RPC_val));
        if($this->verbose){
            echo "parr:\n";
            var_dump($parr);
            echo "message:\n";
            echo $msg->serialize()."\n";
        }
        $this->client->setDebug($this->debug);
        if (!$res = $this->client->send($msg)) {
            return array('error' => array('code' => -1, 'message' => '##Cannot connect to Scheduler##'));
        }
        if($res->faultCode() > 0) {
            return array('error' => array('code' => $res->faultCode(), 'message' => $res->faultString()));   ## changed by sebastian
            /*
            tomas� orig. method
            return $this->dbc->raiseError(
                "SchedulerPhpClient::$method:".$res->faultString()." ".
                $res->faultCode()."\n", $res->faultCode()
            );

            newer method:
            return PEAR::raiseError(
                "SchedulerPhpClient::$method:".$res->faultString()." ".
                $res->faultCode()."\n", $res->faultCode(),
                PEAR_ERROR_RETURN
            );
            */
        }
        if($this->verbose){
            echo "result:\n";
            echo $res->serialize();
        }
        $val = $res->value();
#        echo"<pre>\n"; var_dump($val); exit;
        $resp = XML_RPC_decode($res->value());
        return $resp;
    }

}

/* ======================================================== class definitions */
?>
