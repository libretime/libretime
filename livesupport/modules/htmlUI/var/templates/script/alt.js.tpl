{literal}
<script type="text/javascript">

    document.write('<div id="altContainer"></div>');
    altWidth  = 200;
    altHeight = 0;

    function showAlt(param) {
        var altHeader  = "<div class='alt' id='alt' style='position: absolute; top: -250; left: 0; z-index: 99'>";
        var altFooter  = "</div>";
        var altHtml    = '';

        var sp2         = "&nbsp;&nbsp;";
        var sp5         = sp2 + sp2 + "&nbsp;";                     // Leerzeichen als Abstandshalter (flexibler und code-sparender als eine aufwendige Tabellenkonstruktion) ;
        var oF          = "onfocus = 'if (this.blur) this.blur()'"; // Um hässlichen Linkrahmen in einigen Browsern zu vermeiden;
        var entry       = new Array();
        //contextmenuStatus = 0;

        altHtml = altHtml + param;

        document.getElementById('altContainer').innerHTML = altHeader + altHtml + altFooter;
        document.onmouseover = showAltNow;

        //return false;
    }

    function showAltNow(e) {    // alert("now");
        if (ie5) {
            if (event.clientX + contextmenuWidth  > document.body.clientWidth)  xPos = event.clientX - contextmenuWidth + document.body.scrollLeft;
            else                                                                xPos = event.clientX + document.body.scrollLeft;
            if (event.clientY + contextmenuHeight > document.body.clientWidth)  yPos = event.clientY - contextmenuHeight + document.body.scrollTop;
            else                                                                yPos = event.clientY + document.body.scrollTop;
        }
        else {
            if (e.pageX + contextmenuWidth + 20 > window.innerWidth)            xPos = e.pageX - altWidth/2;
            else                                                                xPos = e.pageX - altWidth/2;
            if (e.pageY + contextmenuHeight +20 > window.innerHeight)           yPos = e.pageY - altHeight/2 +5;
            else                                                                yPos = e.pageY - altHeight/2 +5;
        }

        setTimeout("document.getElementById('alt').style.left = xPos; document.getElementById('alt').style.top = yPos;", 1000);

        document.onmouseover = null;

    }

    function hideAlt() {
        setTimeout("document.getElementById('alt').style.top = -250", 1000);
    }

</script>

<style type="text/css">
    #alt {
        font-size : 80%;
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
</style>
{/literal}
