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
        $dir = new CcMusicDirs();
        $dir->setType($p_type);
        $dir->setDirectory($p_path);
        $dir->save();

        $mus_dir = new MusicDir($dir);

        return $mus_dir;
    }

    public static function addWatchedDir($p_path)
    {
        return self::addDir($p_path, "watched");
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

        $mus_dir = new MusicDir($dir);
        return $mus_dir;
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
        $mus_dir->_dir = $dir;

        return $mus_dir;
    }

    public static function setStorDir($p_dir)
    {
        $dir = self::getStorDir();
        $dir->setDirectory($p_dir);
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

    public static function splitFilePath($p_filepath)
    {
        $mus_dir = self::getWatchedDirFromFilepath($p_filepath);
        if(is_null($mus_dir)) {
            return null;
        }

        $length_dir = strlen($mus_dir->getDirectory());
        $length_filepath = strlen($p_filepath);
        $length = $length_filepath - $length_dir;

        $fp = substr($p_filepath, $length_dir+1, $length);

        return array($mus_dir->getDirectory(), $fp);
    }

}
