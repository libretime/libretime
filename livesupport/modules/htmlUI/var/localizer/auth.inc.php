<?php


function login(&$data)
{
    include_once dirname(__FILE__).'/../../../storageServer/var/conf.php';
    include_once dirname(__FILE__).'/../../../storageServer/var/GreenBox.php';
    $dbc = DB::connect($config['dsn'], TRUE);
    if (DB::isError($dbc)) {
        die($dbc->getMessage());
    }
    $dbc->setFetchMode(DB_FETCHMODE_ASSOC);
    $gb =& new GreenBox($dbc, $config);


    if (!$data['PHP_AUTH_USER'] || !$data['PHP_AUTH_PW']) {
        return FALSE;
    }
    $sessid = $gb->login($data['PHP_AUTH_USER'], $data['PHP_AUTH_PW']);
    if(!$sessid || PEAR::isError($sessid)){
        return FALSE;
    }
    setcookie($config['authCookieName'], $sessid);

    if ($gb->isMemberOf($gb->getSessUserId($sessid), $gb->getSubjId('Admins')) !== TRUE) {
        return FALSE;
    }

    $id = $gb->getObjId($data['PHP_AUTH_USER'], $gb->storId);
    if(PEAR::isError($id)) {
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
