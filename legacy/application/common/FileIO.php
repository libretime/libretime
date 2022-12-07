<?php

declare(strict_types=1);

/**
 * Class Application_Common_FileIO contains helper functions for reading and writing files, and sending them over HTTP.
 */
class Application_Common_FileIO
{
    /**
     * Reads the requested portion of a file and sends its contents to the client with the appropriate headers.
     *
     * This HTTP_RANGE compatible read file function is necessary for allowing streaming media to be skipped around in.
     *
     * @param string $filePath - the full filepath or URL pointing to the location of the file
     * @param string $mimeType - the file's mime type. Defaults to 'audio/mp3'
     * @param int    $size     - the file size, in bytes
     *
     * @see https://groups.google.com/d/msg/jplayer/nSM2UmnSKKA/Hu76jDZS4xcJ
     * @see https://php.net/manual/en/function.readfile.php#86244
     */
    public static function smartReadFile($filePath, $size, $mimeType)
    {
        $fm = @fopen($filePath, 'rb');
        if (!$fm) {
            throw new LibreTimeFileNotFoundException($filePath);
        }

        // Note that $size is allowed to be zero. If that's the case, it means we don't
        // know the filesize, and we need to figure one out so modern browsers don't get
        // confused. This should only affect files imported by legacy upstream since
        // media monitor did not always set the proper size in the database but analyzer
        // seems to always have a value for this.
        if ($size === 0) {
            $fstats = fstat($fm);
            $size = $fstats['size'];
        }

        if ($size <= 0) {
            throw new Exception("Invalid file size returned for file at {$filePath}");
        }

        $begin = 0;
        $end = $size - 1;

        ob_start(); // Must start a buffer here for these header() functions

        if (isset($_SERVER['HTTP_RANGE'])) {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
                $begin = intval($matches[1]);
                if (!empty($matches[2])) {
                    $end = intval($matches[2]);
                }
            }
        }

        if (isset($_SERVER['HTTP_RANGE'])) {
            header('HTTP/1.1 206 Partial Content');
        } else {
            header('HTTP/1.1 200 OK');
        }
        header("Content-Type: {$mimeType}");
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes');
        header('Content-Length:' . (($end - $begin) + 1));
        if (isset($_SERVER['HTTP_RANGE'])) {
            header("Content-Range: bytes {$begin}-{$end}/{$size}");
        }

        // We can have multiple levels of output buffering. Need to
        // keep looping until all have been disabled!!!
        // https://www.php.net/manual/en/function.ob-end-flush.php
        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        // These two lines were removed from Airtime 2.5.x at some point after Libretime forked from Airtime.
        // These lines allow seek to work for files.
        // Issue #349
        $cur = $begin;
        fseek($fm, $begin, 0);

        while (!feof($fm) && (connection_status() == 0) && ($cur <= $end)) {
            echo fread($fm, 1024 * 8);
        }
        fclose($fm);
    }
}
