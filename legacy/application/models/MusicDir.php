<?php

class NestedDirectoryException extends Exception { }

class Application_Model_MusicDir
{
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

    public function setExistsFlag($flag)
    {
        $this->_dir->setExists($flag);
        $this->_dir->save();
    }

    public function setWatchedFlag($flag)
    {
        $this->_dir->setWatched($flag);
        $this->_dir->save();
    }

    public function getWatchedFlag()
    {
        return $this->_dir->getWatched();
    }

    public function getExistsFlag()
    {
        return $this->_dir->getExists();
    }

    /**
     * There are 2 cases where this function can be called.
     * 1. When watched dir was removed
     * 2. When some dir was watched, but it was unmounted
     *
     *  In case of 1, $userAddedWatchedDir should be true
     *  In case of 2, $userAddedWatchedDir should be false
     *
     *  When $userAddedWatchedDir is true, it will set "Watched" flag to false
     *  otherwise, it will set "Exists" flag to true
     */
    public function remove($userAddedWatchedDir=true)
    {

        $music_dir_id = $this->getId();

        $sql = <<<SQL
SELECT DISTINCT s.instance_id
FROM cc_music_dirs            AS md
LEFT JOIN cc_files            AS f ON f.directory = md.id
RIGHT JOIN cc_schedule        AS s ON s.file_id = f.id
WHERE md.id = :musicDirId;
SQL;
        $show_instances = Application_Common_Database::prepareAndExecute($sql,
            array( ':musicDirId' => $music_dir_id ), 'all' );

        // get all the files on this dir
        $sql = <<<SQL
UPDATE cc_files
SET file_exists = 'f'
WHERE id IN
    (SELECT f.id
     FROM cc_music_dirs AS md
     LEFT JOIN cc_files AS f ON f.directory = md.id
     WHERE md.id = :musicDirId);
SQL;

        $affected = Application_Common_Database::prepareAndExecute($sql,
            array( ':musicDirId' => $music_dir_id ), 'all');

        // set RemovedFlag to true
        if ($userAddedWatchedDir) {
            self::setWatchedFlag(false);
        } else {
            self::setExistsFlag(false);
        }
        //$res = $this->_dir->delete();

        foreach ($show_instances as $show_instance_row) {
            $temp_show = new Application_Model_ShowInstance($show_instance_row["instance_id"]);
            $temp_show->updateScheduledTime();
        }
        Application_Model_RabbitMq::PushSchedule();
    }

    /**
     * Checks if p_dir1 is the ancestor of p_dir2. Returns
     * true if it is the ancestor, false otherwise. Note that
     * /home/user is considered the ancestor of /home/user
     *
     * @param string $p_dir1
     *      The potential ancestor directory.
     * @param string $p_dir2
     *      The potential descendent directory.
     * @return boolean
     *      Returns true if it is the ancestor, false otherwise.
     */
    private static function isAncestorDir($p_dir1, $p_dir2)
    {
        if (strlen($p_dir1) > strlen($p_dir2)) {
            return false;
        }

        return substr($p_dir2, 0, strlen($p_dir1)) == $p_dir1;
    }

    /**
     * Checks whether the path provided is a valid path. A valid path
     * is defined as not being nested within an existing watched directory,
     * or vice-versa. Throws a NestedDirectoryException if invalid.
     *
     * @param string $p_path
     *      The path we want to validate
     * @return void
     */
    public static function isPathValid($p_path)
    {
        $dirs = self::getWatchedDirs();
        $dirs[] = self::getStorDir();

        foreach ($dirs as $dirObj) {
            $dir = $dirObj->getDirectory();
            $diff = strlen($dir) - strlen($p_path);
            if ($diff == 0) {
                if ($dir == $p_path) {
                    throw new NestedDirectoryException(sprintf(_("%s is already watched."), $p_path));
                }
            } elseif ($diff > 0) {
                if (self::isAncestorDir($p_path, $dir)) {
                    throw new NestedDirectoryException(sprintf(_("%s contains nested watched directory: %s"), $p_path, $dir));
                }
            } else { /* diff < 0*/
                if (self::isAncestorDir($dir, $p_path)) {
                    throw new NestedDirectoryException(sprintf(_("%s is nested within existing watched directory: %s"), $p_path, $dir));
                }
            }
        }
    }

