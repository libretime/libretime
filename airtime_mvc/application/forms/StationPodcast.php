<?php

class Application_Form_StationPodcast extends Zend_Form {

    public function init() {
        // Station Podcast form
        $podcastPreferences = new Application_Form_PodcastPreferences();
        $this->addSubForm($podcastPreferences, 'preferences_podcast');
    }

}