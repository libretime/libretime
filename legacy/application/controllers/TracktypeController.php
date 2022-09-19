<?php

class TracktypeController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-tracktype-data-table-info', 'json')
            ->addActionContext('get-tracktype-data', 'json')
            ->addActionContext('remove-tracktype', 'json')
            ->initContext();
    }

    public function addTracktypeAction()
    {
        // Start the session to re-open write permission to the session so we can
        // create the namespace for our csrf token verification
        SessionHelper::reopenSessionForWriting();

        $request = $this->getRequest();

        Zend_Layout::getMvcInstance()->assign('parent_page', 'Settings');

        foreach ([
            'js/datatables/js/jquery.dataTables.js',
            'js/datatables/plugin/dataTables.pluginAPI.js',
            'js/airtime/tracktype/tracktype.js',
        ] as $file) {
            $this->view->headScript()->appendFile(Assets::url($file), 'text/javascript');
        }

        $this->view->headLink()->appendStylesheet(Assets::url('css/tracktypes.css'));

        $form = new Application_Form_AddTracktype();

        $this->view->successMessage = '';

        if ($request->isPost()) {
            $params = $request->getPost();
            $postData = explode('&', $params['data']);
            $formData = [];
            foreach ($postData as $k => $v) {
                $v = explode('=', $v);
                $formData[$v[0]] = urldecode($v[1]);
            }

            if ($form->validateCode($formData)) {
                $tracktype = new Application_Model_Tracktype($formData['tracktype_id']);
                if (empty($formData['tracktype_id'])) {
                    $tracktype->setCode($formData['code']);
                }
                $tracktype->setTypeName($formData['type_name']);
                $tracktype->setDescription($formData['description']);
                $tracktype->setVisibility($formData['visibility']);
                $tracktype->save();

                $form->reset();
                $this->view->form = $form;

                if (strlen($formData['tracktype_id']) == 0) {
                    $this->view->successMessage = "<div class='success'>" . _('Track Type added successfully!') . '</div>';
                } else {
                    $this->view->successMessage = "<div class='success'>" . _('Track Type updated successfully!') . '</div>';
                }

                $this->_helper->json->sendJson(['valid' => 'true', 'html' => $this->view->render('tracktype/add-tracktype.phtml')]);
            } else {
                $this->view->form = $form;
                $this->_helper->json->sendJson(['valid' => 'false', 'html' => $this->view->render('tracktype/add-tracktype.phtml')]);
            }
        }

        $this->view->form = $form;
    }

    public function getTracktypeDataTableInfoAction()
    {
        $post = $this->getRequest()->getPost();
        $tracktypes = Application_Model_Tracktype::getTracktypesDataTablesInfo($post);

        $this->_helper->json->sendJson($tracktypes);
    }

    public function getTracktypeDataAction()
    {
        $id = $this->_getParam('id');
        $this->view->entries = Application_Model_Tracktype::GetTracktypeData($id);
    }

    public function removeTracktypeAction()
    {
        // action body
        $delId = $this->_getParam('id');

        $tracktype = new Application_Model_Tracktype($delId);

        // Delete the track type
        $this->view->entries = $tracktype->delete();
    }
}
