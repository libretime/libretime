<?php
class Application_Form_SmartPlaylist extends Zend_Form
{
    public function init(){
        
    }
    public function startForm($p_playlistId)
    {        
        // load type
        $out = CcPlaylistQuery::create()->findPk($p_playlistId);
        if ($out->getDbType() == "static") {
            $playlistType = 0;
        } else {
            $playlistType = 1;
        }
        
        $spType = new Zend_Form_Element_Radio('sp_type');
        $spType->setLabel('Set smart playlist type:')
               ->setDecorators(array('viewHelper'))
               ->setMultiOptions(array(
                    'static' => 'Static',
                    'dynamic' => 'Dynamic'
                ))
               ->setValue($playlistType);
        $this->addElement($spType);
        
        /*
        // load criteria from db
        $out = CcPlaylistcriteriaQuery::create()->findByDbPlaylistId($p_playlistId);
        */
        $storedCrit = array();
        foreach ($out as $crit) {
            $criteria = $crit->getDbCriteria();
            $modifier = $crit->getDbModifier();
            $value = $crit->getDbValue();
            $extra = $crit->getDbExtra();
        
            if($criteria == "limit"){
                $storedCrit["limit"] = array("value"=>$value, "modifier"=>$modifier);
            }else{
                $storedCrit["crit"][] = array("criteria"=>$criteria, "value"=>$value, "modifier"=>$modifier, "extra"=>$extra);
            }
        }
        $openSmartPlaylistOption = false;
        if (!empty($storedCrit)) {
            $openSmartPlaylistOption = true;
        }
        
        $save = new Zend_Form_Element_Button('save_button');
        $save->setAttrib('class', 'ui-button ui-state-default sp-button');
        $save->setAttrib('title', 'Save criteria only');
        $save->setIgnore(true);
        $save->setLabel('Save');
        $save->setDecorators(array('viewHelper'));
        $this->addElement($save);
        
        $generate = new Zend_Form_Element_Button('generate_button');
        $generate->setAttrib('class', 'ui-button ui-state-default sp-button');
        $generate->setAttrib('title', 'Save criteria and generate playlist content');
        $generate->setIgnore(true);
        $generate->setLabel('Generate');
        $generate->setDecorators(array('viewHelper'));
        $this->addElement($generate);
        
        $shuffle = new Zend_Form_Element_Button('shuffle_button');
        $shuffle->setAttrib('class', 'ui-button ui-state-default sp-button');
        $shuffle->setAttrib('title', 'Shuffle playlist content');
        $shuffle->setIgnore(true);
        $shuffle->setLabel('Shuffle');
        $shuffle->setDecorators(array('viewHelper'));
        $this->addElement($shuffle);
        
        $numOfSubForm = 3;
        for ($i=0;$i<$numOfSubForm;$i++) {
            $subform = new Application_Form_SmartPlaylistCriteriaSubForm();
            $subform->setCriteriaSetNumber($i);
            $subform->startForm($p_playlistId);
            $this->addSubForm($subform, 'sp_set_'.$i);
        }
        
        //getting playlist content candidate count that meets criteria
        $pl = new Application_Model_Playlist($p_playlistId);
        $files = $pl->getListofFilesMeetCriteria();
        
        $this->setDecorators(array(
                array('ViewScript', array('viewScript' => 'form/smart-playlist.phtml', "openOption"=> $openSmartPlaylistOption, "parent_form"=>$this, "numOfSubForm"=>$numOfSubForm))
        ));
    }
}