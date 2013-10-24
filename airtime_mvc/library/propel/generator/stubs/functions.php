<?php

if (!function_exists('lcfirst')) {
    function lcfirst($string)
    {
        $string[0] = strtolower($string[0]);

        return $string;
    }
}
