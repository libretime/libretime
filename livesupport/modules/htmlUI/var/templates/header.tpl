{* <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  *}
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<html>

<head>
    <title>LIVESUPPORT</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    {literal}
    <script type='text/javascript'><!--//--><![CDATA[//><!--
    sfHover = function() {
        var sfEls = document.getElementById("nav").getElementsByTagName("LI");
        for (var i=0; i<sfEls.length; i++) {
            sfEls[i].onmouseover=function() {
                this.className+=" sfhover";
            }
            sfEls[i].onmouseout=function() {
                this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
            }
        }
    }
    if (window.attachEvent) window.attachEvent("onload", sfHover);
    //--><!]]></script>
    {/literal}
    <link rel="stylesheet" href="styles.css">
    <link href="styles_livesupport.css" rel="stylesheet" type="text/css" />

    {include file="script/basics.js.tpl"}
    {include file="script/contextMenue.js.tpl"}
    {include file="script/collector.js.tpl"}
</head>

<body>
    <div class="container">
