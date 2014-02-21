<?php

interface Strategy_HistoryItem
{
	public function insertHistoryItem($schedId, $con, $opts);
}