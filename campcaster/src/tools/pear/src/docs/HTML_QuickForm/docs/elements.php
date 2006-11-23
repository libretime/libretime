<?php
/**
* Example of usage for PEAR class HTML_QuickForm
*
* @author      Adam Daniel <adaniel1@eesus.jnj.com>
* @author      Bertrand Mansion <bmansion@mamasam.com>
* @author      Alexey Borzov <avb@php.net>
* @version     3.2
*
* $Id: elements.php,v 1.2 2004/03/22 10:05:09 mansion Exp $ 
*/

require_once 'HTML/QuickForm.php';

$form =& new HTML_QuickForm('frmTest', 'get');

// Use a two-label template for the elements that require some comments
$twoLabel = <<<_HTML
<tr valign="top">
    <td align="right">
        <!-- BEGIN required --><span style="color: #F00;">*</span><!-- END required --><b>{label}</b>
    </td>
    <td align="left">
        <!-- BEGIN error --><span style="color: #F00;">{error}</span><br /><!-- END error -->{element}
        <!-- BEGIN label_2 --><br /><span style="font-size: 80%;">{label_2}</span><!-- END label_2 -->
    </td>
</tr>
_HTML;

$renderer =& $form->defaultRenderer();
$renderer->setElementTemplate($twoLabel, 'iadvChk');
$renderer->setElementTemplate($twoLabel, 'iautoComp');

// Fills with some defaults values
$form->setDefaults(array(
    'itxtTest'  => 'Test Text Box',
    'itxaTest'  => 'Hello World',
    'ichkTest'  => true,
    'iradTest'  => 1,
    'iselTest'  => array('B', 'C'),
    'name'      => array('first'=>'Adam', 'last'=>'Daniel'),
    'phoneNo'   => array('513', '123', '3456'),
    'iradYesNo' => 'Y',
    'ichkABC'   => array('A'=>true,'B'=>true),
    'dateTest1' => array('d'=>11, 'm'=>1, 'Y'=>2003)
));

$form->setConstants(array(
    'dateTest3' => time()
));

// Elements will be displayed in the order they are declared
$form->addElement('header', '', 'Normal Elements');
// Classic form elements
$form->addElement('hidden', 'ihidTest', 'hiddenField');
$form->addElement('text', 'itxtTest', 'Test Text:');
$form->addElement('textarea', 'itxaTest', 'Test TextArea:', array('rows' => 3, 'cols' => 20));
$form->addElement('password', 'ipwdTest', 'Test Password:');
$form->addElement('checkbox', 'ichkTest', 'Test CheckBox:', 'Check the box');
$form->addElement('radio', 'iradTest', 'Test Radio Buttons:', 'Check the radio button #1', 1);
$form->addElement('radio', 'iradTest', '(Not a group)', 'Check the radio button #2', 2);
$form->addElement('button', 'ibtnTest', 'Test Button', array('onclick' => "alert('This is a test');"));
$form->addElement('reset', 'iresTest', 'Test Reset');
$form->addElement('submit', 'isubTest', 'Test Submit');
$form->addElement('image', 'iimgTest', 'http://pear.php.net/gifs/pear-icon.gif');
$select =& $form->addElement('select', 'iselTest', 'Test Select:', array('A'=>'A', 'B'=>'B','C'=>'C','D'=>'D'));
$select->setSize(5);
$select->setMultiple(true);

$form->addElement('header', '', 'Custom Elements');
// Date elements
$form->addElement('date', 'dateTest1', 'Date1:', array('format'=>'dmY', 'minYear'=>2010, 'maxYear'=>2001));
$form->addElement('date', 'dateTest2', 'Date2:', array('format'=>'d-F-Y H:i', 'language'=>'de', 'optionIncrement' => array('i' => 5)));
$form->addElement('date', 'dateTest3', 'Today is:', array('format'=>'l d M Y'));

$main[0] = "Pop";
$main[1] = "Rock";
$main[2] = "Classical";

