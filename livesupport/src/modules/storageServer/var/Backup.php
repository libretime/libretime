<?php
define('BACKUP_EXT', 'tar');
define('ACCESS_TYPE', 'backup');

class Backup 
{
    /**
     *  string - name of logfile
     */
    var $logFile;

    /**
     *  string  -  session id
     */
    var $sessid;
    /**
     *  struct - see search criteria
     */
    var $criteria;

    /**
     *  string - token
     */
    var $token;
    /**
     *  string - name of statusfile
     */
    var $statusFile;
    /**
     *  array - affected gunids
     */
    var $ids;
    /**
     *  array - array of affected filenames
     */
    var $filenames  = array();
    
    /**
     *  string - base tmp name
     */
    var $tmpName;
    /**
     *  stirng - name of temporary tarball file
     */
    var $tmpFile;
    /**
     *  string - name of temporary directory
     */
    var $tmpDir;        
    /**
     *  string - name of temporary playlist directory
     */
    var $tmpDirPlaylist;    
    /**
     *  string - name of temporary audioclip directory
     */
    var $tmpDirClip;   
    /**
     *  string - name of temporary metafile directory
     */
    var $tmpDirMeta;    
    
    /**
     *  string - loglevel
     */
    var $loglevel = 'warn';  # 'debug';
    
    /**
     *  greenbox object reference
     */
    var $gb;

    /**
     *  Constructor
     *
     *  @param gb: greenbox object reference
     */
    function Backup (&$gb)
    {
        $this->gb       =& $gb;
        $this->token    = null;
        $this->logFile  = $this->gb->bufferDir.'/'.ACCESS_TYPE.'.log';
        $this->addLogItem("-I- ".date("Ymd-H:i:s")." construct\n");
    }
    
