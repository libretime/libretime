<?php

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
            $explodeResult = explode(".", $value);
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
