{UIBROWSER->metaDataForm id=$editItem.id assign="_metadataform"}

<div id="tabnav">
<ul>
<!--li><a href="#" class="active">Search</a></li-->
<li><a href="#" onClick="javascript:showMain();"  id="switch_Main">##Main##</a></li>
<li><a href="#" onClick="javascript:showMusic();" id="switch_Music">##Music##</a></li>
<li><a href="#" onClick="javascript:showTalk();"  id="switch_Talk">##Talk##</a></li>
</ul>
</div>

    {*$_metadataform.tabs*}   
    {*$_metadataform.langswitch*}

    {foreach from=$_metadataform.pages key="key" item="dynform"}
        {include file="sub/dynForm_plain.tpl"}
    {/foreach}

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
    document.getElementById('div_Main').style.display   = 'inline';
    document.getElementById('div_Music').style.display  = 'none';
    document.getElementById('div_Talk').style.display   = 'none';
    document.getElementById('switch_Main').className    = 'active';
    document.getElementById('switch_Music').className   = '';
    document.getElementById('switch_Talk').className    = '';
}
function showMusic()
{
    document.getElementById('div_Main').style.display   = 'none';
    document.getElementById('div_Music').style.display  = 'inline';
    document.getElementById('div_Talk').style.display   = 'none';
    document.getElementById('switch_Main').className    = '';
    document.getElementById('switch_Music').className   = 'active';
    document.getElementById('switch_Talk').className    = '';
}
function showTalk()
{
    document.getElementById('div_Main').style.display   = 'none';
    document.getElementById('div_Music').style.display  = 'none';
    document.getElementById('div_Talk').style.display   = 'inline';
    document.getElementById('switch_Main').className    = '';
    document.getElementById('switch_Music').className   = '';
    document.getElementById('switch_Talk').className    = 'active';
}
showMain();
{/literal}
</script>
