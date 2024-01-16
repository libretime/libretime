<?php

use PHPUnit\Framework\TestCase;

require_once '../application/configs/conf.php';

/**
 * @internal
 *
 * @coversNothing
 */
class PreferenceUnitTest extends TestCase
{
    public function setUp(): void
    {
        TestHelper::installTestDatabase();
        TestHelper::setupZendBootstrap();
        parent::setUp();
    }

    public function testSetShowsPopulatedUntil()
    {
        $date = new DateTime('2040-01-01T12:00:00.000000Z');
        Application_Model_Preference::SetShowsPopulatedUntil($date);
        $this->assertEquals(Application_Model_Preference::GetShowsPopulatedUntil(), $date);
    }
}
