{literal}
<script type="text/javascript">
// www.jjam.de - Kontextmenü mit JavaScript - Version 15.12.2002

// Nur für IE 5+ und NN 6+
ie5 = (document.getElementById && document.all && document.styleSheets) ? 1 : 0;
nn6 = (document.getElementById && !document.all) ? 1 : 0;
contextmenuStatus = 0;
document.onmouseup = hidecontextmenu;
document.write('<div id="contextmenucontainer"></div>');
contextmenuWidth  = 0,
contextmenuHeight = 0;

function contextmenu(param) {
    var contextmenuHeader  = "<div class='contextmenu' id='contextmenu' style='position: absolute; top: -250; left: 0; z-index: 100'>" +
                                "<ul>";
    var contextmenuFooter  = "</ul></div>";
    var contextmenuHtml    = '';

    var sp2         = "&nbsp;&nbsp;";
    var sp5         = sp2 + sp2 + "&nbsp;";                     // Leerzeichen als Abstandshalter (flexibler und code-sparender als eine aufwendige Tabellenkonstruktion) ;
    var oF          = "onfocus = 'if (this.blur) this.blur()'"; // Um hässlichen Linkrahmen in einigen Browsern zu vermeiden;
    var entry       = new Array();
    //contextmenuStatus = 0;

    for (var i = 1; i < contextmenu.arguments.length; ++i) {
        switch (contextmenu.arguments[i]) {
            {/literal}
            case "PL.display":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"{$UI_BROWSER}?act=PL.display&id="+param+"\" "+oF+">&nbsp;Display this Playlist&nbsp;</a></li>";
            break;

            case "PL.release":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='#' onClick=\"hpopup('{$UI_HANDLER}?act=PL.release')\" "+oF+">&nbsp;Release Playlist&nbsp;</a></li>";
            break;

            case "PL.addItem":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='#' onClick=\"hpopup('{$UI_HANDLER}?act=PL.addItem&id="+param+"')\" "+oF+">&nbsp;Add to active Playlist&nbsp;</a></li>";
            break;

            case "PL.removeItem":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='#' onClick=\"hpopup('{$UI_HANDLER}?act=PL.removeItem&id="+param+"')\" "+oF+">&nbsp;Remove Item from Playlist&nbsp;</a></li>";
            break;

            case "PL.activate":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: hpopup('{$UI_HANDLER}?act=PL.activate&id="+param+"')\" "+oF+">&nbsp;Activate this Playlist&nbsp;</a></li>";
            break;

            case "PL.create":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='#' onClick=\"hpopup('{$UI_HANDLER}?act=PL.create&id="+param+"')\" "+oF+">&nbsp;New Playlist using Item&nbsp;</a></li>";
            break;

            case "PL.changeFadeIn":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='#' onClick=\"popup('{$UI_BROWSER}?popup[]=PL.changeTransition&type=fadeIn&id="+param+"', 'PL', '350', '100')\" "+oF+">&nbsp;Change Fadein&nbsp;</a></li>";
            break;

            case "PL.changeTransition":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='#' onClick=\"popup('{$UI_BROWSER}?popup[]=PL.changeTransition&type=transition&id="+param+"', 'PL', '350', '100')\" "+oF+">&nbsp;Change Transition&nbsp;</a></li>";
            break;

            case "PL.changeFadeOut":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='#' onClick=\"popup('{$UI_BROWSER}?popup[]=PL.changeTransition&type=fadeOut&id="+param+"', 'PL', '350', '100')\" "+oF+">&nbsp;Change Fadeout&nbsp;</a></li>";
            break;

            case "PL.editMetaData":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='{$UI_BROWSER}?act=PL.editMetaData&id="+param+"'"+oF+">&nbsp;Edit MData&nbsp;</a></li>";
            break;

            case "SP.addItem":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='#' onClick=\"hpopup('{$UI_HANDLER}?act=SP.addItem&id="+param+"')\" "+oF+">&nbsp;Add to ScratchPad&nbsp;</a></li>";
            break;

            case "SP.removeItem":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='#' onClick=\"hpopup('{$UI_HANDLER}?act=SP.removeItem&id="+param+"')\" "+oF+">&nbsp;Remove from Scratchpad&nbsp;</a></li>";
            break;

            case "listen":
                i++;
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='{$CONFIG.accessRawAudioUrl}?sessid={$START.sessid}&id="+contextmenu.arguments[i]+"'"+oF+">&nbsp;Listen&nbsp;</a></li>";
            break;

            case "edit":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='{$UI_BROWSER}?act=editItem&id="+param+"'"+oF+">&nbsp;Edit&nbsp;</a></li>";
            break;

            case "delete":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='#' onClick=\"hpopup('{$UI_HANDLER}?act=delete&id="+param+"')\" "+oF+">&nbsp;!Delete Item!&nbsp;</a></li>";
            break;

            case "fileList":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='{$UI_BROWSER}?act=fileList&id="+param+"'"+oF+">&nbsp;List Folder&nbsp;</a></li>";
            break;

            case "SCHEDULER.addItem":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='#' onClick=\"hpopup('{$UI_HANDLER}?act=SCHEDULER.set&"+param+"'); popup('{$UI_BROWSER}?popup[]=SCHEDULER.addItem', 'Schedule', 600, 400)\"')"+oF+">&nbsp;Insert Playlist here&nbsp;</a></li>";
            break;

            case "SCHEDULER.removeItem":
                contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='#' onClick=\"popup('{$UI_BROWSER}?popup[]=SCHEDULER.removeItem&"+param+"', 'Schedule', 600, 400)\"')"+oF+">&nbsp;Remove Playlist&nbsp;</a></li>";
            break;
            {literal}
        }
    }
    document.getElementById('contextmenucontainer').innerHTML = contextmenuHeader + contextmenuHtml + contextmenuFooter;

    document.oncontextmenu = showcontextmenu;
    return false;
}


