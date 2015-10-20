<?php

interface Publish {

    /**
     * Publish the file with the given file ID
     *
     * @param int $fileId ID of the file to be published
     *
     * @return void
     */
    public function publish($fileId);

    /**
     * Unpublish the file with the given file ID
     *
     * @param int $fileId ID of the file to be unpublished
     *
     * @return void
     */
    public function unpublish($fileId);

}