    /** There are 2 cases where this function can be called.
     * 1. When watched dir was added
     * 2. When some dir was watched, but it was unmounted somehow, but gets mounted again
     *
     *  In case of 1, $userAddedWatchedDir should be true
     *  In case of 2, $userAddedWatchedDir should be false
     *
     *  When $userAddedWatchedDir is true, it will set "Removed" flag to false
     *  otherwise, it will set "Exists" flag to true
     *
     *  @param $nestedWatch - if true, bypass path check, and Watched to false
    **/
    public static function addDir($p_path, $p_type, $userAddedWatchedDir=true, $nestedWatch=false)
    {
        if (!is_dir($p_path)) {
            return array("code"=>2, "error"=>sprintf(_("%s is not a valid directory."), $p_path));
        }
        $real_path = Application_Common_OsPath::normpath($p_path)."/";
        if ($real_path != "/") {
            $p_path = $real_path;
        }

        $exist_dir = self::getDirByPath($p_path);

        if (is_null($exist_dir)) {
            $temp_dir = new CcMusicDirs();
            $dir = new Application_Model_MusicDir($temp_dir);
        } else {
            $dir = $exist_dir;
        }

        $dir->setType($p_type);
        $p_path = Application_Common_OsPath::normpath($p_path)."/";

        try {
            /* isPathValid() checks if path is a substring or a superstring of an
             * existing dir and if not, throws NestedDirectoryException */
            if (!$nestedWatch) {
                self::isPathValid($p_path);
            }
            if ($userAddedWatchedDir) {
                $dir->setWatchedFlag(true);
            } else {
                if ($nestedWatch) {
                    $dir->setWatchedFlag(false);
                }
                $dir->setExistsFlag(true);
            }
            $dir->setDirectory($p_path);

            return array("code"=>0);
        } catch (NestedDirectoryException $nde) {
            $msg = $nde->getMessage();

            return array("code"=>1, "error"=>"$msg");
        } catch (Exception $e) {
            return array("code"=>1,
                "error" => sprintf(
                    _("%s is already set as the current storage dir or in the".
                        " watched folders list"), 
                    $p_path
                )
            );
        }

    }

    /** There are 2 cases where this function can be called.
     * 1. When watched dir was added
     * 2. When some dir was watched, but it was unmounted somehow, but gets mounted again
     *
     *  In case of 1, $userAddedWatchedDir should be true
     *  In case of 2, $userAddedWatchedDir should be false
     *
     *  When $userAddedWatchedDir is true, it will set "Watched" flag to true
     *  otherwise, it will set "Exists" flag to true
    **/
    public static function addWatchedDir($p_path, $userAddedWatchedDir=true, $nestedWatch=false)
    {
        $res = self::addDir($p_path, "watched", $userAddedWatchedDir, $nestedWatch);

        if ($res['code'] != 0) { return $res; }

        //convert "linked" files (Airtime <= 1.8.2) to watched files.
        $propel_link_dir = CcMusicDirsQuery::create()
           ->filterByType('link')
           ->findOne();

        //see if any linked files exist.
        if (isset($propel_link_dir)) {

            //newly added watched directory object
            $propel_new_watch = CcMusicDirsQuery::create()
               ->filterByDirectory(Application_Common_OsPath::normpath($p_path)."/")
               ->findOne();

            //any files of the deprecated "link" type.
            $link_files = CcFilesQuery::create()
               ->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
               ->filterByDbDirectory($propel_link_dir->getId())
               ->find();

            $newly_watched_dir = $propel_new_watch->getDirectory();

            foreach ($link_files as $link_file) {
                $link_filepath = $link_file->getDbFilepath();

                //convert "link" file into a watched file.
                if ((strlen($newly_watched_dir) < strlen($link_filepath)) && (substr($link_filepath, 0, strlen($newly_watched_dir)) === $newly_watched_dir)) {

                    //get the filepath path not including the watched directory.
                    $sub_link_filepath = substr($link_filepath, strlen($newly_watched_dir));

                    $link_file->setDbDirectory($propel_new_watch->getId());
                    $link_file->setDbFilepath($sub_link_filepath);
                    $link_file->save();
                }
            }
        }

        $data = array();
        $data["directory"] = $p_path;
        Application_Model_RabbitMq::SendMessageToMediaMonitor("new_watch", $data);

        return $res;
    }

    public static function getDirByPK($pk)
    {
        $dir = CcMusicDirsQuery::create()->findPK($pk);
        if (!$dir) {
            return null;
        }
        $mus_dir = new Application_Model_MusicDir($dir);

        return $mus_dir;
    }

    public static function getDirByPath($p_path)
    {
        $dir = CcMusicDirsQuery::create()
                    ->filterByDirectory($p_path)
                    ->findOne();
        if ($dir == NULL) {
            return null;
        } else {
            $mus_dir = new Application_Model_MusicDir($dir);

            return $mus_dir;
        }
    }

