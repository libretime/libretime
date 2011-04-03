<?php

class PreferenceController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->statusMsg = "";
        
        $form = new Application_Form_Preferences();
        $this->view->form = $form;
    }

    public function updateAction()
    {
        $request = $this->getRequest();
        if (!$this->getRequest()->isPost()) {
            return $this->_forward('Preference/index');
        }
      
        $form = new Application_Form_Preferences();
        if ($form->isValid($request->getPost())) {

            $values = $form->getValues();
            
            Application_Model_Preference::SetHeadTitle($values["preferences_general"]["stationName"], $this->view); 
            Application_Model_Preference::SetDefaultFade($values["preferences_general"]["stationDefaultFade"]);                      
            Application_Model_Preference::SetStreamLabelFormat($values["preferences_general"]["streamFormat"]);
            Application_Model_Preference::SetAllow3rdPartyApi($values["preferences_general"]["thirdPartyApi"]);

            Application_Model_Preference::SetDoSoundCloudUpload($values["preferences_soundcloud"]["UseSoundCloud"]);  
            Application_Model_Preference::SetSoundCloudUser($values["preferences_soundcloud"]["SoundCloudUser"]);
            Application_Model_Preference::SetSoundCloudPassword($values["preferences_soundcloud"]["SoundCloudPassword"]); 
            Application_Model_Preference::SetSoundCloudTags($values["preferences_soundcloud"]["SoundCloudTags"]);
            Application_Model_Preference::SetSoundCloudGenre($values["preferences_soundcloud"]["SoundCloudGenre"]);
            Application_Model_Preference::SetSoundCloudTrackType($values["preferences_soundcloud"]["SoundCloudTrackType"]);
            Application_Model_Preference::SetSoundCloudLicense($values["preferences_soundcloud"]["SoundCloudLicense"]);                       
            
            $this->view->statusMsg = "<div class='success'>Preferences updated.</div>";
        }
                  
        $this->view->form = $form;
        return $this->render('index'); //render the phtml file
    }
}



