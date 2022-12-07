<?php

declare(strict_types=1);

interface Publish
{
    /**
     * Publish the file with the given file ID.
     *
     * @param int $fileId ID of the file to be published
     */
    public function publish($fileId);

    /**
     * Unpublish the file with the given file ID.
     *
     * @param int $fileId ID of the file to be unpublished
     */
    public function unpublish($fileId);

    /**
     * Fetch the publication status for the file with the given ID.
     *
     * @param int $fileId the ID of the file to check
     *
     * @return int 1 if the file has been published,
     *             0 if the file has yet to be published,
     *             -1 if the file is in a pending state,
     *             2 if the source is unreachable (disconnected)
     */
    public function getPublishStatus($fileId);
}
