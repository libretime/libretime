<?php

class Zend_Controller_Plugin_ConversionTracking extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (!Zend_Session::isStarted()) {
            return;
        }

        //If user is a super admin and old plan level is set to trial....
        if (Application_Common_GoogleAnalytics::didPaidConversionOccur($request))
        {
            //Redirect to Thank you page, unless the request was already going there...
            if ($request->getControllerName() != 'thank-you')
            {
                $request->setModuleName('default')
                    ->setControllerName('thank-you')
                    ->setActionName('index')
                    ->setDispatched(true);
            }
        }
    }

}