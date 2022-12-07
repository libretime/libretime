<?php

declare(strict_types=1);

class Application_Form_AddShowRebroadcastDates extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/add-show-rebroadcast.phtml']],
        ]);

        $relativeDates = [];
        $relativeDates[''] = '';
        for ($i = 0; $i <= 30; ++$i) {
            $relativeDates["{$i} days"] = "+{$i} " . _('days');
        }

        for ($i = 1; $i <= 10; ++$i) {
            $select = new Zend_Form_Element_Select("add_show_rebroadcast_date_{$i}");
            $select->setAttrib('class', 'input_select');
            $select->setMultiOptions($relativeDates);
            $select->setRequired(false);
            $select->setDecorators(['ViewHelper']);
            $this->addElement($select);

            $text = new Zend_Form_Element_Text("add_show_rebroadcast_time_{$i}");
            $text->setAttrib('class', 'input_text');
            $text->addFilter('StringTrim');
            $text->addValidator('date', false, ['HH:mm']);
            $text->addValidator('regex', false, ['/^[0-2]?[0-9]:[0-5][0-9]$/', 'messages' => _('Invalid character entered')]);
            $text->setRequired(false);
            $text->setDecorators(['ViewHelper']);
            $this->addElement($text);
        }
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

    public function isValid($formData)
    {
        if (parent::isValid($formData)) {
            return $this->checkReliantFields($formData);
        }

        return false;
    }

    public function checkReliantFields($formData)
    {
        $noError = true;

        for ($i = 1; $i <= 10; ++$i) {
            $valid = true;
            $days = $formData['add_show_rebroadcast_date_' . $i];
            $time = $formData['add_show_rebroadcast_time_' . $i];

            if (trim($days) == '' && trim($time) == '') {
                continue;
            }

            if (trim($days) == '') {
                $this->getElement('add_show_rebroadcast_date_' . $i)->setErrors([_('Day must be specified')]);
                $valid = false;
            }

            if (trim($time) == '') {
                $this->getElement('add_show_rebroadcast_time_' . $i)->setErrors([_('Time must be specified')]);
                $valid = false;
            }

            if ($valid === false) {
                $noError = false;

                continue;
            }

            $days = explode(' ', $days);
            $day = $days[0];

            $show_start_time = $formData['add_show_start_date'] . ' ' . $formData['add_show_start_time'];
            $show_end = new DateTime($show_start_time);

            $duration = $formData['add_show_duration'];
            $duration = explode(':', $duration);

            $show_end->add(new DateInterval("PT{$duration[0]}H"));
            $show_end->add(new DateInterval("PT{$duration[1]}M"));
            $show_end->add(new DateInterval('PT1H')); // min time to wait until a rebroadcast

            $rebroad_start = $formData['add_show_start_date'] . ' ' . $formData['add_show_rebroadcast_time_' . $i];
            $rebroad_start = new DateTime($rebroad_start);
            $rebroad_start->add(new DateInterval('P' . $day . 'D'));

            if ($rebroad_start < $show_end) {
                $this->getElement('add_show_rebroadcast_time_' . $i)->setErrors([_('Must wait at least 1 hour to rebroadcast')]);
                $valid = false;
                $noError = false;
            }
        }

        return $noError;
    }
}
