<div class="container_elements side_elements">
	<h1>##Open Playlist##</h1>
	{if $PL->activeId}
	<input type="button" class="button" onClick="location.href='ui_browser.php?id=&act=PL.simpleManagement'"   value="Edit" />
	<input type="button" class="button" value="Delete"  onClick="popup('{$UI_BROWSER}?popup[]=PL.confirmDelete',  'PL.deleteActive',   400, 50)" />
	<div id="spl_info">
		<div><span class="sub">Title:</span><span>{$PL->title}</span></div>
		<div><span class="sub">Length:</span><span>{niceTime in=$PL->duration}</span></div>
	</div>
	
	<form name="SPL">
	<div class="pl_row" id="spl_head">
    	<span class="spl_input"><input type="checkbox" class="checkbox" name="all" onClick="collector_switchAll('SPL')"/></span>
    	<span class="spl_title">Title</span>
    	<span class="spl_artist">Creator</span>
    	<span>Playlength</span>
	</div>
	<ul id="spl_sortable">
		{foreach from=$PL->getActiveArr($PL->activeId) key='pos' item='i'}
		<li class="pl_row" id="spl_{$pos}">
		<span class="spl_input"><input type="checkbox" class="checkbox" name="{$pos}"/></span>
		<span class="spl_title">{$i.CcFiles.TrackTitle|truncate:12:"...":true}</span>
		<span class="spl_artist">{$i.CcFiles.ArtistName|truncate:12:"...":true}</span>
		 <span>{niceTime in=$i.DbCliplength}</span>
		</li>
		{/foreach}
	
    {if is_null($pos)}  
    	<li class="spl_empty">##Empty playlist##</li>   
	{/if}
	</ul>
	
	</form>
	<input type="button" class="button" onClick="collector_submit('SPL', 'PL.removeItem')"   value="Remove" />
	{else}
    <input type="button" class="button" onClick="hpopup('ui_handler.php?act=PL.create')" value="New" />
    {/if}
	
</div>