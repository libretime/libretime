{literal}
<script type="text/javascript">
// www.jjam.de - Kontextmenü mit JavaScript - Version 15.12.2002

// Nur für IE 5+ und NN 6+
ie5=(document.getElementById && document.all && document.styleSheets)?1:0;
nn6=(document.getElementById && !document.all)?1:0;
menuStatus = 0;
document.onmouseup = hideMenu;
document.write('<div id="menucontainer"></div>');
menuWidth  = 180,
menuHeight = 0;

function menu(id) {
    var menuHeader  = "<div id='menu' style='position:absolute;top:-250;left:0;z-index:100'>"+
                      "<table cellpadding='5' cellspacing='0' width='"+menuWidth+"' height='"+menuHeight+"' style='border-style:outset;border-width:1;border-color:#3a6c96;background-color:#4682B4'>";
    var menuFooter  = "</table></div>";
    var menuHtml    = '';
    var sp2         = "&nbsp;&nbsp;";
    var sp5         = sp2+sp2+"&nbsp;"; // Leerzeichen als Abstandshalter (flexibler und code-sparender als eine aufwendige Tabellenkonstruktion) ;
    var oF          = "onfocus='if(this.blur)this.blur()'"; // Um hässlichen Linkrahmen in einigen Browsern zu vermeiden;
    var entry       = new Array();
    //menuStatus = 0;

    for (var i = 1; i < menu.arguments.length; ++i) {
        switch (menu.arguments[i]) {
            case "PL.display":
                menuHtml = menuHtml + "<tr><td><a class='menu' href=\"{/literal}{$UI_BROWSER}{literal}?act=PL.display&id="+id+"\" "+oF+">&nbsp;Display this Playlist&nbsp;</a></td></tr>";
            break;

            case "PL.release":
                menuHtml = menuHtml + "<tr><td><a class='menu' href='#' onClick=\"hpopup('{/literal}{$UI_HANDLER}{literal}?act=PL.release')\" "+oF+">&nbsp;Release Playlist&nbsp;</a></td></tr>";
            break;

            case "PL.addItem":
                menuHtml = menuHtml + "<tr><td><a class='menu' href='#' onClick=\"hpopup('{/literal}{$UI_HANDLER}{literal}?act=PL.addItem&id="+id+"')\" "+oF+">&nbsp;Add to active Playlist&nbsp;</a></td></tr>";
            break;

            case "PL.activate":
                menuHtml = menuHtml + "<tr><td><a class='menu' href=\"javascript: hpopup('{/literal}{$UI_HANDLER}{literal}?act=PL.activate&id="+id+"')\" "+oF+">&nbsp;Activate this Playlist&nbsp;</a></td></tr>";
            break;

            case "PL.newUsingItem":
                menuHtml = menuHtml + "<tr><td><a class='menu' href='#' onClick=\"hpopup('{/literal}{$UI_HANDLER}{literal}?act=PL.newUsingItem&id="+id+"')\" "+oF+">&nbsp;New Playlist using Item&nbsp;</a></td></tr>";
            break;
            case "SP.addItem":
                menuHtml = menuHtml + "<tr><td><a class='menu' href='#' onClick=\"hpopup('{/literal}{$UI_HANDLER}{literal}?act=SP.addItem&id="+id+"')\" "+oF+">&nbsp;Add to ScratchPad&nbsp;</a></td></tr>";
            break;
            case "SP.removeItem":
                menuHtml = menuHtml + "<tr><td><a class='menu' href='#' onClick=\"hpopup('{/literal}{$UI_HANDLER}{literal}?act=SP.removeItem&id="+id+"')\" "+oF+">&nbsp;Remove from Scratchpad&nbsp;</a></td></tr>";
            break;
            case "delete":
                menuHtml = menuHtml + "<tr><td><a class='menu' href='{/literal}{$UI_HANDLER}{literal}?act=delete&id="+id+"'"+oF+">&nbsp;!Delete Item!&nbsp;</a></td></tr>";
            break;
        }
    }
    document.getElementById('menucontainer').innerHTML = menuHeader + menuHtml + menuFooter;

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

var passed=false;
function highlight()
{
    //if (!passed) alert('try rightclick in lists...');
    passed = true;
}

function darklight()
{

}
</script>

<style type="text/css">
    a.menu {text-decoration:none;font-family:Verdana,Arial;font-size:80%}
    a.menu:link,a.menu:visited {text-decoration:none;color:#F0F8FF}
    a.menu:hover,a.menu:active {text-decoration:none;background-color:#F0F8FF;color:#000040}
    hr.menu {border:0px;height:1px;background-color:#B0C4DE;color:#B0C4DE}
</style>
{/literal}
