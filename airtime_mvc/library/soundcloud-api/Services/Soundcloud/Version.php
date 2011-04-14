<?php
/**
 * Soundcloud package version.
 *
 * @category Services
 * @package Services_Soundcloud
 * @author Anton Lindqvist <anton@qvister.se>
 * @copyright 2010 Anton Lindqvist <anton@qvister.se>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link http://github.com/mptre/php-soundcloud
 */
class Services_Soundcloud_Version {

    const MAJOR = 2;
    const MINOR = 1;
    const PATCH = 1;

    public static function get() {
        return implode('.', array(self::MAJOR, self::MINOR, self::PATCH));
    }

}
