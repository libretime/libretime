<?php

declare(strict_types=1);

/**
 * custom filter for image uploads.
 *
 * WARNING: you need to include this file directly when using it, it clashes with the
 * way zf1 Zend_Loader_PluginLoader expects it to be found. Another way around this
 * might be to rename the class and have the new name get loaded proper.
 *
 * Since this is only getting used in a few places I am re-adding the
 * require_once there to get this fixed for now.
 */
class Zend_Filter_ImageSize implements Zend_Filter_Interface
{
    public function filter($value)
    {
        if (!file_exists($value)) {
            throw new Zend_Filter_Exception('Image does not exist: ' . $value);
        }

        $image = imagecreatefromstring(file_get_contents($value));
        if (false === $image) {
            throw new Zend_Filter_Exception('Can\'t load image: ' . $value);
        }

        // find ratio to scale down to
        // TODO: pass 600 as parameter in the future
        $origWidth = imagesx($image);
        $origHeight = imagesy($image);
        $ratio = max($origWidth, $origHeight) / 600;

        if ($ratio > 1) {
            // img too big! create a scaled down image
            $newWidth = round($origWidth / $ratio);
            $newHeight = round($origHeight / $ratio);
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

            // determine type and store to disk
            $explodeResult = explode('.', $value);
            $type = strtolower($explodeResult[count($explodeResult) - 1]);
            $writeFunc = 'image' . $type;
            if ($type == 'jpeg' || $type == 'jpg') {
                imagejpeg($resized, $value, 100);
            } else {
                $writeFunc($resized, $value);
            }
        }

        return $value;
    }
}
