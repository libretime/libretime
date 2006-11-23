<?php /** $Id: QuickHtml_example.php,v 1.1 2003/08/25 16:41:02 jrust Exp $ */ ?>
<html>
  <title>QuickForm Using QuickHtml Renderer</title>
<body>
<?php
/**
* Another example of usage for PEAR class HTML_QuickForm using the
* QuickHtml renderer.
*
* This renderer has three main distinctives: an easy way to create
* custom-looking forms, the ability to separate the creation of form
* elements from their display, and being able to use QuickForm in
* widget-based template systems.  See the online documentation for more
* info.
*
* @author      Jason Rust <jrust@rustyparts.com> 
*/

require_once ("HTML/QuickForm.php");
require_once ("HTML/QuickForm/Renderer/QuickHtml.php");
$form =& new HTML_QuickForm('tmp_form','POST');
// get our render
$renderer =& new HTML_QuickForm_Renderer_QuickHtml();
// create the elements
createElements($form);
// set their values
setValues($form);

// Do the magic of creating the form.  NOTE: order is important here: this must
// be called after creating the form elements, but before rendering them.
$form->accept($renderer);

// Because radio buttons have the same name we have to pass the value
// as well as the name in order to get the correct one.
$tmp_radio = ' Yes: ' . $renderer->elementToHtml('tmp_radio', 'Y');
$tmp_radio .= ' No: ' . $renderer->elementToHtml('tmp_radio', 'N');

$tmp_submit = $renderer->elementToHtml('tmp_reset');
$tmp_submit .= $renderer->elementToHtml('tmp_submit');

// Make our form table using some of the widget functions.
$data = '
<table border="0" cellpadding="0" cellspacing="2" bgcolor="#eeeeee" width="500">
  <tr style="font-weight: bold;">' . createHeaderCell('QuickForm using QuickHtml Renderer', 'center', 2) . '</tr>
  <tr>' . createFormCell($renderer->elementToHtml('tmp_textarea'), 'center', 2) . '</tr>
  <tr>' . createHeaderCell('Text box (element is part of an array)', 'left') .
          createHeaderCell('Yes or no?', 'right') . '</tr>
  <tr>' . createFormCell($renderer->elementToHtml('tmp_text[array]'), 'left') .
          createFormCell($tmp_radio, 'right') . '</tr>
  <tr>' . createHeaderCell('Phone Number (a group)', 'left') .
          createHeaderCell('Advanced Check Box?', 'right') . '</tr>
  <tr>' . createFormCell($renderer->elementToHtml('phone_num'), 'left') .
          createFormCell($renderer->elementToHtml('tmp_checkbox'), 'right') . '</tr>
  <tr>' . createHeaderCell('Today is:', 'left') .
          createHeaderCell('Multiple Select', 'right') . '</tr>
  <tr>' . createFormCell($renderer->elementToHtml('tmp_date'), 'left') .
          createFormCell($renderer->elementToHtml('tmp_multipleSelect[0]'), 'right') . '</tr>
  <tr>' . createFormCell($tmp_submit, 'center', 2) . '</tr>
</table>';

// Wrap the form and any remaining elements (i.e. hidden elements) into the form tags.
echo $renderer->toHtml($data);

echo "\n<HR> <b>Submitted Values: </b><br />\n";
echo "<pre>";
print_r($_POST);
// {{{ createElements()

// creates all the fields for the form
function createElements(&$form)
{
    // select list array
    $selectListArray = array(
        'windows'   => 'Windows',
        'linux'     => 'Linux',
        'irix'      => 'Irix',
        'mac'       => 'Mac',
    );

    $form->addElement('text','tmp_text[array]',null,array('size' => 10));
    $form->addElement('hidden','tmp_hidden', 'value');
    $form->addElement('textarea','tmp_textarea',null,array('cols' => 50, 'rows' => 10, 'wrap' => 'virtual'));
    $form->addElement('radio','tmp_radio',null,null,'Y');
    $form->addElement('radio','tmp_radio',null,null,'N');
    $text = array();
    $text[] =& HTML_QuickForm::createElement('text','',null,array('size' => 3));
    $text[] =& HTML_QuickForm::createElement('text','',null,array('size' => 4));
    $text[] =& HTML_QuickForm::createElement('text','',null,array('size' => 3));
    $form->addGroup($text, 'phone_num', null, '-');
    $form->addElement('advcheckbox','tmp_checkbox',null,'Please Check',null,array('not checked', 'checked'));
    $form->addElement('date', 'tmp_date', null, array('format'=>'D d M Y'));
    $form->addElement('select', 'tmp_multipleSelect[0]', null, $selectListArray, array('multiple' => 'multiple', 'size' => 4));
    $form->addElement('reset','tmp_reset','Reset Form');
    $form->addElement('submit','tmp_submit','Submit Form');
    $form->addRule('tmp_text[array]','Text length must be greater than 10','minlength',10,'client');
}

// }}}
// {{{ setValues()

// sets all the default and constant values for the form
function setValues(&$form)
{
    // Fills with some defaults values
    $defaultValues['tmp_textarea']  = '
Test Text Area

With line breaks';
    $defaultValues['phone_num'] = array('513', '123', '3456');
    $defaultValues['tmp_checkbox'] = 'checked';
    $defaultValues['tmp_multipleSelect'][0] = array('linux', 'mac');
    // Fill with some constant values.
    // Constant is not overridden by POST, GET, or defaultValues
    // when values are being filled in
    $constantValues['tmp_radio'] = 'Y';
    $constantValues['tmp_date'] = time();
    $constantValues['tmp_text']['array'] = 'constant';

    $form->setDefaults($defaultValues);
    $form->setConstants($constantValues);
}

// }}}
// {{{ createHeaderCell()

// creates a header cell
function createHeaderCell($text, $align, $colspan = 1)
{
    return '<td align="' . $align . '" width="50%" bgcolor="#cccccc" colspan="' . $colspan . '">' . $text . '</td>';
}

// }}}
// {{{ createFormCell()

// creates a form cell based on the element name
function createFormCell($elementHtml, $align, $colspan = 1)
{
    return '<td align="' . $align . '" width="50%" colspan="' . $colspan . '">' . 
           $elementHtml .
           '</td>';
}

// }}}
?>
</body>
</html>
