<?php

declare(strict_types=1);

/**
 * Check if a field is empty but only when specific fields have specific values.
 *
 * WARNING: you need to include this file directly when using it, it clashes with the
 * way zf1 Zend_Loader_PluginLoader expects it to be found. Another way around this
 * might be to rename the class and have the new name get loaded proper.
 *
 * Since this is only getting used in a few places I am re-adding the
 * require_once there to get this fixed for now.
 */
class ConditionalNotEmpty extends Zend_Validate_Abstract
{
    public const KEY_IS_EMPTY = 'keyIsEmpty';

    protected $_messageTemplates;

    protected $_fieldValues;

    /**
     * Constructs a new ConditionalNotEmpty validator.
     *
     * @param array $fieldValues - the names and expected values of the fields we're depending on;
     *                           E.g., if we have a field that should only be validated when two other
     *                           fields PARENT_1 and PARENT_2 have values of '1' and '0' respectively, then
     *                           $fieldValues should contain ('PARENT_1'=>'1', 'PARENT_2'=>'0')
     */
    public function __construct($fieldValues)
    {
        $this->_fieldValues = $fieldValues;
        $this->_messageTemplates = [
            self::KEY_IS_EMPTY => _("Value is required and can't be empty"),
        ];
    }

    /**
     * Implements Zend_Validate_Abstract.
     * Given names and expected values of the fields we're depending on ($_fieldValues),
     * this function returns true if the expected values doesn't match the actual user input,
     * or if $value is not empty. Returns false otherwise.
     *
     * @param string $value   - this field's value
     * @param array  $context - names and values of the rest of the fields in this form
     *
     * @return bool - true if valid; false otherwise
     */
    public function isValid($value, $context = null)
    {
        if ($value != '') {
            return true;
        }

        if (is_array($context)) {
            foreach ($this->_fieldValues as $fieldName => $fieldValue) {
                if (!isset($context[$fieldName]) || $context[$fieldName] != $fieldValue) {
                    return true;
                }
            }
        } elseif (is_string($context)) {
            if (!isset($context) || $context != $fieldValue) {
                return true;
            }
        }

        $this->_error(self::KEY_IS_EMPTY);

        return false;
    }
}
