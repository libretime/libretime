<?php

class MusicDir {

    /**
     * @holds propel database object
     */
    private $_dir;

    public function __construct($dir)
    {
        $this->_dir = $dir;
    }

    public function getId()
    {
        return $this->_dir->getId();
    }

    public function getType()
    {
        return $this->_dir->getType();
    }

    public function setType($type)
    {
        $this->_dir->setType($type);
    }

    public function getDirectory()
    {
        return $this->_dir->getDirectory();
    }

    public function setDirectory($dir)
    {
        $this->_dir->setDirectory($dir);
        $this->_dir->save();
    }

    public function remove()
    {
        $this->_dir->delete();
    }

    public static function addDir($p_path, $p_type)
    {
        if(!is_dir($p_path)){
            return array("code"=>2, "error"=>"'$p_path' is not a valid directory.");
        }
        $dir = new CcMusicDirs();
        $dir->setType($p_type);
        $p_path = realpath($p_path)."/";
        $temp = $dir->setDirectory($p_path);
        try{
            $dir->save();
            return array("code"=>0);
        }
        catch(Exception $e){
            //echo $e->getMessage();
            return array("code"=>1, "error"=>"'$p_path' is already set as the current storage dir or in the watched folders list");
        }
        
    }

    public static function addWatchedDir($p_path)
    {
        $res = self::addDir($p_path, "watched");
        if($res['code'] == 0){
            $data = array();
            $data["directory"] = $p_path;
            RabbitMq::SendMessageToMediaMonitor("new_watch", $data);
        }
        return $res;
    }

    public static function getDirByPK($pk)
    {
        $dir = CcMusicDirsQuery::create()->findPK($pk);

        $mus_dir = new MusicDir($dir);

        return $mus_dir;
    }

    public static function getDirByPath($p_path)
    {
        $dir = CcMusicDirsQuery::create()
                    ->filterByDirectory($p_path)
                    ->findOne();

        if($dir == NULL){
            return null;
        }
        else{
            $mus_dir = new MusicDir($dir);
            return $mus_dir;
        }
    }

    public static function getWatchedDirs()
    {
        $result = array();

        $dirs = CcMusicDirsQuery::create()
                    ->filterByType("watched")
                    ->find();

        foreach($dirs as $dir) {
            $tmp = new MusicDir($dir);
            $result[] = $tmp;
        }

        return $result;
    }

    public static function getStorDir()
    {
        $dir = CcMusicDirsQuery::create()
                    ->filterByType("stor")
                    ->findOne();

        $mus_dir = new MusicDir($dir);

        return $mus_dir;
    }    
    
    public static function setStorDir($p_dir)
    {
        if(!is_dir($p_dir)){
            return array("code"=>2, "error"=>"'$p_dir' is not a valid directory.");
        }
        $dir = self::getStorDir();
        // if $p_dir doesn't exist in DB
        $p_dir = realpath($p_dir)."/";
        $exist = $dir->getDirByPath($p_dir);
        if($exist == NULL){
            $dir->setDirectory($p_dir);
            $dirId = $dir->getId();
            $data = array();
            $data["directory"] = $p_dir;
            $data["dir_id"] = $dirId;
            RabbitMq::SendMessageToMediaMonitor("change_stor", $data);
            return array("code"=>0);
        }else{
            return array("code"=>1, "error"=>"'$p_dir' is already set as the current storage dir or in the watched folders list.");
        }
    }

    public static function getWatchedDirFromFilepath($p_filepath)
    {
        $dirs = CcMusicDirsQuery::create()
                    ->find();

        foreach($dirs as $dir) {
            $directory = $dir->getDirectory();
            if (substr($p_filepath, 0, strlen($directory)) === $directory) {
                $mus_dir = new MusicDir($dir);
                return $mus_dir;
            }
        }

        return null;
    }
    
    public static function removeWatchedDir($p_dir){
        $p_dir = realpath($p_dir)."/";
        $dir = MusicDir::getDirByPath($p_dir);
        if($dir == NULL){
            return array("code"=>1,"error"=>"'$p_dir' doesn't exist in the watched list.");
        }else{
            $dir->remove();
            $data = array();
            $data["directory"] = $p_dir;
            RabbitMq::SendMessageToMediaMonitor("remove_watch", $data);
            return array("code"=>0);
        }
    }

    public static function splitFilePath($p_filepath)
    {
        $mus_dir = self::getWatchedDirFromFilepath($p_filepath);
        
        if(is_null($mus_dir)) {
            return null;
        }

        $length_dir = strlen($mus_dir->getDirectory());
        $fp = substr($p_filepath, $length_dir);

        return array($mus_dir->getDirectory(), $fp);
    }

}
