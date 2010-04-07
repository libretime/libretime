<div id="masterpalette"> 
<table border="0" class="masterpalette">
	<tr>
		<td>
            <div id="logo">
              <img src="img/logo.png">
            </div>
		</td>
		
		<td>
    		<div id="time">
    		    ##Station Time##
    		    {assign var="_now value=$SCHEDULER->getSchedulerTime(true)}
    		    {if !$_now}
    		      {assign var="_now" value=$smarty.now}
    		    {/if}
                <h1><span id="servertime" class="clock">{$_now|date_format:"%H:%M:%S"}</span></h1>
                {$_now|date_format:"%Z"}
    		</div>
		</td>
		
		<td>	
    		<div id="nowplaying">
            <div class="whatplaying">
                <div class="title" id="now_title_">##Now Playing##: <span id="now_title"></span></div>
                <div class="scala" id="now_scala_">
                    <div class="scala_in" id="now_scala" style="width: 0%;">&nbsp;</div>
                </div>
                
                <div class="time">
                    <span class="left">
                      <span class="left_title" id="now_elapsed_">##Elapsed:##</span>
                      <strong id="now_elapsed"></strong>
                    </span>
                    
                    <span class="right">
                      <span id="now_remaining_">##Remaining:##</span>
                      <strong id="now_remaining"></strong>
                    </span>
                </div>
                
                <div class="playlist">
                    <span class="left">
                      <span class="left_title" id="now_pltitle_">##Playlist:##</span>
                    <span>
                    <strong class="playlist_title" id="now_pltitle"></strong> 
                </div>
                
                <div style="height:3px"> </div>
                
                <div id="next_clip">
                    <span class="next" id="next_title_">##Next File##:</span>
                    <strong id="next_title"></strong>
                    &nbsp;<span id="next_duration"></span>
                </div>
                
                <div id="upcoming_playlist">
                    <span class="next" id="upcoming_pltitle_">##Upcoming Playlist##:</span>
                    <strong id="upcoming_pltitle"></strong>
                    &nbsp;<span id="upcoming_plstart"></span>
                </div>
                
                <div id="upcoming_clip">
                    <span class="next" id="upcoming_title_">##Upcoming Title##:</span>
                    <strong id="upcoming_title"></strong>
                    &nbsp;<span id="upcoming_duration"></span>
                </div>
            </div>  
    		</div>
		</td>
		
		<td>
            <div id="nowplaying_indicator">
                <div id="onair">##Playing Scheduled Item##</div>
                <div id="offair">##Off Air##</div>
            </div>
		</td>
		
		<td>
    		<div id="debug_console" style="width: 180px; height: 140px; overflow: auto">
    	
    		</div>
		</td>
    </tr>
</table>
</div>


{include file="script/clock.js.tpl"}
{include file="script/progressbar.js.tpl"}