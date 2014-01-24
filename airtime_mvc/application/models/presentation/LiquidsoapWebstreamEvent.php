<?php

class Presentation_LiquidsoapWebstreamEvent extends Presentation_LiquidsoapEvent
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
		
		$startDT = $scheduledItem->getDbStarts(null);
		$endDT = $scheduledItem->getDbEnds(null);
		
		$uri = $mediaItem->getUrl();
		//$uri = "http://usa-crash.dnbradio.com:10128";
		
		//create an event to start stream buffering 5 seconds ahead of the streams actual time.
		$buffer_start = clone $startDT;
		$buffer_start->sub(new DateInterval("PT5S"));
		
		$stream_buffer_start = self::AirtimeTimeToPypoTime($buffer_start->format("Y-m-d H:i:s"));
		
		$start = Application_Model_Schedule::AirtimeTimeToPypoTime($startDT->format("Y-m-d H:i:s"));
		$end = Application_Model_Schedule::AirtimeTimeToPypoTime($endDT->format("Y-m-d H:i:s"));
		
		$event = array(
			'start' => $stream_buffer_start,
			'end' => $stream_buffer_start,
			'uri' => $uri,
			'row_id' => $scheduledItem->getDbId(),
			'type' => 'stream_buffer_start',
			'independent_event' => true
		);
		self::appendScheduleItem($data, $start, $event);
		
		$event = array(
			'id' => $scheduledItem->getDbMediaId(),
			'type' => 'stream_output_start',
			'row_id' => $scheduledItem->getDbId(),
			'uri' => $uri,
			'start' => $start,
			'end' => $end,
			'show_name' => $show->getDbName(),
			'independent_event' => true
		);
		self::appendScheduleItem($data, $start, $event);
		
		//since a stream never ends we have to insert an additional "kick stream" event. The "start"
		//time of this event is the "end" time of the stream minus 1 second.
		$stream_end = clone $endDT;
		$stream_end->sub(new DateInterval("PT1S"));
		$stream_end = self::AirtimeTimeToPypoTime($stream_end->format("Y-m-d H:i:s"));
		
		$event = array(
			'start' => $stream_end,
			'end' => $stream_end,
			'uri' => $uri,
			'type' => 'stream_buffer_end',
			'row_id' => $scheduledItem->getDbId(),
			'independent_event' => true
		);
		self::appendScheduleItem($data, $stream_end, $event);
		
		$event = array(
			'start' => $stream_end,
			'end' => $stream_end,
			'uri' => $uri,
			'type' => 'stream_output_end',
			'independent_event' => true
		);
		self::appendScheduleItem($data, $stream_end, $event);
	}
}