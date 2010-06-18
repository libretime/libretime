<?php
require_once(dirname(__FILE__).'/../conf.php');
include_once("XML/RPC.php");

session_start();

/**
 * Get a persistant value.  If the value is present in the $_REQUEST
 * array, the session variable will be set to this value and returned.
 * If the value is not yet set, it will be set to the default value.
 * In all other cases the value from the session variable is returned.
 *
 * @param string $p_name
 * @param mixed $p_defaultValue
 * @return mixed
 */
function camp_session_get($p_name, $p_defaultValue)
{
	// Use the REQUEST variable if it is set.
	if (isset($_REQUEST[$p_name])) {
		$_SESSION[$p_name] = $_REQUEST[$p_name];
	}
	elseif (!isset($_SESSION[$p_name])) {
		$_SESSION[$p_name] = $p_defaultValue;
	}
	return $_SESSION[$p_name];
} // fn camp_session_get


/**
 * Print out an HTML OPTION element.
 *
 * @param string $p_value
 * @param string $p_selectedValue
 * @param string $p_printValue
 * @return boolean
 * 		Return TRUE if the option is selected, FALSE if not.
 */
function camp_html_select_option($p_value, $p_selectedValue, $p_printValue)
{
	$selected = false;
	$str = '<OPTION VALUE="'.htmlspecialchars($p_value, ENT_QUOTES).'"';
	if (!strcmp($p_value, $p_selectedValue)) {
		$str .= ' SELECTED';
		$selected = true;
	}
	$str .= '>'.htmlspecialchars($p_printValue)."</OPTION>\n";
	echo $str;
	return $selected;
} // fn camp_html_select_option


$serverPath =
  "http://{$CC_CONFIG['storageUrlHost']}:{$CC_CONFIG['storageUrlPort']}".
  "{$CC_CONFIG['storageUrlPath']}/{$CC_CONFIG['storageXMLRPC']}";
$serverPath = camp_session_get("storageserver_xmlrpc_path", $serverPath);
$f_selectedMethod = camp_session_get("f_selectedMethod", "listMethods");
$url = parse_url($serverPath);
$client = new XML_RPC_Client($url['path'], $url['host']);

