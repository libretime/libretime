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
            
            $this->view->statusMsg = "Preferences Updated.";
        }
                  
        $this->view->form = $form;
        return $this->render('index'); //render the phtml file
    }
}



