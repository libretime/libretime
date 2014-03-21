<?php

class Format_AudioFileBitRate
{
    private $_audiofile;

    /*
     * @param string $bitrate (bits per second)
     */
    public function __construct($audiofile)
    {
        $this->_audiofile = $audiofile;
    }

    public function getBitRate()
    {
    	$bitrate = $this->_audiofile->getBitRate();
    	
        $kbps = bcdiv($bitrate, 1000, 0);

        if ($kbps == 0) {
            return "";
        } 
        else {
            return "$kbps Kbps";
        }
    }
}