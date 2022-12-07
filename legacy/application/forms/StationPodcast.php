<?php

declare(strict_types=1);

class Application_Form_StationPodcast extends Zend_Form
{
    public function init()
    {
        // Station Podcast form
        $podcastPreferences = new Application_Form_PodcastPreferences();
        $this->addSubForm($podcastPreferences, 'preferences_podcast');

        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        $csrf_element = new Zend_Form_Element_Hidden('csrf');
        $csrf_element->setValue($csrf_namespace->authtoken)
            ->setRequired('true')
            ->removeDecorator('HtmlTag')
            ->removeDecorator('Label');
        $this->addElement($csrf_element);
    }
}
