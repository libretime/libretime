<?php
class uiBrowse
{
    function uiBrowse(&$uiBase)
    {
        $this->Base       =& $uiBase;
        $this->prefix     = 'BROWSE';
        $this->col        =& $_SESSION[constant('UI_'.$this->prefix.'_SESSNAME')]['col'];
        $this->criteria   =& $_SESSION[constant('UI_'.$this->prefix.'_SESSNAME')]['criteria'];
        #$this->results    =& $_SESSION[constant('UI_'.$this->prefix.'_SESSNAME')]['results'];
        $this->reloadUrl  = UI_BROWSER.'?popup[]=_reload_parent&popup[]=_close';

        if (empty($this->criteria['limit']))     $this->criteria['limit']    = UI_BROWSE_DEFAULT_LIMIT;
        if (empty($this->criteria['filetype']))  $this->criteria['filetype'] = UI_FILETYPE_ANY;

        if (!is_array($this->col)) {
            ## init Categorys
            $this->setDefaults();
        }
    }

    function setReload()
    {
        $this->Base->redirUrl = $this->reloadUrl;
    }

    function setDefaults($reload=FALSE)
    {
        $this->col[1]['category'] = UI_BROWSE_DEFAULT_KEY_1;
        $this->col[1]['value'][0] = '%%all%%';
        $this->col[2]['category'] = UI_BROWSE_DEFAULT_KEY_2;
        $this->col[2]['value'][0] = '%%all%%';
        $this->col[3]['category'] = UI_BROWSE_DEFAULT_KEY_3;
        $this->col[3]['value'][0] = '%%all%%';
        
        for ($col=1; $col<=3; $col++) {
            $this->setCategory(array('col' => $col, 'category' => $this->col[$col]['category'], 'value' => array(0 => '%%all%%')));


            $this->setValue(
                array('col'      => $col,
                      'category' => $this->col[$col]['category'],
                      'value'    => $this->col[$col]['value']
                )
            );
        }

        if ($reload === TRUE) $this->setReload();
    }

    function getCriteria()
    {
        return $this->criteria;
    }


    function getResult()
    {   
        $this->searchDB();
        return $this->results;
    }


    function browseForm($id, $mask2)
    {   
        include dirname(__FILE__).'/formmask/metadata.inc.php';
        #$mask2['browse_columns']['category']['options'][0] = tra('Select a Value');
        foreach ($mask['pages'] as $key=>$val) {
            foreach ($mask['pages'][$key] as $v){
                if ($v['type']) $mask2['browse_columns']['category']['options'][$this->Base->_formElementEncode($v['element'])] = tra($v['label']);
            }
        };

        for($n=1; $n<=3; $n++) {
            $form = new HTML_QuickForm('col'.$n, UI_STANDARD_FORM_METHOD, UI_HANDLER);
            $form->setConstants(array('id' => $id, 'col' => $n, 'category' => $this->Base->_formElementEncode($this->col[$n]['category'])));
            $mask2['browse_columns']['value']['options'] = $this->options($this->col[$n]['values']['results']);
            $mask2['browse_columns']['value']['default'] = $this->col[$n]['form_value'];
            $this->Base->_parseArr2Form($form, $mask2['browse_columns']);
            $form->validate();
            $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
            $form->accept($renderer);
            $output['col'.$n]['dynform'] = $renderer->toArray();
        }

        ## form to change limit and file-type
        $form = new HTML_QuickForm('switch', UI_STANDARD_FORM_METHOD, UI_HANDLER);
        $this->Base->_parseArr2Form($form, $mask2['browse_global']);
        $form->setDefaults(array('limit'    => $this->criteria['limit'],
                                 'filetype' => $this->criteria['filetype']));
        $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
        $form->accept($renderer);
        $output['global']['dynform'] = $renderer->toArray();

        return $output;
    }


    function setCategory($parm)
    {
        $which = $parm['col'];
        $this->col[$which]['category'] = $this->Base->_formElementDecode($parm['category']);
        $this->col[$which]['values']   = $this->Base->gb->browseCategory($this->col[$which]['category'], $this->col[$which]['criteria'], $this->Base->sessid);

        $this->Base->redirUrl = UI_BROWSER.'?act='.$this->prefix;

        $this->clearHierarchy($which);
        #print_r($this->col);
    }

    function setValue($parm)
    {
        $this->criteria['offset'] = 0;
        $which = $parm['col'];
        $next  = $which + 1;
        $this->col[$which]['form_value'] =  $parm['value'][0];
        if ($parm['value'][0] == '%%all%%') { 
            $this->col[$next]['criteria'] = array('operator' => 'and', 'filetype' => $this->criteria['filetype']);
        } else { 
            $this->col[$next]['criteria'] = array(
                'operator' => 'and',
                'filetype' => $this->criteria['filetype'],
                'conditions'  => array_merge(
                    is_array($this->col[$which]['criteria']['conditions']) ? $this->col[$which]['criteria']['conditions'] : array(),
                    array(
                        array('cat'  => $this->Base->_formElementDecode($parm['category']),
                              'op'   => '=',
                              'val'  => $parm['value'][0]
                        )
                    )
                )
            );
        }
        $this->col[$next]['values'] = $this->Base->gb->browseCategory($this->col[$next]['category'], $this->col[$next]['criteria'], $this->Base->sessid);

        #echo "cat: ".$this->col[$next]['category']."\n";
        #echo "criteria: "; print_r($this->col[$next]['criteria']);
        #echo "\nvalues: "; print_r($this->col[$next]['values']);

        $this->setCriteria();
        $this->clearHierarchy($next);
        #$this->searchDB();
        $this->Base->redirUrl = UI_BROWSER.'?act='.$this->prefix;
        #$this->setReload();
    }


