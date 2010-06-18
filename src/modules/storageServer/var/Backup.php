<?php
define('BACKUP_EXT', 'tar');
define('ACCESS_TYPE', 'backup');

/**
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision:  $
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class Backup
{
    /**
     * Name of logfile
     * @var string
     */
    private $logFile;

    /**
     * Session id
     * @var string
     */
    private $sessid;

    /**
     * struct - see search criteria
     * @var array
     */
    private $criteria;

    /**
     * @var string
     */
    private $token;

    /**
     * name of statusfile
     * @var string
     */
    private $statusFile;

    /**
     * Affected gunids
     * @var array
     */
    private $ids;

    /**
     * Array of affected filenames
     * @var array
     */
    private $filenames  = array();

    /**
     * Base tmp name
     * @var string
     */
    private $tmpName;

    /**
     * Name of temporary tarball file
     * @var string
     */
    private $tmpFile;

    /**
     * Name of temporary directory
     * @var string
     */
    private $tmpDir;

    /**
     * Name of temporary playlist directory
     * @var string
     */
    private $tmpDirPlaylist;

    /**
     * Name of temporary audioclip directory
     * @var string
     */
    private $tmpDirClip;

    /**
     * Name of temporary metafile directory
     * @var string
     */
    private $tmpDirMeta;

    /**
     * @var string
     */
    private $loglevel = 'warn';  # 'debug';

    /**
     * @var GreenBox
     */
    private $gb;

    /**
     * @param GreeenBox $gb
     */
    public function __construct(&$gb)
    {
        global $CC_CONFIG;
        $this->gb =& $gb;
        $this->token = null;
        $this->logFile = $CC_CONFIG['bufferDir'].'/'.ACCESS_TYPE.'.log';
        $this->addLogItem("-I- ".date("Ymd-H:i:s")." construct\n");
    }


    /**
     * Open a backup
     *      Create a backup file (tarball)
     *
     * @param string $sessid
     * @param array $criteria
     * 		struct - see search criteria
     * @return array
     * 		hasharray with field:
     *      token string: backup token
     */
    public function openBackup($sessid, $criteria='')
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." openBackup - sessid:$sessid\n");
        }
        $this->sessid   = $sessid;
        $this->criteria = $criteria;

        // get ids (and real filenames) which files match with criteria
        $srch = $this->gb->localSearch($this->criteria,$this->sessid);
        if (PEAR::isError($srch)) {
        	return $srch;
        }
        $this->setIDs($srch);

        // get real filenames
        if (is_array($this->ids)) {
            $this->setFilenames();

            $this->setEnviroment(true);

            // write a status file
            file_put_contents($this->statusFile, 'working');

            // save the metafile to tmpdir
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

            // copy all file to tmpdir
            $this->copyAllFiles();

            // do everything
            $this->doIt();

            return array('token'=>$this->token);
        } else {
        	return false;
        }
    }


    /**
     * Check the status of backup.
     *
     * @param unknown $token
     * @return array
     *      status  : string - susccess | working | fault
     *      faultString: string - description of fault
     *      token   : stirng - backup token
     *      url     : string - access url
     *      tmpfile : string - access filename
     */
    public function checkBackup($token)
    {
        global $CC_CONFIG;
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
                $r['url'] = BasicStor::GetUrlPart()."access/$token.".BACKUP_EXT;
                $r['tmpfile'] = $CC_CONFIG['accessDir']."/$token.".BACKUP_EXT;
            case 'working':
            case 'fault':
                $r['status'] = $status;
                $r['faultString'] = $faultString;
                $r['token'] = $token;
                break;
        }
        return $r;
    }


    /**
     * Close a backup
     *
     * @param unknown $token
     * @return boolean
     */
    public function closeBackup($token)
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." closeBackup - token:$token\n");
        }
        # post procedures
        $this->token = $token;
        $this->setEnviroment();
        BasicStor::bsRelease($token, ACCESS_TYPE);
        Backup::rRmDir($this->tmpDir);
        unlink($this->statusFile);
        unlink($this->tmpFile);
        if (is_file($this->tmpName)) {
        	unlink($this->tmpName);
        }
        return !is_file($this->tmpFile);
    }


    /**
     *  list of unclosed backups
     *
     *  @param string $stat
     *      if this parameter is not set, then return with all unclosed backups
     *  @return array of hasharray with field:
     *      status : string - susccess | working | fault
     *      token  : stirng - backup token
     *      url    : string - access url
     */
    public function listBackups($stat='')
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." listBackups - stat:$stat\n");
        }
        // open temporary dir
        $tokens = BasicStor::GetTokensByType(ACCESS_TYPE);
        // echo '<XMP>tokens:'; print_r($tokens); echo '</XMP>';
        foreach ($tokens as $token) {
            $st = $this->checkBackup($token);
            if ($stat=='' || $st['status']==$stat) {
                $r[] = $st;
            }
        }
        return $r;
    }


    /**
     * Set the ids from searchResult
     *
     * @param array $searchResult : array of gunids
     */
    private function setIDs($searchResult)
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
     * Set the filenames from ids.
     *
     */
    private function setFilenames()
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." setFilenames\n");
        }
        if (is_array($this->ids)) {
            foreach ($this->ids as $i => $item) {
                $gunid = $item['gunid'];
                // get a stored file object of this gunid
                $sf = StoredFile::RecallByGunid($gunid);
                if (is_null($sf) || PEAR::isError($sf)) {
                	return $sf;
                }
                $lid = BasicStor::IdFromGunid($gunid);
                if (($res = BasicStor::Authorize('read', $lid, $this->sessid)) !== TRUE) {
                    $this->addLogItem("-E- ".date("Ymd-H:i:s")." setFilenames - authorize gunid:$gunid\n");
                    return PEAR::raiseError('Backup::setFilenames : Authorize ... error.');
                }
                // if the file is a playlist then it has only a meta file
                if (strtolower($sf->md->format) != 'playlist') {
                    $this->filenames[] = array(
                        'filename'  => $sf->getRealFileName(),
                        'format'    => $sf->md->format
                    );
                }
                $this->filenames[] = array(
                    'filename'  => $sf->getRealMetadataFileName(),
                    'format'    => $sf->md->format
                );
                if ($this->loglevel=='debug') {
                    $this->addLogItem("-I- ".date("Ymd-H:i:s")." setFilenames - add file: {$sf->md->format}|".$sf->getRealMetadataFileName()."\n");
                }
            }
            return $this->filenames;
        } else {
            $this->addLogItem("-E- ".date("Ymd-H:i:s")." setFilenames - The IDs variable isn't array.\n");
            return PEAR::raiseError('Backup::setFilenames : The IDs variable isn\'t array.');
        }
    }


    /**
     * Create the tarball - call the shell script
     *
     */
    private function doIt()
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." doIt\n");
        }
        $command = dirname(__FILe__)."/../bin/backup.sh"
            ." {$this->tmpDir}"
            ." {$this->tmpFile}"
            ." {$this->statusFile}"
            ." >> {$this->logFile} &";
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
    private function copyAllFiles()
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
    private function setEnviroment($createDir=false)
    {
        global $CC_CONFIG;
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." setEnviroment - createDirs:$createDir\n");
        }
        // create temporary directories
        if (is_null($this->token) && $createDir) {
            $this->tmpName = tempnam($CC_CONFIG['bufferDir'], ACCESS_TYPE.'_');
            $this->tmpFile = $this->tmpName.'.'.BACKUP_EXT;
            $this->tmpDir = $this->tmpName.'.dir';
            $this->tmpDirPlaylist = $this->tmpDir. '/playlist';
            $this->tmpDirClip = $this->tmpDir. '/audioClip';
            $this->tmpDirMeta = $this->tmpDir. '/meta-inf';
            touch($this->tmpFile);
            mkdir($this->tmpDir);
            mkdir($this->tmpDirPlaylist);
            mkdir($this->tmpDirClip);
            mkdir($this->tmpDirMeta);
            $this->genToken();
        } else {
            $symlink = $CC_CONFIG['accessDir'].'/'.$this->token.'.'.BACKUP_EXT;
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
        $this->statusFile = $CC_CONFIG['accessDir'].'/'.$this->token.'.'.BACKUP_EXT.'.status';
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
     * Generate a new token.
     * @return void
     */
    private function genToken()
    {
        $acc = BasicStor::bsAccess($this->tmpFile, BACKUP_EXT, null, ACCESS_TYPE);
        if (PEAR::isError($acc)) {
        	return $acc;
        }
        $this->token = $acc['token'];
    }


    /**
     * Add a line to the logfile.
     *
     * @param string $item
     * 		the new row of log file
     */
    private function addLogItem($item)
    {
        $f = fopen($this->logFile,'a');
        fwrite($f,$item);
        fclose($f);
    }


    /**
     * Delete a directory recursive
     *
     * @param string $dirname
     * 		path of dir.
     */
    private static function rRmDir($dirname)
    {
        if (is_dir($dirname)) {
            $dir_handle = opendir($dirname);
        }
        while ($file = readdir($dir_handle)) {
            if ( ($file != ".") && ($file != "..") ) {
                if (!is_dir($dirname."/".$file)) {
                    unlink ($dirname."/".$file);
                } else {
                    Backup::rRmDir($dirname."/".$file);
                }
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

} // classs Backup
?>
