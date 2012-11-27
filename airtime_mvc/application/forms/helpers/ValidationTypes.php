<?php
Class Application_Form_Helper_ValidationTypes {

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

}