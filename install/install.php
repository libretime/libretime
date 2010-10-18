<?php
/**
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

echo "**********************\n";
echo "* Campcaster Install *\n";
echo "**********************\n";

require_once(dirname(__FILE__).'/../conf.php');
require_once(dirname(__FILE__).'/../backend/GreenBox.php');
require_once(dirname(__FILE__).'/../backend/cron/Cron.php');
require_once(dirname(__FILE__)."/installInit.php");

// Need to check that we are superuser before running this.

echo " *** Database Installation ***\n";

//sudo -u postgres createuser --no-superuser --no-createdb --no-createrole -A -P myuser

// Create the database user
$command = "sudo -u postgres psql postgres --command \"CREATE USER {$CC_CONFIG['dsn']['username']} "
  ." ENCRYPTED PASSWORD '{$CC_CONFIG['dsn']['password']}' LOGIN CREATEDB NOCREATEUSER;\" 2>/dev/null";
//echo $command."\n";
@exec($command, $output, $results);
if ($results == 0) {
  echo "   * User {$CC_CONFIG['dsn']['username']} created.\n";
} else {
  echo "   * User {$CC_CONFIG['dsn']['username']} already exists.\n";
}

$command = "sudo -u postgres createdb {$CC_CONFIG['dsn']['database']} --owner {$CC_CONFIG['dsn']['username']} 2> /dev/null";
//echo $command."\n";
@exec($command, $output, $results);
if ($results == 0) {
  echo "   * Database '{$CC_CONFIG['dsn']['database']}' created.\n";
} else {
  echo "   * Database '{$CC_CONFIG['dsn']['database']}' already exists.\n";
}

// Connect to DB
campcaster_db_connect(true);

// Install postgres scripting language
$langIsInstalled = $CC_DBC->GetOne('SELECT COUNT(*) FROM pg_language WHERE lanname = \'plpgsql\'');
if ($langIsInstalled == '0') {
  echo "   * Installing Postgres scripting language...\n";
  $sql = "CREATE LANGUAGE 'plpgsql'";
  camp_install_query($sql, false);
} else {
  echo "   * Postgres scripting language already installed\n";
}

//------------------------------------------------------------------------------
// Install database tables
//------------------------------------------------------------------------------
if (!camp_db_table_exists($CC_CONFIG['subjTable'])) {
    echo "   * Creating database table ".$CC_CONFIG['subjTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['subjTable']." (
        id int not null PRIMARY KEY,
        login varchar(255) not null default'',
        pass varchar(255) not null default'',
        type char(1) not null default 'U',
        realname varchar(255) not null default'',
        lastlogin timestamp,
        lastfail timestamp
    )";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['subjTable']."_id_idx
        ON ".$CC_CONFIG['subjTable']." (id)";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['subjTable']."_login_idx
        ON ".$CC_CONFIG['subjTable']." (login)";
    camp_install_query($sql, false);

    $CC_DBC->createSequence($CC_CONFIG['subjSequence']);
    echo "done.\n";
} else {
    echo "   * Skipping: database table already exists: ".$CC_CONFIG['subjTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['smembTable'])) {
    echo "   * Creating database table ".$CC_CONFIG['smembTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['smembTable']." (
        id int not null PRIMARY KEY,
        uid int not null default 0,
        gid int not null default 0,
        level int not null default 0,
        mid int)";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['smembTable']."_id_idx
        ON ".$CC_CONFIG['smembTable']." (id)";
    camp_install_query($sql, false);

    //$CC_DBC->createSequence($CC_CONFIG['smembSequence']);
    echo "done.\n";
} else {
    echo "   * Skipping: database table already exists: ".$CC_CONFIG['smembTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['permTable'])) {
    echo "   * Creating database table ".$CC_CONFIG['permTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['permTable']." (
        permid int not null PRIMARY KEY,
        subj int REFERENCES ".$CC_CONFIG['subjTable']." ON DELETE CASCADE,
        action varchar(20),
        obj int,
        type char(1))";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['permTable']."_permid_idx
        ON ".$CC_CONFIG['permTable']." (permid)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['permTable']."_subj_obj_idx
        ON ".$CC_CONFIG['permTable']." (subj, obj)";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['permTable']."_all_idx
        ON ".$CC_CONFIG['permTable']." (subj, action, obj)";
    camp_install_query($sql, false);

    //$CC_DBC->createSequence($CC_CONFIG['permSequence']);
    echo "done.\n";
} else {
    echo "   * Skipping: database table already exists: ".$CC_CONFIG['permTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['sessTable'])) {
    echo "   * Creating database table ".$CC_CONFIG['sessTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['sessTable']." (
        sessid char(32) not null PRIMARY KEY,
        userid int REFERENCES ".$CC_CONFIG['subjTable']." ON DELETE CASCADE,
        login varchar(255),
        ts timestamp)";
    camp_install_query($sql, false);

    //    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['sessTable']."_sessid_idx
    //        ON ".$CC_CONFIG['sessTable']." (sessid)";
    //    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['sessTable']."_userid_idx
        ON ".$CC_CONFIG['sessTable']." (userid)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['sessTable']."_login_idx
        ON ".$CC_CONFIG['sessTable']." (login)";
    camp_install_query($sql);
} else {
    echo "   * Skipping: database table already exists: ".$CC_CONFIG['sessTable']."\n";
}

/**
 * file states:
 *   empty
 *   incomplete
 *   ready
 *   edited
 *   deleted
 *
 * file types:
 *   audioclip
 *   playlist
 *   webstream
 *
 * access types:
 *   access
 *   download
 *
 */
