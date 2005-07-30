{literal}
<script type="text/javascript">
    document.write('<div id="alttextContainer"></div>');

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

    function showalttextthan(e) {
        var spacer         = 15;
        var alttextWidth   = document.getElementById('alttext').clientWidth;
        var alttextHeight  = document.getElementById('alttext').clientHeight;

        if (ie5) {
           var clickX  = event.clientX;
           var clickY  = event.clientY + document.body.scrollTop;
           var winY    = document.body.clientHeight;
           var scrollY = document.body.scrollTop;
        } else {
           var clickX  = e.pageX;
           var clickY  = e.pageY;
           var winY    = window.innerHeight;
           var scrollY = window.scrollY;
        }

        if (clickX < alttextWidth)                      var xPos = clickX + spacer;
        else                                            var xPos = clickX - alttextWidth - spacer;

        if (clickY + alttextHeight > winY  + scrollY)   var yPos = winY - alttextHeight + scrollY;
        else                                            var yPos = clickY;

        alttexthide = false;
        setTimeout("showalttextnow("+xPos+", "+yPos+")");
        document.onmouseover = null;

    }

    function showalttextnow(xPos, yPos) {
        if (!alttexthide) {
            document.getElementById('alttext').style.left = xPos;
            document.getElementById('alttext').style.top  = yPos;
        }
    }

    function hidealttext() {
        var delay = 0;
        alttexthide = true;
        setTimeout("hidealttextnow()", delay);
    }

    function hidealttextnow() {
        document.getElementById('alttext').style.top = -1000;
    }

</script>

<style type="text/css">
    #alttext {
        font-size :     80%;
        float:          left;
        width:          200px;
        list-style:     none;
        line-height:    16px;
        padding:        4px;
        margin:         0px 0px 0px 0px;
        display:        block;
        clear:          left;
        background:     #FFFACD;
        border:         1px solid #ACB3BA;
    }
</style>
{/literal}
