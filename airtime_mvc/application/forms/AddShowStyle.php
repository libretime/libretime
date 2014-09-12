<?php

require_once 'customfilters/ImageSize.php';

class Application_Form_AddShowStyle extends Zend_Form_SubForm
{

    public function init()
    {
       // Add show background-color input
        $this->addElement('text', 'add_show_background_color', array(
            'label'      => _('Background Colour:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        $bg = $this->getElement('add_show_background_color');

        $bg->setDecorators(array(array('ViewScript', array(
            'viewScript' => 'form/add-show-style.phtml',
            'class'      => 'big'
        ))));

        $stringLengthValidator = Application_Form_Helper_ValidationTypes::overrideStringLengthValidator(6, 6);
        $bg->setValidators(array(
            'Hex', $stringLengthValidator
        ));

    	// Add show color input
        $this->addElement('text', 'add_show_color', array(
            'label'      => _('Text Colour:'),
            'class'      => 'input_text',
            'filters'    => array('StringTrim')
        ));

        $c = $this->getElement('add_show_color');

        $c->setDecorators(array(array('ViewScript', array(
            'viewScript' => 'form/add-show-style.phtml',
            'class'      => 'big'
        ))));

        $c->setValidators(array(
                'Hex', $stringLengthValidator
        ));
        
        // Add show image input
        $fileCountValidator = Application_Form_Helper_ValidationTypes::overrideFileCountValidator(1);
        $fileSizeValidator = Application_Form_Helper_ValidationTypes::overrideFileSizeValidator(array('max' => '5120000'));
        $fileExtensionValidator = Application_Form_Helper_ValidationTypes::overrideFileExtensionValidator('jpg,png,gif');
        
        $upload = new Zend_Form_Element_File('upload');
        
        $upload->setLabel(_('Show Image:'))
        	   ->setRequired(false)
        	   ->setDecorators(array('File', array('ViewScript', array(
        				'viewScript' => 'form/add-show-style.phtml',
        				'class'		 => 'big',
        	   			'placement'  => false
        		))))
        	   ->addValidator('Count', false, 1)
        	   ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
        	   ->addFilter('ImageSize');
        $this->addElement($upload);
        
//         $this->addElement('file', 'add_show_image', array(
//         		'disableLoadDefaultDecorators' => true,
//         		'decorators'  => array('File', array('ViewScript', array(
//         				'viewScript' => 'form/add-show-style.phtml',
//         				'class'		 => 'big',
//         				'placement'  => false
//         		))),
//         		'label'       => _('Show Image:'),
//         		'class'       => 'input_file',
//         		'required'    => false,
//         		'validators'  => array(
//         				$fileCountValidator,
//         				$fileSizeValidator,
//         				$fileExtensionValidator),
//         		'destination' => '../public/images/upload',
//         		'method'	  => 'post'
//         ));
        
        // Change form enctype to accommodate file upload
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
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

}
