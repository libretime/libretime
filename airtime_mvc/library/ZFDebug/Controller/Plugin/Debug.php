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
 * @see Zend_Controller_Exception
 */
require_once 'Zend/Controller/Exception.php';

/**
 * @see Zend_Version
 */
require_once 'Zend/Version.php';

/**
 * @see ZFDebug_Controller_Plugin_Debug_Plugin_Text
 */
require_once 'ZFDebug/Controller/Plugin/Debug/Plugin/Text.php';

/**
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License     New BSD License
 */
class ZFDebug_Controller_Plugin_Debug extends Zend_Controller_Plugin_Abstract
{
    /**
     * Contains registered plugins
     *
     * @var array
     */
    protected $_plugins = array();

    /**
     * Contains options to change Debug Bar behavior
     */
    protected $_options = array(
        'plugins'           => array(
            'Variables' => null,
            'Time' => null,
            'Memory' => null),
        'z-index'           => 255,
        'jquery_path'       => 'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js',
        'image_path'        => null
    );
    
    /**
     * Standard plugins
     *
     * @var array
     */
    public static $standardPlugins = array('Cache', 'Html', 'Database', 'Exception', 'File', 'Memory', 'Registry', 'Time', 'Variables');

    /**
     * Debug Bar Version Number
     * for internal use only
     *
     * @var string
     */
    protected $_version = '1.5.4';

    /**
     * Creates a new instance of the Debug Bar
     *
     * @param array|Zend_Config $options
     * @throws Zend_Controller_Exception
     * @return void
     */

    protected $_closingBracket = null;

    public function __construct($options = null)
    {
        if (isset($options)) {
            if ($options instanceof Zend_Config) {
                $options = $options->toArray();
            }

            /*
             * Verify that adapter parameters are in an array.
             */
            if (!is_array($options)) {
                throw new Zend_Exception('Debug parameters must be in an array or a Zend_Config object');
            }

            $this->setOptions($options);
        }
        
        /**
         * Creating ZF Version Tab always shown
         */
        $version = new ZFDebug_Controller_Plugin_Debug_Plugin_Text();
        $version->setPanel($this->_getVersionPanel())
                ->setTab($this->_getVersionTab())
                ->setIdentifier('copyright')
                ->setIconData('data:image/gif;base64,R0lGODlhEAAQAPcAAPb7/ef2+VepAGKzAIC8SavSiYS9Stvt0uTx4fX6+ur1632+QMLgrGOuApDIZO738drs0Ofz5t7v2MfjtPP6+t7v12SzAcvnyX2+PaPRhH2+Qmy3H3K5LPP6+cXkwIHAR2+4JHi7NePz8YC/Rc3ozfH49XK5KXq9OrzdpNzu1YrEUqrVkdzw5uTw4d/v2dDow5zOeO3279Hq0m+4JqrUhpnMbeHw3N3w6Mflwm22HmazBODy7tfu3un06r7gsuXy4sTisIzGXvH59ny9PdPr1rXZpMzlu36/Q5bLb+Pw3tDnxNHr1Lfbm+b199/x62q1Fp3NcdjszqTPh/L599vt04/GWmazCPb7/LHZnW63I3W6MXa7MmGuAt/y7Gq1E2m0Eb7cp9frzZLJaO/489bu3HW3N7rerN/v2q7WjIjEVuLx343FVrDXj9nt0cTjvW2zIoPBSNjv4OT09IXDUpvLeeHw3dPqyNLpxs/nwHe8OIvFWrPaoGe0C5zMb83mvHm8Oen06a3Xl9XqyoC/Qr/htWe0DofDU4nFWbPYk7ndqZ/PfYPBTMPhrqHRgoLBSujz55PKadHpxfX6+6LNeqPQfNXt2pPIYH2+O7vcoHi4OOf2+PL5+NTs2N3u1mi1E7XZl4zEVJjLaZHGauby5KTShmSzBO/38s/oz3i7MtbrzMHiuYTCT4fDTtXqye327uDv3JDHXu328JnMcu738LLanvD49ZTJYpPKauX19tvv44jBWo7GWpfKZ+Dv27XcpcrluXu8ONTs16zXleT08qfUjKzUlc7pzm63HaTRfZXKZuj06HG4KavViGe0EcDfqcjmxaDQgZrNdOHz77/ep4/HYL3esnW6LobCS3S5K57OctDp0JXKbez17N7x6cbkwLTZlbXXmLrcnrvdodHr06PQe8jkt5jIa93v13m8OI7CW3O6L3a7Nb7gs6nUjmu2GqjTgZjKaKLQeZnMc4LAReL08rTbopbLbuTx4KDOdtbry7DYmrvfrrPaoXK5K5zOegAAACH5BAEAAAAALAAAAAAQABAAAAhMAAEIHEiwoMGDCBMOlCKgoUMuHghInEiggEOHAC5eJNhQ4UAuAjwIJLCR4AEBDQS2uHiAYLGOHjNqlCmgYAONApQ0jBGzp8+fQH8GBAA7');
        $this->registerPlugin($version);

        /**
         * Loading aready defined plugins
         */
        $this->_loadPlugins();
    }
    
