{literal}
<style type="text/css">
    .dynformelement {
        width : 270px;
        text-align : right;
    }
</style>
{/literal}

<div id="metadataform">
<center>
    {$editMetaData.tabs}
    {$editMetaData.langswitch}
    {foreach from=$editMetaData.pages key=key item=dynform}
        {include file="form_parts/dynForm_plain.tpl"}
    {/foreach}
</center>
</div>

<script language="javascript">
{literal}
function switchMDataLang()
{
    if (validate_editMetaData(document.forms['editMetaData'])) {
        document.forms['editMetaData'].elements['langid'].value = document.forms['langswitch'].elements['langid'].value;
        document.forms['editMetaData'].submit();
    }
    document.forms['langswitch'].elements['langid'].value = document.forms['editMetaData'].elements['langid'].value
    showMain();
    return false;
}

function spread(element, name)
{
    if (document.forms['editMetaData'].elements['Main__' + name])           document.forms['editMetaData'].elements['Main__' + name].value = element.value;
    if (document.forms['editMetaData'].elements['Music_Basic__' + name])    document.forms['editMetaData'].elements['Music_Basic__' + name].value = element.value;
    if (document.forms['editMetaData'].elements['Music_Advanced__' + name]) document.forms['editMetaData'].elements['Music_Advanced__' + name].value = element.value;
    if (document.forms['editMetaData'].elements['Talk_Basic__' + name])     document.forms['editMetaData'].elements['Talk_Basic__' + name].value = element.value;
    if (document.forms['editMetaData'].elements['Talk_Advanced__' + name])  document.forms['editMetaData'].elements['Talk_Advanced__' + name].value = element.value;
}

function showMain()
{
    document.getElementById('metadataform').style.height=400;
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
    document.getElementById('metadataform').style.height=600;
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
    document.getElementById('metadataform').style.height=1300;
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
    document.getElementById('metadataform').style.height=400;
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
    document.getElementById('metadataform').style.height=400;
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
