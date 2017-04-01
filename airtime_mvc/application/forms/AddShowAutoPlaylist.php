<?php

class Application_Form_AddShowAutoPlaylist extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/add-show-autoplaylist.phtml'))
        ));

        $notEmptyValidator = Application_Form_Helper_ValidationTypes::overrideNotEmptyValidator();
        // retrieves the length limit for each char field
        // and store to assoc array
        $maxLens = Application_Model_Show::getMaxLengths();

        // Add autoplaylist checkbox element
        $this->addElement('checkbox', 'add_show_has_autoplaylist', array(
            'label'      => _('Auto Schedule Playlist ?'),
            'required'   => false,
            'class'      => 'input_text',
            'decorators'  => array('ViewHelper')
        ));
     
        $autoPlaylistSelect = new Zend_Form_Element_Select("add_show_autoplaylist_id");
        $autoPlaylistSelect->setLabel(_("Select Playlist"));
        $autoPlaylistSelect->setMultiOptions(Application_Model_Library::getPlaylistNames(true));
        $autoPlaylistSelect->setValue(null);
        $autoPlaylistSelect->setDecorators(array('ViewHelper'));
        $this->addElement($autoPlaylistSelect);
        // Add autoplaylist checkbox element
        $this->addElement('checkbox', 'add_show_autoplaylist_repeat', array(
            'label'      => _('Repeat AutoPlaylist Until Show is Full ?'),
            'required'   => false,
            'class'      => 'input_text',
            'decorators'  => array('ViewHelper')
        ));
    }

    public function disable()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element->getType() != 'Zend_Form_Element_Hidden') {
                $element->setAttrib('disabled','disabled');
            }
        }
    }

    public function makeReadonly()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element->getType() != 'Zend_Form_Element_Hidden') {
                $element->setAttrib('readonly','readonly');
            }
        }
    }
}
