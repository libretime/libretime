<?php

declare(strict_types=1);

class Application_Validate_NotDemoValidate extends Zend_Validate_Abstract
{
    public const NOTDEMO = 'notdemo';

    protected $_messageTemplates = [
        self::NOTDEMO => 'Cannot be changed in demo mode',
    ];

    public function isValid($value)
    {
        $this->_setValue($value);

        $CC_CONFIG = Config::getConfig();
        if (isset($CC_CONFIG['demo']) && $CC_CONFIG['demo'] == 1) {
            $this->_error(self::NOTDEMO);

            return false;
        }

        return true;
    }
}
