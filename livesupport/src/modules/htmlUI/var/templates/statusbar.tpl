{assign var='_nowplaying'  value=$SCHEDULER->getNowNextClip()}
{assign var='_nextplaying' value=$SCHEDULER->getNowNextClip(1)}

    <!-- start header -->
        <!-- start header left -->
        <div class="headLeft">
            <img src="img/logo_livesupport.png" alt="Livesupport Logo" />
            <div class="container">
                {include file="userinfo.tpl"}
            </div>
        </div>
        <!-- end header left -->
        <!-- start header right -->
        <div class="headRight">
            <!-- start station information -->
            <div class="station">
                <img src="{$STATIONPREFS.stationLogoPath}" alt="{$STATIONPREFS.stationName}">
                <div class="frequence">{$STATIONPREFS.stationFrequency}</div>
            </div>
            <!-- end station information -->
            <!-- start onair information -->
            <div class="onair" id="statusbar_indicator">
                {if $_nowplaying}
                    <img src="img/el_onair.png" alt="on air" />
                {else}
                    <img src="img/el_offair.png" alt="off air">
                {/if}
            </div>
            <!-- end onair information -->
            <!-- start station time -->
            <div class="stationtime">
                <h1>##Station Time##</h1>
                <div class="time"><span id="statusbar_servertime" class="clock" style="position:relative;">{$smarty.now|date_format:"%H:%M:%S"}</span>
                    <div class="timezone">cet</div>
                </div>
            </div>
            <!-- end station time -->
            <!-- start what playing -->
            <div class="whatplaying" id="statusbar_whatsplaying">
                {if $_nowplaying}
                    <div class="nowplaying">##Now Playing##: <strong id="now_title"></strong></div>
                    <div class="scala">
                        <div class="scala_in" id="now_scala" style="width: {$_nowplaying.percentage}%;">&nbsp;</div>
                    </div>
                    <div class="time">
                        <div class="left">Elapsed:    <strong id="now_elapsed"></strong></div>
                        <div class="right">Remaining: <strong id="now_remaining"></strong></div>
                    </div>
                {/if}
                <div class="clearer"></div>
                <div id="next_clip">
                {if $_nextplaying}
                    <p>##Playing Next##: <span id="next_title"></span> &nbsp;<span id="next_duration"></span></p>
                {/if}
                </div>
            </div>
            <!-- end what playing -->
        </div>
        <!-- end header right -->
    <!-- end header -->

{include file="script/clock.js.tpl"}
{include file="script/progressbar.js.tpl"}

{assign var='_nowplaying'  value=null}
{assign var='_nextplaying' value=null}
