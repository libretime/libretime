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
 * @see Zend_Session
 */
require_once 'Zend/Session.php';

/**
 * @see Zend_Session_Namespace
 */
require_once 'Zend/Session/Namespace.php';

/**
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License     New BSD License
 */
class ZFDebug_Controller_Plugin_Debug_Plugin_Time extends Zend_Controller_Plugin_Abstract implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface
{
    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $_identifier = 'time';

    /**
     * @var array
     */
    protected $_timer = array(
        'dispatchLoopStartup' => 0,
        'dispatchLoopShutdown' => 0
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
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAKrSURBVDjLpdPbT9IBAMXx/qR6qNbWUy89WS5rmVtutbZalwcNgyRLLMyuoomaZpRQCt5yNRELL0TkBSXUTBT5hZSXQPwBAvor/fZGazlb6+G8nIfP0znbgG3/kz+Knsbb+xxNV63DLxVLHzqV0vCrfMluzFmw1OW8ePEwf8+WgM1UXDnapVgLePr5Nj9DJBJGFEN8+TzKqL2RzkenV4yl5ws2BXob1WVeZxXhoB+PP0xzt0Bly0fKTePozV5GphYQPA46as+gU5/K+w2w6Ev2Ol/KpNCigM01R2uPgDcQIRSJEYys4JmNoO/y0tbnY9JlxnA9M15bfHZHCnjzVN4x7TLz6fMSJqsPgLAoMvV1niSQBGIbUP3Ki93t57XhItVXjulTQHf9hfk5/xgGyzQTgQjx7xvE4nG0j3UsiiLR1VVaLN3YpkTuNLgZGzRSq8wQUoD16flkOPSF28/cLCYkwqvrrAGXC1UYWtuRX1PR5RhgTJTI1Q4wKwzwWHk4kQI6a04nQ99mUOlczMYkFhPrBMQoN+7eQ35Nhc01SvA7OEMSFzTv8c/0UXc54xfQcj/bNzNmRmNy0zctMpeEQFSio/cdvqUICz9AiEPb+DLK2gE+2MrR5qXPpoAn6mxdr1GBwz1FiclDcAPCEkTXIboByz8guA75eg8WxxDtFZloZIdNKaDu5rnt9UVHE5POep6Zh7llmsQlLBNLSMTiEm5hGXXDJ6qb3zJiLaIiJy1Zpjy587ch1ahOKJ6XHGGiv5KeQSfFun4ulb/josZOYY0di/0tw9YCquX7KZVnFW46Ze2V4wU1ivRYe1UWI1Y1vgkDvo9PGLIoabp7kIrctJXSS8eKtjyTtuDErrK8jIYHuQf8VbK0RJUsLfEg94BfIztkLMvP3v3XN/5rfgIYvAvmgKE6GAAAAABJRU5ErkJggg==';
    }

    /**
     * Gets menu tab for the Debugbar
     *
     * @return string
     */
    public function getTab()
    {
        return round($this->_timer['dispatchLoopShutdown'],2) .'/'.round($this->_timer['dispatchLoopShutdown']-$this->_timer['dispatchLoopStartup'],2). ' ms';
    }

    /**
     * Gets content panel for the Debugbar
     *
     * @return string
     */
    public function getPanel()
    {
        $html = '<h4>Custom Timers</h4>';
        $html .= 'Dispatch: ' . round(($this->_timer['dispatchLoopShutdown']-$this->_timer['dispatchLoopStartup']),2) .' ms'.$this->getLinebreak();
        if (isset($this->_timer['user']) && count($this->_timer['user'])) {
            foreach ($this->_timer['user'] as $name => $time) {
                $html .= ''.$name.': '. round($time,2).' ms'.$this->getLinebreak();
            }
        }

        if (!Zend_Session::isStarted()){
            Zend_Session::start();
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this_module = $request->getModuleName();
        $this_controller = $request->getControllerName();
        $this_action = $request->getActionName();

        $timerNamespace = new Zend_Session_Namespace('ZFDebug_Time',false);
        $timerNamespace->data[$this_module][$this_controller][$this_action][] = round($this->_timer['dispatchLoopShutdown'],2);

        $html .= '<h4>Overall Timers</h4>';

        foreach ($timerNamespace->data as $module => $controller)
        {
            if ($module != $this_module) {
                continue;
            }
            $html .= $module . $this->getLinebreak();
            $html .= '<div class="pre">';
            foreach ($controller as $con => $action)
            {
                if ($con != $this_controller) {
                    continue;
                }
                $html .= '    ' . $con . $this->getLinebreak();
                $html .= '<div class="pre">';
                foreach ($action as $key => $data)
                {
                    if ($key != $this_action) {
                        continue;
                    }
                    $html .= '        ' . $key . $this->getLinebreak();
                    $html .= '<div class="pre">';
                    $html .= '            Avg: ' . $this->_calcAvg($data) . ' ms / '.count($data).' requests'.$this->getLinebreak();
                    $html .= '            Min: ' . round(min($data), 2) . ' ms'.$this->getLinebreak();
                    $html .= '            Max: ' . round(max($data), 2) . ' ms'.$this->getLinebreak();
                    $html .= '</div>';
                }
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        $html .= $this->getLinebreak().'Reset timers by sending ZFDEBUG_RESET as a GET/POST parameter';

        return $html;
    }

    /**
     * Sets a time mark identified with $name
     *
     * @param string $name
     */
    public function mark($name) {
        if (isset($this->_timer['user'][$name]))
            $this->_timer['user'][$name] = (microtime(true)-$_SERVER['REQUEST_TIME'])*1000-$this->_timer['user'][$name];
        else
            $this->_timer['user'][$name] = (microtime(true)-$_SERVER['REQUEST_TIME'])*1000;
    }

    #public function routeStartup(Zend_Controller_Request_Abstract $request) {
    #     $this->timer['routeStartup'] = (microtime(true)-$_SERVER['REQUEST_TIME'])*1000;
    #}

    #public function routeShutdown(Zend_Controller_Request_Abstract $request) {
    #     $this->timer['routeShutdown'] = (microtime(true)-$_SERVER['REQUEST_TIME'])*1000;
    #}

    /**
     * Defined by Zend_Controller_Plugin_Abstract
     *
     * @param Zend_Controller_Request_Abstract
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $reset = Zend_Controller_Front::getInstance()->getRequest()->getParam('ZFDEBUG_RESET');
        if (isset($reset)) {
            if (!Zend_Session::isStarted()) {
                Zend_Session::start();
            }
            $timerNamespace = new Zend_Session_Namespace('ZFDebug_Time',false);
            $timerNamespace->unsetAll();
        }
        
        $this->_timer['dispatchLoopStartup'] = (microtime(true)-$_SERVER['REQUEST_TIME'])*1000;
    }

    /**
     * Defined by Zend_Controller_Plugin_Abstract
     *
     * @param Zend_Controller_Request_Abstract
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        $this->_timer['dispatchLoopShutdown'] = (microtime(true)-$_SERVER['REQUEST_TIME'])*1000;
    }
    
    /**
     * Calculate average time from $array
     *
     * @param array $array
     * @param int $precision
     * @return float
     */
    protected function _calcAvg(array $array, $precision=2)
    {
        if (!is_array($array)) {
            return 'ERROR in method _calcAvg(): this is a not array';
        }

        foreach ($array as $value)
            if (!is_numeric($value)) {
                return 'ERROR in method _calcAvg(): the array contains one or more non-numeric values';
            }

        $cuantos=count($array);
        return round(array_sum($array)/$cuantos,$precision);
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