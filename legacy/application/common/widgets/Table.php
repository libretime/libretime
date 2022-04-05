<?php
/**
 * Created by PhpStorm.
 * User: asantoni
 * Date: 11/09/15
 * Time: 2:47 PM.
 */
class AirtimeTableView
{
    private static function _getTableJavaScriptDependencies()
    {
        return ['js/airtime/widgets/table.js',
            'js/datatables/js/jquery.dataTables.js',
            'js/datatables/plugin/dataTables.pluginAPI.js',
            'js/datatables/plugin/dataTables.fnSetFilteringDelay.js',
            'js/datatables/plugin/dataTables.ColVis.js',
            'js/datatables/plugin/dataTables.colReorder.min.js?',
            'js/datatables/plugin/dataTables.FixedColumns.js',
            'js/datatables/plugin/dataTables.FixedHeader.js',
            'js/datatables/plugin/dataTables.columnFilter.js?', ];
    }

    public static function injectTableJavaScriptDependencies(&$headScript, $baseUrl, $airtimeVersion)
    {
        $deps = self::_getTableJavaScriptDependencies();
        for ($i = 0; $i < count($deps); ++$i) {
            $headScript->appendFile($baseUrl . $deps[$i] . '?' . $airtimeVersion, 'text/javascript');
        }
    }
}
