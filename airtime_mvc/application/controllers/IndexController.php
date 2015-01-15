<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function indexAction()
    {
        $this->_forward('index', 'showbuilder');
    }

    public function mainAction()
    {
        $this->_helper->layout->setLayout('layout');
    }

}
