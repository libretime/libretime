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
            Application_Model_Preference::SetHeadTitle($values["stationName"], $this->view); 
            Application_Model_Preference::SetDefaultFade($values["stationDefaultFade"]);                      
            Application_Model_Preference::SetStreamLabelFormat($values["streamFormat"]);
            Application_Model_Preference::SetAllow3rdPartyApi($values["thirdPartyApi"]);
            Application_Model_Preference::SetDoSoundCloudUpload($values["UseSoundCloud"]);  
            Application_Model_Preference::SetSoundCloudUser($values["SoundCloudUser"]);
            Application_Model_Preference::SetSoundCloudPassword($values["SoundCloudPassword"]); 
            Application_Model_Preference::SetSoundCloudTags($values["SoundCloudTags"]);
            Application_Model_Preference::SetSoundCloudGenre($values["SoundCloudGenre"]);
            Application_Model_Preference::SetSoundCloudTrackType($values["SoundCloudTrackType"]);
            Application_Model_Preference::SetSoundCloudLicense($values["SoundCloudLicense"]);                       
            
            $this->view->statusMsg = "<div class='success'>Preferences updated.</div>";
        }
                  
        $this->view->form = $form;
        return $this->render('index'); //render the phtml file
    }
}



