<?php
require_once('DB.php');
require_once('./m2treeTest.php');
require_once("./conf.php");
PEAR::setErrorHandling(PEAR_ERROR_DIE);
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'errCallback');

function errCallback($err)
{
	if (assert_options(ASSERT_ACTIVE)==1) {
	    return;
	}
	echo "<pre>\n";
	echo "request: "; print_r($_REQUEST);
    echo "\ngm:\n".$err->getMessage()."\nui:\n".$err->getUserInfo()."\n";
    echo "<hr>BackTrace:\n";
    print_r($err->backtrace);
    echo "</pre>\n";
    exit;
}

$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

$m2 = new M2treeTest();
#$m2->uninstall();
#exit;
#$r = $m2->install(); if(PEAR::isError($r)){ echo $r->getMessage()."\n".$r->getUserInfo()."\n"; exit; }

M2tree::reset();
#$r = $m2->_test_addObj(); if(PEAR::isError($r)){ echo $r->getMessage()."\n".$r->getUserInfo()."\n"; exit; }
$r = $m2->_test();
if (PEAR::isError($r)) {
    echo $r->getMessage()."\n".$r->getUserInfo()."\n";
    exit;
}

/*
$parid = $m2->_t['s1'];
for($i=1; $i<=20; $i++){
    $r = $m2->addObj("X$i", "XX", $parid);
    if (PEAR::isError($r)) return $r;
    $parid = $r;
    //$m2->_t["p$i"] = $r;
}
$r = M2tree::DumpTree(); echo "$r\n";
*/


#$r = M2tree::GetSubTree($m2->_t['i1'], TRUE); var_dump($r);
#$r = $m2->getPath($m2->_t['r1'], 'id, name, level'); var_dump($r);
#$r = $m2->getPath($m2->_t['r1'], 'id, name, level', TRUE); var_dump($r);
/*
foreach(M2tree::GetAllObjects() as $k=>$obj){
    $r = M2tree::IsChildOf($m2->_t['r1'], $obj['id'], TRUE);
    echo "{$obj['name']}: $r\n";
}
*/
#$r = $m2->getDir($m2->_t['i1'], 'id, name, level'); var_dump($r);
#$r = $m2->getPath($m2->_t['s3'], 'name'); var_dump($r);
#$r = $m2->addObj("Issue1", "XX", $m2->_t["s4"]); var_dump($r);
#$r = M2tree::MoveObj($m2->_t['i1'], $m2->_t['s4']); var_dump($r);
#$r = M2tree::CopyObj($m2->_t['i1'], $m2->_t['s4']); var_dump($r);
#$r = M2tree::RemoveObj($m2->_t['p2']); var_dump($r);
#$r = $m2->renameObj($m2->_t['s1'], 'Section2'); var_dump($r);
#$r = $m2->renameObj($m2->_t['s3'], 'Section2'); var_dump($r);

$r = M2tree::DumpTree();
echo "$r\n";
?>