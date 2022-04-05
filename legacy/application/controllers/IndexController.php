<?php

class IndexController extends Zend_Controller_Action
{
    public function init()
    {
    }

    public function indexAction()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $this->view->headTitle(Application_Model_Preference::GetHeadTitle());
        $this->view->headScript()->appendFile($baseUrl . 'js/libs/jquery-1.8.3.min.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');

        $this->view->headScript()->appendFile($baseUrl . 'js/i18n/jquery.i18n.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl . 'locale/general-translation-table?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendScript('$.i18n.setDictionary(general_dict)');
        $this->view->headScript()->appendScript("var baseUrl='{$baseUrl}'");

        // jplayer
        $this->view->headScript()->appendFile($baseUrl . 'js/jplayer/jquery.jplayer.min.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl . 'js/jplayer/jplayer.playlist.min.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');

        $this->view->headLink()->setStylesheet($baseUrl . 'css/radio-page/radio-page.css?' . $CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl . 'css/embed/weekly-schedule-widget.css?' . $CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl . 'css/radio-page/station-podcast.css?' . $CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl . 'css/bootstrap.css?' . $CC_CONFIG['airtime_version']);

        // jplayer control buttons
        $this->view->headLink()->appendStylesheet($baseUrl . 'css/redmond/jquery-ui-1.8.8.custom.css?' . $CC_CONFIG['airtime_version']);

        $this->_helper->layout->setLayout('radio-page');

        // translate page to station default language
        $locale = Application_Model_Preference::GetDefaultLocale();
        if ($locale) {
            Application_Model_Locale::configureLocalization($locale);
        }

        $this->view->stationLogo = Application_Model_Preference::GetStationLogo();

        $stationName = Application_Model_Preference::GetStationName();
        $this->view->stationName = $stationName;

        $stationDescription = Application_Model_Preference::GetStationDescription();
        $this->view->stationDescription = $stationDescription;

        $this->view->stationUrl = Application_Common_HTTPHelper::getStationUrl();

        $displayRadioPageLoginButtonValue = Application_Model_Preference::getRadioPageDisplayLoginButton();
        if ($displayRadioPageLoginButtonValue == '') {
            $displayRadioPageLoginButtonValue = true;
        }
        $this->view->displayLoginButton = $displayRadioPageLoginButtonValue;

        // station feed episodes
        $stationPodcastId = Application_Model_Preference::getStationPodcastId();
        $podcastEpisodesService = new Application_Service_PodcastEpisodeService();
        $episodes = $podcastEpisodesService->getPodcastEpisodes($stationPodcastId, 0, 0, PodcastEpisodesPeer::PUBLICATION_DATE, 'DESC');
        foreach ($episodes as $e => $v) {
            $episodes[$e]['CcFiles']['track_title'] = htmlspecialchars($v['CcFiles']['track_title'], ENT_QUOTES);
            $episodes[$e]['CcFiles']['artist_name'] = htmlspecialchars($v['CcFiles']['artist_name'], ENT_QUOTES);

            $pubDate = explode(' ', $v['publication_date']);
            $episodes[$e]['publication_date'] = $pubDate[0];

            $length = explode('.', $v['CcFiles']['length']);
            $episodes[$e]['CcFiles']['length'] = $length[0];

            $episodes[$e]['mime'] = FileDataHelper::getAudioMimeTypeArray()[$v['CcFiles']['mime']];

            if (is_null($v['CcFiles']['description'])) {
                $episodes[$e]['CcFiles']['description'] = '';
            }
        }

        $episodePages = array_chunk($episodes, 10);

        $this->view->episodes = json_encode($episodePages, JSON_FORCE_OBJECT);
        $this->view->displayRssTab = (!Application_Model_Preference::getStationPodcastPrivacy());

        $stationPodcast = PodcastQuery::create()->findOneByDbId($stationPodcastId);
        $url = $stationPodcast->getDbUrl();
        $this->view->stationPodcastRssUrl = $url;

        $stationName = Application_Model_Preference::GetStationName();
        $this->view->podcastTitle = sprintf(_('%s Podcast'), !empty($stationName) ? $stationName : $CC_CONFIG['stationId']);
        $this->view->emptyPodcastMessage = _('No tracks have been published yet.');
    }

    public function mainAction()
    {
        $this->_helper->layout->setLayout('layout');
    }

    public function maintenanceAction()
    {
        $this->getResponse()->setHttpResponseCode(503);
    }
}
