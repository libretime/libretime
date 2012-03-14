<?php

class DashboardController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('switch-source', 'json')
                    ->addActionContext('disconnect-source', 'json')
                    ->initContext();
    }

    public function indexAction()
    {
        // action body
    }
    
    public function disconnectSourceAction(){
        $request = $this->getRequest();
        
        $sourcename = $request->getParam('sourcename');
        $data = array("sourcename"=>$sourcename);
        Application_Model_RabbitMq::SendMessageToPypo("disconnect_source", $data);
    }
    
    public function switchSourceAction(){
        $request = $this->getRequest();
        
        $sourcename = $this->_getParam('sourcename');
        $current_status = $this->_getParam('status');
        $change_status_to = "on";
        
        if(strtolower($current_status) == "on"){
            $change_status_to = "off";
        }
        
        $data = array("sourcename"=>$sourcename, "status"=>$change_status_to);
        Application_Model_RabbitMq::SendMessageToPypo("switch_source", $data);
        if(strtolower($current_status) == "on"){
            Application_Model_Preference::SetSourceSwitchStatus($sourcename, "off");
            $this->view->status = "OFF";
        }else{
            Application_Model_Preference::SetSourceSwitchStatus($sourcename, "on");
            $this->view->status = "ON";
        }
    }
    
    public function switchOffSource(){
        
    }
    
    public function streamPlayerAction()
    {
        global $CC_CONFIG;
        
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();
        
        $this->view->headLink()->appendStylesheet($baseUrl.'/js/jplayer/skin/jplayer.blue.monday.css?'.$CC_CONFIG['airtime_version']);
        $this->_helper->layout->setLayout('bare');

        $logo = Application_Model_Preference::GetStationLogo();
        if($logo){
            $this->view->logo = "data:image/png;base64,$logo";
        } else {
            $this->view->logo = "$baseUrl/css/images/airtime_logo_jp.png";
        }
    }

    public function helpAction()
    {
        // action body
    }

    public function aboutAction()
    {
        $this->view->airtime_version = Application_Model_Preference::GetAirtimeVersion();
    }

}

