{literal}
<style type="text/css">
    .dynformelement {
        width : 270px;
        text-align : right;
    }
</style>
{/literal}

<div id="mdataform">
<center>
    {$mDataForm.tabs}
    {$mDataForm.langswitch}
    {foreach from=$mDataForm.pages key=key item=dynform}
        {include file="form_parts/dynForm_plain.tpl"}
    {/foreach}
</center>
</div>

<script language="javascript">
{literal}
function collectAll()
{
    alert('collecting data...');
    return true;
}

function switchMDataLang()
{
    if (validate_metadata(document.forms['metadata'])) {
        document.forms['metadata'].elements['langid'].value = document.forms['langswitch'].elements['langid'].value;
        document.forms['metadata'].submit();
    }
    document.forms['langswitch'].elements['langid'].value = document.forms['metadata'].elements['langid'].value
    showMain();
    return false;
}

function spread(element, name)
{
    if (document.forms['metadata'].elements['Main-' + name])           document.forms['metadata'].elements['Main-' + name].value = element.value;
    if (document.forms['metadata'].elements['Music_Basic-' + name])    document.forms['metadata'].elements['Music_Basic-' + name].value = element.value;
    if (document.forms['metadata'].elements['Music_Advanced-' + name]) document.forms['metadata'].elements['Music_Advanced-' + name].value = element.value;
    if (document.forms['metadata'].elements['Talk_Basic-' + name])     document.forms['metadata'].elements['Talk_Basic-' + name].value = element.value;
    if (document.forms['metadata'].elements['Talk_Advanced-' + name])  document.forms['metadata'].elements['Talk_Advanced-' + name].value = element.value;
}

function showMain()
{
    document.getElementById('mdataform').style.height=400;
    document.getElementById('masterpanel').style.height=600;
    document.getElementById('div_Main').style.visibility='';
    document.getElementById('div_Main').style.height='';
    document.getElementById('div_Music_Basic').style.visibility='hidden';
    document.getElementById('div_Music_Basic').style.height='0';
    document.getElementById('div_Music_Advanced').style.visibility='hidden';
    document.getElementById('div_Music_Advanced').style.height='0';
    document.getElementById('div_Talk_Basic').style.visibility='hidden';
    document.getElementById('div_Talk_Basic').style.height='0';
    document.getElementById('div_Talk_Advanced').style.visibility='hidden';
    document.getElementById('div_Talk_Advanced').style.height='0';
}
function showMusic_Basic()
{
    document.getElementById('mdataform').style.height=600;
    document.getElementById('masterpanel').style.height=800;
    document.getElementById('div_Main').style.visibility='hidden';
    document.getElementById('div_Main').style.height='0';
    document.getElementById('div_Music_Basic').style.visibility='';
    document.getElementById('div_Music_Basic').style.height='';
    document.getElementById('div_Music_Advanced').style.visibility='hidden';
    document.getElementById('div_Music_Advanced').style.height='0';
    document.getElementById('div_Talk_Basic').style.visibility='hidden';
    document.getElementById('div_Talk_Basic').style.height='0';
    document.getElementById('div_Talk_Advanced').style.visibility='hidden';
    document.getElementById('div_Talk_Advanced').style.height='0';
}
function showMusic_Advanced()
{
    document.getElementById('mdataform').style.height=1300;
    document.getElementById('masterpanel').style.height=1500;
    document.getElementById('div_Main').style.visibility='hidden';
    document.getElementById('div_Main').style.height='0';
    document.getElementById('div_Music_Basic').style.visibility='hidden';
    document.getElementById('div_Music_Basic').style.height='0';
    document.getElementById('div_Music_Advanced').style.visibility='';
    document.getElementById('div_Music_Advanced').style.height='';
    document.getElementById('div_Talk_Basic').style.visibility='hidden';
    document.getElementById('div_Talk_Basic').style.height='0';
    document.getElementById('div_Talk_Advanced').style.visibility='hidden';
    document.getElementById('div_Talk_Advanced').style.height='0';
}
function showTalk_Basic()
{
    document.getElementById('mdataform').style.height=400;
    document.getElementById('masterpanel').style.height=600;
    document.getElementById('div_Main').style.visibility='hidden';
    document.getElementById('div_Main').style.height='0';
    document.getElementById('div_Music_Basic').style.visibility='hidden';
    document.getElementById('div_Music_Basic').style.height='0';
    document.getElementById('div_Music_Advanced').style.visibility='hidden';
    document.getElementById('div_Music_Advanced').style.height='0';
    document.getElementById('div_Talk_Basic').style.visibility='';
    document.getElementById('div_Talk_Basic').style.height='';
    document.getElementById('div_Talk_Advanced').style.visibility='hidden';
    document.getElementById('div_Talk_Advanced').style.height='0';
}
function showTalk_Advanced()
{
    document.getElementById('mdataform').style.height=400;
    document.getElementById('masterpanel').style.height=600;
    document.getElementById('div_Main').style.visibility='hidden';
    document.getElementById('div_Main').style.height='0';
    document.getElementById('div_Music_Basic').style.visibility='hidden';
    document.getElementById('div_Music_Basic').style.height='0';
    document.getElementById('div_Music_Advanced').style.visibility='hidden';
    document.getElementById('div_Music_Advanced').style.height='0';
    document.getElementById('div_Talk_Basic').style.visibility='hidden';
    document.getElementById('div_Talk_Basic').style.height='0';
    document.getElementById('div_Talk_Advanced').style.visibility='';
    document.getElementById('div_Talk_Advanced').style.height='';
}
showMain();
{/literal}
</script>