    /**
     * Sets options of the Debug Bar
     *
     * @param array $options
     * @return ZFDebug_Controller_Plugin_Debug
     */
    public function setOptions(array $options = array())
    {
        if (isset($options['jquery_path'])) {
            $this->_options['jquery_path'] = $options['jquery_path'];
        }

        if (isset($options['z-index'])) {
            $this->_options['z-index'] = $options['z-index'];
        }

        if (isset($options['image_path'])) {
            $this->_options['image_path'] = $options['image_path'];
        }
        
        if (isset($options['plugins'])) {
        	$this->_options['plugins'] = $options['plugins'];
        }
        return $this;
    }

    /**
     * Register a new plugin in the Debug Bar
     *
     * @param ZFDebug_Controller_Plugin_Debug_Plugin_Interface
     * @return ZFDebug_Controller_Plugin_Debug
     */
    public function registerPlugin(ZFDebug_Controller_Plugin_Debug_Plugin_Interface $plugin)
    {
        $this->_plugins[$plugin->getIdentifier()] = $plugin;
        return $this;
    }

    /**
     * Unregister a plugin in the Debug Bar
     *
     * @param string $plugin
     * @return ZFDebug_Controller_Plugin_Debug
     */
    public function unregisterPlugin($plugin)
    {
        if (false !== strpos($plugin, '_')) {
            foreach ($this->_plugins as $key => $_plugin) {
                if ($plugin == get_class($_plugin)) {
                    unset($this->_plugins[$key]);
                }
            }
        } else {
            $plugin = strtolower($plugin);
            if (isset($this->_plugins[$plugin])) {
                unset($this->_plugins[$plugin]);
            }
        }
        return $this;
    }
    
    /**
     * Get a registered plugin in the Debug Bar
     *
     * @param string $identifier
     * @return ZFDebug_Controller_Plugin_Debug_Plugin_Interface
     */
    public function getPlugin($identifier)
    {
        $identifier = strtolower($identifier);
        if (isset($this->_plugins[$identifier])) {
            return $this->_plugins[$identifier];
        }
        return false;
    }
    
