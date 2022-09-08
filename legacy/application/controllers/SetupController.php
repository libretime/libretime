<?php

/** This class displays the Language and Timezone setup popup dialog that you see on first run. */
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
            $formData = $request->getPost();
            if ($form->isValid($formData)) {
                $userService = new Application_Service_UserService();
                $currentUser = $userService->getCurrentUser();
                $currentUserId = $currentUser->getDbId();

                Application_Model_Preference::SetUserTimezone($formData['setup_timezone'], $currentUserId);

                Application_Model_Preference::SetUserLocale($formData['setup_language'], $currentUserId);
                Application_Model_Preference::SetDefaultLocale($formData['setup_language']);

                Application_Model_Preference::setLangTimezoneSetupComplete(true);

                $this->_redirect('/showbuilder');
            }
        }
        $this->_redirect('/showbuilder');
    }
}
