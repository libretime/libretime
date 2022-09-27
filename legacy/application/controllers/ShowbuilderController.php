<?php

class ShowbuilderController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('schedule-move', 'json')
            ->addActionContext('schedule-add', 'json')
            ->addActionContext('schedule-remove', 'json')
            ->addActionContext('builder-dialog', 'json')
            ->addActionContext('check-builder-feed', 'json')
            ->addActionContext('builder-feed', 'json')
            ->addActionContext('context-menu', 'json')
            ->initContext();
    }

    public function indexAction()
    {
        $userType = Application_Model_User::GetCurrentUser()->getType();

        // $this->_helper->layout->setLayout("showbuilder");

        $this->view->headScript()->appendScript("localStorage.setItem( 'user-type', '{$userType}' );");

        $this->view->headLink()->appendStylesheet(Assets::url('css/redmond/jquery-ui-1.8.8.custom.css'));

        $this->view->headScript()->appendFile(Assets::url('js/contextmenu/jquery.contextMenu.js'), 'text/javascript');

        $this->view->headScript()->appendFile(Assets::url('js/blockui/jquery.blockUI.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/buttons/buttons.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/utilities/utilities.js'), 'text/javascript');

        $this->view->headLink()->appendStylesheet(Assets::url('css/media_library.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/jquery.contextMenu.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/datatables/css/ColVis.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/datatables/css/dataTables.colReorder.min.css'));
        $this->view->headScript()->appendFile(Assets::url('js/airtime/library/library.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/library/events/library_showbuilder.js'), 'text/javascript');
        $headScript = $this->view->headScript();
        AirtimeTableView::injectTableJavaScriptDependencies($headScript);

        // PLUPLOAD
        $this->view->headScript()->appendFile(Assets::url('js/libs/dropzone.min.js'), 'text/javascript');

        $this->view->headScript()->appendFile(Assets::url('js/timepicker/jquery.ui.timepicker.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/showbuilder/tabs.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/showbuilder/builder.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/showbuilder/main_builder.js'), 'text/javascript');

        // MEDIA BUILDER
        $this->view->headScript()->appendFile(Assets::url('js/libs/dayjs.min.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/libs/utc.min.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/libs/timezone.min.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/library/spl.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/library/podcast.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/library/publish.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/playlist/smart_blockbuilder.js'), 'text/javascript');
        $this->view->headLink()->appendStylesheet(Assets::url('css/playlist_builder.css'));

        $this->view->headLink()->appendStylesheet(Assets::url('css/jquery.ui.timepicker.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/showbuilder.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/dashboard.css'));

        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        $csrf_element = new Zend_Form_Element_Hidden('csrf');
        $csrf_element->setValue($csrf_namespace->authtoken)->setRequired('true')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $this->view->csrf = $csrf_element;

        $request = $this->getRequest();
        // populate date range form for show builder.
        $now = time();
        $from = $request->getParam('from', $now);
        $to = $request->getParam('to', $now + (3 * 60 * 60));

        $utcTimezone = new DateTimeZone('UTC');
        $displayTimeZone = new DateTimeZone(Application_Model_Preference::GetTimezone());

        $start = DateTime::createFromFormat('U', $from, $utcTimezone);
        $start->setTimezone($displayTimeZone);
        $end = DateTime::createFromFormat('U', $to, $utcTimezone);
        $end->setTimezone($displayTimeZone);

        $this->checkAndShowSetupPopup($request);

        $form = new Application_Form_ShowBuilder();
        $form->populate([
            'sb_date_start' => $start->format('Y-m-d'),
            'sb_time_start' => $start->format('H:i'),
            'sb_date_end' => $end->format('Y-m-d'),
            'sb_time_end' => $end->format('H:i'),
        ]);

        $this->view->sb_form = $form;
    }

    /** Check if we need to show the timezone/language setup popup and display it. (eg. on first run) */
    public function checkAndShowSetupPopup($request)
    {
        $setupComplete = Application_Model_Preference::getLangTimezoneSetupComplete();
        $previousPage = strtolower($request->getHeader('Referer'));
        $userService = new Application_Service_UserService();
        $currentUser = $userService->getCurrentUser();
        $previousPageWasLoginScreen = (strpos($previousPage, 'login') !== false)
            || (strpos($previousPage, SAAS_LOGIN_REFERRER) !== false);

        // If current user is Super Admin, and they came from the login page,
        // and they have not seen the setup popup before
        if ($currentUser->isSuperAdmin() && $previousPageWasLoginScreen && empty($setupComplete)) {
            $lang_tz_popup_form = new Application_Form_SetupLanguageTimezone();
            $this->view->lang_tz_popup_form = $lang_tz_popup_form;
            $this->view->headScript()->appendFile(Assets::url('js/airtime/nowplaying/lang-timezone-setup.js'), 'text/javascript');
        }
    }

    public function contextMenuAction()
    {
        $baseUrl = Config::getBasePath();

        $id = $this->_getParam('id');
        $now = floatval(microtime(true));

        $request = $this->getRequest();
        $menu = [];

        $user = Application_Model_User::getCurrentUser();

        $item = CcScheduleQuery::create()->findPK($id);
        $instance = $item->getCcShowInstances();

        $menu['preview'] = ['name' => _('Preview'), 'icon' => 'play'];
        // select the cursor
        $menu['selCurs'] = ['name' => _('Select cursor'), 'icon' => 'select-cursor'];
        $menu['delCurs'] = ['name' => _('Remove cursor'), 'icon' => 'select-cursor'];

        if ($now < floatval($item->getDbEnds('U.u')) && $user->canSchedule($instance->getDbShowId())) {
            // remove/truncate the item from the schedule
            $menu['del'] = ['name' => _('Delete'), 'icon' => 'delete', 'url' => $baseUrl . 'showbuilder/schedule-remove'];
        }

        $this->view->items = $menu;
    }

    public function builderDialogAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id');

        $instance = CcShowInstancesQuery::create()->findPK($id);

        if (is_null($instance)) {
            $this->view->error = _('show does not exist');

            return;
        }

        $displayTimeZone = new DateTimeZone(Application_Model_Preference::GetTimezone());

        $start = $instance->getDbStarts(null);
        $start->setTimezone($displayTimeZone);
        $end = $instance->getDbEnds(null);
        $end->setTimezone($displayTimeZone);

        $show_name = $instance->getCcShow()->getDbName();
        $start_time = $start->format(DEFAULT_TIMESTAMP_FORMAT);
        $end_time = $end->format(DEFAULT_TIMESTAMP_FORMAT);

        $this->view->title = "{$show_name}:    {$start_time} - {$end_time}";
        $this->view->start = $start_time;
        $this->view->end = $end_time;

        $form = new Application_Form_ShowBuilder();
        $form->populate([
            'sb_date_start' => $start->format('Y-m-d'),
            'sb_time_start' => $start->format('H:i'),
            'sb_date_end' => $end->format('Y-m-d'),
            'sb_time_end' => $end->format('H:i'),
        ]);

        $this->view->sb_form = $form;

        $this->view->dialog = $this->view->render('showbuilder/builderDialog.phtml');
    }

    public function checkBuilderFeedAction()
    {
        $request = $this->getRequest();
        $show_filter = intval($request->getParam('showFilter', 0));
        $my_shows = intval($request->getParam('myShows', 0));
        $timestamp = intval($request->getParam('timestamp', -1));
        $instances = $request->getParam('instances', []);

        [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($request);

        $opts = ['myShows' => $my_shows, 'showFilter' => $show_filter];
        $showBuilder = new Application_Model_ShowBuilder($startsDT, $endsDT, $opts);

        // only send the schedule back if updates have been made.
        // -1 default will always call the schedule to be sent back if no timestamp is defined.
        $this->view->update = $showBuilder->hasBeenUpdatedSince(
            $timestamp,
            $instances
        );
    }

    public function builderFeedAction()
    {
        $current_time = time();

        $request = $this->getRequest();
        $show_filter = intval($request->getParam('showFilter', 0));
        $show_instance_filter = intval($request->getParam('showInstanceFilter', 0));
        $my_shows = intval($request->getParam('myShows', 0));

        [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($request);

        $opts = [
            'myShows' => $my_shows,
            'showFilter' => $show_filter,
            'showInstanceFilter' => $show_instance_filter,
        ];
        $showBuilder = new Application_Model_ShowBuilder($startsDT, $endsDT, $opts);

        $data = $showBuilder->getItems();
        $this->view->schedule = $data['schedule'];
        $this->view->instances = $data['showInstances'];
        $this->view->timestamp = $current_time;
    }

    public function scheduleAddAction()
    {
        $request = $this->getRequest();

        $mediaItems = $request->getParam('mediaIds', []);
        $scheduledItems = $request->getParam('schedIds', []);

        $log_vars = [];
        $log_vars['url'] = $_SERVER['HTTP_HOST'];
        $log_vars['action'] = 'showbuilder/schedule-add';
        $log_vars['params'] = [];
        $log_vars['params']['media_items'] = $mediaItems;
        $log_vars['params']['scheduled_items'] = $scheduledItems;
        Logging::debug($log_vars);

        try {
            $scheduler = new Application_Model_Scheduler();
            $scheduler->scheduleAfter($scheduledItems, $mediaItems);
        } catch (OutDatedScheduleException $e) {
            $this->view->error = $e->getMessage();
            Logging::error($e->getMessage());
        } catch (Exception $e) {
            $this->view->error = $e->getMessage();
            Logging::error($e->getMessage());
        }
    }

    public function scheduleRemoveAction()
    {
        $request = $this->getRequest();
        $items = $request->getParam('items', []);

        $log_vars = [];
        $log_vars['url'] = $_SERVER['HTTP_HOST'];
        $log_vars['action'] = 'showbuilder/schedule-remove';
        $log_vars['params'] = [];
        $log_vars['params']['removed_items'] = $items;
        Logging::debug($log_vars);

        try {
            $scheduler = new Application_Model_Scheduler();
            $scheduler->removeItems($items);
        } catch (OutDatedScheduleException $e) {
            $this->view->error = $e->getMessage();
            Logging::error($e->getMessage());
        } catch (Exception $e) {
            $this->view->error = $e->getMessage();
            Logging::error($e->getMessage());
        }
    }

    public function scheduleMoveAction()
    {
        $request = $this->getRequest();
        $selectedItems = $request->getParam('selectedItem');
        $afterItem = $request->getParam('afterItem');

        $log_vars = [];
        $log_vars['url'] = $_SERVER['HTTP_HOST'];
        $log_vars['action'] = 'showbuilder/schedule-move';
        $log_vars['params'] = [];
        $log_vars['params']['selected_items'] = $selectedItems;
        $log_vars['params']['destination_after_item'] = $afterItem;
        Logging::debug($log_vars);

        try {
            $scheduler = new Application_Model_Scheduler();
            $scheduler->moveItem($selectedItems, $afterItem);
        } catch (OutDatedScheduleException $e) {
            $this->view->error = $e->getMessage();
            Logging::error($e->getMessage());
        } catch (Exception $e) {
            $this->view->error = $e->getMessage();
            Logging::error($e->getMessage());
        }
    }

    public function scheduleReorderAction()
    {
        throw new Exception('this controller is/was a no-op please fix your code');
    }
}
