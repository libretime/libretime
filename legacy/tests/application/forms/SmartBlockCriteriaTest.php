<?php

/**
 * @internal
 *
 * @coversNothing
 */
class SmartBlockCriteriaTest extends PHPUnit_Framework_TestCase
{
    public function setUp(): void
    {
        TestHelper::installTestDatabase();
        TestHelper::setupZendBootstrap();
        TestHelper::loginUser();
    }

    /**
     * A limit in minutes is a duration, not an item count: 600 minutes is ten
     * hours and has nothing to do with the 500 items maximum.
     */
    public function testMinuteLimitIsNotValidatedAsAnItemCount(): void
    {
        [$isValid, $messages] = $this->validateLimit('minutes', '600');

        $this->assertTrue($isValid, implode(', ', $messages));
    }

    /**
     * Durations can be fractional, only the item count has to be an integer.
     */
    public function testFractionalMinuteLimitIsValid(): void
    {
        [$isValid, $messages] = $this->validateLimit('minutes', '30.5');

        $this->assertTrue($isValid, implode(', ', $messages));
    }

    /**
     * The 24 hours maximum applies to minutes as well as to hours.
     */
    public function testMinuteLimitAboveTwentyFourHoursIsInvalid(): void
    {
        [$isValid, $messages] = $this->validateLimit('minutes', '1441');

        $this->assertFalse($isValid);
        $this->assertContains('24 hrs', implode(', ', $messages));
    }

    public function testHourLimitAboveTwentyFourHoursIsInvalid(): void
    {
        [$isValid, $messages] = $this->validateLimit('hours', '25');

        $this->assertFalse($isValid);
        $this->assertContains('24 hrs', implode(', ', $messages));
    }

    public function testEmptyLimitValueIsInvalid(): void
    {
        [$isValid] = $this->validateLimit('minutes', '');

        $this->assertFalse($isValid);
    }

    public function testItemLimitAboveFiveHundredIsInvalid(): void
    {
        [$isValid, $messages] = $this->validateLimit('items', '501');

        $this->assertFalse($isValid);
        $this->assertContains('500 is the max item limit', implode(', ', $messages));
    }

    public function testFractionalItemLimitIsInvalid(): void
    {
        [$isValid, $messages] = $this->validateLimit('items', '10.5');

        $this->assertFalse($isValid);
        $this->assertContains('integer', implode(', ', $messages));
    }

    /**
     * Validate a smart block criteria made of the given limit only.
     *
     * @return array{0: bool, 1: string[]}
     */
    private function validateLimit(string $modifier, string $value): array
    {
        $block = new CcBlock();
        $block->setDbName('smart block limit test');
        $block->setDbType('dynamic');
        $block->save();

        $form = new Application_Form_SmartBlockCriteria();
        $form->startForm($block->getDbId());

        $isValid = $form->isValid(['data' => BlockModelData::getLimitCriteria($modifier, $value)]);

        $messages = [];
        foreach ($form->getElement('sp_limit_value')->getMessages() as $message) {
            $messages[] = is_array($message) ? implode(', ', $message) : (string) $message;
        }

        $block->delete();

        return [$isValid, $messages];
    }
}