$secondary[0][0] = "Belle & Sebastian";
$secondary[0][1] = "Elliot Smith";
$secondary[0][2] = "Beck";
$secondary[1][3] = "Noir Desir";
$secondary[1][4] = "Violent Femmes";
$secondary[2][5] = "Wagner";
$secondary[2][6] = "Mozart";
$secondary[2][7] = "Beethoven";

$opts[] = $main;
$opts[] = $secondary;

$hs =& $form->addElement('hierselect', 'ihsTest', 'Hierarchical select:', array('style' => 'width: 20em;'), '<br />');
$hs->setOptions($opts);

$form->addElement('advcheckbox', 'iadvChk', array('Advanced checkbox:', 'Unlike standard checkbox, this element <b>has</b> a value<br />when it is not checked.'), 'Check the box', null, array('off', 'on'));

$form->addElement('autocomplete', 'iautoComp', array('Your favourite fruit:', 'This is autocomplete element.<br />Start typing and see how it suggests possible completions.'), array('Pear', 'Orange', 'Apple'), array('size' => 30));


$form->addElement('header', '', 'Grouped Elements');
// Grouped elements
$name['last'] = &HTML_QuickForm::createElement('text', 'last', null, array('size' => 30));
$name['first'] = &HTML_QuickForm::createElement('text', 'first', null, array('size' => 20));
$form->addGroup($name, 'name', 'Name (last, first):', ',&nbsp;');
// Creates a group of text inputs
$areaCode = &HTML_QuickForm::createElement('text', '', null, array('size' => 3, 'maxlength' => 3));
$phoneNo1 = &HTML_QuickForm::createElement('text', '', null, array('size' => 3, 'maxlength' => 3));
$phoneNo2 = &HTML_QuickForm::createElement('text', '', null, array('size' => 4, 'maxlength' => 4));
$form->addGroup(array($areaCode, $phoneNo1, $phoneNo2), 'phoneNo', 'Telephone:', '-');

// Creates a radio buttons group
$radio[] = &HTML_QuickForm::createElement('radio', null, null, 'Yes', 'Y');
$radio[] = &HTML_QuickForm::createElement('radio', null, null, 'No', 'N');
$form->addGroup($radio, 'iradYesNo', 'Yes/No:');

// Creates a checkboxes group
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'A', null, 'A');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'B', null, 'B');
$checkbox[] = &HTML_QuickForm::createElement('checkbox', 'C', null, 'C');
$form->addGroup($checkbox, 'ichkABC', 'ABC:', '<br />');
// Creates a group of buttons to be displayed at the bottom of the form
$buttons[] = &HTML_QuickForm::createElement('submit', null, 'Submit');
$buttons[] = &HTML_QuickForm::createElement('reset', null, 'Reset');
$buttons[] = &HTML_QuickForm::createElement('image', 'iimgTest', 'http://pear.php.net/gifs/pear-icon.gif');
$buttons[] = &HTML_QuickForm::createElement('button', 'ibutTest', 'Test Button', array('onClick' => "alert('This is a test');"));
$form->addGroup($buttons, null, null, '&nbsp;', false);


// applies new filters to the element values
$form->applyFilter('__ALL__', 'trim');
// Adds some validation rules
$form->addRule('itxtTest', 'Test Text is a required field', 'required');
$form->addRule('itxaTest', 'Test TextArea is a required field', 'required');
$form->addRule('itxaTest', 'Test TextArea must be at least 5 characters', 'minlength', 5);
$form->addRule('ipwdTest', 'Password must be between 8 to 10 characters', 'rangelength', array(8, 10));

// Tries to validate the form
if ($form->validate()) {
    // Form is validated, then processes the data
    $form->freeze();
    $form->process('myProcess', false);
    echo "\n<HR>\n";
}

// Process callback
function myProcess($values)
{
    echo '<pre>';
    var_dump($values);
    echo '</pre>';
}

$form->display();
?>