if (!camp_db_table_exists($CC_CONFIG['filesTable'])) {
    echo "   * Creating database table ".$CC_CONFIG['filesTable']."...";
    $sql =
        "CREATE TABLE ".$CC_CONFIG['filesTable']."
        (
          id serial NOT NULL,
          gunid bigint NOT NULL,
          \"name\" character varying(255) NOT NULL DEFAULT ''::character varying,
          mime character varying(255) NOT NULL DEFAULT ''::character varying,
          ftype character varying(128) NOT NULL DEFAULT ''::character varying,
          state character varying(128) NOT NULL DEFAULT 'empty'::character varying,
          currentlyaccessing integer NOT NULL DEFAULT 0,
          editedby integer,
          mtime timestamp(6) with time zone,
          md5 character(32),
          track_title character varying(512),
          artist_name character varying(512),
          bit_rate character varying(32),
          sample_rate character varying(32),
          format character varying(128),
          length time without time zone,
          album_title character varying(512),
          genre character varying(64),
          comments text,
          \"year\" character varying(16),
          track_number integer,
          channels integer,
          url character varying(1024),
          bpm character varying(8),
          rating character varying(8),
          encoded_by character varying(255),
          disc_number character varying(8),
          mood character varying(64),
          label character varying(512),
          composer character varying(512),
          encoder character varying(64),
          checksum character varying(256),
          lyrics text,
          orchestra character varying(512),
          conductor character varying(512),
          lyricist character varying(512),
          original_lyricist character varying(512),
          radio_station_name character varying(512),
          info_url character varying(512),
          artist_url character varying(512),
          audio_source_url character varying(512),
          radio_station_url character varying(512),
          buy_this_url character varying(512),
          isrc_number character varying(512),
          catalog_number character varying(512),
          original_artist character varying(512),
          copyright character varying(512),
          report_datetime character varying(32),
          report_location character varying(512),
          report_organization character varying(512),
          subject character varying(512),
          contributor character varying(512),
          \"language\" character varying(512),
          CONSTRAINT cc_files_pkey PRIMARY KEY (id),
          CONSTRAINT cc_files_editedby_fkey FOREIGN KEY (editedby)
              REFERENCES cc_subjs (id) MATCH SIMPLE
              ON UPDATE NO ACTION ON DELETE NO ACTION
        )";

    camp_install_query($sql, false);

//    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['filesTable']."_id_idx
//        ON ".$CC_CONFIG['filesTable']." (id)";
//    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['filesTable']."_gunid_idx
        ON ".$CC_CONFIG['filesTable']." (gunid)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['filesTable']."_name_idx
        ON ".$CC_CONFIG['filesTable']." (name)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['filesTable']."_md5_idx
        ON ".$CC_CONFIG['filesTable']." (md5)";
    camp_install_query($sql);

    //$CC_DBC->createSequence($CC_CONFIG['filesSequence']);

} else {
    echo "   * Skipping: database table already exists: ".$CC_CONFIG['filesTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['playListTable'])) {
    echo "   * Creating database table ".$CC_CONFIG['playListTable']."...";
    $sql =
        "CREATE TABLE ".$CC_CONFIG['playListTable']."
        (
          id serial NOT NULL,
          \"name\" character varying(255) NOT NULL DEFAULT ''::character varying,
          state character varying(128) NOT NULL DEFAULT 'empty'::character varying,
          currentlyaccessing integer NOT NULL DEFAULT 0,
          editedby integer,
          mtime timestamp(6) with time zone,
          creator character varying(32),
          description character varying(512),
          CONSTRAINT cc_playlist_pkey PRIMARY KEY (id),
          CONSTRAINT cc_playlist_editedby_fkey FOREIGN KEY (editedby)
              REFERENCES cc_subjs (id) MATCH SIMPLE
              ON UPDATE NO ACTION ON DELETE NO ACTION
        )";

    camp_install_query($sql);

} else {
    echo "   * Skipping: database table already exists: ".$CC_CONFIG['playListTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['playListContentsTable'])) {
    echo "   * Creating database table ".$CC_CONFIG['playListContentsTable']."...";
    $sql =
        "CREATE TABLE ".$CC_CONFIG['playListContentsTable']."
        (
          id serial NOT NULL,
          playlist_id integer,
          file_id integer,
          position integer,
          cliplength time without time zone DEFAULT '00:00:00.000',
          cuein time without time zone DEFAULT '00:00:00.000',
          cueout time without time zone DEFAULT '00:00:00.000',
          fadein time without time zone DEFAULT '00:00:00.000',
          fadeout time without time zone DEFAULT '00:00:00.000',
          CONSTRAINT cc_playlistcontents_pkey PRIMARY KEY (id),
          CONSTRAINT cc_playlistcontents_playlist_id_fkey FOREIGN KEY (playlist_id)
              REFERENCES ".$CC_CONFIG['playListTable']." (id) MATCH SIMPLE
              ON UPDATE NO ACTION ON DELETE CASCADE,
          CONSTRAINT cc_playlistcontents_file_id_fkey FOREIGN KEY (file_id)
          	  REFERENCES ".$CC_CONFIG['filesTable']." (id) MATCH SIMPLE
          	  ON UPDATE NO ACTION ON DELETE CASCADE
        );

    CREATE OR REPLACE FUNCTION calculate_position() RETURNS trigger AS
	\$calc_pos\$
    BEGIN
    	IF(TG_OP='INSERT') THEN
        	UPDATE ".$CC_CONFIG['playListContentsTable']." SET position = (position + 1) WHERE (playlist_id = new.playlist_id AND position >= new.position AND id != new.id);
        END IF;
        IF(TG_OP='DELETE') THEN
        	UPDATE ".$CC_CONFIG['playListContentsTable']." SET position = (position - 1) WHERE (playlist_id = old.playlist_id AND position > old.position);
        END IF;
        RETURN NULL;
    END;
    \$calc_pos\$
	LANGUAGE 'plpgsql';

	CREATE TRIGGER calculate_position AFTER INSERT OR DELETE ON ".$CC_CONFIG['playListContentsTable']."
    FOR EACH ROW EXECUTE PROCEDURE calculate_position();

    CREATE OR REPLACE VIEW cc_playlisttimes AS (
    SELECT PL.id, COALESCE(T.length, '00:00:00') AS length
    from ".$CC_CONFIG['playListTable']." AS PL LEFT JOIN
    (SELECT playlist_id AS id, text(SUM(cliplength)) AS length
    FROM ".$CC_CONFIG['playListContentsTable']." GROUP BY playlist_id) AS T

    ON PL.id = T.id
    );

    ";

    camp_install_query($sql);

} else {
    echo "   * Skipping: database table already exists: ".$CC_CONFIG['playListContentsTable']."\n";
}

//if (!camp_db_sequence_exists($CC_CONFIG["filesSequence"])) {
//    echo " * Creating database sequence for ".$CC_CONFIG['filesTable']."...\n";
//    $CC_DBC->createSequence($CC_CONFIG['filesSequence']);
////    $sql = "CREATE SEQUENCE ".$CC_CONFIG["filesSequence"]
////  		." INCREMENT 1
////  			MINVALUE 1
////  			MAXVALUE 9223372036854775807
////  			START 1000000
////  			CACHE 1";
////    camp_install_query($sql);
//} else {
//    echo " * Skipping: database sequence already exists: ".$CC_CONFIG['filesSequence']."\n";
//}

if (!camp_db_table_exists($CC_CONFIG['accessTable'])) {
    echo "   * Creating database table ".$CC_CONFIG['accessTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['accessTable']." (
        gunid bigint,                             -- global unique id
        token bigint,                             -- access token
        chsum char(32) not null default'',        -- md5 checksum
        ext varchar(128) not null default'',      -- extension
        type varchar(20) not null default'',      -- access type
        parent bigint,                            -- parent token
        owner int REFERENCES ".$CC_CONFIG['subjTable'].",  -- subject have started it
        ts timestamp
    )";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['accessTable']."_token_idx
        ON ".$CC_CONFIG['accessTable']." (token)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['accessTable']."_gunid_idx
        ON ".$CC_CONFIG['accessTable']." (gunid)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['accessTable']."_parent_idx
        ON ".$CC_CONFIG['accessTable']." (parent)";
    camp_install_query($sql);
} else {
    echo "   * Skipping: database table already exists: ".$CC_CONFIG['accessTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['transTable'])) {
    echo "   * Creating database table ".$CC_CONFIG['transTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['transTable']." (
        id int not null,          -- primary key
        trtok char(16) not null,  -- transport token
        direction varchar(128) not null,  -- direction: up|down
        state varchar(128) not null,      -- state
        trtype varchar(128) not null,     -- transport type
        lock char(1) not null default 'N',-- running lock
        target varchar(255) default NULL, -- target system,
                                          -- if NULL => predefined set
        rtrtok char(16) default NULL,     -- remote hub's transport token
        mdtrtok char(16),         -- metadata transport token
        gunid bigint,             -- global unique id
        pdtoken bigint,           -- put/download token from archive
        url varchar(255),         -- url on remote side
        localfile varchar(255),   -- pathname of local part
        fname varchar(255),       -- mnemonic filename
        title varchar(255),       -- dc:title mdata value (or filename ...)
        expectedsum char(32),     -- expected file checksum
        realsum char(32),         -- checksum of transported part
        expectedsize int,         -- expected filesize in bytes
        realsize int,             -- filesize of transported part
        uid int,                  -- local user id of transport owner
        errmsg varchar(255),      -- error message string for failed tr.
        jobpid int,               -- pid of transport job
        start timestamp,          -- starttime
        ts timestamp              -- mtime
    )";
    camp_install_query($sql, false);

    $CC_DBC->createSequence($CC_CONFIG['transSequence']);
    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['transTable']."_id_idx
        ON ".$CC_CONFIG['transTable']." (id)";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['transTable']."_trtok_idx
        ON ".$CC_CONFIG['transTable']." (trtok)";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['transTable']."_token_idx
        ON ".$CC_CONFIG['transTable']." (pdtoken)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['transTable']."_gunid_idx
        ON ".$CC_CONFIG['transTable']." (gunid)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['transTable']."_state_idx
        ON ".$CC_CONFIG['transTable']." (state)";
    camp_install_query($sql);
} else {
    echo "   * Skipping: database table already exists: ".$CC_CONFIG['transTable']."\n";
}

/**
 * Scheduler tables.
 */
if (!camp_db_table_exists($CC_CONFIG['scheduleTable'])) {
    echo "   * Creating database table ".$CC_CONFIG['scheduleTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['scheduleTable']."("
          ."  id bigint NOT NULL,"
          ."  playlist_id integer NOT NULL,"
          ."  starts timestamp without time zone NOT NULL,"
          ."  ends timestamp without time zone NOT NULL,"
          ."  group_id integer,"
          ."  file_id integer,"
          ."  clip_length time without time zone DEFAULT '00:00:00'::time without time zone,"
          ."  fade_in time without time zone DEFAULT '00:00:00'::time without time zone,"
          ."  fade_out time without time zone DEFAULT '00:00:00'::time without time zone,"
          ."  cue_in time without time zone DEFAULT '00:00:00'::time without time zone,"
          ."  cue_out time without time zone DEFAULT '00:00:00'::time without time zone,"
          ."  CONSTRAINT cc_schedule_pkey PRIMARY KEY (id),"
          ."  CONSTRAINT unique_id UNIQUE (id))";
    camp_install_query($sql);
} else {
    echo "   * Skipping: database table already exists: ".$CC_CONFIG['scheduleTable']."\n";
}


if (!camp_db_table_exists($CC_CONFIG['backupTable'])) {
    echo "   * Creating database table ".$CC_CONFIG['backupTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['backupTable']." ("
    ."   token       VARCHAR(64)     NOT NULL,"
    ."   sessionId   VARCHAR(64)     NOT NULL,"
    ."   status      VARCHAR(32)     NOT NULL,"
    ."   fromTime    TIMESTAMP       NOT NULL,"
    ."   toTime      TIMESTAMP       NOT NULL,"
    ."   PRIMARY KEY(token))";
    camp_install_query($sql);
} else {
    echo "   * Skipping: database table already exists: ".$CC_CONFIG['backupTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['prefTable'])) {
    echo "   * Creating database table ".$CC_CONFIG['prefTable']."...";
    //$CC_DBC->createSequence($CC_CONFIG['prefSequence']);
    $sql = "CREATE TABLE ".$CC_CONFIG['prefTable']." (
        id int not null,
        subjid int REFERENCES ".$CC_CONFIG['subjTable']." ON DELETE CASCADE,
        keystr varchar(255),
        valstr text
    )";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['prefTable']."_id_idx
        ON ".$CC_CONFIG['prefTable']." (id)";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['prefTable']."_subj_key_idx
        ON ".$CC_CONFIG['prefTable']." (subjid, keystr)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['prefTable']."_subjid_idx
        ON ".$CC_CONFIG['prefTable']." (subjid)";
    camp_install_query($sql);

    echo "   * Inserting starting data into table ".$CC_CONFIG['prefTable']."...";
    $stPrefGr = Subjects::GetSubjId($CC_CONFIG['StationPrefsGr']);
    Prefs::Insert($CC_CONFIG["systemPrefId"], 'stationName', "Radio Station 1");
//    $genres = file_get_contents( dirname(__FILE__).'/../genres.xml');
//    Prefs::Insert($CC_CONFIG["systemPrefId"], 'genres', $genres);
    echo "done.\n";
} else {
    echo "   * Skipping: database table already exists: ".$CC_CONFIG['prefTable']."\n";
}

//------------------------------------------------------------------------
// Install default data
//------------------------------------------------------------------------
echo " *** Inserting Default Data ***\n";

// Add the "Station Preferences" group
if (!empty($CC_CONFIG['StationPrefsGr'])) {
    if (!Subjects::GetSubjId('scheduler')) {
        echo "   * Creating group '".$CC_CONFIG['StationPrefsGr']."'...";
        $stPrefGr = Subjects::AddSubj($CC_CONFIG['StationPrefsGr']);
        Subjects::AddSubjectToGroup('root', $CC_CONFIG['StationPrefsGr']);
        echo "done.\n";
    } else {
        echo "   * Skipping: group already exists: '".$CC_CONFIG['StationPrefsGr']."'\n";
    }
}

// Add the root user if it doesnt exist yet.
$rootUid = Subjects::GetSubjId('root');
if (!$rootUid) {
    echo "   * Creating user 'root'...";
    $rootUid = BasicStor::addSubj("root", $CC_CONFIG['tmpRootPass']);

    // Add root user to the admin group
    //$r = Subjects::AddSubjectToGroup('root', $CC_CONFIG['AdminsGr']);
    //if (PEAR::isError($r)) {
    //return $r;
    //}
    echo "done.\n";
} else {
    echo "   * Skipping: user already exists: 'root'\n";
}

// Create the user named 'scheduler'.
if (!Subjects::GetSubjId('scheduler')) {
    echo "   * Creating user 'scheduler'...";
    $subid = Subjects::AddSubj('scheduler', $CC_CONFIG['schedulerPass']);
    $res = Alib::AddPerm($subid, 'read', '0', 'A');
    //$r = Subjects::AddSubjectToGroup('scheduler', $CC_CONFIG['AllGr']);
    echo "done.\n";
} else {
    echo "   * Skipping: user already exists: 'scheduler'\n";
}

// Need to add 'scheduler' to group StationPrefs
Subjects::AddSubjectToGroup('scheduler', $CC_CONFIG['StationPrefsGr']);


//------------------------------------------------------------------------
// Install storage directories
//------------------------------------------------------------------------
echo " *** Directory Setup ***\n";
foreach (array('storageDir', 'bufferDir', 'transDir', 'accessDir', 'cronDir') as $d) {
    $test = file_exists($CC_CONFIG[$d]);
    if ( $test === FALSE ) {
        @mkdir($CC_CONFIG[$d], 02775);
        if (file_exists($CC_CONFIG[$d])) {
            $rp = realpath($CC_CONFIG[$d]);
            echo "   * Directory $rp created\n";
        } else {
            echo "   * Failed creating {$CC_CONFIG[$d]}\n";
            exit(1);
        }
    } elseif (is_writable($CC_CONFIG[$d])) {
        $rp = realpath($CC_CONFIG[$d]);
        echo "   * Skipping directory already exists: $rp\n";
    } else {
        $rp = realpath($CC_CONFIG[$d]);
        echo "   * WARNING: Directory already exists, but is not writable: $rp\n";
        //exit(1);
    }
    $CC_CONFIG[$d] = $rp;
}

//------------------------------------------------------------------------
// Storage directory writability test
//------------------------------------------------------------------------

//echo "   * Testing writability of ".$CC_CONFIG['storageDir']."...";
//if (!($fp = @fopen($CC_CONFIG['storageDir']."/_writeTest", 'w'))) {
//    echo "\nPlease make directory {$CC_CONFIG['storageDir']} writeable by your webserver".
//        "\nand run install again\n\n";
//    exit(1);
//} else {
//    fclose($fp);
//    unlink($CC_CONFIG['storageDir']."/_writeTest");
//}
//echo "done.\n";


//
// Make sure the Smarty Templates Compiled directory has the right perms
//
echo "   * Setting dir permissions...\n";
install_setDirPermissions($CC_CONFIG["smartyTemplateCompiled"]);
install_setDirPermissions($CC_CONFIG["storageDir"]);
install_setDirPermissions($CC_CONFIG["bufferDir"]);
install_setDirPermissions($CC_CONFIG["transDir"]);
install_setDirPermissions($CC_CONFIG["accessDir"]);


//------------------------------------------------------------------------
// Install Cron job
//------------------------------------------------------------------------
$m = '*/2';
$h ='*';
$dom = '*';
$mon = '*';
$dow = '*';
$command = realpath("{$CC_CONFIG['cronDir']}/transportCron.php");
$old_regex = '/transportCron\.php/';
echo " * Install storageServer cron job...\n";

$cron = new Cron();
$access = $cron->openCrontab('write');
if ($access != 'write') {
    do {
        $r = $cron->forceWriteable();
    } while ($r);
}

foreach ($cron->ct->getByType(CRON_CMD) as $id => $line) {
    if (preg_match($old_regex, $line['command'])) {
        echo "    * Removing old entry: ".$line['command']."\n";
        $cron->ct->delEntry($id);
    }
}
echo "    * Adding new entry: ".$command."\n";
$cron->ct->addCron($m, $h, $dom, $mon, $dow, $command);
$cron->closeCrontab();
echo "   Done.\n";

echo "*******************************\n";
echo "* Campcaster Install Complete *\n";
echo "*******************************\n";

?>