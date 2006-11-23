<?php
/**
* Example of usage for PEAR class HTML_QuickForm. 
* Using filters to clean up the submitted values.
*
* @version 3.2
*
* $Id: filters.php,v 1.1 2003/11/21 16:52:48 avb Exp $ 
*/

require_once 'HTML/QuickForm.php';

function _filterAustin($value) 
{
    return strtoupper($value).', GROOVY BABY!';
}

$form =& new HTML_QuickForm('frmTest', 'get');

$form->addElement('text', 'txtTest', 'Test Text to trim:');
$form->addRule('txtTest', 'Test text is required', 'required');

$phoneGrp[] =& $form->createElement('text', '', null, array('size' => 3, 'maxlength' => 3));
$phoneGrp[] =& $form->createElement('text', '', null, array('size' => 3, 'maxlength' => 3));
$phoneGrp[] =& $form->createElement('text', '', null, array('size' => 4, 'maxlength' => 4));
$form->addGroup($phoneGrp, 'phone', 'Telephone (will be converted to numbers):', '-');
$form->addGroupRule('phone', 'The phone is required', 'required', null, 3);

$form->addElement('text', 'txtAustin', 'Text for custom filter:');
$form->addRule('txtAustin', 'Custom filter text is required', 'required');

$form->addElement('submit', 'isubTest', 'Submit');

// now we apply the filters
$form->applyFilter('txtTest', 'trim');
// the filter will be applied recursively
$form->applyFilter('phone', 'intval');

if ($form->validate()) {
    // Here the filter is applied after validation
    $form->applyFilter('txtAustin', '_filterAustin');

    echo "<pre>\n";
    echo "Values before filter:\n\n";
    var_dump($form->getElementValue('txtTest'));
    echo "\n";
    var_dump($form->getElementValue('phone'));
    echo "\n";
    var_dump($form->getElementValue('txtAustin'));

    echo "\n\nValues after filter:\n\n";
    var_dump($form->exportValue('txtTest'));
    echo "\n";
    var_dump($form->exportValue('phone'));
    echo "\n";
    var_dump($form->exportValue('txtAustin'));
    echo "</pre>\n";
}

$form->display();
?>
