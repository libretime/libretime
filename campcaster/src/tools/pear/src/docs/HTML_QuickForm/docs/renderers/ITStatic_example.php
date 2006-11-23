<?php
/**
* Example of usage for PEAR class HTML_QuickForm
*
* @author      Bertrand Mansion <bmansion@mamasam.com>
* @version     2.0
*/

// $Id: ITStatic_example.php,v 1.4 2004/10/02 09:54:41 ths Exp $

require_once('HTML/QuickForm.php');
require_once('HTML/QuickForm/Renderer/ITStatic.php');
require_once('HTML/Template/ITX.php');


// Form name will be used to find the placeholders.

$form = new HTML_QuickForm('form', 'POST');

// Fills with some defaults values

$defaultValues['company']  = 'Mamasam';
$defaultValues['country']  = array();
$defaultValues['name']     = array('first'=>'Bertrand', 'last'=>'Mansion');
$defaultValues['phone']    = array('513', '123', '4567');
$form->setDefaults($defaultValues);

// Hidden

$form->addElement('hidden', 'session', '1234567890');

// Personal information

$form->addElement('header', 'personal', 'Personal Information');

$form->addElement('hidden', 'ihidTest', 'hiddenField');
$form->addElement('text', 'email', 'Your email:');
$form->addElement('password', 'pass', 'Your password:', 'size=10');
$name['last'] = &HTML_QuickForm::createElement('text', 'first', 'First', 'size=10');
$name['first'] = &HTML_QuickForm::createElement('text', 'last', 'Last', 'size=10');
$form->addGroup($name, 'name', 'Name:', ',&nbsp;');
$areaCode = &HTML_QuickForm::createElement('text', '', null,'size=4 maxlength=3');
$phoneNo1 = &HTML_QuickForm::createElement('text', '', null, 'size=4 maxlength=3');
$phoneNo2 = &HTML_QuickForm::createElement('text', '', null, 'size=5 maxlength=4');
$form->addGroup(array($areaCode, $phoneNo1, $phoneNo2), 'phone', 'Telephone:', '-');

// Company information

$form->addElement('header', 'company_info', 'Company Information');

$form->addElement('text', 'company', 'Company:', 'size=20');

$str[] = &HTML_QuickForm::createElement('text', '', null, 'size=20');
$str[] = &HTML_QuickForm::createElement('text', '', null, 'size=20');
$form->addGroup($str, 'street', 'Street:', '<br />');

$addr['zip'] = &HTML_QuickForm::createElement('text', 'zip', 'Zip', 'size=6 maxlength=10');
$addr['city'] = &HTML_QuickForm::createElement('text', 'city', 'City', 'size=15');
$form->addGroup($addr, 'address', 'Zip, city:');

$select = array('' => 'Please select...', 'AU' => 'Australia', 'FR' => 'France', 'DE' => 'Germany', 'IT' => 'Italy');
$form->addElement('select', 'country', 'Country:', $select);

$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'A', null, 'A');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'B', null, 'B');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'C', null, 'C');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'D', null, 'D');
$form->addGroup($checkbox, 'destination', 'Destination:', array('&nbsp;', '<br />'));

// Other elements

$form->addElement('checkbox', 'news', '', " Check this box if you don't want to receive our newsletter.");

$form->addElement('reset', 'reset', 'Reset');
$form->addElement('submit', 'submit', 'Register');

// Adds some validation rules

$form->addRule('email', 'Email address is required', 'required');
$form->addGroupRule('name', 'Name is required', 'required');
$form->addRule('pass', 'Password must be between 8 to 10 characters', 'rangelength', array(8, 10));
$form->addRule('country', 'Country is a required field', 'required');
$form->addGroupRule('destination', 'Please check at least two boxes', 'required', null, 2);
$form->addGroupRule('phone', 'Please fill all phone fields', 'required');
$form->addGroupRule('phone', 'Values must be numeric', 'numeric');

$AddrRules['zip'][0] = array('Zip code is required', 'required');
$AddrRules['zip'][1] = array('Zip code is numeric only', 'numeric');
$AddrRules['city'][0] = array('City is required', 'required');
$AddrRules['city'][1] = array('City is letters only', 'lettersonly');
$form->addGroupRule('address', $AddrRules);

// Tries to validate the form
if ($form->validate()) {
    // Form is validated, then freezes the data
    $form->freeze();
}

// Could be HTML_Template_Sigma('./templates')
$tpl =& new HTML_Template_ITX('./templates');
$tpl->loadTemplateFile('it-static.html');

$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$renderer->setRequiredTemplate('{label}<font color="red" size="1">*</font>');
$renderer->setErrorTemplate('<font color="orange" size="1">{error}</font><br />{html}');

$form->accept($renderer);

$tpl->show();

?>