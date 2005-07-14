{literal}
<script type="text/javascript">
    // www.jjam.de - Kontextmenü mit JavaScript - Version 15.12.2002

    // Nur für IE 5+ und NN 6+
    ie5 = (document.getElementById && document.all && document.styleSheets) ? 1 : 0;
    nn6 = (document.getElementById && !document.all) ? 1 : 0;

    document.write('<div id="contextmenucontainer"></div>');
    contextmenuStatus = 0;
    contextmenuWidth  = 200,
    contextmenuHeight = 0;
    document.onclick  = hidecontextmenu;


    function contextmenu(param) {
        var contextmenuHeader  = "<div class='contextmenu' id='contextmenu' style='position: absolute; top: -1000; left: 0; z-index: 100'>" +
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
                case "PL.release":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: popup('{$UI_BROWSER}?popup[]=PL.confirmRelease', 'PL.confirmRelease', 400, 50)\" "+oF+">&nbsp;##Close Playlist##&nbsp;</a></li>";
                break;

                case "PL.addItem":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: hpopup('{$UI_HANDLER}?act=PL.addItem&id="+param+"')\" "+oF+">&nbsp;##Add to active Playlist##&nbsp;</a></li>";
                break;

                case "PL.removeItem":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: hpopup('{$UI_HANDLER}?act=PL.removeItem&id="+param+"')\" "+oF+">&nbsp;##Remove File from Playlist##&nbsp;</a></li>";
                break;

                case "PL.activate":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: hpopup('{$UI_HANDLER}?act=PL.activate&id="+param+"')\" "+oF+">&nbsp;##Edit Playlist##&nbsp;</a></li>";
                break;

                case "PL.create":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: hpopup('{$UI_HANDLER}?act=PL.create&id="+param+"')\" "+oF+">&nbsp;##New Playlist including &quot;##"+contextmenu.arguments[++i]+"&quot;&nbsp;</a></li>";
                break;

                case "PL.changeFadeIn":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: popup('{$UI_BROWSER}?popup[]=PL.changeTransition&type=fadeIn&id="+param+"', 'PL', '400', '150')\" "+oF+">&nbsp;##Change Fadein##&nbsp;</a></li>";
                break;

                case "PL.changeTransition":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: popup('{$UI_BROWSER}?popup[]=PL.changeTransition&type=transition&id="+param+"', 'PL', '400', '150')\" "+oF+">&nbsp;##Change Transition##&nbsp;</a></li>";
                break;

                case "PL.changeFadeOut":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: popup('{$UI_BROWSER}?popup[]=PL.changeTransition&type=fadeOut&id="+param+"', 'PL', '400', '150')\" "+oF+">&nbsp;##Change Fadeout##&nbsp;</a></li>";
                break;

                case "SP.addItem":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: hpopup('{$UI_HANDLER}?act=SP.addItem&id="+param+"')\" "+oF+">&nbsp;##Add to ScratchPad##&nbsp;</a></li>";
                break;

                case "SP.removeItem":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: hpopup('{$UI_HANDLER}?act=SP.removeItem&id="+param+"')\" "+oF+">&nbsp;##Remove from Scratchpad##&nbsp;</a></li>";
                break;

                case "listen":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='{$CONFIG.accessRawAudioUrl}?sessid={$START.sessid}&id="+contextmenu.arguments[++i]+"' "+oF+">&nbsp;##Listen ## "+contextmenu.arguments[++i]+"&nbsp;</a></li>";
                break;

                case "edit":
                    i++;
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='{$UI_BROWSER}?act=editItem&id="+param+"' "+oF+">&nbsp;##Edit## "+contextmenu.arguments[i]+"&nbsp;</a></li>";
                break;

                case "delete":
                    i++;
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: popup('{$UI_BROWSER}?popup[]=deleteItem&id="+param+"', 'deleteItem', 400, 50)\" "+oF+">&nbsp;##Delete## "+contextmenu.arguments[i]+"&nbsp;</a></li>";
                break;

                case "fileList":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='{$UI_BROWSER}?act=fileList&id="+param+"' "+oF+">&nbsp;##List Folder##&nbsp;</a></li>";
                break;

                case "SCHEDULER.addItem":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: hpopup('{$UI_HANDLER}?act=SCHEDULER.setScheduleAtTime&"+param+"'); popup('{$UI_BROWSER}?popup[]=SCHEDULER.addItem', 'Schedule', 420, 200)\" "+oF+">&nbsp;##Insert Playlist here##&nbsp;</a></li>";
                break;

                case "SCHEDULER.removeItem":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: popup('{$UI_BROWSER}?popup[]=SCHEDULER.removeItem&"+param+"', 'Schedule', 400, 50)\" "+oF+">&nbsp;##Remove Playlist##&nbsp;</a></li>";
                break;

                case "SCHEDULER.addPL":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: hpopup('{$UI_HANDLER}?act=SCHEDULER.set&view=day&today=1');"+
                                                                                                      "hpopup('{$UI_HANDLER}?act=SCHEDULER.setScheduleAtTime&today=1&hour=0&minute=0');"+
                                                                                                      "location.href='ui_browser.php?act=SCHEDULER';"+
                                                                                                      "popup('{$UI_BROWSER}?popup[]=SCHEDULER.addItem&playlistId="+param+"', 'Schedule', 420, 200)\" "+oF+
                                                                                                      ">&nbsp;##Schedule Playlist##&nbsp;</a></li>";
                break;

                case "SUBJECTS.chgPasswd":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='{$UI_BROWSER}?act=SUBJECTS.chgPasswd&"+param+"' "+oF+">&nbsp;##Change password##&nbsp;</a></li>";
                break;

                case "SUBJECTS.manageGroupMember":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='{$UI_BROWSER}?act=SUBJECTS.manageGroupMember&"+param+"' "+oF+">&nbsp;##Manage group members##&nbsp;</a></li>";
                break;

                case "SUBJECTS.addSubj2Gr":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='#'  onClick=\"hpopup('{$UI_HANDLER}?act=SUBJECTS.addSubj2Gr&"+param+"')\" "+oF+">&nbsp;##Add to group##&nbsp;</a></li>";
                break;

                case "SUBJECTS.removeSubjFromGr":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href='#'  onClick=\"hpopup('{$UI_HANDLER}?act=SUBJECTS.removeSubjFromGr&"+param+"')\" "+oF+">&nbsp;##Remove from group##&nbsp;</a></li>";
                break;

                case "SUBJECTS.removeSubj":
                    contextmenuHtml = contextmenuHtml + "<li><a class='contextmenu' href=\"javascript: popup('{$UI_BROWSER}?popup[]=SUBJECTS.confirmRemoveSubj&"+param+"', 'confirmRemoveSubj', 400, 50)\" "+oF+">&nbsp;##Delete##&nbsp;</a></li>";
                break;

                {literal}
            }
        }
        document.getElementById('contextmenucontainer').innerHTML = contextmenuHeader + contextmenuHtml + contextmenuFooter;
        document.onclick = showcontextmenu;

        return false;
    }


    function showcontextmenu(e) {
        if (ie5) {
            if (event.clientX + contextmenuWidth  > document.body.clientWidth)  xPos = event.clientX - contextmenuWidth + document.body.scrollLeft;
            else                                                                xPos = event.clientX + document.body.scrollLeft;
            if (event.clientY + contextmenuHeight > document.body.clientWidth)  yPos = event.clientY - contextmenuHeight + document.body.scrollTop;
            else                                                                yPos = event.clientY + document.body.scrollTop;
        }
        else {
            if (e.pageX + contextmenuWidth + 20 > window.innerWidth)            xPos = e.pageX - contextmenuWidth;
            else                                                                xPos = e.pageX;
            if (e.pageY + contextmenuHeight +20 > window.innerHeight)           yPos = e.pageY - contextmenuHeight;
            else                                                                yPos = e.pageY;
        }

        setTimeout("document.getElementById('contextmenu').style.left = xPos; document.getElementById('contextmenu').style.top = yPos;", 10);
        contextmenuStatus = 1;
        document.onclick  = hidecontextmenu;
    }


    function hidecontextmenu(e) {
        if (contextmenuStatus == 1) {
            setTimeout("document.getElementById('contextmenu').style.top =- 250", 0);
            contextmenuStatus = 0;
        }
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
