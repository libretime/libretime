<script language="javascript">

{literal}
// play-progress-bar object

function plPrBar(tit, eh, ei, es, dh, di, ds, next, ntit, ndur){
    this.tit        = tit;
    this.next       = next;
    this.interval   = 333;
    this.ntit       = ntit;
    this.ndur       = ndur;

    // inits:
    this.elapsed    = new Date();
    this.duration   = new Date();
    this.remaining  = new Date();
    this.elapsed.setTime (Date.UTC(1970, 0, 1, eh, ei, es));
    this.duration.setTime(Date.UTC(1970, 0, 1, dh, di, ds));
              //alert("elapsed:" + this.elapsed.getUTCSeconds() + "   duration:" + this.duration.getUTCSeconds());
    // methods:
    this.init   = plPrBar_init;
    this.tick   = plPrBar_tick;
    this.update = plPrBar_update;
    this.stop   = plPrBar_stop;
    this.show   = plPrBar_show;
    this.hide   = plPrBar_hide;
    this.create = plPrBar_create;
    this.run    = setInterval("ppb.tick();", this.interval);
}

function plPrBar_init() {
    document.getElementById("now_title").innerHTML              = this.tit;

    if (this.next == 0) {
        document.getElementById("next_clip").innerHTML          = '';
    } else {
        document.getElementById("next_title").innerHTML         = this.ntit;
        document.getElementById("next_duration").innerHTML      = this.ndur;
    }

    this.show();
    this.update();
}

function plPrBar_tick() {
    if (this.remaining.getTime() <= this.interval*2)  {
        this.stop();
        return;
    }

    this.elapsed.setTime(this.elapsed.getTime() + this.interval);
    this.remaining.setTime(this.duration.getTime() - this.elapsed.getTime());

    this.update();
}

function plPrBar_update() {
    document.getElementById("now_elapsed").innerHTML   = twoDigit(this.elapsed.getUTCHours())   + ":" + twoDigit(this.elapsed.getUTCMinutes())   + ":" + twoDigit(this.elapsed.getUTCSeconds());
    document.getElementById("now_remaining").innerHTML = twoDigit(this.remaining.getUTCHours()) + ":" + twoDigit(this.remaining.getUTCMinutes()) + ":" + twoDigit(this.remaining.getUTCSeconds());
    document.getElementById("now_scala").style.width   = (100 / this.duration.getTime() * this.elapsed.getTime()) + "%";
}

function plPrBar_stop() {
    clearInterval(this.run);

    if (this.next == 0) {
        // just if no next item to play
        this.hide();
    } else {
        // all values for next clip needed here:
        jsCom("jscom_wrapper", ["uiBrowser", "SCHEDULER", "getNowNextClip4jscom"], this.create);
    }
}

function plPrBar_create(jscomRes) {
        if (jscomRes !== '') {
            eval('var parms = ' + jscomRes + ';');
            ppb = new plPrBar(parms[0],
                              parms[1], parms[2], parms[3],
                              parms[4], parms[5], parms[6],
                              parms[7], parms[8], parms[9]
                             );
            ppb.init();
        } else {
            plPrBar_hide();
        };
}

function plPrBar_show() {
    document.getElementById("statusbar_indicator").innerHTML      = '<img src="img/el_onair.gif" alt="on air">';
    document.getElementById("statusbar_whatsplaying").className   = 'whatplaying';
}

function plPrBar_hide() {
    document.getElementById("statusbar_indicator").innerHTML    = '<img src="img/el_offair.gif" alt="off air">';
    document.getElementById("statusbar_whatsplaying").innerHTML = "";
}
{/literal}

{if (is_array($_nowplaying.duration))}
    ppb = new plPrBar  ("{$_nowplaying.title|truncate:33}",
                        {$_nowplaying.elapsed.h|string_format:"%d"}, {$_nowplaying.elapsed.m|string_format:"%d"}, {$_nowplaying.elapsed.s|string_format:"%d"},
                        {$_nowplaying.duration.h|string_format:"%d"}, {$_nowplaying.duration.m|string_format:"%d"}, {$_nowplaying.duration.s|string_format:"%d"},
                        {if is_array($_nextplaying)}
                            1, "{$_nextplaying.title|truncate:22}", "{$_nextplaying.duration.h}:{$_nextplaying.duration.m}:{$_nextplaying.duration.s|truncate:2:""}"
                        {else}
                            0, "", ""
                        {/if}
                       );
    ppb.init();
{/if}


{$JSCOM->genJsCode()}

</script>



