'use strict';

var wavesurfer = [];

window.addEventListener("load",()=>{
  let op=window.inputKnobsOptions||{};
  op.sliderWidth=op.sliderWidth||op.sliderDiameter||128;
  op.sliderHeight=op.sliderHeight||op.sliderDiameter||20;
  op.fgcolor=op.fgcolor||"#f00";
  op.bgcolor=op.bgcolor||"#000";
  op.sliderMode=op.sliderMode||"relative";
  let styles=document.createElement("style");
  styles.innerHTML=
`input[type=range].input-slider{
  -webkit-appearance:none;
  -moz-appearance:none;
  border:none;
  box-sizing:border-box;
  overflow:hidden;
  background-repeat:no-repeat;
  background-size:100% 100%;
  background-position:0px 0%;
  background-color:transparent;
  touch-action:none;
}
input[type=range].input-slider{
  width:${op.sliderWidth}px; height:${op.sliderHeight}px;
}
input[type=range].input-slider::-webkit-slider-thumb{
  -webkit-appearance:none;
  opacity:0;
}
input[type=range].input-slider::-moz-range-thumb{
  -moz-appearance:none;
  height:0;
  border:none;
}
input[type=range].input-slider::-moz-range-track{
  -moz-appearance:none;
  height:0;
  border:none;
}`;
  document.head.appendChild(styles);
  let makeHSliderFrames=(fr,fg,bg,w,h)=>{
    let r=
`<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="${w}" height="${fr*h}" viewBox="0 0 ${w} ${fr*h}" preserveAspectRatio="none">
<defs><g id="B"><rect x="0" y="0" width="${w}" height="${h}" rx="${h/2}" ry="${h/2}" fill="${bg}"/></g>
<g id="K"><circle x="${w/2}" y="0" r="${h/2*0.9}" fill="${fg}"/></g></defs>`;
    for(let i=0;i<fr;++i){
      r+=`<use xlink:href="#B" transform="translate(0,${h*i})"/>`;
      r+=`<use xlink:href="#K" transform="translate(${h/2+(w-h)*i/100},${h/2+h*i})"/>`;
    }
    return r+"</svg>";
  }
  let initKnobs=(el)=>{
    let w,h,d,fg,bg;
    if(el.inputKnobs){
      el.redraw();
      return;
    }
    let ik=el.inputKnobs={};
    el.refresh=()=>{
      d=+el.getAttribute("data-diameter");
      let st=document.defaultView.getComputedStyle(el,null);
      w=parseFloat(el.getAttribute("data-width")||d||st.width);
      h=parseFloat(el.getAttribute("data-height")||d||st.height);
      bg=el.getAttribute("data-bgcolor")||op.bgcolor;
      fg=el.getAttribute("data-fgcolor")||op.fgcolor;
      ik.sensex=ik.sensey=200;
      ik.sensex=w-h;
      ik.sensey=Infinity;
      el.style.backgroundSize="auto 100%";
      el.style.width=w+"px";
      el.style.height=h+"px";
      ik.frameheight=h;
      let src=el.getAttribute("data-src");
      if(src){
        el.style.backgroundImage=`url(${src})`;
        let sp=+el.getAttribute("data-sprites");
        if(sp)
          ik.sprites=sp;
        else
          ik.sprites=0;
        if(ik.sprites>=1)
          el.style.backgroundSize=`100% ${(ik.sprites+1)*100}%`;
        el.style.backgroundColor=bg;
        el.style.borderRadius=Math.min(w,h)*0.25+"px";
      }
      else{
        let svg=makeHSliderFrames(101,fg,bg,w,h);
        ik.sprites=100;
        el.style.backgroundImage="url(data:image/svg+xml;base64,"+btoa(svg)+")";
        el.style.backgroundSize=`100% ${(ik.sprites+1)*100}%`;
      }
      ik.valrange={min:+el.min, max:(el.max=="")?100:+el.max, step:(el.step=="")?1:+el.step};
      el.redraw(true);
    };
    el.setValue=(v)=>{
      v=(Math.round((v-ik.valrange.min)/ik.valrange.step))*ik.valrange.step+ik.valrange.min;
      if(v<ik.valrange.min) v=ik.valrange.min;
      if(v>ik.valrange.max) v=ik.valrange.max;
      el.value=v;
      if(el.value!=ik.oldvalue){
        el.setAttribute("value",el.value);
        el.redraw();
        let event=document.createEvent("HTMLEvents");
        event.initEvent("input",false,true);
        el.dispatchEvent(event);
        ik.oldvalue=el.value;
      }
    };
    ik.pointerdown=(ev)=>{
      el.focus();
      if(ev.touches)
        ev = ev.touches[0];
      let rc=el.getBoundingClientRect();
      let cx=(rc.left+rc.right)*0.5,cy=(rc.top+rc.bottom)*0.5;
      let dx=ev.clientX,dy=ev.clientY;
      if(op.sliderMode=="abs"){
        dv=(ik.valrange.min+ik.valrange.max)*0.5+((dx-cx)/ik.sensex-(dy-cy)/ik.sensey)*(ik.valrange.max-ik.valrange.min);
        el.setValue(dv);
      }
      ik.dragfrom={x:ev.clientX,y:ev.clientY,a:Math.atan2(ev.clientX-cx,cy-ev.clientY),v:+el.value};
      document.addEventListener("mousemove",ik.pointermove);
      document.addEventListener("mouseup",ik.pointerup);
      document.addEventListener("touchmove",ik.pointermove);
      document.addEventListener("touchend",ik.pointerup);
      document.addEventListener("touchcancel",ik.pointerup);
      document.addEventListener("touchstart",ik.preventScroll);
      ev.preventDefault();
      ev.stopPropagation();
    };
    ik.pointermove=(ev)=>{
      let dv;
      let rc=el.getBoundingClientRect();
      let cx=(rc.left+rc.right)*0.5,cy=(rc.top+rc.bottom)*0.5;
      if(ev.touches)
        ev = ev.touches[0];
      let dx=ev.clientX-ik.dragfrom.x,dy=ev.clientY-ik.dragfrom.y;
      dv=(dx/ik.sensex-dy/ik.sensey)*(ik.valrange.max-ik.valrange.min);
      if(ev.shiftKey)
        dv*=0.2;
      el.setValue(ik.dragfrom.v+dv);
    };
    ik.pointerup=()=>{
      document.removeEventListener("mousemove",ik.pointermove);
      document.removeEventListener("touchmove",ik.pointermove);
      document.removeEventListener("mouseup",ik.pointerup);
      document.removeEventListener("touchend",ik.pointerup);
      document.removeEventListener("touchcancel",ik.pointerup);
      document.removeEventListener("touchstart",ik.preventScroll);
      let event=document.createEvent("HTMLEvents");
      event.initEvent("change",false,true);
      el.dispatchEvent(event);
    };
    ik.preventScroll=(ev)=>{
      ev.preventDefault();
    };
    ik.keydown=()=>{
      el.redraw();
    };
    ik.wheel=(ev)=>{
      let delta=ev.deltaY>0?-ik.valrange.step:ik.valrange.step;
      if(!ev.shiftKey)
        delta*=5;
      el.setValue(+el.value+delta);
      ev.preventDefault();
      ev.stopPropagation();
    };
    el.redraw=(f)=>{
      if(f||ik.valueold!=el.value){
        let v=(el.value-ik.valrange.min)/(ik.valrange.max-ik.valrange.min);
        if(ik.sprites>=1)
          el.style.backgroundPosition="0px "+(-((v*ik.sprites)|0)*ik.frameheight)+"px";
        else
          el.style.backgroundPosition=((w-h)*v)+"px 0px";
        ik.valueold=el.value;
      }
    };
    el.refresh();
    el.redraw(true);
    el.addEventListener("keydown",ik.keydown);
    el.addEventListener("mousedown",ik.pointerdown);
    el.addEventListener("touchstart",ik.pointerdown);
    el.addEventListener("wheel",function (event){ event.stopPropagation(); });   //previous: el.addEventListener("wheel",ik.wheel); Maybe optional, can accidently move knob if allowed
    el.addEventListener("click",function (event){ event.stopPropagation(); });
  }
  let refreshque=()=>{
    let elem=document.querySelectorAll("input.input-slider");
    for(let i=0;i<elem.length;++i)
      procque.push([initKnobs,elem[i]]);
  }
  let procque=[];
  refreshque();
  setInterval(()=>{
    for(let i=0;procque.length>0&&i<8;++i){
      let q=procque.shift();
      q[0](q[1]);
    }
    if(procque.length<=0)
      refreshque();
  },50);
});

