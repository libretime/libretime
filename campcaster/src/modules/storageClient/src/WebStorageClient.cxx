/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the Campcaster project.
    http://campcaster.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    Campcaster is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    Campcaster is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with Campcaster; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#if HAVE_UNISTD_H
#include <unistd.h>
#else
#error "Need unistd.h"
#endif

#include <iostream>
#include <boost/date_time/posix_time/posix_time.hpp>
#include <XmlRpcClient.h>
#include <XmlRpcUtil.h>

#include "LiveSupport/Core/Md5.h"
#include "LiveSupport/Core/XmlRpcCommunicationException.h"
#include "LiveSupport/Core/XmlRpcMethodFaultException.h"
#include "LiveSupport/Core/XmlRpcMethodResponseException.h"
#include "LiveSupport/Core/XmlRpcInvalidArgumentException.h"
#include "LiveSupport/Core/XmlRpcIOException.h"
#include "LiveSupport/Core/XmlRpcInvalidDataException.h"
#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Core/FileTools.h"

#include "WebStorageClient.h"

using namespace boost::posix_time;
using namespace XmlRpc;

using namespace LiveSupport::Core;
using namespace LiveSupport::StorageClient;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  configuration file constants */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string WebStorageClient::configElementNameStr = "webStorage";

namespace {

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the temp files
 *----------------------------------------------------------------------------*/
const std::string    localTempStorageAttrName = "tempFiles";

/*------------------------------------------------------------------------------
 *  The name of the config child element for the storage server location
 *----------------------------------------------------------------------------*/
const std::string    locationConfigElementName = "location";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the storage server name
 *----------------------------------------------------------------------------*/
const std::string    locationServerAttrName = "server";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the storage server port
 *----------------------------------------------------------------------------*/
const std::string    locationPortAttrName = "port";

/*------------------------------------------------------------------------------
 *  The name of the config element attribute for the storage server php page
 *----------------------------------------------------------------------------*/
const std::string    locationPathAttrName = "path";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  constants for the SMIL file */

/*------------------------------------------------------------------------------
 *  The XML version used to create the SMIL file.
 *----------------------------------------------------------------------------*/
const std::string    xmlVersion = "1.0";

/*------------------------------------------------------------------------------
 *  The name of the SMIL root node.
 *----------------------------------------------------------------------------*/
const std::string    smilRootNodeName = "smil";

/*------------------------------------------------------------------------------
 *  The name of the SMIL language description attribute.
 *----------------------------------------------------------------------------*/
const std::string    smilLanguageAttrName = "xmlns";

/*------------------------------------------------------------------------------
 *  The value of the SMIL language description attribute.
 *----------------------------------------------------------------------------*/
const std::string    smilLanguageAttrValue
                            = "http://www.w3.org/2001/SMIL20/Language";

/*------------------------------------------------------------------------------
 *  The name of the body node in the SMIL file.
 *----------------------------------------------------------------------------*/
const std::string    smilBodyNodeName = "body";

/*------------------------------------------------------------------------------
 *  The name of the parallel audio clip list node in the SMIL file.
 *----------------------------------------------------------------------------*/
const std::string    smilParNodeName = "par";

/*------------------------------------------------------------------------------
 *  The name of the audio clip or playlist element node in the SMIL file.
 *----------------------------------------------------------------------------*/
const std::string    smilPlayableNodeName = "audio";

/*------------------------------------------------------------------------------
 *  The name of the attribute containing the URI of the Playable element.
 *----------------------------------------------------------------------------*/
const std::string    smilPlayableUriAttrName = "src";

/*------------------------------------------------------------------------------
 *  The name of the attribute containing the Id of the Playable element.
 *----------------------------------------------------------------------------*/
const std::string    smilPlayableIdAttrName = "id";

/*------------------------------------------------------------------------------
 *  The name of the attribute containing the clipBegin of the Playable element.
 *----------------------------------------------------------------------------*/
const std::string    smilPlayableStartAttrName = "clipBegin";

/*------------------------------------------------------------------------------
 *  The name of the attribute containing the clipEnd of the Playable element.
 *----------------------------------------------------------------------------*/
const std::string    smilPlayableEndAttrName = "clipEnd";

/*------------------------------------------------------------------------------
 *  The name of the attribute containing the clipLength of the Playable element.
 *----------------------------------------------------------------------------*/
const std::string    smilPlayableLengthAttrName = "clipLength";

/*------------------------------------------------------------------------------
 *  The name of the attribute containing the relative offset of the element.
 *----------------------------------------------------------------------------*/
const std::string    smilRelativeOffsetAttrName = "begin";

/*------------------------------------------------------------------------------
 *  The name of the animation element in the SMIL file.
 *----------------------------------------------------------------------------*/
const std::string    smilAnimateNodeName = "animate";

/*------------------------------------------------------------------------------
 *  The name of the "attribute name" attribute of the animation element.
 *----------------------------------------------------------------------------*/
const std::string    smilAnimateNameAttrName = "attributeName";

/*------------------------------------------------------------------------------
 *  The value of the "attribute name" attribute of the animation element.
 *----------------------------------------------------------------------------*/
const std::string    smilAnimateNameAttrValue = "soundLevel";

/*------------------------------------------------------------------------------
 *  The name of the starting sound level % attribute of the animation element.
 *----------------------------------------------------------------------------*/
const std::string    smilAnimateFromAttrName = "from";

/*------------------------------------------------------------------------------
 *  The name of the ending sound level % attribute of the animation element.
 *----------------------------------------------------------------------------*/
const std::string    smilAnimateToAttrName = "to";

/*------------------------------------------------------------------------------
 *  The name of the "calculation mode" attribute of the animation element.
 *----------------------------------------------------------------------------*/
const std::string    smilAnimateCalcModeAttrName = "calcMode";

/*------------------------------------------------------------------------------
 *  The value of the "calculation mode" attribute of the animation element.
 *----------------------------------------------------------------------------*/
const std::string    smilAnimateCalcModeAttrValue = "linear";

/*------------------------------------------------------------------------------
 *  The name of the rel. offset of the start of the animation attribute.
 *----------------------------------------------------------------------------*/
const std::string    smilAnimateBeginAttrName = "begin";

/*------------------------------------------------------------------------------
 *  The name of the rel. offset of the end of the animation attribute.
 *----------------------------------------------------------------------------*/
const std::string    smilAnimateEndAttrName = "end";

/*------------------------------------------------------------------------------
 *  The name of the "what to do after done" attribute of the animation element.
 *----------------------------------------------------------------------------*/
const std::string    smilAnimateFillAttrName = "fill";

/*------------------------------------------------------------------------------
 *  The value of the "what to do after done" attribute of the animation element.
 *----------------------------------------------------------------------------*/
const std::string    smilAnimateFillAttrValue = "freeze";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: error reports */

/*------------------------------------------------------------------------------
 *  The name of the error code parameter in the returned struct
 *----------------------------------------------------------------------------*/
const std::string    errorCodeParamName = "faultCode";

/*------------------------------------------------------------------------------
 *  The name of the error message parameter in the returned struct
 *----------------------------------------------------------------------------*/
const std::string    errorMessageParamName = "faultString";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: getVersion */

/*------------------------------------------------------------------------------
 *  The name of the get version method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    getVersionMethodName = "locstor.getVersion";

/*------------------------------------------------------------------------------
 *  The name of version return parameter for getVersion
 *----------------------------------------------------------------------------*/
const std::string    getVersionResultParamName = "version";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: resetStorage */

/*------------------------------------------------------------------------------
 *  The name of the reset storage method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    resetStorageMethodName 
                            = "locstor.resetStorage";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: search */

/*------------------------------------------------------------------------------
 *  The name of the search method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    searchMethodName 
                            = "locstor.searchMetadata";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    searchSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the search criteria parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    searchCriteriaParamName = "criteria";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    searchResultParamName = "results";

/*------------------------------------------------------------------------------
 *  The name of the result's unique ID parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    searchResultUniqueIdParamName = "gunid";

/*------------------------------------------------------------------------------
 *  The name of the result's type parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    searchResultTypeParamName = "type";

/*------------------------------------------------------------------------------
 *  The value of the 'type' parameter when an audio clip is returned
 *----------------------------------------------------------------------------*/
const std::string    searchResultAudioClipTypeValue = "audioclip";

/*------------------------------------------------------------------------------
 *  The value of the 'type' parameter when a playlist is returned
 *----------------------------------------------------------------------------*/
const std::string    searchResultPlaylistTypeValue = "playlist";

/*------------------------------------------------------------------------------
 *  The value of the 'type' parameter when a web stream is returned
 *----------------------------------------------------------------------------*/
const std::string    searchResultWebStreamTypeValue = "webstream";

/*------------------------------------------------------------------------------
 *  The name of the count parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    searchCountParamName = "cnt";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: browse */

/*------------------------------------------------------------------------------
 *  The name of the browse method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    browseMethodName 
                            = "locstor.browseCategory";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    browseSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the metadata type parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    browseMetadataParamName = "category";

/*------------------------------------------------------------------------------
 *  The name of the search criteria parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    browseCriteriaParamName = "criteria";

/*------------------------------------------------------------------------------
 *  The name of the list of metadata values parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    browseResultParamName = "results";

/*------------------------------------------------------------------------------
 *  The name of the count parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    browseResultCountParamName = "cnt";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ playlist methods */

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: createPlaylist */

/*------------------------------------------------------------------------------
 *  The name of the create playlist method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    createPlaylistMethodName 
                            = "locstor.createPlaylist";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    createPlaylistSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the playlist unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    createPlaylistPlaylistIdParamName = "plid";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    createPlaylistResultParamName = "plid";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: existsPlaylist */

/*------------------------------------------------------------------------------
 *  The name of the exists playlist method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    existsPlaylistMethodName 
                            = "locstor.existsPlaylist";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    existsPlaylistSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the audio clip unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    existsPlaylistPlaylistIdParamName = "plid";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    existsPlaylistResultParamName = "exists";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: getPlaylist */

/*------------------------------------------------------------------------------
 *  The name of the opening 'get playlist' method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    getPlaylistOpenMethodName 
                            = "locstor.accessPlaylist";

/*------------------------------------------------------------------------------
 *  The name of the closing 'get playlist' method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    getPlaylistCloseMethodName 
                            = "locstor.releasePlaylist";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    getPlaylistSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the playlist unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    getPlaylistPlaylistIdParamName = "plid";

/*------------------------------------------------------------------------------
 *  The name of the recursive parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    getPlaylistRecursiveParamName = "recursive";

/*------------------------------------------------------------------------------
 *  The name of the result URL parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    getPlaylistUrlParamName = "url";

/*------------------------------------------------------------------------------
 *  The name of the token parameter returned (for open) or input (for close)
 *----------------------------------------------------------------------------*/
const std::string    getPlaylistTokenParamName = "token";

/*------------------------------------------------------------------------------
 *  The name of the content parameter returned (for open) or input (for close)
 *----------------------------------------------------------------------------*/
const std::string    getPlaylistContentParamName = "content";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: editPlaylist */

/*------------------------------------------------------------------------------
 *  The name of the 'edit playlist' method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    editPlaylistMethodName 
                            = "locstor.editPlaylist";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    editPlaylistSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the playlist unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    editPlaylistPlaylistIdParamName = "plid";

/*------------------------------------------------------------------------------
 *  The name of the result URL parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    editPlaylistUrlParamName = "url";

/*------------------------------------------------------------------------------
 *  The name of the token parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    editPlaylistTokenParamName = "token";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: savePlaylist */

/*------------------------------------------------------------------------------
 *  The name of the 'save playlist' method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    savePlaylistMethodName 
                            = "locstor.savePlaylist";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    savePlaylistSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the token parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    savePlaylistTokenParamName = "token";

/*------------------------------------------------------------------------------
 *  The name of the new playlist parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    savePlaylistNewPlaylistParamName = "newPlaylist";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    savePlaylistResultParamName = "plid";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: revertPlaylist */

/*------------------------------------------------------------------------------
 *  The name of the 'revert playlist' method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    revertPlaylistMethodName 
                            = "locstor.revertEditedPlaylist";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    revertPlaylistSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the token parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    revertPlaylistTokenParamName = "token";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    revertPlaylistResultParamName = "plid";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ audio clip methods */

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: existsAudioClip */

/*------------------------------------------------------------------------------
 *  The name of the exists audio clip method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    existsAudioClipMethodName 
                            = "locstor.existsAudioClip";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    existsAudioClipSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the audio clip unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    existsAudioClipAudioClipIdParamName = "gunid";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    existsAudioClipResultParamName = "exists";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: getAudioClip */

/*------------------------------------------------------------------------------
 *  The name of the opening 'get audio clip' method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    getAudioClipOpenMethodName 
                            = "locstor.downloadMetadataOpen";

/*------------------------------------------------------------------------------
 *  The name of the closing 'get audio clip' method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    getAudioClipCloseMethodName 
                            = "locstor.downloadMetadataClose";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    getAudioClipSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the audio clip unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    getAudioClipAudioClipIdParamName = "gunid";

/*------------------------------------------------------------------------------
 *  The name of the result URL parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    getAudioClipUrlParamName = "url";

/*------------------------------------------------------------------------------
 *  The name of the token parameter returned (for open) or input (for close)
 *----------------------------------------------------------------------------*/
const std::string    getAudioClipTokenParamName = "token";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: storeAudioClip */

/*------------------------------------------------------------------------------
 *  The name of the opening 'store audio clip' method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    storeAudioClipOpenMethodName 
                            = "locstor.storeAudioClipOpen";

/*------------------------------------------------------------------------------
 *  The name of the closing 'store audio clip' method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    storeAudioClipCloseMethodName 
                            = "locstor.storeAudioClipClose";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    storeAudioClipSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the audio clip unique ID parameter for both 'open' and 'close'
 *----------------------------------------------------------------------------*/
const std::string    storeAudioClipAudioClipIdParamName = "gunid";

/*------------------------------------------------------------------------------
 *  The name of the metadata file name parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    storeAudioClipMetadataParamName = "metadata";

/*------------------------------------------------------------------------------
 *  The name of the binary file name parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    storeAudioClipFileNameParamName = "fname";

/*------------------------------------------------------------------------------
 *  The name of the checksum of the binary file name in the input structure
 *----------------------------------------------------------------------------*/
const std::string    storeAudioClipChecksumParamName = "chsum";

/*------------------------------------------------------------------------------
 *  The name of the URL parameter returned by the 'open' method
 *----------------------------------------------------------------------------*/
const std::string    storeAudioClipUrlParamName = "url";

/*------------------------------------------------------------------------------
 *  The name of the token parameter for both 'open' and 'close' methods
 *----------------------------------------------------------------------------*/
const std::string    storeAudioClipTokenParamName = "token";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: acquireAudioClip */

/*------------------------------------------------------------------------------
 *  The name of the acquire audio clip method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    acquireAudioClipMethodName 
                            = "locstor.accessRawAudioData";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    acquireAudioClipSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the audio clip unique ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    acquireAudioClipAudioClipIdParamName = "gunid";

/*------------------------------------------------------------------------------
 *  The name of the result URL parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    acquireAudioClipUrlParamName = "url";

/*------------------------------------------------------------------------------
 *  The name of the token parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    acquireAudioClipTokenParamName = "token";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: releaseAudioClip */

/*------------------------------------------------------------------------------
 *  The name of the release audio clip method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    releaseAudioClipMethodName 
                            = "locstor.releaseRawAudioData";

/*------------------------------------------------------------------------------
 *  The name of the token parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    releaseAudioClipTokenParamName = "token";

/*------------------------------------------------------------------------------
 *  The name of the result parameter returned by the method
 *----------------------------------------------------------------------------*/
const std::string    releaseAudioClipResultParamName = "status";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: createBackupXxxx */

/*------------------------------------------------------------------------------
 *  The name of the 'open' create backup  method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    createBackupOpenMethodName 
                            = "locstor.createBackupOpen";

/*------------------------------------------------------------------------------
 *  The name of the 'check' create backup  method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    createBackupCheckMethodName 
                            = "locstor.createBackupCheck";

/*------------------------------------------------------------------------------
 *  The name of the 'close' create backup  method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    createBackupCloseMethodName 
                            = "locstor.createBackupClose";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    createBackupSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the search criteria parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    createBackupCriteriaParamName = "criteria";

/*------------------------------------------------------------------------------
 *  The name of the token parameter in the input or output structure
 *----------------------------------------------------------------------------*/
const std::string    createBackupTokenParamName = "token";

/*------------------------------------------------------------------------------
 *  The name of the status parameter in the output structure
 *----------------------------------------------------------------------------*/
const std::string    createBackupStatusParamName = "status";

/*------------------------------------------------------------------------------
 *  The name of the URL parameter in the output structure
 *----------------------------------------------------------------------------*/
const std::string    createBackupUrlParamName = "url";

/*------------------------------------------------------------------------------
 *  The name of the 'temporary file' parameter in the output structure
 *----------------------------------------------------------------------------*/
const std::string    createBackupTmpFileParamName = "tmpfile";

/*------------------------------------------------------------------------------
 *  The name of the faultString parameter in the output structure
 *----------------------------------------------------------------------------*/
const std::string    createBackupFaultStringParamName = "faultString";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: restoreBackupXxxx */

/*------------------------------------------------------------------------------
 *  The name of the 'open' restore backup method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    restoreBackupOpenMethodName 
                            = "locstor.restoreBackupOpen";

/*------------------------------------------------------------------------------
 *  The name of the 'close put' restore backup method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    restoreBackupClosePutMethodName 
                            = "locstor.restoreBackupClosePut";

/*------------------------------------------------------------------------------
 *  The name of the 'check' restore backup method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    restoreBackupCheckMethodName 
                            = "locstor.restoreBackupCheck";

/*------------------------------------------------------------------------------
 *  The name of the 'close' restore backup method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    restoreBackupCloseMethodName 
                            = "locstor.restoreBackupClose";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    restoreBackupSessionIdParamName    = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the file name parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    restoreBackupChecksumParamName     = "chsum";

/*------------------------------------------------------------------------------
 *  The name of the URL parameter in the input or output structure
 *----------------------------------------------------------------------------*/
const std::string    restoreBackupUrlParamName          = "url";

/*------------------------------------------------------------------------------
 *  The name of the PUT token parameter in the input or output structure
 *----------------------------------------------------------------------------*/
const std::string    restoreBackupPutTokenParamName     = "token";

/*------------------------------------------------------------------------------
 *  The name of the token parameter in the input or output structure
 *----------------------------------------------------------------------------*/
const std::string    restoreBackupTokenParamName        = "token";

/*------------------------------------------------------------------------------
 *  The name of the status parameter in the output structure
 *----------------------------------------------------------------------------*/
const std::string    restoreBackupStatusParamName       = "status";

/*------------------------------------------------------------------------------
 *  The name of the faultString parameter in the output structure
 *----------------------------------------------------------------------------*/
const std::string    restoreBackupFaultStringParamName  = "faultString";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: exportPlaylistXxxx */

/*------------------------------------------------------------------------------
 *  The name of the 'open' export playlist  method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    exportPlaylistOpenMethodName 
                            = "locstor.exportPlaylistOpen";

/*------------------------------------------------------------------------------
 *  The name of the 'close' export playlist  method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    exportPlaylistCloseMethodName 
                            = "locstor.exportPlaylistClose";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    exportPlaylistSessionIdParamName = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the playlist ID array parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    exportPlaylistPlaylistIdArrayParamName = "plids";

/*------------------------------------------------------------------------------
 *  The name of the format parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    exportPlaylistFormatParamName = "type";

/*------------------------------------------------------------------------------
 *  The name of the 'standalone' parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    exportPlaylistStandaloneParamName = "standalone";

/*------------------------------------------------------------------------------
 *  The name of the URL return parameter in the output structure
 *----------------------------------------------------------------------------*/
const std::string    exportPlaylistUrlParamName = "url";

/*------------------------------------------------------------------------------
 *  The name of the token parameter in the input and output structures
 *----------------------------------------------------------------------------*/
const std::string    exportPlaylistTokenParamName = "token";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: importPlaylist */

/*------------------------------------------------------------------------------
 *  The name of the opening 'import playlist' method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    importPlaylistOpenMethodName 
                            = "locstor.importPlaylistOpen";

/*------------------------------------------------------------------------------
 *  The name of the closing 'import playlist' method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    importPlaylistCloseMethodName 
                            = "locstor.importPlaylistClose";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    importPlaylistSessionIdParamName   = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the checksum parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    importPlaylistChecksumParamName    = "chsum";

/*------------------------------------------------------------------------------
 *  The name of the writable URL parameter in the output structure
 *----------------------------------------------------------------------------*/
const std::string    importPlaylistUrlParamName         = "url";

/*------------------------------------------------------------------------------
 *  The name of the token parameter for both 'open' and 'close'
 *----------------------------------------------------------------------------*/
const std::string    importPlaylistTokenParamName       = "token";

/*------------------------------------------------------------------------------
 *  The name of the unique ID parameter in the output structure
 *----------------------------------------------------------------------------*/
const std::string    importPlaylistUniqueIdParamName    = "gunid";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: remoteSearchXXXX */

/*------------------------------------------------------------------------------
 *  The name of the 'open' remote search  method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    remoteSearchOpenMethodName  = "locstor.globalSearch";

/*------------------------------------------------------------------------------
 *  The name of the 'close' remote search  method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    remoteSearchCloseMethodName = "locstor.getSearchResults";

/*------------------------------------------------------------------------------
 *   The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    remoteSearchSessionIdParamName  = "sessid";

/*------------------------------------------------------------------------------
 *   The name of the criteria parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    remoteSearchCriteriaParamName   = "criteria";

/*------------------------------------------------------------------------------
 *   The name of the token parameter in the input or output structure
 *----------------------------------------------------------------------------*/
const std::string    remoteSearchTokenParamName      = "trtok";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: checkTransport */

/*------------------------------------------------------------------------------
 *  The name of the check transport method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    checkTransportMethodName = "locstor.getTransportInfo";

/*------------------------------------------------------------------------------
 *   The name of the token parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    checkTransportTokenParamName           = "trtok";

/*------------------------------------------------------------------------------
 *   The name of the state parameter in the output structure
 *----------------------------------------------------------------------------*/
const std::string    checkTransportStateParamName           = "state";

/*------------------------------------------------------------------------------
 *   The name of the error message parameter in the output structure
 *----------------------------------------------------------------------------*/
const std::string    checkTransportErrorMessageParamName    = "errmsg";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: doTransportAction */

/*------------------------------------------------------------------------------
 *  The name of the do transport action method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    doTransportActionMethodName = "locstor.doTransportAction";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    doTransportActionSessionIdParamName    = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the token parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    doTransportActionTokenParamName        = "trtok";

/*------------------------------------------------------------------------------
 *  The name of the action parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    doTransportActionActionParamName       = "action";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: uploadToHub */

/*------------------------------------------------------------------------------
 *  The name of the upload to hub method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    uploadToHubMethodName = "locstor.upload2Hub";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    uploadToHubSessionIdParamName    = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the token parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    uploadToHubUniqueIdParamName     = "gunid";

/*------------------------------------------------------------------------------
 *  The name of the 'with or without content' parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    uploadToHubWithContentParamName  = "withContent";

/*------------------------------------------------------------------------------
 *  The name of the token parameter in the output structure
 *----------------------------------------------------------------------------*/
const std::string    uploadToHubTokenParamName        = "trtok";


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  storage server constants: downloadFromHub */

/*------------------------------------------------------------------------------
 *  The name of the download from hub method on the storage server
 *----------------------------------------------------------------------------*/
const std::string    downloadFromHubMethodName = "locstor.downloadFromHub";

/*------------------------------------------------------------------------------
 *  The name of the session ID parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    downloadFromHubSessionIdParamName    = "sessid";

/*------------------------------------------------------------------------------
 *  The name of the token parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    downloadFromHubUniqueIdParamName     = "gunid";

/*------------------------------------------------------------------------------
 *  The name of the 'with or without content' parameter in the input structure
 *----------------------------------------------------------------------------*/
const std::string    downloadFromHubWithContentParamName  = "withContent";

/*------------------------------------------------------------------------------
 *  The name of the token parameter in the output structure
 *----------------------------------------------------------------------------*/
const std::string    downloadFromHubTokenParamName        = "trtok";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the web storage client.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: configure(const xmlpp::Element   &  element)
                                                throw (std::invalid_argument)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute = 0;

