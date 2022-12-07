<?php

declare(strict_types=1);

class Application_Form_EditHistoryItem extends Application_Form_EditHistory
{
    public const ID_PREFIX = 'his_item_';

    public function init()
    {
        parent::init();

        $this->setDecorators([
            'PrepareElements',
            ['ViewScript', ['viewScript' => 'form/edit-history-item.phtml']],
        ]);

        /*
        $instance = new Zend_Form_Element_Select("instance_id");
        $instance->setLabel(_("Choose Show Instance"));
        $instance->setMultiOptions(array("0" => "-----------"));
        $instance->setValue(0);
        $instance->setDecorators(array('ViewHelper'));
        $this->addElement($instance);
        */

        $starts = new Zend_Form_Element_Text(self::ID_PREFIX . 'starts');
        $starts->setValidators([
            new Zend_Validate_Date(self::VALIDATE_DATETIME_FORMAT),
        ]);
        $starts->setAttrib('class', self::TEXT_INPUT_CLASS . ' datepicker');
        $starts->setAttrib('data-format', self::TIMEPICKER_DATETIME_FORMAT);
        $starts->addFilter('StringTrim');
        $starts->setLabel(_('Start Time'));
        $starts->setDecorators(['ViewHelper']);
        $starts->setRequired(true);
        $this->addElement($starts);

        $ends = new Zend_Form_Element_Text(self::ID_PREFIX . 'ends');
        $ends->setValidators([
            new Zend_Validate_Date(self::VALIDATE_DATETIME_FORMAT),
        ]);
        $ends->setAttrib('class', self::TEXT_INPUT_CLASS . ' datepicker');
        $ends->setAttrib('data-format', self::TIMEPICKER_DATETIME_FORMAT);
        $ends->addFilter('StringTrim');
        $ends->setLabel(_('End Time'));
        $ends->setDecorators(['ViewHelper']);
        // $ends->setRequired(true);
        $this->addElement($ends);
    }

    public function createFromTemplate($template, $required)
    {
        parent::createFromTemplate($template, $required);
    }

    public function populateShowInstances($possibleInstances, $default)
    {
        $possibleInstances['0'] = _('No Show');

        $instance = new Zend_Form_Element_Select('his_instance_select');
        // $instance->setLabel(_("Choose Show Instance"));
        $instance->setMultiOptions($possibleInstances);
        $instance->setValue($default);
        $instance->setDecorators(['ViewHelper']);
        $this->addElement($instance);
    }
}
