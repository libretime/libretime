<?php

class SetupController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('setup-language-timezone', 'json');
    }

    public function setupLanguageTimezoneAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        $form = new Application_Form_SetupLanguageTimezone();

        if ($request->isPost()) {

            $postData = $request->getPost();
            $formData = array();
            foreach ($postData["data"] as $key => $value) {
                if ($value["name"] == "csrf") continue;
                $formData[$value["name"]] = $value["value"];
            }
            if ($form->isValid($formData)) {
                $userService = new Application_Service_UserService();
                $currentUser = $userService->getCurrentUser();
                $currentUserId = $currentUser->getDbId();
                
                Application_Model_Preference::SetUserTimezone($formData["timezone"], $currentUserId);
                Application_Model_Preference::SetDefaultTimezone($formData["timezone"]);

                Application_Model_Preference::SetUserLocale($formData["language"], $currentUserId);
                Application_Model_Preference::SetDefaultLocale($formData["language"]);

                Application_Model_Preference::setLangTimezoneSetupComplete(true);

                $this->_helper->json->sendJson(null);
            } else {
                $this->_helper->json->sendJson($form->get);
            }
        } else {
            $this->_helper->json->sendJson($form);
        }
    }
}