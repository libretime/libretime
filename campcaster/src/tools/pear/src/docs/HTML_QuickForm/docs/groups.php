<?php
/**
* Examples of usage for grouped elements in HTML_QuickForm
*
* $Id: groups.php,v 1.1 2003/12/20 21:05:32 avb Exp $
* 
* @author      Bertrand Mansion <bmansion@mamasam.com>
* @author      Alexey Borzov <avb@php.net>
* @version     3.2
*/

require_once 'HTML/QuickForm.php';

$form =& new HTML_QuickForm('frmGroups');
$form->setDefaults(array(
    'id'        => array('lastname' => 'Mamasam', 'code' => '1234'),
    'phoneNo'   => array('513', '123', '3456'),
    'ichkABC'   => array('A'=>true)
));

$renderer =& $form->defaultRenderer();

// Setting templates for form and headers
$renderer->setFormTemplate("<form{attributes}>\n<table width=\"450\" border=\"0\" cellpadding=\"3\" cellspacing=\"2\" bgcolor=\"#CCCC99\">\n{content}\n</table>\n</form>");
$renderer->setHeaderTemplate("\t<tr>\n\t\t<td style=\"white-space:nowrap;background:#996;color:#ffc;\" align=\"left\" colspan=\"2\"><b>{header}</b></td>\n\t</tr>");

// Setting a special template for id element
$renderer->setGroupTemplate('<table><tr>{content}</tr></table>', 'id');
$renderer->setGroupElementTemplate('<td>{element}<br /><span style="font-size:10px;"><!-- BEGIN required --><span style="color: #f00">* </span><!-- END required --><span style="color:#996;">{label}</span></span></td>', 'id');


$form->addElement('header', '', 'Tests on grouped elements');

// Creates a group of text inputs with templates
$id['lastname'] = &HTML_QuickForm::createElement('text', 'lastname', 'Name', array('size' => 30));
$id['code'] = &HTML_QuickForm::createElement('text', 'code', 'Code', array('size' => 5, 'maxlength' => 4));
$form->addGroup($id, 'id', 'ID:', ',&nbsp');

// Add a complex rule for id element
$form->addGroupRule('id', array(
    'lastname' => array(
        array('Name is required', 'required', null, 'client'),
        array('Name is letters only', 'lettersonly', null, 'client')
    ),
    'code'     => array(
        array('Code must be numeric', 'numeric', null, 'client')
    )
));


// Creates a group of text inputs
$areaCode = &HTML_QuickForm::createElement('text', '', null, array('size' => 4, 'maxlength' => 3));
$phoneNo1 = &HTML_QuickForm::createElement('text', '', null, array('size' => 4, 'maxlength' => 3));
$phoneNo2 = &HTML_QuickForm::createElement('text', '', null, array('size' => 5, 'maxlength' => 4));
$form->addGroup(array($areaCode, $phoneNo1, $phoneNo2), 'phoneNo', 'Telephone:', '-');

// Adds validation rules for groups
$form->addGroupRule('phoneNo', 'Please fill all phone fields', 'required', null, 3, 'client');
$form->addGroupRule('phoneNo', 'Values must be numeric', 'numeric', null, 3, 'client');

// Creates a checkboxes group using an array of separators
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'A', null, 'A');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'B', null, 'B');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'C', null, 'C');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'D', null, 'D');
$form->addGroup($checkbox, 'ichkABC', 'ABCD:', array('&nbsp;', '<br />'));

// At least one element is required
$form->addGroupRule('ichkABC', 'Please check at least two boxes', 'required', null, 2, 'client', true);

// Creates a standard radio buttons group
$radio[] = &HTML_QuickForm::createElement('radio', null, null, 'Yes', 'Y');
$radio[] = &HTML_QuickForm::createElement('radio', null, null, 'No', 'N');
$form->addGroup($radio,  'iradYesNo', 'Yes/No:');

// Validate the radio buttons
$form->addRule('iradYesNo', 'Check Yes or No', 'required', null, 'client');

// Creates a group of buttons to be displayed at the bottom of the form
$buttons[] =& $form->createElement('submit', null, 'Submit');
$buttons[] =& $form->createElement('reset', null, 'Reset');
$buttons[] =& $form->createElement('checkbox', 'clientSide', null, 'use client-side validation', array('checked' => 'checked', 'onclick' => "if (this.checked) {this.form.onsubmit = validate_" . $form->getAttribute('id') . ";} else {this.form.onsubmit = null;}"));
$form->addGroup($buttons);


// Tries to validate the form
if ($form->validate()) {
    // Form is validated, then processes the data
    $form->freeze();
    $form->process('var_dump');
    echo "\n<HR>\n";
}
$form->display();

?>
