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
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License     New BSD License
 */
class ZFDebug_Controller_Plugin_Debug_Plugin_Memory extends Zend_Controller_Plugin_Abstract implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface
{
    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $_identifier = 'memory';

    /**
     * @var array
     */
    protected $_memory = array(
        'dispatchLoopShutdown' => 0, 
        'dispatchLoopStartup' => 0
    );

    protected $_closingBracket = null;

    /**
     * Creating time plugin
     * @return void
     */
    public function __construct()
    {
        Zend_Controller_Front::getInstance()->registerPlugin($this);
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
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAGvSURBVDjLpZO7alZREEbXiSdqJJDKYJNCkPBXYq12prHwBezSCpaidnY+graCYO0DpLRTQcR3EFLl8p+9525xgkRIJJApB2bN+gZmqCouU+NZzVef9isyUYeIRD0RTz482xouBBBNHi5u4JlkgUfx+evhxQ2aJRrJ/oFjUWysXeG45cUBy+aoJ90Sj0LGFY6anw2o1y/mK2ZS5pQ50+2XiBbdCvPk+mpw2OM/Bo92IJMhgiGCox+JeNEksIC11eLwvAhlzuAO37+BG9y9x3FTuiWTzhH61QFvdg5AdAZIB3Mw50AKsaRJYlGsX0tymTzf2y1TR9WwbogYY3ZhxR26gBmocrxMuhZNE435FtmSx1tP8QgiHEvj45d3jNlONouAKrjjzWaDv4CkmmNu/Pz9CzVh++Yd2rIz5tTnwdZmAzNymXT9F5AtMFeaTogJYkJfdsaaGpyO4E62pJ0yUCtKQFxo0hAT1JU2CWNOJ5vvP4AIcKeao17c2ljFE8SKEkVdWWxu42GYK9KE4c3O20pzSpyyoCx4v/6ECkCTCqccKorNxR5uSXgQnmQkw2Xf+Q+0iqQ9Ap64TwAAAABJRU5ErkJggg==';
    }

    /**
     * Gets menu tab for the Debugbar
     *
     * @return string
     */
    public function getTab()
    {
        if (function_exists('memory_get_peak_usage')) {
            return round(memory_get_peak_usage()/1024) . 'K of '.ini_get("memory_limit");
        }
        return 'MemUsage n.a.';
    }

    /**
     * Gets content panel for the Debugbar
     *
     * @return string
     */
    public function getPanel()
    {
        $panel = '<h4>Memory Usage</h4>';
        $panel .= 'Dispatch: ' . round(($this->_memory['dispatchLoopShutdown']-$this->_memory['dispatchLoopStartup'])/1024,2) .'K'.$this->getLinebreak();
        if (isset($this->_memory['user']) && count($this->_memory['user'])) {
            foreach ($this->_memory['user'] as $key => $value) {
                $panel .= $key.': '.round($value/1024).'K'.$this->getLinebreak();
            }
        }
        return $panel;
    }
    
    /**
     * Sets a memory mark identified with $name
     *
     * @param string $name
     */
    public function mark($name) {
        if (!function_exists('memory_get_peak_usage')) {
            return;
        }
        if (isset($this->_memory['user'][$name]))
            $this->_memory['user'][$name] = memory_get_peak_usage()-$this->_memory['user'][$name];
        else
            $this->_memory['user'][$name] = memory_get_peak_usage();
    }
    
    
    /**
     * Defined by Zend_Controller_Plugin_Abstract
     *
     * @param Zend_Controller_Request_Abstract
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if (function_exists('memory_get_peak_usage')) {
            $this->_memory['dispatchLoopStartup'] = memory_get_peak_usage();
        }
    }

    /**
     * Defined by Zend_Controller_Plugin_Abstract
     *
     * @param Zend_Controller_Request_Abstract
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        if (function_exists('memory_get_peak_usage')) {
            $this->_memory['dispatchLoopShutdown'] = memory_get_peak_usage();
        }
    }
    
    public function getLinebreak()
    {
        return '<br'.$this->getClosingBracket();
    }

    public function getClosingBracket()
    {
        if (!$this->_closingBracket) {
            if ($this->_isXhtml()) {
                $this->_closingBracket = ' />';
            } else {
                $this->_closingBracket = '>';
            }
        }

        return $this->_closingBracket;
    }  
    
    protected function _isXhtml()
    {
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
        $doctype = $view->doctype();
        return $doctype->isXhtml();
    }
    
}