<?php

class ShowBuilderController extends Zend_Controller_Action {

    public function init() {
    }

    public function indexAction() {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $userType = Application_Model_User::GetCurrentUser()->getType();

        //$this->_helper->layout->setLayout("showbuilder");

        $this->view->headScript()->appendScript("localStorage.setItem( 'user-type', '$userType' );");
        $this->view->headScript()->appendScript(Application_Common_GoogleAnalytics::generateGoogleTagManagerDataLayerJavaScript());

        $this->view->headLink()->appendStylesheet($baseUrl . 'css/redmond/jquery-ui-1.8.8.custom.css?' . $CC_CONFIG['airtime_version']);

        $this->view->headScript()->appendFile($baseUrl.'js/contextmenu/jquery.contextMenu.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/js/jquery.dataTables.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.pluginAPI.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.fnSetFilteringDelay.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.ColVis.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.ColReorder.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.FixedColumns.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.columnFilter.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');

        $this->view->headScript()->appendFile($baseUrl.'js/blockui/jquery.blockUI.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/buttons/buttons.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/utilities/utilities.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'css/media_library.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/jquery.contextMenu.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/datatables/css/ColVis.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/datatables/css/ColReorder.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/library/_library.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/library/events/_library_showbuilder.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        // PLUPLOAD
        $this->view->headScript()->appendFile($baseUrl.'js/libs/dropzone.min.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headScript()->appendFile($baseUrl.'js/timepicker/jquery.ui.timepicker.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/showbuilder/_builder.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/showbuilder/_main_builder.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        // MEDIA BUILDER
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/library/_spl.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/playlist/_smart_blockbuilder.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headLink()->appendStylesheet($baseUrl.'css/playlist_builder.css?'.$CC_CONFIG['airtime_version']);

        $this->view->headLink()->appendStylesheet($baseUrl.'css/jquery.ui.timepicker.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/showbuilder.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/_showbuilder.css?'.$CC_CONFIG['airtime_version']);

        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        $csrf_element = new Zend_Form_Element_Hidden('csrf');
        $csrf_element->setValue($csrf_namespace->authtoken)->setRequired('true')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $this->view->csrf = $csrf_element;

        $request = $this->getRequest();
        //populate date range form for show builder.
        $now  = time();
        $from = $request->getParam("from", $now);
        $to   = $request->getParam("to", $now + (3*60*60));

        $utcTimezone = new DateTimeZone("UTC");
        $displayTimeZone = new DateTimeZone(Application_Model_Preference::GetTimezone());

        $start = DateTime::createFromFormat("U", $from, $utcTimezone);
        $start->setTimezone($displayTimeZone);
        $end = DateTime::createFromFormat("U", $to, $utcTimezone);
        $end->setTimezone($displayTimeZone);

        $form = new Application_Form_ShowBuilderNew();
        $form->populate(array(
                            'sb_date_start' => $start->format("Y-m-d"),
                            'sb_time_start' => $start->format("H:i"),
                            'sb_date_end'   => $end->format("Y-m-d"),
                            'sb_time_end'   => $end->format("H:i")
                        ));

        $this->view->sb_form = $form;
    }

}
