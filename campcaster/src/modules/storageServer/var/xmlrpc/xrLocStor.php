<?php
/**
 * @author $Author$
 * @version $Revision$
 */

/* ====================================================== specific PHP config */
ini_set("mbstring.internal_encoding", 'UTF-8');
ini_set("html_errors", FALSE);
ini_set("error_prepend_string", "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<methodResponse>
<fault>
<value>
<struct>
<member>
<name>faultCode</name>
<value><int>804</int></value>
</member>
<member>
<name>faultString</name>
<value><string>");
ini_set("error_append_string", "</string></value>
</member>
</struct>
</value>
</fault>
</methodResponse>");
header("Content-type: text/xml");

/* ================================================================= includes */
require_once(dirname(__FILE__).'/../conf.php');
require_once('DB.php');
require_once("XML/RPC/Server.php");
require_once('XR_LocStor.php');

/* ============================================ setting default error handler */
function errHndl($errno, $errmsg, $filename, $linenum, $vars)
{
    switch ($errno) {
        case E_WARNING:
        case E_NOTICE:
        case E_USER_WARNING:
        case E_USER_NOTICE:
            return;
            break;
        default:
            $xr = new XML_RPC_Response(0, 805,
                htmlspecialchars("ERROR:xrLocStor: $errno $errmsg ($filename:$linenum)"));
            header("Content-type: text/xml");
            echo $xr->serialize();
            exit($errno);
    }
}
$old_error_handler = set_error_handler("errHndl", E_ALL);

/* ============================================================= runable code */
$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
if (PEAR::isError($CC_DBC)) {
    trigger_error("DB::connect: ".$CC_DBC->getMessage()." ".$CC_DBC->getUserInfo(),E_USER_ERROR);
}
$CC_DBC->setErrorHandling(PEAR_ERROR_RETURN);
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

$locStor = new XR_LocStor();

$methods = array(
    'test'                    => 'Tests toupper and checks sessid, params: '.
                                  'teststring, sessid.',
    'getVersion'              => 'Dummy function for connection testing.',
    'authenticate'            => 'Checks authentication.',
    'login'                   => 'Login to storage.',
    'logout'                  => 'Logout from storage.',
    'existsAudioClip'         => 'Checks if an Audio clip with the specified '.
                                  'id is stored in local storage.',
    'storeAudioClipOpen'      => 'Open channel to store a new audio clip '.
                                    'or replace an existing one.',
    'storeAudioClipClose'     => 'Close channel to store a new audio clip'.
                                    ' or replace an existing one.',
    'downloadRawAudioDataOpen'=> 'Create and return downloadable URL'.
                                    'for audio file',
    'downloadRawAudioDataClose'=>'Discard downloadable URL for audio file',
    'downloadMetadataOpen'    => 'Create and return downloadable URL'.
                                    'for metadata',
    'downloadMetadataClose'   => 'Discard downloadable URL for metadata',
    'openPut'                 => 'openPut',
    'closePut'                => 'closePut',
    'deleteAudioClip'         => 'Delete an existing Audio clip.',
    'updateAudioClipMetadata' => 'Update the metadata of an Audio clip '.
                                  'stored in Local storage.',
    'searchMetadata'          => 'Search through the metadata of stored '.
                                  'files, return all matching clip ids.',
    'browseCategory'          =>'Return values of specified metadata category.',
    'accessRawAudioData'      => 'Get access to raw audio data.',
    'releaseRawAudioData'     => 'Release access to raw audio data.',
    'getAudioClip'            => 'Return the contents of an Audio clip.',
    'resetStorage'            => 'Reset storageServer for debugging.',
    'storeWebstream'          => 'Store audio stream identified by URL',

    'createPlaylist'          => 'Create a new Playlist metafile.',
    'editPlaylist'            => 'Open a Playlist metafile for editing.',
    'savePlaylist'            => 'Save a Playlist metafile.',
    'revertEditedPlaylist'    => 'RollBack playlist changes to the locked state.',
    'deletePlaylist'          => 'Delete a Playlist metafile.',
    'accessPlaylist'          => 'Open readable URL to a Playlist metafile.',
    'releasePlaylist'         => 'Release readable URL from accessPlaylist.',
    'existsPlaylist'          => 'Check whether a Playlist exists.',
    'playlistIsAvailable'     => 'Check whether a Playlist is available '.
                                    'for editing.',
    'exportPlaylistOpen'      => 'Create a tarfile with playlist export.',
    'exportPlaylistClose'     => 'Close playlist export.',
    'importPlaylistOpen'      => 'Open writable handle for playlist import.',
    'importPlaylistClose'     => 'Close import-handle and import playlist.',

    'renderPlaylistToFileOpen'	=> 'Render playlist to ogg file (open handle)',
    'renderPlaylistToFileCheck'	=> 'Render playlist to ogg file (check results)',
    'renderPlaylistToFileClose'	=> 'Render playlist to ogg file (close handle)',

    'renderPlaylistToStorageOpen'	=> 'Render playlist to storage media clip (open handle)',
    'renderPlaylistToStorageCheck'	=> 'Render playlist to storage media clip (check results)',

    'renderPlaylistToRSSOpen'	=> 'Render playlist to RSS file (open handle)',
    'renderPlaylistToRSSCheck'	=> 'Render playlist to RSS file (check results)',
    'renderPlaylistToRSSClose'	=> 'Render playlist to RSS file (close handle)',

    'createBackupOpen'  => 'Create backup of storage (open handle)',
    'createBackupCheck' => 'Create backup of storage (check results)',
    'createBackupClose' => 'Create backup of storage (close handle)',

    'restoreBackupOpen'  => 'Restore a backup file (open handle)',
    'restoreBackupClosePut' => 'Restore a backup file (close PUT handle)',
    'restoreBackupCheck' => 'Restore a backup file (check results)',
    'restoreBackupClose' => 'Restore a backup file (close handle)',

    'loadPref'                => 'Load user preference value.',
    'savePref'                => 'Save user preference value.',
    'delPref'                 => 'Delete user preference record.',
    'loadGroupPref'           => 'Read group preference record.',
    'saveGroupPref'           => 'Delete user preference record.',

    'getTransportInfo'          => 'Common "check" method and info getter for transports',
    'turnOnOffTransports'       => 'Turn transports on/off, optionaly return current state',
    'doTransportAction'         => 'Pause, resume or cancel transport',
    'uploadFile2Hub'            => 'Open async file transfer from local storageServer to network hub',
    'getHubInitiatedTransfers'  => 'Get list of prepared transfers initiated by hub',
    'startHubInitiatedTransfer' => 'Start of download initiated by hub',
    'upload2Hub'                => 'Start upload of audioclip or playlist from local storageServer to hub',
    'downloadFromHub'           => 'Start download of audioclip or playlist from hub to local storageServer',
    'globalSearch'              => 'Start search job on network hub',
    'getSearchResults'          => 'Get results from search job on network hub',

);

$defs = array();
foreach ($methods as $method => $description) {
    $defs["locstor.$method"] = array(
            "function" => array(&$locStor, "xr_$method"),
#            "function" => "\$GLOBALS['locStor']->xr_$method",
            "signature" => array(
                array($GLOBALS['XML_RPC_Struct'], $GLOBALS['XML_RPC_Struct'])
            ),
            "docstring" => $description
    );
}

$s = new XML_RPC_Server($defs);

?>