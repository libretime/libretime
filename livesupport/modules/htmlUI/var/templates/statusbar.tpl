{include file="script/clock.js.tpl"}
    <!-- start header -->
        <!-- start header left -->
        <div class="headLeft">
            <img src="img/logo_livesupport.gif" alt="Livesupport Logo" />
            <div class="container">
                {include file="userinfo.tpl"}
            </div>
        </div>
        <!-- end header left -->
        <!-- start header right -->
        <div class="headRight">
            <!-- start station information -->
            <div class="station">
                <img src="{$STATIONPREFS.stationLogoPath}" alt="{$STATIONPREFS.stationName}" width='127' height='34'>
                <div class="frequence">{$STATIONPREFS.stationFrequency}</div>
            </div>
            <!-- end station information -->
            <!-- start onair information -->
            <div class="onair">
                <img src="img/el_onair.gif" alt="on air" />
                <!--img src="img/el_offair.gif" alt="off air" /-->
            </div>
            <!-- end onair information -->
            <!-- start station time -->
            <div class="stationtime">
                <h1>##Station Time##</h1>
                <div class="time"><span id=servertime class="clock" style="position:relative;">{$smarty.now|date_format:"%H:%M:%S"}</span>
                    <div class="timezone">cet</div>
                </div>
                {*
                <h1>##Local Time##</h1>
                <div class="time">
                    <span id=localtime class="clock" style="position:relative;"></span>
                    <div class="timezone">cet</div>
                </div>
                *}
            </div>
            <!-- end station time -->
            <!-- start what playing -->
            <div class="whatplaying">
                {$SCHEDULER->getNowNextClip()}
                <div class="nowplaying">Now Playing: Don Quixote</div>
                <div class="scala">
                    <div class="scala_in" style="width: 100px;">&nbsp;</div>
                </div>
                <div class="time">
                    <div class="left">Elapsed:<strong>00:05:24</strong></div>
                    <div class="right">Remaining:<strong>00:02:05</strong></div>
                </div>
                <div class="clearer"></div>
                <p>Playing Next: Diego Diego 00:03:25</p>
            </div>
            <!-- end what playing -->
        </div>
        <!-- end header right -->
    <!-- end header -->
{*
<!-- old template -->
<div id="statusbar">

    <div class="statusbaritem">
        server time
        <br>
        <span id=servertime style="position:relative;"></span>
    </div>

    <div class="statusbaritem">
        local  time
        <br>
        <span id=localtime style="position:relative;"></span>
    </div>

    <div class="statusbaritem">
        <img src="{$STATIONPREFS.stationLogoPath}" width="30" height="50">
    </div>

    <div class="statusbaritem">
        {$STATIONPREFS.stationName}
        <br>
        {$STATIONPREFS.frequency}
    </div>

    <div class="statusbaritem">
        {$SCHEDULER->getNowNextClip()}
        <br>
        {$STATIONPREFS.frequency}
    </div>

    <div class="statusbaritem">
        {include file="userinfo.tpl"}
    </div>

</div>
*}
