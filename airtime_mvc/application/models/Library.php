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

}
