<?php
/**
 * @package Campcaster
 * @subpackage htmlUI
 * @version $Revision$
 */
class uiExchange
{
	private $Base;
	private $file;
	private $folder;

    public function __construct(&$uiBase)
    {
        $this->Base =& $uiBase;
        $this->file =& $_SESSION['EXCHANGE']['file'];
        $this->folder =& $_SESSION['EXCHANGE']['folder'];
        if (empty($this->folder)) {
            $this->folder = '/tmp';
        }
    }

    // GB wrapper methods

    function getBackupToken()
    {
        $token = $this->Base->gb->loadPref($this->Base->sessid, UI_BACKUPTOKEN_KEY);

        if (PEAR::isError($token)) {
            return false;
        }
        return $token;
    }


    function createBackupOpen()
    {
        $criteria = array('filetype' => UI_FILETYPE_ANY);
        $token = $this->Base->gb->createBackupOpen($this->Base->sessid, $criteria);
        if (PEAR::isError($token)) {
            $this->Base->_retMsg('Error initializing backup: $1', $token->getMessage());
            return false;
        }
        $this->createBackupCheck();
        $this->Base->gb->savePref($this->Base->sessid, UI_BACKUPTOKEN_KEY, $token['token']);
        return true;
    }


    function createBackupCheck()
    {
        $token = $this->getBackupToken();
        if ($token === false) {
            return false;
        }
        $res = $this->Base->gb->createBackupCheck($token);
        if (PEAR::isError($res)) {
            $this->Base->_retMsg('Unable to check backup status: $1', $res->getMessage());
            return false;
        }
        return $res;
    }


    // Download the backup

    function createBackupDownload()
    {
        $check = $this->createBackupCheck();
        header('Content-Length: '.filesize($check['tmpfile']));
        header("Content-Transfer-Encoding: binary");
        $newname='backup_'.date("Ymd", filectime($check['tmpfile'])).'.tar';
        header('Content-Disposition: attachment; filename="'.$newname.'"');
        readfile($check['tmpfile']);
    }


    function createBackupClose()
    {
        $token = $this->getBackupToken();

        if ($token === false) {
            $this->Base->_retMsg('Token not available');
            return false;
        }
        $status = $this->Base->gb->createBackupClose($token);
        if (PEAR::isError($status)) {
            $this->Base->_retMsg('Error closing backup: $1', $status->getMessage());
            return false;
        }
        if ($status === true) {
            $this->Base->gb->delPref($this->Base->sessid, UI_BACKUPTOKEN_KEY);
        }
        return $status;
    }


