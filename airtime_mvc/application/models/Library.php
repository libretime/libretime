<?php

class Application_Model_Library
{

    public static function getObjInfo($p_type)
    {
        $info = array();

        if (strcmp($p_type, 'playlist')==0) {
            $info['className'] = 'Application_Model_Playlist';
        } elseif (strcmp($p_type, 'block')==0) {
            $info['className'] = 'Application_Model_Block';
        } elseif (strcmp($p_type, 'webstream')==0) {
            $info['className'] = 'Application_Model_Webstream';
        } else {
            throw new Exception("Unknown object type: '$p_type'");
        }

        return $info;
    }

    public static function changePlaylist($p_id, $p_type)
    {
        $obj_sess = new Zend_Session_Namespace(UI_PLAYLISTCONTROLLER_OBJ_SESSNAME);

        if (is_null($p_id) || is_null($p_type)) {
            unset($obj_sess->id);
            unset($obj_sess->type);
        } else {
            $obj_sess->id = intval($p_id);
            $obj_sess->type = $p_type;
        }
    }

    public static function getPlaylistNames($alphasort = false)
    {

        $playlistNames = array(NULL  => _("None"));
        //if we want to return the playlists sorted alphabetically by name
        if ($alphasort) {
            $playlists = CcPlaylistQuery::create()
                ->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
                ->orderByname()
                ->find();

        }
        else {
            $playlists = CcPlaylistQuery::create()
                ->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
                ->find();
        }
        foreach ($playlists as $playlist) {
            $playlistNames[$playlist->getDbId()] = $playlist->getDbName();
        }

        return $playlistNames;
    }

    public static function getTracktypes()
    {
        $track_type_options = array(NULL  => _("None"));
        $track_types = Application_Model_Tracktype::getTracktypes();
        
        array_multisort(array_map(function($element) {
            return $element['type_name'];
        }, $track_types), SORT_ASC, $track_types);
        
        foreach ($track_types as $key => $tt) {
            $track_type_options[$tt['code']] = $tt['type_name'];
        }

        return $track_type_options;
    }
}
