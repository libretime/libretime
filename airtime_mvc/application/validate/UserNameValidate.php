<?php

class Application_Validate_UserNameValidate extends Zend_Validate_Abstract
{
    const LOGIN = 'login';

    protected $_messageTemplates = array(
        self::LOGIN => "'%value%' is already taken"
    );

    public function isValid($value)
    {
        $this->_setValue($value);

        $count = CcSubjsQuery::create()->filterByDbLogin($value)->count();

        if ($count != 0) {
            $this->_error(self::LOGIN);
            return false;
        }

        return true;
    }
}

