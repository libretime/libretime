<?php

declare(strict_types=1);

class Application_Form_AddShowRepeats extends Zend_Form_SubForm
{
    public function init()
    {
        $linked = new Zend_Form_Element_Checkbox('add_show_linked');
        $linked->setLabel(_('Link:'));
        $this->addElement($linked);

        // Add type select
        $this->addElement('select', 'add_show_repeat_type', [
            'required' => true,
            'label' => _('Repeat Type:'),
            'class' => ' input_select',
            'multiOptions' => [
                '0' => _('weekly'),
                '1' => _('every 2 weeks'),
                '4' => _('every 3 weeks'),
                '5' => _('every 4 weeks'),
                '2' => _('monthly'),
            ],
        ]);

        // Add days checkboxes
        $this->addElement(
            'multiCheckbox',
            'add_show_day_check',
            [
                'label' => _('Select Days:'),
                'required' => false,
                'multiOptions' => [
                    '0' => _('Sun'),
                    '1' => _('Mon'),
                    '2' => _('Tue'),
                    '3' => _('Wed'),
                    '4' => _('Thu'),
                    '5' => _('Fri'),
                    '6' => _('Sat'),
                ],
            ]
        );

        $repeatMonthlyType = new Zend_Form_Element_Radio('add_show_monthly_repeat_type');
        $repeatMonthlyType
            ->setLabel(_('Repeat By:'))
            ->setRequired(true)
            ->setMultiOptions(
                [2 => _('day of the month'), 3 => _('day of the week')]
            )
            ->setValue(2);
        $this->addElement($repeatMonthlyType);

        // Add end date element
        $this->addElement('text', 'add_show_end_date', [
            'label' => _('Date End:'),
            'class' => 'input_text',
            'value' => date('Y-m-d'),
            'required' => false,
            'filters' => ['StringTrim'],
            'validators' => [
                'NotEmpty',
                ['date', false, ['YYYY-MM-DD']],
            ],
        ]);

        // Add no end element
        $this->addElement('checkbox', 'add_show_no_end', [
            'label' => _('No End?'),
            'required' => false,
            'checked' => true,
        ]);
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
        if (!$formData['add_show_no_end']) {
            $start_timestamp = $formData['add_show_start_date'];
            $end_timestamp = $formData['add_show_end_date'];
            $showTimeZone = new DateTimeZone($formData['add_show_timezone']);

            // We're assuming all data is valid at this point (timezone, etc.).

            $startDate = new DateTime($start_timestamp, $showTimeZone);
            $endDate = new DateTime($end_timestamp, $showTimeZone);

            if ($endDate < $startDate) {
                $this->getElement('add_show_end_date')->setErrors([_('End date must be after start date')]);

                return false;
            }

            return true;
        }

        if (!isset($formData['add_show_day_check'])) {
            $this->getElement('add_show_day_check')->setErrors([_('Please select a repeat day')]);
        }

        return true;
    }
}
