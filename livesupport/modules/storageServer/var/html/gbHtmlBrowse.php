<?php
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
            'listdata'  => ($gb->getFileType($id)=='Folder'?
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
<title>Storage - browser</title>
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
    var newPath=prompt('Destination folder (relative path, e.g. "..", "folderName", "../folderName):', relPath);
    if(newPath==null || newPath=='') return;
    location.href='gbHttp.php?id='+id+'&act=move&newPath='+newPath;
 }
 function fcopy(id, relPath){
    var newPath=prompt('Destination folder (relative path, e.g. "..", "folderName", "../folderName):', relPath);
    if(newPath==null || newPath=='') return;
    location.href='gbHttp.php?id='+id+'&act=copy&newPath='+newPath;
 }
 function freplicate(name, id){
    var np=prompt('Destination folder (relative path, e.g. "..", "folderName", "../folderName):', id);
    if(np==null || np=='') return;
    location.href='gbHttp.php?id='+id+'&act=repl&newparid='+np;
 }
 function newFolder(){
    var nn=prompt('New folder name:');
    if(nn==null) return;
    location.href='gbHttp.php?id=<?php echo$tpldata['id']?>&act=newFolder&newname='+nn;
 }
-->
</script>
</head><body>
<div id="rmenu">
 Logged as: <span class="b"><?php echo$tpldata['loggedAs']?></span><br>
 <a href="gbHttp.php?act=logout">logout</a><br>
 <a href="gbHtmlPerms.php?id=<?php echo$tpldata['id']?>">Permission editor</a><br>
 <a href="gbHtmlSubj.php">User/Group editor</a><br>
</div>

<?php if($tpldata['showMenu']){?>
<h3>
 <a href="gbHtmlBrowse.php?act=getHomeDir" class="button">Home directory</a>
 <a href="gbHtmlBrowse.php?id=<?php echo$tpldata['id']?>&amp;act=newfile" class="button"><span class="hidden">[</span>Upload&nbsp;new&nbsp;file<span class="hidden">]</span></a>
 <a href="javascript:newFolder()" class="button"><span class="hidden">[</span>Create&nbsp;new&nbsp;folder<span class="hidden">]</span></a>
<!-- <a href="gbHtmlBrowse.php?id=<?php echo$tpldata['id']?>&amp;act=sform" class="button"><span class="hidden">[</span>Search<span class="hidden">]</span></a>-->
</h3>
<?php }?>

<?php if($tpldata['showPath']){?>
 <h3>
    <a href="gbHtmlBrowse.php?id=<?php echo$tpldata['id']?>&amp;tree=Y" class="button">Tree</a>&nbsp;&nbsp;
    <?php foreach($tpldata['pathdata'] as $o){?>
        <a href="gbHtmlBrowse.php?id=<?php echo urlencode($o['id'])?>"><?php echo$o['name']?></a>
        <?php if($o['type']=='Folder'){?><span class="slash b">/</span><?php }?>
    <?php }?>:
    <span style="padding-left:6em">
        <a href="gbHtmlPerms.php?id=<?php echo$id?>" class="button">permissions</a>
    </span>
 </h3>
<?php }?>

