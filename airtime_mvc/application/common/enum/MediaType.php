<?php

final class MediaType {

    const __default = self::FILE;

    const FILE = 1;
    const PLAYLIST = 2;
    const BLOCK = 3;
    const WEBSTREAM = 4;
    const PODCAST = 5;

    public static function getDefault() {
        return static::__default;
    }

}