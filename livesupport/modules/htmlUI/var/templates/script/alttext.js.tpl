{literal}
<script type="text/javascript">

    document.write('<div id="alttextContainer"></div>');
    alttextWidth    = 200;
    alttextHeight   = 0;
    alttextduration = 0;

    function showalttext(param) {
        var alttextHeader  = "<div class='alttext' id='alttext' style='position: absolute; top: -1000; left: 0; z-index: 99'>";
        var alttextFooter  = "</div>";
        var alttextHtml    = '';

        var sp2         = "&nbsp;&nbsp;";
        var sp5         = sp2 + sp2 + "&nbsp;";                     // Leerzeichen als Abstandshalter (flexibler und code-sparender als eine aufwendige Tabellenkonstruktion) ;
        var oF          = "onfocus = 'if (this.blur) this.blur()'"; // Um hässlichen Linkrahmen in einigen Browsern zu vermeiden;
        var entry       = new Array();
        //contextmenuStatus = 0;

        alttextHtml = alttextHtml + param;

        document.getElementById('alttextContainer').innerHTML = alttextHeader + alttextHtml + alttextFooter;
        document.onmouseover = showalttextthan;

        //return false;
    }

    function showalttextthan(e) {    // alert("now");
        if (ie5) {
            if (event.clientX + contextmenuWidth  > document.body.clientWidth)  xPos = event.clientX - contextmenuWidth + document.body.scrollLeft;
            else                                                                xPos = event.clientX + document.body.scrollLeft;
            if (event.clientY + contextmenuHeight > document.body.clientWidth)  yPos = event.clientY - contextmenuHeight + document.body.scrollTop;
            else                                                                yPos = event.clientY + document.body.scrollTop;
        }
        else {
            if (e.pageX + contextmenuWidth + 20 > window.innerWidth)            xPos = e.pageX - alttextWidth/2;
            else                                                                xPos = e.pageX - alttextWidth/2;
            if (e.pageY + contextmenuHeight +20 > window.innerHeight)           yPos = e.pageY - alttextHeight/2 + 15;
            else                                                                yPos = e.pageY - alttextHeight/2 + 15;
        }

        alttexthide = false;
        setTimeout("showalttextnow("+xPos+", "+yPos+")", alttextduration);
        document.onmouseover = null;

    }

    function showalttextnow(xPos, yPos) {
        if (!alttexthide) {
            document.getElementById('alttext').style.left = xPos;
            document.getElementById('alttext').style.top = yPos;
        }
    }

    function hidealttext() {
        alttexthide = true;
        setTimeout("hidealttextnow()", alttextduration);
    }

    function hidealttextnow() {
        document.getElementById('alttext').style.top = -1000;
    }

</script>

<style type="text/css">
    #alttext {
        font-size : 80%;
        float: left;
        width: 200px;
        list-style: none;
        line-height: 20px;
        padding: 4px;
        margin: 0px 0px 0px 0px;
        display: block;
        clear: left;
        background: yellow;
        border: 1px solid #ACB3BA;
    }
</style>
{/literal}
