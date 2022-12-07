<?php

declare(strict_types=1);

class Application_Form_ShowBuilder extends Zend_Form_SubForm
{
    public function init()
    {
        $user = Application_Model_User::getCurrentUser();

        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/showbuilder.phtml']],
        ]);

        // Add start date element
        $startDate = new Zend_Form_Element_Text('sb_date_start');
        $startDate->class = 'input_text';
        $startDate->setRequired(true)
            ->setLabel(_('Date Start:'))
            ->setValue(date('Y-m-d'))
            ->setFilters(['StringTrim'])
            ->setValidators([
                'NotEmpty',
                ['date', false, ['YYYY-MM-DD']],
            ])
            ->setDecorators(['ViewHelper']);
        $startDate->setAttrib('alt', 'date');
        $this->addElement($startDate);

        // Add start time element
        $startTime = new Zend_Form_Element_Text('sb_time_start');
        $startTime->class = 'input_text';
        $startTime->setRequired(true)
            ->setValue('00:00')
            ->setFilters(['StringTrim'])
            ->setValidators([
                'NotEmpty',
                ['date', false, ['HH:mm']],
                ['regex', false, ['/^[0-2]?[0-9]:[0-5][0-9]$/', 'messages' => _('Invalid character entered')]],
            ])
            ->setDecorators(['ViewHelper']);
        $startTime->setAttrib('alt', 'time');
        $this->addElement($startTime);

        // Add end date element
        $endDate = new Zend_Form_Element_Text('sb_date_end');
        $endDate->class = 'input_text';
        $endDate->setRequired(true)
            ->setLabel(_('Date End:'))
            ->setValue(date('Y-m-d'))
            ->setFilters(['StringTrim'])
            ->setValidators([
                'NotEmpty',
                ['date', false, ['YYYY-MM-DD']],
            ])
            ->setDecorators(['ViewHelper']);
        $endDate->setAttrib('alt', 'date');
        $this->addElement($endDate);

        // Add end time element
        $endTime = new Zend_Form_Element_Text('sb_time_end');
        $endTime->class = 'input_text';
        $endTime->setRequired(true)
            ->setValue('01:00')
            ->setFilters(['StringTrim'])
            ->setValidators([
                'NotEmpty',
                ['date', false, ['HH:mm']],
                ['regex', false, ['/^[0-2]?[0-9]:[0-5][0-9]$/', 'messages' => _('Invalid character entered')]],
            ])
            ->setDecorators(['ViewHelper']);
        $endTime->setAttrib('alt', 'time');
        $this->addElement($endTime);

        // add a select to choose a show.
        $showSelect = new Zend_Form_Element_Select('sb_show_filter');
        $showSelect->setLabel(_('Filter by Show'));
        $showSelect->setMultiOptions($this->getShowNames());
        $showSelect->setValue(null);
        $showSelect->setDecorators(['ViewHelper']);
        $this->addElement($showSelect);

        if ($user->getType() === 'H') {
            $myShows = new Zend_Form_Element_Checkbox('sb_my_shows');
            $myShows->setLabel(_('All My Shows:'))
                ->setDecorators(['ViewHelper']);
            $this->addElement($myShows);
        }
    }

    private function getShowNames()
    {
        $user = Application_Model_User::getCurrentUser();
        $showNames = ['0' => _('Filter by Show')];
        if ($user->getType() === 'H') {
            $showNames['-1'] = _('My Shows');
        }

        $shows = CcShowQuery::create()
            ->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
            ->orderByDbName()
            ->find();

        foreach ($shows as $show) {
            $showNames[$show->getDbId()] = $show->getDbName();
        }

        return $showNames;
    }
}
