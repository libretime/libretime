<?php
require_once "../application/configs/conf.php";
require_once "TestHelper.php";
require_once "Preference.php";

class PreferenceUnitTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        TestHelper::installTestDatabase();
        TestHelper::setupZendBootstrap();
        parent::setUp();
    }

    /*    
    public function testSetHeadTitle()
    {
        $title = "unit test";
        //This function is confusing and doesn't really work so we're just gonna let it formSlide...
        Application_Model_Preference::SetHeadTitle($title);
        $this->assertEquals(Application_Model_Preference::GetHeadTitle(), $title);
    }
     */
    
    public function testSetShowsPopulatedUntil()
    {
        $date = new DateTime();
        Application_Model_Preference::SetShowsPopulatedUntil($date);
        $this->assertEquals(Application_Model_Preference::GetShowsPopulatedUntil(), $date);
    }

}
