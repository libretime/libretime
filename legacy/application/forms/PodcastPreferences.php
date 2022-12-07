<?php

declare(strict_types=1);

class Application_Form_PodcastPreferences extends Zend_Form_SubForm
{
    public function init()
    {
        $isPrivate = Application_Model_Preference::getStationPodcastPrivacy();
        $stationPodcastPrivacy = new Zend_Form_Element_Radio('stationPodcastPrivacy');
        $stationPodcastPrivacy->setLabel(_('Feed Privacy'));
        $stationPodcastPrivacy->setMultiOptions([
            _('Public'),
            _('Private'),
        ]);
        $stationPodcastPrivacy->setSeparator(' ');
        $stationPodcastPrivacy->addDecorator('HtmlTag', [
            'tag' => 'dd',
            'id' => 'stationPodcastPrivacy-element',
            'class' => 'radio-inline-list',
        ]);
        $stationPodcastPrivacy->setValue($isPrivate);
        $this->addElement($stationPodcastPrivacy);
    }
}
