<?php
$mask = array(
    'addUser'   => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'constant'  => 'SUBJECTS.addSubj'
        ),
        array(
            'element'   => 'login',
            'type'      => 'text',
            'label'     => 'Login',
            'required'  => TRUE
        ),
        array(
            'element'   => 'passwd',
            'type'      => 'password',
            'label'     => 'Password',
            'required'  => TRUE
        ),
        array(
            'element'   => 'passwd2',
            'type'      => 'password',
            'label'     => 'Repeat password',
            'required'  => TRUE
        ),
        array(
            'element'   => 'cancel',
            'type'      => 'button',
            'label'     => 'Cancel',
            'attributes'=> array('onClick' => 'location.href="'.UI_BROWSER.'?act=SUBJECTS"'),
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'Submit',
            'type'      => 'submit',
            'label'     => 'Submit',
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('cancel', 'Submit')
        )
    ),

    'addGroup'   => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'constant'  => 'SUBJECTS.addSubj'
        ),
        array(
            'element'   => 'login',
            'type'      => 'text',
            'label'     => 'Groupname',
            'required'  => TRUE
        ),
        array(
            'element'   => 'cancel',
            'type'      => 'button',
            'label'     => 'Cancel',
            'attributes'=> array('onClick' => 'location.href="'.UI_BROWSER.'?act=SUBJECTS"'),
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'Submit',
            'type'      => 'submit',
            'label'     => 'Submit',
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('cancel', 'Submit')
        )
    ),

    'chgPasswd'   => array(
        array(
            'element'   => 'act',
            'type'      => 'hidden',
            'constant'  => 'SUBJECTS.chgPasswd'
        ),
        array(
            'element'   => 'login',
            'type'      => 'hidden'
        ),
        'oldpasswd' => array(
            'element'   => 'oldpasswd',
            'type'      => 'password',
            'label'     => 'Old password',
            'required'  => TRUE
        ),
        array(
            'element'   => 'passwd',
            'type'      => 'password',
            'label'     => 'Password',
            'required'  => TRUE
        ),
        array(
            'element'   => 'passwd2',
            'type'      => 'password',
            'label'     => 'Repeat password',
            'required'  => TRUE
        ),
        'cancel' => array(
            'element'   => 'cancel',
            'type'      => 'button',
            'label'     => 'Cancel',
            'groupit'   => TRUE
        ),
        array(
            'element'   => 'Submit',
            'type'      => 'submit',
            'label'     => 'Submit',
            'groupit'   => TRUE
        ),
        array(
            'group'     => array('cancel', 'Submit')
        )
    ),
);
?>