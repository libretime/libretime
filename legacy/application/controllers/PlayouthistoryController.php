<?php

declare(strict_types=1);

class PlayouthistoryController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
            ->addActionContext('file-history-feed', 'json')
            ->addActionContext('item-history-feed', 'json')
            ->addActionContext('show-history-feed', 'json')
            ->addActionContext('edit-file-item', 'json')
            ->addActionContext('create-list-item', 'json')
            ->addActionContext('edit-list-item', 'json')
            ->addActionContext('delete-list-item', 'json')
            ->addActionContext('delete-list-items', 'json')
            ->addActionContext('update-list-item', 'json')
            ->addActionContext('update-file-item', 'json')
            ->initContext();
    }

    public function indexAction()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Config::getBasePath();

        Zend_Layout::getMvcInstance()->assign('parent_page', 'Analytics');

        [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($this->getRequest());

        $userTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());
        $startsDT->setTimezone($userTimezone);
        $endsDT->setTimezone($userTimezone);

        $form = new Application_Form_DateRange();
        $form->populate([
            'his_date_start' => $startsDT->format('Y-m-d'),
            'his_time_start' => $startsDT->format('H:i'),
            'his_date_end' => $endsDT->format('Y-m-d'),
            'his_time_end' => $endsDT->format('H:i'),
        ]);

        $this->view->date_form = $form;

        $this->view->headScript()->appendFile(Assets::url('js/contextmenu/jquery.contextMenu.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/datatables/js/jquery.dataTables.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/datatables/plugin/dataTables.pluginAPI.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/datatables/plugin/dataTables.fnSetFilteringDelay.js'), 'text/javascript');

        $this->view->headScript()->appendFile(Assets::url('js/timepicker/jquery.ui.timepicker.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/bootstrap-datetime/bootstrap-datetimepicker.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/buttons/buttons.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/utilities/utilities.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/libs/CSVexport.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/libs/pdfmake.min.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/libs/vfs_fonts.min.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/playouthistory/historytable.js'), 'text/javascript');

        $this->view->headLink()->appendStylesheet(Assets::url('css/bootstrap-datetimepicker.min.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/jquery.ui.timepicker.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/playouthistory.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/history_styles.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/jquery.contextMenu.css'));

        // set datatables columns for display of data.
        $historyService = new Application_Service_HistoryService();
        $columns = json_encode($historyService->getDatatablesLogSheetColumns());
        $script = "localStorage.setItem( 'datatables-historyitem-aoColumns', JSON.stringify({$columns}) ); ";

        $columns = json_encode($historyService->getDatatablesFileSummaryColumns());
        $script .= "localStorage.setItem( 'datatables-historyfile-aoColumns', JSON.stringify({$columns}) );";
        $this->view->headScript()->appendScript($script);

        $user = Application_Model_User::getCurrentUser();
        $this->view->userType = $user->getType();
    }

    public function fileHistoryFeedAction()
    {
        try {
            $request = $this->getRequest();
            $params = $request->getParams();
            $instance = $request->getParam('instance_id', null);

            [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($request);

            $historyService = new Application_Service_HistoryService();
            $r = $historyService->getFileSummaryData($startsDT, $endsDT, $params);

            $this->view->sEcho = $r['sEcho'];
            $this->view->iTotalDisplayRecords = $r['iTotalDisplayRecords'];
            $this->view->iTotalRecords = $r['iTotalRecords'];
            $this->view->history = $r['history'];
            $this->view->history = SecurityHelper::htmlescape_recursive($this->view->history);
        } catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }

    public function itemHistoryFeedAction()
    {
        try {
            $request = $this->getRequest();
            $params = $request->getParams();
            $instance = $request->getParam('instance_id', null);

            [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($request);

            $historyService = new Application_Service_HistoryService();
            $r = $historyService->getPlayedItemData($startsDT, $endsDT, $params, $instance);

            $this->view->sEcho = $r['sEcho'];
            $this->view->iTotalDisplayRecords = $r['iTotalDisplayRecords'];
            $this->view->iTotalRecords = $r['iTotalRecords'];
            $this->view->history = $r['history'];
            $this->view->history = SecurityHelper::htmlescape_recursive($this->view->history);
        } catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }

    public function showHistoryFeedAction()
    {
        try {
            $request = $this->getRequest();
            $params = $request->getParams();
            $instance = $request->getParam('instance_id', null);

            [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($request);

            $historyService = new Application_Service_HistoryService();
            $shows = $historyService->getShowList($startsDT, $endsDT);
            $shows = SecurityHelper::htmlescape_recursive($shows);

            $this->_helper->json->sendJson($shows);
        } catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }

    public function editFileItemAction()
    {
        $file_id = $this->_getParam('id');

        $historyService = new Application_Service_HistoryService();
        $form = $historyService->makeHistoryFileForm($file_id);

        $this->view->form = $form;
        $this->view->dialog = $this->view->render('playouthistory/dialog.phtml');

        unset($this->view->form);
    }

    public function createListItemAction()
    {
        try {
            $request = $this->getRequest();
            $params = $request->getPost();
            Logging::info($params);

            $historyService = new Application_Service_HistoryService();
            $json = $historyService->createPlayedItem($params);

            if (isset($json['form'])) {
                $this->view->form = $json['form'];
                $json['form'] = $this->view->render('playouthistory/dialog.phtml');

                unset($this->view->form);
            }

            $this->_helper->json->sendJson($json);
        } catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }

    public function editListItemAction()
    {
        $id = $this->_getParam('id', null);

        $populate = isset($id) ? true : false;

        $historyService = new Application_Service_HistoryService();
        $form = $historyService->makeHistoryItemForm($id, $populate);

        $this->view->form = $form;
        $this->view->dialog = $this->view->render('playouthistory/dialog.phtml');

        unset($this->view->form);
    }

    public function deleteListItemAction()
    {
        $history_id = $this->_getParam('id');

        $historyService = new Application_Service_HistoryService();
        $historyService->deletePlayedItem($history_id);
    }

    public function deleteListItemsAction()
    {
        $history_ids = $this->_getParam('ids');

        $historyService = new Application_Service_HistoryService();
        $historyService->deletePlayedItems($history_ids);
    }

    public function updateListItemAction()
    {
        try {
            $request = $this->getRequest();
            $params = $request->getPost();
            Logging::info($params);

            $historyService = new Application_Service_HistoryService();
            $json = $historyService->editPlayedItem($params);

            if (isset($json['form'])) {
                $this->view->form = $json['form'];
                $json['form'] = $this->view->render('playouthistory/dialog.phtml');

                unset($this->view->form);
            }

            $this->_helper->json->sendJson($json);
        } catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }

    public function updateFileItemAction()
    {
        $request = $this->getRequest();
        $params = $request->getPost();
        Logging::info($params);

        $historyService = new Application_Service_HistoryService();
        $json = $historyService->editPlayedFile($params);

        $this->_helper->json->sendJson($json);
    }
}
