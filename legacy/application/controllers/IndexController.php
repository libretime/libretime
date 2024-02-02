<?php

class IndexController extends Zend_Controller_Action
{
    public function init() {}

    public function indexAction()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Config::getBasePath();
        if (Application_Model_Preference::getRadioPageDisabled()) {
            $this->_helper->redirector->gotoUrl($baseUrl . 'login');

            return;
        }

        $this->view->headTitle(Application_Model_Preference::GetHeadTitle());
        $this->view->headScript()->appendFile(Assets::url('js/libs/jquery-1.8.3.min.js'), 'text/javascript');

        $this->view->headScript()->appendFile(Assets::url('js/i18n/jquery.i18n.js'), 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl . 'locale/general-translation-table', 'text/javascript');
        $this->view->headScript()->appendScript('$.i18n.setDictionary(general_dict)');
        $this->view->headScript()->appendScript("var baseUrl='{$baseUrl}'");
            $this->view->headScript()->appendFile(Assets::url('js/i18n/jquery.i18n.js'), 'text/javascript');
            $this->view->headScript()->appendFile($baseUrl . 'locale/general-translation-table', 'text/javascript');
            $this->view->headScript()->appendScript('$.i18n.setDictionary(general_dict)');
            $this->view->headScript()->appendScript("var baseUrl='{$baseUrl}'");

            // jplayer
            $this->view->headScript()->appendFile(Assets::url('js/jplayer/jquery.jplayer.min.js'), 'text/javascript');
            $this->view->headScript()->appendFile(Assets::url('js/jplayer/jplayer.playlist.min.js'), 'text/javascript');

            $this->view->headLink()->setStylesheet(Assets::url('css/radio-page/radio-page.css'));
            $this->view->headLink()->appendStylesheet(Assets::url('css/embed/weekly-schedule-widget.css'));
            $this->view->headLink()->appendStylesheet(Assets::url('css/radio-page/station-podcast.css'));
            $this->view->headLink()->appendStylesheet(Assets::url('css/bootstrap.css'));

            // jplayer control buttons
            $this->view->headLink()->appendStylesheet(Assets::url('css/redmond/jquery-ui-1.8.8.custom.css'));

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

            $this->view->stationUrl = Config::getPublicUrl();

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
