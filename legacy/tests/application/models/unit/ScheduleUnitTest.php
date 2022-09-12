<?php

/**
 * @internal
 *
 * @coversNothing
 */
class ScheduleUnitTest extends Zend_Test_PHPUnit_ControllerTestCase // PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        TestHelper::installTestDatabase();
        TestHelper::setupZendBootstrap();

        parent::setUp();
    }

    public function testCheckOverlappingShows()
    {
    }

    public function testIsFileScheduledInTheFuture()
    {
        TestHelper::loginUser();
        $CC_CONFIG = Config::getConfig();

        $testShowData = ShowServiceData::getNoRepeatNoRRData();
        $showService = new Application_Service_ShowService();
        $futureDate = new DateTime();
        $futureDate->add(new DateInterval('P1Y')); // 1 year into the future
        $futureDateString = $futureDate->format('Y-m-d');

        $testShowData['add_show_start_date'] = $futureDateString;
        $testShowData['add_show_end_date'] = $futureDateString;
        $testShowData['add_show_end_date_no_repeat'] = $futureDateString;

        // Fudge the "populated until" date to workaround and issue where the default
        // value will prevent anything from actually being scheduled. Normally this isn't
        // a problem because as soon as you view the calendar for the first time, this is
        // set to a week ahead in the future.
        $populateUntil = new DateTime('now', new DateTimeZone('UTC'));
        $populateUntil = $populateUntil->add(new DateInterval('P2Y')); // 2 years ahead in the future.
        Application_Model_Preference::SetShowsPopulatedUntil($populateUntil);

        // $showService->setCcShow($testShowData); //Denise says this is not needed.
        $showService->addUpdateShow($testShowData); // Create show instances

        // Moved creation of stor directory to TestHelper for setup

        // Insert a fake file into the database
        $request = $this->getRequest();
        $params = $request->getParams();
        $params['action'] = '';
        $params['api_key'] = $CC_CONFIG['apiKey'][0];
        $request->setParams($params);

        $metadata = [
            'MDATA_KEY_FILEPATH' => '/tmp/foobar.mp3',
            'MDATA_KEY_DURATION' => '00:01:00',
            'is_record' => false,
        ];

        // Create the file in the database via the HTTP API.
        $apiController = new ApiController($this->request, $this->getResponse());
        $results = $apiController->dispatchMetadata($metadata, 'create');
        $fileId = $results['fileid'];
        $this->assertNotEquals($fileId, -1);
        $this->assertEquals($fileId, 1);

        // The file should not be scheduled in the future (or at all) at this point
        $scheduleModel = new Application_Model_Schedule();
        $scheduleModel->IsFileScheduledInTheFuture($fileId);
        $this->assertEquals($scheduleModel->IsFileScheduledInTheFuture($fileId), false);

        // Schedule the fake file in the test show, which should be in the future.
        $showInstance = new Application_Model_ShowInstance(1);
        $showInstance->addFileToShow($fileId);

        // Test the function we actually want to test. :-)
        $this->assertEquals($scheduleModel->IsFileScheduledInTheFuture($fileId), true);
    }
}
