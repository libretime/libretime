<?
#echo"<pre>\n"; print_r($_FILES); print_r($_REQUEST); print_r($_SERVER); exit;
require_once"gbHtml_h.php";
require_once"gbHtmlTestAuth.php";

$fldsname=array('author'=>'Author', 'title'=>'Title', 'creator'=>'Creator',
    'description'=>'Description', 'subject'=>'Subject', 'genre'=>'Genre');

$sessid = $_REQUEST[$config['authCookieName']];
$userid = $gb->getSessUserId($sessid);
$login = $gb->getSessLogin($sessid);

#$path = ($_REQUEST['path']=='' ? '/' : $_REQUEST['path']);
$id = (!$_REQUEST['id'] ? $gb->storId : $_REQUEST['id']);

#echo"<pre>\nsessid=$sessid\nuserid=$userid\nlogin=$login\n"; exit;

$tpldata = array(
    'msg'       => $_SESSION['alertMsg'],
    'loggedAs'  => $login,
    'id'        => $id,
); unset($_SESSION['alertMsg']);

switch($_REQUEST['act']){
    case"getHomeDir":
        $id = $gb->getObjId($login, $gb->storId);
        $tpldata['id'] = $id;
    default:
#        echo"<pre>\n$path\n$upath<hr>\n"; print_r($_FILES); print_r($_REQUEST); exit;
        $tpldata=array_merge($tpldata, array(
            'pathdata'  => $gb->getPath($id, $sessid),
            'listdata'  => ($gb->getObjType($id)=='Folder'?
                $gb->listFolder($id, $sessid):array()
            ),
            'tree'  => ($_REQUEST['tree']=='Y'),
            'showPath'  => true,
            'showTree'  => true,
        ));
        if($_REQUEST['tree']=='Y'){
            $tpldata['treedata'] = $gb->getSubTree($id, $sessid);
        }
        break;
    case"newfile":
        $tpldata=array(
            'pathdata'  => $gb->getPath($id, $sessid),
            'showEdit'  => true,
            'id'    => $id,
        );
        break;
    case"sform":
        $tpldata=array(
#            'pathdata'  => $gb->getPath($path, $sessid),
            'showSForm'  => true,
            'id'    => $id,
        );
        break;
    case"search":
        $tpldata=array(
#            'pathdata'  => $gb->getPath($path, $sessid),
            'search'  => $gb->localSearch($_REQUEST['srch'], $sessid),
            'showSRes'  => true,
            'id'    => $id,
        );
        break;
}

if(PEAR::isError($tpldata['listdata'])){
    $tpldata['msg'] = $tpldata['listdata']->getMessage();
    $tpldata['listdata'] = array();
}
#echo"<pre>\n$path<hr>\n"; print_r($tpldata['pathdata']); print_r($tpldata); exit;

$tpldata['showMenu']=true;


// =================== template: =================== 

?>
<html><head>
<title>Browser</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="default.css">
<style type="text/css">
<!--
 #rmenu { float:right; margin-right:1em; border:1px solid black; background-color:#ddd; padding:2px 1ex; }
 #parent, #parent a { background-color:#888; font-weight:bold; color:white; }
 #tree { width:50%; }
-->
</style>
<script type="text/javascript">
<!--
 function frename(name, id){
    var s=document.getElementById('ID'+id);
    s.innerHTML='<form action="gbHttp.php" method="post" style="display:inline">'+
        '<input type="text" name="newname" value="'+name+'" size="12">'+
        '<input type="hidden" name="id" value="'+id+'">'+
        '<input type="hidden" name="act" value="rename">'+
    '</form>';
 }
 function fmove(id, relPath){
    var newPath=prompt('Destination folder (relative path):', relPath);
    if(newPath==null) return;
    location.href='gbHttp.php?id='+id+'&act=move&newPath='+newPath;
 }
 function fcopy(id, relPath){
    var newPath=prompt('Destination folder (relative path):', relPath);
    if(newPath==null) return;
    location.href='gbHttp.php?id='+id+'&act=copy&newPath='+newPath;
 }
 function freplicate(name, id){
    var np=prompt('Destination folder (relative path):', id);
    if(np==null) return;
    location.href='gbHttp.php?id='+id+'&act=repl&newparid='+np;
 }
 function newFolder(){
    var nn=prompt('New folder name:');
    if(nn==null) return;
    location.href='gbHttp.php?id=<?=$tpldata['id']?>&act=newFolder&newname='+nn;
 }
-->
</script>
</head><body>
<div id="rmenu">
 Logged as: <span class="b"><?php echo$tpldata['loggedAs']?></span><br>
 <a href="gbHttp.php?act=logout">logout</a><br>
 <a href="gbHtmlPerms.php?id=<?=$tpldata['id']?>">Permission editor</a><br>
 <a href="gbHtmlSubj.php">User/Group editor</a><br>
</div>

<?if($tpldata['showMenu']){?>
<h3>
 <a href="gbHtmlBrowse.php?act=getHomeDir" class="button">Home directory</a>
 <a href="gbHtmlBrowse.php?id=<?=$tpldata['id']?>&act=newfile" class="button"><span class="hidden">[</span>Upload&nbsp;new&nbsp;file<span class="hidden">]</span></a>
 <a href="javascript:newFolder()" class="button"><span class="hidden">[</span>Create&nbsp;new&nbsp;folder<span class="hidden">]</span></a>
 <a href="gbHtmlBrowse.php?id=<?=$tpldata['id']?>&act=sform" class="button"><span class="hidden">[</span>Search<span class="hidden">]</span></a>
</h3>
<?}?>