// Used for converting back to HH-MM-SS, Cuein and Cueout
function toHHMMSS(secs) {
	let totalSeconds = secs;
	let hours = Math.floor(totalSeconds / 3600);
	totalSeconds %= 3600;
	let minutes = Math.floor(totalSeconds / 60);
	let seconds = totalSeconds % 60;

	// If you want strings with leading zeroes:
	minutes = String(minutes).padStart(2, "0");
	hours = String(hours).padStart(2, "0");
	seconds = String(seconds).padStart(2, "0");
	return(hours + ":" + minutes + ":" + seconds.slice(0, 6));
}

//outputs gain percentage from decibels
function deciNum(value){
  const decibels = Math.exp(value / 8.6858);
  return 50 * decibels;
}

//outputs decibel from gain
function gainNum(perc, minDb, maxDb, minP, maxP){
  const getPerc = (db) => (Math.round(1000000000 * Math.pow(10, db / 20)) / 10000000);
  const maxPerc = maxP || (_.isNumber(maxDb) && getPerc(maxDb)) || 100;
  const minPerc = minP || (_.isNumber(minDb) && getPerc(minDb)) || 0;
  const range = maxPerc - minPerc;
  const newPerc = (range / 50) * perc;
  const total = Math.round(1000000000 * 20 * (Math.log(newPerc * 0.01) / Math.log(10))) / 1000000000;
  return total.toFixed(2);
}

