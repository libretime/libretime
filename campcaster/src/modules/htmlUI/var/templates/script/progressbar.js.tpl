<script language="javascript">

{literal}
// play-progress-bar object

function plPrBar() { 
    plPrBar_debug('new plPrBar'); 
    // methods:
    this.init       = plPrBar_init;
    this.tick       = plPrBar_tick;
    this.update     = plPrBar_update;
    this.onair      = plPrBar_onair;
    this.offair     = plPrBar_offair;
    this.show       = plPrBar_show;
    this.hide       = plPrBar_hide;
    this.create     = plPrBar_create;
    this.request    = plPrBar_request;
}


function plPrBar_init(current, c_tit, c_pltit, c_eh, c_ei, c_es, c_dh, c_di, c_ds, 
                      next, n_tit, n_pltit, n_dh, n_di, n_ds, 
                      upcoming, u_tit, u_pltit, u_dh, u_di, u_ds, u_sh, u_si, u_ss) {
    plPrBar_debug('init');
    this.interval   = 333;
    if (current == 1) {
        //plPrBar_debug('init::current: ' + current);
        this.current      = true;
        this.c_tit        = c_tit;
        this.c_pltit      = c_pltit;
        this.c_elapsed    = new Date();
        this.c_duration   = new Date();
        this.c_remaining  = new Date();
        
        this.c_elapsed.setTime (Date.UTC(1970, 0, 1, c_eh, c_ei, c_es));
        this.c_duration.setTime(Date.UTC(1970, 0, 1, c_dh, c_di, c_ds));
        this.c_remaining.setTime(this.c_duration.getTime() - this.c_elapsed.getTime());
    }
    if (next == 1) {
        //plPrBar_debug('init::next: ' + next);
        this.next       = true; 
        this.n_tit      = n_tit.slice(0, 22);
        this.n_pltit    = n_pltit.slice(0, 22);
        this.n_duration = new Date;
        this.n_duration.setTime(Date.UTC(1970, 0, 1, n_dh, n_di, n_ds));
    }
    if (upcoming == 1) {
        //plPrBar_debug('init::upcoming: ' + upcoming);
        this.upcoming   = true;
        this.u_tit      = u_tit;
        this.u_pltit    = u_pltit;
        this.u_duration = new Date;
        this.u_duration.setTime(Date.UTC(1970, 0, 1, u_dh, u_di, u_ds));
        this.u_plstart    = new Date();
        this.u_plstart.setTime(Date.UTC(1970, 0, 1, u_sh, u_si, u_ss));
    }
    if (this.current) {
        this.show('now');
        document.getElementById("now_title").innerHTML      = this.c_tit;
        document.getElementById("now_pltitle").innerHTML    = this.c_pltit;
        this.progress = window.setInterval("ppb.tick();", this.interval);
        this.onair();
        this.update();
    } else {
        this.hide('now');
        this.offair();  
    }
    if (this.next) {
        this.show('next');
        document.getElementById("next_title").innerHTML     = this.n_tit;
        document.getElementById("next_duration").innerHTML  = '(' + twoDigit(this.n_duration.getUTCHours()) 
                                                              + ':' + twoDigit(this.n_duration.getUTCMinutes()) 
                                                              + ':' + twoDigit(this.n_duration.getUTCSeconds()) + ')';
    } else {
        this.hide('next');
    }
    if (this.upcoming) {
        this.show('upcoming');
        document.getElementById("upcoming_pltitle").innerHTML     = this.u_pltit;
        document.getElementById("upcoming_plstart").innerHTML     = '(at ' + twoDigit(this.u_plstart.getUTCHours()) 
                                                                  + ':' + twoDigit(this.u_plstart.getUTCMinutes()) 
                                                                  + ':' + twoDigit(this.u_plstart.getUTCSeconds()) + ')';
        document.getElementById("upcoming_title").innerHTML       = this.u_tit;
        document.getElementById("upcoming_duration").innerHTML    = '(' + twoDigit(this.u_duration.getUTCHours()) 
                                                                  + ':' + twoDigit(this.u_duration.getUTCMinutes()) 
                                                                  + ':' + twoDigit(this.u_duration.getUTCSeconds()) + ')';
    } else {
        this.hide('upcoming');
    }
}

function plPrBar_tick() {
    //plPrBar_debug('tick:' + this.c_remaining.getTime());
    if (this.c_remaining.getTime() <= this.interval*2)  {
        window.clearInterval(this.progress);
        this.request()
        return;
    }
    this.c_elapsed.setTime(this.c_elapsed.getTime() + this.interval);
    this.c_remaining.setTime(this.c_duration.getTime() - this.c_elapsed.getTime());
    this.update();
}

