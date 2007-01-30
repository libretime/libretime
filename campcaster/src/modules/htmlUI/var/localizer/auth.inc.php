<?php
function login(&$data)
{
    include(dirname(__FILE__).'/../../../storageServer/var/conf.php');
    include_once(dirname(__FILE__).'/../../../storageServer/var/GreenBox.php');
    include_once('DB.php');
    global $CC_DBC, $CC_CONFIG;

    $CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);

    if (DB::isError($CC_DBC)) {
        die($CC_DBC->getMessage());
    }

    $CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);
    $gb = new GreenBox();

    if (!$data['PHP_AUTH_USER'] || !$data['PHP_AUTH_PW']) {
        return FALSE;
    }

    $sessid = Alib::Login($data['PHP_AUTH_USER'], $data['PHP_AUTH_PW']);

    if (!$sessid || PEAR::isError($sessid)){
        return FALSE;
    }

    setcookie($CC_CONFIG['authCookieName'], $sessid);

    if (Subjects::IsMemberOf(GreenBox::GetSessUserId($sessid), Subjects::GetSubjId('Admins')) !== TRUE) {
        return FALSE;
    }

    $id = M2tree::GetObjId($data['PHP_AUTH_USER'], $gb->storId);

    if (PEAR::isError($id)) {
        return FALSE;
    }

    return TRUE;
}

function authenticate()
{
    Header("WWW-Authenticate: Basic realm=\"My Realm\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Access denied.";
    exit;
}


if (!isset($_SERVER['PHP_AUTH_USER'])) {
    authenticate();
} elseif (login($_SERVER) !== TRUE) {
    authenticate();
}
?>