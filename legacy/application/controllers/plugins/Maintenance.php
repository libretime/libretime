<?php

declare(strict_types=1);

class Zend_Controller_Plugin_Maintenance extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $maintenanceFile = isset($_SERVER['AIRTIME_BASE']) ? $_SERVER['AIRTIME_BASE'] . 'maintenance.txt' : '/tmp/maintenance.txt';

        if (file_exists($maintenanceFile)) {
            $request->setModuleName('default')
                ->setControllerName('index')
                ->setActionName('maintenance')
                ->setDispatched(true);
        }
    }
}
