<?
$gb->initDb();
$gb->init();
#system("rm -f {$config['storageDir']}/*.bin {$config['storageDir']}/*.xml");
$d = $gb->testData();
$gb->putFile('/folder1/folder1_2/folder1_2_1', 'fileA', "123\n345\n", "<xml/>", 'at');
$gb->createReplica('/folder1/folder1_2/folder1_2_1/fileA', '/folder1/folder1_2/folder1_2_1', 'replFA', 'at');
$gb->putFile('/folder1/folder1_2/folder1_2_1', 'fileB', "123\n345\n789\n", "<xml/>", 'at');
$gb->ovewriteMetadata('/folder1/folder1_2/folder1_2_1/fileA', "<xml>\n</xml>", 'at');

$gb->deleteFile('/folder1/folder1_2/folder1_2_1/fileB', 'at');

?>