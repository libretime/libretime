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
    document.getElementById('div_Main').style.display='inline';
    document.getElementById('div_Music').style.display='none';
    document.getElementById('div_Talk').style.display='none';
}
function showMusic()
{
    document.getElementById('div_Main').style.display='none';
    document.getElementById('div_Music').style.display='inline';
    document.getElementById('div_Talk').style.display='none';
}
function showTalk()
{
    document.getElementById('div_Main').style.display='none';
    document.getElementById('div_Music').style.display='none';
    document.getElementById('div_Talk').style.display='inline';
}
showMain();
{/literal}
</script>
