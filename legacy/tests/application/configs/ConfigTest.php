<?php

/**
 * @internal
 * @coversNothing
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testIsYesValue()
    {
        foreach (['yes', 'Yes', 'True', 'true', true] as $value) {
            $this->assertEquals(Config::isYesValue($value), true);
        }

        foreach (['no', 'No', 'False', 'false', false] as $value) {
            $this->assertEquals(Config::isYesValue($value), false);
        }

        foreach (['', 'anything', '0', 0, '1', 1, null] as $value) {
            $this->assertEquals(Config::isYesValue($value), false);
        }
    }
}