function plPrBar_update() {
    //plPrBar_debug('update');
    document.getElementById("now_elapsed").innerHTML   = twoDigit(this.c_elapsed.getUTCHours())   + ":" + twoDigit(this.c_elapsed.getUTCMinutes())   + ":" + twoDigit(this.c_elapsed.getUTCSeconds());
    document.getElementById("now_remaining").innerHTML = twoDigit(this.c_remaining.getUTCHours()) + ":" + twoDigit(this.c_remaining.getUTCMinutes()) + ":" + twoDigit(this.c_remaining.getUTCSeconds());
    document.getElementById("now_scala").style.width   = (100 / this.c_duration.getTime() * this.c_elapsed.getTime()) + "%";
}

function plPrBar_request() {
    plPrBar_debug('request');
    jsCom("jscom_wrapper", ["uiBrowser", "SCHEDULER", "getScheduleInfo_jscom", "1"], this.create);     
}

function plPrBar_create(jscomRes) {
    plPrBar_debug('create: ' + jscomRes);
    window.clearInterval(ppb.progress);
    if (jscomRes !== '') {
        eval('var parms = ' + jscomRes + ';');
        ppb = new plPrBar();
        ppb.init(parms[0], parms[1], parms[2], parms[3], parms[4], parms[5], parms[6], parms[7], parms[8],
                 parms[9], parms[10], parms[11], parms[12], parms[13], parms[14],
                 parms[15], parms[16], parms[17], parms[18], parms[19], parms[20], parms[21], parms[22], parms[23]);
    } else {
        plPrBar_hide('now');
        plPrBar_hide('next');
        plPrBar_hide('upcoming');
    };
    setTimeout("ppb.request()", 10000);
}

function plPrBar_onair() {
    plPrBar_debug('onair');
    document.getElementById("offair").style.display = 'none';
    document.getElementById("onair").style.display = 'block';
}

function plPrBar_offair() {
    plPrBar_debug('offair');
    document.getElementById("onair").style.display = 'none';
    document.getElementById("offair").style.display = 'block';
}

function plPrBar_show(what) {
    plPrBar_debug('show: ' + what);
    if (what == 'now') {
        document.getElementById("now_title_").style.visibility      = 'visible';
        document.getElementById("now_pltitle_").style.visibility    = 'visible';
        document.getElementById("now_elapsed_").style.visibility    = 'visible';
        document.getElementById("now_remaining_").style.visibility  = 'visible';
        document.getElementById("now_scala_").style.visibility      = 'visible';
    }
    if (what == 'next') {
        document.getElementById("next_title_").style.visibility     = 'visible';
    }
    if (what == 'upcoming') {
        document.getElementById("upcoming_pltitle_").style.visibility   = 'visible';
        document.getElementById("upcoming_title_").style.visibility     = 'visible';
    } 
}

function plPrBar_hide(what) {
    plPrBar_debug('hide: ' + what);
    if (what == 'now') {
        document.getElementById("now_title_").style.visibility      = 'hidden';
        document.getElementById("now_title").innerHTML              = '';
        document.getElementById("now_pltitle_").style.visibility    = 'hidden';
        document.getElementById("now_pltitle").innerHTML            = '';
        document.getElementById("now_elapsed_").style.visibility    = 'hidden';
        document.getElementById("now_elapsed").innerHTML            = '';
        document.getElementById("now_remaining_").style.visibility  = 'hidden';
        document.getElementById("now_remaining").innerHTML          = '';
        document.getElementById("now_scala_").style.visibility      = 'hidden';
    }
    if (what == 'next') {
        document.getElementById("next_title_").style.visibility     = 'hidden';
        document.getElementById("next_title").innerHTML             = '';
        document.getElementById("next_duration").innerHTML          = ''; 
    }
    if (what == 'upcoming') {
        document.getElementById("upcoming_pltitle_").style.visibility   = 'hidden';
        document.getElementById("upcoming_pltitle").innerHTML           = '';
        document.getElementById("upcoming_title_").style.visibility     = 'hidden';
        document.getElementById("upcoming_title").innerHTML             = ''; 
    } 
}

function plPrBar_debug(msg) {
    if (ppb_debug) {
        var jetzt = new Date();
        var Min = jetzt.getMinutes();
        var Sek = jetzt.getSeconds();
        var Min = ((Min < 10) ? "0" + Min : Min);
        var Sek = ((Sek < 10) ? "0" + Sek : Sek);
        var Stamp = Min+':'+Sek;
        document.getElementById('debug_console').innerHTML = document.getElementById('debug_console').innerHTML + Stamp + ' ' + msg + '<br>';
    }
}
{/literal}

{$JSCOM->genJsCode()}

ppb_debug = false;
ppb = new plPrBar();
ppb.request();
//interval = setInterval("ppb.request();", 10000);

</script>