    /**
     *  Open a backup
     *      Create a backup file (tarball)
     *
     *  @param sessid  :  string  -  session id
     *  @param criteria : struct - see search criteria
     *  @return hasharray with field: 
     *      token string: backup token
     */
    function openBackup($sessid,$criteria='')
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." openBackup - sessid:$sessid\n");
        }
        $this->sessid   = $sessid;
        $this->criteria = $criteria;
        
        # get ids (and real filenames) which files match with criteria
        $srch = $r = $this->gb->localSearch($this->criteria,$this->sessid);
        if(PEAR::isError($r)){ return $r; }
        $this->setIDs($srch);
        #echo '<XMP>this->ids:'; print_r($this->ids); echo '</XMP>';
        
        # get real filenames
        if (is_array($this->ids)) {
            $this->setFilenames();
            #echo '<XMP>this->filenames:'; print_r($this->filenames); echo '</XMP>';
               
            $this->setEnviroment(true);
            
            # write a status file
            file_put_contents($this->statusFile, 'working');
    
            # save the metafile to tmpdir
            $hostname = trim(`hostname`);
            $ctime      = time();
            $ctime_f    = date("Ymd-H:i:s");
            file_put_contents("{$this->tmpDirMeta}/storage.xml",
                "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n".
                "<storage\n".
                " type=\"".ACCESS_TYPE."\"\n".
                " version=\"1.0\"\n".
                " ctime=\"$ctime\"\n".
                " hostname=\"$hostname\"\n".
                "/><!-- $ctime_f -->\n"
            );
    
            # copy all file to tmpdir
            $this->copyAllFiles();
            
            # do everything
            $this->doIt();
            
            return array('token'=>$this->token);
        } else return false;
    }
    
    /**
     *  check the status of backup
     *
     *  @param token : token
     *  @return hasharray with field: 
     *      status  : string - susccess | working | fault
     *      faultString: string - description of fault
     *      token   : stirng - backup token
     *      url     : string - access url
     *      tmpfile : string - access filename
     */
    function checkBackup($token)
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." checkBackup - token:$token\n");
        }
        $this->token = $token;
        $this->setEnviroment();
        $status = file_get_contents($this->statusFile);
        if (strpos($status,'fault')!==false) {
            list($status,$faultString) = explode('|',$status); 
        }
        switch ($status) {
            case 'success':
                $r['url']       = $this->gb->getUrlPart()."access/$token.".BACKUP_EXT;
                $r['tmpfile']   = $this->gb->accessDir."/$token.".BACKUP_EXT;
            case 'working':
            case 'fault':
                $r['status']    = $status;
                $r['faultString'] = $faultString;
                $r['token']     = $token;
            break;
        }
        return $r;
    }
    
    /**
     *  Close a backup
     *
     *  @param token   : token
     *  @return status : boolean
     */
    function closeBackup($token)
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." closeBackup - token:$token\n");
        }
        # post procedures
        $this->token = $token;
        $this->setEnviroment();
        $this->gb->bsRelease($token,ACCESS_TYPE);
        Backup::rRmDir($this->tmpDir);
        unlink($this->statusFile);
        unlink($this->tmpFile);
        if (is_file($this->tmpName)) unlink($this->tmpName);
        return !is_file($this->tmpFile);
    }
    
    /**
     *  list of unclosed backups
     *
     *  @param stat : status (optional)
     *      if this parameter is not set, then return with all unclosed backups
     *  @return array of hasharray with field: 
     *      status : string - susccess | working | fault
     *      token  : stirng - backup token
     *      url    : string - access url
     */
    function listBackups($stat='')
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." listBackups - stat:$stat\n");
        }
        # open temporary dir
        $tokens = $this->gb->getTokensByType(ACCESS_TYPE);
        # echo '<XMP>tokens:'; print_r($tokens); echo '</XMP>';
        foreach ($tokens as $token) {
            $st = $this->checkBackup($token);
            if ($stat=='' || $st['status']==$stat) {
                $r[] = $st;
            }
        }
        return $r;
    }
    
    /**
     *  set the ids from searchResult
     *
     *  @param searchResult : array of gunids
     */
    function setIDs($searchResult)
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." setIDs\n");
        }
        if (is_array($searchResult['results'])) {
            $this->ids = $searchResult['results'];
        } else {
            $this->addLogItem("-E- ".date("Ymd-H:i:s")." setIDs - the parameter is not array!\n");
            return PEAR::raiseError('The IDs variable isn\'t array.');
        }
    }
    
    /**
     *  set the filenames from ids
     *
     */
    function setFilenames ()
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." setFilenames\n");
        }
        if (is_array($this->ids)) {
            foreach ($this->ids as $i=>$item) {
                $gunid = $item['gunid'];
                # get a stored file object of this gunid
                $sf = $r = StoredFile::recallByGunid($this->gb, $gunid);
                if(PEAR::isError($r)) return $r;
                $lid = $this->gb->_idFromGunid($gunid);
                if(($res = $this->gb->_authorize('read', $lid, $this->sessid)) !== TRUE){
                    $this->addLogItem("-E- ".date("Ymd-H:i:s")." setFilenames - authorize gunid:$gunid\n");
                    return PEAR::raiseError('Backup::setFilenames : Authorize ... error.');
                }
                # if the file is a playlist then it have only meta file
                if (strtolower($sf->md->format)!='playlist') {
                    $this->filenames[] = array(
                        'filename'  => $sf->_getRealRADFname(), # get real filename of raw media data
                        'format'    => $sf->md->format
                    );
                }
                $this->filenames[] = array(
                    'filename'  => $sf->_getRealMDFname(), # get real filename of metadata file
                    'format'    => $sf->md->format
                );
                if ($this->loglevel=='debug') {
                    $this->addLogItem("-I- ".date("Ymd-H:i:s")." setFilenames - add file: {$sf->md->format}|".$sf->_getRealMDFname()."\n");
                }
            }
            return $this->filenames;
        } else {
            $this->addLogItem("-E- ".date("Ymd-H:i:s")." setFilenames - The IDs variable isn't array.\n");
            return PEAR::raiseError('Backup::setFilenames : The IDs variable isn\'t array.');
        }
    }
    
    /**
     *  Create the tarball - call the shell script
     *
     */
    function doIt()
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." doIt\n");
        }
        $command = dirname(__FILE__).'/../bin/backup.sh'.
            " {$this->tmpDir}".
            " {$this->tmpFile}".
            " {$this->statusFile}".
            " >> {$this->logFile} &";
        $res = system("$command");
        sleep(2);
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." doIt - command:$command\n");
        }
    }
    
    /**
     *  Copy the real files into the tmp dirs to tar they.
     *
     */
    function copyAllFiles()
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." copyAllFiles\n");
        }
        //echo '<XMP>this->filenames:'; print_r($this->filenames); echo '</XMP>';   
        if (is_array($this->filenames)) {
            foreach ($this->filenames as $v) {
                # get the filename from full path
                $fn = substr($v['filename'],strrpos($v['filename'],'/'));
                switch (strtolower($v['format'])) {
                    case 'playlist':
                        # if playlist then copy to the playlist dir
                        copy($v['filename'],$this->tmpDirPlaylist.$fn);
                        break;
                    case 'audioclip':
                        # if audioclip then copy to the audioclip dir
                        copy($v['filename'],$this->tmpDirClip.$fn);
                        break;
                }
            }
        }
    }
    
    /**
     *  Figure out the enviroment to the backup
     *
     */
    function setEnviroment($createDir=false)
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." setEnviroment - createDirs:$createDir\n");
        }
        # create a temporary directories
        if (is_null($this->token) && $createDir) {
            $this->tmpName          = tempnam($this->gb->bufferDir, ACCESS_TYPE.'_');
            $this->tmpFile          = $this->tmpName.'.'.BACKUP_EXT;
            $this->tmpDir           = $this->tmpName.'.dir';        
            $this->tmpDirPlaylist   = $this->tmpDir. '/playlist';    
            $this->tmpDirClip       = $this->tmpDir. '/audioClip';   
            $this->tmpDirMeta       = $this->tmpDir. '/meta-inf';
            touch($this->tmpFile);
            mkdir($this->tmpDir);
            mkdir($this->tmpDirPlaylist);
            mkdir($this->tmpDirClip);
            mkdir($this->tmpDirMeta);
            $this->genToken();
        } else {
            $symlink = $this->gb->accessDir.'/'.$this->token.'.'.BACKUP_EXT;
            if (is_link($symlink) && is_file(readlink($symlink))) {
                $this->tmpName          = str_replace('.tar','',readlink($symlink));
                $this->tmpFile          = $this->tmpName.'.'.BACKUP_EXT;
                $this->tmpDir           = $this->tmpName.'.dir';        
                $this->tmpDirPlaylist   = $this->tmpDir. '/playlist';    
                $this->tmpDirClip       = $this->tmpDir. '/audioClip';   
                $this->tmpDirMeta       = $this->tmpDir. '/meta-inf';
            } else {
                $this->addLogItem("-E- ".date("Ymd-H:i:s")." setEnviroment - Corrupt symbolic link.\n");
                return false;                 
            }
        }
        $this->statusFile       = $this->gb->accessDir.'/'.$this->token.'.'.BACKUP_EXT.'.status';
        if ($this->loglevel=='debug') {
            $this->addLogItem("this->tmpName: $this->tmpName\n");
            $this->addLogItem("this->tmpFile: $this->tmpFile\n");
            $this->addLogItem("this->tmpDir: $this->tmpDir\n");
            $this->addLogItem("this->tmpDirPlaylist: $this->tmpDirPlaylist\n");
            $this->addLogItem("this->tmpDirClip: $this->tmpDirClip\n");
            $this->addLogItem("this->tmpDirMeta: $this->tmpDirMeta\n");
            $this->addLogItem("this->token: $this->token\n");
            $this->addLogItem("this->statusFile: $this->statusFile\n");
        }
    }
    
    /**
     *  generate a new token.
     *
     */
    function genToken()
    {
        $acc = $this->gb->bsAccess($this->tmpFile, BACKUP_EXT, null, ACCESS_TYPE);
        if($this->gb->dbc->isError($acc)){ return $acc; }
        $this->token = $acc['token'];
    }
    
    /**
     *  Add a line to the logfile.
     *
     *  @param item : string - the new row of log file
     */
    function addLogItem($item)
    {
        $f = fopen ($this->logFile,'a');
        fwrite($f,$item);
        fclose($f);
        //echo file_get_contents($this->logFile)."<BR><BR>\n\n";
    }
    
    /**
     * Delete a directory recursive
     *
     *  @param dirname : string - path of dir.
     */
    function rRmDir($dirname)
    {
        if(is_dir($dirname))
            $dir_handle = opendir($dirname);
        while($file = readdir($dir_handle)) {
            if($file!="." && $file!="..") {
                if(!is_dir($dirname."/".$file))
                    unlink ($dirname."/".$file);
                else 
                    Backup::rRmDir($dirname."/".$file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }
}
?>