<?php

class DashboardController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('switch-source', 'json')
            ->addActionContext('disconnect-source', 'json')
            ->initContext();
    }

    public function indexAction()
    {
    }

    public function disconnectSourceAction()
    {
        $request = $this->getRequest();
        $sourcename = $request->getParam('sourcename');

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        $show = Application_Model_Show::getCurrentShow();
        $show_id = isset($show[0]['id']) ? $show[0]['id'] : 0;
        $source_connected = Application_Model_Preference::GetSourceStatus($sourcename);

        if ($user->canSchedule($show_id) && $source_connected) {
            $data = ['sourcename' => $sourcename];
            Application_Model_RabbitMq::SendMessageToPypo('disconnect_source', $data);
        } else {
            if ($source_connected) {
                $this->view->error = _("You don't have permission to disconnect source.");
            } else {
                $this->view->error = _('There is no source connected to this input.');
            }
        }
    }

    public function switchSourceAction()
    {
        $sourcename = $this->_getParam('sourcename');
        $current_status = $this->_getParam('status');

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        $show = Application_Model_Show::getCurrentShow();
        $show_id = isset($show[0]['id']) ? $show[0]['id'] : 0;

        $source_connected = Application_Model_Preference::GetSourceStatus($sourcename);
        if ($user->canSchedule($show_id) && ($source_connected || $sourcename == 'scheduled_play' || $current_status == 'on')) {
            $change_status_to = 'on';

            if (strtolower($current_status) == 'on') {
                $change_status_to = 'off';
            }

            $data = ['sourcename' => $sourcename, 'status' => $change_status_to];
            Application_Model_RabbitMq::SendMessageToPypo('switch_source', $data);
            if (strtolower($current_status) == 'on') {
                Application_Model_Preference::SetSourceSwitchStatus($sourcename, 'off');
                $this->view->status = 'OFF';

                // Log table updates
                Application_Model_LiveLog::SetEndTime(
                    $sourcename == 'scheduled_play' ? 'S' : 'L',
                    new DateTime('now', new DateTimeZone('UTC'))
                );
            } else {
                Application_Model_Preference::SetSourceSwitchStatus($sourcename, 'on');
                $this->view->status = 'ON';

                // Log table updates
                Application_Model_LiveLog::SetNewLogTime(
                    $sourcename == 'scheduled_play' ? 'S' : 'L',
                    new DateTime('now', new DateTimeZone('UTC'))
                );
            }
        } else {
            if ($source_connected) {
                $this->view->error = _("You don't have permission to switch source.");
            } else {
                if ($sourcename == 'scheduled_play') {
                    $this->view->error = _("You don't have permission to disconnect source.");
                } else {
                    $this->view->error = _('There is no source connected to this input.');
                }
            }
        }
    }

    public function streamPlayerAction()
    {
        $this->view->headLink()->appendStylesheet(Assets::url('js/jplayer/skin/jplayer.blue.monday.css'));
        $this->_helper->layout->setLayout('livestream');

        $logo = Application_Model_Preference::GetStationLogo();
        $this->view->logo = 'data:image/png;base64,' . $logo;
    }

    public function helpAction()
    {
        Zend_Layout::getMvcInstance()->assign('parent_page', 'Help');
    }

    public function aboutAction()
    {
        $config = Config::getConfig();
        Zend_Layout::getMvcInstance()->assign('parent_page', 'Help');
        $this->view->airtime_version = $config['airtime_version'];
    }

    public function tableTestAction()
    {
        Zend_Layout::getMvcInstance()->assign('parent_page', 'Help');

        $headScript = $this->view->headScript();
        AirtimeTableView::injectTableJavaScriptDependencies($headScript);
        $this->view->headScript()->appendFile(Assets::url('js/airtime/widgets/table-example.js'));
    }
}
