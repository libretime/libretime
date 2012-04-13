<?php
class Application_Common_OsPath{
    // this function is from http://stackoverflow.com/questions/2670299/is-there-a-php-equivalent-function-to-the-python-os-path-normpath
    public static function normpath($path)
    {
        if (empty($path))
            return '.';
    
        if (strpos($path, '/') === 0)
            $initial_slashes = true;
        else
            $initial_slashes = false;
        if (
            ($initial_slashes) &&
            (strpos($path, '//') === 0) &&
            (strpos($path, '///') === false)
        )
            $initial_slashes = 2;
        $initial_slashes = (int) $initial_slashes;
    
        $comps = explode('/', $path);
        $new_comps = array();
        foreach ($comps as $comp)
        {
            if (in_array($comp, array('', '.')))
                continue;
            if (
                ($comp != '..') ||
                (!$initial_slashes && !$new_comps) ||
                ($new_comps && (end($new_comps) == '..'))
            )
                array_push($new_comps, $comp);
            elseif ($new_comps)
                array_pop($new_comps);
        }
        $comps = $new_comps;
        $path = implode('/', $comps);
        if ($initial_slashes)
            $path = str_repeat('/', $initial_slashes) . $path;
        if ($path)
            return $path;
        else
            return '.';
    }
}
