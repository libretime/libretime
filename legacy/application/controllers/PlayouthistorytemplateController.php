<?php

class PlayouthistorytemplateController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
            ->addActionContext('create-template', 'json')
            ->addActionContext('update-template', 'json')
            ->addActionContext('delete-template', 'json')
            ->addActionContext('set-template-default', 'json')
            ->initContext();
    }

    public function indexAction()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Config::getBasePath();

        Zend_Layout::getMvcInstance()->assign('parent_page', 'Analytics');

        $this->view->headScript()->appendFile(Assets::url('js/airtime/playouthistory/template.js'), 'text/javascript');
        $this->view->headLink()->appendStylesheet(Assets::url('css/history_styles.css'));

        $historyService = new Application_Service_HistoryService();
        $this->view->template_list = $historyService->getListItemTemplates();
        $this->view->template_file = $historyService->getFileTemplates();
        $this->view->configured = $historyService->getConfiguredTemplateIds();
    }

    public function configureTemplateAction()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Config::getBasePath();

        Zend_Layout::getMvcInstance()->assign('parent_page', 'Analytics');

        $this->view->headScript()->appendFile(Assets::url('js/airtime/playouthistory/configuretemplate.js'), 'text/javascript');
        $this->view->headLink()->appendStylesheet(Assets::url('css/history_styles.css'));

        try {
            $templateId = $this->_getParam('id');

            $historyService = new Application_Service_HistoryService();
            $template = $historyService->loadTemplate($templateId);

            $templateType = $template['type'];
            $supportedTypes = $historyService->getSupportedTemplateTypes();

            if (!in_array($templateType, $supportedTypes)) {
                throw new Exception("Error: {$templateType} is not supported.");
            }

            $getMandatoryFields = 'mandatory' . ucfirst($templateType) . 'Fields';
            $mandatoryFields = $historyService->{$getMandatoryFields}();

            $this->view->template_id = $templateId;
            $this->view->template_name = $template['name'];
            $this->view->template_fields = $template['fields'];
            $this->view->template_type = $templateType;
            $this->view->fileMD = $historyService->getFileMetadataTypes();
            $this->view->fields = $historyService->getFieldTypes();
            $this->view->required_fields = $mandatoryFields;
            $this->view->configured = $historyService->getConfiguredTemplateIds();
        } catch (Exception $e) {
            Logging::info('Error?');
            Logging::info($e);
            Logging::info($e->getMessage());

            $this->_forward('index', 'playouthistorytemplate');
        }
    }

    public function createTemplateAction()
    {
        $templateType = $this->_getParam('type', null);

        $request = $this->getRequest();
        $params = $request->getPost();

        try {
            $historyService = new Application_Service_HistoryService();
            $supportedTypes = $historyService->getSupportedTemplateTypes();

            if (!in_array($templateType, $supportedTypes)) {
                throw new Exception("Error: {$templateType} is not supported.");
            }

            $id = $historyService->createTemplate($params);

            $this->view->url = $this->view->baseUrl("Playouthistorytemplate/configure-template/id/{$id}");
        } catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());

            $this->view->error = $e->getMessage();
        }
    }

    public function setTemplateDefaultAction()
    {
        $templateId = $this->_getParam('id', null);

        try {
            $historyService = new Application_Service_HistoryService();
            $historyService->setConfiguredTemplate($templateId);
        } catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }

    public function updateTemplateAction()
    {
        $templateId = $this->_getParam('id', null);
        $name = $this->_getParam('name', null);
        $fields = $this->_getParam('fields', []);

        try {
            $historyService = new Application_Service_HistoryService();
            $historyService->updateItemTemplate($templateId, $name, $fields);
        } catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }

    public function deleteTemplateAction()
    {
        $templateId = $this->_getParam('id');

        try {
            $historyService = new Application_Service_HistoryService();
            $historyService->deleteTemplate($templateId);
        } catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }
}
