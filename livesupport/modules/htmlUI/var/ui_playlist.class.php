<?php
class uiPlaylist
{
    function uiPlaylist(&$uiBase)
    {
        $this->Base  =& $uiBase;
        $this->items =& $_SESSION[UI_PLAYLIST_SESSNAME]['content'];
        $this->reloadUrl = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';
    }

    function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    }

    function get()
    {   #print_r($this->items);
        return $this->items;
    }

    function activate($id)
    {
        $this->items = $this->Base->gb->getPlaylistArray($id, $this->Base->sessid);
        $this->Base->_retMsg('Playlist $1 activated', $this->Base->_getMDataValue($id, 'title'));
    }

    function addItem($id)
    {


    }

    function removeItem($id)
    {


    }
}
