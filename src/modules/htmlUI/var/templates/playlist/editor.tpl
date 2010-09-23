<!-- start new and improved! playlist editor --> 
<div class="pl_container" >
    <div class="pl_header">
        <div class="pl_header_left"><span>##Playlist Editor## </span></div>
        <div class="pl_header_right"><span>{$PL->title}</span><span>{niceTime in=$PL->duration} </span></div>
    </div>
    <div class="pl_contents">
    <form name="PL">
    <div class="pl_head">
        <span class="pl_input"><input type="checkbox" name="all" onClick="collector_switchAll('PL')"></span>
        <span class="pl_title">##Title##</span>
        <span class="pl_artist">##Artist##</span>
        <span class="pl_length">##Length##</span>
        <span class="pl_cue_in">##Cue In##</span>
        <span class="pl_cue_out">##Cue Out##</span>
        <span class="pl_playlength">##Playlength##</span>      
    </div>
    <div class="pl_main">
    	<ul id="pl_sortable">
        	{foreach from=$PL->getActiveArr($PL->activeId) key='pos' item='i'}
        	<li class="pl_row" id="pl_{$pos}">
                <span class="pl_input">
                	<input type="checkbox" class="checkbox" name="{$pos}"/>
                </span>
                <span class="pl_title">
                	{$i.track_title}
                </span>
                <span class="pl_artist">
                	{$i.artist_name}
                </span>
                <span class="pl_length" >
                    {assign var="_playlength" value=$i.length}{niceTime in=$_playlength}
                </span>
                <span class="pl_cue_in">
                    {assign var="_duration" value=$i.cuein}{niceTime in=$_duration}
                </span>
                <span class="pl_cue_out">
                    {assign var="_duration" value=$i.cueout}{niceTime in=$_duration}
                </span>
                <span class="pl_playlength">
                    {assign var="_duration" value=$i.cliplength}{niceTime in=$_duration}
                </span>
            </li>
        	{/foreach}
        	{if is_null($pos)}  
            	<li class="pl_empty">##Empty playlist##</li>   
        	{/if}
    	</ul>
    </div>
    
    </form>
    </div>
    <div class="pl_footer">
        <input type="button" class="button_large" onClick="collector_submit('PL', 'PL.removeItem')"   value="##Remove Selected##" />
        <input type="button" class="button_large" onClick="collector_clearAll('PL', 'PL.removeItem')" value="##Clear Playlist##" />
    </div>
    <div class="pl_container_button">
        <input type="button" class="button_large" value="##Close Playlist##"   onClick="popup('{$UI_BROWSER}?popup[]=PL.confirmRelease', 'PL.confirmRelease', 400, 50)">
        <input type="button" class="button_large" value="##Description##"      onClick="location.href='{$UI_BROWSER}?act=PL.editMetaData'">
        <input type="button" class="button_large" value="##Delete Playlist##"  onClick="popup('{$UI_BROWSER}?popup[]=PL.confirmDelete',  'PL.deleteActive',   400, 50)">
    </div>
</div>

<script type="text/javascript">
        document.forms['PL'].elements['all'].checked = false;
        collector_switchAll('PL');
</script>

{assign var="_duration" value=null}

<!-- end playlist editor -->