//outputs steps from decibels
function deciSteps(value){
  //convert decibel to gain/percent
  const percent = deciNum(value);
  var result = percent / 100;
  return result;
}

function formatTimeCallback(seconds, pxPerSec) {

    seconds = Number(seconds);

    //alert(seconds);
    var minutes = Math.floor(seconds / 60);
    seconds = seconds % 60;

    // fill up seconds with zeroes
    var secondsStr = Math.round(seconds).toString();
    if (pxPerSec >= 25 * 10) {
        secondsStr = seconds.toFixed(2);
    } else if (pxPerSec >= 25 * 1) {
        secondsStr = seconds.toFixed(1);
    }

    if (minutes > 0) {
        if (seconds < 10) {
            secondsStr = '0' + secondsStr;
        }
        return `${minutes}:${secondsStr}`;
    }

    return secondsStr;
}

/**
 * Use timeInterval to set the period between notches, in seconds,
 * adding notches as the number of pixels per second increases.
 *
 * Note that if you override the default function, you'll almost
 * certainly want to override formatTimeCallback, primaryLabelInterval
 * and/or secondaryLabelInterval so they all work together.
 *
 * @param: pxPerSec
 */
function timeInterval(pxPerSec) {
    var retval = 1;
    if (pxPerSec >= 25 * 100) {
        retval = 0.01;
    } else if (pxPerSec >= 25 * 40) {
        retval = 0.025;
    } else if (pxPerSec >= 25 * 10) {
        retval = 0.1;
    } else if (pxPerSec >= 25 * 4) {
        retval = 0.25;
    } else if (pxPerSec >= 25) {
        retval = 1;
    } else if (pxPerSec * 5 >= 25) {
        retval = 5;
    } else if (pxPerSec * 15 >= 25) {
        retval = 15;
    } else {
        retval = Math.ceil(0.5 / pxPerSec) * 60;
    }
    return retval;
}

/**
 * Return the cadence of notches that get labels in the primary color.
 * EG, return 2 if every 2nd notch should be labeled,
 * return 10 if every 10th notch should be labeled, etc.
 *
 * Note that if you override the default function, you'll almost
 * certainly want to override formatTimeCallback, primaryLabelInterval
 * and/or secondaryLabelInterval so they all work together.
 *
 * @param pxPerSec
 */
function primaryLabelInterval(pxPerSec) {
    var retval = 1;
    if (pxPerSec >= 25 * 100) {
        retval = 10;
    } else if (pxPerSec >= 25 * 40) {
        retval = 4;
    } else if (pxPerSec >= 25 * 10) {
        retval = 10;
    } else if (pxPerSec >= 25 * 4) {
        retval = 4;
    } else if (pxPerSec >= 25) {
        retval = 1;
    } else if (pxPerSec * 5 >= 25) {
        retval = 5;
    } else if (pxPerSec * 15 >= 25) {
        retval = 15;
    } else {
        retval = Math.ceil(0.5 / pxPerSec) * 60;
    }
    return retval;
}

/**
 * Return the cadence of notches to get labels in the secondary color.
 * EG, return 2 if every 2nd notch should be labeled,
 * return 10 if every 10th notch should be labeled, etc.
 *
 * Secondary labels are drawn after primary labels, so if
 * you want to have labels every 10 seconds and another color labels
 * every 60 seconds, the 60 second labels should be the secondaries.
 *
 * Note that if you override the default function, you'll almost
 * certainly want to override formatTimeCallback, primaryLabelInterval
 * and/or secondaryLabelInterval so they all work together.
 *
 * @param pxPerSec
 */
