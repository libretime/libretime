<?php
define('ACCESS_TYPE', 'restore');

class Restore {
    /**
     *  string - name of logfile
     */
    var $logFile;

    /**
     *  string  -  session id
     */
    var $sessid;

    /**
     *  string - token
     */
    var $token;
    /**
     *  string - name of statusfile
     */
    var $statusFile;
     /**
     *  string - name of temporary directory, to here extract the backup tarball
     */
    var $tmpDir;        
    
    /**
     *  string - loglevel
     */
    var $loglevel = 'warn';
    #var $loglevel = 'debug';
    
    /**
     *  greenbox object reference
     */
    var $gb;

    /**
     *  Constructor
     *
     *  @param gb: greenbox object reference
     */
    function Restore (&$gb) {
        $this->gb       =& $gb;
        $this->token    = null;
        $this->logFile  = $this->gb->bufferDir.'/'.ACCESS_TYPE.'.log';
        if ($this->loglevel=='debug') {
        	$this->addLogItem("-I- ".date("Ymd-H:i:s")." construct\n");
        }
    }
    
    /**
     *  Call asyncronly the restore procedure
     *      Restore from backup.
     *
     *  @param sessid      : string  -  session id
     *  @param backup_file : path of the backup file
     *  @return hasharray with field: 
     *      token string: backup token
     */
    function openRestore($sessid,$backup_file) {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I-".date("Ymd-H:i:s")." doRestore - sessid:$sessid\n");
        }
        $this->sessid = $sessid;
        
        //generate token
        $this->token = StoredFile::_createGunid();
                
        // status file -> working
        $this->setEnviroment();
        file_put_contents($this->statusFile, 'working');
        
        //call the restore script in background
        $command = dirname(__FILE__).'/../bin/restore.php';
        $runLog = "/dev/null";
        $params = "{$backup_file} {$this->statusFile} {$this->token} {$sessid}>> $runLog &";
        system("$command $params");
        
