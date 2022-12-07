<?php

declare(strict_types=1);

class Application_Form_Helper_ValidationTypes
{
    public static function overrideNotEmptyValidator()
    {
        $validator = new Zend_Validate_NotEmpty();
        $validator->setMessage(
            _("Value is required and can't be empty"),
            Zend_Validate_NotEmpty::IS_EMPTY
        );

        return $validator;
    }

    public static function overrideEmailAddressValidator()
    {
        $validator = new Zend_Validate_EmailAddress();
        $validator->setMessage(
            _("'%value%' is no valid email address in the basic format local-part@hostname"),
            Zend_Validate_EmailAddress::INVALID_FORMAT
        );

        return $validator;
    }

    public static function overrrideDateValidator($p_format)
    {
        $validator = new Zend_Validate_Date();

        $validator->setFormat($p_format);

        $validator->setMessage(
            _("'%value%' does not fit the date format '%format%'"),
            Zend_Validate_Date::FALSEFORMAT
        );

        return $validator;
    }

    public static function overrideRegexValidator($p_pattern, $p_msg)
    {
        $validator = new Zend_Validate_Regex($p_pattern);

        $validator->setMessage(
            $p_msg,
            Zend_Validate_Regex::NOT_MATCH
        );

        return $validator;
    }

    public static function overrideStringLengthValidator($p_min, $p_max)
    {
        $validator = new Zend_Validate_StringLength();
        $validator->setMin($p_min);
        $validator->setMax($p_max);

        $validator->setMessage(
            _("'%value%' is less than %min% characters long"),
            Zend_Validate_StringLength::TOO_SHORT
        );

        $validator->setMessage(
            _("'%value%' is more than %max% characters long"),
            Zend_Validate_StringLength::TOO_LONG
        );

        return $validator;
    }

    public static function overrideBetweenValidator($p_min, $p_max)
    {
        $validator = new Zend_Validate_Between($p_min, $p_max, true);

        $validator->setMessage(
            _("'%value%' is not between '%min%' and '%max%', inclusively"),
            Zend_Validate_Between::NOT_BETWEEN
        );

        return $validator;
    }

    public static function overridePasswordIdenticalValidator($p_matchAgainst)
    {
        $validator = new Zend_Validate_Identical();
        $validator->setToken($p_matchAgainst);

        $validator->setMessage(
            _('Passwords do not match'),
            Zend_Validate_Identical::NOT_SAME
        );

        return $validator;
    }
}
