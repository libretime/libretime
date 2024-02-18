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

        $playlistNames = Application_Model_Library::getPlaylistNames(true);

        // Add autoplaylist checkbox element
        $this->addElement('checkbox', 'add_show_has_autoplaylist', [
            'label' => _('Add Autoloading Playlist ?'),
            'required' => false,
            'class' => 'input_text',
            'decorators' => ['ViewHelper'],
        ]);

        $autoPlaylistSelect = new Zend_Form_Element_Select('add_show_autoplaylist_id');
        $autoPlaylistSelect->setLabel(_('Select Playlist'));
        $autoPlaylistSelect->setMultiOptions($playlistNames);
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

        $introPlaylistSelect = new Zend_Form_Element_Select('add_show_intro_playlist_id');
        $introPlaylistSelect->setLabel(_('Select Intro Playlist'));
        $introPlaylistSelect->setMultiOptions($playlistNames);
        $introPlaylistSelect->setValue(null);
        $introPlaylistSelect->setDecorators(['ViewHelper']);
        $this->addElement($introPlaylistSelect);

        $outroPlaylistSelect = new Zend_Form_Element_Select('add_show_outro_playlist_id');
        $outroPlaylistSelect->setLabel(_('Select Outro Playlist'));
        $outroPlaylistSelect->setMultiOptions($playlistNames);
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
