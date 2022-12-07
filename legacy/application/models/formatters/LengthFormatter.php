<?php

declare(strict_types=1);

class LengthFormatter
{
    /**
     * @string length
     */
    private $_length;

    // @param string $length formatted H:i:s.u (can be > 24 hours)
    public function __construct($length)
    {
        $this->_length = $length;
    }

    public function format()
    {
        if (!preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}/', $this->_length)) {
            return $this->_length;
        }

        $pieces = explode(':', $this->_length);

        $seconds = round($pieces[2], 1);
        $seconds = number_format($seconds, 1);
        [$seconds, $milliStr] = explode('.', $seconds);

        if (intval($pieces[0]) !== 0) {
            $hours = ltrim($pieces[0], '0');
        }

        $minutes = $pieces[1];
        // length is less than 1 hour
        if (!isset($hours)) {
            if (intval($minutes) !== 0) {
                $minutes = ltrim($minutes, '0');
            }
            // length is less than 1 minute
            else {
                unset($minutes);
            }
        }

        if (isset($hours, $minutes, $seconds)) {
            $time = sprintf('%d:%02d:%02d.%s', $hours, $minutes, $seconds, $milliStr);
        } elseif (isset($minutes, $seconds)) {
            $time = sprintf('%d:%02d.%s', $minutes, $seconds, $milliStr);
        } else {
            $time = sprintf('%d.%s', $seconds, $milliStr);
        }

        return $time;
    }
}
