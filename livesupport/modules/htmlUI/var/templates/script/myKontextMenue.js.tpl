{literal}
<script type="text/javascript">
// www.jjam.de - Kontextmenü mit JavaScript - Version 15.12.2002

// Nur für IE 5+ und NN 6+
ie5=(document.getElementById && document.all && document.styleSheets)?1:0;
nn6=(document.getElementById && !document.all)?1:0;

function initMenu(id, type) {
    if (ie5 || nn6) {
        menuWidth  = 0,
        menuHeight = 0;
        //menuStatus = 0;

        sp2="&nbsp;&nbsp;";
        sp5=sp2+sp2+"&nbsp;"; // Leerzeichen als Abstandshalter (flexibler und code-sparender als eine aufwendige Tabellenkonstruktion)
        oF="onfocus='if(this.blur)this.blur()'"; // Um hässlichen Linkrahmen in einigen Browsern zu vermeiden

        if (type == 'playlist')
            document.getElementById('menucontainer').innerHTML =
             "<div id='menu' style='position:absolute;top:-250;left:0;z-index:100'>"+
             "<table cellpadding='5' cellspacing='0' width='"+menuWidth+"' height='"+menuHeight+"' style='border-style:outset;border-width:1;border-color:#3a6c96;background-color:#4682B4'>"+
             "<tr><td><a class='menu' href=\"{/literal}javascript: hpopup('{$UI_HANDLER}?act=activatePL&id="+id+"', 'activatePL'){literal}\""+oF+">&nbsp; Activate this Playlist &nbsp;</a></td></tr>"+
             "<tr><td><a class='menu' href=\"{/literal}{$UI_BROWSER}?act=PL.display&id="+id+"{literal}\""+oF+">&nbsp; Display this Playlist &nbsp;</a></td></tr>"+ 
             "</table></div>";
        else
            document.getElementById('menucontainer').innerHTML =
            "<div id='menu' style='position:absolute;top:-250;left:0;z-index:100'>"+
            "<table cellpadding='5' cellspacing='0' width='"+menuWidth+"' height='"+menuHeight+"' style='border-style:outset;border-width:1;border-color:#3a6c96;background-color:#4682B4'>"+
            "<tr><td><a class='menu' href='#'"+oF+">&nbsp; Add to Playlist &nbsp;</a></td></tr>"+
            "</table></div>";
   }
}


menuStatus = 0;
document.onmousedown = hideMenu;

function contextmenu(id, type){
    initMenu(id, type);
    document.oncontextmenu = showMenu;
    return false;
}


function showMenu(e) {
    if(ie5) {
        if(event.clientX>menuWidth) xPos=event.clientX-menuWidth+document.body.scrollLeft;
        else xPos=event.clientX+document.body.scrollLeft;
        if (event.clientY>menuHeight) yPos=event.clientY-menuHeight+document.body.scrollTop;
        else yPos=event.clientY+document.body.scrollTop;
    }
    else {
        if(e.pageX>menuWidth+window.pageXOffset) xPos=e.pageX-menuWidth;
        else xPos=e.pageX;
        if(e.pageY>menuHeight+window.pageYOffset) yPos=e.pageY-menuHeight;
        else yPos=e.pageY;
    }
    document.getElementById("menu").style.left=xPos;
    document.getElementById("menu").style.top=yPos;
    menuStatus=1;
    document.oncontextmenu = null;
}


function hideMenu(e) {
    if (menuStatus==1) {
        setTimeout("document.getElementById('menu').style.top=-250", 100);
        menuStatus=0;
    }
}
</script>

<style type='text/css'>
    a.menu {text-decoration:none;font-family:Verdana,Arial;font-size:80%}
    a.menu:link,a.menu:visited {text-decoration:none;color:#F0F8FF}
    a.menu:hover,a.menu:active {text-decoration:none;background-color:#F0F8FF;color:#000040}
    hr.menu {border:0px;height:1px;background-color:#B0C4DE;color:#B0C4DE}
</style>

<div id="menucontainer"></div>
{/literal}
