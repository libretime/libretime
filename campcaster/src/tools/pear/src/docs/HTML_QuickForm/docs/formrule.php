<?php
/**
* Examples of usage for HTML_QuickForm: fancy validation with addFormRule()
*
* $Id: formrule.php,v 1.1 2003/12/20 21:05:32 avb Exp $
* 
* @author      Alexey Borzov <avb@php.net>
* @version     3.2
*/

require_once 'HTML/QuickForm.php';

function _validate_shipping($values)
{
    // In Real Life (tm) you will probably query your DB for these
    $profiles = array('foo', 'bar', 'baz');
    $errors   = array();
    switch ($values['profile']) {
        case 'personal': 
            if (empty($values['persProfileName'])) {
                $errors['persProfileName'] = 'Enter the profile name';
            } elseif (in_array($values['persProfileName'], $profiles)) {
                $errors['persProfileName'] = 'The profile already exists';
            }
            if (empty($values['persName']['first']) || empty($values['persName']['last'])) {
                $errors['persName'] = 'Name is required';
            }
            if (empty($values['persAddress'])) {
                $errors['persAddress'] = 'Address is required';
            }
            break;

        case 'company': 
            if (empty($values['compProfileName'])) {
                $errors['compProfileName'] = 'Enter the profile name';
            } elseif (in_array($values['compProfileName'], $profiles)) {
                $errors['compProfileName'] = 'The profile already exists';
            }
            if (empty($values['compName'])) {
                $errors['compName'] = 'Company name is required';
            }
            if (empty($values['compAddress'])) {
                $errors['compAddress'] = 'Address is required';
            }
            break;

        case 'existing': 
        default:
            if (empty($values['profileName'])) {
                $errors['profileName'] = 'Enter the profile name';
            } elseif (!in_array($values['profileName'], $profiles)) {
                $errors['profileName'] = 'The profile does not exist';
            }
            break;
    } // switch
    return empty($errors)? true: $errors;
}

$form =& new HTML_QuickForm('frmFancy');
$form->setDefaults(array(
    'profile'     => 'existing',
    'stuffAmount' => '1'
));
$renderer =& $form->defaultRenderer();
$renderer->setElementTemplate("\n\t<tr>\n\t\t<td style=\"white-space: nowrap; background-color: #F0F0F0;\" align=\"left\" valign=\"top\" colspan=\"2\"><b>{element}</b></td>\n\t</tr>", 'profile');

$form->addElement('header', null, 'Choose stuff');
$form->addElement('select', 'stuffName', 'Stuff to send:', array('' => '--select--', 'n' => 'Nuts', 'b' => 'Bolts', 'f' => 'Flotsam', 'j' => 'Jetsam'));
$form->addElement('text',   'stuffAmount', 'Amount of stuff:', array('size' => 2, 'maxlength' => 2));


$form->addElement('header', null, 'Choose shipping profile');
$form->addElement('static', 'note', 'Note:', 'profiles \'foo\', \'bar\' and \'baz\' are considered existing');

$form->addElement('radio',  'profile', null, 'Use existing profile', 'existing');
$form->addElement('text',   'profileName', 'Profile name:', array('size' => 32, 'maxlength' => 32));

$form->addElement('radio',  'profile', null, 'New personal profile', 'personal');
$form->addElement('text',   'persProfileName', 'Profile name:', array('size' => 32, 'maxlength' => 32));
$name[] =& $form->createElement('text', 'first', null, array('size' => 14, 'maxlength' => 100));
$name[] =& $form->createElement('text', 'last', null, array('size' => 14, 'maxlength' => 100));
$form->addGroup($name, 'persName', 'Name (first, last):', ' ');
$form->addElement('text',   'persAddress', 'Address:', array('size' => 32, 'maxlength' => 255));

$form->addElement('radio',  'profile', null, 'New company profile', 'company');
$form->addElement('text',   'compProfileName', 'Profile name:', array('size' => 32, 'maxlength' => 32));
$form->addElement('text',   'compName', 'Company name:', array('size' => 32, 'maxlength' => 100));
$form->addElement('text',   'compAddress', 'Address:', array('size' => 32, 'maxlength' => 255));

$form->addElement('submit', null, 'Send');

$form->addFormRule('_validate_shipping');

if ($form->validate()) {
    echo "<pre>\n";
    var_dump($form->exportValues());
    echo "</pre>\n";
}

$form->display();
?>