    /**
     * Defined by Zend_Controller_Plugin_Abstract
     */
    public function dispatchLoopShutdown()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            return;
        }
        $disable = Zend_Controller_Front::getInstance()->getRequest()->getParam('ZFDEBUG_DISABLE');
        if (isset($disable)) {
            return;
        }
        
        $html = '';

        /**
         * Creating menu tab for all registered plugins
         */
        foreach ($this->_plugins as $plugin)
        {
            $panel = $plugin->getPanel();
            if ($panel == '') {
                continue;
            }

            /* @var $plugin ZFDebug_Controller_Plugin_Debug_Plugin_Interface */
            $html .= '<div id="ZFDebug_' . $plugin->getIdentifier()
                  . '" class="ZFDebug_panel">' . $panel . '</div>';
        }

        $html .= '<div id="ZFDebug_info">';

        /**
         * Creating panel content for all registered plugins
         */
        foreach ($this->_plugins as $plugin)
        {
            $tab = $plugin->getTab();
            if ($tab == '') {
                continue;
            }

            if (null !== $this->_options['image_path'] && file_exists($this->_options['image_path'] .'/'. $plugin->getIdentifier() .'.png')) {
                $plugin_icon = $this->_options['image_path'] .'/'. $plugin->getIdentifier() .'.png';
            } else {
                $plugin_icon = $plugin->getIconData();
            }

            /* @var $plugin ZFDebug_Controller_Plugin_Debug_Plugin_Interface */
            $html .= '<span class="ZFDebug_span clickable" onclick="ZFDebugPanel(\'ZFDebug_' . $plugin->getIdentifier() . '\');">';
            $html .= '<img src="' . $plugin_icon . '" style="vertical-align:middle" alt="' . $plugin->getIdentifier() . '" title="' . $plugin->getIdentifier() . '"'. $this->getClosingBracket() .' ';
            $html .= $tab . '</span>';
        }

        $html .= '<span class="ZFDebug_span ZFDebug_last clickable" id="ZFDebug_toggler" onclick="ZFDebugSlideBar()">&#171;</span>';

        $html .= '</div>';
        $this->_output($html);
    }

    ### INTERNAL METHODS BELOW ###

    /**
     * Load plugins set in config option
     *
     * @return void;
     */
    protected function _loadPlugins()
    {
    	foreach($this->_options['plugins'] as $plugin => $options) {
    	    if (is_numeric($plugin)) {
    	        # Plugin passed as array value instead of key
    	        $plugin = $options;
    	        $options = array();
    	    }
    	    
    	    // Register an instance
    	    if (is_object($plugin) && in_array('ZFDebug_Controller_Plugin_Debug_Plugin_Interface', class_implements($plugin))) {
    	        $this->registerPlugin($plugin);
    	        continue;
    	    }
    	    
    	    if (!is_string($plugin)) {
    	        throw new Exception("Invalid plugin name", 1);
    	    }
    	    $plugin = ucfirst($plugin);
    	    
    	    // Register a classname
    	    if (in_array($plugin, ZFDebug_Controller_Plugin_Debug::$standardPlugins)) {
    	        // standard plugin
                $pluginClass = 'ZFDebug_Controller_Plugin_Debug_Plugin_' . $plugin;
    	    } else {
    	        // we use a custom plugin
                if (!preg_match('~^[\w]+$~D', $plugin)) {
                    throw new Zend_Exception("ZFDebug: Invalid plugin name [$plugin]");
                }
                $pluginClass = $plugin;
            }

            require_once str_replace('_', DIRECTORY_SEPARATOR, $pluginClass) . '.php';
            $object = new $pluginClass($options);
    		$this->registerPlugin($object);
    	}
    }

    /**
     * Return version tab
     *
     * @return string
     */
    protected function _getVersionTab()
    {
        return ' ' . Zend_Version::VERSION . '/'.phpversion();
    }

    /**
     * Returns version panel
     *
     * @return string
     */
    protected function _getVersionPanel()
    {
        $panel = '<h4>ZFDebug v'.$this->_version.'</h4>' .
                 '<p>©2008-2009 <a href="http://jokke.dk">Joakim Nygård</a> &amp; <a href="http://www.bangal.de">Andreas Pankratz</a></p>' .
                 '<p>The project is hosted at <a href="http://code.google.com/p/zfdebug/">http://zfdebug.googlecode.com</a> and released under the BSD License' . $this->getLinebreak() .
                 'Includes images from the <a href="http://www.famfamfam.com/lab/icons/silk/">Silk Icon set</a> by Mark James</p>'.
                 '<p>Disable ZFDebug temporarily by sending ZFDEBUG_DISABLE as a GET/POST parameter</p>';
        // $panel .= '<h4>Zend Framework '.Zend_Version::VERSION.' / PHP '.phpversion().' with extensions:</h4>';
        // $extensions = get_loaded_extensions();
        // natcasesort($extensions);
        // $panel .= implode('<br>', $extensions);
        return $panel;
    }

    /**
     * Returns path to the specific icon
     *
     * @return string
     */
    protected function _icon($kind)
    {
        switch ($kind) {
            case 'database':
                if (null === $this->_options['image_path'])
                    return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAEYSURBVBgZBcHPio5hGAfg6/2+R980k6wmJgsJ5U/ZOAqbSc2GnXOwUg7BESgLUeIQ1GSjLFnMwsKGGg1qxJRmPM97/1zXFAAAAEADdlfZzr26miup2svnelq7d2aYgt3rebl585wN6+K3I1/9fJe7O/uIePP2SypJkiRJ0vMhr55FLCA3zgIAOK9uQ4MS361ZOSX+OrTvkgINSjS/HIvhjxNNFGgQsbSmabohKDNoUGLohsls6BaiQIMSs2FYmnXdUsygQYmumy3Nhi6igwalDEOJEjPKP7CA2aFNK8Bkyy3fdNCg7r9/fW3jgpVJbDmy5+PB2IYp4MXFelQ7izPrhkPHB+P5/PjhD5gCgCenx+VR/dODEwD+A3T7nqbxwf1HAAAAAElFTkSuQmCC';

                return $this->_options['image_path'] . '/database.png';
                break;
            case 'exception':
                if (null === $this->_options['image_path'])
    				return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAJPSURBVDjLpZPLS5RhFMYfv9QJlelTQZwRb2OKlKuINuHGLlBEBEOLxAu46oL0F0QQFdWizUCrWnjBaDHgThCMoiKkhUONTqmjmDp2GZ0UnWbmfc/ztrC+GbM2dXbv4ZzfeQ7vefKMMfifyP89IbevNNCYdkN2kawkCZKfSPZTOGTf6Y/m1uflKlC3LvsNTWArr9BT2LAf+W73dn5jHclIBFZyfYWU3or7T4K7AJmbl/yG7EtX1BQXNTVCYgtgbAEAYHlqYHlrsTEVQWr63RZFuqsfDAcdQPrGRR/JF5nKGm9xUxMyr0YBAEXXHgIANq/3ADQobD2J9fAkNiMTMSFb9z8ambMAQER3JC1XttkYGGZXoyZEGyTHRuBuPgBTUu7VSnUAgAUAWutOV2MjZGkehgYUA6O5A0AlkAyRnotiX3MLlFKduYCqAtuGXpyH0XQmOj+TIURt51OzURTYZdBKV2UBSsOIcRp/TVTT4ewK6idECAihtUKOArWcjq/B8tQ6UkUR31+OYXP4sTOdisivrkMyHodWejlXwcC38Fvs8dY5xaIId89VlJy7ACpCNCFCuOp8+BJ6A631gANQSg1mVmOxxGQYRW2nHMha4B5WA3chsv22T5/B13AIicWZmNZ6cMchTXUe81Okzz54pLi0uQWp+TmkZqMwxsBV74Or3od4OISPr0e3SHa3PX0f3HXKofNH/UIG9pZ5PeUth+CyS2EMkEqs4fPEOBJLsyske48/+xD8oxcAYPzs4QaS7RR2kbLTTOTQieczfzfTv8QPldGvTGoF6/8AAAAASUVORK5CYII=';

                return $this->_options['image_path'] . '/exception.png';
                 break;
            case 'error':
                if (null === $this->_options['image_path'])
    			    return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAIsSURBVDjLpVNLSJQBEP7+h6uu62vLVAJDW1KQTMrINQ1vPQzq1GOpa9EppGOHLh0kCEKL7JBEhVCHihAsESyJiE4FWShGRmauu7KYiv6Pma+DGoFrBQ7MzGFmPr5vmDFIYj1mr1WYfrHPovA9VVOqbC7e/1rS9ZlrAVDYHig5WB0oPtBI0TNrUiC5yhP9jeF4X8NPcWfopoY48XT39PjjXeF0vWkZqOjd7LJYrmGasHPCCJbHwhS9/F8M4s8baid764Xi0Ilfp5voorpJfn2wwx/r3l77TwZUvR+qajXVn8PnvocYfXYH6k2ioOaCpaIdf11ivDcayyiMVudsOYqFb60gARJYHG9DbqQFmSVNjaO3K2NpAeK90ZCqtgcrjkP9aUCXp0moetDFEeRXnYCKXhm+uTW0CkBFu4JlxzZkFlbASz4CQGQVBFeEwZm8geyiMuRVntzsL3oXV+YMkvjRsydC1U+lhwZsWXgHb+oWVAEzIwvzyVlk5igsi7DymmHlHsFQR50rjl+981Jy1Fw6Gu0ObTtnU+cgs28AKgDiy+Awpj5OACBAhZ/qh2HOo6i+NeA73jUAML4/qWux8mt6NjW1w599CS9xb0mSEqQBEDAtwqALUmBaG5FV3oYPnTHMjAwetlWksyByaukxQg2wQ9FlccaK/OXA3/uAEUDp3rNIDQ1ctSk6kHh1/jRFoaL4M4snEMeD73gQx4M4PsT1IZ5AfYH68tZY7zv/ApRMY9mnuVMvAAAAAElFTkSuQmCC';

                return $this->_options['image_path'] . '/error.png';
                break;
            default:
                if (null === $this->_options['image_path'])
                    return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAHhSURBVDjLpZI9SJVxFMZ/r2YFflw/kcQsiJt5b1ije0tDtbQ3GtFQYwVNFbQ1ujRFa1MUJKQ4VhYqd7K4gopK3UIly+57nnMaXjHjqotnOfDnnOd/nt85SURwkDi02+ODqbsldxUlD0mvHw09ubSXQF1t8512nGJ/Uz/5lnxi0tB+E9QI3D//+EfVqhtppGxUNzCzmf0Ekojg4fS9cBeSoyzHQNuZxNyYXp5ZM5Mk1ZkZT688b6thIBenG/N4OB5B4InciYBCVyGnEBHO+/LH3SFKQuF4OEs/51ndXMXC8Ajqknrcg1O5PGa2h4CJUqVES0OO7sYevv2qoFBmJ/4gF4boaOrg6rPLYWaYiVfDo0my8w5uj12PQleB0vcp5I6HsHAUoqUhR29zH+5B4IxNTvDmxljy3x2YCYUwZVlbzXJh9UKeQY6t2m0Lt94Oh5loPdqK3EkjzZi4MM/Y9Db3MTv/mYWVxaqkw9IOATNR7B5ABHPrZQrtg9sb8XDKa1+QOwsri4zeHD9SAzE1wxBTXz9xtvMc5ZU5lirLSKIz18nJnhOZjb22YKkhd4odg5icpcoyL669TAAujlyIvmPHSWXY1ti1AmZ8mJ3ElP1ips1/YM3H300g+W+51nc95YPEX8fEbdA2ReVYAAAAAElFTkSuQmCC';

                return $this->_options['image_path'] . '/unknown.png';
                break;
        }
    }

    /**
     * Returns html header for the Debug Bar
     *
     * @return string
     */
    protected function _headerOutput() {
        $collapsed = isset($_COOKIE['ZFDebugCollapsed']) ? $_COOKIE['ZFDebugCollapsed'] : 0;

        return ('
            <style type="text/css" media="screen">
                #ZFDebug_debug { font: 11px/1.4em Lucida Grande, Lucida Sans Unicode, sans-serif; position:fixed; bottom:5px; left:5px; color:#000; z-index: ' . $this->_options['z-index'] . ';}
                #ZFDebug_debug ol {margin:10px 0px; padding:0 25px}
                #ZFDebug_debug li {margin:0 0 10px 0;}
                #ZFDebug_debug .clickable {cursor:pointer}
                #ZFDebug_toggler { font-weight:bold; background:#BFBFBF; }
                .ZFDebug_span { border: 1px solid #999; border-right:0px; background:#DFDFDF; padding: 5px 5px; }
                .ZFDebug_last { border: 1px solid #999; }
                .ZFDebug_panel { text-align:left; position:absolute;bottom:21px;width:800px; max-height:400px; overflow:auto; display:none; background:#E8E8E8; padding:5px; border: 1px solid #999; }
                .ZFDebug_panel .pre {font: 11px/1.4em Monaco, Lucida Console, monospace; margin:0 0 0 22px}
                #ZFDebug_exception { border:1px solid #CD0A0A;display: block; }
            </style>
            <script type="text/javascript">
                if (typeof jQuery == "undefined") {
                    var scriptObj = document.createElement("script");
                    scriptObj.src = "'.$this->_options['jquery_path'].'";
                    scriptObj.type = "text/javascript";
                    var head=document.getElementsByTagName("head")[0];
                    head.insertBefore(scriptObj,head.firstChild);
                    jQuery.noConflict();
                }

                var ZFDebugLoad = window.onload;
                window.onload = function(){
                    if (ZFDebugLoad) {
                        ZFDebugLoad();
                    }
                    ZFDebugCollapsed();
                };
                
                function ZFDebugCollapsed() {
                    if ('.$collapsed.' == 1) {
                        ZFDebugPanel();
                        jQuery("#ZFDebug_toggler").html("&#187;");
                        return jQuery("#ZFDebug_debug").css("left", "-"+parseInt(jQuery("#ZFDebug_debug").outerWidth()-jQuery("#ZFDebug_toggler").outerWidth()+1)+"px");
                    }
                }
                
                function ZFDebugPanel(name) {
                    jQuery(".ZFDebug_panel").each(function(i){
                        if(jQuery(this).css("display") == "block") {
                            jQuery(this).slideUp();
                        } else {
                            if (jQuery(this).attr("id") == name)
                                jQuery(this).slideDown();
                            else
                                jQuery(this).slideUp();
                        }
                    });
                }

                function ZFDebugSlideBar() {
                    if (jQuery("#ZFDebug_debug").position().left > 0) {
                        document.cookie = "ZFDebugCollapsed=1;expires=;path=/";
                        ZFDebugPanel();
                        jQuery("#ZFDebug_toggler").html("&#187;");
                        return jQuery("#ZFDebug_debug").animate({left:"-"+parseInt(jQuery("#ZFDebug_debug").outerWidth()-jQuery("#ZFDebug_toggler").outerWidth()+1)+"px"}, "normal", "swing");
                    } else {
                        document.cookie = "ZFDebugCollapsed=0;expires=;path=/";
                        jQuery("#ZFDebug_toggler").html("&#171;");
                        return jQuery("#ZFDebug_debug").animate({left:"5px"}, "normal", "swing");
                    }
                }

                function ZFDebugToggleElement(name, whenHidden, whenVisible){
                    if(jQuery(name).css("display")=="none"){
                        jQuery(whenVisible).show();
                        jQuery(whenHidden).hide();
                    } else {
                        jQuery(whenVisible).hide();
                        jQuery(whenHidden).show();
                    }
                    jQuery(name).slideToggle();
                }
            </script>');
    }

    /**
     * Appends Debug Bar html output to the original page
     *
     * @param string $html
     * @return void
     */
    protected function _output($html)
    {
        $response = $this->getResponse();
        $response->setBody(preg_replace('/(<\/head>)/i', $this->_headerOutput() . '$1', $response->getBody()));
        $response->setBody(str_ireplace('</body>', '<div id="ZFDebug_debug">'.$html.'</div></body>', $response->getBody()));
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
        if ($view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view) {
            $doctype = $view->doctype();
            return $doctype->isXhtml();
        }
        return false;
    }
}