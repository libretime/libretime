<?php

class EmbedController extends Zend_Controller_Action
{
    public function init()
    {
        // translate widgets to station default language
        $locale = Application_Model_Preference::GetDefaultLocale();
        if ($locale) {
            Application_Model_Locale::configureLocalization($locale);
        }
    }

    /**
     * This is the action that is called to insert the player onto a web page.
     * It passes all the js and css files to the view, as well as all the
     * stream customization information.
     *
     * The view for this action contains all the inline javascript needed to
     * create the player.
     */
    public function playerAction()
    {
        $this->view->layout()->disableLayout();

        $CC_CONFIG = Config::getConfig();

        $request = $this->getRequest();

        $this->view->playerhtml5_js = '/js/airtime/player/playerhtml5.js?' . $CC_CONFIG['airtime_version'];
        $this->view->jquery = '/js/libs/jquery-1.10.2.min.js';
        $this->view->metadata_api_url = '/api/live-info';
        $this->view->player_title = json_encode($this->view->escape($request->getParam('title')));
        $this->view->jquery_i18n = '/js/i18n/jquery.i18n.js?';

        $styleParam = $request->getParam('style');
        $player_style = isset($styleParam) ? $styleParam : 'basic';
        if ($player_style == 'premium') {
            $this->view->css = '/css/radio-page/premium_player.css?' . $CC_CONFIG['airtime_version'];
        } else {
            $this->view->css = '/css/player.css?' . $CC_CONFIG['airtime_version'];
        }
        $this->view->player_style = $player_style;

        $stream = $request->getParam('stream');
        $streamData = Application_Model_StreamSetting::getEnabledStreamData();
        $availableMobileStreams = [];
        $availableDesktopStreams = [];

        if ($stream == 'auto') {
            $this->view->playerMode = 'auto';
            $this->view->streamURL = json_encode('');
            foreach ($streamData as $s) {
                if ($s['mobile']) {
                    array_push($availableMobileStreams, $s);
                } elseif (!$s['mobile']) {
                    array_push($availableDesktopStreams, $s);
                }
            }
        } elseif ($stream == 'file') {
            $this->view->playerMode = 'file';
            $this->view->streamURL = json_encode($request->getParam('file_url'));
            $this->view->codec = $request->getParam('file_codec');
        } elseif (!empty($stream)) {
            $this->view->playerMode = 'manual';
            $selectedStreamData = $streamData[$stream];
            $this->view->streamURL = json_encode($selectedStreamData['url']);
            $this->view->codec = $selectedStreamData['codec'];
        }
        $this->view->availableMobileStreams = json_encode($availableMobileStreams);
        $this->view->availableDesktopStreams = json_encode($availableDesktopStreams);
    }

    public function weeklyProgramAction()
    {
        $this->view->layout()->disableLayout();

        $CC_CONFIG = Config::getConfig();

        $request = $this->getRequest();

        $this->view->angular = Application_Common_HTTPHelper::getStationUrl() . 'js/libs/angular.min.js?' . $CC_CONFIG['airtime_version'];
        $widgetStyle = $request->getParam('style');
        if ($widgetStyle == 'premium') {
            $this->view->widgetStyle = 'premium';
            $this->view->css = '/css/embed/weekly-schedule-widget.css?' . $CC_CONFIG['airtime_version'];
        } else {
            $this->view->widgetStyle = 'basic';
            $this->view->css = '/css/embed/weekly-schedule-widget-basic.css?' . $CC_CONFIG['airtime_version'];
        }
        $this->view->jquery = '/js/libs/jquery-1.8.3.min.js?' . $CC_CONFIG['airtime_version'];

        $weeklyScheduleData = WidgetHelper::getWeekInfoV2();

        $this->view->schedule_data = json_encode($weeklyScheduleData);

        $currentDay = new DateTime('now', new DateTimeZone(Application_Model_Preference::GetTimezone()));
        // day of the month without leading zeros (1 to 31)
        $this->view->currentDayOfMonth = $currentDay->format('j');
    }
}