        return array('token'=>$this->token);
    }
    
    /**
     *  check the status of restore
     *
     *  @param token : token
     *  @return hasharray with field: 
     *      status  : string - susccess | working | fault
     *      faultString : string - description of fault
     *      token   : stirng - backup token
     *      url     : string - access url
     *      tmpfile : string - access filename
     */
    function checkRestore($token) {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." checkBackup - token:$token\n");
        }
        $this->token = $token;
        $this->setEnviroment();
        if (is_file($this->statusFile)) {
            $stat = file_get_contents($this->statusFile);
            if (strpos($stat,'fault|')!==false) {
                list($stat,$message) = explode('|',$stat);
            }
            $r['status']    = $stat;
            if ($stat=='fault') $r['faultString'] = $message;
            $r['token']     = $token;
            return $r;
        } else {
            return PEAR::raiseError('Restore::checkRestore: invalid token!');
        }
    }
    
    /**
     *  check the status of restore
     *
     *  @param token : token
     *  @return hasharray with field: 
     *      status  : boolean - is susccess
     */
    function closeRestore($token) {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." checkBackup - token:$token\n");
        }
        $this->token = $token;
        $this->setEnviroment();
        $this->rRmDir($this->tmpDir);
        unlink($this->statusFile);
        return !is_file($this->statusFile);
    }
    
    /**
     * Do restore in background
     * 
     * this function is called from the asyncron commandline script
     * 		../bin/restore.php
     *
     *  @param backupfile : string - path of backupfile
     *  @param token	  : string - restore token
     *  @param sessid	  : string - session id
     */
    function startRestore($backupfile,$token,$sessid) {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." startRestore - bufile:$backupfile | token:$token\n");
        }
        $this->token  = $token;
        $this->sessid = $sessid;
        $this->setEnviroment();
        
        // extract tarball
        $command = 'tar -xf '.$backupfile.' --directory '.$this->tmpDir;
        $res = system($command);
        #$this->addLogItem('command: '.$command."\n");
        #$this->addLogItem('res: '.$res."\n");
        
        //simple check of archive format
        if (is_dir($this->tmpDir.'audioClip/') &&
            is_dir($this->tmpDir.'meta-inf/') &&
            is_dir($this->tmpDir.'playlist/')) {
            //search metafiles
            $this->metafiles = $this->getMetaFiles();
            #$this->addLogItem('metafiles:'.print_r($this->metafiles,true));
            //add to storage server
            foreach ($this->metafiles as $info) {
                $r = $this->addFileToStorage($info['file'],$info['type'],$info['id']);
                if(PEAR::isError($r)){
                    $this->addLogItem("-E- ".date("Ymd-H:i:s").
                        " startRestore - addFileToStorage \n".
                        "(".$put->getMessage()."/".$put->getUserInfo().")\n"
                    );
                 	file_put_contents($this->statusFile, 'fault|'.$put->getMessage()."/".$put->getUserInfo());
                    return;
                }
            }
        } else {
            $this->addLogItem("-E- ".date("Ymd-H:i:s")." startRestore - invalid archive format\n");
          	file_put_contents($this->statusFile, 'fault|invalid archive format');
          	return;
        }
        file_put_contents($this->statusFile, 'success');
    }
    
    /**
     *  get the metafiles
     *
     *  @return array of hasharray with field: 
     *      file    : string - metafile path
     *      type    : stirng - audioClip | playlist
     *      id      : string - the backuped gunid
     */
    function getMetaFiles() {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." getMetaFiles - tmpDir:{$this->tmpDir}\n");
        }
        $audioclips = scandir($this->tmpDir.'audioClip/');
        $playlists = scandir($this->tmpDir.'playlist/');
        for ($i=0;$i<count($audioclips);$i++) {
            if (strpos($audioclips[$i],'xml')!==false) 
                $r[]=array (    'file' => $this->tmpDir.'audioClip/'.$audioclips[$i],
                                'type' => 'audioClip',
                                'id'   => str_replace('.xml','',$audioclips[$i]));
        }
        for ($i=0;$i<count($playlists);$i++) {
            if (strpos($playlists[$i],'xml')!==false)
                $r[]=array (    'file' => $this->tmpDir.'playlist/'.$playlists[$i],
                                'type' => 'playlist',
                                'id'   => str_replace('.xml','',$playlists[$i]));
        }
        return $r;
    }
    
    /**
     * Add the file to the storage server
     *
     *  @param file   : string - path of metafile
     *  @param type	  : string - restore token
     *  @param sessid	  : string - session id
     * 
     *  @return true if succes or PEAR error
     */
    function addFileToStorage($file,$type,$gunid) {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." addFileToStorage - file:$file | type:$type | id:$gunid\n");
        }
        require_once "XmlParser.php";
        $tree = XmlParser::parse($file);
        $mediaFileLP = str_replace('.xml','',$file);
        $mediaFileLP = ($type=='audioClip' && is_file($mediaFileLP))?$mediaFileLP:'';
        $ex = $r = $this->gb->existsFile($this->sessid,$gunid);
        if (PEAR::isError($r)) {
            $this->addLogItem("-E- ".date("Ymd-H:i:s").
                " addFileToStorage - existsFile($gunid) ".
                "(".$r->getMessage()."/".$r->getUserInfo().")\n"
            );
        }
        if (!PEAR::isError($ex) && $ex) { // file is exists in storage server
            //replace it
            $id = $this->gb->_idFromGunid($gunid);
            $replace = $this->gb->replaceFile(
                $id,   				# id int, virt.file's local id
                $mediaFileLP,       # mediaFileLP string, local path of media file
                $file,              # mdataFileLP string, local path of metadata file
                $this->sessid);     # sessid string, session id
            if (PEAR::isError($replace)) {
            	$this->addLogItem("-E- ".date("Ymd-H:i:s").
            	    " addFileToStorage - replaceFile Error ".
                    "(".$replace->getMessage()."/".$replace->getUserInfo().")\n"
                );
        	  	file_put_contents($this->statusFile, 'fault|'.$replace->getMessage()."/".$replace->getUserInfo());
            	return $replace;
            }
            #$this->addLogItem("replace it \n");
        } else {
            // add as new
            $parid = $this->gb->_getHomeDirIdFromSess($this->sessid);
            #$this->addLogItem("Parid:$parid\n");
            $name = $tree->children[0]->children[0]->content;
            if(empty($name)) $name = $tree->attrs['title']->val;
            if(empty($name)) $name = '???';
            if ($this->loglevel=='debug') {
                $this->addLogItem("-I- ".date("Ymd-H:i:s")." putFile\n".
                    "$parid, $name, $mediaFileLP, $file, {$this->sessid}, $gunid, $type \n"
                );
            }
            $put = $this->gb->putFile(
                $parid,             # parent id
                $name,              # name of original file
                $mediaFileLP,       # media file if have
                $file,              # meta file
                $this->sessid,      # sessid
                $gunid,             # gunid
                $type               # type
            );
         #   $this->addLogItem("add as new \n");
            if (PEAR::isError($put)) {
                $this->addLogItem("-E- ".date("Ymd-H:i:s").
                    " addFileToStorage - putFile Error ".
                    "(".$put->getMessage()."/".$put->getUserInfo().")\n"
                    ."\n---\n".file_get_contents($file)."\n---\n"
                );
           		file_put_contents($this->statusFile, 'fault|'.$put->getMessage()."/".$put->getUserInfo());
                //$this->addLogItem("Error Object: ".print_r($put,true)."\n");
                return $put;
            }
        }
        $ac = $r = StoredFile::recallByGunid($this->gb, $gunid);
        if (PEAR::isError($r)) { return $r; }
        $res = $r = $ac->setState('ready');
        if (PEAR::isError($r)) { return $r; }
        return true;
    }
    
    /**
     *  Figure out the enviroment to the backup
     *
     */
    function setEnviroment() {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." setEnviroment\n");
        }

        $this->statusFile = $this->gb->accessDir.'/'.$this->token.'.status';
        $this->tmpDir     = '/tmp/ls_restore/'.$this->token.'/';
        $this->rMkDir($this->tmpDir);
    }

    
    /**
     *  Add a line to the logfile.
     *
     *  @param item : string - the new row of log file
     */
    function addLogItem($item) {
        $f = fopen ($this->logFile,'a');
        flock($f,LOCK_SH);                                                                                                                                       
        fwrite($f,$item);
        flock($f,LOCK_UN);                                                                                                                                       
        fclose($f);
        //echo file_get_contents($this->logFile)."<BR><BR>\n\n";
    }
    
    /**
     * Delete a directory recursive
     *
     *  @param dirname : string - path of dir.
     * 
     *  @return boolean : is success
     */
    function rRmDir($dirname) {
        if(is_dir($dirname))
            $dir_handle = opendir($dirname);
        while($file = readdir($dir_handle)) {
            if($file!="." && $file!="..") {
                if(!is_dir($dirname."/".$file))
                    unlink ($dirname."/".$file);
                else
                    Restore::rRmDir($dirname."/".$file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }
    
    /**
     * Create a directory recursive
     *
     *  @param dirname   : string  - path of dir.
     *  @param mode      : octal   - rights of dir.
     *  @param recursive : boolean - do it recursive.
     * 
     *  @return boolean 
     */
    function rMkDir($dirname,$mode=0777,$recursive=true) {
        if( is_null($dirname) || $dirname === "" ){
            return false;
        }
        if( is_dir($dirname) || $dirname === "/" ){
            return true;
        }
        if($this->rMkDir(dirname($dirname), $mode, $recursive)) {
            return mkdir($dirname, $mode);
        }
        return false;
    }
}
?>