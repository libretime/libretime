<?php

class Application_Form_Login extends Zend_Form
{
    public function init()
    {
        $CC_CONFIG = Config::getConfig();

        // Set the method for the display form to POST
        $this->setMethod('post');

        // If the request comes from an origin we consider safe, we disable the CSRF
        // token checking ONLY for the login page.
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request) {
            $refererUrl = $request->getHeader('referer');
            $originIsSafe = false;
            foreach (CORSHelper::getAllowedOrigins($request) as $safeOrigin) {
                if ($this->startsWith($safeOrigin, $refererUrl)) {
                    $originIsSafe = true;

                    break;
                }
            }
        }

        if (!$originIsSafe) {
            $this->addElement('hash', 'csrf', [
                'salt' => 'unique',
            ]);
        }

        $this->setDecorators([
            ['ViewScript', ['viewScript' => 'form/login.phtml']],
        ]);

        // Add username element
        $username = new Zend_Form_Element_Text('username');
        $username->setLabel(_('Username:'))
            ->setAttribs([
                'autofocus' => 'true',
                'class' => 'input_text',
                'required' => 'true',
            ])
            ->setValue((isset($CC_CONFIG['demo']) && $CC_CONFIG['demo'] == 1) ? 'admin' : '')
            ->addFilter('StringTrim')
            ->setDecorators(['ViewHelper'])
            ->setValidators(['NotEmpty']);
        $this->addElement($username);

        // Add password element
        $this->addElement('password', 'password', [
            'label' => _('Password:'),
            'class' => 'input_text',
            'required' => true,
            'value' => (isset($CC_CONFIG['demo']) && $CC_CONFIG['demo'] == 1) ? 'admin' : '',
            'filters' => ['StringTrim'],
            'validators' => [
                'NotEmpty',
            ],
            'decorators' => [
                'ViewHelper',
            ],
        ]);

        $locale = new Zend_Form_Element_Select('locale');
        $locale->setLabel(_('Language:'));
        $locale->setMultiOptions(Application_Model_Locale::getLocales());
        $locale->setDecorators(['ViewHelper']);
        $this->addElement($locale);
        $this->setDefaults([
            'locale' => Application_Model_Locale::getUserLocale(),
        ]);

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label' => _('Login'),
            'class' => 'ui-button ui-widget ui-state-default ui-button-text-only center',
            'decorators' => [
                'ViewHelper',
            ],
        ]);
    }

    /**
     * tests if a string starts with a given string.
     *
     * This method was pinched as is from phing since it was the only line of code
     * actually used from phing. I'm not 100% convinced why it was deemed necessary
     * in the first place as it is a rather simple method in the first place.
     *
     * For now it's here as a copy and we can refactor it away completely later.
     *
     * @see <https://github.com/phingofficial/phing/blob/41b2f54108018cf69aaa73904fade23e5adfd0cc/classes/phing/util/StringHelper.php>
     *
     * @param mixed $check
     * @param mixed $string
     *
     * @return bool
     */
    private function startsWith($check, $string)
    {
        if ($check === '' || $check === $string) {
            return true;
        }

        return (strpos($string, $check) === 0) ? true : false;
    }
}
