<!-- start new and improved! playlist editor --> 
<div class="pl_container" >
    <div class="pl_header">
        <div class="pl_header_left"><span>##Playlist Editor## </span></div>
        <div class="pl_header_right"><span>{$PL->title}</span><span class="pl_duration">{$PL->duration} </span></div>
    </div>
    <div class="pl_contents">
    <form name="PL">
    <div class="pl_head">
        <span class="pl_input"><input type="checkbox" name="all" onClick="collector_switchAll('PL')"></span>
        <span class="pl_title">##Title##</span>
        <span class="pl_artist">##Creator##</span>
        <span class="pl_length">##Length##</span>
        <span class="pl_cue_in">##Cue In##</span>
        <span class="pl_cue_out">##Cue Out##</span>
        <span class="pl_playlength">##Playlength##</span>      
    </div>
    <div class="pl_main">
    	<ul id="pl_sortable">
        	{foreach from=$PL->getActiveArr($PL->activeId) key='pos' item='i'}
        	<li class="pl_row" id="pl_{$pos}">
        		<div class="pl_fade_in"><span>Fade in: </span><span class="pl_time">{$i.fadein}</span></div>
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
                    {$i.length}
                </span>
                <span class="pl_cue_in pl_time">
                    {$i.cuein}
                </span>
                <span class="pl_cue_out pl_time">
                    {$i.cueout}
                </span>
                <span class="pl_playlength">
                    {$i.cliplength}
                </span>
                <div class="pl_fade_out"><span>Fade out: </span><span class="pl_time">{$i.fadeout}</span></div>
            </li>
        	{/foreach}
    	</ul>
    	{if is_null($pos)}  
        	<div class="pl_empty">##Empty playlist##</div>   
    	{/if}
    </div>
    
    </form>
    </div>
    <div class="pl_footer">
        <input type="button" class="button_large" onClick="collector_submit('PL', 'PL.removeItem')"   value="##Remove Selected##" />
        <input type="button" class="button_large" onClick="collector_clearAll('PL', 'PL.removeItem')" value="##Clear Playlist##" />
    </div>
    <div class="pl_container_button">
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