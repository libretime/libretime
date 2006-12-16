<?php
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
 *
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
        'm'=>'createBackupOpen',
        'p'=>array('sessionId'/*string*/, 'criteria'/*struct*/, 'from'/*datetime*/, 'to'/*datetime*/),
        't'=>array('string', 'struct', 'dateTime.iso8601', 'dateTime.iso8601'),
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
        'm'=>'createBackupCheck',
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
        'm'=>'createBackupClose',
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
/**
 * @author Sebastian Gobel <sebastian.goebel@web.de>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class SchedulerPhpClient {
    /**
     * Array with methods description
     * @var array
     */
    private $mdefs = array();

    /**
     * XMLRPC client object reference
     * @var XMLRPC_Client
     */
    private $client = NULL;

    /**
     * Verbosity flag
     * @var boolean
     */
    private $verbose = FALSE;

    /**
     * XMLRPC debug flag
     * @var boolean
     */
    private $debug = 0;

    /**
     * Constructor - please DON'T CALL IT, use factory method instead
     *
     * @param array $mdefs
     *		hash array with methods description
     * @param int $debug
     *		XMLRPC debug flag
     * @param boolean $verbose
     * 		verbosity flag
     */
    public function __construct($mdefs, $debug=0, $verbose=FALSE)
    {
        global $CC_CONFIG;
        $this->mdefs = $mdefs;
        $this->debug = $debug;
        $this->verbose = $verbose;
        $confPrefix = "scheduler";
        $serverPath = "http://{$CC_CONFIG["{$confPrefix}UrlHost"]}:{$CC_CONFIG["{$confPrefix}UrlPort"]}".
                      "{$CC_CONFIG["{$confPrefix}UrlPath"]}/{$CC_CONFIG["{$confPrefix}XMLRPC"]}";
        if ($this->verbose) {
        	echo "serverPath: $serverPath\n";
        }
        $url = parse_url($serverPath);
        $this->client = new XML_RPC_Client($url['path'], $url['host'], $url['port']);
    } // constructor


    /**
     * Factory, create object instance
     *
     * In fact it doesn't create instance of SchedulerPhpClient, but
     * dynamically extend this class with set of methods based on $mdefs array
     * (using eval function) and instantiate resulting class
     * SchedulerPhpClientCore instead.
     * Each new method in this subclass accepts parameters according to $mdefs
     * array, call wrapper callMethod(methodname, parameters) and return its
     * result.
     *
     * @todo Replace this method by using PHP5 __call method instead.
     *
     * @param array $mdefs
     * 		hash array with methods description
     * @param int $debug
     * 		XMLRPC debug flag
     * @param boolean $verbose
     * 		verbosity flag
     * @return object, created object instance
     */
    public function &factory($mdefs, $debug=0, $verbose=FALSE)
    {
        global $CC_DBC;
        $f = '';
        foreach ($mdefs as $fn => $farr) {
            $f .=
                '    function '.$fn.'(){'."\n".
                '        $pars = func_get_args();'."\n".
                '        $r = $this->callMethod("'.$fn.'", $pars);'."\n".
                '        return $r;'."\n".
                '    }'."\n";
        }
        $e = "class SchedulerPhpClientCore extends SchedulerPhpClient{\n".
             "$f\n".
             "}\n";
        if (FALSE === eval($e)) {
        	return $CC_DBC->raiseError("Eval failed");
        }
        $spc = new SchedulerPhpClientCore($mdefs, $debug, $verbose);
        return $spc;
    } // fn factory


    /**
     * XMLRPC methods wrapper
     * Encode XMLRPC request message, send it, receive and decode response.
     *
     * @param string $method
     * 		method name
     * @param array $gettedPars
     * 		returned by func_get_args() in called method
     * @return array
     * 		PHP hash with response
     */
    public function callMethod($method, $gettedPars)
    {
        $parr = array();
        $XML_RPC_val = new XML_RPC_Value;
        foreach ($this->mdefs[$method]['p'] as $i => $p) {
            $parr[$p] = new XML_RPC_Value;
            switch ($this->mdefs[$method]['t'][$i]) {
                // array type: normal array
                case 'array':
                    $parr[$p]->addArray($gettedPars[$i]);
                    break;
                // stuct type: assoc. array
                case 'struct':
                    $parr[$p]->addStruct($gettedPars[$i]);
                    break;
                // scalar types: 'int' | 'boolean' | 'string' | 'double' | 'dateTime.iso8601' | 'base64'
                default:
                    $parr[$p]->addScalar($gettedPars[$i], $this->mdefs[$method]['t'][$i]);
            }
        }
        $XML_RPC_val->addStruct($parr);
        $fullmethod = $this->mdefs[$method]['m'];
        $msg = new XML_RPC_Message($fullmethod, array($XML_RPC_val));
        if ($this->verbose) {
            echo "parr:\n";
            var_dump($parr);
            echo "message:\n";
            echo $msg->serialize()."\n";
        }
        $this->client->setDebug($this->debug);
        if (!$res = $this->client->send($msg)) {
            return array('error' => array('code' => -1, 'message' => '##Cannot connect to Scheduler##'));
        }
        if ($res->faultCode() > 0) {
            return array('error' => array('code' => $res->faultCode(), 'message' => $res->faultString()));
        }
        if ($this->verbose) {
            echo "result:\n";
            echo $res->serialize();
        }
        $val = $res->value();
        $resp = XML_RPC_decode($res->value());
        return $resp;
    } // fn callMethod

} // class SchedulerPhpClient

/* ======================================================== class definitions */
?>