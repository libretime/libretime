<?php
class uiPlaylist
{
    function uiPlaylist(&$uiBase)
    {
        $this->Base      =& $uiBase;
        $this->activeId  =& $_SESSION[UI_PLAYLIST_SESSNAME]['activeId'];
        $this->token     =& $_SESSION[UI_PLAYLIST_SESSNAME]['token'];
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
    }

    function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    }

    function get()
    {
        if (!$this->activeId) {
            return FALSE;
        }
        print_r( $this->Base->gb->getPlaylistArray($this->activeId, $this->Base->sessid));
        return $this->Base->gb->getPlaylistArray($this->activeId, $this->Base->sessid);
    }

    function activate($plid)
    {
        # test if PL available
        # look PL
        # store access token to ls_pref abd session
        # load PL into session
        if($this->token) {
            $this->Base->_retMsg('You have an Playlist already activated,\n first close it');
            return FALSE;
        }
        if(($userid = $this->Base->gb->playlistIsAvailable($plid, $this->Base->sessid)) !== TRUE) {
            $this->Base->_retMsg('Playlist is looked by $1', $this->Base->gb->getSubjName($userid));
            return FALSE;
        }
        $this->token = $this->Base->gb->lockPlaylistForEdit($plid, $this->Base->sessid);
        $this->Base->gb->savePref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY, $plid.':'.$this->token);
        $this->activeId = $plid;
        $this->Base->_retMsg('Playlist "$1" activated', $this->Base->_getMDataValue($plid, UI_MDATA_KEY_TITLE));
        return TRUE;
    }

    function release()
    {
        # get token from ls_pref
        # release PL
        # delete PL from session
        # remove token from ls_pref
        if(!$this->token) {
            $this->Base->_retMsg('No Playlist is looked by You');
            return FALSE;
        }
        $plgunid = $this->Base->gb->releaseLockedPlaylist($this->token, $this->Base->sessid);
        if (PEAR::isError($plgunid)) {
            $this->Base->_retMsg('Unable to release Playlist');
            return FALSE;
        }
        $this->Base->_retMsg('Playlist "$1" released', $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($plgunid), UI_MDATA_KEY_TITLE));
        $this->activeId = NULL;
        $this->token    = NULL;
        $this->Base->gb->delPref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY);
        return TRUE;
    }


    function revert()
    {
        if(!$this->token) {
            $this->Base->_retMsg('No Playlist is looked by You');
            return FALSE;
        }
        $plgunid = $this->Base->gb->revertEditedPlaylist($this->token, $this->Base->sessid);
        if (PEAR::isError($plgunid)) {
            $this->Base->_retMsg('Unable to revert to looked state');
            return FALSE;
        }
        $this->Base->_retMsg('Playlist "$1" reverted and released', $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($plgunid), UI_MDATA_KEY_TITLE));
        $this->activeId = NULL;
        $this->token    = NULL;
        $this->Base->gb->delPref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY);
        return TRUE;
    }


    function loadLookedFromPref()
    {
        if(is_string($saved = $this->Base->gb->loadPref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY))) {
            $this->Base->_retMsg('Found Playlist looked by you');
            #$this->release();
            list ($this->activeId, $this->token) = explode (':', $saved);

            $this->Base->redirUrl = UI_BROWSER.'?popup[]=_2PL.simpleManagement&popup[]=_close';
            return TRUE;
        }
        return FALSE;
    }

    function addItem($id)
    {
        if ($this->Base->gb->addAudioClipToPlaylist($this->token, $id, $this->Base->sessid) === FALSE) {
            $this->Base_retMsg('Cannot add Item to Playlist');
            return FALSE;
        }
        return TRUE;
    }

    function removeItem($elemIds)
    {
        if (!$elemIds) {
            $this->Base->_retMsg('No Item(s) given');
            return FALSE;
        }
        if (!is_array($elemIds))
            $elemIds = array($elemIds);

        foreach ($elemIds as $elemId) {
            if ($this->Base->gb->delAudioClipFromPlaylist($this->token, $elemId, $this->Base->sessid) !== TRUE) {
                $this->Base->_retMsg('Cannot remove Item from Playlist');
                return FALSE;
            }
        }
        return TRUE;
    }

    function create($id=FALSE)
    {
        # create PL
        # activate
        # add clip if given
        if (is_array($this->activeId)) {
            $this->Base->_retMsg('Already active Playlist');
            return FALSE;
        }
        $datetime = date('Y-m-d H:i:s');
        if (!$plid = $this->Base->gb->createPlaylist($this->Base->homeid, $datetime, $this->Base->sessid)) {
            $this->Base->_retMsg('Cannot create Playlist');
            return FALSE;
        }
        $this->Base->_setMDataValue($plid, 'dc:title', $datetime);
        if ($this->activate($plid)===FALSE) {
            return FALSE;
        }
        if ($id!==FALSE) {
            if ($this->addItem($id)!==TRUE) {
                return FALSE;
            }
        }
        return $plid;
    }


    function getFlat()
    {
        $this->plwalk($this->get());
        #print_r($this->flat);
        return $this->flat;
    }


    function plwalk($arr, $parent=0, $attrs=0)
    {
        foreach ($arr['children'] as $node=>$sub) {
            if ($sub['elementname']=='playlistelement') {
                $this->plwalk($sub, $node, $sub['attrs']);
            }
            if ($sub['elementname']=='audioclip') {
                #$this->flat["$parent.$node"] = $sub['attrs'];
                #$this->flat["$parent.$node"]['type'] = $sub['elementname'];
                $this->flat["$parent.$node"] = $this->Base->_getMetaInfo($this->Base->gb->_idFromGunid($sub['attrs']['id']));
                $this->flat["$parent.$node"]['attrs'] = $attrs;
            }
        }
    }
}