    function options($arr)
    {   
        $ret['%%all%%'] = '---all---';
        if (is_array($arr)) {
            foreach ($arr as $val)
                $ret[$val]  = $val;
        }

        return $ret;
    }

    function clearHierarchy($which)
    {
        $this->col[$which]['form_value'] = NULL;
        $which++;
        for ($col=$which; $col<=3; $col++) {
            $this->col[$col]['criteria']    = NULL;
            $this->col[$col]['values']      = NULL;
            $this->col[$col]['form_value']  = NULL;
        }
    }
    
    function setCriteria() {
        //$this->criteria['conditions'] = array();
        unset($this->criteria['conditions']);
        for($col=3; $col>=1; $col--) {
            if (is_array($this->col[$col]['criteria'])) {
                $this->criteria = array_merge ($this->col[$col]['criteria'], $this->criteria);
                break;
            }
        }
    }


    function searchDB()
    {
        $this->results = array('page' => $this->criteria['offset']/$this->criteria['limit']);

        $results = $this->Base->gb->localSearch($this->criteria, $this->Base->sessid);
        
        if (!is_array($results) || !count($results)) {
            return false;    
        }
        
        $this->results['cnt'] = $results['cnt'];
        foreach ($results['results'] as $rec) {
            $tmpId = $this->Base->gb->_idFromGunid($rec["gunid"]);
            $this->results['items'][] = $this->Base->_getMetaInfo($tmpId);
        }

        /*
        ## test
        for ($n=0; $n<=$this->criteria['limit']; $n++) {
            $this->results['items'][] = Array
                (
                    'id' => 24,
                    'gunid' => '1cc472228d0cb2ac',
                    'title' => 'Item '.$n,
                    'creator' => 'Sebastian',
                    'duration' => '&nbsp;&nbsp;&nbsp;10:00',
                    'type' => 'webstream'
                );
        }
        $results['cnt'] = 500;
        $this->results['cnt'] = $results['cnt'];
        ## end test
        */

        $this->pagination($results);
        #print_r($this->criteria);
        #print_r($this->results);
        return TRUE;
    }


    function pagination(&$results)
    {
        if (sizeof($this->results['items']) == 0) {
            return FALSE;
        }
        $delta = 4;
        $currp = ($this->criteria['offset'] / $this->criteria['limit']) + 1;   # current page
        $maxp  = ceil($results['cnt'] / $this->criteria['limit']);           # maximum page

        /*
        for ($n = 1; $n <= $maxp; $n = $n+$width) {
            $width = pow(10, floor(($n)/10));
            $this->results['pagination'][$n] = $n;
        }
        */

        $deltaLower = UI_BROWSERESULTS_DELTA;
        $deltaUpper = UI_BROWSERESULTS_DELTA;
        $start = $currp;

        if ($start+$delta-$maxp > 0) $deltaLower += $start+$delta-$maxp;  ## correct lower boarder if page is near end

        for ($n = $start-$deltaLower; $n <= $start+$deltaUpper; $n++) {
            if ($n <= 0)            $deltaUpper++;                        ## correct upper boarder if page is near zero
            elseif ($n <= $maxp)    $this->results['pagination'][$n] = $n;
        }

        #array_pop($this->results['pagination']);
        $this->results['pagination'][1] ? NULL : $this->results['pagination'][1] = '|<<';
        $this->results['pagination'][$maxp] ? NULL : $this->results['pagination'][$maxp] = '>>|';
        $this->results['next']  = $results['cnt'] > $this->criteria['offset'] + $this->criteria['limit'] ? TRUE : FALSE;
        $this->results['prev']  = $this->criteria['offset'] > 0 ? TRUE : FALSE;
        ksort($this->results['pagination']);
    }


    function reOrder($by)
    {
        $this->criteria['offset'] = NULL;

        if ($this->criteria['orderby'] == $by && !$this->criteria['desc'])
            $this->criteria['desc'] = TRUE;
        else
            $this->criteria['desc'] = FALSE;

        $this->criteria['orderby'] = $by;
        $this->setReload();
        #$this->searchDB();
    }


    function setOffset($page)
    {
        $o =& $this->criteria['offset'];
        $l =& $this->criteria['limit'];

        if ($page == 'next') {
            $o += $l;
        } elseif ($page == 'prev') {
            $o -= $l;
        } elseif (is_numeric($page)) {
            $o = $l * ($page-1);
        }
        $this->setReload();
        #$this->searchDB();
    }

    function setLimit($limit)
    {   
        $this->criteria['limit'] = $limit;
        $this->setReload();
        #$this->searchDB();
    }

    function setFiletype($filetype)
    {  
        $this->criteria['filetype'] = $filetype; 
        $this->criteria['offset'] = 0;
        
        for ($n=1; $n<=3; $n++) {
             $this->col[$n]['criteria']['filetype'] = $filetype;  
             
            $this->col[$n]['values'] = $this->Base->gb->browseCategory($this->col[$n]['category'], $this->col[$n]['criteria'], $this->Base->sessid);
            $this->clearHierarchy($n);
        }
        
        $this->setReload();
        #$this->searchDB();
    }
}
?>