function showcontextmenu(e) {
    if (ie5) {
        if (event.clientX > contextmenuWidth)  xPos = event.clientX - contextmenuWidth + document.body.scrollLeft;
        else                            xPos = event.clientX + document.body.scrollLeft;
        if (event.clientY > contextmenuHeight) yPos = event.clientY - contextmenuHeight + document.body.scrollTop;
        else                            yPos = event.clientY + document.body.scrollTop;
    }
    else {
        if (e.pageX > contextmenuWidth + window.pageXOffset)  xPos = e.pageX - contextmenuWidth;
        else                                                  xPos = e.pageX;
        if (e.pageY > contextmenuHeight + window.pageYOffset) yPos = e.pageY - contextmenuHeight;
        else                                                  yPos = e.pageY;
    }

    document.getElementById("contextmenu").style.left = xPos;
    document.getElementById("contextmenu").style.top  = yPos;
    //document.getElementById('contextmenustyle').innerHTML = '<style type="text/css">#contextmenu {top: 50px; left: 300px; }</style>';

    contextmenuStatus = 1;
    document.oncontextmenu = null;
}


function hidecontextmenu(e) {
    if (contextmenuStatus == 1) {
        setTimeout("document.getElementById('contextmenu').style.top =- 250", 100);
        contextmenuStatus = 0;
    }
}

var passed = false;

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

#contextmenu {
    font-size : 80%;
    }

#contextmenu ul {
    float: left;
    width: 200px;
    list-style: none;
    line-height: 20px;
    padding: 0;
    margin: 0px 0 0 0px;
    display: block;
    clear: left;
    background: #eee;
    border-top: 1px solid #ACB3BA;
}

#contextmenu a {
    display: block;
    background: #eee;
    width: 200px;
    color: #666;
    text-decoration: none;
    padding: 0px;
    border-top: 0px solid #ACB3BA;
    border-left: 1px solid #ACB3BA;
    border-bottom: 1px solid #ACB3BA;
    border-right: 1px solid #ACB3BA;
}

#contextmenu li {
    float: left;
    clear: left;
    padding: 0;
}

#contextmenu a:hover {
    color: #000;
    background: #D6E3EF;
}


</style>
{/literal}
