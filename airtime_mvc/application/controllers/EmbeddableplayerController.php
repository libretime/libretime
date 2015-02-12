<?php

class EmbeddablePlayerController extends Zend_Controller_Action
{
    public function init()
    {

    }
    
    public function indexAction()
    {
        $form = new Application_Form_EmbeddablePlayer();

        $request = $this->getRequest();

        if ($request->isPost()) {
            $formValues = $request->getPost();
            if ($form->isValid($formValues)) {

                $this->view->statusMsg = "<div class='success'>". _("Preferences updated.")."</div>";

            } else {

            }

            $this->view->form = $form;
        }

        $this->view->form = $form;
    }
}