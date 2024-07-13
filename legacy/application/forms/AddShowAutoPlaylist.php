<?php

class Application_Form_AddShowAutoPlaylist extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/add-show-autoplaylist.phtml']],
        ]);

        $notEmptyValidator = Application_Form_Helper_ValidationTypes::overrideNotEmptyValidator();
        // retrieves the length limit for each char field
        // and store to assoc array
        $maxLens = Application_Model_Show::getMaxLengths();

        // Add autoplaylist checkbox element
        $this->addElement('checkbox', 'add_show_has_autoplaylist', [
            'label' => _('Add Autoloading Playlist ?'),
            'required' => false,
            'class' => 'input_text',
            'decorators' => ['ViewHelper'],
        ]);

        $autoPlaylistSelect = new Zend_Form_Element_Select('add_show_autoplaylist_id');
        $autoPlaylistSelect->setLabel(_('Select Playlist'));
        $autoPlaylistSelect->setMultiOptions(Application_Model_Library::getPlaylistNames(true));
        $autoPlaylistSelect->setValue(null);
        $autoPlaylistSelect->setDecorators(['ViewHelper']);
        $this->addElement($autoPlaylistSelect);
        // Add autoplaylist checkbox element
        $this->addElement('checkbox', 'add_show_autoplaylist_repeat', [
            'label' => _('Repeat Playlist Until Show is Full ?'),
            'required' => false,
            'class' => 'input_text',
            'decorators' => ['ViewHelper'],
        ]);

        // Add override intro playlist checkbox element
        $this->addElement('checkbox', 'add_show_override_intro_playlist', [
            'label' => _('Override Intro Playlist ?'),
            'required' => false,
            'class' => 'input_text',
            'decorators' => ['ViewHelper'],
        ]);

        $introPlaylistSelect = new Zend_Form_Element_Select('add_show_intro_playlist_id');
        $introPlaylistSelect->setLabel(_('Select Intro Playlist'));
        $introPlaylistSelect->setMultiOptions(Application_Model_Library::getPlaylistNames(true));
        $introPlaylistSelect->setValue(null);
        $introPlaylistSelect->setDecorators(['ViewHelper']);
        $this->addElement($introPlaylistSelect);

        // Add override outro playlist checkbox element
        $this->addElement('checkbox', 'add_show_override_outro_playlist', [
            'label' => _('Override Outro Playlist ?'),
            'required' => false,
            'class' => 'input_text',
            'decorators' => ['ViewHelper'],
        ]);

        $outroPlaylistSelect = new Zend_Form_Element_Select('add_show_outro_playlist_id');
        $outroPlaylistSelect->setLabel(_('Select Outro Playlist'));
        $outroPlaylistSelect->setMultiOptions(Application_Model_Library::getPlaylistNames(true));
        $outroPlaylistSelect->setValue(null);
        $outroPlaylistSelect->setDecorators(['ViewHelper']);
        $this->addElement($outroPlaylistSelect);
    }

    public function disable()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element->getType() != 'Zend_Form_Element_Hidden') {
                $element->setAttrib('disabled', 'disabled');
            }
        }
    }

    public function makeReadonly()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element->getType() != 'Zend_Form_Element_Hidden') {
                $element->setAttrib('readonly', 'readonly');
            }
        }
    }
}
