{if $_REQUEST.act == addFileMData || $_REQUEST.act == addWebstreamMData || $_REQUEST.act == editItem}

    {UIBROWSER->metaDataForm id=$editItem.id langid=$editItem.curr_langid assign="_metadataform"}
    
    {assign var="dynform" value=$_metadataform.langswitch}
    {include file="sub/dynForm_plain.tpl"}

    {foreach from=$_metadataform.pages key="key" item="dynform"}
        {include file="sub/dynForm_plain.tpl"}
    {/foreach}
    
    <script language="javascript">
    {literal}
    
    var MData_confirmChangeVisited = true;
    
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
        if (document.forms['editMetaData'].elements['Voice___' + name])     document.forms['editMetaData'].elements['Voice___' + name].value  = element.value;
    }
    {/literal}
    
    
    {if $_REQUEST.act == addFileData || $_REQUEST.act == addWebstreamData}
        document.getElementById('div_MData').style.display   = 'none';
    {else}
        document.getElementById('div_Data').style.display  = 'none';
        showMain();
    {/if}
    
    </script>
    
{/if}