function secondaryLabelInterval(pxPerSec) {
    // draw one every 10s as an example
    return Math.floor(10 / timeInterval(pxPerSec));
}


function renderWaveform(track_id, selector_id, url, cuein, cueout) {

    var trackid = "t"+track_id;
    var a = cuein.split(':'); // split it at the colons
    var b = cueout.split(':'); // split it at the colons
    var startseconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
    var endseconds = (+b[0]) * 60 * 60 + (+b[1]) * 60 + (+b[2]);

    $('#track-playedit-'+track_id).attr('onClick', 'wavesurfer["'+ trackid +'"].play('+ startseconds +', '+endseconds+');');

    wavesurfer[trackid] = WaveSurfer.create({
        container: document.querySelector(selector_id),
        waveColor: 'hsla(0, 0%, 50%, 0.8)',
        backgroundColor: '#333',
        progressColor: 'hsla(0, 0%, 0%, 0)',
        backend: 'MediaElementWebAudio',
        height: 70,
        cursorColor: '#ccc',
        cursorWidth: 1,
        responsive: true,
        scrollParent: true,
        minimap: true,
        autoCenter: true,
        splitChannels: true,
        plugins: [
            WaveSurfer.regions.create({
                regions: [
                    {
                        id: track_id,
                        start: startseconds,
                        end: endseconds,
                        loop: false,
                        drag: false,
                        color: 'hsla(232, 64%, 50%, 0.2)',
                        handleStyle: {
                            left: {
                                backgroundColor: '#00e640',
                                width: '2px',
                            },
                            right: {
                                backgroundColor: '#f22613',
                                width: '2px',
                            },
                        },
                    }
                ]
            }),
            WaveSurfer.timeline.create({
                container: "#timeline-"+ track_id,
                formatTimeCallback: formatTimeCallback,
                timeInterval: timeInterval,
                primaryLabelInterval: primaryLabelInterval,
                secondaryLabelInterval: secondaryLabelInterval,
                primaryColor: '#ff611f',
                secondaryColor: '#cccccc',
                primaryFontColor: '#ff611f',
                secondaryFontColor: '#cccccc'
            })
        ]
    });

    var eTrack = wavesurfer[trackid];
    eTrack.load(url);

    // Zoom slider
    var slider = document.querySelector('[data-action="zoom-'+track_id+'"]');
    slider.value = eTrack.params.minPxPerSec;
    slider.min = eTrack.params.minPxPerSec;
    slider.addEventListener('input', function() {
        eTrack.zoom(Number(this.value));
    });

    document.getElementById('tracktimerinput-'+track_id).value='0.000';

    eTrack.on('audioprocess', function (e) {
        document.getElementById('tracktimerinput-'+track_id).value=(eTrack.getCurrentTime().toFixed(3));
    });

    var playPause = document.getElementById('track-play-'+track_id);
    eTrack.on('play', function () {
        //var playButton = document.getElementById('track-play-'+track_id);
        playPause.style.backgroundColor = "#32CD32";
    });
    eTrack.on('pause', function () {
        //var pauseButton = document.getElementById('track-play-'+track_id);
        playPause.style.backgroundColor = "#555555";
    });
    eTrack.on('error', function(e) {
        console.warn(e);
    });

    function saveCue() {
         var region = eTrack.regions.list[track_id];
           document.getElementsByClassName("cuein_"+track_id)[0].value = toHHMMSS(region.start);
           document.getElementsByClassName("cueout_"+track_id)[0].value = toHHMMSS(region.end);
   				 $('#track-playedit-'+track_id).attr('onClick', 'wavesurfer["t'+ track_id +'"].play('+ region.start +', '+ region.end +');');

           return {
                   start: region.start,
                   end: region.end
           };
    }
    eTrack.on('ready', () => {
             eTrack.on('region-update-end', saveCue);
    });

    eTrack.on('region-in', function(e) {
        document.getElementById('tracktimerinput-'+track_id).style.color = "rgb(56, 232, 56)";
    });
    eTrack.on('region-out', function(e) {
        document.getElementById('tracktimerinput-'+track_id).style.color = "#ffffff";
    });

    //Volume to Gain deciSteps(gainNum(gain_level))
    eTrack.setVolume(0.6);

    return eTrack;
}
