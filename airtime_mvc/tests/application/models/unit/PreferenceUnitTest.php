<?php
require_once "../application/configs/conf.php";

class PreferenceUnitTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        TestHelper::installTestDatabase();
        TestHelper::setupZendBootstrap();
        parent::setUp();
    }

    public function testSetShowsPopulatedUntil()
    {
        $date = new DateTime();
        Application_Model_Preference::SetShowsPopulatedUntil($date);
        $this->assertEquals(Application_Model_Preference::GetShowsPopulatedUntil(), $date);
    }

}
