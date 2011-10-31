<?php
/**
 * ZFDebug Zend Additions
 *
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License     New BSD License
 */
class ZFDebug_Controller_Plugin_Debug_Plugin_Database extends ZFDebug_Controller_Plugin_Debug_Plugin implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface
{

    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $_identifier = 'database';

    /**
     * @var array
     */
    protected $_db = array();
    
    protected $_explain = false;

    /**
     * Create ZFDebug_Controller_Plugin_Debug_Plugin_Variables
     *
     * @param Zend_Db_Adapter_Abstract|array $adapters
     * @return void
     */
    public function __construct(array $options = array())
    {
        if(!isset($options['adapter']) || !count($options['adapter'])) {
            if (Zend_Db_Table_Abstract::getDefaultAdapter()) {
                $this->_db[0] = Zend_Db_Table_Abstract::getDefaultAdapter();
                $this->_db[0]->getProfiler()->setEnabled(true);
            }
        } else if ($options['adapter'] instanceof Zend_Db_Adapter_Abstract ) {
            $this->_db[0] = $options['adapter'];
        	$this->_db[0]->getProfiler()->setEnabled(true);
        } else {
            foreach ($options['adapter'] as $name => $adapter) {
                if ($adapter instanceof Zend_Db_Adapter_Abstract) {
                    $adapter->getProfiler()->setEnabled(true);
                    $this->_db[$name] = $adapter;
                }
            }
        }
        
        if (isset($options['explain'])) {            
            $this->_explain = (bool)$options['explain'];
        }
    }

    /**
     * Gets identifier for this plugin
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }
    
    /**
     * Returns the base64 encoded icon
     *
     * @return string
     **/
    public function getIconData()
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAEYSURBVBgZBcHPio5hGAfg6/2+R980k6wmJgsJ5U/ZOAqbSc2GnXOwUg7BESgLUeIQ1GSjLFnMwsKGGg1qxJRmPM97/1zXFAAAAEADdlfZzr26miup2svnelq7d2aYgt3rebl585wN6+K3I1/9fJe7O/uIePP2SypJkiRJ0vMhr55FLCA3zgIAOK9uQ4MS361ZOSX+OrTvkgINSjS/HIvhjxNNFGgQsbSmabohKDNoUGLohsls6BaiQIMSs2FYmnXdUsygQYmumy3Nhi6igwalDEOJEjPKP7CA2aFNK8Bkyy3fdNCg7r9/fW3jgpVJbDmy5+PB2IYp4MXFelQ7izPrhkPHB+P5/PjhD5gCgCenx+VR/dODEwD+A3T7nqbxwf1HAAAAAElFTkSuQmCC';
    }

    /**
     * Gets menu tab for the Debugbar
     *
     * @return string
     */
    public function getTab()
    {
        if (!$this->_db)
            return 'No adapter';

        foreach ($this->_db as $adapter) {
            $profiler = $adapter->getProfiler();
            $adapterInfo[] = $profiler->getTotalNumQueries().' in '.round($profiler->getTotalElapsedSecs()*1000, 2).' ms';
        }
        $html = implode(' / ', $adapterInfo);

        return $html;
    }

    /**
     * Gets content panel for the Debugbar
     *
     * @return string
     */
    public function getPanel()
    {
        if (!$this->_db)
            return '';

        $html = '<h4>Database queries</h4>';
        if (Zend_Db_Table_Abstract::getDefaultMetadataCache ()) {
            $html .= 'Metadata cache is ENABLED';
        } else {
            $html .= 'Metadata cache is DISABLED';
        }

        # For adding quotes to query params
        function add_quotes(&$value, $key) {
            $value = "'".$value."'";
        }

        foreach ($this->_db as $name => $adapter) {
            if ($profiles = $adapter->getProfiler()->getQueryProfiles()) {
                $adapter->getProfiler()->setEnabled(false);
                $html .= '<h4>Adapter '.$name.'</h4><ol>';
                foreach ($profiles as $profile) {
                    $params = $profile->getQueryParams();
                    array_walk($params, 'add_quotes');
                    $paramCount = count($params);
                    if ($paramCount) {
                        $html .= '<li>'.htmlspecialchars(preg_replace(array_fill(0, $paramCount, '/\?/'), $params, $profile->getQuery(), 1));
                    } else {
                        $html .= '<li>'.htmlspecialchars($profile->getQuery());
                    }
                    $html .= '<p><strong>Time:</strong> '.round($profile->getElapsedSecs()*1000, 2).' ms'.$this->getLinebreak();
                    
                    $supportedAdapter = ($adapter instanceof Zend_Db_Adapter_Mysqli 
                        || $adapter instanceof Zend_Db_Adapter_Pdo_Mysql);
                
                    # Run explain if enabled, supported adapter and SELECT query
                    if ($this->_explain && $supportedAdapter && Zend_Db_Profiler::SELECT == $profile->getQueryType()) {
                        $explain = $adapter->fetchRow('EXPLAIN '.$profile->getQuery());
                        $html .= '<strong>Type:</strong> '.strtolower($explain['select_type']).', '.$explain['type'].$this->getLinebreak()
                                .'<strong>Possible Keys:</strong> '.$explain['possible_keys'].$this->getLinebreak()
                                .'<strong>Key Used:</strong> '.$explain['key'].$this->getLinebreak()
                                .'<strong>Rows:</strong> '.$explain['rows'].$this->getLinebreak()
                                .'<strong>Extra:</strong> '.$explain['Extra'];
                    }

                    $html .= '</p></li>';
                }
                $html .= '</ol>';
            }
        }

        return $html;
    }

}