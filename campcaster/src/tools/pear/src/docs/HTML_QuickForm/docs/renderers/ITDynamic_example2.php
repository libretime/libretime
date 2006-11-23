<?php
/**
*Example of usage for HTML_QuickForm with ITDynamic renderer (2-column layout)
*
* @author      Adam Daniel <adaniel1@eesus.jnj.com>
* @author      Bertrand Mansion <bmansion@mamasam.com>
* @author      Alexey Borzov <borz_off@cs.msu.su>
* @version     3.0
*/
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITDynamic.php';
// can use either HTML_Template_Sigma or HTML_Template_ITX
require_once 'HTML/Template/ITX.php';
//require_once 'HTML/Template/Sigma.php';

$form = new HTML_QuickForm('frmTest', 'POST');

// Fills with some defaults values
$defaultValues['company']  = 'Mamasam';
$defaultValues['country']  = array();
$defaultValues['name']      = array('first'=>'Alexey', 'last'=>'Borzov');
$defaultValues['phone']   = array('513', '123', '4567');
$form->setDefaults($defaultValues);

// Hidden
$form->addElement('hidden', 'session', '1234567890');
$form->addElement('hidden', 'timer', '12345');
$form->addElement('hidden', 'ihidTest', 'hiddenField');

// Personal information
$form->addElement('header', 'personal_info', 'Personal Information');

$name['last'] = &HTML_QuickForm::createElement('text', 'first', 'First', 'size=10');
$name['first'] = &HTML_QuickForm::createElement('text', 'last', 'Last', 'size=10');
$form->addGroup($name, 'name', 'Name:', ',&nbsp;');

$areaCode = &HTML_QuickForm::createElement('text', '', null,'size=4 maxlength=3');
$phoneNo1 = &HTML_QuickForm::createElement('text', '', null, 'size=4 maxlength=3');
$phoneNo2 = &HTML_QuickForm::createElement('text', '', null, 'size=5 maxlength=4');
$form->addGroup(array($areaCode, $phoneNo1, $phoneNo2), 'phone', 'Telephone:', '-');

$form->addElement('text', 'email', 'Your email:');

$form->addElement('password', 'pass', 'Your password:', 'size=10');

// to finish the first column:
$form->addElement('static', null, null, 'first column');


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

// Creates a checkboxes group using an array of separators
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'A', null, 'A');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'B', null, 'B');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'C', null, 'C');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'D', null, 'D');
$form->addGroup($checkbox, 'destination', 'Destination:', array('&nbsp;', '<br />'));

// to finish the second column:
$form->addElement('static', null, null, 'second column');

// can't render these elements properly, so they are in the template
//$form->addElement('reset', 'reset', 'Reset');
//$form->addElement('submit', 'submit', 'Register');

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


// can use either HTML_Template_Sigma or HTML_Template_ITX
$tpl =& new HTML_Template_ITX('./templates');
// $tpl =& new HTML_Template_Sigma('./templates');

$tpl->loadTemplateFile('it-dynamic-2.html');

$renderer =& new HTML_QuickForm_Renderer_ITDynamic($tpl);
$renderer->setElementBlock(array(
    'name'     => 'qf_group_table',
    'address'  => 'qf_group_table'
));

$form->accept($renderer);

$tpl->show();
?>
