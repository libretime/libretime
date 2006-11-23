<?php
/**
 * Usage example for HTML_QuickForm, built-in validation rules.
 *
 * @author Alexey Borzov <avb@php.net>
 *
 * $Id: rules-builtin.php,v 1.4 2004/11/26 10:24:54 avb Exp $ 
 */

require_once 'HTML/QuickForm.php';

$form =& new HTML_QuickForm('builtin');

// We need an additional label below the element
$renderer =& $form->defaultRenderer();
$renderer->setElementTemplate(<<<EOT
<tr>
    <td align="right" valign="top" nowrap="nowrap"><!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required --><b>{label}</b></td>
    <td valign="top" align="left">
        <!-- BEGIN error --><span style="color: #ff0000">{error}</span><br /><!-- END error -->{element}
        <!-- BEGIN label_2 --><br/><span style="font-size: 80%">{label_2}</span><!-- END label_2 -->
    </td>
</tr>

EOT
);

$form->addElement('header', null, 'Required rule');
$form->addElement('text', 'rRequired', array('Required:', 'Rule type \'required\'<br />Note: when the field is not \'required\' and is empty, other validation rules will <b>not</b> be applied to it'));
$form->addRule('rRequired', 'The field is required', 'required', null, 'client');

// RangeLength rules
$form->addElement('header', null, 'Range based rules');
$form->addElement('text', 'rMaxLength', array('Maximum length check (5):', 'Rule type \'maxlength\', $format = 5'));
$form->addElement('text', 'rMinLength', array('Minimum length check (5):', 'Rule type \'minlength\', $format = 5'));
$form->addElement('text', 'rRangeLength', array('Length range check (5-10):', 'Rule type \'rangelength\', $format = array(5, 10)'));

$form->addRule('rMaxLength', 'Should be less than or equal to 5 symbols', 'maxlength', 5, 'client');
$form->addRule('rMinLength', 'Should be more than or equal to 5 symbols', 'minlength', 5, 'client');
$form->addRule('rRangeLength', 'Should be between 5 and 10 symbols', 'rangelength', array(5,10), 'client');

// Email rule
$form->addElement('header', null, 'Email rule');
$form->addElement('text', 'rEmail', array('Email check:', 'Rule type \'email\''));
$form->addRule('rEmail', 'Should contain a valid email', 'email', null, 'client');

// RegEx rules
$form->addElement('header', null, 'Regex based rules');
$form->addElement('text', 'rRegex', array('Letters \'A\', \'B\', \'C\' only:', 'Rule type \'regex\' with $format = \'/^[ABCabc]+$/\''));
$form->addElement('text', 'rLettersOnly', array('Letters only:', 'Rule type \'lettersonly\''));
$form->addElement('text', 'rAlphaNumeric', array('Alphanumeric:', 'Rule type \'alphanumeric\''));
$form->addElement('text', 'rNumeric', array('Numeric:', 'Rule type \'numeric\''));
$form->addElement('text', 'rNoPunctuation', array('No punctuation:', 'Rule type \'nopunctuation\''));
$form->addElement('text', 'rNonZero', array('Nonzero:', 'Rule type \'nonzero\''));

$form->addRule('rRegex', 'Should contain letters A, B, C only', 'regex', '/^[ABCabc]+$/', 'client');
$form->addRule('rLettersOnly', 'Should contain letters only', 'lettersonly', null, 'client');
$form->addRule('rAlphaNumeric', 'Should be alphanumeric', 'alphanumeric', null, 'client');
$form->addRule('rNumeric', 'Should be numeric', 'numeric', null, 'client');
$form->addRule('rNoPunctuation', 'Should contain no punctuation', 'nopunctuation', null, 'client');
$form->addRule('rNonZero', 'Should be nonzero', 'nonzero', null, 'client');

// Compare rule
$form->addElement('header', null, 'Compare rule');
$form->addElement('password', 'cmpPasswd', 'Password:');
$form->addElement('password', 'cmpRepeat', array('Repeat password:', 'Rule type \'compare\', added to array(\'cmpPasswd\', \'cmpRepeat\')'));
$form->addRule(array('cmpPasswd', 'cmpRepeat'), 'The passwords do not match', 'compare', null, 'client');

// File rules
$form->addElement('header', null, 'Uploaded file rules');
$form->addElement('file', 'tstUpload', array('Upload file:', 'Rule types: \'uploadedfile\', \'maxfilesize\' with $format = 10240, \'mimetype\' with $format = \'text/xml\', filename with $format = \'/\\.xml$/\'<br />Validation for files is obviously <b>server-side only</b>'));
$form->addRule('tstUpload', 'Upload is required', 'uploadedfile');
$form->addRule('tstUpload', 'File size should be less than 10kb', 'maxfilesize', 10240);
$form->addRule('tstUpload', 'File type should be text/xml', 'mimetype', 'text/xml');
$form->addRule('tstUpload', 'File name should be *.xml', 'filename', '/\\.xml$/');

$form->addElement('header', null, 'Submit the form');
$submit[] =& $form->createElement('submit', null, 'Send');
$submit[] =& $form->createElement('checkbox', 'clientSide', null, 'use client-side validation', array('checked' => 'checked', 'onclick' => "if (this.checked) {this.form.onsubmit = oldHandler;} else {oldHandler = this.form.onsubmit; this.form.onsubmit = null;}"));
$form->addGroup($submit, null, null, '&nbsp;', false);

$form->applyFilter('__ALL__', 'trim');

$form->validate();

$form->display();
?>
