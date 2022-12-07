<?php

declare(strict_types=1);

require_once 'customfilters/ImageSize.php';

class Application_Form_AddShowStyle extends Zend_Form_SubForm
{
    public function init()
    {
        // Add show background-color input
        $this->addElement('text', 'add_show_background_color', [
            'label' => _('Background Colour:'),
            'class' => 'input_text',
            'filters' => ['StringTrim'],
        ]);

        $bg = $this->getElement('add_show_background_color');

        $bg->setDecorators([['ViewScript', [
            'viewScript' => 'form/add-show-style.phtml',
            'class' => 'big',
        ]]]);

        $stringLengthValidator = Application_Form_Helper_ValidationTypes::overrideStringLengthValidator(6, 6);
        $bg->setValidators([
            'Hex', $stringLengthValidator,
        ]);

        // Add show color input
        $this->addElement('text', 'add_show_color', [
            'label' => _('Text Colour:'),
            'class' => 'input_text',
            'filters' => ['StringTrim'],
        ]);

        $c = $this->getElement('add_show_color');

        $c->setDecorators([['ViewScript', [
            'viewScript' => 'form/add-show-style.phtml',
            'class' => 'big',
        ]]]);

        $c->setValidators([
            'Hex', $stringLengthValidator,
        ]);

        // Show the current logo
        $this->addElement('image', 'add_show_logo_current', [
            'label' => _('Current Logo:'),
        ]);

        $logo = $this->getElement('add_show_logo_current');
        $logo->setDecorators([
            ['ViewScript', [
                'viewScript' => 'form/add-show-style.phtml',
                'class' => 'big',
            ]],
        ]);
        // Since we need to use a Zend_Form_Element_Image proto, disable it
        $logo->setAttrib('disabled', 'disabled');

        // Button to remove the current logo
        $this->addElement('button', 'add_show_logo_current_remove', [
            'label' => '<span class="ui-button-text">' . _('Remove') . '</span>',
            'class' => 'ui-button ui-state-default ui-button-text-only',
            'escape' => false,
        ]);

        // Add show image input
        $upload = new Zend_Form_Element_File('add_show_logo');

        $upload->setLabel(_('Show Logo:'))
            ->setRequired(false)
            ->setDecorators(['File', ['ViewScript', [
                'viewScript' => 'form/add-show-style.phtml',
                'class' => 'big',
                'placement' => false,
            ]]])
            ->addValidator('Count', false, 1)
            ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
            ->addFilter('ImageSize');

        $this->addElement($upload);

        // Add image preview
        $this->addElement('image', 'add_show_logo_preview', [
            'label' => _('Logo Preview:'),
        ]);

        $preview = $this->getElement('add_show_logo_preview');
        $preview->setDecorators([['ViewScript', [
            'viewScript' => 'form/add-show-style.phtml',
            'class' => 'big',
        ]]]);
        $preview->setAttrib('disabled', 'disabled');

        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        $csrf_element = new Zend_Form_Element_Hidden('csrf');
        $csrf_element->setValue($csrf_namespace->authtoken)
            ->setRequired('true')
            ->removeDecorator('HtmlTag')
            ->removeDecorator('Label');
        $this->addElement($csrf_element);
    }

    public function disable()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if (
                $element->getType() != 'Zend_Form_Element_Hidden'
                // We should still be able to remove the show logo
                && $element->getName() != 'add_show_logo_current_remove'
            ) {
                $element->setAttrib('disabled', 'disabled');
            }
        }
    }

    public function hideShowLogo()
    {
        $this->removeElement('add_show_logo');
        $this->removeElement('add_show_logo_preview');
    }
}
