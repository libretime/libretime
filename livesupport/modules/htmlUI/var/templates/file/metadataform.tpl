{UIBROWSER->metaDataForm id=$editItem.id langid=$editItem.curr_langid assign="_metadataform"}

<div id="tabnav">
    <ul>
        <li><a href="#" onClick="javascript:showMain();"  id="switch_Main">##Main##</a></li>
        <li><a href="#" onClick="javascript:showMusic();" id="switch_Music">##Music##</a></li>
        <li><a href="#" onClick="javascript:showTalk();"  id="switch_Talk">##Talk##</a></li>
    </ul>
</div>

    {assign var="dynform" value=$_metadataform.langswitch}
    {include file="sub/dynForm_plain.tpl"}

    {foreach from=$_metadataform.pages key="key" item="dynform"}
        {include file="sub/dynForm_plain.tpl"}
    {/foreach}

<script language="javascript">
{literal}

var MData_confirmChangeVisited = false;

function MData_confirmChange(element)
{
    if (MData_confirmChangeVisited) return true;

    if (confirm("##Are you sure you want to change existing metadata?##")) {
        MData_confirmChangeVisited = true;
        return true;
    } else {
        document.forms['langswitch'].elements['target_langid'].focus();
        return false;
    }
}

function MData_loadLang()
{
    {/literal}
    location.href="{$UI_BROWSER}?act=editItem&id={$editItem.id}&MData_langId=" + document.forms['langswitch'].elements['MData_langid'].value;
    {literal}
}

function MData_submit()
{
    if (validate_editMetaData(document.forms['editMetaData'])) {
        document.forms['editMetaData'].elements['target_langid'].value = document.forms['langswitch'].elements['target_langid'].value;
        document.forms['editMetaData'].submit();
    }
    showMain();
    return false;
}

function MData_switchLang()
{
    document.forms['editMetaData'].elements['target_langid'].value = document.forms['langswitch'].elements['target_langid'].value;
    document.forms['editMetaData'].submit();
}

function MData_cancel()
{
    {/literal}
    location.href='{$UI_BROWSER}';
    {literal}
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
