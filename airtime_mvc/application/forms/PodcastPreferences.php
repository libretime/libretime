<?php

class Application_Form_PodcastPreferences extends Zend_Form_SubForm {

    public function init() {
        $this->setDecorators(array(
                                 array('ViewScript', array('viewScript' => 'form/preferences_podcast.phtml'))
                             ));

        $isPrivate = Application_Model_Preference::getStationPodcastPrivacy();
        $stationPodcastPrivacy = new Zend_Form_Element_Radio('stationPodcastPrivacy');
        $stationPodcastPrivacy->setLabel(_('Station Podcast Feed Privacy'));
        $stationPodcastPrivacy->setMultiOptions(array(
                                                    _("Public"),
                                                    _("Private"),
                                                ));
        $stationPodcastPrivacy->setValue($isPrivate);
        $this->addElement($stationPodcastPrivacy);

        $stationPodcast = PodcastQuery::create()->findOneByDbId(Application_Model_Preference::getStationPodcastId());
        $url = $stationPodcast->getDbUrl();
        $feedUrl = new Zend_Form_Element_Text("stationPodcastFeedUrl:");
        $feedUrl->setAttrib('class', 'input_text')
            ->setAttrib('disabled', 'disabled')
            ->setRequired(false)
            ->setLabel(_("Station Podcast Feed URL"))
            ->setValue($url);
        $this->addElement($feedUrl);
    }

}