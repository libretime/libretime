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
    Version  : $Revision: 1.16 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/xmlrpc/xrLocStor.php,v $

------------------------------------------------------------------------------*/

/* ====================================================== specific PHP config */
//error_reporting(0);
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
require_once 'DB.php';
require_once "XML/RPC/Server.php";
require_once '../conf.php';
require_once 'XR_LocStor.php';

/* ============================================ setting default error handler */
function errHndl($errno, $errmsg, $filename, $linenum, $vars){
    if($errno == 8 /*E_NOTICE*/) return;
    $xr =& new XML_RPC_Response(0, 805,
        "ERROR:xrLocStor: $errno $errmsg ($filename:$linenum)");
    header("Content-type: text/xml");
    echo $xr->serialize();
    exit($errno);
}
$old_error_handler = set_error_handler("errHndl");


/* ============================================================= runable code */
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);

$locStor = &new XR_LocStor(&$dbc, $config);

$methods = array(
    'test'                    => 'Tests toupper and checks sessid, params: '.
                                  'teststring, sessid.',
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

    'loadPref'                => 'Load user preference value.',
    'savePref'                => 'Save user preference value.',
);

$defs = array();
foreach($methods as $method=>$description){
    $defs["locstor.$method"] = array(
#            "function" => array(&$locStor, "xr_$method"),
            "function" => "\$GLOBALS['locStor']->xr_$method",
            "signature" => array(
                array($GLOBALS['XML_RPC_Struct'], $GLOBALS['XML_RPC_Struct'])
            ),
            "docstring" => $description
    );
}
$s=new XML_RPC_Server( $defs );

?>
