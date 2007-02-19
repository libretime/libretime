<?php
define('ACCESS_TYPE', 'restore');

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
class Restore {
    /**
     * Name of logfile
     * @var string
     */
    private $logFile;

    /**
     * session id
     * @var string
     */
    private $sessid;

    /**
     * @var string
     */
    private $token;

    /**
     * Name of statusfile
     * @var string
     */
    private $statusFile;

    /**
     * Name of temporary directory, to here extract the backup tarball
     * @var string
     */
    private $tmpDir;

    /**
     * @var string
     */
    // private $loglevel = 'warn';
    public $loglevel = 'warn';
    // public $loglevel = 'debug';

    /**
     * @var GreenBox
     */
    private $gb;

    /**
     * @param GreenBox $gb
     * 		greenbox object reference
     */
    public function __construct(&$gb)
    {
        global $CC_CONFIG;
        $this->gb =& $gb;
        $this->token = null;
        $this->logFile = $CC_CONFIG['bufferDir'].'/'.ACCESS_TYPE.'.log';
        if ($this->loglevel == 'debug') {
        	$this->addLogItem("-I- ".date("Ymd-H:i:s")." construct\n");
        }
    }


    /**
     * Call asyncronously the restore procedure.  Restore from backup.
     *
     * @param string $sessid
     * 		session id
     * @param string $backup_file
     * 		path of the backup file
     * @return array
     * 		hasharray with field:
     *      token string: backup token
     */
    function openRestore($sessid, $backup_file)
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I-".date("Ymd-H:i:s")." doRestore - sessid:$sessid\n");
        }
        $this->sessid = $sessid;

        // generate token
        $this->token = StoredFile::CreateGunid();

        // status file -> working
        $this->setEnviroment();
        file_put_contents($this->statusFile, 'working');

        //call the restore script in background
        $command = dirname(__FILE__).'/../bin/restore.php';
        $runLog = "/dev/null";
        $params = "{$backup_file} {$this->statusFile} {$this->token} {$sessid}>> $runLog &";
        $ret = system("$command $params", $st);
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I-".date("Ymd-H:i:s")." restore.php call: $st/$ret\n");
        }

        return array('token'=>$this->token);
    }


    /**
     * Check the status of restore
     *
     * @param string $token
     * @return array
     * 		hasharray with field:
     *      status  : string - susccess | working | fault
     *      faultString : string - description of fault
     *      token   : stirng - backup token
     *      url     : string - access url
     *      tmpfile : string - access filename
     */
    function checkRestore($token)
    {
        if ($this->loglevel == 'debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." checkBackup - token:$token\n");
        }
        $this->token = $token;
        $this->setEnviroment();
        if (is_file($this->statusFile)) {
            $r = array();
            $stat = file_get_contents($this->statusFile);
            if (strpos($stat,'fault|') !== false) {
                list($stat,$message) = explode('|',$stat);
            }
            $r['status'] = $stat;
            if ($stat=='fault') {
            	$r['faultString'] = $message;
            } else {
            	$r['faultString'] = '';
            }
            return $r;
        } else {
            return PEAR::raiseError('Restore::checkRestore: invalid token!');
        }
    }


    /**
     * Check the status of restore.
     *
     * @param string $token
     * @return array
     * 		hasharray with field:
     *      status  : boolean - is success
     */
    function closeRestore($token)
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." checkBackup - token:$token\n");
        }
        $this->token = $token;
        $this->setEnviroment();
        $this->rRmDir($this->tmpDir);
        unlink($this->statusFile);
        return array("status" => !is_file($this->statusFile));
    }


    /**
     * Do restore in background
     *
     * this function is called from the asyncron commandline script
     * 		../bin/restore.php
     *
     * @param string $backupfile
     * 		path of backupfile
     * @param string $token
     * 		restore token
     * @param string $sessid
     * 		session id
     */
    function startRestore($backupfile, $token, $sessid)
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." startRestore - bufile:$backupfile | token:$token\n");
        }
        $this->token  = $token;
        $this->sessid = $sessid;
        $this->setEnviroment();

        // extract tarball
        $command = 'tar -xf '.$backupfile .' --directory '.$this->tmpDir;
        $res = system($command);
        //$this->addLogItem('command: '.$command."\n");
        //$this->addLogItem('res: '.$res."\n");

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
                if (PEAR::isError($r)) {
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
        // unlink($backupfile);
    }


    /**
     * Get the metafiles.
     *
     * @return array
     * 		array of hasharray with field:
     *      file    : string - metafile path
     *      type    : stirng - audioClip | playlist
     *      id      : string - the backuped gunid
     */
    function getMetaFiles()
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." getMetaFiles - tmpDir:{$this->tmpDir}\n");
        }
        $audioclips = scandir($this->tmpDir.'audioClip/');
        $playlists = scandir($this->tmpDir.'playlist/');
        for ($i = 0; $i < count($audioclips); $i++) {
            if (strpos($audioclips[$i],'xml')!==false)
                $r[] = array('file' => $this->tmpDir.'audioClip/'.$audioclips[$i],
                             'type' => 'audioClip',
                             'id'   => str_replace('.xml','',$audioclips[$i]));
        }
        for ($i = 0; $i < count($playlists); $i++) {
            if (strpos($playlists[$i],'xml') !== false)
                $r[] = array('file' => $this->tmpDir.'playlist/'.$playlists[$i],
                             'type' => 'playlist',
                             'id'   => str_replace('.xml','',$playlists[$i]));
        }
        return $r;
    }


    /**
     * Add the file to the storage server.
     *
     *  @param string $file
     * 		path of metafile
     *  @param string $type
     * 		restore token
     *  @param string $sessid
     * 		session id
     *
     *  @return mixed
     * 		true if success or PEAR_error
     */
    function addFileToStorage($file,$type,$gunid)
    {
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." addFileToStorage - file:$file | type:$type | id:$gunid\n");
        }
        require_once("XmlParser.php");
        $tree = XmlParser::parse($file);
        $mediaFileLP = str_replace('.xml','',$file);
        $mediaFileLP = ($type=='audioClip' && is_file($mediaFileLP))?$mediaFileLP:'';
        $ex = $this->gb->existsFile($this->sessid,$gunid);
        if (PEAR::isError($ex)) {
            $this->addLogItem("-E- ".date("Ymd-H:i:s").
                " addFileToStorage - existsFile($gunid) ".
                "(".$ex->getMessage()."/".$ex->getUserInfo().")\n"
            );
        }
        if (!PEAR::isError($ex) && $ex) { // file is exists in storage server
            //replace it
            $id = BasicStor::IdFromGunid($gunid);
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
            $name = $tree->children[0]->children[0]->content;
            if (empty($name)) {
            	$name = $tree->attrs['title']->val;
            }
            if (empty($name)) {
            	$name = '???';
            }
            if ($this->loglevel=='debug') {
                $this->addLogItem("-I- ".date("Ymd-H:i:s")." putFile\n".
                    "$parid, $name, $mediaFileLP, $file, {$this->sessid}, $gunid, $type \n"
                );
            }
            $values = array(
                "filename" => $name,
                "filepath" => $mediaFileLP,
                "metadata" => $file,
                "gunid" => $gunid,
                "filetype" => $type
            );
            $put = $this->gb->putFile($parid, $values, $this->sessid);
            //$this->addLogItem("add as new \n");
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
        $ac = StoredFile::RecallByGunid($gunid);
        if (is_null($ac) || PEAR::isError($ac)) {
        	return $ac;
        }
        $res = $ac->setState('ready');
        if (PEAR::isError($res)) {
        	return $res;
        }
        return true;
    }


    /**
     * Figure out the environment to the backup.
     *
     */
    function setEnviroment()
    {
        global $CC_CONFIG;
        if ($this->loglevel=='debug') {
            $this->addLogItem("-I- ".date("Ymd-H:i:s")." setEnviroment\n");
        }
        $this->statusFile = $CC_CONFIG['accessDir'].'/'.$this->token.'.status';
        $this->tmpDir = '/tmp/ls_restore/'.$this->token.'/';
        $this->rMkDir($this->tmpDir);
    }


    /**
     * Add a line to the logfile.
     *
     * @param string $item
     * 		the new row of log file
     */
    function addLogItem($item)
    {
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
     * @param string $dirname
     * 		path of dir
     *
     * @return boolean
     * 		is success
     */
    function rRmDir($dirname)
    {
        if (is_dir($dirname)) {
            $dir_handle = opendir($dirname);
        }
        while ($file = readdir($dir_handle)) {
            if ($file!="." && $file!="..") {
                if (!is_dir($dirname."/".$file)) {
                    unlink ($dirname."/".$file);
                } else {
                    Restore::rRmDir($dirname."/".$file);
                }
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }


    /**
     * Create a directory recursive
     *
     * @param string $dirname
     * 		path of dir.
     * @param int $mode
     * 		octal - rights of dir.
     * @param boolean $recursive
     * 		do it recursive.
     *
     * @return boolean
     */
    function rMkDir($dirname, $mode=0777, $recursive=true)
    {
        if (is_null($dirname) || $dirname === "" ) {
            return false;
        }
        if (is_dir($dirname) || $dirname === "/" ) {
            return true;
        }
        if ($this->rMkDir(dirname($dirname), $mode, $recursive)) {
            return mkdir($dirname, $mode);
        }
        return false;
    }

} // class Restore
?>
