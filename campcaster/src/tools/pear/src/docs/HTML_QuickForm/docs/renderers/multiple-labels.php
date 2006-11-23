<?php
/**
 * Example of usage for QuickForm elements with multiple labels (using Default renderer)
 *
 * @author Jon Wood <jon@jellybob.co.uk>
 *
 * $Id: multiple-labels.php,v 1.1 2004/03/06 12:03:50 avb Exp $ 
 */

require_once 'HTML/QuickForm.php';

$template =
'<tr>
    <td align="right" valign="top">
        <!-- BEGIN required --><font color="red">*</font><!-- END required -->
        <b>{label}</b>
    </td>
    <td nowrap="nowrap" valign="top" align="left">
        {element}
        <!-- BEGIN error --><br/><font color="red">{error}</font><br/><!-- END error -->
        <!-- BEGIN label_2 --><br/><font size="-1">{label_2}</font><!-- END label_2 -->
    </td>
</tr>';

// Create the form, and add a header to it.
$form = new HTML_QuickForm('labels_example', 'post');
$form->addHeader('QuickForm Labels Example');

// Do the magic! Just pass your label to the element as an array!
$form->addElement('text', 'name', array('Name', 'The name that you would like to enter in this element.'));
$form->addElement('checkbox', 'check', array('Check Me!', 'If you check this box, it will have tick in it.'));

// More boring stuff.
$form->addElement('submit', null, 'Submit');

if ($form->validate()) {
    $form->freeze();
}

// customize the element template
$renderer =& $form->defaultRenderer();
$renderer->setElementTemplate($template);

// output the form
$form->display();
?>
