<?php
require_once (dirname(__FILE__).'/Crontab.php');
require_once (dirname(__FILE__).'/../conf.php');
/**
 * This class can call a PHP function from crontab.
 * Example:
 * <pre>
 *    $cron = new Cron();
 *    $access = $cron->openCrontab('write');
 *    if ($access != 'write') {
 *        do {
 *           $access = $cron->forceWriteable();
 *        } while ($access != 'write');
 *    }
 *    $cron->addCronJob('*','*','*','*','*',
 *              'ClassName',
 *              array('first','secound','third')
 *    );
 *    $cron->closeCrontab();
 * </pre>
 * @package Campcaster
 * @subpackage StorageServer.Cron
 */
class Cron {
    /**
     * @var Crontab
     */
    public $ct;

    /**
     * @var array This array created with getCommand() function
     */
    private $params;

    /**
     * @var string available values: read | write
     */
    private $ctAccess = 'read';

    private $lockfile;
    private $cronfile;
    private $paramdir;
    private $cronUserName;

    /**
     * Constructor
     */
    function Cron() {
        global $CC_CONFIG;
        $this->lockfile = $CC_CONFIG['lockfile'];
        $this->cronfile = $CC_CONFIG['cronfile'];
        $this->paramdir = $CC_CONFIG['paramdir'];
        $this->cronUserName = $CC_CONFIG['cronUserName'];
    }


    /* ==================================================== Cronjob functions */
    /**
     * Add a cronjob to the crontab
     *
     * @access public
     * @param string    $m          minute
     * @param string    $h          hour
     * @param string    $dom        day of month
     * @param string    $mo         month
     * @param string    $dow        day of week
     * @param string    $className  name of class, which's execute() is called by croncall.php
     * @param string    $params     the parameter(s)
     * @return bool     true if success else PEAR error.
     */
    function addCronJob($m, $h, $dom, $mo, $dow, $className, $params)
    {
        if ($this->ctAccess == 'write') {
            $this->ct->addCron($m, $h, $dom, $mo, $dow,
                $this->getCommand($className, $params));
            return true;
        } else {
            return PEAR::raiseError('CronJob::addCronJob : '.
                'The crontab is not writable');
        }
    }

    /**
     * This function return with the active cronjobs
     *
     * @access public
     * @return array array of cronjob struct
     */
    function listCronJob()
    {
        return $this->ct->getByType(CRON_CMD);
    }

    /**
     * Remove a cronjob.
     *
     * @access public
     * @param  int  $index index of the cronjobs' array.
     * @return bool true if success else PEAR error.
     */
    function removeCronJob($index)
    {
        if ($this->ctAccess == 'write') {
            $this->crontab->delEntry($index);
            return true;
        } else {
            return PEAR::raiseError('CronJob::removeCronJob : '.
                'The crontab is not writable');
        }
    }

    /* ==================================================== Crontab functions */
    /**
     * Open the crontab
     *
     * @access public
     * @param string $access only for listing 'read', for add and delete 'write'
     * @return string sucessed access - available values read | write
     */
    function openCrontab($access = 'read')
    {
        $access = strtolower($access);
        $this->ct = new Crontab($this->cronUserName);
        if ($access == 'write' &&
            $this->isCrontabWritable() &&
            $this->lockCrontab()) {
                $this->ctAccess = $access;
        } else {
            $this->ctAccess = 'read';
        }
        return $this->ctAccess;
    }

    /**
     * Close the crontab
     *
     * @access public
     * @return bool true if everything is ok, false is the lock file can't delete
     */
    function closeCrontab()
    {
        if ($this->ctAccess == 'write') {
            $this->ct->writeCrontab();
        }
        return $this->ctAccess == 'write' ? $this->unlockCrontab() : true;
    }

    /**
     * Check the crontab is writable
     *
     * @access private
     * @return bool
     */
    function isCrontabWritable()
    {
        return !is_file($this->lockfile);
    }

    /**
     * Try to lock the crontab
     *
     * @access private
     * @return bool true if the locking is success
     */
    function lockCrontab()
    {
        return touch($this->lockfile);
    }

    /**
     * Try to unlock the crontab
     *
     * @access private
     * @return bool true if the unlocking is success
     */
    function unlockCrontab()
    {
        return unlink($this->lockfile);
    }

    /**
     * If the crontab opened with read access. This function force set
     * the access to write.
     *
     * @access public
     * @return bool true if the setting is success
     */
    function forceWriteable()
    {
        if ($this->isCrontabWritable() && $this->lockCrontab()) {
            $this->ctAccess = 'write';
            return true;
        }
        return false;
    }

    /* ======================================================= Misc functions */
    /**
     * Get the shell command for the cronjob
     *
     * @param string $className name of the class what is called by croncall.php
     * @param mixed $params with this parameter could be called the execute() of class
     * @return string shell command
     */
    function getCommand($className, $params)
    {
        $this->params = array (
            'class' => $className,
            'params' => $params
        );
        return $this->cronfile.' "'.str_replace('"','\"',serialize($this->params)).'"';
    }
}
?>