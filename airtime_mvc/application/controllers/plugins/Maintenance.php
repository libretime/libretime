<?php

class Zend_Controller_Plugin_Maintenance extends Zend_Controller_Plugin_Abstract
{
    protected $maintenanceFile = '/tmp/maintenance.txt';

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        if (file_exists($this->maintenanceFile)) {
            $request->setModuleName('default')
                    ->setControllerName('index')
                    ->setActionName('maintenance')
                    ->setDispatched(true);
        }
    }
}