$methodDefs = array(
    "listMethods" => array('m'=>"system.listMethods", 'p'=>NULL),
    "methodHelp" => array('m'=>"system.methodHelp", 'p'=>0),
    "methodSignature" => array('m'=>"system.methodSignature", 'p'=>0),
    "test" => array('m'=>"locstor.test", 'p'=>array('sessid', 'teststring')),
    "getVersion" => array('m'=>"locstor.getVersion", 'p'=>array(), 'r'=>'version'),
    "authenticate" => array('m'=>"locstor.authenticate", 'p'=>array('login', 'pass'), 'r'=>'authenticate'),
    "login" => array('m'=>"locstor.login", 'p'=>array('login', 'pass'), 'r'=>'sessid'),
    "logout" => array('m'=>"locstor.logout", 'p'=>array('sessid'), 'r'=>'status'),
    "storeAudioClipOpen" => array('m'=>"locstor.storeAudioClipOpen",
        'p'=>array('sessid', 'gunid', 'metadata', 'fname', 'chsum'),
        'r'=>array('url', 'token')
    ),
    "storeAudioClipClose" => array('m'=>"locstor.storeAudioClipClose",
        'p'=>array('sessid', 'token'), 'r'=>'gunid'),
    "accessRawAudioData" => array('m'=>"locstor.accessRawAudioData",
        'p'=>array('sessid', 'gunid'), 'r'=>array('url', 'token')),
    "releaseRawAudioData" => array('m'=>"locstor.releaseRawAudioData",
        'p'=>array('token'), 'r'=>'status'),
    "downloadRawAudioDataOpen" =>
        array('m'=>"locstor.downloadRawAudioDataOpen",
            'p'=>array('sessid', 'gunid'), 'r'=>array('url', 'token')),
    "downloadRawAudioDataClose" =>
        array('m'=>"locstor.downloadRawAudioDataClose",
            'p'=>array('sessid', 'token'), 'r'=>'gunid'),
    "downloadMetadataOpen" => array('m'=>"locstor.downloadMetadataOpen",
        'p'=>array('sessid', 'gunid'), 'r'=>array('url', 'token')),
    "downloadMetadataClose" => array('m'=>"locstor.downloadMetadataClose",
        'p'=>array('sessid', 'token'), 'r'=>'gunid'),

    "deleteAudioClip" =>
        array('m'=>"locstor.deleteAudioClip",
            'p'=>array('sessid', 'gunid', 'forced'), 'r'=>'status'),
    "existsAudioClip" => array('m'=>"locstor.existsAudioClip",
        'p'=>array('sessid', 'gunid'), 'r'=>'exists'),
    "getAudioClip" => array('m'=>"locstor.getAudioClip",
        'p'=>array('sessid', 'gunid'), 'r'=>'metadata'),
    "updateAudioClipMetadata" => array('m'=>"locstor.updateAudioClipMetadata",
        'p'=>array('sessid', 'gunid', 'metadata'), 'r'=>'status'),
    "searchMetadata" => array('m'=>"locstor.searchMetadata", 'p'=>NULL),
    "browseCategory" => array('m'=>"locstor.browseCategory", 'p'=>NULL),
    "resetStorage" => array('m'=>"locstor.resetStorage",
        'p'=>array()),
#        'p'=>array('loadSampleData', 'invalidateSessionIds')),
    "storeWebstream" => array('m'=>"locstor.storeWebstream",
        'p'=>array('sessid', 'gunid', 'metadata', 'fname', 'url'),
        'r'=>array('gunid')
    ),

    "createPlaylist" => array('m'=>"locstor.createPlaylist",
        'p'=>array('sessid', 'plid', 'fname'), 'r'=>'plid'),
    "editPlaylist" => array('m'=>"locstor.editPlaylist",
        'p'=>array('sessid', 'plid'), 'r'=>array('url', 'token')),
    "savePlaylist" => array('m'=>"locstor.savePlaylist",
        'p'=>array('sessid', 'token', 'newPlaylist'), 'r'=>'plid'),
    "revertEditedPlaylist" => array('m'=>"locstor.revertEditedPlaylist",
        'p'=>array('sessid', 'token'), 'r'=>'plid'),
    "deletePlaylist" => array('m'=>"locstor.deletePlaylist",
        'p'=>array('sessid', 'plid', 'forced'), 'r'=>'status'),
    "accessPlaylist" => array('m'=>"locstor.accessPlaylist",
        'p'=>array('sessid', 'plid'), 'r'=>array('url', 'token')),
    "releasePlaylist" => array('m'=>"locstor.releasePlaylist",
        'p'=>array('token'), 'r'=>'plid'),
    "existsPlaylist" => array('m'=>"locstor.existsPlaylist",
        'p'=>array('sessid', 'plid'), 'r'=>'exists'),
    "playlistIsAvailable" => array('m'=>"locstor.playlistIsAvailable",
        'p'=>array('sessid', 'plid'), 'r'=>array('available', 'ownerid', 'ownerlogin')),

    "exportPlaylistOpen" => array('m'=>"locstor.exportPlaylistOpen",
        'p'=>array('sessid', 'plids', 'type', 'standalone'),
        'r'=>array('url', 'token')),
    "exportPlaylistClose" => array('m'=>"locstor.exportPlaylistClose",
        'p'=>array('token'), 'r'=>array('status')),
    "importPlaylistOpen" => array('m'=>"locstor.importPlaylistOpen",
        'p'=>array('sessid', 'chsum'), 'r'=>array('url', 'token')),
    "importPlaylistClose" => array('m'=>"locstor.importPlaylistClose",
        'p'=>array('token'), 'r'=>array('gunid')),

    "renderPlaylistToFileOpen" => array('m'=>"locstor.renderPlaylistToFileOpen",
        'p'=>array('sessid', 'plid'),
        'r'=>array('token')),
    "renderPlaylistToFileCheck" => array('m'=>"locstor.renderPlaylistToFileCheck",
        'p'=>array('token'), 'r'=>array('status', 'url')),
    "renderPlaylistToFileClose"   => array('m'=>"locstor.renderPlaylistToFileClose",
        'p'=>array('token'), 'r'=>array('status')),
    "renderPlaylistToStorageOpen"   => array('m'=>"locstor.renderPlaylistToStorageOpen",
        'p'=>array('sessid', 'plid'),
        'r'=>array('token')),
    "renderPlaylistToStorageCheck" => array('m'=>"locstor.renderPlaylistToStorageCheck",
        'p'=>array('token'), 'r'=>array('status', 'gunid')),
    "renderPlaylistToRSSOpen"   => array('m'=>"locstor.renderPlaylistToRSSOpen",
        'p'=>array('sessid', 'plid'),
        'r'=>array('token')),
    "renderPlaylistToRSSCheck" => array('m'=>"locstor.renderPlaylistToRSSCheck",
        'p'=>array('token'), 'r'=>array('status', 'url')),
    "renderPlaylistToRSSClose" => array('m'=>"locstor.renderPlaylistToRSSClose",
        'p'=>array('token'), 'r'=>array('status')),

    "loadPref" => array('m'=>"locstor.loadPref",
        'p'=>array('sessid', 'key'), 'r'=>'value'),
    "savePref" => array('m'=>"locstor.savePref",
        'p'=>array('sessid', 'key', 'value'), 'r'=>'status'),
    "delPref" => array('m'=>"locstor.delPref",
        'p'=>array('sessid', 'key'), 'r'=>'status'),
    "loadGroupPref" => array('m'=>"locstor.loadGroupPref",
        'p'=>array('sessid', 'group', 'key'), 'r'=>'value'),
    "saveGroupPref" => array('m'=>"locstor.saveGroupPref",
        'p'=>array('sessid', 'group', 'key', 'value'), 'r'=>'status'),

    "getTransportInfo" => array('m'=>"locstor.getTransportInfo",
        'p'=>array('trtok'),
        'r'=>array('state', 'realsize', 'expectedsize', 'realsum', 'expectedsum')),
    "turnOnOffTransports" => array('m'=>"locstor.turnOnOffTransports",
        'p'=>array('sessid', 'onOff'), 'r'=>array('state')),
    "doTransportAction" => array('m'=>"locstor.doTransportAction",
        'p'=>array('sessid', 'trtok', 'action'), 'r'=>array('state')),
    "uploadFile2Hub" => array('m'=>"locstor.uploadFile2Hub",
        'p'=>array('sessid', 'filePath'), 'r'=>array('trtok')),
    "getHubInitiatedTransfers" => array('m'=>"locstor.getHubInitiatedTransfers",
        'p'=>array('sessid'), 'r'=>array()),
    "startHubInitiatedTransfer" => array('m'=>"locstor.startHubInitiatedTransfer",
        'p'=>array('trtok'), 'r'=>array()),
    "upload2Hub" => array('m'=>"locstor.upload2Hub",
        'p'=>array('sessid', 'gunid'), 'r'=>array('trtok')),
    "downloadFromHub" => array('m'=>"locstor.downloadFromHub",
        'p'=>array('sessid', 'gunid'), 'r'=>array('trtok')),
    "globalSearch" => array('m'=>"locstor.globalSearch",
        'p'=>array('sessid', 'criteria'), 'r'=>array('trtok')),
    "getSearchResults" => array('m'=>"locstor.getSearchResults",
        'p'=>array('trtok')),

    "createBackupOpen" => array('m'=>"locstor.createBackupOpen",
        'p'=>array('sessid', 'criteria'), 'r'=>array('token')),
    "createBackupCheck" => array('m'=>"locstor.createBackupCheck",
#        'p'=>array('token'), 'r'=>array('status', 'url', 'metafile', 'faultString')),
        'p'=>array('token'), 'r'=>array('status', 'url', 'tmpfile')),
    "createBackupClose" => array('m'=>"locstor.createBackupClose",
        'p'=>array('token'), 'r'=>array('status')),
    "restoreBackupOpen" => array('m'=>"locstor.restoreBackupOpen",
        'p'=>array('sessid', 'chsum'), 'r'=>array('url', 'token')),
    "restoreBackupClosePut" => array('m'=>"locstor.restoreBackupClosePut",
        'p'=>array('sessid', 'token'), 'r'=>array('token')),
    "restoreBackupCheck" => array('m'=>"locstor.restoreBackupCheck",
        'p'=>array('token'), 'r'=>array('status', 'faultString')),
    "restoreBackupClose" => array('m'=>"locstor.restoreBackupClose",
        'p'=>array('token'), 'r'=>array('status')),
    "openPut"       => array('m'=>"locstor.openPut", 'p'=>array()),
    "closePut"      => array('m'=>"locstor.closePut", 'p'=>array()),
);

