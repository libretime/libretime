{literal}

<SCRIPT LANGUAGE="JavaScript">
<!-- Original:  Tomleung (lok_2000_tom@hotmail.com) This tag should not be removed-->
<!--Server time ticking clock v2.0 Updated by js-x.com-->
<!-- server time ticking clock modified for livesupport.campware.org using above script-->
function twoDigit(_v)
{
  _v = Math.round(_v);
  if(_v<10) _v="0"+_v;
  return _v;
}
function MakeArrayday(size)
{
  this.length = size;
  for(var i = 1; i <= size; i++)
    this[i] = "";
  return this;
}
function MakeArraymonth(size)
{
  this.length = size;
  for(var i = 1; i <= size; i++)
    this[i] = "";
  return this;
}

var hours;
var minutes;
var seconds;
var timer=null;
function sClock()
{
  sinterval = 100;      // milliseconds
  {/literal}
  hours   = {$smarty.now|date_format:"%H"};
  minutes = {$smarty.now|date_format:"%M"};
  seconds = {$smarty.now|date_format:"%S"};
  {literal}
  if(timer){clearInterval(timer);timer=null;}
  timer=setInterval("work();", sinterval);
}

function work()
{
  if (!document.layers && !document.all && !document.getElementById) return;
  var runTime = new Date();
  var dn = "AM";
  var shours = hours;
  var sminutes = minutes;
  var sseconds = seconds;
  if (shours >= 12)
  {
    dn = "PM";
    shours-=12;
  }
  if (!shours) shours = 12;
  sminutes=twoDigit(sminutes);
  sseconds=twoDigit(sseconds);
  shours  =twoDigit(shours  );
  movingtime = ""+ shours + ":" + sminutes +":"+sseconds+"" + dn;
  if (document.getElementById)
    document.getElementById("servertime").innerHTML=movingtime;
  else if (document.layers)
  {
    document.layers.clock.document.open();
    document.layers.clock.document.write(movingtime);
    document.layers.clock.document.close();
  }
  else if (document.all)
    clock.innerHTML = movingtime;

  if((seconds=seconds + sinterval/1000)>59)
  {
    seconds=0;
    if(++minutes>59)
    {
      minutes=0;
      if(++hours>23)
      {
        hours=0;
      }
    }
  }
}
</script>

<SCRIPT LANGUAGE="JavaScript">
<!-- Original:  Tomleung (lok_2000_tom@hotmail.com) This tag should not be removed-->
<!-- Local time ticking clock modified for livesupport.campware.org using above script-->
function lMakeArrayday(size)
{
  this.length = size;
  for(var i = 1; i <= size; i++)
    this[i] = "";
  return this;
}
function lMakeArraymonth(size)
{
  this.length = size;
  for(var i = 1; i <= size; i++)
    this[i] = "";
  return this;
}

var lhours;
var lminutes;
var lseconds;
var ltimer=null;
function lClock()
{
  linterval = 100;      // milliseconds
  var tDate = new Date();
  lhours   = tDate.getHours();
  lminutes = tDate.getMinutes();
  lseconds = tDate.getSeconds();
  if(ltimer){clearInterval(ltimer);ltimer=null;}
  ltimer=setInterval("lwork();", linterval);
}

function lwork()
{
  if (!document.layers && !document.all && !document.getElementById) return;
  var runTime = new Date();
  var dn = "AM";
  var shours = lhours;
  var sminutes = lminutes;
  var sseconds = lseconds;
  if (shours >= 12)
  {
    dn = "PM";
    shours-=12;
  }
  if (!shours) shours = 12;
  sminutes=twoDigit(sminutes);
  sseconds=twoDigit(sseconds);
  shours  =twoDigit(shours  );
  movingtime = ""+ shours + ":" + sminutes +":"+sseconds+"" + dn;
  if (document.getElementById)
    document.getElementById("localtime").innerHTML=movingtime;
  else if (document.layers)
  {
    document.layers.clock.document.open();
    document.layers.clock.document.write(movingtime);
    document.layers.clock.document.close();
  }
  else if (document.all)
    clock.innerHTML = movingtime;

  if((lseconds=lseconds + linterval/1000)>59)
  {
    lseconds=0;
    if(++lminutes>59)
    {
      lminutes=0;
      if(++lhours>23)
      {
        lhours=0;
      }
    }
  }
}

sClock();
lClock();
</script>
{/literal}
