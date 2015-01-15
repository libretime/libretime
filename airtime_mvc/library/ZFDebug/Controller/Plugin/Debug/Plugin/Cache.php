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
class ZFDebug_Controller_Plugin_Debug_Plugin_Cache extends ZFDebug_Controller_Plugin_Debug_Plugin implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface
{
    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $_identifier = 'cache';

    /**
     * @var Zend_Cache_Backend_ExtendedInterface
     */
    protected $_cacheBackends = array();

    /**
     * Create ZFDebug_Controller_Plugin_Debug_Plugin_Cache
     *
     * @param array $options
     * @return void
     */
    public function __construct(array $options = array())
    {
        if (!isset($options['backend'])) {
            throw new Zend_Exception("ZFDebug: Cache plugin needs 'backend' parameter");
        }
        is_array($options['backend']) || $options['backend'] = array($options['backend']);
        foreach ($options['backend'] as $name => $backend) {
            if ($backend instanceof Zend_Cache_Backend_ExtendedInterface ) {
                $this->_cacheBackends[$name] = $backend;
            }
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
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAI/SURBVDjLjZPbS9NhHMYH+zNidtCSQrqwQtY5y2QtT2QGrTZf13TkoYFlzsWa/tzcoR3cSc2xYUlGJfzAaIRltY0N12H5I+jaOxG8De+evhtdOP1hu3hv3sPzPO/z4SsBIPnfuvG8cbBlWiEVO5OUItA0VS8oxi9EdhXo+6yV3V3UGHRvVXHNfNv6zRfNuBZVoiFcB/3LdnQ8U+Gk+bhPVKB3qUOuf6/muaQR/qwDkZ9BRFdCmMr5EPz6BN7lMYylLGgNNaKqt3K0SKDnQ7us690t3rNsxeyvaUz+8OJpzo/QNzd8WTtcaQ7WlBmPvxhx1V2Pg7oDziIBimwwf3qAGWESkVwQ7owNujk1ztvk+cg4NnAUTT4FrrjqUKHdF9jxBfXr1rgjaSk4OlMcLrnOrJ7latxbL1V2lgvlbG9MtMTrMw1r1PImtfyn1n5q47TlBLf90n5NmalMtUdKZoyQMkLKlIGLjMyYhFpmlz3nGEVmFJlRZNaf7pIaEndM24XIjCOzjX9mm2S2JsqdkMYIqbB1j5C6yWzVk7YRFTsGFu7l+4nveExIA9aMCcOJh6DIoMigyOh+o4UryRWQOtIjaJtoziM1FD0mpE4uZcTc72gBaUyYKEI6khgqINXO3saR7kM8IZUVCRDS0Ucf+xFbCReQhr97MZ51wpWxYnhpCD3zOrT4lTisr+AJqVx0Fiiyr4/vhP4VyyMFIUWNqRrV96vWKXKckBoIqWzXYcoPDrUslDJoopuEVEpIB0sR+AuErIiZ6OqMKAAAAABJRU5ErkJggg==';
    }

    /**
     * Gets menu tab for the Debugbar
     *
     * @return string
     */
    public function getTab()
    {
        return 'Cache';
    }

    /**
     * Gets content panel for the Debugbar
     *
     * @return string
     */
    public function getPanel()
    {
        $panel = '';

        # Support for APC
        if (function_exists('apc_sma_info') && ini_get('apc.enabled')) {
            $mem = apc_sma_info();
            $mem_size = $mem['num_seg']*$mem['seg_size'];
            $mem_avail = $mem['avail_mem'];
            $mem_used = $mem_size-$mem_avail;
            
            $cache = apc_cache_info();
            
            $panel .= '<h4>APC '.phpversion('apc').' Enabled</h4>';
            $panel .= round($mem_avail/1024/1024, 1).'M available, '.round($mem_used/1024/1024, 1).'M used'.$this->getLinebreak()
                    . $cache['num_entries'].' Files cached ('.round($cache['mem_size']/1024/1024, 1).'M)'.$this->getLinebreak()
                    . $cache['num_hits'].' Hits ('.round($cache['num_hits'] * 100 / ($cache['num_hits']+$cache['num_misses']), 1).'%)'.$this->getLinebreak()
                    . $cache['expunges'].' Expunges (cache full count)'; 
        }

        foreach ($this->_cacheBackends as $name => $backend) {
            $fillingPercentage = $backend->getFillingPercentage();
            $ids = $backend->getIds();
            
            # Print full class name, backends might be custom
            $panel .= '<h4>Cache '.$name.' ('.get_class($backend).')</h4>';
            $panel .= count($ids).' Entr'.(count($ids)>1?'ies':'y').''.$this->getLinebreak()
                    . 'Filling Percentage: '.$backend->getFillingPercentage().'%'.$this->getLinebreak();
            
            $cacheSize = 0;
            foreach ($ids as $id)
            {
                # Calculate valid cache size
                $mem_pre = memory_get_usage();
                if ($cached = $backend->load($id)) {
                    $mem_post = memory_get_usage();
                    $cacheSize += $mem_post-$mem_pre;
                    unset($cached);
                }                
            }
            $panel .= 'Valid Cache Size: '.round($cacheSize/1024, 1). 'K';
        }
        return $panel;
    }
}