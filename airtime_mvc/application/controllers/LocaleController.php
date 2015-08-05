<?php

class LocaleController extends Zend_Controller_Action
{

    public function init()
    {
    }
    
    public function datatablesTranslationTableAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        header("Content-type: text/javascript");

        $locale = Application_Model_Preference::GetLocale();
        echo "var datatables_dict =" .
            file_get_contents(Application_Common_OsPath::join(
                //$_SERVER["DOCUMENT_ROOT"],
                dirname(__FILE__) . "/../../public/", // Fixing this... -- Albert
                "js/datatables/i18n/",
                $locale.".txt")
            );
    }

    public function generalTranslationTableAction()
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
            "Current" => _("Current"),
            //dashboard/versiontooltip.js
            "You are running the latest version" => _("You are running the latest version"),
            "New version available: " => _("New version available: "),
            "This version will soon be obsolete." => _("This version will soon be obsolete."),
            "This version is no longer supported." => _("This version is no longer supported."),
            "Please upgrade to " => _("Please upgrade to "),
            //library/events/library_playlistbuilder.js
            "Add to current playlist" => _("Add to current playlist"),
            "Add to current smart block" => _("Add to current smart block"),
            "Adding 1 Item" => _("Adding 1 Item"),
            "Adding %s Items" => _("Adding %s Items"),
            "You can only add tracks to smart blocks." => _("You can only add tracks to smart blocks."),
            "You can only add tracks, smart blocks, and webstreams to playlists." => _("You can only add tracks, smart blocks, and webstreams to playlists."),
            //library/events/library_showbuilder.js
            //already in library/events/library_playlistbuilder.js
            "Please select a cursor position on timeline." => _("Please select a cursor position on timeline."),
            //"Adding 1 Item" => _("Adding 1 Item"),
            //"Adding %s Items" => _("Adding %s Items"),
            //library/library.js
            "Edit Metadata" => _("Edit Metadata"),
            "Add to selected show" => _("Add to selected show"),
            "Select" => _("Select"),
            "Select this page" => _("Select this page"),
            "Deselect this page" => _("Deselect this page"),
            "Deselect all" => _("Deselect all"),
            "Are you sure you want to delete the selected item(s)?" => _("Are you sure you want to delete the selected item(s)?"),
            "Scheduled" => _("Scheduled"),
            "Playlist" => _("Playlist / Block"),
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
            "Website" => _("Website"),
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
            "You are currently uploading files. %sGoing to another screen will cancel the upload process. %sAre you sure you want to leave the page?"
                => _("You are currently uploading files. %sGoing to another screen will cancel the upload process. %sAre you sure you want to leave the page?"),
            //library/spl.js
            "Open Media Builder" => _("Open Media Builder"),
            "please put in a time '00:00:00 (.0)'" => _("please put in a time '00:00:00 (.0)'"),
            "please put in a time in seconds '00 (.0)'" => _("please put in a time in seconds '00 (.0)'"),
            "Your browser does not support playing this file type: " => _("Your browser does not support playing this file type: "),
            "Dynamic block is not previewable" => _("Dynamic block is not previewable"),
            "Limit to: " => _("Limit to: "),
            "Playlist saved" => _("Playlist saved"),
            "Playlist shuffled" => _("Playlist shuffled"),
            "Airtime is unsure about the status of this file. This can happen when the file is on a remote drive that is unaccessible or the file is in a directory that isn't 'watched' anymore."
                => _("Airtime is unsure about the status of this file. This can happen when the file is on a remote drive that is unaccessible or the file is in a directory that isn't 'watched' anymore."),
            //listenerstat/listenerstat.js
            "Listener Count on %s: %s" => _("Listener Count on %s: %s"),
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
            "The desired block length will not be reached if Airtime cannot find enough unique tracks to match your criteria. Enable this option if you wish to allow tracks to be added multiple times to the smart block."
                => _("The desired block length will not be reached if Airtime cannot find enough unique tracks to match your criteria. Enable this option if you wish to allow tracks to be added multiple times to the smart block."),
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
           //preferences/musicdirs.js
            "Choose Storage Folder" => _("Choose Storage Folder"),
            "Choose Folder to Watch" => _("Choose Folder to Watch"),
            "Are you sure you want to change the storage folder?\nThis will remove the files from your Airtime library!"
                => _("Are you sure you want to change the storage folder?\nThis will remove the files from your Airtime library!"),
            "Manage Media Folders" => _("Manage Media Folders"),
            "Are you sure you want to remove the watched folder?" => _("Are you sure you want to remove the watched folder?"),
            "This path is currently not accessible." => _("This path is currently not accessible."),
            //preferences/streamsetting.js
        	"Some stream types require extra configuration. Details about enabling %sAAC+ Support%s or %sOpus Support%s are provided." => _("Some stream types require extra configuration. Details about enabling %sAAC+ Support%s or %sOpus Support%s are provided."),
            "Connected to the streaming server" => _("Connected to the streaming server"),
            "The stream is disabled" => _("The stream is disabled"),
            "Getting information from the server..." => _("Getting information from the server..."),
            "Can not connect to the streaming server" => _("Can not connect to the streaming server"),
            "If Airtime is behind a router or firewall, you may need to configure port forwarding and this field information will be incorrect. In this case you will need to manually update this field so it shows the correct host/port/mount that your DJ's need to connect to. The allowed range is between 1024 and 49151."
                => _("If Airtime is behind a router or firewall, you may need to configure port forwarding and this field information will be incorrect. In this case you will need to manually update this field so it shows the correct host/port/mount that your DJ's need to connect to. The allowed range is between 1024 and 49151."),
            "For more details, please read the %sAirtime Manual%s" => _("For more details, please read the %sAirtime Manual%s"),
            "Check this option to enable metadata for OGG streams (stream metadata is the track title, artist, and show name that is displayed in an audio player). VLC and mplayer have a serious bug when playing an OGG/VORBIS stream that has metadata information enabled: they will disconnect from the stream after every song. If you are using an OGG stream and your listeners do not require support for these audio players, then feel free to enable this option."
                => _("Check this option to enable metadata for OGG streams (stream metadata is the track title, artist, and show name that is displayed in an audio player). VLC and mplayer have a serious bug when playing an OGG/VORBIS stream that has metadata information enabled: they will disconnect from the stream after every song. If you are using an OGG stream and your listeners do not require support for these audio players, then feel free to enable this option."),
            "Check this box to automatically switch off Master/Show source upon source disconnection." => _("Check this box to automatically switch off Master/Show source upon source disconnection."),
            "Check this box to automatically switch on Master/Show source upon source connection." => _("Check this box to automatically switch on Master/Show source upon source connection."),
            "If your Icecast server expects a username of 'source', this field can be left blank." => _("If your Icecast server expects a username of 'source', this field can be left blank."),
            "If your live streaming client does not ask for a username, this field should be 'source'." => _("If your live streaming client does not ask for a username, this field should be 'source'."),
            "WARNING: This will restart your stream and may cause a short dropout for your listeners!" => _("WARNING: This will restart your stream and may cause a short dropout for your listeners!"),
            "This is the admin username and password for Icecast/SHOUTcast to get listener statistics." => _("This is the admin username and password for Icecast/SHOUTcast to get listener statistics."),
            //preferences/support-setting.js
            "Image must be one of jpg, jpeg, png, or gif" => _("Image must be one of jpg, jpeg, png, or gif"),
            //schedule/add-show.js
            "Warning: You cannot change this field while the show is currently playing" => _("Warning: You cannot change this field while the show is currently playing"),
            "No result found" => _("No result found"),
            "This follows the same security pattern for the shows: only users assigned to the show can connect." => _("This follows the same security pattern for the shows: only users assigned to the show can connect."),
            "Specify custom authentication which will work only for this show." => _("Specify custom authentication which will work only for this show."),
            "If your live streaming client does not ask for a username, this field should be 'source'." => _("If your live streaming client does not ask for a username, this field should be 'source'."),
            "The show instance doesn't exist anymore!" => _("The show instance doesn't exist anymore!"),
            "Warning: Shows cannot be re-linked" => _("Warning: Shows cannot be re-linked"),
            "By linking your repeating shows any media items scheduled in any repeat show will also get scheduled in the other repeat shows" => _("By linking your repeating shows any media items scheduled in any repeat show will also get scheduled in the other repeat shows"),
            "Timezone is set to the station timezone by default. Shows in the calendar will be displayed in your local time defined by the Interface Timezone in your user settings." => _("Timezone is set to the station timezone by default. Shows in the calendar will be displayed in your local time defined by the Interface Timezone in your user settings."),
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
            "This show is not completely filled with content." => _("This show is not completely filled with content."),
            //already in schedule/add-show.js
            //"The show instance doesn"t exist anymore!" => _("The show instance doesn"t exist anymore!"),
            //schedule/schedule.js
            "January" => _("January"),
            "February" => _("February"),
            "March" => _("March"),
            "April" => _("April"),
            "May" => _("May"),
            "June" => _("June"),
            "July" => _("July"),
            "August" => _("August"),
            "September" => _("September"),
            "October" => _("October"),
            "November" => _("November"),
            "December" => _("December"),
            "Jan" => _("Jan"),
            "Feb" => _("Feb"),
            "Mar" => _("Mar"),
            "Apr" => _("Apr"),
            "May" => _("May"),
            "Jun" => _("Jun"),
            "Jul" => _("Jul"),
            "Aug" => _("Aug"),
            "Sep" => _("Sep"),
            "Oct" => _("Oct"),
            "Nov" => _("Nov"),
            "Dec" => _("Dec"),
            "today" => _("today"),
            "day" => _("day"),
            "week" => _("week"),
            "month" => _("month"),
            "Sunday" => _("Sunday"),
            "Monday" => _("Monday"),
            "Tuesday" => _("Tuesday"),
            "Wednesday" => _("Wednesday"),
            "Thursday" => _("Thursday"),
            "Friday" => _("Friday"),
            "Saturday" => _("Saturday"),
            "Sun" => _("Sun"),
            "Mon" => _("Mon"),
            "Tue" => _("Tue"),
            "Wed" => _("Wed"),
            "Thu" => _("Thu"),
            "Fri" => _("Fri"),
            "Sat" => _("Sat"),
            "Shows longer than their scheduled time will be cut off by a following show." => _("Shows longer than their scheduled time will be cut off by a following show."),
            "Cancel Current Show?" => _("Cancel Current Show?"),
            "Stop recording current show?" => _("Stop recording current show?"),
            "Ok" => _("Ok"),
            "Contents of Show" => _("Contents of Show"),
            //already in schedule/add-show.js
            //"The show instance doesn"t exist anymore!" => _("The show instance doesn"t exist anymore!"),
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
            //already in library/spl.js
            //"Airtime is unsure about the status of this file. This can happen when the file is on a remote drive that is unaccessible or the file is in a directory that isn"t "watched" anymore."
                //=> _("Airtime is unsure about the status of this file. This can happen when the file is on a remote drive that is unaccessible or the file is in a directory that isn"t "watched" anymore."),
            "Cannot schedule outside a show." => _("Cannot schedule outside a show."),
            "Moving 1 Item" => _("Moving 1 Item"),
            "Moving %s Items" => _("Moving %s Items"),
        	"Save" => _("Save"),
        	"Cancel" => _("Cancel"),
        	"Fade Editor" => _("Fade Editor"),
        	"Cue Editor" => _("Cue Editor"),
        	"Waveform features are available in a browser supporting the Web Audio API" => _("Waveform features are available in a browser supporting the Web Audio API"),
            //already in library/library.js
            //"Select" => _("Select"),
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
            //serverbrowse/serverbrowse.js
            "Look in" => _("Look in"),
            "Cancel" => _("Cancel"),
            "Open" => _("Open"),
            //user/user.js
            "Admin" => _("Admin"),
            "DJ" => _("DJ"),
            "Program Manager" => _("Program Manager"),
            "Guest" => _("Guest"),
            "Guests can do the following:" => _("Guests can do the following:"),
            "View schedule" => _("View schedule"),
            "View show content" => _("View show content"),
            "DJs can do the following:" => _("DJs can do the following:"),
            "Manage assigned show content" => _("Manage assigned show content"),
            "Import media files" => _("Import media files"),
            "Create playlists, smart blocks, and webstreams" => _("Create playlists, smart blocks, and webstreams"),
            "Manage their own library content" => _("Manage their own library content"),
            "Progam Managers can do the following:" => _("Progam Managers can do the following:"),
            "View and manage show content" => _("View and manage show content"),
            "Schedule shows" => _("Schedule shows"),
            "Manage all library content" => _("Manage all library content"),
            "Admins can do the following:" => _("Admins can do the following:"),
            "Manage preferences" => _("Manage preferences"),
            "Manage users" => _("Manage users"),
            "Manage watched folders" => _("Manage watched folders"),
            "Send support feedback" => _("Send support feedback"),
            "View system status" => _("View system status"),
            "Access playout history" => _("Access playout history"),
            "View listener stats" => _("View listener stats"),
            //dataTables/ColVis.js
            "Show / hide columns" => _("Show / hide columns"),
            //datatables.columnFilter.js
            "From {from} to {to}" => _("From {from} to {to}"),
            "kbps" => _("kbps"),
            "yyyy-mm-dd" => _("yyyy-mm-dd"),
            "hh:mm:ss.t" => _("hh:mm:ss.t"),
            "kHz" => _("kHz"),
            //datepicker
            //months are already in schedule/schedule.js
            "Su" => _("Su"),
            "Mo" => _("Mo"),
            "Tu" => _("Tu"),
            "We" => _("We"),
            "Th" => _("Th"),
            "Fr" => _("Fr"),
            "Sa" => _("Sa"),
            "Close" => _("Close"),
            //timepicker
            "Hour" => _("Hour"),
            "Minute" => _("Minute"),
            "Done" => _("Done"),
            //plupload ships with translation files but a lot are incomplete
            //so we will keep them here to prevent incomplete translations
            "Select files" => _("Select files"),
            "Add files to the upload queue and click the start button." => _("Add files to the upload queue and click the start button."),
            "Filename" => _("Add files to the upload queue and click the start button."),
            "Status" => _("Status"),
            "Size" => _("Status"),
            "Add Files" => _("Add Files"),
            "Stop Upload" => _("Stop Upload"),
            "Start upload" => _("Start upload"),
            "Add files" => _("Add files"),
            "Uploaded %d/%d files"=> _("Uploaded %d/%d files"),
            "N/A" => _("N/A"),
            "Drag files here." => _("Drag files here."),
            "File extension error." => _("File extension error."),
            "File size error." => _("File size error."),
            "File count error." => _("File count error."),
            "Init error." => _("Init error."),
            "HTTP Error." => _("HTTP Error."),
            "Security error." => _("Security error."),
            "Generic error." => _("Generic error."),
            "IO error." => _("IO error."),
            "File: %s" => _("File: %s"),
            "Close" => _("Close"),
            "%d files queued" => _("%d files queued"),
            "File: %f, size: %s, max file size: %m" => _("File: %f, size: %s, max file size: %m"),
            "Upload URL might be wrong or doesn't exist" => _("Upload URL might be wrong or doesn't exist"),
            "Error: File too large: " => _("Error: File too large: "),
            "Error: Invalid file extension: " => _("Error: Invalid file extension: "),
            //history translations
            "Set Default" => _("Set Default"),
            "Create Entry" => _("Create Entry"),
            "Edit History Record" => _("Edit History Record"),
            "No Show" => _("No Show"),
            "All" => _("All"),
            "Copied %s row%s to the clipboard" => _("Copied %s row%s to the clipboard"),
            "%sPrint view%sPlease use your browser's print function to print this table. Press escape when finished." => _("%sPrint view%sPlease use your browser's print function to print this table. Press escape when finished.")
        );
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        header("Content-type: text/javascript");
        echo "var general_dict=".json_encode($translations);

    }
}
