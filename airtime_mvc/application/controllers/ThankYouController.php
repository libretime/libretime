<?php

class ThankYouController extends Zend_Controller_Action
{
    public function indexAction()
    {
        //Variable for the template (thank-you/index.phtml)
        $this->view->stationUrl = Application_Common_HTTPHelper::getStationUrl();
        $this->view->conversionUrl  = Application_Common_HTTPHelper::getStationUrl() . 'thank-you/confirm-conversion';
        $this->view->gaEventTrackingJsCode = ""; //Google Analytics event tracking code that logs an event.

        // Embed the Google Analytics conversion tracking code if the
        // user is a super admin and old plan level is set to trial.
        if (Application_Common_GoogleAnalytics::didPaidConversionOccur($this->getRequest())) {
            $this->view->gaEventTrackingJsCode = Application_Common_GoogleAnalytics::generateConversionTrackingJavaScript();
        }

        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        $csrf_element = new Zend_Form_Element_Hidden('csrf');
        $csrf_element->setValue($csrf_namespace->authtoken)->setRequired('true')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $csrf_form = new Zend_Form();
        $csrf_form->addElement($csrf_element);
        $this->view->form = $csrf_form;
    }

    /** Confirm that a conversion was tracked. */
    public function confirmConversionAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $current_namespace = new Zend_Session_Namespace('csrf_namespace');
        $observed_csrf_token = $this->_getParam('csrf_token');
        $expected_csrf_token = $current_namespace->authtoken;

        if($observed_csrf_token != $expected_csrf_token) {
            Logging::info("Invalid CSRF token");
            return;
        }

        if ($this->getRequest()->isPost()) {
            Logging::info("Goal conversion from trial to paid.");
            // Clear old plan level so we prevent duplicate events.
            // This should only be called from AJAX. See thank-you/index.phtml
            Application_Model_Preference::ClearOldPlanLevel();
        }
    }
}