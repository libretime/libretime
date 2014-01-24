<?php

interface Interface_Schedulable
{
	public function isSchedulable();
	public function isScheduled();
	public function isScheduledInFuture();
	public function getSchedulingLength();
	public function getSchedulingCueIn();
	public function getSchedulingCueOut();
	public function getSchedulingFadeIn();
	public function getSchedulingFadeOut();
	public function getScheduledContent();
}