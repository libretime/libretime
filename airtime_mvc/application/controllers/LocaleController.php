<?php

class LocaleController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('language-table', 'json')
                    ->initContext();
    }
    
    public function translationTableAction()
    {
        $translations = array (
            //common/common.js
            "Audio Player" => _("Audio Player"),
            //dashboard/dashboard.js
            "Recording:" => _("Recording:"),
            "Master Stream" => _("Master Stream"),
            "Live Stream" => _("Live Stream"),
            "Nothing Scheduled" => _("Nothing Scheduled"),
            "Current Show:" => _("Current Show:"),
            //dashboard/versiontooltip.js
            "You are running the latest version" => _("You are running the latest version"),
            "New version available: " => _("New version available: "),
            "This version will soon be obsolete." => _("This version will soon be obsolete."),
        	"This version is no longer supported." => _("This version is no longer supported."),
            "Please upgrade to " => _("Please upgrade to "),
            //library/events/library_playlistbuilder.js
            " Add to current playlist" => _(" Add to current playlist"),
            " Add to current smart block" => _(" Add to current smart block"),
            "Adding 1 Item." => _("Adding 1 Item."),
            /*****embedded variable*****/
            "Adding " => _("Adding "),
            " Items." => _(" Items."),
            "You can only add tracks to smart blocks." => _("You can only add tracks to smart blocks."),
            "You can only add tracks, smart blocks, and webstreams to playlists." => _("You can only add tracks, smart blocks, and webstreams to playlists."),
            //library/events/library_showbuilder.js
            "Adding 1 Item." => _("Adding 1 Item."),
            /****string with variable*****/
            "Adding " => _("Adding "),
            " Items." => _(" Items."),
            //library/library.js
            "Select" => _("Select"),
            "Select this page" => _("Select this page"),
            "Deselect this page" => _("Deselect this page"),
            "Deselect all" => _("Deselect all"),
            "Are you sure you want to delete the selected item(s)?" => _("Are you sure you want to delete the selected item(s)?"),
            "Title" => _("Title"),
            "Creator" => _("Creator"),
            "Album" => _("Album"),
            "Bit Rate" => _("Bit Rate"),
            "BPM" => _("BPM"),
            "Composer" => _("Composer"),
            "Conductor" => _("Conductor"),
            "Copyright" => _("Copyright"),
            "Encoded By" => _("Encoded By"),
            "Genre" => _("Genre"),
            "ISRC" => _("ISRC"),
            "Label" => _("Label"),
            "Language" => _("Language"),
            "Last Modified" => _("Last Modified"),
            "Last Played" => _("Last Played"),
            "Length" => _("Length"),
            "Mime" => _("Mime"),
            "Mood" => _("Mood"),
            "Owner" => _("Owner"),
            "Replay Gain" => _("Replay Gain"),
            "Sample Rate" => _("Sample Rate"),
            "Track Number" => _("Track Number"),
            "Uploaded" => _("Uploaded"),
            "Website" => _("Webiste"),
            "Year" => _("Year"),
            "Loading..." => _("Loading..."),
            "All" => _("All"),
            "Files" => _("Files"),
            "Playlists" => _("Playlists"),
            "Smart Blocks" => _("Smart Blocks"),
            "Web Streams" => _("Web Streams"),
            "Unknown type: " => _("Unknown type: "),
            "Are you sure you want to delete the selected item?" => _("Are you sure you want to delete the selected item?"),
            "Uploading in progress..." => _("Uploading in progress..."),
            "Retrieving data from the server..." => _("Retrieving data from the server..."),
            "The soundcloud id for this file is: " => _("The soundcloud id for this file is: "),
            "There was an error while uploading to soundcloud." => _("There was an error while uploading to soundcloud."),
            "Error code: " => _("Error code: "),
            "Error msg: " => _("Error msg: "),
            "Input must be a positive number" => _("Input must be a positive number"),
            "Input must be a number" => _("Input must be a number"),
            "Input must be in the format: yyyy-mm-dd" => _("Input must be in the format: yyyy-mm-dd"),
            "Input must be in the format: hh:mm:ss.t" => _("Input must be in the format: hh:mm:ss.t"),
            //library/plupload.js
            "You are currently uploading files." => _("You are currently uploading files."),
            "Going to another screen will cancel the upload process." => _("Going to another screen will cancel the upload process."),
            "Are you sure you want to leave the page?" => _("Are you sure you want to leave the page?"),
            //library/spl.js
            "please put in a time '00:00:00 (.0)'" => _("please put in a time '00:00:00 (.0)'"),
            "please put in a time in seconds '00 (.0)'" => _("please put in a time in seconds '00 (.0)'"),
            "Your browser does not support playing this file type: " => _("Your browser does not support playing this file type: "),
            "Dynamic block is not previewable" => _("Dynamic block is not previewable"),
            "Limit to: " => _("Limit to: "),
            "-error" => _("-error"),
            "Playlist saved" => _("Playlist saved"),
            "Airtime is unsure about the status of this file. This can happen when the file is on a remote drive that is unaccessible or the file is in a directory that isn't 'watched' anymore."
                => _("Airtime is unsure about the status of this file. This can happen when the file is on a remote drive that is unaccessible or the file is in a directory that isn't 'watched' anymore."),
            //listenerstat/listenerstat.js
            "Listener Count on" => _("Listener Count on"),
            "You clicked point " => _("You clicked point "),
            "in" => _("in"),
            //nowplaying/register.js
            "Remind me in 1 week" => _("Remind me in 1 week"),
            "Remind me never" => _("Remind me never"),
            "Yes, help Airtime" => _("Yes, help Airtime"),
            "Image must be one of jpg, jpeg, png, or gif" => _("Image must be one of jpg, jpeg, png, or gif"),
            //playlist/smart_blockbuilder.js
            "A static smart block will save the criteria and generate the block content immediately. This allows you to edit and view it in the Library before adding it to a show."
                => _("A static smart block will save the criteria and generate the block content immediately. This allows you to edit and view it in the Library before adding it to a show."),
            "A dynamic smart block will only save the criteria. The block content will get generated upon adding it to a show. You will not be able to view and edit the content in the Library."
                => _("A dynamic smart block will only save the criteria. The block content will get generated upon adding it to a show. You will not be able to view and edit the content in the Library."),
            "If your criteria is too strict, Airtime may not be able to fill up the desired smart block length. Hence, if you check this option, tracks will be used more than once."
                => _("If your criteria is too strict, Airtime may not be able to fill up the desired smart block length. Hence, if you check this option, tracks will be used more than once."),
            "Smart block shuffled" => _("Smart block shuffled"),
            "Smart block generated and criteria saved" => _("Smart block generated and criteria saved"),
            "Smart block saved" => _("Smart block saved"),
            "Processing..." => _("Processing..."),
            "Select modifier" => _("Select modifier"),
            "contains" => _("contains"),
            "does not contain" => _("does not contain"),
            "is" => _("is"),
            "is not" => _("is not"),
            "starts with" => _("starts with"),
            "ends with" => _("ends with"),
            "is greater than" => _("is greater than"),
            "is less than" => _("is less than"),
            "is in the range" => _("is in the range"),
            //playouthistory/historytable.js
            "Title" => _("Title"),
            "Creator" => _("Creator"),
            "Played" => _("Played"),
            "Length" => _("Length"),
            "Composer" => _("Composer"),
            "Copyright" => _("Copyright"),
            //preferences/musicdirs.js
            "Choose Storage Folder" => _("Choose Storage Folder"),
            "Choose Folder to Watch" => _("Choose Folder to Watch"),
            "Are you sure you want to change the storage folder?\nThis will remove the files from your Airtime library!"
                => _("Are you sure you want to change the storage folder?\nThis will remove the files from your Airtime library!"),
            "Manage Media Folders" => _("Manage Media Folders"),
            "Are you sure you want to remove the watched folder?" => _("Are you sure you want to remove the watched folder?"),
            "This path is currently not accessible." => _("This path is currently not accessible."),
            //preferences/streamsetting.js
            "Connected to the streaming server" => _("Connected to the streaming server"),
            "The stream is disabled" => _("The stream is disabled"),
            "Getting information from the server..." => _("Getting information from the server..."),
            "Can not connect to the streaming server" => _("Can not connect to the streaming server"),
            "If Airtime is behind a router or firewall, you may need to configure port forwarding and this field information will be incorrect. In this case you will need to manually update this field so it shows the correct host/port/mount that your DJ's need to connect to. The allowed range is between 1024 and 49151."
                => _("If Airtime is behind a router or firewall, you may need to configure port forwarding and this field information will be incorrect. In this case you will need to manually update this field so it shows the correct host/port/mount that your DJ's need to connect to. The allowed range is between 1024 and 49151."),
            /*****embedded variable*****/
            "For more details, please read the " => _("For more details, please read the "),
            "Airtime manual" => _("Airtime manual"),
            "Check this option to enable metadata for OGG streams (stream metadata is the track title, artist, and show name that is displayed in an audio player). VLC and mplayer have a serious bug when playing an OGG/VORBIS stream that has metadata information enabled: they will disconnect from the stream after every song. If you are using an OGG stream and your listeners do not require support for these audio players, then feel free to enable this option."
                => _("Check this option to enable metadata for OGG streams (stream metadata is the track title, artist, and show name that is displayed in an audio player). VLC and mplayer have a serious bug when playing an OGG/VORBIS stream that has metadata information enabled: they will disconnect from the stream after every song. If you are using an OGG stream and your listeners do not require support for these audio players, then feel free to enable this option."),
            "Check this box to automatically switch off Master/Show source upon source disconnection." => _("Check this box to automatically switch off Master/Show source upon source disconnection."),
            "Check this box to automatically switch on Master/Show source upon source connection." => _("Check this box to automatically switch on Master/Show source upon source connection."),
            "If your Icecast server expects a username of 'source', this field can be left blank." => _("If your Icecast server expects a username of 'source', this field can be left blank."),
            "If your live streaming client does not ask for a username, this field should be 'source'." => _("If your live streaming client does not ask for a username, this field should be 'source'."),
            "If you change the username or password values for an enabled stream the playout engine will be rebooted and your listeners will hear silence for 5-10 seconds. Changing the following fields will NOT cause a reboot: Stream Label (Global Settings), and Switch Transition Fade(s), Master Username, and Master Password (Input Stream Settings). If Airtime is recording, and if the change causes a playout engine restart, the recording will be interrupted."
                => _("If you change the username or password values for an enabled stream the playout engine will be rebooted and your listeners will hear silence for 5-10 seconds. Changing the following fields will NOT cause a reboot: Stream Label (Global Settings), and Switch Transition Fade(s), Master Username, and Master Password (Input Stream Settings). If Airtime is recording, and if the change causes a playout engine restart, the recording will be interrupted."),
            //preferences/support-setting.js
            "Image must be one of jpg, jpeg, png, or gif" => _("Image must be one of jpg, jpeg, png, or gif"),
            //schedule/add-show.js
            "No result found" => _("No result found"),
            "This follows the same security pattern for the shows: only users assigned to the show can connect." => _("This follows the same security pattern for the shows: only users assigned to the show can connect."),
            "Specify custom authentication which will work only for this show." => _("Specify custom authentication which will work only for this show."),
            "If your live streaming client does not ask for a username, this field should be 'source'." => _("If your live streaming client does not ask for a username, this field should be 'source'."),
            "The show instance doesn't exist anymore!" => _("The show instance doesn't exist anymore!"),
            //schedule/full-calendar-functions
            //already in schedule/add-show.js
            //"The show instance doesn't exist anymore!" => _("The show instance doesn't exist anymore!"),
            "Show" => _("Show"),
            "Show is empty" => _("Show is empty"),
            "1m" => _("1m"),
            "5m" => _("5m"),
            "10m" => _("10m"),
            "15m" => _("15m"),
            "30m" => _("30m"),
            "60m" => _("60m"),
            "Uploading in progress..." => _("Uploading in progress..."),
            "Retreiving data from the server..." => _("Retreiving data from the server..."),
            //already in library/library.js
            //"The soundcloud id for this file is: " => _("The soundcloud id for this file is: "),
            //"There was error while uploading to soundcloud." => _("There was error while uploading to soundcloud."),
            //"Error code: " => _("Error code: "),
            //"Error msg: " => _("Error msg: "),
            "This show has no scheduled content." => _("This show has no scheduled content."),
            //already in schedule/add-show.js
            //"The show instance doesn't exist anymore!" => _("The show instance doesn't exist anymore!"),
            //schedule/schedule.js
            "Shows longer than their scheduled time will be cut off by a following show." => _("Shows longer than their scheduled time will be cut off by a following show."),
            "Cancel Current Show?" => _("Cancel Current Show?"),
            "Stop recording current show?" => _("Stop recording current show?"),
            "Ok" => _("Ok"),
            "Contents of Show" => _("Contents of Show"),
            //already in schedule/add-show.js
            //"The show instance doesn't exist anymore!" => _("The show instance doesn't exist anymore!"),
            "Remove all content?" => _("Remove all content?"),
            //showbuilder/builder.js
            "Delete selected item(s)?" => _("Delete selected item(s)?"),
            "Start" => _("Start"),
            "End" => _("End"),
            "Duration" => _("Duration"),
            //already in library/library.js
            //"Title" => _("Title"),
            //"Creator" => _("Creator"),
            //"Album" => _("Album"),
            //"Mime" => _("Mime"),
            "Cue In" => _("Cue In"),
            "Cue Out" => _("Cue Out"),
            "Fade In" => _("Fade In"),
            "Fade Out" => _("Fade Out"),
            "Show Empty" => _("Show Empty"),
            "Recording From Line In" => _("Recording From Line In"),
            "Track preview" => _("Track preview"),
            //already in library/spl/js
            //"Airtime is unsure about the status of this file. This can happen when the file is on a remote drive that is unaccessible or the file is in a directory that isn't 'watched' anymore."
                //=> _("Airtime is unsure about the status of this file. This can happen when the file is on a remote drive that is unaccessible or the file is in a directory that isn't 'watched' anymore."),
            "Cannot schedule outside a show." => _("Cannot schedule outside a show."),
            /*****embedded variable*****/
            "Moving " => _("Moving "),
            " Item." => _(" Item."),
            " Items." => _(" Items."),
            //already in library/library.js
            "Select" => _("Select"),
            "Select all" => _("Select all"),
            "Select none" => _("Select none"),
            "Remove overbooked tracks" => _("Remove overbooked tracks"),
            "Remove selected scheduled items" => _("Remove selected scheduled items"),
            "Jump to the current playing track" => _("Jump to the current playing track"),
            "Cancel current show" => _("Cancel current show"),
            //already in schedule/schedule.js
            //"Cancel Current Show?" => _("Cancel Current Show?"),
            "Stop recording current show?" => _("Stop recording current show?"),
            //showbuilder/main_builder.js
            "Open library to add or remove content" => _("Open library to add or remove content"),
            "Add / Remove Content" => _("Add / Remove Content"),
            //status/status.js
            "in use" => _("in use"),
            "Disk" => _("Disk"),
            //user/user.js
            "Admin" => _("Admin"),
            "DJ" => _("DJ"),
            "Program Manager" => _("Program Manager"),
            "Guest" => _("Guest"),
            
        );
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        header("Content-type: text/javascript");
        echo "var lang_dict=".json_encode($translations);
        
    }
}