    // backup schduler methods
    /**
     * @return array
     */
    function getScheduleBackupForm()
    {
        include('formmask/exchange.inc.php');
        $form = new HTML_QuickForm('BACKUP_Schedule', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        uiBase::parseArrayToForm($form, $mask['BACKUP.schedule']);
        $renderer = new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        return $renderer->toArray();
    }


    /**
     * Copy a file or directory.
     *
     * @param string $target
     * 		path to file or directory
     * @return boolean
     */
    function copy2target($target)
    {
        if (is_dir($target)) {
            $this->folder = $target;
        } else {
            $pathinfo = pathinfo($target);
            $this->folder = $pathinfo['dirname'];
            $this->file   = $pathinfo['basename'];
        }

        $this->completeTarget();
        $target = $this->folder.'/'.$this->file;

        if ($this->checkTarget() !== true) {
            $this->Base->_retMsg('$1: open: permission denied', $target);
            return false;
        }

        $check = $this->createBackupCheck();

        if ($check['status'] === 'success') {
            if (@copy($check['tmpfile'], $target)) {
                @chmod($target,0666);
                $this->Base->_retMsg('Copy backup to $1 successfull', $target);
                return true;
            }
            $this->Base->_retMsg('Unable to copy backup from $1 to $2', $check['tmpfile'], $target);
            return false;
        }
        $this->Base->_retMsg('Backup status is $1, not ready', $check['status']);
        return false;
    }

    // Restore methods
    function getRestoreToken()
    {
    	$token = $this->Base->gb->loadPref($this->Base->sessid, UI_RESTORETOKEN_KEY);
        if (PEAR::isError($token)) {
            return false;
        }
        return $token;
    }


    /**
     * @param unknown_type $backupFile
     * @return boolean
     */
    function backupRestoreOpen($backupFile)
    {
        $token = $this->Base->gb->backupRestoreOpen($this->Base->sessid,$backupFile);
        if (PEAR::isError($token)) {
            $this->Base->_retMsg('Error initializing backup restore: $1', $token->getMessage());
            return false;
        }
        #$this->backupRestoreCheck();  //?
        $this->Base->gb->savePref($this->Base->sessid, UI_RESTORETOKEN_KEY, $token['token']);
        return true;
    }


    function backupRestoreCheck()
    {
        $token = $this->getRestoreToken();
        if ($token === false) {
            return false;
        }
        $res = $this->Base->gb->backupRestoreCheck($token);
        if (PEAR::isError($res)) {
            $this->Base->_retMsg('Unable to check backup restore status: $1', $res->getMessage());
            return false;
        }
        return $res;
    }


    function backupRestoreClose()
    {
        $token = $this->getRestoreToken();
        if ($token === false) {
            $this->Base->_retMsg('Backup restore token is not available');
            return false;
        }
        $status = $this->Base->gb->backupRestoreClose($token);
        if (PEAR::isError($status)) {
            $this->Base->_retMsg('Error closing restore backup: $1', $status->getMessage());
            return false;
        }
        if ($status === true) {
            $this->Base->gb->delPref($this->Base->sessid, UI_RESTORETOKEN_KEY);
        }
        return $status;
    }

    // file browser methods

    function setFolder($subfolder)
    {
        $this->file = false;
        $newfolder = realpath($this->folder.'/'.$subfolder);

        if (!is_dir($newfolder)) {
            # F5 pressed
            return false;
        }

        if (!is_executable($newfolder)) {
            $this->errorMsg = tra('$1: cd: permission denied', $newfolder);
            return false;
        }

        $this->folder = $newfolder;
    }


    function setFile($file)
    {
        if (is_writable($this->folder.'/'.$file)) {
            $this->file = $file;

            return true;
        }
        $this->file = false;
        $this->errorMsg = tra('$1: open: permission denied', $file);

        return false;
    }

    function getPath()
    {
        $path = $this->folder.'/'.$this->file;

        if (is_dir($this->folder)) {
            return str_replace('//', '/', $path);
        }

        return false;
    }

    function checkTarget()
    {
        if (!is_writable($this->folder.'/'.$this->file) && !is_writable($this->folder)) {
            return false;
        }
        return true;
    }


    function completeTarget()
    {
        if (!$this->file) {
            $this->file = 'ls-backup_'.date('Y-m-d');
        }
    }


    function listFolder()
    {
        if (!is_readable($this->folder)) {
            $this->errorMsg = tra('$1: ls: permission denied', $this->folder);
            return array('subdirs' => array('..' => array()), 'files' => array());
        }

        $d = dir($this->folder);

        while (false !== ($entry = $d->read())) {
            $loc = $this->folder.'/'.$entry;

            if (is_dir($loc)) {
                $dirs[$entry]  = $this->_getFileProperty($loc);
            } else {
                $files[$entry] = $this->_getFileProperty($loc);
            }

        }

        @ksort($dirs);
        @ksort($files);

        return array('subdirs' => $dirs, 'files' => $files);
    }

    function _getFileProperty($loc)
    {
        $uarr  = posix_getpwuid(fileowner($loc));
        $user  = $uarr['name'];
        $grarr = posix_getgrgid(filegroup($loc));
        $group = $grarr['name'];
        return array(
                    'r' => is_readable($loc),
                    'w' => is_writable($loc),
                    'x' => is_executable($loc),
                    'u' => $user,
                    'g' => $group,
                );

    }
}
?>