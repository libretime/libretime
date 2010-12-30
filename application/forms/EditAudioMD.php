<?php

class Application_Form_EditAudioMD extends Zend_Form
{

    public function init()
    {
         // Set the method for the display form to POST
        $this->setMethod('post');

		// Add title field
        $this->addElement('text', 'track_title', array(
            'label'      => 'Title:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            )
        ));

		// Add artist field
        $this->addElement('text', 'artist_name', array(
            'label'      => 'Artist:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            )
        ));

		// Add bitrate field
       // $this->addElement('text', 'bit_rate', array(
       //     'label'      => 'Bitrate:',
		//	'attribs'    => array('disabled' => 'disabled')
        //));

		// Add album field
        $this->addElement('text', 'album_title', array(
            'label'      => 'Album:',
            'filters'    => array('StringTrim')
        ));

		// Add genre field
        $this->addElement('text', 'genre', array(
            'label'      => 'Genre:',
            'filters'    => array('StringTrim')
        ));

		// Add year field
        $this->addElement('text', 'year', array(
            'label'      => 'Year:',
            'filters'    => array('StringTrim'),
            'validators' => array(
				array('date', false, array('YYYY-MM-DD')),
        		array('date', false, array('YYYY'))
    		) 
        ));

		// Add label field
        $this->addElement('text', 'label', array(
            'label'      => 'Label:',
            'filters'    => array('StringTrim')
        ));

		// Add composer field
        $this->addElement('text', 'composer', array(
            'label'      => 'Composer:',
            'filters'    => array('StringTrim')
        ));

		// Add mood field
        $this->addElement('text', 'mood', array(
            'label'      => 'Mood:',
            'filters'    => array('StringTrim')
        ));

		// Add language field
        $this->addElement('text', 'language', array(
            'label'      => 'Language:',
            'filters'    => array('StringTrim')
        ));

		// Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Submit',
        ));
    }


}

