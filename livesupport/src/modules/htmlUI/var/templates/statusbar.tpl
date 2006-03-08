{assign var='_nowplaying'  value=$SCHEDULER->getNowNextClip()}
{assign var='_nextplaying' value=$SCHEDULER->getNowNextClip(1)}

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
                        <div class="left">Elapsed:    <strong id="now_elapsed"></strong></div>
                        <div class="right">Remaining: <strong id="now_remaining"></strong></div>
                    </div>
                {/if}
                <div  style="height:5px"> </div>
                <div id="next_clip">
                {if $_nextplaying}
                    <p class="next">##Playing Next##: <span id="next_title"></span>&nbsp; <span id="next_duration"></span></p>
                {/if}
                </div>
            </div>  
    		</div>
		</td>
		
		<td>
        {if $_nowplaying}
            <div id="nowplaying_indicator"><div id="onair">##on air##</div></div>            
        {else}
            <div id="nowplaying_indicator"><div id="offair">##off air##</div></div>
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