<?php if($tpldata['showTree']) if($tpldata['tree']){?>
 <?php foreach($tpldata['treedata'] as $o){?>
    <?php echo str_repeat('&nbsp;', ($tpldata['tree']?intval($o['level']):3)*2)?>
    <a href="gbHtmlBrowse.php?id=<?php echo$o['id']?>"><?php echo$o['name']?></a>
    <br>
 <?php }?>
<?php }else{?>
 <table border="0">
    <tr><th>fname</th><th>gunid</th><th>actions</th></tr>
 <?php foreach($tpldata['listdata'] as $o){?>
    <tr><td valign="top">
    <?php echo str_repeat('&nbsp;', ($tpldata['tree']?intval($o['level']):3)*2)?><span id="ID<?php echo$o['id']?>"
    ><a <?php if($o['type']=='Folder'){?>href="gbHtmlBrowse.php?id=<?php echo$o['id']?>"<?php }?>><?php echo$o['name']?></a
    ></span>
    </td><td valign="top">
    <i><?php echo($o['gunid'] ? "({$o['gunid']})" : '' )?></i>
    </td><td>
    <?php $a=array('Folder'=>'D', 'File'=>'F', 'Replica'=>'R', 'audioclip'=>'A', 'playlist'=>'P', 'webstream'=>'S'); echo$a[$o['type']]?>
    &nbsp;<a href="javascript:frename('<?php echo$o['name']?>', '<?php echo$o['id']?>')" class="button">rename</a>
    <?php if($o['type']!='Folder'){?>
    &nbsp;<a href="javascript:fmove('<?php echo$o['id']?>', '')" class="button">move</a>
    &nbsp;<a href="javascript:fcopy('<?php echo$o['id']?>', '')" class="button">copy</a>
    <?php }?>
<?php /*?>
    &nbsp;<a href="javascript:freplicate('<?php echo$o['name']?>', '<?php echo$o['id']?>')" class="button">replicate</a>
<?php */?>
    &nbsp;<a href="gbHtmlPerms.php?id=<?php echo$o['id']?>" class="button">permissions</a>
    &nbsp;<a href="gbHttp.php?act=delete&amp;id=<?php echo$o['id']?>" class="button"
        onClick="return confirm('Delete object &quot;<?php echo$o['name']?>&quot;?')">DEL</a>
    <?php if($o['type']=='File' || $o['type']=='audioclip'){?>
    &nbsp;<a href="../xmlrpc/simpleGet.php?sessid=<?php echo$sessid?>&amp;id=<?php echo$o['gunid']?>" class="button">simpleGet</a>
    &nbsp;<a href="gbHttp.php?act=getInfo&amp;id=<?php echo$o['id']?>" class="button">Analyze</a>
    &nbsp;<a href="gbHttp.php?act=getMdata&amp;id=<?php echo$o['id']?>" class="button">MetaData</a>
    <?php }?>
    <?php if($o['type']=='playlist'){?>
    &nbsp;<a href="../xmlrpc/simpleGet.php?sessid=<?php echo$sessid?>&amp;id=<?php echo$o['gunid']?>" class="button">simpleGet</a>
    &nbsp;<a href="gbHttp.php?act=getMdata&amp;id=<?php echo$o['id']?>" class="button">MetaData</a>
    <?php }?>
    <?php if($o['type']=='webstream'){?>
    &nbsp;<a href="../xmlrpc/simpleGet.php?sessid=<?php echo$sessid?>&amp;id=<?php echo$o['gunid']?>" class="button">simpleGet</a>
    &nbsp;<a href="gbHttp.php?act=getMdata&amp;id=<?php echo$o['id']?>" class="button">MetaData</a>
    <?php }?>
    <?php if($o['type']=='Replica'){?>
    &nbsp; (-&gt;<?php echo$o['target']?>)
    <?php }?>
    </td>
   </tr>
 <?php } if(count($tpldata['listdata'])==0){?>
  <tr><td>No objects</td></tr>
 <?php }?>
 </table>
<?php }?>

<?php if($tpldata['showEdit']){?>
<form method="post" enctype="multipart/form-data" action="gbHttp.php">
<?php #<form method="post" enctype="multipart/form-data" action="http://localhost:8000">?>
<table>
 <tr><td>File name:</td><td><input type="text" name="filename" value=""></td></tr>
 <tr><td>Media file:</td><td><input type="file" name="mediafile"></td></tr>
 <tr><td>Metadata file:</td><td><input type="file" name="mdatafile"></td></tr>
<?php for($i=0; $i<0; $i++){?>
 <tr><td>
    <select name="elnames[<?php echo$i?>]">
    <?php $ii=0?>
    <?php foreach($fldsname as $fld=>$descr){?>
        <option value="<?php echo$fld?>"<?php echo($i==$ii++ ? ' selected' : '')?>><?php echo$descr?></option>
    <?php }?>
    </select>
 </td><td><input type="text" name="elvals[<?php echo$i?>]" value=""></td></tr>
<?php }?>
 <tr><td colspan="2"><input type="submit" value="Send!"></td></tr>
</table>
<input type="hidden" name="MAX_FILE_SIZE" value="1048576">
<input type="hidden" name="act" value="upload">
<input type="hidden" name="id" value="<?php echo$tpldata['id']?>">
</form>
<?php }?>

<?php if($tpldata['showSForm']){?>
<form method="post" action="gbHtmlBrowse.php">
<table>
 <tr><td>Search string:</td><td><input type="text" name="srch" value=""></td></tr>
 <tr><td colspan="2"><input type="submit" value="Send!"></td></tr>
</table>
<input type="hidden" name="act" value="search">
<input type="hidden" name="id" value="<?php echo$tpldata['id']?>">
</form>
<?php }?>

<?php if($tpldata['showSRes']){?>
<ul>
<?php  if(is_array($tpldata['search'])) foreach($tpldata['search'] as $k=>$v){?>
 <li><a href="gbHttp.php?act=getMdata&amp;id=<?php echo$gb->_idFromGunid($v['gunid'])?>"><?php echo$v['gunid']?></a>
<?php  }else{?>
 No items found
<?php  }?>
</ul>
<?php }?>

<?php if($tpldata['msg']){?>
<script type="text/javascript">
<!--
 alert('<?php echo$tpldata['msg']?>');
-->
</script><noscript><?php echo$tpldata['msg']?></noscript>
<?php }?>
</body></html>