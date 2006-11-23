<?php
/**
 * Example of usage for HTML_QuickForm Object renderer
 * with Flexy template engine and dynamic template
 *
 * @author Ron McClain <mixtli@cats.ucsc.edu>
 *
 * $Id: FlexyDynamic_example.php,v 1.2 2003/11/03 12:55:53 avb Exp $ 
 */

require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/Object.php';
require_once 'HTML/Template/Flexy.php';

$form = new HTML_QuickForm('frmTest', 'post');

$form->setDefaults(array(
    'itxtTest'  => 'Test Text Box',
    'itxaTest'  => 'Hello World',
    'iselTest'  => array('B', 'C'),
    'name'      => array('first' => 'Thomas', 'last' => 'Schulz'),
    'iradYesNo' => 'Y',
    'ichkABCD'  => array('A'=>true,'D'=>true)
));

$form->addElement('header', '', 'Normal Elements');

$form->addElement('hidden', 'ihidTest', 'hiddenField');

$form->addElement('text', 'itxtTest', 'Test Text');

$form->addElement('textarea', 'itxaTest', 'Test TextArea');

// will be later assigned to style green
$form->addElement('password', 'ipwdTest', array('Test Password', 'Please choose a password which is hard to guess'));
$select =& $form->addElement('select', 'iselTest', 'Test Select', array('A'=>'A', 'B'=>'B','C'=>'C','D'=>'D'));
$select->setSize(5);
$select->setMultiple(true);

$form->addElement('submit', 'isubTest', 'Test Submit');

$form->addElement('header', '', 'Grouped Elements');

$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'A', null, 'A');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'B', null, 'B');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'C', null, 'C');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'D', null, 'D');
$form->addGroup($checkbox, 'ichkABCD', 'ABCD', '<br />');

// will be later assigned to style fancygroup
$radio[] = &HTML_QuickForm::createElement('radio', null, null, 'Yes', 'Y');
$radio[] = &HTML_QuickForm::createElement('radio', null, null, 'No', 'N');
$form->addGroup($radio, 'iradYesNo', 'Yes/No');

// will be later assigned to style fancygroup
$name['first'] = &HTML_QuickForm::createElement('text', 'first', 'First:');
$name['first']->setSize(20);
$name['last'] = &HTML_QuickForm::createElement('text', 'last', 'Last:');
$name['last']->setSize(30);
$form->addGroup($name, 'name', 'Name');

// add some 'required' rules to show "stars" and (possible) errors...
$form->addRule('itxtTest', 'Test Text is a required field', 'required');
$form->addRule('itxaTest', 'Test TextArea is a required field', 'required');
$form->addGroupRule('iradYesNo', 'Check Yes or No', 'required');
$form->addGroupRule('name', array('last' => array(array('Last name is required', 'required'))));

// try to validate the form
if ($form->validate()) {
    $form->freeze();
}

$renderer =& new HTML_QuickForm_Renderer_Object(true);

// give some elements aditional style informations
$renderer->setElementStyle(array(
    'ipwdTest'  => 'green',
    'iradYesNo' => 'fancygroup',
    'name'      => 'fancygroup'
));

$form->accept($renderer);


$options = &PEAR::getStaticProperty('HTML_Template_Flexy','options');
$options = array(
	'templateDir' => './templates',
	'compileDir' => './templates/build',
	'debug' => 0
);
$tpl =& new HTML_Template_Flexy($options);

//$tpl->compile("styles/green.html");
//$tpl->compile("styles/fancygroup.html");

// assign array with form data
$view = new StdClass;
$view->form = $renderer->toObject();

// capture the array stucture 
// (only for showing in sample template)
ob_start();
print_r($renderer->toObject());
$view->dynamic_object =  ob_get_contents();
// XXX: dunno how to make Flexy ignore the placeholder
$view->formdata = '{formdata}';
ob_end_clean();

// render and display the template
$tpl->compile('flexy-dynamic.html');
$tpl->outputObject($view);
?>