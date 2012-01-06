<?php

class NestedDirectoryException extends Exception { }

class Application_Model_MusicDir {

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

    public function setRemoved($flag){
        $this->_dir->setRemoved($flag);
        $this->_dir->save();
    }
    
    public function getRemoved(){
        return $this->_dir->getRemoved();
    }
    
    public function remove()
    {
        global $CC_DBC;
Logging::log("remove!!");
        $music_dir_id = $this->getId();

        $sql = "SELECT DISTINCT s.instance_id from cc_music_dirs as md LEFT JOIN cc_files as f on f.directory = md.id
        RIGHT JOIN cc_schedule as s on s.file_id = f.id WHERE md.id = $music_dir_id";

        $show_instances = $CC_DBC->GetAll($sql);
        
        // get all the files on this dir
        $sql = "SELECT f.id FROM cc_music_dirs as md LEFT JOIN cc_files as f on f.directory = md.id WHERE md.id = $music_dir_id";
        $files = $CC_DBC->GetAll($sql);
        
        // set file_exist flag to false
        foreach( $files as $file_row ){
            Logging::log(print_r($file_row['id'], true));
            $temp_file = Application_Model_StoredFile::Recall($file_row['id']);
            $temp_file->setFileExistFlag(false);
        }
        
Logging::log("remove!!222222");
Logging::log(print_r($this->_dir,true));

        // set Removed flat to true
        self::setRemoved(true);
        //$res = $this->_dir->delete();
        
Logging::log("remove!!44444");
        foreach ($show_instances as $show_instance_row) {
            $temp_show = new Application_Model_ShowInstance($show_instance_row["instance_id"]);
            $temp_show->updateScheduledTime();
        }
Logging::log("remove end!!");
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
    private static function isAncestorDir($p_dir1, $p_dir2){
        if (strlen($p_dir1) > strlen($p_dir2)){
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
    public static function isPathValid($p_path){
        $dirs = self::getWatchedDirs();
        $dirs[] = self::getStorDir();
Logging::log("dirs: ".print_r($dirs, true));
        foreach ($dirs as $dirObj){
            $dir = $dirObj->getDirectory();
            $diff = strlen($dir) - strlen($p_path);
            if ($diff == 0){
                if ($dir == $p_path){
                    throw new NestedDirectoryException("'$p_path' is already watched.");
                }
            } else if ($diff > 0){
                if (self::isAncestorDir($p_path, $dir)){
                    throw new NestedDirectoryException("'$p_path' contains nested watched directory: '$dir'");
                }
            } else { /* diff < 0*/
                if (self::isAncestorDir($dir, $p_path)){
                    throw new NestedDirectoryException("'$p_path' is nested within existing watched directory: '$dir'");
                }
            }
        }
    }

    public static function addDir($p_path, $p_type)
    {
        if(!is_dir($p_path)){
            return array("code"=>2, "error"=>"'$p_path' is not a valid directory.");
        }
        $real_path = realpath($p_path)."/";
        if($real_path != "/"){
            $p_path = $real_path;
        }
        Logging::log("dir:".print_r($p_path, true));
        $exist_dir = self::getDirByPath($p_path);
        Logging::log(print_r($exist_dir, true));
        if( $exist_dir == NULL ){
            Logging::log("new");
            $dir = new CcMusicDirs();
        }else{
            Logging::log("exist");
            $dir = $exist_dir;
        }
        
        $dir->setType($p_type);
        $p_path = realpath($p_path)."/";


        try {
            /* isPathValid() checks if path is a substring or a superstring of an
             * existing dir and if not, throws NestedDirectoryException */
            self::isPathValid($p_path);
            $dir->setRemoved(false);
            $dir->setDirectory($p_path);
 Logging::log("dir obj:".print_r($dir, true));
            if( $exist_dir == NULL ){
                $dir->save();
            }
            return array("code"=>0);
        } catch (NestedDirectoryException $nde){
            $msg = $nde->getMessage();
            return array("code"=>1, "error"=>"$msg");
        } catch(Exception $e){
            return array("code"=>1, "error"=>"'$p_path' is already set as the current storage dir or in the watched folders list");
        }

    }

    public static function addWatchedDir($p_path)
    {
        $res = self::addDir($p_path, "watched");
        if ($res['code'] == 0){

            //convert "linked" files (Airtime <= 1.8.2) to watched files.
            $propel_link_dir = CcMusicDirsQuery::create()
               ->filterByType('link')
               ->findOne();

            //see if any linked files exist.
            if (isset($propel_link_dir)) {

                //newly added watched directory object
                $propel_new_watch = CcMusicDirsQuery::create()
                   ->filterByDirectory(realpath($p_path)."/")
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
        }
        return $res;
    }

    public static function getDirByPK($pk)
    {
        $dir = CcMusicDirsQuery::create()->findPK($pk);

        $mus_dir = new Application_Model_MusicDir($dir);

        return $mus_dir;
    }

    public static function getDirByPath($p_path)
    {
        Logging::log($p_path);
        $dir = CcMusicDirsQuery::create()
                    ->filterByDirectory($p_path)
                    ->findOne();

        if($dir == NULL){
            return null;
        }
        else{
            $mus_dir = new Application_Model_MusicDir($dir);
            return $mus_dir;
        }
    }

    public static function getWatchedDirs()
    {
        $result = array();

        $dirs = CcMusicDirsQuery::create()
                    ->filterByType("watched")
                    ->filterByRemoved(false)
                    ->find();

        foreach($dirs as $dir) {
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
        if(!is_dir($p_dir)){
            return array("code"=>2, "error"=>"'$p_dir' is not a valid directory.");
        }else if(Application_Model_Preference::GetImportTimestamp()+10 > time()){
            return array("code"=>3, "error"=>"Airtime is currently importing files. Please wait until this is complete before changing the storage directory.");
        }
        $dir = self::getStorDir();
        // if $p_dir doesn't exist in DB
        $exist = $dir->getDirByPath($p_dir);
        if($exist == NULL){
            $dir->setDirectory($p_dir);
            $dirId = $dir->getId();
            $data = array();
            $data["directory"] = $p_dir;
            $data["dir_id"] = $dirId;
            Application_Model_RabbitMq::SendMessageToMediaMonitor("change_stor", $data);
            return array("code"=>0);
        }else{
            return array("code"=>1, "error"=>"'$p_dir' is already set as the current storage dir or in the watched folders list.");
        }
    }

    public static function getWatchedDirFromFilepath($p_filepath)
    {
        $dirs = CcMusicDirsQuery::create()
                    ->filterByType(array("watched", "stor"))
                    ->filterByRemoved(false)
                    ->find();

        foreach($dirs as $dir) {
            $directory = $dir->getDirectory();
            if (substr($p_filepath, 0, strlen($directory)) === $directory) {
                $mus_dir = new Application_Model_MusicDir($dir);
                return $mus_dir;
            }
        }

        return null;
    }

    public static function removeWatchedDir($p_dir){
        $real_path = realpath($p_dir)."/";
        if($real_path != "/"){
            $p_dir = $real_path;
        }
        $dir = Application_Model_MusicDir::getDirByPath($p_dir);
        Logging::log(print_r($dir,true));
        if($dir == NULL){
            return array("code"=>1,"error"=>"'$p_dir' doesn't exist in the watched list.");
        }else{
            $dir->remove();
            $data = array();
            $data["directory"] = $p_dir;
            Application_Model_RabbitMq::SendMessageToMediaMonitor("remove_watch", $data);
            return array("code"=>0);
        }
    }

    public static function splitFilePath($p_filepath)
    {
        $mus_dir = self::getWatchedDirFromFilepath($p_filepath);
Logging::log("mus_dir:".print_r($mus_dir, true));
        if(is_null($mus_dir)) {
            return null;
        }

        $length_dir = strlen($mus_dir->getDirectory());
        $fp = substr($p_filepath, $length_dir);

        return array($mus_dir->getDirectory(), $fp);
    }

}
