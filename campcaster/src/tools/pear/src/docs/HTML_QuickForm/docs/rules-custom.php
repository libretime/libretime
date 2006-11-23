<?php
/**
 * Usage example for HTML_QuickForm, using custom validation rules.
 *
 * @author Alexey Borzov <avb@php.net>
 *
 * $Id: rules-custom.php,v 1.2 2003/11/03 20:45:23 avb Exp $ 
 */

require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Rule.php';

class RuleNumericRange extends HTML_QuickForm_Rule
{
    function validate($value, $options)
    {
        if (isset($options['min']) && floatval($value) < $options['min']) {
            return false;
        }
        if (isset($options['max']) && floatval($value) > $options['max']) {
            return false;
        }
        return true;
    }

    function getValidationScript($options = null)
    {
        $jsCheck = array();
        if (isset($options['min'])) {
            $jsCheck[] = 'Number({jsVar}) >= ' . $options['min'];
        }
        if (isset($options['max'])) {
            $jsCheck[] = 'Number({jsVar}) <= ' . $options['max'];
        }
        return array('', "{jsVar} != '' && !(" . implode(' && ', $jsCheck) . ')');
    } // end func getValidationScript
}

// In case you are wondering, this checks whether there are too many
// CAPITAL LETTERS in the string
function countUpper($value, $limit = null)
{
    if (empty($value)) {
        return false;
    }
    if (!isset($limit)) {
        $limit = 0.5;
    }
    $upper = array_filter(preg_split('//', $value, -1, PREG_SPLIT_NO_EMPTY), 'ctype_upper');
    return (count($upper) / strlen($value)) <= $limit;
}

// BC thingie: it expects the first param to be element name
function countUpper_old($name, $value, $limit = null)
{
    if (empty($value)) {
        return false;
    }
    if (!isset($limit)) {
        $limit = 0.5;
    }
    $upper = array_filter(preg_split('//', $value, -1, PREG_SPLIT_NO_EMPTY), 'ctype_upper');
    return (count($upper) / strlen($value)) <= $limit;
}

$form =& new HTML_QuickForm('custom');

$form->addElement('header', null, 'Custom rule class');

// registering the custom rule class
$form->registerRule('numRange', null, 'RuleNumericRange');
$form->addElement('text', 'rNumber_1_10', 'The number (1-10):');
$form->addRule('rNumber_1_10', 'Enter number from 1 to 10', 'numRange', array('min' => 1, 'max' => 10), 'client');

// adding an instance of the custom rule class without registering
$form->addElement('text', 'rNonnegative', 'Nonnegative number:');
$form->addRule('rNonnegative', 'Enter nonnegative number', new RuleNumericRange(), array('min' => 0), 'client');

// adding a classname of the custom rule class without registering
$form->addElement('text', 'rNonpositive', 'Nonpositive number:');
$form->addRule('rNonpositive', 'Enter nonpositive number', 'RuleNumericRange', array('max' => 0), 'client');

$form->addElement('header', null, 'Using callbacks');

// using callback without registering
$form->addElement('text', 'rUpper_0_5', 'Some (preferrably lowercase) text:');
$form->addRule('rUpper_0_5', 'There are too many CAPITAL LETTERS', 'callback', 'countUpper');

// register with 'callback' type
$form->registerRule('upper', 'callback', 'countUpper');
$form->addElement('text', 'rUpper_0_25', 'Some (mostly lowercase) text:');
$form->addRule('rUpper_0_25', 'There are too many CAPITAL LETTERS', 'upper', 0.25);

// BC feature: register with 'function' type
$form->registerRule('upperOld', 'function', 'countUpper_old');
$form->addElement('text', 'rUpper_0', 'Some lowercase text:');
$form->addRule('rUpper_0', 'There are CAPITAL LETTERS, this is not allowed', 'upperOld', 0);

$form->addElement('submit', null, 'Send');

$form->applyFilter(array('rUpper_0_5', 'rUpper_0_25', 'rUpper_0'), 'trim');
$form->applyFilter(array('rNumber_1_10', 'rNonnegative', 'rNonpositive'), 'floatval');

$form->validate();

$form->display();
?>
