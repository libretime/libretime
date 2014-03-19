<?php

interface Interface_Playlistable
{
	public function generateContent(PropelPDO $con);
	public function shuffleContent(PropelPDO $con);
	public function clearContent(PropelPDO $con);
	public function getContents(PropelPDO $con);
}