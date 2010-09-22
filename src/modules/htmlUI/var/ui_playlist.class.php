<?php
require_once("../../../storageServer/var/Playlist.php");

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


    public function setReload()
    {
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
            if (UI_WARNING) {
            	$this->Base->_retMsg('You already have an open playlist. Close it first.');
            }
            return FALSE;
        }
        $userid = $this->Base->gb->playlistIsAvailable($plid, $this->Base->sessid);
        if ($userid !== TRUE) {
             if (UI_WARNING) {
             	$this->Base->_retMsg('Playlist has been locked by "$1".', Subjects::GetSubjName($userid));
             }
            return FALSE;
        }
        $res = $this->Base->gb->lockPlaylistForEdit($plid, $this->Base->sessid);
        if (PEAR::isError($res)) {
            if (UI_VERBOSE === TRUE) {
            	print_r($res);
            }
            $this->Base->_retMsg('Unable to open playlist "$1".', $this->Base->getMetadataValue($plid, UI_MDATA_KEY_TITLE));
            return FALSE;
        }
        
        $this->Base->gb->savePref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY, $plid);
        $this->activeId = $plid;
        $_SESSION['pl'] = "Activating playlist with id: " . $this->activeId;
        
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
        if (PEAR::isError($res)) {
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
            $this->Base->_retMsg('A playlist is already opened.');
            return FALSE;
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


    /**
     * WARNING: THIS FUNCTION IS REALLY SSSLLLLOOOOWWWW!
     *
     * @param unknown_type $id
     * @return array
     */
    public function getFlat($id)
    {
    	$this->flat = array();
        $this->_plwalk($this->getPLArray($id));

        if (count($this->flat) > 0) {
            reset($this->flat);
            $this->flat[key($this->flat)]['firstInList'] = true;
            end($this->flat);
            $this->flat[key($this->flat)]['lastInList'] = true;
            reset($this->flat);
            return $this->flat;
        } else {
            return array();
        }
    } // fn getFlat


    private function _plwalk($arr, $parent=0, $attrs=0)
    {
    	// Note: the array $this->flat needs to be initialized before
    	// this function is called.
        foreach ($arr['children'] as $node => $sub) {
            if ($sub['elementname']===UI_PL_ELEM_PLAYLIST) {
                $this->_plwalk($sub, $node, $sub['attrs']);
            }
            if ($sub['elementname']===UI_FILETYPE_AUDIOCLIP || $sub['elementname']===UI_FILETYPE_PLAYLIST) {
                #$this->flat["$parent.$node"] = $sub['attrs'];
                #$this->flat["$parent.$node"]['type'] = $sub['elementname'];
                $this->flat[$parent] = $this->Base->getMetaInfo(BasicStor::IdFromGunid($sub['attrs']['id']));
                $this->flat[$parent]['attrs'] = $attrs;
                $this->flat[$parent]['playlength'] = $sub['attrs']['playlength'];
            }
            if ($sub['elementname']===UI_PL_ELEM_FADEINFO) {
                $this->flat[$parent][UI_PL_ELEM_FADEIN]  = Playlist::playlistTimeToSeconds($sub['attrs'][UI_PL_ELEM_FADEIN]);
                $this->flat[$parent][UI_PL_ELEM_FADEOUT] = Playlist::playlistTimeToSeconds($sub['attrs'][UI_PL_ELEM_FADEOUT]);
                $this->flat[$parent]['fadein_ms']  = $sub['attrs'][UI_PL_ELEM_FADEIN]  ? Playlist::playlistTimeToSeconds($sub['attrs'][UI_PL_ELEM_FADEIN])  * 1000 : 0;
                $this->flat[$parent]['fadeout_ms'] = $sub['attrs'][UI_PL_ELEM_FADEOUT] ? Playlist::playlistTimeToSeconds($sub['attrs'][UI_PL_ELEM_FADEOUT]) * 1000 : 0;
            }
        }
    } // fn _plwalk


    public function changeTransition($id, $type, $duration)
    {
        $pause = $pause;
        $xfade = Playlist::secondsToPlaylistTime($duration/1000);

        if ($id) {
            // just change fade between 2 clips
            $curr = $this->getCurrElement($id);
            $prev = $this->getPrevElement($id);
            $next = $this->getNextElement($id);

            switch ($type) {
                case "fadeX":
                    $item[$prev['attrs']['id']] =
                                  array(UI_PL_ELEM_FADEIN  => Playlist::secondsToPlaylistTime($prev[UI_PL_ELEM_FADEIN]),
                                        UI_PL_ELEM_FADEOUT => $xfade
                                  );
                    $item[$id]  = array(UI_PL_ELEM_FADEIN  => $xfade,
                                        UI_PL_ELEM_FADEOUT => Playlist::secondsToPlaylistTime($curr[UI_PL_ELEM_FADEOUT])
                                  );
                break;
                case "pause":
                    $item[$prev['attrs']['id']] =
                                  array(UI_PL_ELEM_FADEIN  => Playlist::secondsToPlaylistTime($prev[UI_PL_ELEM_FADEIN]),
                                        UI_PL_ELEM_FADEOUT => $pause
                                  );
                    $item[$id]  = array(UI_PL_ELEM_FADEIN  => $pause,
                                        UI_PL_ELEM_FADEOUT => Playlist::secondsToPlaylistTime($curr[UI_PL_ELEM_FADEOUT])
                                  );
                break;
                case "fadeIn":
                    $item[$id]  = array(UI_PL_ELEM_FADEIN  => $xfade,
                                        UI_PL_ELEM_FADEOUT => Playlist::secondsToPlaylistTime($curr[UI_PL_ELEM_FADEOUT])
                                  );
                break;
                case "fadeOut":
                    $item[$id] = array(UI_PL_ELEM_FADEIN  => Playlist::secondsToPlaylistTime($curr[UI_PL_ELEM_FADEIN]),
                                       UI_PL_ELEM_FADEOUT => $xfade
                                 );
                break;
            }
            foreach ($item as $i=>$val) {
                $r = $this->Base->gb->changeFadeInfo($this->token, $i, $val[UI_PL_ELEM_FADEIN], $val[UI_PL_ELEM_FADEOUT], $this->Base->sessid);
                if (PEAR::isError($r)) {
                    if (UI_VERBOSE === TRUE) {
                    	print_r($r);
                    }
                    $this->Base->_retMsg('Changing fade information failed.');
                    return FALSE;
                }
            }
        } else {
            // change fade of all clips
            foreach ($this->getFlat($this->activeId) as $v) {
                $r = $this->Base->gb->changeFadeInfo($this->token, $v['attrs']['id'], $type==='pause'?$pause:$xfade, $type==='pause'?$pause:$xfade, $this->Base->sessid);
                if (PEAR::isError($r)) {
                    if (UI_VERBOSE === TRUE) {
                    	print_r($r);
                    }
                    $this->Base->_retMsg('Changing fade information failed.');
                    return FALSE;
                }
            }
        }
        return TRUE;
    } // fn changeTransition


    public function moveItem($oldPos, $newPos)
    {
        $r = $this->Base->gb->moveAudioClipInPlaylist($this->activeId, $oldPos, $newPos);
        if (PEAR::isError($r) || $r === FALSE) {
            if (UI_VERBOSE === TRUE) {
            	print_r($r);
            }
            $this->Base->_retMsg('Cannot move item.');
            return FALSE;
        }
        return TRUE;
    } // fn moveItem


    public function reorder($items)
    {
        // just to be sure items are in right order
        asort($items);
        $pos = 0;
        foreach ($items as $id => $v) {
            $pos++;
            $r = $this->Base->gb->moveAudioClipInPlaylist($this->token, $id, $pos, $this->Base->sessid);
            if (PEAR::isError($r)) {
                if (UI_VERBOSE === TRUE) {
                	print_r($r);
                }
                $this->Base->_retMsg('Cannot move item.');
                return FALSE;
            }
        }
        return TRUE;
    } // fn reorder


    private function getCurrElement($id)
    {
        $arr = $this->getFlat($this->activeId);
        while ($val = current($arr)) {
            if ($val['attrs']['id'] == $id) {
                return current($arr);
            }
            next($arr);
        }
    } // fn getCurrElement


    private function getPrevElement($id)
    {
        $arr = $this->getFlat($this->activeId);
        while ($val = current($arr)) {
            if ($val['attrs']['id'] == $id) {
                return prev($arr);
            }
            next($arr);
        }
    } // fn getPrevElement


    private function getNextElement($id)
    {
        $arr = $this->getFlat($this->activeId);
        while ($val = current($arr)) {
            if ($val['attrs']['id'] == $id) {
                return next($arr);
            }
            next($arr);
        }
    } // fn getNextElement


    public function changeTransitionForm($id, $type, $mask)
    {
        $form = new HTML_QuickForm('PL_changeTransition', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $s = $this->getCurrElement($id);
        switch ($type) {
            case "fadeIn":
                $d = $this->getCurrElement($id);
                $duration = $d['fadein_ms'];
                $form->setConstants(array('headline' => '<b>'.$s['title'].'</b>'));
            break;
            case "transition":
                $d = $this->getPrevElement($id);
                $duration = $s['fadein_ms'];
                $form->setConstants(array('headline' => '<b>'.$d['title'].'</b> <-> <b>'.$s['title'].'</b>'));
            break;
            case "fadeOut":
                $d = $this->getCurrElement($id);
                $duration = $d['fadeout_ms'];
                $form->setConstants(array('headline' => '<b>'.$s['title'].'</b>'));
            break;
        }
        $form->setConstants(array('id'       => $id,
                                  'duration' => $duration)
        );
        uiBase::parseArrayToForm($form, $mask[$type]);
        uiBase::parseArrayToForm($form, $mask['all']);
        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        return $renderer->toArray();
    } // fn changeTransitionForm


    public function changeAllTransitionsForm($mask)
    {
        $form = new HTML_QuickForm('PL_changeTransition', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        uiBase::parseArrayToForm($form, $mask['transition']);
        uiBase::parseArrayToForm($form, $mask['all']);
        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        return $renderer->toArray();
    } // fn changeAllTransitionsForm


    public function setClipLengthForm($id, $elemId, $mask)
    {
        if (isset($elemId)) {
            $mask['act']['constant'] = 'PL.setClipLength';
            $mask['elemId']['constant'] = $elemId;
            $element = $this->getCurrElement($elemId);
            $playLegthS = Playlist::playlistTimeToSeconds($element['playlength']);
            $clipStartS = Playlist::playlistTimeToSeconds($element['attrs']['clipStart']);
            $clipEndS   = Playlist::playlistTimeToSeconds($element['attrs']['clipEnd']);
            $mask['duration']['constant']  = round($playLegthS);
            $mask['clipLength']['default'] = round($clipEndS - $clipStartS);
            $mask['clipStart']['default']  = round($clipStartS);
            $mask['clipEnd']['default']    = round($clipEndS);
            for ($n=0; $n<=round($playLegthS); $n++) {
                $options[$n] = date('i:s', $n);
            }
            $mask['clipStart']['options']  = $options;
            $mask['clipLength']['options'] = $options;
            $mask['clipEnd']['options']    = array_reverse(array_reverse($options), true);
        } else {
            $mask['act']['constant'] = 'PL.addItem';
            $mask['id']['constant'] = $id;
            $mask['clipLength']['default'] = substr($this->Base->getMetadataValue($id, UI_MDATA_KEY_DURATION), 0, 8);
            $mask['duration']['constant'] = $mask['playlength']['default'];
        }

        $form = new HTML_QuickForm('PL_setClipLengthForm', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        uiBase::parseArrayToForm($form, $mask);
        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        return $renderer->toArray();
    } // fn setClipLengthForm

    function setClipLength($p_elemId, &$p_mask)
    {
        $form = new HTML_QuickForm('PL_setClipLengthForm', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        uiBase::parseArrayToForm($form, $p_mask);

        if (!$form->validate()) {
            return false;
        }
        $values = $form->exportValues();
        $elem = $this->getCurrElement($values['elemId']);
        if (!$elem) {
            return false;
        }

        $clipStart = GreenBox::secondsToPlaylistTime($values['clipStart']);
        $clipEnd = GreenBox::secondsToPlaylistTime($values['clipEnd']);

        $this->Base->gb->changeClipLength($this->token, $p_elemId, $clipStart, $clipEnd, $this->Base->sessid);

    }


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
                $mask['playlist'][$k]['attributes']['onFocus']  = 'MData_confirmChange(this)';
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


    public function isAvailable($id)
    {
        if (Greenbox::getFileType($id) !== UI_FILETYPE_PLAYLIST) {
            return TRUE;
        }
        if ($this->Base->gb->playlistIsAvailable($id, $this->Base->sessid) === TRUE) {
            return TRUE;
        }
        return FALSE;
    } // fn isAvailable


    function isUsedBy($id)
    {
        if (Greenbox::getFileType($id) !== UI_FILETYPE_PLAYLIST) {
            return FALSE;
        }
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
