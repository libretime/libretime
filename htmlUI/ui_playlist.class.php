<?php
require_once(dirname(__FILE__)."/../backend/Playlist.php");

/**
 * @package Campcaster
 * @subpackage htmlUI
 * @copyright 2010 Sourcefabric O.P.S.
 */
class uiPlaylist
{
	public $activeId;
	public $title;
	public $duration;
	
	private $Base;
	private $reloadUrl;
	private $redirectUrl;
	private $returnUrl;
	private $flat;

    public function __construct($uiBase)
    {
        $this->Base = $uiBase;
        $this->activeId =& $_SESSION[UI_PLAYLIST_SESSNAME]['activeId'];
        $this->title = $this->Base->gb->getPLMetadataValue($this->activeId, UI_MDATA_KEY_TITLE);
        $this->duration = $this->Base->gb->getPLMetadataValue($this->activeId, UI_MDATA_KEY_DURATION);
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
        $this->redirectUrl = UI_BROWSER.'?popup[]=_2PL.simpleManagement&popup[]=_close';
        $this->returnUrl = UI_BROWSER.'?act=PL.simpleManagement';
    } // constructor


    public function setReload($url=NULL)
    {
        if($url)
           $this->Base->redirUrl = $url; 
        else
            $this->Base->redirUrl = $this->reloadUrl;
            
    } // fn setReload


    public function setRedirect($target=FALSE)
    {
        if ($target !== FALSE) {
            $this->Base->redirUrl = UI_BROWSER."?popup[]=$target&popup[]=_close";
        } else {
            $this->Base->redirUrl = $this->redirectUrl;
        }
    } // fn setRedirect


    public function setReturn()
    {
        $this->Base->redirUrl = $this->returnUrl;
    } // fn setReturn


    private function getPLArray($id)
    {
        $res =  $this->Base->gb->getPlaylistArray($id);
        $_SESSION['pl'] = $res;
        return $res;
    } // fn getPLArray


    public function getActiveArr()
    { 
        if (!$this->activeId) {
            return FALSE;
        }
        return $this->getPLArray($this->activeId);
    } // fn getActiveArr


    public function getActiveId()
    {
        if (!$this->activeId) {
            return FALSE;
        }
        return $this->activeId;
    } // fn getActiveId


    public function activate($plid, $msg=TRUE)
    {
        // test if PL available
        // look PL
        // store access token to ls_pref abd session
        // load PL into session
        if ($this->activeId) {
            $this->release();
        }
        
        $userid = $this->Base->gb->playlistIsAvailable($plid, $this->Base->sessid);
        if ($userid !== TRUE) {
             if (UI_WARNING) {
             	$this->Base->_retMsg('Playlist has been locked by "$1".', Subjects::GetSubjName($userid));
             }
            return FALSE;
        }
        $res = $this->Base->gb->lockPlaylistForEdit($plid, $this->Base->sessid);
        if (PEAR::isError($res) || $res === FALSE) {
            if (UI_VERBOSE === TRUE) {
            	print_r($res);
            }
            $this->Base->_retMsg('Unable to open playlist "$1".', $this->Base->getMetadataValue($plid, UI_MDATA_KEY_TITLE));
            return FALSE;
        }
        
        $this->Base->gb->savePref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY, $plid);
        $this->activeId = $plid;
        
        if ($msg && UI_VERBOSE) {
        	$this->Base->_retMsg('Playlist "$1" opened.', $this->Base->getMetadataValue($plid, UI_MDATA_KEY_TITLE));
        }