    if (!(attribute = element.get_attribute(localTempStorageAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += localTempStorageAttrName;
        throw std::invalid_argument(eMsg);
    }

    localTempStorage = attribute->get_value();

    // read the storage server location
    xmlpp::Node::NodeList   childNodes 
                            = element.get_children(locationConfigElementName);
    xmlpp::Node::NodeList::iterator it = childNodes.begin();

    if (it == childNodes.end()) {
        std::string eMsg = "missing ";
        eMsg += locationConfigElementName;
        eMsg += " XML element";
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Element      * locationConfigElement 
                                = dynamic_cast<const xmlpp::Element*> (*it);
    if (!(attribute = locationConfigElement
                      ->get_attribute(locationServerAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += locationServerAttrName;
        throw std::invalid_argument(eMsg);
    }
    storageServerName = attribute->get_value();

    if (!(attribute = locationConfigElement
                      ->get_attribute(locationPortAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += locationPortAttrName;
        throw std::invalid_argument(eMsg);
    }
    std::stringstream   storageServerPortValue(attribute->get_value());
    storageServerPortValue >> storageServerPort;
    
    if (!(attribute = locationConfigElement
                      ->get_attribute(locationPathAttrName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += locationPathAttrName;
        throw std::invalid_argument(eMsg);
    }
    storageServerPath = attribute->get_value();

    ++it;
    if (it != childNodes.end()) {
        std::string eMsg = "more than one ";
        eMsg += locationConfigElementName;
        eMsg += " XML element";
        throw std::invalid_argument(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Execute an XML-RPC function call.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: execute(const std::string &     methodName,
                            const XmlRpcValue &     parameters,
                            XmlRpcValue &           result) const
                                                throw (XmlRpcException)
{
    XmlRpcClient xmlRpcClient(storageServerName.c_str(), storageServerPort,
                              storageServerPath.c_str(), false);
    
    result.clear();
    if (!xmlRpcClient.execute(methodName.c_str(),
                              parameters,
                              result)) {
        xmlRpcClient.close();
        std::string eMsg = "cannot execute XML-RPC method '";
        eMsg += methodName;
        eMsg += "'";
        throw XmlRpcCommunicationException(eMsg);
    }
    xmlRpcClient.close();
    
    if (xmlRpcClient.isFault()) {
        int                 faultCode   = result[errorCodeParamName];
        std::string         faultString = result[errorMessageParamName];
        throw Core::XmlRpcMethodFaultException(methodName,
                                               faultCode,
                                               faultString);
    }
}


/*------------------------------------------------------------------------------
 *  Check that an XML-RPC struct contains a member of a given type.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: checkStruct(const std::string &     methodName,
                                XmlRpcValue &           xmlRpcStruct,
                                const std::string &     memberName,
                                XmlRpcValue::Type       memberType) const
                                                throw (XmlRpcException)
{
    if (!xmlRpcStruct.hasMember(memberName)) {
        std::stringstream eMsg;
        eMsg << "The return value of the XML-RPC method '" 
             << methodName
             << "',\n"
             << xmlRpcStruct
             << "\ndoes not contain the expected field '"
             << memberName
             << "'." ;
        throw XmlRpcMethodResponseException(eMsg.str());
    }
    
    if (xmlRpcStruct[memberName].getType() != memberType) {
        std::stringstream eMsg;
        eMsg << "In the return value of the XML-RPC method '" 
             << methodName
             << "',\n"
             << xmlRpcStruct
             << "\nthe type of the field '"
             << memberName
             << "' is wrong: "
             << xmlRpcStruct[memberName].getType()
             << " instead of "
             << memberType
             << "." ;
        throw XmlRpcMethodResponseException(eMsg.str());
    }
}


/*------------------------------------------------------------------------------
 *  Return the version string of the test storage.
 *----------------------------------------------------------------------------*/
Ptr<const Glib::ustring>::Ref
WebStorageClient :: getVersion(void)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    // add a dummy parameter, as this is the only way to enforce parameters
    // to be of XML-RPC type struct
    parameters["dummy"] = 0;
    
    execute(getVersionMethodName, parameters, result);
    
    checkStruct(getVersionMethodName,
                result,
                getVersionResultParamName,
                XmlRpcValue::TypeString);

    Ptr<Glib::ustring>::Ref     version(new Glib::ustring(
                                            result[getVersionResultParamName]));
    return version;
}


/*------------------------------------------------------------------------------
 *  Create a new playlist.
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
WebStorageClient :: createPlaylist(Ptr<SessionId>::Ref sessionId)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[createPlaylistSessionIdParamName] 
            = sessionId->getId();

    execute(createPlaylistMethodName, parameters, result);
    
    checkStruct(createPlaylistMethodName,
                result,
                createPlaylistResultParamName,
                XmlRpcValue::TypeString);
    
    Ptr<UniqueId>::Ref          newId(new UniqueId(std::string(
                                    result[createPlaylistResultParamName] )));

    Ptr<const std::string>::Ref url, token;
    
    editPlaylistGetUrl(sessionId, newId, url, token);
    
    Ptr<time_duration>::Ref     playlength(new time_duration(0,0,0,0));
    Ptr<Playlist>::Ref          playlist(new Playlist(newId, playlength));
    playlist->setEditToken(token);
    
    editedPlaylists[newId->getId()] = std::make_pair(sessionId, playlist);
    savePlaylist(sessionId, playlist);
    
    token.reset();
    playlist->setEditToken(token);
    
    return playlist->getId();
}


/*------------------------------------------------------------------------------
 *  Tell if a playlist exists.
 *----------------------------------------------------------------------------*/
bool
WebStorageClient :: existsPlaylist(Ptr<SessionId>::Ref          sessionId,
                                   Ptr<const UniqueId>::Ref     id) const
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[existsPlaylistSessionIdParamName] 
            = sessionId->getId();
    parameters[existsPlaylistPlaylistIdParamName] 
            = std::string(*id);
    
    execute(existsPlaylistMethodName, parameters, result);
    
    checkStruct(existsPlaylistMethodName,
                result,
                existsPlaylistResultParamName,
                XmlRpcValue::TypeBoolean);
    
    return bool(result[existsPlaylistResultParamName]);
}
 

/*------------------------------------------------------------------------------
 *  Return a playlist to be displayed.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: getPlaylist(Ptr<SessionId>::Ref          sessionId,
                                Ptr<const UniqueId>::Ref    id) const
                                                throw (Core::XmlRpcException)
{
    EditedPlaylistsType::const_iterator
                    editIt = editedPlaylists.find(id->getId());

    if (editIt != editedPlaylists.end()                     // is being edited
            && (*editIt->second.first == *sessionId)) {        // by us
         return editIt->second.second;
    }

    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[getPlaylistSessionIdParamName]  = sessionId->getId();
    parameters[getPlaylistPlaylistIdParamName] = std::string(*id);
    parameters[getPlaylistRecursiveParamName]  = false;

    execute(getPlaylistOpenMethodName, parameters, result);
    
    checkStruct(getPlaylistOpenMethodName,
                result,
                getPlaylistTokenParamName,
                XmlRpcValue::TypeString);
    
    Ptr<const std::string>::Ref     token(new std::string(
                                        result[getPlaylistTokenParamName] ));
    
    checkStruct(getPlaylistOpenMethodName,
                result,
                getPlaylistUrlParamName,
                XmlRpcValue::TypeString);
    
    const std::string   url     = result[getPlaylistUrlParamName];

    Ptr<UniqueId>::Ref  idNotConst(new UniqueId(id->getId()));
    Ptr<Playlist>::Ref  playlist(new Playlist(idNotConst));
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser());
        parser->parse_file(url);
        const xmlpp::Document     * document = parser->get_document();
        const xmlpp::Element      * root     = document->get_root_node();

        playlist->configure(*root);

    } catch (std::invalid_argument &e) {
        std::string eMsg = "semantic error in playlist metafile: ";
        eMsg += e.what();
        throw XmlRpcInvalidDataException(eMsg);
    } catch (xmlpp::exception &e) {
        std::string eMsg = "error parsing playlist metafile: ";
        eMsg += e.what();
        throw XmlRpcInvalidDataException(eMsg);
    }
    playlist->setToken(token);

    releasePlaylistFromServer(playlist);
    
    return playlist;
}


/*------------------------------------------------------------------------------
 *  Return a playlist to be edited.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: editPlaylist(Ptr<SessionId>::Ref        sessionId,
                                 Ptr<const UniqueId>::Ref   id)
                                                throw (Core::XmlRpcException)
{
    if (editedPlaylists.find(id->getId()) != editedPlaylists.end()) {
        throw XmlRpcInvalidArgumentException("playlist is already"
                                             " being edited");
    }
    
    Ptr<const std::string>::Ref     url, editToken;

    editPlaylistGetUrl(sessionId, id, url, editToken);

    Ptr<UniqueId>::Ref              idNotConst(new UniqueId(id->getId()));
    Ptr<Playlist>::Ref              playlist(new Playlist(idNotConst));
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser());
        parser->parse_file(*url);
        const xmlpp::Document     * document = parser->get_document();
        const xmlpp::Element      * root     = document->get_root_node();

        playlist->configure(*root);

    } catch (std::invalid_argument &e) {
        std::string eMsg = "semantic error in playlist metafile: ";
        eMsg += e.what();
        throw XmlRpcInvalidDataException(eMsg);
    } catch (xmlpp::exception &e) {
        std::string eMsg = "error parsing playlist metafile: ";
        eMsg += e.what();
        throw XmlRpcInvalidDataException(eMsg);
    }

    playlist->setEditToken(editToken);
    editedPlaylists[id->getId()] = std::make_pair(sessionId, playlist);

    return playlist;
}


/*------------------------------------------------------------------------------
 *  Opens the playlist for editing, and returns its URL.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: editPlaylistGetUrl(Ptr<SessionId>::Ref          sessionId,
                                       Ptr<const UniqueId>::Ref     id,
                                       Ptr<const std::string>::Ref& url,
                                       Ptr<const std::string>::Ref& editToken)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[editPlaylistSessionIdParamName] 
            = sessionId->getId();
    parameters[editPlaylistPlaylistIdParamName] 
            = std::string(*id);
    
    execute(editPlaylistMethodName, parameters, result);
    
    checkStruct(editPlaylistMethodName,
                result,
                editPlaylistUrlParamName,
                XmlRpcValue::TypeString);
    
    checkStruct(editPlaylistMethodName,
                result,
                editPlaylistTokenParamName,
                XmlRpcValue::TypeString);
    
    url.reset(new const std::string(result[getPlaylistUrlParamName]));
    editToken.reset(new const std::string(result[getPlaylistTokenParamName]));
}


/*------------------------------------------------------------------------------
 *  Save a playlist after editing.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: savePlaylist(Ptr<SessionId>::Ref sessionId,
                                 Ptr<Playlist>::Ref  playlist)
                                                throw (Core::XmlRpcException)
{
    if (!playlist || !playlist->getEditToken()) {
        throw XmlRpcInvalidArgumentException("playlist has no editToken field");
    }
    
    EditedPlaylistsType::iterator
                    editIt = editedPlaylists.find(playlist->getId()->getId());
    
    if ((editIt == editedPlaylists.end()) 
            || *editIt->second.first != *sessionId) {
        throw XmlRpcInvalidArgumentException("savePlaylist() called without "
                                             "editPlaylist()");
    }
    editedPlaylists.erase(editIt);

    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[savePlaylistSessionIdParamName] 
            = sessionId->getId();
    parameters[savePlaylistTokenParamName] 
            = *playlist->getEditToken();
    parameters[savePlaylistNewPlaylistParamName] 
            = std::string(*playlist->getXmlDocumentString());

    execute(savePlaylistMethodName, parameters, result);
    
    checkStruct(savePlaylistMethodName,
                result,
                savePlaylistResultParamName,
                XmlRpcValue::TypeString);
    
    if (std::string(result[savePlaylistResultParamName])
                                        != std::string(*playlist->getId())) {
        std::stringstream eMsg;
        eMsg << "Mismatched playlist ID from XML-RPC method '" 
             << savePlaylistMethodName
             << "': "
             << result[savePlaylistResultParamName]
             << " instead of "
             << std::string(*playlist->getId())
             << ".";
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    Ptr<const std::string>::Ref     nullpointer;
    playlist->setEditToken(nullpointer);
}


/*------------------------------------------------------------------------------
 *  Revert a playlist to its pre-editing state.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: revertPlaylist(Ptr<const std::string>::Ref editToken)
                                                throw (XmlRpcException)
{
    if (!editToken) {
        throw XmlRpcInvalidArgumentException("null pointer in argument");
    }
    
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[revertPlaylistSessionIdParamName]        // dummy parameter
            = "";
    parameters[revertPlaylistTokenParamName] 
            = *editToken;

    execute(revertPlaylistMethodName, parameters, result);
    
    checkStruct(revertPlaylistMethodName,
                result,
                revertPlaylistResultParamName,
                XmlRpcValue::TypeString);
}


/*------------------------------------------------------------------------------
 *  Acquire resources for a playlist, step 1: execute the XML-RPC call.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: acquirePlaylist(Ptr<SessionId>::Ref         sessionId,
                                    Ptr<const UniqueId>::Ref    id) const
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[getPlaylistSessionIdParamName]  = sessionId->getId();
    parameters[getPlaylistPlaylistIdParamName] = std::string(*id);
    parameters[getPlaylistRecursiveParamName]  = true;

    execute(getPlaylistOpenMethodName, parameters, result);
    
    checkStruct(getPlaylistOpenMethodName,
                result,
                getPlaylistTokenParamName,
                XmlRpcValue::TypeString);
    
    Ptr<const std::string>::Ref     token(new std::string(
                                        result[getPlaylistTokenParamName] ));

    Ptr<Playlist>::Ref  playlist = acquirePlaylist(id, result);
    playlist->setToken(token);
    
    return playlist;
}


/*------------------------------------------------------------------------------
 *  Acquire resources for a playlist, step 2: create the temp files.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
WebStorageClient :: acquirePlaylist(Ptr<const UniqueId>::Ref    id,
                                    XmlRpcValue &               content) const
                                                throw (Core::XmlRpcException)
{
    // construct the playlist
    checkStruct(getPlaylistOpenMethodName,
                content,
                getPlaylistUrlParamName,
                XmlRpcValue::TypeString);
    
    const std::string   url     = content[getPlaylistUrlParamName];

    Ptr<UniqueId>::Ref  idNotConst(new UniqueId(id->getId()));
    Ptr<Playlist>::Ref  playlist(new Playlist(idNotConst));
    try {
        Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser());
        parser->parse_file(url);
        const xmlpp::Document     * document = parser->get_document();
        const xmlpp::Element      * root     = document->get_root_node();

        playlist->configure(*root);

    } catch (std::invalid_argument &e) {
        std::string eMsg = "semantic error in playlist metafile: ";
        eMsg += e.what();
        throw XmlRpcInvalidDataException(eMsg);
    } catch (xmlpp::exception &e) {
        std::string eMsg = "error parsing playlist metafile: ";
        eMsg += e.what();
        throw XmlRpcInvalidDataException(eMsg);
    }

    // read the content array corresponding to the playlist
    checkStruct(getPlaylistOpenMethodName,
                content,
                getPlaylistContentParamName,
                XmlRpcValue::TypeArray);
    
    XmlRpcValue         innerContent = content[getPlaylistContentParamName];

    // construct the SMIL file
    Ptr<xmlpp::Document>::Ref
                        smilDocument(new xmlpp::Document(xmlVersion));
    xmlpp::Element    * smilRootNode 
                        = smilDocument->create_root_node(smilRootNodeName);
    smilRootNode->set_attribute(smilLanguageAttrName,
                                smilLanguageAttrValue);

    xmlpp::Element    * smilBodyNode
                        = smilRootNode->add_child(smilBodyNodeName);
    xmlpp::Element    * smilParNode
                        = smilBodyNode->add_child(smilParNodeName);
    
    // we assume that the playlist is as long as the size of the content array
    Playlist::const_iterator it = playlist->begin();
    int                      index = 0;
    while (it != playlist->end() && index < innerContent.size()) {
        Ptr<PlaylistElement>::Ref   plElement = it->second;
        Ptr<time_duration>::Ref     relativeOffset
                                              = plElement->getRelativeOffset();
        Ptr<time_duration>::Ref     clipStart
                                              = plElement->getClipStart();
        Ptr<time_duration>::Ref     clipEnd
                                              = plElement->getClipEnd();
        Ptr<time_duration>::Ref     clipLength
                                              = plElement->getClipLength();
											  
        Ptr<FadeInfo>::Ref          fadeInfo  = plElement->getFadeInfo();

        XmlRpcValue     contentElement = innerContent[index];

        Ptr<Playable>::Ref              playable;
        Ptr<const std::string>::Ref     url;
        Ptr<UniqueId>::Ref              subPlaylistId;
        
        switch (plElement->getType()) {
            case PlaylistElement::AudioClipType :
				{
                url.reset(new std::string(
                                    contentElement[getPlaylistUrlParamName]));
                playable = plElement->getAudioClip();
				Ptr<Playable>::Ref audioClip = playable;
				subPlaylistId = audioClip->getId();
				}
                break;
            case PlaylistElement::PlaylistType :
                subPlaylistId = plElement->getPlaylist()->getId();
                playable = acquirePlaylist(subPlaylistId, contentElement);
                url      = playable->getUri();
                plElement->setPlayable(playable);
                break;
            default :     // this should never happen
                throw XmlRpcInvalidArgumentException(
                                           "unexpected playlist element type "
                                           "(neither audio clip nor playlist)");
        }

        xmlpp::Element* smilPlayableNode
                        = smilParNode->add_child(smilPlayableNodeName);
        smilPlayableNode->set_attribute(
                        smilPlayableUriAttrName, 
                        *url );
        smilPlayableNode->set_attribute(
                        smilPlayableIdAttrName, 
                        *subPlaylistId->toDecimalString() );
        smilPlayableNode->set_attribute(
                        smilRelativeOffsetAttrName, 
                        *TimeConversion::timeDurationToSmilString(
                                                            relativeOffset ));
		if(PlaylistElement::AudioClipType == plElement->getType())
		{
			smilPlayableNode->set_attribute(
							smilPlayableStartAttrName, 
							*TimeConversion::timeDurationToSmilString(
																clipStart ));
			smilPlayableNode->set_attribute(
							smilPlayableEndAttrName, 
							*TimeConversion::timeDurationToSmilString(
															clipEnd ));
		}
		
		smilPlayableNode->set_attribute(
						smilPlayableLengthAttrName, 
						*TimeConversion::timeDurationToSmilString(
															clipLength ));

        if (fadeInfo) {
            Ptr<time_duration>::Ref     fadeIn  = fadeInfo->getFadeIn();
            Ptr<time_duration>::Ref     fadeOut = fadeInfo->getFadeOut();

            if (fadeIn) {
                xmlpp::Element* smilFadeInNode
                                = smilPlayableNode->add_child(
                                                        smilAnimateNodeName);
                smilFadeInNode->set_attribute(
                                    smilAnimateNameAttrName,
                                    smilAnimateNameAttrValue );
                smilFadeInNode->set_attribute(
                                    smilAnimateFromAttrName,
                                    "0%" );
                smilFadeInNode->set_attribute(
                                    smilAnimateToAttrName,
                                    "100%" );
                smilFadeInNode->set_attribute(
                                    smilAnimateCalcModeAttrName,
                                    smilAnimateCalcModeAttrValue );
                smilFadeInNode->set_attribute(
                                    smilAnimateBeginAttrName,
                                    "0s" );
                smilFadeInNode->set_attribute(
                                    smilAnimateEndAttrName,
                                    *TimeConversion::timeDurationToSmilString(
                                                                fadeIn ));
                smilFadeInNode->set_attribute(
                                    smilAnimateCalcModeAttrName,
                                    smilAnimateCalcModeAttrValue );
                smilFadeInNode->set_attribute(
                                    smilAnimateFillAttrName,
                                    smilAnimateFillAttrValue );
            }

            if (fadeOut) {
                xmlpp::Element* smilFadeOutNode
                                = smilPlayableNode->add_child(
                                                        smilAnimateNodeName);
                smilFadeOutNode->set_attribute(
                                    smilAnimateNameAttrName,
                                    smilAnimateNameAttrValue );
                smilFadeOutNode->set_attribute(
                                    smilAnimateFromAttrName,
                                    "100%" );
                smilFadeOutNode->set_attribute(
                                    smilAnimateToAttrName,
                                    "0%" );
                smilFadeOutNode->set_attribute(
                                    smilAnimateCalcModeAttrName,
                                    smilAnimateCalcModeAttrValue );
                Ptr<time_duration>::Ref  playlength = playable->getPlaylength();
                Ptr<time_duration>::Ref  fadeBegin(new time_duration(
                                                    *playlength - *fadeOut ));
                smilFadeOutNode->set_attribute(
                                    smilAnimateBeginAttrName,
                                    *TimeConversion::timeDurationToSmilString(
                                                                fadeBegin ));
                smilFadeOutNode->set_attribute(
                                    smilAnimateEndAttrName,
                                    *TimeConversion::timeDurationToSmilString(
                                                                playlength ));
                smilFadeOutNode->set_attribute(
                                    smilAnimateCalcModeAttrName,
                                    smilAnimateCalcModeAttrValue );
                smilFadeOutNode->set_attribute(
                                    smilAnimateFillAttrName,
                                    smilAnimateFillAttrValue );
            }
        }
        ++it;
        ++index;
    }

    std::stringstream fileName;
    fileName << localTempStorage << std::string(*playlist->getId())
             << "-" << std::rand() << ".smil";

    try {
        smilDocument->write_to_file_formatted(fileName.str(), "UTF-8");
    } catch (xmlpp::exception &e) {
        std::string     errorMessage = "could not write the temp file in "
                                       "WebStorageClient::acquirePlaylist: ";
        errorMessage += e.what();
        throw XmlRpcIOException(errorMessage);
    }
    Ptr<std::string>::Ref   playlistUri(new std::string(fileName.str()));
    playlist->setUri(playlistUri);

    return playlist;
}


/*------------------------------------------------------------------------------
 *  Release a playlist.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: releasePlaylist(Ptr<Playlist>::Ref  playlist) const
                                                throw (Core::XmlRpcException)
{
    if (playlist->getToken()) {
        releasePlaylistFromServer(playlist);
    }
    
    releasePlaylistTempFile(playlist);
}
    
    
/*------------------------------------------------------------------------------
 *  Release a playlist, step 1: release access URLs at the storage server.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: releasePlaylistFromServer(
                                        Ptr<Playlist>::Ref      playlist) const
                                                throw (Core::XmlRpcException)
{
    if (! playlist->getToken()) {
        throw XmlRpcInvalidArgumentException("releasePlaylist() called without"
                                             " acquirePlaylist()");
    }

    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[getPlaylistTokenParamName] = std::string(*playlist->getToken());
    
    execute(getPlaylistCloseMethodName, parameters, result);
    
    checkStruct(getPlaylistCloseMethodName,
                result,
                getPlaylistPlaylistIdParamName,
                XmlRpcValue::TypeString);
    
    if (std::string(result[getPlaylistPlaylistIdParamName])
                                        != std::string(*playlist->getId())) {
        std::stringstream eMsg;
        eMsg << "Mismatched playlist ID from XML-RPC method '" 
             << getPlaylistCloseMethodName
             << "': "
             << result[getPlaylistPlaylistIdParamName]
             << " instead of "
             << std::string(*playlist->getId())
             << ".";
        throw XmlRpcMethodResponseException(eMsg.str());
    }
}


/*------------------------------------------------------------------------------
 *  Release a playlist, step 2: delete the temp file.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: releasePlaylistTempFile(Ptr<Playlist>::Ref  playlist) const
                                                throw (Core::XmlRpcException)
{
    if (! playlist->getUri()) {
        throw XmlRpcInvalidArgumentException("playlist URI not found");
    }

    std::ifstream ifs(playlist->getUri()->substr(7).c_str());
    if (!ifs) {                                              // cut of "file://"
        ifs.close();
        throw XmlRpcIOException("playlist temp file not found");
    }
    ifs.close();

    std::remove(playlist->getUri()->substr(7).c_str());
   
    std::string                 eMsg = "";
    Playlist::const_iterator    it   = playlist->begin();
    while (it != playlist->end()) {
        Ptr<PlaylistElement>::Ref   plElement = it->second;
        if (plElement->getType() == PlaylistElement::AudioClipType) {
            // no temp file; nothing to do
            ++it;
        } else if (plElement->getType() == PlaylistElement::PlaylistType) {
            try {
                releasePlaylistTempFile(it->second->getPlaylist());
            }
            catch (XmlRpcException &e) {
                eMsg += e.what();
                eMsg += '\n';
            }
            ++it;
        } else {
            // this should never happen
            eMsg += "unexpected playlist element type\n";
        }        
    }

    Ptr<std::string>::Ref   nullPointer;
    playlist->setUri(nullPointer);

    if (eMsg != "") {
        eMsg.insert(0, "some playlist elements could not be released:\n");
        throw XmlRpcInvalidArgumentException(eMsg);
    }
}


/*------------------------------------------------------------------------------
 *  Tell if an audio clip exists.
 *----------------------------------------------------------------------------*/
bool
WebStorageClient :: existsAudioClip(Ptr<SessionId>::Ref         sessionId,
                                    Ptr<const UniqueId>::Ref    id) const
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[existsAudioClipSessionIdParamName] 
            = sessionId->getId();
    parameters[existsAudioClipAudioClipIdParamName] 
            = std::string(*id);
    
    execute(existsAudioClipMethodName, parameters, result);
    
    checkStruct(existsAudioClipMethodName,
                result,
                existsAudioClipResultParamName,
                XmlRpcValue::TypeBoolean);
    
    return bool(result[existsAudioClipResultParamName]);
}
 

/*------------------------------------------------------------------------------
 *  Retrieve an audio clip from the storage.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
WebStorageClient :: getAudioClip(Ptr<SessionId>::Ref        sessionId,
                                 Ptr<const UniqueId>::Ref   id) const
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[getAudioClipSessionIdParamName] 
            = sessionId->getId();
    parameters[getAudioClipAudioClipIdParamName] 
            = std::string(*id);
    
    execute(getAudioClipOpenMethodName, parameters, result);
    
    checkStruct(getAudioClipOpenMethodName,
                result,
                getAudioClipUrlParamName,
                XmlRpcValue::TypeString);
    
    checkStruct(getAudioClipOpenMethodName,
                result,
                getAudioClipTokenParamName,
                XmlRpcValue::TypeString);
    
    const std::string   url     = result[getAudioClipUrlParamName];
    const std::string   token   = result[getAudioClipTokenParamName];

    Ptr<UniqueId>::Ref  idNotConst(new UniqueId(id->getId()));
    Ptr<AudioClip>::Ref audioClip(new AudioClip(idNotConst));

    try {
        Ptr<xmlpp::DomParser>::Ref  parser(new xmlpp::DomParser());
        parser->parse_file(url);
        const xmlpp::Document     * document = parser->get_document();
        const xmlpp::Element      * root     = document->get_root_node();

        audioClip->configure(*root);

    } catch (std::invalid_argument &e) {
        std::string eMsg = "semantic error in audio clip metafile: ";
        eMsg += e.what();
        throw XmlRpcInvalidDataException(eMsg);
    } catch (xmlpp::exception &e) {
        std::string eMsg = "error parsing audio clip metafile: ";
        eMsg += e.what();
        throw XmlRpcInvalidDataException(eMsg);
    }

    parameters.clear();
    parameters[getAudioClipSessionIdParamName] = sessionId->getId();
    parameters[getAudioClipTokenParamName]     = token;
    
    execute(getAudioClipCloseMethodName, parameters, result);
    
    checkStruct(getAudioClipCloseMethodName,
                result,
                getAudioClipAudioClipIdParamName,
                XmlRpcValue::TypeString);
    
    if (std::string(result[getAudioClipAudioClipIdParamName])
                                                    != std::string(*id)) {
        std::stringstream eMsg;
        eMsg << "Mismatched audio clip ID from XML-RPC method '" 
             << getAudioClipCloseMethodName
             << "': "
             << result[getAudioClipAudioClipIdParamName]
             << " instead of "
             << std::string(*id)
             << ".";
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    return audioClip;
}


/*------------------------------------------------------------------------------
 *  Upload an audio clip to the local storage.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: storeAudioClip(Ptr<SessionId>::Ref sessionId,
                                   Ptr<AudioClip>::Ref audioClip)
                                                throw (Core::XmlRpcException)
{
    if (!audioClip || !audioClip->getUri()) {
        throw XmlRpcInvalidArgumentException(
                                        "binary audio clip file not found");
    }
    
    // temporary hack; we will expect an absolute file name from getUri()
    //   in the final version
    std::string     binaryFileName = audioClip->getUri()->substr(5);
    std::ifstream   ifs(binaryFileName.c_str());
    if (!ifs) {
        ifs.close();
        throw XmlRpcIOException("could not read audio clip");
    }
    std::string     md5string = Md5(ifs);
    ifs.close();

    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[storeAudioClipSessionIdParamName] 
            = sessionId->getId();
    if (audioClip->getId()) {
        parameters[storeAudioClipAudioClipIdParamName]
            = std::string(*audioClip->getId());
    }
    parameters[storeAudioClipMetadataParamName] 
            = std::string(*audioClip->getXmlDocumentString());
    parameters[storeAudioClipFileNameParamName] 
            = std::string(*audioClip->getUri());
    parameters[storeAudioClipChecksumParamName] 
            = md5string;

    execute(storeAudioClipOpenMethodName, parameters, result);
    
    checkStruct(storeAudioClipOpenMethodName,
                result,
                storeAudioClipUrlParamName,
                XmlRpcValue::TypeString);
    
    checkStruct(storeAudioClipOpenMethodName,
                result,
                storeAudioClipTokenParamName,
                XmlRpcValue::TypeString);
    
    std::string url     = std::string(result[storeAudioClipUrlParamName]);
    std::string token   = std::string(result[storeAudioClipTokenParamName]);
    
    try {
        FileTools::copyFileToUrl(binaryFileName, url);
        
    } catch (std::runtime_error &e) {
        throw XmlRpcCommunicationException(e.what());
    }
    
    parameters.clear();
    parameters[storeAudioClipSessionIdParamName] 
            = sessionId->getId();
    parameters[storeAudioClipTokenParamName] 
            = token;
    
    execute(storeAudioClipCloseMethodName, parameters, result);
    
    checkStruct(storeAudioClipCloseMethodName,
                result,
                storeAudioClipAudioClipIdParamName,
                XmlRpcValue::TypeString);
    
    if (audioClip->getId()
            && std::string(result[storeAudioClipAudioClipIdParamName])
                                        != std::string(*audioClip->getId())) {
        std::stringstream eMsg;
        eMsg << "Mismatched audio clip ID from XML-RPC method '" 
             << storeAudioClipCloseMethodName
             << "': "
             << result[storeAudioClipAudioClipIdParamName]
             << " instead of "
             << std::string(*audioClip->getId())
             << ".";
        throw XmlRpcMethodResponseException(eMsg.str());
    }

    if (!audioClip->getId()) {
        Ptr<UniqueId>::Ref  newId(new UniqueId(std::string(
                                result[storeAudioClipAudioClipIdParamName] )));
        audioClip->setId(newId);
    }
}


/*------------------------------------------------------------------------------
 *  Acquire resources for an audio clip.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
WebStorageClient :: acquireAudioClip(Ptr<SessionId>::Ref        sessionId,
                                     Ptr<const UniqueId>::Ref   id) const
                                                throw (Core::XmlRpcException)
{
    Ptr<AudioClip>::Ref  audioClip = getAudioClip(sessionId, id);

    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[acquireAudioClipSessionIdParamName] 
            = sessionId->getId();
    parameters[acquireAudioClipAudioClipIdParamName] 
            = std::string(*id);
    
    execute(acquireAudioClipMethodName, parameters, result);
    
    checkStruct(acquireAudioClipMethodName,
                result,
                acquireAudioClipUrlParamName,
                XmlRpcValue::TypeString);
    
    checkStruct(acquireAudioClipMethodName,
                result,
                acquireAudioClipTokenParamName,
                XmlRpcValue::TypeString);
    
    Ptr<const std::string>::Ref uri(new const std::string(
                                    result[acquireAudioClipUrlParamName] ));
    Ptr<const std::string>::Ref token(new const std::string( 
                                    result[acquireAudioClipTokenParamName] ));

    audioClip->setUri(uri);
    audioClip->setToken(token);

    return audioClip;    
}


/*------------------------------------------------------------------------------
 *  Release an audio clip.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: releaseAudioClip(Ptr<AudioClip>::Ref audioClip) const
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[releaseAudioClipTokenParamName] 
            = *audioClip->getToken();
    
    execute(releaseAudioClipMethodName, parameters, result);
    
    checkStruct(releaseAudioClipMethodName,
                result,
                releaseAudioClipResultParamName,
                XmlRpcValue::TypeBoolean);
    
    if (! bool(result[releaseAudioClipResultParamName])) {
        std::stringstream eMsg;
        eMsg << "XML-RPC method '" 
             << releaseAudioClipMethodName
             << "' returned 'false'";
        throw XmlRpcMethodResponseException(eMsg.str());
    }
    
    Ptr<const std::string>::Ref     nullpointer;
    audioClip->setToken(nullpointer);
    audioClip->setUri(nullpointer);
}


/*------------------------------------------------------------------------------
 *  Reset the storage to its initial state.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: reset(void)
                                                throw (Core::XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters["dummy_param"] = "dummy_value"; 
    
    execute(resetStorageMethodName, parameters, result);
    
    extractSearchResults(resetStorageMethodName, result, localSearchResults);
    
    editedPlaylists.clear();
}


/*------------------------------------------------------------------------------
 *  Search for audio clips or playlists.
 *----------------------------------------------------------------------------*/
int
WebStorageClient :: search(Ptr<SessionId>::Ref      sessionId,
                           Ptr<SearchCriteria>::Ref searchCriteria)
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[searchSessionIdParamName] 
            = sessionId->getId();
    parameters[searchCriteriaParamName] 
            = *searchCriteria;

    execute(searchMethodName, parameters, result);
    
    return extractSearchResults(searchMethodName, result, localSearchResults);
}

    
/*------------------------------------------------------------------------------
 *  Extract the results returned by search() or remoteSearchClose().
 *----------------------------------------------------------------------------*/
int
WebStorageClient :: extractSearchResults(
                                const std::string &             methodName,
                                XmlRpcValue &                   xmlRpcStruct,
                                Ptr<SearchResultsType>::Ref &   searchResults)
                                                throw (XmlRpcException)
{
    checkStruct(methodName,
                xmlRpcStruct,
                searchResultParamName,
                XmlRpcValue::TypeArray);
    
    XmlRpcValue resultArray = xmlRpcStruct[searchResultParamName];
    
    searchResults.reset(new SearchResultsType);
    Ptr<Playable>::Ref      playable;
    
    for (int i=0; i < resultArray.size(); i++) {
        if (resultArray[i].getType() != XmlRpcValue::TypeStruct) {
            std::stringstream eMsg;
            eMsg << "The 'results' parameter returned by XML-RPC method '"
                 << methodName
                 << "' is expected to be an array of structs, but it isn't:\n"
                 << xmlRpcStruct;
            throw XmlRpcMethodResponseException(eMsg.str());
        }
        
        try {
            playable = createPlayable(resultArray[i]);
            
        } catch (std::invalid_argument &e) {
            std::stringstream eMsg;
            eMsg << "Malformed item returned by XML-RPC method '"
                 << methodName
                 << "': "
                 << resultArray[i];
            throw XmlRpcMethodResponseException(eMsg.str());
        }
        
        if (playable && playable->getPlaylist()) {
                                                    // can be 0 if a web stream
            searchResults->push_back(playable);     // is found
        }
    }
    
    // TODO: REMOVE STARTING HERE (see ticket #1701)
    // <<<
    for (int i=0; i < resultArray.size(); i++) {
        if (resultArray[i].getType() != XmlRpcValue::TypeStruct) {
            std::stringstream eMsg;
            eMsg << "The 'results' parameter returned by XML-RPC method '"
                 << methodName
                 << "' is expected to be an array of structs, but it isn't:\n"
                 << xmlRpcStruct;
            throw XmlRpcMethodResponseException(eMsg.str());
        }
        
        try {
            playable = createPlayable(resultArray[i]);
            
        } catch (std::invalid_argument &e) {
            std::stringstream eMsg;
            eMsg << "Malformed item returned by XML-RPC method '"
                 << methodName
                 << "': "
                 << resultArray[i];
            throw XmlRpcMethodResponseException(eMsg.str());
        }
        
        if (playable && playable->getAudioClip()) {
                                                    // can be 0 if a web stream
            searchResults->push_back(playable);     // is found
        }
    }
    // >>>
    // TODO: REMOVE UNTIL HERE (and fix line x-36)
    
    checkStruct(methodName,
                xmlRpcStruct,
                searchCountParamName,
                XmlRpcValue::TypeInt);
    
    return int(xmlRpcStruct[searchCountParamName]);
}


/*------------------------------------------------------------------------------
 *  Browse for metadata values.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Glib::ustring> >::Ref
WebStorageClient :: browse(Ptr<SessionId>::Ref              sessionId,
                           Ptr<const Glib::ustring>::Ref    metadataType,
                           Ptr<SearchCriteria>::Ref         searchCriteria) 
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[browseSessionIdParamName] 
            = sessionId->getId();
    parameters[browseMetadataParamName] 
            = std::string(*metadataType);
    parameters[browseCriteriaParamName] 
            = *searchCriteria;

    execute(browseMethodName, parameters, result);
    
    checkStruct(browseMethodName,
                result,
                browseResultParamName,
                XmlRpcValue::TypeArray);
    
    XmlRpcValue     metadataValues = result[browseResultParamName];
    Ptr<std::vector<Glib::ustring> >::Ref 
                                    results(new std::vector<Glib::ustring>);
    
    for (int i=0; i < metadataValues.size(); i++) {
        if (metadataValues[i].getType() != XmlRpcValue::TypeString) {
            std::stringstream eMsg;
            eMsg << "Non-string metadata value returned by XML-RPC method '"
                 << browseMethodName
                 << "':\n"
                 << result;
            throw XmlRpcMethodResponseException(eMsg.str());
        }
        results->push_back(Glib::ustring(metadataValues[i]));
    }

    return results;
}


/*------------------------------------------------------------------------------
 *  Search for audio clips or playlists on a remote network hub.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
WebStorageClient :: remoteSearchOpen(Ptr<SessionId>::Ref        sessionId,
                                     Ptr<SearchCriteria>::Ref   searchCriteria)
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[remoteSearchSessionIdParamName] 
            = sessionId->getId();
    parameters[remoteSearchCriteriaParamName] 
            = *searchCriteria;

    execute(remoteSearchOpenMethodName, parameters, result);
    
    checkStruct(remoteSearchOpenMethodName,
                result,
                remoteSearchTokenParamName,
                XmlRpcValue::TypeString);
    
    Ptr<Glib::ustring>::Ref     token(new Glib::ustring( 
                                    result[remoteSearchTokenParamName] ));
    
    return token;
}


/*------------------------------------------------------------------------------
 *  Download the search results after the remote search has finished.
 *----------------------------------------------------------------------------*/
int
WebStorageClient :: remoteSearchClose(Ptr<const Glib::ustring>::Ref     token) 
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[remoteSearchTokenParamName] 
            = std::string(*token);

    execute(remoteSearchCloseMethodName, parameters, result);
    
    return extractSearchResults(remoteSearchCloseMethodName,
                                result,
                                remoteSearchResults);
}


/*------------------------------------------------------------------------------
 *  Return a list of all playlists in the storage.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref
WebStorageClient :: getAllPlaylists(Ptr<SessionId>::Ref sessionId,
                                    int                 limit,
                                    int                 offset)
                                                throw (XmlRpcException)
{
    Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria(
                                                std::string("playlist")));
    criteria->setLimit(limit);
    criteria->setOffset(offset);
    search(sessionId, criteria);
    
    Ptr<std::vector<Ptr<Playlist>::Ref> >::Ref      playlists(
                                        new std::vector<Ptr<Playlist>::Ref>);
    
    SearchResultsType::const_iterator it;
    for (it = localSearchResults->begin();
                                    it != localSearchResults->end(); ++it) {
        Ptr<Playlist>::Ref      playlist = (*it)->getPlaylist();
        if (playlist) {
            playlists->push_back(playlist);
        }
    }

    return playlists;
}


/*------------------------------------------------------------------------------
 *  Return a list of all audio clips in the storage.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref
WebStorageClient :: getAllAudioClips(Ptr<SessionId>::Ref    sessionId,
                                     int                    limit,
                                     int                    offset)
                                                throw (XmlRpcException)
{
    Ptr<SearchCriteria>::Ref    criteria(new SearchCriteria(
                                                std::string("audioClip")));
    criteria->setLimit(limit);
    criteria->setOffset(offset);
    search(sessionId, criteria);
    
    Ptr<std::vector<Ptr<AudioClip>::Ref> >::Ref     audioClips(
                                        new std::vector<Ptr<AudioClip>::Ref>);
    
    SearchResultsType::const_iterator it;
    for (it = localSearchResults->begin();
                                    it != localSearchResults->end(); ++it) {
        Ptr<AudioClip>::Ref     audioClip = (*it)->getAudioClip();
        if (audioClip) {
            audioClips->push_back(audioClip);
        }
    }

    return audioClips;
}


/*------------------------------------------------------------------------------
 *  Initiate the creation of a storage backup.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
WebStorageClient :: createBackupOpen(Ptr<SessionId>::Ref        sessionId,
                                     Ptr<SearchCriteria>::Ref   criteria) const
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[createBackupSessionIdParamName] 
            = sessionId->getId();
    parameters[createBackupCriteriaParamName] 
            = *criteria;

    execute(createBackupOpenMethodName, parameters, result);
    
    checkStruct(createBackupOpenMethodName,
                result,
                createBackupTokenParamName,
                XmlRpcValue::TypeString);
    
    Ptr<Glib::ustring>::Ref     token(new Glib::ustring( 
                                    result[createBackupTokenParamName] ));
    
    return token;
}


/*------------------------------------------------------------------------------
 *  Check the status of a storage backup.
 *----------------------------------------------------------------------------*/
AsyncState
WebStorageClient :: createBackupCheck(
                          const Glib::ustring &             token,
                          Ptr<const Glib::ustring>::Ref &   url,
                          Ptr<const Glib::ustring>::Ref &   path,
                          Ptr<const Glib::ustring>::Ref &   errorMessage) const
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[createBackupTokenParamName] 
            = std::string(token);

    execute(createBackupCheckMethodName, parameters, result);
    
    checkStruct(createBackupCheckMethodName,
                result,
                createBackupStatusParamName,
                XmlRpcValue::TypeString);
    
    std::string     stateString = result[createBackupStatusParamName];    
    AsyncState      state       = AsyncState::fromBackupString(stateString);
    
    if (state == AsyncState::finishedState) {
        checkStruct(createBackupCheckMethodName,
                    result,
                    createBackupUrlParamName,
                    XmlRpcValue::TypeString);
        
        url.reset(new const Glib::ustring(
                        std::string(result[createBackupUrlParamName]) ));
        
        checkStruct(createBackupCheckMethodName,
                    result,
                    createBackupTmpFileParamName,
                    XmlRpcValue::TypeString);
        
        path.reset(new const Glib::ustring(
                        std::string(result[createBackupTmpFileParamName]) ));
            
    } else if (state == AsyncState::failedState) {
        checkStruct(createBackupCheckMethodName,
                    result,
                    createBackupFaultStringParamName,
                    XmlRpcValue::TypeString);
        
        errorMessage.reset(new Glib::ustring(
                        std::string(result[createBackupFaultStringParamName])));
        
    } else if (state == AsyncState::invalidState) {
        std::stringstream eMsg;
        eMsg << "Incorrect value '"
             << stateString
             << "' returned by the XML-RPC method '" 
             << createBackupCheckMethodName
             << "; expected one of 'working', 'success' or 'fault'.";
        throw XmlRpcMethodResponseException(eMsg.str());
    }
    
    return state;
}

        
/*------------------------------------------------------------------------------
 *  Close the storage backup process.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: createBackupClose(const Glib::ustring &     token) const
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[createBackupTokenParamName] 
            = std::string(token);

    execute(createBackupCloseMethodName, parameters, result);
}


/*------------------------------------------------------------------------------
 *  Initiate the uploading of a storage backup to the local storage.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
WebStorageClient :: restoreBackupOpen(
                        Ptr<SessionId>::Ref             sessionId,
                        Ptr<const Glib::ustring>::Ref   path)           const
                                                throw (XmlRpcException)
{
    std::ifstream   ifs(path->c_str());
    if (!ifs) {
        ifs.close();
        throw XmlRpcIOException("Could not read the playlist archive file.");
    }
    std::string     md5string = Md5(ifs);
    ifs.close();

    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[restoreBackupSessionIdParamName] 
            = sessionId->getId();
    parameters[restoreBackupChecksumParamName] 
            = md5string;

    execute(restoreBackupOpenMethodName, parameters, result);
    
    checkStruct(restoreBackupOpenMethodName,
                result,
                restoreBackupUrlParamName,
                XmlRpcValue::TypeString);
    
    checkStruct(restoreBackupOpenMethodName,
                result,
                restoreBackupPutTokenParamName,
                XmlRpcValue::TypeString);
    
    std::string url      = std::string(result[restoreBackupUrlParamName]);
    std::string putToken = std::string(result[restoreBackupPutTokenParamName]);
    
    try {
        FileTools::copyFileToUrl(*path, url);
        
    } catch (std::runtime_error &e) {
        throw XmlRpcCommunicationException(e.what());
    }
    
    parameters.clear();
    parameters[restoreBackupSessionIdParamName] 
            = sessionId->getId();
    parameters[restoreBackupPutTokenParamName]
            = putToken;
    
    result.clear();
    execute(restoreBackupClosePutMethodName, parameters, result);
    
    checkStruct(restoreBackupClosePutMethodName,
                result,
                restoreBackupTokenParamName,
                XmlRpcValue::TypeString);
    
    Ptr<Glib::ustring>::Ref     token(new Glib::ustring( 
                                    result[restoreBackupTokenParamName] ));
    
    return token;
}


/*------------------------------------------------------------------------------
 *  Check the status of a backup restore.
 *----------------------------------------------------------------------------*/
AsyncState
WebStorageClient :: restoreBackupCheck(
                        const Glib::ustring &           token,
                        Ptr<const Glib::ustring>::Ref & errorMessage)   const
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[restoreBackupTokenParamName] 
            = std::string(token);

    execute(restoreBackupCheckMethodName, parameters, result);
    
    checkStruct(restoreBackupCheckMethodName,
                result,
                restoreBackupStatusParamName,
                XmlRpcValue::TypeString);
       
    std::string     stateString = result[restoreBackupStatusParamName];    
    AsyncState      state       = AsyncState::fromBackupString(stateString);
    
    if (state == AsyncState::failedState) {
        checkStruct(restoreBackupCheckMethodName,
                    result,
                    restoreBackupFaultStringParamName,
                    XmlRpcValue::TypeString);
        
        errorMessage.reset(new Glib::ustring(
                    std::string(result[restoreBackupFaultStringParamName])));
    
    } else if (state == AsyncState::invalidState) {
        std::stringstream eMsg;
        eMsg << "Incorrect value '"
             << stateString
             << "' returned by the XML-RPC method '" 
             << restoreBackupCheckMethodName
             << "; expected one of 'working', 'success' or 'fault'.";
        throw XmlRpcMethodResponseException(eMsg.str());
    }
    
    return state;
}


/*------------------------------------------------------------------------------
 *  Close the backup restore process.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: restoreBackupClose(const Glib::ustring &    token) const
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[restoreBackupTokenParamName] 
            = std::string(token);

    execute(restoreBackupCloseMethodName, parameters, result);
}


/*------------------------------------------------------------------------------
 *  Initiate the exporting of a playlist.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
WebStorageClient :: exportPlaylistOpen(Ptr<SessionId>::Ref      sessionId,
                                       Ptr<UniqueId>::Ref       playlistId,
                                       ExportFormatType         format,
                                       Ptr<Glib::ustring>::Ref  url) const
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    XmlRpcValue     playlistIdArray;
    playlistIdArray.setSize(1);
    playlistIdArray[0] = std::string(*playlistId);
    
    parameters.clear();
    parameters[exportPlaylistSessionIdParamName] 
            = sessionId->getId();
    parameters[exportPlaylistPlaylistIdArrayParamName] 
            = playlistIdArray;
    switch (format) {
        case internalFormat:    parameters[exportPlaylistFormatParamName] 
                                        = "lspl";
                                break;
                                
        case smilFormat:        parameters[exportPlaylistFormatParamName] 
                                        = "smil";
                                break;
    }
    parameters[exportPlaylistStandaloneParamName]
            = false;
    
    execute(exportPlaylistOpenMethodName, parameters, result);
    
    checkStruct(exportPlaylistOpenMethodName,
                result,
                exportPlaylistUrlParamName,
                XmlRpcValue::TypeString);
    
    url->assign(std::string(result[exportPlaylistUrlParamName]));
    
    checkStruct(exportPlaylistOpenMethodName,
                result,
                exportPlaylistTokenParamName,
                XmlRpcValue::TypeString);
    
    Ptr<Glib::ustring>::Ref     token(new Glib::ustring( 
                                    result[exportPlaylistTokenParamName] ));
    
    return token;
}


/*------------------------------------------------------------------------------
 *  Close the playlist export process.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: exportPlaylistClose(
                            Ptr<const Glib::ustring>::Ref   token) const
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[exportPlaylistTokenParamName] 
            = std::string(*token);

    execute(exportPlaylistCloseMethodName, parameters, result);
}


/*------------------------------------------------------------------------------
 *  Import a playlist archive to the local storage.
 *----------------------------------------------------------------------------*/
Ptr<UniqueId>::Ref
WebStorageClient :: importPlaylist(
                            Ptr<SessionId>::Ref             sessionId,
                            Ptr<const Glib::ustring>::Ref   path)       const
                                                throw (XmlRpcException)
{
    std::ifstream   ifs(path->c_str());
    if (!ifs) {
        ifs.close();
        throw XmlRpcIOException("Could not read the playlist archive file.");
    }
    std::string     md5string = Md5(ifs);
    ifs.close();

    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[importPlaylistSessionIdParamName] 
            = sessionId->getId();
    parameters[importPlaylistChecksumParamName] 
            = md5string;

    execute(importPlaylistOpenMethodName, parameters, result);
    
    checkStruct(importPlaylistOpenMethodName,
                result,
                importPlaylistUrlParamName,
                XmlRpcValue::TypeString);
    
    checkStruct(importPlaylistOpenMethodName,
                result,
                importPlaylistTokenParamName,
                XmlRpcValue::TypeString);
    
    std::string url     = std::string(result[importPlaylistUrlParamName]);
    std::string token   = std::string(result[importPlaylistTokenParamName]);
    
    try {
        FileTools::copyFileToUrl(*path, url);
        
    } catch (std::runtime_error &e) {
        throw XmlRpcCommunicationException(e.what());
    }
    
    parameters.clear();
    parameters[importPlaylistTokenParamName] 
            = token;
    
    execute(importPlaylistCloseMethodName, parameters, result);
    
    checkStruct(importPlaylistCloseMethodName,
                result,
                importPlaylistUniqueIdParamName,
                XmlRpcValue::TypeString);
    
    Ptr<UniqueId>::Ref  id(new UniqueId(std::string(
                                result[importPlaylistUniqueIdParamName] )));
    return id;
}


/*------------------------------------------------------------------------------
 *  Check the status of the asynchronous network transport operation.
 *----------------------------------------------------------------------------*/
AsyncState
WebStorageClient :: checkTransport(Ptr<const Glib::ustring>::Ref  token,
                                   Ptr<Glib::ustring>::Ref        errorMessage)
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[checkTransportTokenParamName] 
            = std::string(*token);

    execute(checkTransportMethodName, parameters, result);
    
    checkStruct(checkTransportMethodName,
                result,
                checkTransportStateParamName,
                XmlRpcValue::TypeString);
    
    std::string     stateString = result[checkTransportStateParamName];    
    AsyncState      state       = AsyncState::fromTransportString(stateString);

    if (state == AsyncState::failedState) {
        if (errorMessage) {
            checkStruct(checkTransportMethodName,
                        result,
                        checkTransportErrorMessageParamName,
                        XmlRpcValue::TypeString);
            errorMessage->assign(std::string(
                                result[checkTransportErrorMessageParamName]));
        }
        
    } else if (state == AsyncState::invalidState) {
        std::stringstream eMsg;
        eMsg << "Unrecognized transport state returned by XML-RPC method '"
                << checkTransportMethodName
                << "':\n"
                << result;
        throw XmlRpcMethodResponseException(eMsg.str());
    }
    
    return state;
}


/*------------------------------------------------------------------------------
 *  Cancel an asynchronous network transport operation.
 *----------------------------------------------------------------------------*/
void
WebStorageClient :: cancelTransport(Ptr<SessionId>::Ref             sessionId,
                                    Ptr<const Glib::ustring>::Ref   token)
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[doTransportActionSessionIdParamName] 
            = sessionId->getId();
    parameters[doTransportActionTokenParamName] 
            = std::string(*token);
    parameters[doTransportActionActionParamName] 
            = "cancel";

    execute(doTransportActionMethodName, parameters, result);
}


/*------------------------------------------------------------------------------
 *  Upload an audio clip or playlist to the network hub.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
WebStorageClient :: uploadToHub(Ptr<const SessionId>::Ref       sessionId,
                                Ptr<const UniqueId>::Ref        id)
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[uploadToHubSessionIdParamName] 
            = sessionId->getId();
    parameters[uploadToHubUniqueIdParamName] 
            = std::string(*id);
    parameters[uploadToHubWithContentParamName] 
            = true;
    
    execute(uploadToHubMethodName, parameters, result);
    
    checkStruct(uploadToHubMethodName,
                result,
                uploadToHubTokenParamName,
                XmlRpcValue::TypeString);
    
    Ptr<Glib::ustring>::Ref     token(new Glib::ustring( 
                                    result[uploadToHubTokenParamName] ));
    
    return token;
}


/*------------------------------------------------------------------------------
 *  Download an audio clip or playlist from the network hub.
 *----------------------------------------------------------------------------*/
Ptr<Glib::ustring>::Ref
WebStorageClient :: downloadFromHub(Ptr<const SessionId>::Ref       sessionId,
                                    Ptr<const UniqueId>::Ref        id)
                                                throw (XmlRpcException)
{
    XmlRpcValue     parameters;
    XmlRpcValue     result;

    parameters.clear();
    parameters[downloadFromHubSessionIdParamName] 
            = sessionId->getId();
    parameters[downloadFromHubUniqueIdParamName] 
            = std::string(*id);
    parameters[downloadFromHubWithContentParamName] 
            = true;
    
    execute(downloadFromHubMethodName, parameters, result);
    
    checkStruct(downloadFromHubMethodName,
                result,
                downloadFromHubTokenParamName,
                XmlRpcValue::TypeString);
    
    Ptr<Glib::ustring>::Ref     token(new Glib::ustring( 
                                    result[downloadFromHubTokenParamName] ));
    
    return token;
}


/*------------------------------------------------------------------------------
 * Create a new Playable object.
 *----------------------------------------------------------------------------*/
Ptr<Playable>::Ref
WebStorageClient :: createPlayable(XmlRpcValue  data)
                                                throw (XmlRpcException)
{
    checkStruct("private:createPlayable",
                data,
                "gunid",
                XmlRpcValue::TypeString);
    Ptr<UniqueId>::Ref              uniqueId(new UniqueId(std::string(
                                                            data["gunid"])));
    
    checkStruct("private:createPlayable",
                data,
                "title",
                XmlRpcValue::TypeString);
    Ptr<const Glib::ustring>::Ref   title(new const Glib::ustring(std::string(
                                                            data["title"] )));
    
    Ptr<const Glib::ustring>::Ref   creator(new const Glib::ustring(std::string(
                                                            data["creator"] )));
    
    Ptr<const Glib::ustring>::Ref   source(new const Glib::ustring(std::string(
                                                            data["source"] )));
    
    checkStruct("private:createPlayable",
                data,
                "length",
                XmlRpcValue::TypeString);
    Ptr<const std::string>::Ref     playlengthString(new const std::string(
                                                            data["length"] ));
    Ptr<time_duration>::Ref         playlength
                                    = TimeConversion::parseTimeDuration(
                                                            playlengthString);
    
    checkStruct("private:createPlayable",
                data,
                "type",
                XmlRpcValue::TypeString);
    std::string         type = data["type"];
    
    Ptr<Playable>::Ref  playable;
    
    if (type == "audioclip") {
        playable.reset(new AudioClip(uniqueId, title, playlength));
        if (*creator != "") {
            playable->setMetadata(creator, "dc:creator");
        }
        if (*source != "") {
            playable->setMetadata(source, "dc:source");
        }
    
    } else if (type == "playlist") {
        playable.reset(new Playlist(uniqueId, title, playlength));
        if (*creator != "") {
            playable->setMetadata(creator, "dc:creator");
        }
        if (*source != "") {
            playable->setMetadata(source, "dc:source");
        }
    
    } else if (type == "webstream") {
        // TODO: handle this case
    
    } else {
        std::stringstream   eMsg;
        eMsg << "Invalid Playable type '" 
             << type
             << "' found in StorageClient::createPlayable():\n"
             << data;
        throw XmlRpcMethodResponseException(eMsg.str());
    }
    
    return playable;
}

