<?php
/**
 *  _parseArr2Form
 *
 *  Add elements/rules/groups to an given HTML_QuickForm object
 *
 *  @param form object, reference to HTML_QuickForm object
 *  @param mask array, reference to array defining to form elements
 *  @param side string, side where the validation should beeing
 */
function parseArr2Form(&$form, $mask, $side='client')
{
    foreach($mask as $k=>$v) {
        ## add elements ########################
        if ($v['type']=='radio') {
            foreach($v['options'] as $rk=>$rv) {
                $radio[] =& $form->createElement($v['type'], NULL, NULL, $rv, $rk, $v['attributes']);
            }
            $form->addGroup($radio, $v['element'], tra($v['label']));
            unset($radio);
    
        } elseif ($v['type']=='select') {
            $elem[$v['element']] =& $form->createElement($v['type'], $v['element'], tra($v['label']), $v['options'], $v['attributes']);
            $elem[$v['element']]->setMultiple($v['multiple']);
            if (isset($v['selected'])) $elem[$v['element']]->setSelected($v['selected']);
            if (!$v['groupit'])        $form->addElement($elem[$v['element']]);
    
        } elseif ($v['type']=='date') {
            $elem[$v['element']] =& $form->createElement($v['type'], $v['element'], tra($v['label']), $v['options'], $v['attributes']);
            if (!$v['groupit'])     $form->addElement($elem[$v['element']]);
    
        } elseif ($v['type']=='checkbox' || $v['type']=='static') {
            $elem[$v['element']] =& $form->createElement($v['type'], $v['element'], tra($v['label']), $v['text'], $v['attributes']);
            if (!$v['groupit'])     $form->addElement($elem[$v['element']]);
    
        } elseif (isset($v['type'])) {
            $elem[$v['element']] =& $form->createElement($v['type'], $v['element'], tra($v['label']),
                                        ($v[type]=='text' || $v['type']=='file' || $v['type']=='password') ? array_merge(array('size'=>UI_INPUT_STANDARD_SIZE, 'maxlength'=>UI_INPUT_STANDARD_MAXLENGTH), $v['attributes']) :
                                        ($v['type']=='textarea' ? array_merge(array('rows'=>UI_TEXTAREA_STANDART_ROWS, 'cols'=>UI_TEXTAREA_STANDART_COLS), $v['attributes']) :
                                        ($v['type']=='button' || $v['type']=='submit' || $v['type']=='reset' ? array_merge(array('class'=>UI_BUTTON_STYLE), $v['attributes']) : $v['attributes']))
                                    );
            if (!$v['groupit'])     $form->addElement($elem[$v['element']]);
        }
        ## add required rule ###################
        if ($v['required']) {
            $form->addRule($v['element'], isset($v['requiredmsg']) ? tra($v['requiredmsg']) : tra('Missing value for $1', tra($v['label'])), 'required', NULL, $side);
        }
        ## add constant value ##################
        if (isset($v['constant'])) {
            $form->setConstants(array($v['element']=>$v['constant']));
        }
        ## add default value ###################
        if (isset($v['default'])) {
            $form->setDefaults(array($v['element']=>$v['default']));
        }
        ## add other rules #####################
        if ($v['rule']) {
            $form->addRule($v['element'], isset($v['rulemsg']) ? tra($v['rulemsg']) : tra('$1 must be $2', tra($v['element']), tra($v['rule'])), $v['rule'] ,$v['format'], $side);
        }
        ## add group ###########################
        if (is_array($v['group'])) {
            foreach($v['group'] as $val) {
                $groupthose[] =& $elem[$val];
            }
            $form->addGroup($groupthose, $v['name'], tra($v['label']), $v['seperator'], $v['appendName']);
            if ($v['rule']) {
                $form->addRule($v['name'], isset($v['rulemsg']) ? tra($v['rulemsg']) : tra('$1 must be $2', tra($v['name'])), $v['rule'], $v['format'], $side);
            }
            if ($v['grouprule']) {
                $form->addGroupRule($v['name'], $v['arg1'], $v['grouprule'], $v['format'], $v['howmany'], $side, $v['reset']);
            }
            unset($groupthose);
        }
        ## check error on type file ##########
        if ($v['type']=='file') {
            if ($_POST[$v['element']]['error']) {
                $form->setElementError($v['element'], isset($v['requiredmsg']) ? tra($v['requiredmsg']) : tra('Missing value for $1', tra($v['label'])));
            }
        }
    }
    
    $form->validate();
}

function addLanguageFormArr()
{
    return array(
        array(
            'element'   => 'action',
            'type'      => 'hidden',
            'constant'  => 'do_add_language'
        ),
        array(
            'element'   => 'Id',
            'type'      => 'text',
            'label'     => 'Id',
            'required'  => TRUE
        ),
        array(
            'element'   => 'Name',
            'type'      => 'text',
            'label'     => 'English name',
            'required'  => TRUE
        ), 
        array(
            'element'   => 'NativeName',
            'type'      => 'text',
            'label'     => 'Native Name',
            'required'  => TRUE
        ), 
        array(
            'element'   => 'Submit',
            'type'      => 'submit',
            'label'     => 'Submit',
            'groupit'   => TRUE
        ), 
        array(
            'element'   => 'Cancel',
            'type'      => 'button',
            'label'     => 'Cancel',
            'attributes' => array('onClick' => 'location.href="?action=list_languages"'),
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('Cancel', 'Submit')
        )
    );
}

function tra($str)
{
    return $str;
}
?>