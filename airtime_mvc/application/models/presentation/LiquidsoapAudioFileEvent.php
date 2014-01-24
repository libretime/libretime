<?php

class Presentation_LiquidsoapAudioFileEvent extends Presentation_LiquidsoapEvent
{
	private $_scheduledItem;
	
	/*
	 * @param CcSchedule $s
	*/
	public function __construct($s)
	{
		$this->_scheduledItem = $s;
	}
	
	public function createScheduleEvent(&$data)
	{
		//CcSchedule object
		$scheduledItem = $this->_scheduledItem;
		//AudioFile object
		$mediaItem = $scheduledItem->getMediaItem()->getChildObject();
		//CcShowObject
		$show = $scheduledItem->getCcShowInstances()->getCcShow();
		
		$startDt = $scheduledItem->getDbStarts(null);
		$endDt = $scheduledItem->getDbEnds(null);
		
		$start = self::AirtimeTimeToPypoTime($startDt->format("Y-m-d H:i:s"));
		$end = self::AirtimeTimeToPypoTime($endDt->format("Y-m-d H:i:s"));
		
		list(,,,$start_hour,,) = explode("-", $start);
		list(,,,$end_hour,,) = explode("-", $end);
		
		$same_hour = $start_hour == $end_hour;
		$independent_event = !$same_hour;
		
		if (!Application_Model_Preference::GetEnableReplayGain()) {
			$replay_gain = 0;
		}
		else {
			$gain = $mediaItem->getReplayGain();
			$replay_gain = is_null($gain) ? 0 : $gain;
			$replay_gain += Application_Model_Preference::getReplayGainModifier();
		}
		
		/*
		 * TODO if cueout is larger than the show's end time, it must be changed to be the show's end time.
		 */
		$event = array(
			'id' => $scheduledItem->getDbMediaId(),
			'type' => 'file',
			'row_id' => $scheduledItem->getDbId(),
			'uri' => $mediaItem->getFilepath(),
			'fade_in' => $scheduledItem->getDbFadeIn() * 100,
			'fade_out' => $scheduledItem->getDbFadeOut() * 100,
			'cue_in' => Application_Common_DateHelper::CalculateLengthInSeconds($scheduledItem->getDbCueIn()),
			'cue_out' => Application_Common_DateHelper::CalculateLengthInSeconds($scheduledItem->getDbCueOut()),
			'start' => $start,
			'end' => $end,
			'show_name' => $show->getDbName(),
			'replay_gain' => $replay_gain,
			'independent_event' => $independent_event,
		);
		
		self::appendScheduleItem($data, $event["start"], $event);
	}
}