if (isset($_REQUEST['go_button'])) {
    // Get the parameters
    $methodParams = $methodDefs[$f_selectedMethod]['p'];
    foreach ($methodParams as $methodParamName) {
        $inputParamName = "param_".$methodParamName;
        $xmlParameters[$methodParamName] = $_REQUEST[$inputParamName];
        $_SESSION[$inputParamName] = $_REQUEST[$inputParamName];
    }

    // Create the XML-RPC message
    $actualMethod = $methodDefs[$f_selectedMethod]['m'];
    $msg = new XML_RPC_Message($actualMethod, array(XML_RPC_encode($xmlParameters)));
    $sentMessage = $msg->serialize();

    // Send it
    $sendResult = $client->send($msg);
    if ($sendResult->faultCode() > 0) {
        $errorMsg = "xr_cli_test.php: ".$sendResult->faultString()." ".$sendResult->faultCode()."\n";
    } else {
        // If successful
        $xmlResponse = XML_RPC_decode($sendResult->value());

        // Special case state handling
        switch ($f_selectedMethod) {
            case "login":
                // Remember the login session ID so we can use it to call
                // other methods.
                $loggedIn = true;
                $_SESSION['xmlrpc_session_id'] = $xmlResponse['sessid'];
                break;
            case "logout":
                unset($_SESSION['xmlrpc_session_id']);
                break;
            case "storeAudioClipOpen":
                $_SESSION['xmlrpc_token'] = $xmlResponse['token'];
                $_SESSION['xmlrpc_put_url'] = $xmlResponse['url'];
                break;
        }

        if (isset($methodDefs[$method]['r'])) {
            $expectedResult = $methodDefs[$method]['r'];
            if (is_array($expectedResult)) {
                foreach ($expectedResult as $resultName) {
                    $actualResults[$resultName] = $xmlResponse[$resultName];
                }
                echo join(' ', $actualResults)."\n";
            } else {
                switch ($expectedResult) {
                    case "status":
                    case "exists":
                        echo ($xmlResponse[$expectedResult]=='1' ? "TRUE" : "FALSE" )."\n";
                        break;
                    default:
                        echo "{$xmlResponse[$expectedResult]}\n";
                }
            }
        } else {
            switch ($method) {
                case "searchMetadata":
                case "getSearchResults":
                    $acCnt = 0;
                    $acGunids = array();
                    $plCnt = 0;
                    $plGunids = array();
                    $fld = (isset($options['category']) ? $options['category'] : 'gunid' );
                    foreach ($xmlResponse['results'] as $k => $v) {
                        if ($v['type']=='audioclip') {
                            $acCnt++;
                            $acGunids[] = $v[$fld];
                        }
                        if ($v['type']=='playlist') {
                            $plCnt++;
                            $plGunids[] = $v[$fld];
                        }
                    }
                    echo "AC({$acCnt}): ".
                            join(", ", $acGunids).
                        " | PL({$plCnt}): ".
                            join(", ", $plGunids).
                        "\n";
                    break;
                case "browseCategory":
                    echo "RES({$xmlResponse['cnt']}): ".
                            join(", ", $xmlResponse['results']).
                        "\n";
                    break;
                default:
                    //print_r($xmlResponse);
            }
        }
    }
}
?>
<html>
<body bgcolor="#dddddd">
<form>
StorageServer path : <INPUT type="text" name="f_storageserver_xmlrpc_path" value="<?php echo $serverPath; ?>" size="100"><br>
Method:
<select name="f_selectedMethod" onchange="this.form.submit();">
<?php
foreach ($methodDefs as $methodName => $methodDef) {
    camp_html_select_option($methodName, $f_selectedMethod, $methodName);
}
?>
</select>
<br>
Parameters:
<?PHP
$methodParams = $methodDefs[$f_selectedMethod]['p'];
if (!is_array($methodParams) || count($methodParams) == 0) {
    echo "This method requires no parameters.<br>";
} else {
    echo "<table cellpadding=3>";
    foreach ($methodParams as $methodParamName) {
        $value = "";
        if ($methodParamName == "sessid" && isset($_SESSION['xmlrpc_session_id'])) {
            $value = $_SESSION['xmlrpc_session_id'];
        } elseif ($methodParamName == "token" && isset($_SESSION['xmlrpc_token'])) {
            $value = $_SESSION['xmlrpc_token'];
        } elseif (isset($_SESSION["param_".$methodParamName])) {
            $value = $_SESSION["param_".$methodParamName];
        }
        echo "<tr>";
        echo "<td>$methodParamName</td>"; ?> <td><INPUT type="text" name="param_<?php echo $methodParamName; ?>" value="<?php echo $value; ?>"><td></tr>
        <?php
    }
    echo "</table>";
}
?>
<br>
<INPUT type="submit" name="go_button" value="Send Message">
</form>


<?PHP
if ($loggedIn) {
    echo "You have logged in with session ID: ".$_SESSION['xmlrpc_session_id']."<br><br>";
}
if (isset($sentMessage)) {
    ?>
    Sent message:<br>
<TEXTAREA cols="60" rows="8">Method name: <?php echo $actualMethod; ?>
<?php print_r($xmlParameters);?></TEXTAREA>
    <br>
<?PHP
}
if (isset($errorMsg)) {
    ?>
    Error:<br>
    <TEXTAREA cols="60" rows="8"><?php print_r($errorMsg);?></TEXTAREA>
    <br>
    <?php
}

if (isset($xmlResponse)) {
    ?>
    Response:<br>
    <TEXTAREA cols="60" rows="8"><?php print_r($xmlResponse);?></TEXTAREA>
    <br>
    <?php
}
?>
</body>
</html>
