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
        $this->Base->gb->savePref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY, $this->token);
        #$this->active = $this->Base->gb->getPlaylistArray($plid, $this->Base->sessid);
        $this->activeId = $plid;
        $this->Base->_retMsg('Playlist "$1" activated', $this->Base->_getMDataValue($plid, 'title'));
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
        $this->Base->_retMsg('Playlist "$1" released', $this->Base->_getMDataValue($this->Base->gb->_idFromGunid($plgunid), 'title'));
        $this->activeId = NULL;
        $this->token    = NULL;
        $this->Base->gb->delPref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY);
        return TRUE;
    }

    function testForLooked()
    {
        if(is_string($this->token = $this->Base->gb->loadPref($this->Base->sessid, UI_PL_ACCESSTOKEN_KEY))) {
            $this->Base->_retMsg('Playlist looked by You was released');
            $this->release();
            return TRUE;
        }
        return FALSE;
    }

    function addItem($id)
    {
        if (!$this->Base->gb->addAudioClipToPlaylist($this->token, $id, $this->Base->sessid)) {
            $this->Base_retMsg('Cannot add File to Playlist');
            return FALSE;
        }
        return TRUE;

    }

    function removeItem($id)
    {


    }

    function newUsingItem($id)
    {
        # create PL
        # activate
        # add clip
        if ($this->testNew() === FALSE) {
            $this->Base->_retMsg('Already active Playlist');
            return FALSE;
        }
        $this->addItem($id);
        return TRUE;
    }

    function createEmpty()
    {
        if (!$plid = $this->Base->gb->createPlaylist($this->Base->homeid, date('Y-M-D H-i-s'), $this->Base->sessid)) {
            $this->Base->_retMsg('Cannot create Playlist');
            return FALSE;
        }
        $this->Base->_setMDataValue($plid, 'dc:title', 'empty');
        return $plid;
    }

    function testNew()
    {
        # if exists -> return false
        # else
            # create empty
            # activate

        if (is_array($this->activeId)) {
            return FALSE;
        }
        $plid = $this->createEmpty();
        $this->activate($plid);
        return TRUE;
    }
}
