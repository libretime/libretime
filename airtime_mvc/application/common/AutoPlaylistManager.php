<?php

class AutoPlaylistManager {
    /**
     * @var int how often, in seconds, to check for and ingest new podcast episodes
     */
    private static $_AUTOPLAYLIST_POLL_INTERVAL_SECONDS = 60;  // 10 minutes
    
    /**
     * Check whether $_AUTOPLAYLIST_POLL_INTERVAL_SECONDS have passed since the last call to
     * buildAutoPlaylist
     *
     * @return bool true if $_AUTOPLAYLIST_POLL_INTERVAL_SECONDS has passed since the last check
     */
    public static function hasAutoPlaylistPollIntervalPassed() {
        $lastPolled = Application_Model_Preference::getAutoPlaylistPollLock();
        return empty($lastPolled) || (microtime(true) > $lastPolled + self::$_AUTOPLAYLIST_POLL_INTERVAL_SECONDS);
    }

     /**
     * Find all shows with autoplaylists who have yet to have their playlists built and added to the schedule
     *
     */
    public static function buildAutoPlaylist() {
        $autoPlaylists = static::_upcomingAutoPlaylistShows();
        foreach ($autoPlaylists as $autoplaylist) {
            // creates a ShowInstance object to build the playlist in from the ShowInstancesQuery Object     
            $si = new Application_Model_ShowInstance($autoplaylist->getDbId());
            $playlistid = $si->GetAutoPlaylistId();
            // call the addPlaylist to show function and don't check for user permission to avoid call to non-existant user object
            $sid = $si->getShowId();
            $playlistrepeat = new Application_Model_Show($sid);
            $introplaylistid = Application_Model_Preference::GetIntroPlaylist();
            $outroplaylistid = Application_Model_Preference::GetOutroPlaylist();

            // we want to check and see if we need to repeat this process until the show is 100% scheduled
            // so we create a while loop and break it immediately if repeat until full isn't enabled
            // otherwise we continue to go through adding playlists, including the intro and outro if enabled
            $full = false;
            $repeatuntilfull = $playlistrepeat->getAutoPlaylistRepeat();
            $tempPercentScheduled = 0;
            while(!$full) {
                $si = new Application_Model_ShowInstance($autoplaylist->getDbId());
                // we do not want to try to schedule an empty playlist
                if ($playlistid != null) {
                    $si->addPlaylistToShow($playlistid, false);
                }
                $ps = $si->getPercentScheduled();
                if ($introplaylistid != null) {
                    //Logging::info('adding intro');
                    $si->addPlaylistToShowStart($introplaylistid, false);
                }
                if ($outroplaylistid != null) {
                    //Logging::info('adding outro');
                    $si->addPlaylistToShow($outroplaylistid, false);
                    //Logging::info("The total percent scheduled is % $ps");
                }
                if ($ps > 100) {
                    $full = true;
                }
                elseif (!$repeatuntilfull) {
                    break;
                }
                // we want to avoid an infinite loop if all of the playlists are null
                if ($playlistid == null && $introplaylistid == null && $outroplaylistid == null) {
                    break;
                }
                // another possible issue would be if the show isn't increasing in length each loop
                // ie if all of the playlists being added are zero lengths this could cause an infinite loop
                if ($tempPercentScheduled == $ps) {
                    break;
                }
                //now reset it to zero
                $tempPercentScheduled = $ps;
            }
            $si->setAutoPlaylistBuilt(true);
        }
        Application_Model_Preference::setAutoPlaylistPollLock(microtime(true));
    }

    /**
     * Find all show instances starting in the next hour with autoplaylists not yet added to the schedule
     *
     * @return PropelObjectCollection collection of ShowInstance objects
     *                                that have unbuilt autoplaylists
     */
    protected static function _upcomingAutoPlaylistShows() {
	    //setting now so that past shows aren't referenced
            $now = new DateTime("now", new DateTimeZone("UTC"));
	    // only build playlists for shows that start up to an hour from now
	    $future = clone $now;
	    $future->add(new DateInterval('PT1H'));
	    
        return CcShowInstancesQuery::create()
            ->filterByDbStarts($now,Criteria::GREATER_THAN) 
          ->filterByDbStarts($future,Criteria::LESS_THAN)
            ->useCcShowQuery('a', 'left join')
                ->filterByDbHasAutoPlaylist(true)
            ->endUse()
            ->filterByDbAutoPlaylistBuilt(false)
            ->find();
    }
}
