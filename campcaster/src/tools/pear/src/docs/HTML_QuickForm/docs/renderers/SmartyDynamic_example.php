<?php
/**
 * Example of usage for HTML_QuickForm Array renderer with Smarty template engine
 *
 * @author Thomas Schulz <ths@4bconsult.de>
 * @author Alexey Borzov <borz_off@cs.msu.su>
 *
 * $Id: SmartyDynamic_example.php,v 1.4 2004/10/15 20:31:00 ths Exp $
 */

require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/Array.php';
// fix this if your Smarty is somewhere else
require_once 'Smarty.class.php';

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

$form->addElement('text', 'itxtTest', array('Test Text', 'note' => 'Note for Testtext element.'));

$form->addElement('textarea', 'itxaTest', 'Test TextArea', 'cols="40" rows="2"');

// will be later assigned to style green
$form->addElement('password', 'ipwdTest', 'Test Password');
$select =& $form->addElement(
    'select',
    'iselTest',
    array('Test Select', 'note' => 'We recommend to check at least two categories!'),
    array('A'=>'A * * * * (luxory)', 'B'=>'B * * *','C'=>'C * *','D'=>'D * (simple)')
 );
$select->setSize(4);
$select->setMultiple(true);

$form->addElement('submit', 'isubTest', 'Test Submit');

$form->addElement('header', '', 'Grouped Elements');

$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'A', null, 'A');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'B', null, 'B');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'C', null, 'C');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'D', null, 'D');
$form->addGroup($checkbox, 'ichkABCD', 'ABCD', array('&nbsp;', '<br />'));

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

$renderer =& new HTML_QuickForm_Renderer_Array(true, true);

// give some elements aditional style informations
$renderer->setElementStyle(array(
    'ipwdTest'  => 'green',
    'iradYesNo' => 'fancygroup',
    'name'      => 'fancygroup'
));

$form->accept($renderer);

// setup a template object
$tpl =& new Smarty;
$tpl->template_dir = './templates';
$tpl->compile_dir  = './templates';

// assign array with form data
$tpl->assign('form', $renderer->toArray());

// capture the array stucture
// (only for showing in sample template)
ob_start();
print_r($renderer->toArray());
$tpl->assign('dynamic_array', ob_get_contents());
ob_end_clean();

// render and display the template
$tpl->display('smarty-dynamic.tpl');

?>