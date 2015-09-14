<?php

require_once "Enum.php";
final class MediaType extends Enum {

    const __default = self::FILE;

    const FILE = 1;
    const PLAYLIST = 2;
    const BLOCK = 3;
    const WEBSTREAM = 4;
    const PODCAST = 5;

}