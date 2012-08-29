<?php

class PasswordNotEmpty extends ConditionalNotEmpty
{
    public function isValid($value, $context = null)
    {
        $result = parent::isValid($value, $context);
        if (!$result) {
            // allow empty if username/email was set before and didn't change
            $storedUser = Application_Model_Preference::GetSoundCloudUser();
            if ($storedUser != '' && $storedUser == $context['SoundCloudUser']) {
                return true;
            }
        }

        return $result;
    }
}