        return TRUE;
    }


    public function release($msg=TRUE)
    {  
        // release PL
        // delete PL from session
        if (!$this->activeId) {
            if (UI_WARNING) {
            	$this->Base->_retMsg('There is no playlist available to unlock.');
            }
            return FALSE;
        }
        $res = $this->Base->gb->releaseLockedPlaylist($this->activeId, $this->Base->sessid);
        if (PEAR::isError($res) || $res === FALSE) {
            if (UI_VERBOSE === TRUE) {
            	print_r($res);
            }
            if (UI_WARNING) {
            	$this->Base->_retMsg('Unable to release playlist.');
            }
            return FALSE;
        }
        if ($msg && UI_VERBOSE) {
        	$this->Base->_retMsg('Playlist "$1" released.', $this->Base->getMetadataValue(BasicStor::IdFromGunid($plgunid), UI_MDATA_KEY_TITLE));
        }
        $this->activeId = NULL;
        $this->Base->gb->delPref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY);
        
        return TRUE;
    } // fn release


    public function reportLookedPL($setMsg=FALSE)
    {
        if (is_string($this->Base->gb->loadPref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY))) {
            if ($setMsg == TRUE) {
            	$this->Base->_retMsg('Found locked playlist.');
            }
            return TRUE;
        }
        return FALSE;
    } // fn reportLookedPL


    public function loadLookedFromPref()
    {
        if (is_string($plid = $this->Base->gb->loadPref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY))) {
           
            if (!$this->Base->gb->existsPlaylist($plid)) {
                $this->Base->gb->delPref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY);
                $this->Base->_retMsg('Playlist not found in database.');
                $this->Base->redirUrl = UI_BROWSER.'?popup[]=_2PL.simpleManagement&popup[]=_close';
                return FALSE;
            }
            
            $this->activeId = $plid;
         
            $this->Base->redirUrl = UI_BROWSER.'?popup[]=_2PL.simpleManagement&popup[]=_close';
            return TRUE;
        }
        return FALSE;
    } // fn loadLookedFromPref


    /**
     * Add item to playlist
     *
     * @param int $elemIds
     * @param array $duration
     * @return unknown
     */
    public function addItem($elemIds, $pos=NULL, $duration=NULL)
    {
        $fadeIn = NULL;
        $fadeOut = NULL;
        $cliplength = NULL;
        $cueIn = NULL;
        $cueIn = NULL;
       
        /*
        gstreamer bug:
        Warning: The clipEnd can't be bigger than ninety nine percent (99%) of the clipLength,
        this means also if no clipEnd is defined it should be 00:00:00.000000 and not the clipLength.
        $clipend = '00:00:00.000000';
        */

        if (!$elemIds) {
            if (UI_WARNING) {
            	$this->Base->_retMsg('No item(s) selected.');
            }
            return FALSE;
        }       
        if (!is_array($elemIds)) {
            $elemIds = array($elemIds);
        }  
        if (isset($duration)) {
            $length = sprintf('%02d', $duration['H']).':'.sprintf('%02d', $duration['i']).':'.sprintf('%02d', $duration['s']).'.000000';
        }
        
        foreach ($elemIds as $elemId) {
            $r = $this->Base->gb->addAudioClipToPlaylist($this->activeId, $elemId, $pos, $fadeIn, $fadeOut, $cliplength, $cueIn, $cueOut);
            if (PEAR::isError($r)) {
                if (UI_VERBOSE === TRUE) {
                	print_r($r);
                }
                $this->Base->_retMsg('Error while trying to add item to playlist.');
                return FALSE;
            }
        }
        
        $this->Base->SCRATCHPAD->reloadActivePLMetadata($this->activeId);
        
        return TRUE;
    } // fn addItem


    public function removeItem($positions)
    {
        if (!$positions) {
            if (UI_WARNING) {
            	$this->Base->_retMsg('No item(s) selected.');
            }
            return FALSE;
        }
        if (!is_array($positions))
            $positions = array($positions);
        
        //so the automatic updating of playlist positioning doesn't affect removal.
        sort($positions);
        $positions = array_reverse($positions);
      
        foreach ($positions as $pos) {
            if ($this->Base->gb->delAudioClipFromPlaylist($this->activeId, $pos) !== TRUE) {
                $this->Base->_retMsg('Cannot remove item from playlist.');
                return FALSE;
            }
        }
        
        $this->Base->SCRATCHPAD->reloadActivePLMetadata($this->activeId);
        
        return TRUE;
    } // fn removeItem


    /**
     * Create a playlist.
     *
     * @param array $ids
     * 		Optional list of media files to be added to the playlist
     * 		after it is created.
     * @return FALSE|int
     */
    public function create($ids = null)
    {
        // create PL
        // activate
        // add clip if $id is set
       
        if ($this->activeId) {
            $this->release();
        }
        
        $datetime = strftime('%Y-%m-%d %H:%M:%S');
        $plid = $this->Base->gb->createPlaylist($datetime, $this->Base->sessid);
      
        if (!$plid) {
            $this->Base->_retMsg('Cannot create playlist.');
            return FALSE;
        }

        $this->Base->gb->setPLMetadataValue($plid, UI_MDATA_KEY_CREATOR, $this->Base->login);
        $this->Base->gb->setPLMetadataValue($plid, UI_MDATA_KEY_DESCRIPTION, tra('created at $1', $datetime));

        
        if ($this->activate($plid)===FALSE) {
            $this->Base->_retMsg('Cannot activate playlist.');
            return FALSE;
        }
        if ($ids) {
            if ($this->addItem($ids)!==TRUE) {
                return FALSE;
            }
        }
        
        return $plid;
    } // fn create

    public function moveItem($oldPos, $newPos)
    {
        $response = array();
        
        $r = $this->Base->gb->moveAudioClipInPlaylist($this->activeId, $oldPos, $newPos);
        if (PEAR::isError($r) || $r === FALSE) {
            $response["error"] = "Failed to Move file.";
            $response["oldPos"] = $oldPos;
            $response["newPos"] = $newPos;
        }
        else{
          $response["error"] = FALSE;  
        }
        
        die(json_encode($response));
    } // fn moveItem


    public function setClipLength($pos, $cueIn, $cueOut)
    {
        $response = array();
       
        $res = $this->Base->gb->changeClipLength($this->activeId, $pos, $cueIn, $cueOut);

        $response = $res;
                
        die(json_encode($response));
    }
    
    public function setFadeLength($pos, $fadeIn, $fadeOut)
    {
        $response = array();
        
        $res = $this->Base->gb->changeFadeInfo($this->activeId, $pos, $fadeIn, $fadeOut);
        
        $response = $res;
                
        die(json_encode($response));
    } // fn setFade


    public function metaDataForm($langid)
    {
        include(dirname(__FILE__).'/formmask/metadata.inc.php');
        $id = $this->activeId;
        $langid = $langid ? $langid : UI_DEFAULT_LANGID;

        foreach ($mask['playlist'] as $k=>$v) {
            $mask['playlist'][$k]['element'] = uiBase::formElementEncode($v['element']);
            
            $getval = $this->Base->gb->getPLMetadataValue($id, $v['element'], $langid);
            if ($getval) {
                $mask['playlist'][$k]['default']                = $getval;
                //$mask['playlist'][$k]['attributes']['onFocus']  = 'MData_confirmChange(this)';
            };
        }
        $form = new HTML_QuickForm('editMetaData', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        uiBase::parseArrayToForm($form, $mask['basics']);
        uiBase::parseArrayToForm($form, $mask['playlist']);
        uiBase::parseArrayToForm($form, $mask['buttons']);
        $form->setConstants(array('act'  => 'PL.editMetaData',
                                  'id'   => $id,
                                  'curr_langid' => $langid
                            )
        );
        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output['main'] = $renderer->toArray();

        $form = new HTML_QuickForm('langswitch', UI_STANDARD_FORM_METHOD, UI_BROWSER);
        uiBase::parseArrayToForm($form, $mask['langswitch']);
        $form->setConstants(array('target_langid'   => $langid));
        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output['langswitch'] = $renderer->toArray();

        #print_r($output);
        return $output;
    } // fn metadataForm


    public function editMetaData($formdata)
    {
        include(dirname(__FILE__).'/formmask/metadata.inc.php');

        $id             = $this->activeId;
        $curr_langid    = $formdata['curr_langid'];

        ## if language switched stay on metadataform ##
        if ($curr_langid === $formdata['target_langid']) {
            $this->Base->redirUrl = UI_BROWSER."?act=PL.simpleManagement";
        } else {
            $this->Base->redirUrl = UI_BROWSER."?act=PL.editMetaData&id=$id&curr_langid=".$formdata['target_langid'];
        }
     
        foreach ($mask['playlist'] as $k=>$v) {
            
            $formdata[uiBase::formElementEncode($v['element'])] ? $mData[uiBase::formElementDecode($v['element'])] = $formdata[uiBase::formElementEncode($v['element'])] : NULL;
        }

        if (!count($mData)) {
        	return;
        }

        foreach ($mData as $key => $val) {
            $r = $this->Base->gb->setPLMetadataValue($id, $key, $val, $curr_langid);
            if (PEAR::isError($r)) {
                if (UI_VERBOSE === TRUE) {
                	print_r($r);
                }
                $this->Base->_retMsg('Unable to set "$1" to value "$2".', $key, $val);
            }
        }
        if (UI_VERBOSE) {
        	$this->Base->_retMsg('Metadata saved.');
        } 

        $this->Base->SCRATCHPAD->reloadMetadata();
    } // fn editMetadata


    public function deleteActive()
    {
        $id = $this->activeId;
        $this->release(FALSE);
       
        $res = $this->Base->gb->deletePlaylist($id);
        if ($res === TRUE) {
            return $id;
        }

        $this->Base->_retMsg('Cannot delete this playlist.');
        return FALSE;
    } // fn deleteActive
    
    public function delete($id)
    {
        $res = $this->Base->gb->deletePlaylist($id);
        if ($res === TRUE) {
            return $id;
        }

        $this->Base->_retMsg('Cannot delete this playlist.');
        return FALSE;
    }

    public function isAvailable($id)
    {
        if ($this->Base->gb->playlistIsAvailable($id, $this->Base->sessid) === TRUE) {
            return TRUE;
        }
        return FALSE;
    } // fn isAvailable


    function isUsedBy($id)
    {
        if (($userid = $this->Base->gb->playlistIsAvailable($id, $this->Base->sessid)) !== TRUE) {
             return Subjects::GetSubjName($userid);
        }
        return FALSE;
    } // fn isUsedBy


    public function exportForm($id,$mask)
    {
        $mask['act']['constant'] = 'PL.export';
        $mask['id']['constant'] = $id;
        $form = new HTML_QuickForm('PL_exportForm', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        uiBase::parseArrayToForm($form, $mask);
        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        return $renderer->toArray();
    } // fn exportForm


    public function importForm($id, $mask)
    {
        $form = new HTML_QuickForm('PL_importForm', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        //print_r($mask);
        uiBase::parseArrayToForm($form, $mask);
        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        return $renderer->toArray();
    } // fn importForm

} // class uiPlaylist
?>
