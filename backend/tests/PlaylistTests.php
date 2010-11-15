<?php

$path = dirname(__FILE__).'/../../3rd_party/php/pear';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once(dirname(__FILE__).'/../../3rd_party/php/propel/runtime/lib/Propel.php');
// Initialize Propel with the runtime configuration
Propel::init(dirname(__FILE__)."/../../backend/propel-db/build/conf/campcaster-conf.php");

// Add the generated 'classes' directory to the include path
set_include_path(dirname(__FILE__)."/../../backend/propel-db/build/classes" . PATH_SEPARATOR . get_include_path());

require_once('DB.php');
require_once('PHPUnit.php');

require_once(dirname(__FILE__).'/../../conf.php');
require_once(dirname(__FILE__).'/../GreenBox.php');
require_once(dirname(__FILE__).'/../Playlist.php');

$tz = ini_get('date.timezone') ? ini_get('date.timezone') : 'America/Toronto';
date_default_timezone_set($tz);

//old database connection still needed to get a session instance.
$dsn = $CC_CONFIG['dsn'];
$CC_DBC = DB::connect($dsn, TRUE);
if (PEAR::isError($CC_DBC)) {
	echo "ERROR: ".$CC_DBC->getMessage()." ".$CC_DBC->getUserInfo()."\n";
	exit(1);
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

class PlaylistTests extends PHPUnit_TestCase {

    private $greenbox;
    private $storedFile;
    private $storedFile2;

    function __construct($name) {
        parent::__construct($name);
    }

    function setup() {
        global $CC_CONFIG, $CC_DBC;
        $this->greenbox = new GreenBox();
        
         // Add a file
        $values = array("filepath" => dirname(__FILE__)."/test10001.mp3");
        $this->storedFile = StoredFile::Insert($values, false);
        
        // Add a file
        $values = array("filepath" => dirname(__FILE__)."/test10002.mp3");
        $this->storedFile2 = StoredFile::Insert($values, false);
        
    }

    function testGBCreatePlaylist() {

        $pl = new Playlist();
        $pl_id = $pl->create("create");
        
        if (!is_int($pl_id)) {
            $this->fail("problems creating playlist.");
            return;
        }
    }

    function testGBLock() {
        $pl = new Playlist();
        $pl_id = $pl->create("lock test");

        $sessid = Alib::Login('root', 'q');

        $res = $this->greenbox->lockPlaylistForEdit($pl_id, $sessid);

        if($res !== TRUE) {
            $this->fail("problems locking playlist for editing.");
            return;
        }
    }

    function testGBUnLock() {
        $pl = new Playlist();
        $pl_id = $pl->create("unlock test");

        $sessid = Alib::Login('root', 'q');

        $this->greenbox->lockPlaylistForEdit($pl_id, $sessid);
        $res = $this->greenbox->releaseLockedPlaylist($pl_id, $sessid);

        if($res !== TRUE) {
           $this->fail("problems unlocking playlist.");
           return;
        }
    }
    
    function testGBSetPLMetaData() {
        $pl = new Playlist();
        $pl_id = $pl->create("set meta data test");

        $res = $this->greenbox->setPLMetadataValue($pl_id, "dc:title", "A Title");

        if($res !== TRUE) {
           $this->fail("problems setting playlist metadata.");
           return;
        }
    }

    function testGBGetPLMetaData() {
        $pl = new Playlist();
        $name = "Testing";
        $pl_id = $pl->create($name);

        $res = $this->greenbox->getPLMetadataValue($pl_id, "dc:title");

        if($res !== $name) {
           $this->fail("problems getting playlist metadata.");
           return;
        }
    }

    function testAddAudioClip() {

        $pl = new Playlist();
        $pl_id = $pl->create("Playlist Unit Test ". uniqid());
        $res = $this->greenbox->addAudioClipToPlaylist($pl_id, $this->storedFile->getId());
        if($res !== TRUE) {
           $this->fail("problems adding audioclip to playlist.");
           return;
        }
        $res = $this->greenbox->addAudioClipToPlaylist($pl_id, $this->storedFile2->getId());
        if($res !== TRUE) {
           $this->fail("problems adding audioclip 2 to playlist.");
           return;
        }
    }

    function testMoveAudioClip() {
        $pl = new Playlist();
        $pl_id = $pl->create("Move");

        $this->greenbox->addAudioClipToPlaylist($pl_id, $this->storedFile->getId());
        $this->greenbox->addAudioClipToPlaylist($pl_id, $this->storedFile2->getId());

        $res = $this->greenbox->moveAudioClipInPlaylist($pl_id, 0, 1);

        if($res !== TRUE) {
           $this->fail("problems moving audioclip in playlist.");
           return;
        }
    }

    function testDeleteAudioClip() {
        $pl = new Playlist();
        $pl_id = $pl->create("Delete");

        $this->greenbox->addAudioClipToPlaylist($pl_id, $this->storedFile->getId());
        $res = $this->greenbox->delAudioClipFromPlaylist($pl_id, 0);

        if($res !== TRUE) {
           $this->fail("problems deleting audioclip from playlist.");
           return;
        }
    }
    
    function testFadeInfo() {
        $pl = new Playlist();
        $pl_id = $pl->create("Fade Info");
        
        $this->greenbox->addAudioClipToPlaylist($pl_id, $this->storedFile2->getId());
        
        $res = $this->greenbox->changeFadeInfo($pl_id, 0, '00:00:01.14', null);
       
        if(!is_array($res) && count($res) !== 2) {
           $this->fail("problems setting fade in playlist.");
           return; 
        } 
    }
}

?>