<?if($tpldata['showPath']){?>
 <h3>
    <a href="gbHtmlBrowse.php?id=<?=$tpldata['id']?>&tree=Y" class="button">Tree</a>&nbsp;&nbsp;
    <?foreach($tpldata['pathdata'] as $o){?>
        <a href="gbHtmlBrowse.php?id=<?=urlencode($o['id'])?>"><?=$o['name']?></a>
        <?if($o['type']=='Folder'){?><span class="slash b">/</span><?}?>
    <?}?>:
    <span style="padding-left:6em">
        <a href="gbHtmlPerms.php?id=<?=$id?>" class="button">permissions</a>
    </span>
 </h3>
<?}?>

<?if($tpldata['showTree']) if($tpldata['tree']){?>
 <?foreach($tpldata['treedata'] as $o){?>
    <?=str_repeat('&nbsp;', ($tpldata['tree']?intval($o['level']):3)*2)?>
    <a href="gbHtmlBrowse.php?id=<?=$o['id']?>"><?=$o['name']?></a>
    <br>
 <?}?>
<?}else{?>
 <table border="0">
 <?foreach($tpldata['listdata'] as $o){?>
    <tr><td valign="top">
    <?=str_repeat('&nbsp;', ($tpldata['tree']?intval($o['level']):3)*2)?><span id="ID<?=$o['id']?>"
    ><a <?if($o['type']=='Folder'){?>href="gbHtmlBrowse.php?id=<?=$o['id']?>"<?}?>><?=$o['name']?></a
    ></span>
    </td><td>
    <?$a=array('Folder'=>'D', 'File'=>'F', 'Replica'=>'R'); echo$a[$o['type']]?>
    &nbsp;<a href="javascript:frename('<?=$o['name']?>', '<?=$o['id']?>')" class="button">rename</a>
    &nbsp;<a href="javascript:fmove('<?=$o['id']?>', '.')" class="button">move</a>
    &nbsp;<a href="javascript:fcopy('<?=$o['id']?>', '.')" class="button">copy</a>
<?/*?>
    &nbsp;<a href="javascript:freplicate('<?=$o['name']?>', '<?=$o['id']?>')" class="button">replicate</a>
<?*/?>
    &nbsp;<a href="gbHtmlPerms.php?id=<?=$o['id']?>" class="button">permissions</a>
    &nbsp;<a href="gbHttp.php?act=delete&id=<?=$o['id']?>" class="button"
        onClick="return confirm('Delete object &quot;<?=$o['name']?>&quot;?')">DEL</a>
    <?if($o['type']=='File'){?>
    &nbsp;<a href="gbHttp.php?act=getFile&id=<?=$o['id']?>" class="button">Access</a>
    &nbsp;<a href="gbHttp.php?act=getInfo&id=<?=$o['id']?>" class="button">Analyze</a>
    &nbsp;<a href="gbHttp.php?act=getMdata&id=<?=$o['id']?>" class="button">MetaData</a>
    <?}?>
    <?if($o['type']=='Replica'){?>
    &nbsp; (-&gt;<?=$o['target']?>)
    <?}?>
    </td>
   </tr>
 <?} if(count($tpldata['listdata'])==0){?>
  <tr><td>No objects</td></tr>
 <?}?>
 </table>
<?}?>

<?if($tpldata['showEdit']){?>
<form method="post" enctype="multipart/form-data" action="gbHttp.php">
<?#<form method="post" enctype="multipart/form-data" action="http://localhost:8000">?>
<table>
 <tr><td>File name:</td><td><input type="text" name="filename" value=""></td></tr>
 <tr><td>Media file:</td><td><input type="file" name="mediafile"></td></tr>
 <tr><td>Metadata file:</td><td><input type="file" name="mdatafile"></td></tr>
<?for($i=0; $i<0; $i++){?>
 <tr><td>
    <select name="elnames[<?=$i?>]">
    <?$ii=0?>
    <?foreach($fldsname as $fld=>$descr){?>
        <option value="<?=$fld?>"<?=($i==$ii++ ? ' selected' : '')?>><?=$descr?></option>
    <?}?>
    </select>
 </td><td><input type="text" name="elvals[<?=$i?>]" value=""></td></tr>
<?}?>
 <tr><td colspan="2"><input type="submit" value="Send!"></td></tr>
</table>
<input type="hidden" name="MAX_FILE_SIZE" value="1048576">
<input type="hidden" name="act" value="upload">
<input type="hidden" name="id" value="<?=$tpldata['id']?>">
</form>
<?}?>

<?if($tpldata['showSForm']){?>
<form method="post" action="gbHtmlBrowse.php">
<table>
 <tr><td>Search string:</td><td><input type="text" name="srch" value=""></td></tr>
 <tr><td colspan="2"><input type="submit" value="Send!"></td></tr>
</table>
<input type="hidden" name="act" value="search">
<input type="hidden" name="id" value="<?=$tpldata['id']?>">
</form>
<?}?>

<?if($tpldata['showSRes']){?>
<ul>
<? if(is_array($tpldata['search'])) foreach($tpldata['search'] as $k=>$v){?>
 <li><a href="gbHttp.php?act=getMdata&id=<?=$gb->_idFromGunid($v['gunid'])?>"><?=$v['gunid']?></a>
<? }else{?>
 No items found
<? }?>
</ul>
<?}?>

<?php if($tpldata['msg']){?>
<script type="text/javascript">
<!--
 alert('<?php echo$tpldata['msg']?>');
-->
</script><noscript><?php echo$tpldata['msg']?></noscript>
<?php }?>
</body></html>