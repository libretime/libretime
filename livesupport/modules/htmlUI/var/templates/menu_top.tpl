	<!-- start nav -->
		<div class="container_nav">
			<ul id="nav">
				<li><a href="{$UI_BROWSER}?act=fileList&id={$START.fid}">##File List##</a></li>
                <!-- <li><a href="{$UI_BROWSER}?id={$START.fid}&act=uploadFileM">##UploadM##</a></li>  -->
                <li><a href="{$UI_BROWSER}?folderId={$START.fid}&act=editFile">##Upload##</a></li>
                <li><a href="{$UI_BROWSER}?folderId={$START.fid}&act=editWebstream">##Stream##</a></li>
                <li><a href="{$UI_BROWSER}?id={$START.fid}&act=PL.simpleManagement">##PL Editor##</a></li>
                <li><a href="{$UI_BROWSER}?id={$START.id}&act=SEARCH">##Search##</a></li>
                <li><a href="{$UI_BROWSER}?id={$START.id}&act=BROWSE">##Browse##</a></li>
                <li><a href="{$UI_BROWSER}?act=SCHEDULER">##Scheduler##</a></li>
                <li><a href="{$UI_BROWSER}?id={$START.id}&act=subjects">##User/Groups##</a></li>
                <!-- <li><a href="{$UI_BROWSER}?act=MetaDataValues&Main=1">##Metadata##</a></li> -->
                <li><a href="{$UI_BROWSER}?act=changeStationPrefs">##StationPrefs##</a></li>
			</ul>
		</div>
	<!-- end nav -->
{*
<!-- old template -->
<div id="menu_top">
    <a href="{$UI_BROWSER}?act=fileList&id={$START.fid}">[File List]</a>
    <!-- <a href="{$UI_BROWSER}?id={$START.fid}&act=uploadFileM">[UploadM]</a>  -->
    <a href="{$UI_BROWSER}?folderId={$START.fid}&act=editFile">[Upload]</a>
    <a href="{$UI_BROWSER}?folderId={$START.fid}&act=editWebstream">[Stream]</a>
    <a href="{$UI_BROWSER}?id={$START.fid}&act=PL.simpleManagement">[PL Editor]</a>
    <a href="{$UI_BROWSER}?id={$START.id}&act=SEARCH">[Search]</a>
    <a href="{$UI_BROWSER}?id={$START.id}&act=BROWSE">[Browse]</a>
    <a href="{$UI_BROWSER}?act=SCHEDULER">[Scheduler]</a>
    <a href="{$UI_BROWSER}?id={$START.id}&act=subjects">{tra 0='[User/Groups]'}</a>
    <!-- <a href="{$UI_BROWSER}?act=MetaDataValues&Main=1">[Metadata]</a> -->
    <a href="{$UI_BROWSER}?act=changeStationPrefs">[StationPrefs]</a>
</div>
*}