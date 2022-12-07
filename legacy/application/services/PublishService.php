<?php

declare(strict_types=1);

class Application_Service_PublishService
{
    /**
     * @var array map of arbitrary source names to descriptive labels
     */
    private static $SOURCES = [
        'station_podcast' => 'My Podcast',
    ];

    /**
     * Publish or remove the file with the given file ID from the services
     * specified in the request data (ie. the station podcast).
     *
     * @param int   $fileId ID of the file to be published
     * @param array $data   request data containing what services to publish to
     */
    public static function publish($fileId, $data)
    {
        foreach ($data as $k => $v) {
            $service = PublishServiceFactory::getService($k);
            $service->{$v}($fileId);
        }
    }

    /**
     * For the file with the given ID, check external sources and generate
     * an array of source states and labels.
     *
     * Sources to which the file has been published should be passed back in a
     * "published" array, while sources to which the file has not been published
     * should be passed back in a "toPublish" array.
     *
     * @param int $fileId the ID of the file to check
     *
     * @return array array containing published and toPublish arrays. Has the form
     *               [
     *               "toPublish" => [
     *               "source" => "label",
     *               ...
     *               ]
     *               "published" => [
     *               "source" => "label",
     *               ...
     *               ]
     *               ]
     */
    public static function getSourceLists($fileId)
    {
        $sources = [];
        foreach (self::$SOURCES as $source => $label) {
            $service = PublishServiceFactory::getService($source);
            $status = $service->getPublishStatus($fileId);
            array_push($sources, [
                'source' => $source,
                'label' => _($label),
                'status' => $status,
            ]);
        }

        return $sources;
    }

    /**
     * Given a cc_file identifier, check if the associated file has
     * been published to any sources.
     *
     * @param int $fileId the cc_files identifier of the file to check
     *
     * @return bool true if the file has been published to any source,
     *              otherwise false
     */
    public static function isPublished($fileId)
    {
        foreach (self::$SOURCES as $source => $label) {
            $service = PublishServiceFactory::getService($source);
            // 1: published or -1: pending
            if (abs($service->getPublishStatus($fileId)) == 1) {
                return true;
            }
        }

        return false;
    }
}