    /**
     * Search and returns watched dirs
     *
     * @param $exists search condition with exists flag
     * @param $watched search condition with watched flag
     */
    public static function getWatchedDirs($exists=true, $watched=true)
    {
        $result = array();

        $dirs = CcMusicDirsQuery::create()
                    ->filterByType("watched");
        if ($exists !== null) {
            $dirs = $dirs->filterByExists($exists);
        }
        if ($watched !== null) {
            $dirs = $dirs->filterByWatched($watched);
        }
         $dirs = $dirs->find();

        foreach ($dirs as $dir) {
            $result[] = new Application_Model_MusicDir($dir);
        }

        return $result;
    }

    public static function getStorDir()
    {
        $dir = CcMusicDirsQuery::create()
                    ->filterByType("stor")
                    ->findOne();

        $mus_dir = new Application_Model_MusicDir($dir);

        return $mus_dir;
    }

    public static function setStorDir($p_dir)
    {
        // we want to be consistent when storing dir path.
        // path should always ends with trailing '/'
        $p_dir = Application_Common_OsPath::normpath($p_dir)."/";
        if (!is_dir($p_dir)) {
            return array("code"=>2, "error"=>sprintf(_("%s is not a valid directory."), $p_dir));
        } elseif (Application_Model_Preference::GetImportTimestamp()+10 > time()) {
            return array("code"=>3, "error"=>"Airtime is currently importing files. Please wait until this is complete before changing the storage directory.");
        }
        $dir = self::getStorDir();
        // if $p_dir doesn't exist in DB
        $exist = $dir->getDirByPath($p_dir);
        if ($exist == NULL) {
            $dir->setDirectory($p_dir);
            $dirId = $dir->getId();
            $data = array();
            $data["directory"] = $p_dir;
            $data["dir_id"] = $dirId;
            Application_Model_RabbitMq::SendMessageToMediaMonitor("change_stor", $data);

            return array("code"=>0);
        } else {
            return array("code"=>1,
                "error"=>sprintf(_("%s is already set as the current storage dir or in the watched folders list."), $p_dir));
        }
    }

    public static function getWatchedDirFromFilepath($p_filepath)
    {
        $dirs = CcMusicDirsQuery::create()
                    ->filterByType(array("watched", "stor"))
                    ->filterByExists(true)
                    ->filterByWatched(true)
                    ->find();

        foreach ($dirs as $dir) {
            $directory = $dir->getDirectory();
            if (substr($p_filepath, 0, strlen($directory)) === $directory) {
                $mus_dir = new Application_Model_MusicDir($dir);

                return $mus_dir;
            }
        }

        return null;
    }

    /** There are 2 cases where this function can be called.
     * 1. When watched dir was removed
     * 2. When some dir was watched, but it was unmounted
     *
     *  In case of 1, $userAddedWatchedDir should be true
     *  In case of 2, $userAddedWatchedDir should be false
     *
     *  When $userAddedWatchedDir is true, it will set "Watched" flag to false
     *  otherwise, it will set "Exists" flag to true
    **/
    public static function removeWatchedDir($p_dir, $userAddedWatchedDir=true)
    {
        //make sure that $p_dir has a trailing "/"
        $real_path = Application_Common_OsPath::normpath($p_dir)."/";
        if ($real_path != "/") {
            $p_dir = $real_path;
        }
        $dir = Application_Model_MusicDir::getDirByPath($p_dir);
        if (is_null($dir)) {
            return array("code"=>1, "error"=>sprintf(_("%s doesn't exist in the watched list."), $p_dir));
        } else {
            $dir->remove($userAddedWatchedDir);
            $data = array();
            $data["directory"] = $p_dir;
            Application_Model_RabbitMq::SendMessageToMediaMonitor("remove_watch", $data);

            return array("code"=>0);
        }
    }

    public static function splitFilePath($p_filepath)
    {
        $mus_dir = self::getWatchedDirFromFilepath($p_filepath);

        if (is_null($mus_dir)) {
            return null;
        }

        $length_dir = strlen($mus_dir->getDirectory());
        $fp = substr($p_filepath, $length_dir);

        return array($mus_dir->getDirectory(), trim($fp));
    }


    public function unhideFiles() 
    {
        $files = $this->_dir->getCcFiless();
        $hid = 0;
        foreach ($files as $file) {
            $hid++;
            $file->setDbHidden(false);
            $file->save();
        }
        Logging::info("unhide '$hid' files");
    }
}
