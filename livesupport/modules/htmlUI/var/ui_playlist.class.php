<?php
class uiPlaylist
{
    function uiPlaylist(&$uiBase)
    {
        $this->Base   =& $uiBase;
        $this->active =& $_SESSION[UI_PLAYLIST_SESSNAME]['active'];
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
    }

    function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    }

    function get()
    {   #print_r($this->items);
        return is_array($this->active) ? $this->active : FALSE;
    }

    function activate($plid)
    {
        # test if PL available
        # look PL
        # store access token to ls_pref
        # load PL into session
        if(is_string($this->Base->gb->loadPref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY))) {
            $this->Base->_retMsg('You have an Playlist already activated,\n first close it');
            return FALSE;
        }
        if($this->Base->gb->playlistIsAvailable($plid, $this->Base->sessid) !== TRUE) {
            $this->Base->_retMsg('Playlist is looked');
            return FALSE;
        }
        $token = $this->Base->gb->lockPlaylistForEdit($plid, $this->Base->sessid);
        $this->Base->gb->savePref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY, $token);
        $this->active = $this->Base->gb->getPlaylistArray($plid, $this->Base->sessid);
        $this->active['id'] = $plid;
        $this->Base->_retMsg('Playlist "$1" activated', $this->Base->_getMDataValue($plid, 'title'));
    }

    function release()
    {
        # get token from ls_pref
        # release PL
        # delete PL from session
        # remove token from ls_pref
        if(!is_string($token = $this->Base->gb->loadPref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY))) {
            $this->Base->_retMsg('No Playlist is looked by You');
            return FALSE;
        }
        $plgunid = $this->Base->gb->releaseLockedPlaylist($token, $this->Base->sessid);
        $this->Base->_retMsg('Playlist "$1" released', $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($plgunid), 'title'));
        $this->active = NULL;
        $this->Base->gb->delPref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY);
        return TRUE;
    }

    function addItem($id)
    {


    }

    function removeItem($id)
    {


    }

    function newUsingItem($id)
    {
        # create PL
        # activate
        # add clip
        if (!$plid = $this->Base->gb->createPlaylist($this->Base->homeid, date('Y-M-D H-i-s'), $this->Base->sessid)) {
            $this->Base->_retMsg('Cannot create Playlist');
            return FALSE;
        }
        $this->activate($plid);
        if (!$this->Base->gb->addAudioClipToPlaylist($token, $id, $this->Base->sessid)) {
            $this->Base_retMsg('Cannot add File to Playlist');
            return FALSE;
        }
        return TRUE;
    }
}
