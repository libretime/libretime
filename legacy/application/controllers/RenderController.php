<?php

declare(strict_types=1);

class RenderController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        $csrf_element = new Zend_Form_Element_Hidden('csrf_token');
        $csrf_element->setValue($csrf_namespace->authtoken)->setRequired('true')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $this->view->csrf = $csrf_element;
    }

    public function podcastUrlDialogAction()
    {
        $path = 'podcast/podcast_url_dialog.phtml';
        $this->_helper->json->sendJson(['html' => $this->view->render($path)]);
    }
}
