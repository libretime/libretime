<?php

class Application_Common_OsPath
{
    // this function is from https://stackoverflow.com/questions/2670299/is-there-a-php-equivalent-function-to-the-python-os-path-normpath
    public static function normpath($path)
    {
        if (empty($path)) {
            return '.';
        }

        if (strpos($path, '/') === 0) {
            $initial_slashes = true;
        } else {
            $initial_slashes = false;
        }
        if (
            $initial_slashes
            && (strpos($path, '//') === 0)
            && (strpos($path, '///') === false)
        ) {
            $initial_slashes = 2;
        }
        $initial_slashes = (int) $initial_slashes;

        $comps = explode('/', $path);
        $new_comps = [];
        foreach ($comps as $comp) {
            if (in_array($comp, ['', '.'])) {
                continue;
            }
            if (
                ($comp != '..')
                || (!$initial_slashes && !$new_comps)
                || ($new_comps && (end($new_comps) == '..'))
            ) {
                array_push($new_comps, $comp);
            } elseif ($new_comps) {
                array_pop($new_comps);
            }
        }
        $comps = $new_comps;
        $path = implode('/', $comps);
        if ($initial_slashes) {
            $path = str_repeat('/', $initial_slashes) . $path;
        }
        if ($path) {
            return $path;
        }

        return '.';
    }

    /* Similar to the os.path.join python method
     * https://stackoverflow.com/a/1782990/276949 */
    public static function join()
    {
        $args = func_get_args();
        $paths = [];

        foreach ($args as $arg) {
            $paths = array_merge($paths, (array) $arg);
        }

        foreach ($paths as &$path) {
            $path = trim($path, DIRECTORY_SEPARATOR);
        }

        if (substr($args[0], 0, 1) == DIRECTORY_SEPARATOR) {
            $paths[0] = DIRECTORY_SEPARATOR . $paths[0];
        }

        return implode(DIRECTORY_SEPARATOR, $paths);
    }

    public static function formatDirectoryWithDirectorySeparators($dir)
    {
        if ($dir[0] != '/') {
            $dir = '/' . $dir;
        }

        if ($dir[strlen($dir) - 1] != '/') {
            $dir .= '/';
        }

        return $dir;
    }
}
