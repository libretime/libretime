{literal}
<script type="text/javascript">

var mouseoverWidth  = 150;
var mouseoverHeight = 0;
var duration        = 0;

var mouseoverHeader = "<div id='mouseoverText' style='position:absolute; top: -250; left: 0; z-index: 100; background-color: #ffcacb; padding: 2px; width: " + mouseoverWidth + "'>";
var mouseoverFooter = "</div>";
document.write('<div id="mouseoverContainer"></div>');

function mouseoverShow(text)
{
    document.getElementById('mouseoverContainer').innerHTML = mouseoverHeader + text + mouseoverFooter;
    document.onmouseover = mouseoverPlace;
}

function mouseoverPlace(e)
{
    if (ie5) {
        var corr = 5;
        if (event.clientX > mouseoverWidth)     xPos = event.clientX - mouseoverWidth + document.body.scrollLeft;
        else                                    xPos = event.clientX + document.body.scrollLeft;
        if (event.clientY > mouseoverHeight)    yPos = event.clientY - mouseoverHeight + document.body.scrollTop;
        else                                    yPos = event.clientY + document.body.scrollTop;
    }
    else {
        var corr = 10;
        if (e.pageX > mouseoverWidth  + window.pageXOffset) xPos = e.pageX - mouseoverWidth;
        else                                                xPos = e.pageX;
        if (e.pageY > mouseoverHeight + window.pageYOffset) yPos = e.pageY - mouseoverHeight;
        else                                                yPos = e.pageY;
    }

    document.getElementById("mouseoverText").style.left = xPos - corr;
    document.getElementById("mouseoverText").style.top  = yPos;
    mouseoverStatus = 1;
    document.onmouseover = null;
}

function mouseoverHide(e) {
    if (mouseoverStatus == 1) {
        setTimeout("document.getElementById('mouseoverText').style.top =- 250", duration);
        mouseoverStatus = 0;
    }
}

</script>
{/literal}