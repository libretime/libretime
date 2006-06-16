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
define('PHP5', version_compare( phpversion(), "5.0.0", ">=" ));

/* ====================================================== specific PHP config */
//error_reporting(0);
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
require_once dirname(__FILE__).'/../conf.php';
require_once 'DB.php';
require_once "XML/RPC/Server.php";
require_once 'XR_Archive.php';

/* ============================================ setting default error handler */
function errHndl($errno, $errmsg, $filename, $linenum, $vars){
    switch($errno){
        case E_WARNING:
        case E_NOTICE: 
        case E_USER_WARNING:
        case E_USER_NOTICE: 
            return;
            break; 
        default:   
            $xr =& new XML_RPC_Response(0, 805,
                htmlspecialchars("ERROR:xrLocStor: $errno $errmsg ($filename:$linenum)"));
            header("Content-type: text/xml");
            echo $xr->serialize();
            exit($errno);
    }
}
if(PHP5){
    $old_error_handler = set_error_handler("errHndl", E_ALL);
}else{
    $old_error_handler = set_error_handler("errHndl");
}


/* ============================================================= runable code */
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);

$archive = &new XR_Archive($dbc, $config);

$methods = array(
    'test'                    => 'Tests toupper and checks sessid, params: '.
                                  'teststring, sessid.',
    'getVersion'              => 'Dummy function for connection testing.',
    'authenticate'            => 'Checks authentication.',
    'login'                   => 'Login to storage.',
    'logout'                  => 'Logout from storage.',
    'existsAudioClip'         => 'Checks if an Audio clip with the specified '.
                                  'id is stored in local storage.',
    'storeAudioClipOpen'      => 'Open channel for store a new audio clip '.
                                    'or replace an existing one.',
    'storeAudioClipClose'     => 'Close channel for store a new audio clip'.
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
                                  'AudioClips, return all matching clip ids.',
    'accessRawAudioData'      => 'Get access to raw audio data.',
    'releaseRawAudioData'     => 'Release access to raw audio data.',
    'getAudioClip'            => 'Return the contents of an Audio clip.',
    'resetStorage'            => 'Reset storageServer for debugging.',
    'createPlaylist'          => 'Create a new Playlist metafile.',
    'editPlaylist'            => 'Open a Playlist metafile for editing.',
    'savePlaylist'            => 'Save a Playlist metafile.',
    'deletePlaylist'          => 'Delete a Playlist metafile.',
    'accessPlaylist'          => 'Open readable URL to a Playlist metafile.',
    'releasePlaylist'         => 'Release readable URL from accessPlaylist.',
    'existsPlaylist'          => 'Check whether a Playlist exists.',
    'playlistIsAvailable'     => 'Check whether a Playlist is available '.
                                    'for editing.',

    'uploadOpen'              => 'Open file-layer upload',
    'uploadCheck'             => 'Check the checksum of uploaded file',
    'uploadClose'             => 'Close file-layer upload',
    'downloadOpen'            => 'Open file-layer download',
//    'downloadCheck'           => 'Check the checksum of downloaded file',
    'downloadClose'           => 'Close file-layer download',
    'prepareHubInitiatedTransfer'   => 'Prepare hub initiated transfer',
    'listHubInitiatedTransfers'     => 'List hub initiated transfers',
    'setHubInitiatedTransfer'       => 'Set state of hub initiated transfers',
    'ping'                    => 'Echo request',
);

$defs = array();
foreach($methods as $method=>$description){
    $defs["archive.$method"] = array(
            "function" => array(&$archive, "xr_$method"),
#            "function" => "\$GLOBALS['archive']->xr_$method",
            "signature" => array(
                array($GLOBALS['XML_RPC_Struct'], $GLOBALS['XML_RPC_Struct'])
            ),
            "docstring" => $description
    );
}
$s = &new XML_RPC_Server( $defs );

?>