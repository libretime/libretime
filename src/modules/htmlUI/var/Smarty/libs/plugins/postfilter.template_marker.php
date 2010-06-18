<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     postfilter.template_marker.php
 * Type:     postfilter
 * Name:     template_marker
 * Version:  1.0
 * Date:     March, 2003
 * Purpose:  Mark the Template begin and end
 * Install:  Drop into the plugin directory, call
 *           $smarty->load_filter('post','template_marker');
 *           from application.
 * Author:   Erik Seifert <shaggy@gmx.at>
 * -------------------------------------------------------------
 */
 function smarty_postfilter_template_marker($compiled, &$smarty)
 {
    return '<!-- BEGIN : ' . $smarty->_current_file . ' -->' . $compiled . '<!-- END : ' . $smarty->_current_file . ' -->';
 }
?>