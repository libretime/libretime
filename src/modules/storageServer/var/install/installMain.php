<?php
/**
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision: 2475 $
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */

// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

//------------------------------------------------------------------------------
// Install database tables
//------------------------------------------------------------------------------

if (!camp_db_table_exists($CC_CONFIG['treeTable'])) {
    echo " * Creating database table ".$CC_CONFIG['treeTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['treeTable']." (
        id int not null PRIMARY KEY,
        name varchar(255) not null default'',
        -- parid int,
        type varchar(255) not null default'',
        param varchar(255))";
    camp_install_query($sql, false);

    $CC_DBC->createSequence($CC_CONFIG['treeTable']."_id_seq");

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['treeTable']."_id_idx
        ON ".$CC_CONFIG['treeTable']." (id)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['treeTable']."_name_idx
        ON ".$CC_CONFIG['treeTable']." (name)";
    camp_install_query($sql);
} else {
    echo " * Skipping: database table already exists: ".$CC_CONFIG['treeTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['structTable'])) {
    echo " * Creating database table ".$CC_CONFIG['structTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['structTable']." (
        rid int not null PRIMARY KEY,
        objid int not null REFERENCES ".$CC_CONFIG['treeTable']." ON DELETE CASCADE,
        parid int not null REFERENCES ".$CC_CONFIG['treeTable']." ON DELETE CASCADE,
        level int)";
    camp_install_query($sql, false);

    $CC_DBC->createSequence($CC_CONFIG['structTable']."_id_seq");

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['structTable']."_rid_idx
        ON ".$CC_CONFIG['structTable']." (rid)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['structTable']."_objid_idx
        ON ".$CC_CONFIG['structTable']." (objid)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['structTable']."_parid_idx
        ON ".$CC_CONFIG['structTable']." (parid)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['structTable']."_level_idx
        ON ".$CC_CONFIG['structTable']." (level)";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['structTable']."_objid_level_idx
        ON ".$CC_CONFIG['structTable']." (objid, level)";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['structTable']."_objid_parid_idx
        ON ".$CC_CONFIG['structTable']." (objid, parid)";
    camp_install_query($sql);
} else {
    echo " * Skipping: database table already exists: ".$CC_CONFIG['structTable']."\n";
}

//
// Insert the RootNode if its not there yet.
//
$sql = "SELECT * FROM ".$CC_CONFIG['treeTable']
       ." WHERE name='".$CC_CONFIG['RootNode']."'"
       ." AND type='RootNode'";
$row = $CC_DBC->GetRow($sql);
if (!PEAR::isError($row) && !$row) {
    echo " * Creating ROOT NODE in ".$CC_CONFIG['treeTable']."...";
    $oid = $CC_DBC->nextId($CC_CONFIG['treeTable']."_id_seq");
    if (PEAR::isError($oid)) {
        echo $oid->getMessage()."\n";
        //print_r($oid);
        exit();
    }
    $CC_DBC->query("
        INSERT INTO ".$CC_CONFIG['treeTable']."
            (id, name, type)
        VALUES
            ($oid, '".$CC_CONFIG['RootNode']."', 'RootNode')
    ");
    echo "done.\n";
} else {
    echo " * Skipping: Root node already exists in ".$CC_CONFIG['treeTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['classTable'])) {
    echo " * Creating database table ".$CC_CONFIG['classTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['classTable']." (
        id int not null PRIMARY KEY,
        cname varchar(20))";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['classTable']."_id_idx
        ON ".$CC_CONFIG['classTable']." (id)";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['classTable']."_cname_idx
        ON ".$CC_CONFIG['classTable']." (cname)";
    camp_install_query($sql);
} else {
    echo " * Skipping: database table already exists: ".$CC_CONFIG['classTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['cmembTable'])) {
    echo " * Creating database table ".$CC_CONFIG['cmembTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['cmembTable']." (
        objid int not null,
        cid int not null)";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['cmembTable']."_idx
        ON ".$CC_CONFIG['cmembTable']." (objid, cid)";
    camp_install_query($sql);
} else {
    echo " * Skipping: database table already exists: ".$CC_CONFIG['cmembTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['subjTable'])) {
    echo " * Creating database table ".$CC_CONFIG['subjTable']."...";
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

    $CC_DBC->createSequence($CC_CONFIG['subjTable']."_id_seq");
    echo "done.\n";
} else {
    echo " * Skipping: database table already exists: ".$CC_CONFIG['subjTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['smembTable'])) {
    echo " * Creating database table ".$CC_CONFIG['smembTable']."...";
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

    $CC_DBC->createSequence($CC_CONFIG['smembTable']."_id_seq");
    echo "done.\n";
} else {
    echo " * Skipping: database table already exists: ".$CC_CONFIG['smembTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['permTable'])) {
    echo " * Creating database table ".$CC_CONFIG['permTable']."...";
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

    $CC_DBC->createSequence($CC_CONFIG['permTable']."_id_seq");
    echo "done.\n";
} else {
    echo " * Skipping: database table already exists: ".$CC_CONFIG['permTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['sessTable'])) {
    echo " * Creating database table ".$CC_CONFIG['sessTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['sessTable']." (
        sessid char(32) not null PRIMARY KEY,
        userid int REFERENCES ".$CC_CONFIG['subjTable']." ON DELETE CASCADE,
        login varchar(255),
        ts timestamp)";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['sessTable']."_sessid_idx
        ON ".$CC_CONFIG['sessTable']." (sessid)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['sessTable']."_userid_idx
        ON ".$CC_CONFIG['sessTable']." (userid)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['sessTable']."_login_idx
        ON ".$CC_CONFIG['sessTable']." (login)";
    camp_install_query($sql);
} else {
    echo " * Skipping: database table already exists: ".$CC_CONFIG['sessTable']."\n";
}

/**
 * file states:
 *  <ul>
 *      <li>empty</li>
 *      <li>incomplete</li>
 *      <li>ready</li>
 *      <li>edited</li>
 *      <li>deleted</li>
 *  </ul>
 * file types:
 *  <ul>
 *      <li>audioclip</li>
 *      <li>playlist</li>
 *      <li>webstream</li>
 *  </ul>
 * access types:
 *  <ul>
 *      <li>access</li>
 *      <li>download</li>
 *  </ul>
 */
if (!camp_db_table_exists($CC_CONFIG['filesTable'])) {
    echo " * Creating database table ".$CC_CONFIG['filesTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['filesTable']." (
        id int not null,
        gunid bigint not null,                      -- global unique ID
        name varchar(255) not null default'',       -- human file id ;)
        mime varchar(255) not null default'',       -- mime type
        ftype varchar(128) not null default'',      -- file type
        state varchar(128) not null default'empty', -- file state
        currentlyaccessing int not null default 0,  -- access counter
        editedby int REFERENCES ".$CC_CONFIG['subjTable'].", -- who edits it
        mtime timestamp(6) with time zone,           -- lst modif.time
        md5 char(32)
    )";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['filesTable']."_id_idx
        ON ".$CC_CONFIG['filesTable']." (id)";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['filesTable']."_gunid_idx
        ON ".$CC_CONFIG['filesTable']." (gunid)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['filesTable']."_name_idx
        ON ".$CC_CONFIG['filesTable']." (name)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['filesTable']."_md5_idx
        ON ls_files (md5)";
    camp_install_query($sql);
} else {
    echo " * Skipping: database table already exists: ".$CC_CONFIG['filesTable']."\n";
}

/**
 * id  subjns  subject predns  predicate   objns   object
 * y1  literal xmbf    NULL    namespace   literal http://www.sotf.org/xbmf
 * x1  gunid   <gunid> xbmf    contributor NULL    NULL
 * x2  mdid    x1      xbmf    role        literal Editor
 *
 * predefined shortcuts:
 *      _L              = literal
 *      _G              = gunid (global id of media file)
 *      _I              = mdid (local id of metadata record)
 *      _nssshortcut    = namespace shortcut definition
 *      _blank          = blank node
 */
if (!camp_db_table_exists($CC_CONFIG['mdataTable'])) {
    echo " * Creating database table ".$CC_CONFIG['mdataTable']."...";
    $CC_DBC->createSequence($CC_CONFIG['mdataTable']."_id_seq");
    $sql = "CREATE TABLE ".$CC_CONFIG['mdataTable']." (
        id int not null,
        gunid bigint,
        subjns varchar(255),             -- subject namespace shortcut/uri
        subject varchar(255) not null default '',
        predns varchar(255),             -- predicate namespace shortcut/uri
        predicate varchar(255) not null,
        predxml char(1) not null default 'T', -- Tag or Attribute
        objns varchar(255),              -- object namespace shortcut/uri
        object text
    )";
    camp_install_query($sql, false);

    $sql = "CREATE UNIQUE INDEX ".$CC_CONFIG['mdataTable']."_id_idx
        ON ".$CC_CONFIG['mdataTable']." (id)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['mdataTable']."_gunid_idx
        ON ".$CC_CONFIG['mdataTable']." (gunid)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['mdataTable']."_subj_idx
        ON ".$CC_CONFIG['mdataTable']." (subjns, subject)";
    camp_install_query($sql, false);

    $sql = "CREATE INDEX ".$CC_CONFIG['mdataTable']."_pred_idx
        ON ".$CC_CONFIG['mdataTable']." (predns, predicate)";
    camp_install_query($sql);
} else {
    echo " * Skipping: database table already exists: ".$CC_CONFIG['mdataTable']."\n";
}

if (!camp_db_table_exists($CC_CONFIG['accessTable'])) {
    echo " * Creating database table ".$CC_CONFIG['accessTable']."...";
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
    echo " * Skipping: database table already exists: ".$CC_CONFIG['accessTable']."\n";
}

echo " * Inserting starting data into tables...\n";
$gb = new GreenBox();
$r = $gb->initData(true);
if (PEAR::isError($r)) {
    echo "\n   * ERROR: ";
    print_r($r);
}
//echo "done.\n";

//------------------------------------------------------------------------------
// Submodules
//------------------------------------------------------------------------------

if (!camp_db_table_exists($CC_CONFIG['transTable'])) {
    echo " * Creating database table ".$CC_CONFIG['transTable']."...";
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

    $CC_DBC->createSequence($CC_CONFIG['transTable']."_id_seq");
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
    echo " * Skipping: database table already exists: ".$CC_CONFIG['transTable']."\n";
}

?>