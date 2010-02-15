{assign var='_nowplaying'  value=$SCHEDULER->getNowNextClip()}
{assign var='_nextplaying' value=$SCHEDULER->getNowNextClip()}

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
                <h1><span id="servertime" class="clock">{$smarty.now|date_format:"%H:%M:%S"}</span></h1>
                {$smarty.now|date_format:"%Z"}
    		</div>
		</td>
		
		<td>	
    		<div id="nowplaying">
            <div class="whatplaying">
                {if $_nowplaying}
                    <div class="title">##Now Playing##: <span id="now_title"></span></div>
                    <div class="scala">
                        <div class="scala_in" id="now_scala" style="width: {$_nowplaying.percentage}%;">&nbsp;</div>
                    </div>
                    
                    <div class="time">
                        <span class="left">
                          <span class="left_title">##Elapsed:##</span>
                          <strong id="now_elapsed"></strong>
                        </span>
                        
                        <span class="right">
                          ##Remaining:##
                          <strong id="now_remaining"></strong>
                        </span>
                    </div>
                    
                    <div class="playlist">
                        <span class="left">
                          <span class="left_title">##Playlist:##</span>
                        <span>

                        <strong class="playlist_title" id="now_pl_title"></strong> 
                    </div>
                {/if}
                <div  style="height:3px"> </div>
                <div id="next_clip">
                {if $_nextplaying}
                    <span class="next">##Next Clip##:</span> <strong id="next_title"></strong>&nbsp; <strong id="next_duration"></strong></div>
                {/if}
                </div>
            </div>  
    		</div>
		</td>
		
		<td>
        {if $_nowplaying}
            <div id="nowplaying_indicator"><div id="onair">##Playing Scheduled Item##</div></div>            
        {else}
            <div id="nowplaying_indicator"><div id="offair">##Off Air##</div></div>
        {/if}
    		
		</td>
		
		<td>
    		<div id="station">
    		  <img src="{$STATIONPREFS.stationLogoPath}" alt="{$STATIONPREFS.stationName}">
    		</div>
		</td>
    </tr>
</table>
</div>


{include file="script/clock.js.tpl"}
{include file="script/progressbar.js.tpl"}


{assign var='_nowplaying'  value=null}
{assign var='_nextplaying' value=null}
