<?php

interface Interface_Playlistable
{
	public function generate();
	public function shuffle();
	public function clear();
	public function getContents();
	public function getScheduledContent();
}