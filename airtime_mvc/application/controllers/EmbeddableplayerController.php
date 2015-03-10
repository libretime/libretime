<?php

class EmbeddablePlayerController extends Zend_Controller_Action
{
    public function init()
    {

    }
    
    public function indexAction()
    {
        $form = new Application_Form_EmbeddablePlayer();

        $this->view->form = $form;
    }

    public function embedCodeAction()
    {
        $this->view->layout()->disableLayout();
    }
}