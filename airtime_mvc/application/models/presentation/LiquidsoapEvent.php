<?php

class Presentation_LiquidsoapEvent
{
	/**
	 * Convert a time string in the format "YYYY-MM-DD HH:mm:SS"
	 * to "YYYY-MM-DD-HH-mm-SS".
	 *
	 * @param  string $p_time
	 * @return string
	 */
	public static function AirtimeTimeToPypoTime($p_time)
	{
		$p_time = substr($p_time, 0, 19);
		$p_time = str_replace(" ", "-", $p_time);
		$p_time = str_replace(":", "-", $p_time);
	
		return $p_time;
	}
	
	/**
	 * This function will ensure that an existing index in the
	 * associative array is never overwritten, instead appending
	 * _0, _1, _2, ... to the end of the key to make sure it is unique
	 */
	public static function appendScheduleItem(&$data, $time, $item)
	{
		$key = $time;
		$i = 0;
	
		while (array_key_exists($key, $data["media"])) {
			$key = "{$time}_{$i}";
			$i++;
		}
	
		$data["media"][$key] = $item;
	}
}