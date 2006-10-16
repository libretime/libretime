<?php
$mask = array(
    'BACKUP.schedule'   => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'constant'  => 'BACKUP.schedule'
        ),
        array(
            'element'   => 'mon',
            'type'      => 'checkbox',
            'label'     => 'Mon',
            'groupit'   => true
        ),
        array(
            'element'   => 'tue',
            'type'      => 'checkbox',
            'label'     => 'Tue',
            'groupit'   => true
        ),
        array(
            'element'   => 'wed',
            'type'      => 'checkbox',
            'label'     => 'Wed',
            'groupit'   => true
        ),
        array(
            'element'   => 'thu',
            'type'      => 'checkbox',
            'label'     => 'Thu',
            'groupit'   => true
        ),
        array(
            'element'   => 'fri',
            'type'      => 'checkbox',
            'label'     => 'Fri',
            'groupit'   => true
        ),
        array(
            'element'   => 'sat',
            'type'      => 'checkbox',
            'label'     => 'Sat',
            'groupit'   => true
        ),
        array(
            'element'   => 'sun',
            'type'      => 'checkbox',
            'label'     => 'Sun',
            'groupit'   => true
        ),
        array(
            'group'     => array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'),
            'label'     => 'Weeksdays'
        ),
        array(
            'element'   => 'date',
            'type'      => 'date',
            'label'     => 'Month/Day',
            'options'   => array(
                'format'            => 'md',
                'addEmptyOption'    => true,
                'emptyOptionValue'  => '*',
                'emptyOptionText'   => '*'
             )
        ),
        array(
            'element'   => 'time',
            'type'      => 'date',
            'label'     => 'Hour/Minute',
            'options'   => array(
                'format'            => 'Hi',
             )
        ),
        array(
            'element'   =>'cancel',
            'type'      =>'button',
            'label'     =>'Cancel',
            'attributes'=>array('onclick' => 'location.href="'.UI_BROWSER.'?act=BACKUP"'),
            'groupit'   => TRUE
        ),
        array(
            'element'   =>'Submit',
            'type'      =>'submit',
            'label'     =>'Submit',
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('cancel', 'Submit'),
            'label'     => ' '
        ),
    )
);
?>