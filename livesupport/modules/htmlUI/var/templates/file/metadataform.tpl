{UIBROWSER->metaDataForm id=$editItem.id assign="_metadataform"}

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
    {$_metadataform.tabs}
    {$_metadataform.langswitch}
    {foreach from=$_metadataform.pages key="key" item="dynform"}
        {include file="sub/dynForm_plain.tpl"}
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
    if (document.forms['editMetaData'].elements['Main___' + name])     document.forms['editMetaData'].elements['Main___' + name].value  = element.value;
    if (document.forms['editMetaData'].elements['Music___' + name])    document.forms['editMetaData'].elements['Music___' + name].value = element.value;
    if (document.forms['editMetaData'].elements['Talk___' + name])     document.forms['editMetaData'].elements['Talk___' + name].value  = element.value;
}

function showMain()
{
    document.getElementById('metadataform').style.height=400;
    document.getElementById('masterpanel').style.height=600;
    document.getElementById('div_Main').style.visibility='';
    document.getElementById('div_Main').style.height='';
    document.getElementById('div_Music').style.visibility='hidden';
    document.getElementById('div_Music').style.height='0';
    document.getElementById('div_Talk').style.visibility='hidden';
    document.getElementById('div_Talk').style.height='0';
}
function showMusic()
{
    document.getElementById('metadataform').style.height=1600;
    document.getElementById('masterpanel').style.height=1800;
    document.getElementById('div_Main').style.visibility='hidden';
    document.getElementById('div_Main').style.height='0';
    document.getElementById('div_Music').style.visibility='';
    document.getElementById('div_Music').style.height='';
    document.getElementById('div_Talk').style.visibility='hidden';
    document.getElementById('div_Talk').style.height='0';
}
function showTalk()
{
    document.getElementById('metadataform').style.height=800;
    document.getElementById('masterpanel').style.height=1000;
    document.getElementById('div_Main').style.visibility='hidden';
    document.getElementById('div_Main').style.height='0';
    document.getElementById('div_Music').style.visibility='hidden';
    document.getElementById('div_Music').style.height='0';
    document.getElementById('div_Talk').style.visibility='';
    document.getElementById('div_Talk').style.height='';
}
showMain();
{/literal}
</script>
