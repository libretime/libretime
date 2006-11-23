<?php
/**
 * Example of usage for HTML_QuickForm with ITDynamic renderer
 *
 * @author Alexey Borzov <borz_off@cs.msu.su>
 *
 * $Id: ITDynamic_example.php,v 1.3 2003/09/09 10:46:51 avb Exp $ 
 */

require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITDynamic.php';
// can use either HTML_Template_Sigma or HTML_Template_ITX
require_once 'HTML/Template/ITX.php';
// require_once 'HTML/Template/Sigma.php';

$form = new HTML_QuickForm('frmTest', 'post');

$form->setDefaults(array(
    'itxtTest'  => 'Test Text Box',
    'itxaTest'  => 'Hello World',
    'iselTest'  => array('B', 'C'),
    'name'      => array('first' => 'Alexey', 'last' => 'Borzov'),
    'iradYesNo' => 'Y',
    'ichkABCD'  => array('A'=>true,'D'=>true)
));

$form->addElement('header', '', 'Normal Elements');

$form->addElement('hidden', 'ihidTest', 'hiddenField');
// will be rendered in default qf_element block
$form->addElement('text', 'itxtTest', 'Test Text:');
// will be rendered in qf_textarea block, as it exists in template
$form->addElement('textarea', 'itxaTest', 'Test TextArea:', array('rows' => 5, 'cols' => 40));
// will be later assigned to qf_green, note that an array of labels is passed
$form->addElement('password', 'ipwdTest', array('Test Password:', 'The password is expected to be long enough.'));
$select =& $form->addElement('select', 'iselTest', 'Test Select:', array('A'=>'A', 'B'=>'B','C'=>'C','D'=>'D'));
$select->setSize(5);
$select->setMultiple(true);
$form->addElement('submit', 'isubTest', 'Test Submit');

$form->addElement('header', '', 'Grouped Elements');

// will be rendered in default qf_group block
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'A', null, 'A');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'B', null, 'B');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'C', null, 'C');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'D', null, 'D');
$form->addGroup($checkbox, 'ichkABCD', 'ABCD:', array('&nbsp;', '<br />'));

// fancygroup candidates
// will be rendered in qf_fancygroup_radio
$radio[] = &HTML_QuickForm::createElement('radio', null, null, 'Yes', 'Y');
$radio[] = &HTML_QuickForm::createElement('radio', null, null, 'No', 'N');
$form->addGroup($radio, 'iradYesNo', 'Yes/No:');

// will be rendered in qf_fancygroup_element
$name['first'] = &HTML_QuickForm::createElement('text', 'first', 'First:');
$name['first']->setSize(20);
$name['last'] = &HTML_QuickForm::createElement('text', 'last', 'Last:');
$name['last']->setSize(30);
$form->addGroup($name, 'name', 'Name');

// add some 'required' rules to show "stars" and (possible) errors...
$form->addRule('itxtTest', 'Test Text is a required field', 'required');
$form->addRule('itxaTest', 'Test TextArea is a required field', 'required');
$form->addRule('iradYesNo', 'Check Yes or No', 'required');
$form->addGroupRule('name', array('last' => array(array('Last name is required', 'required'))));

// try to validate the form
if ($form->validate()) {
    $form->freeze();
}

// create a template object and load the template file
// can use either HTML_Template_Sigma or HTML_Template_ITX
$tpl =& new HTML_Template_ITX('./templates');
// $tpl =& new HTML_Template_Sigma('./templates');

$tpl->loadTemplateFile('it-dynamic.html', true, true);

// create a renderer
$renderer =& new HTML_QuickForm_Renderer_ITDynamic($tpl);

// assign elements to blocks
$renderer->setElementBlock(array(
    'ipwdTest'  => 'qf_green',
    'iradYesNo' => 'qf_fancygroup',
    'name'      => 'qf_fancygroup'
));

// Black Magic :]
$form->accept($renderer);

// display the results
$tpl->show();
?>
