<?php
require_once 'DB.php';
require_once './m2treeTest.php';
require_once"./conf.php";
PEAR::setErrorHandling(PEAR_ERROR_DIE);
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'errCallback');

function errCallback($err)
{
	if(assert_options(ASSERT_ACTIVE)==1) return;
	echo "<pre>\n";
	echo "request: "; print_r($_REQUEST);
    echo "\ngm:\n".$err->getMessage()."\nui:\n".$err->getUserInfo()."\n";
    echo "<hr>BackTrace:\n";
    print_r($err->backtrace);
    echo "</pre>\n";
    exit;
}

$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);

$m2 = &new M2treeTest($dbc, $config);
#$m2->uninstall();
#exit;
#$r = $m2->install(); if($dbc->isError($r)){ echo $r->getMessage()."\n".$r->getUserInfo()."\n"; exit; }

$m2->reset();
#$r = $m2->_test_addObj(); if($dbc->isError($r)){ echo $r->getMessage()."\n".$r->getUserInfo()."\n"; exit; }
$r = $m2->_test(); if($dbc->isError($r)){ echo $r->getMessage()."\n".$r->getUserInfo()."\n"; exit; }

/*
$parid = $m2->_t['s1'];
for($i=1; $i<=20; $i++){
    $r = $m2->addObj("X$i", "XX", $parid);
    if($m2->dbc->isError($r)) return $r;
    $parid = $r;
    //$m2->_t["p$i"] = $r;
}
$r = $m2->dumpTree(); echo "$r\n";
*/


#$r = $m2->getSubTree($m2->_t['i1'], TRUE); var_dump($r);
#$r = $m2->getPath($m2->_t['r1'], 'id, name, level'); var_dump($r);
#$r = $m2->getPath($m2->_t['r1'], 'id, name, level', TRUE); var_dump($r);
/*
foreach($m2->getAllObjects() as $k=>$obj){
    $r = $m2->isChildOf($m2->_t['r1'], $obj['id'], TRUE);
    echo "{$obj['name']}: $r\n";
}
*/
#$r = $m2->getDir($m2->_t['i1'], 'id, name, level'); var_dump($r);
#$r = $m2->getPath($m2->_t['s3'], 'name'); var_dump($r);
#$r = $m2->addObj("Issue1", "XX", $m2->_t["s4"]); var_dump($r);
#$r = $m2->moveObj($m2->_t['i1'], $m2->_t['s4']); var_dump($r);
#$r = $m2->copyObj($m2->_t['i1'], $m2->_t['s4']); var_dump($r);
#$r = $m2->removeObj($m2->_t['p2']); var_dump($r);
#$r = $m2->renameObj($m2->_t['s1'], 'Section2'); var_dump($r);
#$r = $m2->renameObj($m2->_t['s3'], 'Section2'); var_dump($r);

$r = $m2->dumpTree(); echo "$r\n";
?>