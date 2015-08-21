<?php

class EmbeddableWidgetsController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function playerAction()
    {
        Zend_Layout::getMvcInstance()->assign('parent_page', 'Widgets');

        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $this->view->headLink()->appendStylesheet($baseUrl.'css/player-form.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/player/player.js?'.$CC_CONFIG['airtime_version']);

        $form = new Application_Form_Player();

        $apiEnabled = Application_Model_Preference::GetAllow3rdPartyApi();
        $numEnabledStreams = $form->getElement('player_stream_url')->getAttrib('numberOfEnabledStreams');

        if ($numEnabledStreams > 0 && $apiEnabled) {
            $this->view->player_form = $form;
        } else {
            $this->view->player_error_msg = _("To configure and use the embeddable player you must:<br><br>
            1. Enable at least one MP3, AAC, or OGG stream under System -> Streams<br>
            2. Enable the Public Airtime API under System -> Preferences");
        }
    }

    public function scheduleAction()
    {
        Zend_Layout::getMvcInstance()->assign('parent_page', 'Widgets');

        $apiEnabled = Application_Model_Preference::GetAllow3rdPartyApi();

        if (!$apiEnabled) {
            $this->view->weekly_schedule_error_msg = _("To use the embeddable weekly schedule widget you must:<br><br>
            Enable the Public Airtime API under System -> Preferences");
        }
    }
}

