<?php

class Application_Form_WatchedDirPreferences extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences_watched_dirs.phtml'))
        ));

        $this->addElement('text', 'watchedFolder', array(
            'class'      => 'input_text',
            'label'      => 'Choose a Folder to Watch:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'value' => '',
            'decorators' => array(
                'ViewHelper'
            )
        ));
    }

    public function verifyChosenFolder() {

        $element = $this->getElement('watchedFolder');

        if (!is_dir($element->getValue())) {
            $element->setErrors(array('Not a valid Directory'));
            return false;
        }
        else {
            $element->setValue("");
            return true;
        }

    }

    public function setWatchedDirs() {

        $watched_dirs = MusicDir::getWatchedDirs();
        $i = 1;
        foreach($watched_dirs as $dir) {

            $text = new Zend_Form_Element_Text("watched_dir_$i");
            $text->setAttrib('class', 'input_text');
            $text->addFilter('StringTrim');
            $text->setValue($dir->getDirectory());
            $text->setDecorators(array('ViewHelper'));
            $this->addElement($text);

            $i = $i + 1;
        }
    }


}

