<?php

use Airtime\MediaItem\Webstream;

use Airtime\MediaItem\WebstreamQuery;

class Application_Service_WebstreamService
{
	public function makeWebstreamForm($id, $populate = false) {

		try {
			$form = new Application_Form_Webstream();
			
			if ($populate) {
				
				$webstream = WebstreamQuery::create()->findPk($id);
				$length = $webstream->getHoursMins();
				
				$formValues = array(
					"id" => $id,
					"name" => $webstream->getName(),
					"description" => $webstream->getDescription(),
					"url" => $webstream->getUrl(),
					"hours" => $length[0],
					"mins" => $length[1]
				);
				
				$form->populate($formValues);
			}
			
			return $form;
		}
		catch (Exception $e) {
			Logging::info($e);
			throw $e;
		}
	}
	
	private function buildFromFormValues($ws, $values) {
	
		$hours = intval($values["hours"]);
		$minutes = intval($values["mins"]);
	
		if ($minutes > 59) {
			//minutes cannot be over 59. Need to convert anything > 59 minutes into hours.
			$hours += intval($minutes/60);
			$minutes = $minutes%60;
		}

		$length = "$hours:$minutes";
		
		$ws->setName($values["name"]);
		$ws->setDescription($values["description"]);
		$ws->setUrl($values["url"]);
		$ws->setLength($length);
		
		return $ws;
	}
	
	public function createWebstream($values) {
		
		$ws = new Webstream();
		$ws = self::buildFromFormValues($ws, $values);
		
		return $ws;
	}
	
	public function updateWebstream($values) {
		
		$ws = WebstreamQuery::create()->findPk($values["id"]);
		$ws = self::buildFromFormValues($ws, $values);
		
		return $ws;
	}
	
	public function saveWebstream($values) {
		
		if ($values["id"] != null) {
			$ws = self::updateWebstream($values);
		}
		else {
			$ws = self::createWebstream($values);
		}
		$ws->save();
		
		return $ws;
	}
	
	public function deleteWebstreams($ids) {
		
		WebstreamQuery::create()->findPks($ids)->delete();
	}
}