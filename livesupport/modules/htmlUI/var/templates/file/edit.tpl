<div class="standardFrame">
{include file="sub/x.tpl"}

<h4>
{if $editItem.id}
    Edit
{else}
    New
{/if}
{if $editItem.type == 'file'}
    File
{else}
    Webstream
{/if}
</h4>

<input type="button" onClick="showData()" value="Data">
<input type="button" onClick="showMData()" value="MData">

{if $editItem.type == 'file'}
<div id="div_Data">
    {include file="file/fileform.tpl"}
</div>
<div id="div_MData">
    {include file="file/metadataform.tpl"}
</div>
{/if}



{if $editItem.type == 'webstream'}
<div id="div_Data">
    {include file="file/webstreamform.tpl"}
</div>
<div id="div_MData">
    {include file="file/metadataform.tpl"}
</div>
{/if}

</div>

{literal}
<script>

function showData()
{
{/literal}
    {if $editItem.id && $editItem.type == 'file'}
        alert('Sorry, function temporary disabled');
        return false;
    {/if}
{literal}
    document.getElementById('div_Data').style.visibility='visible';
    document.getElementById('div_Data').style.height='';
    document.getElementById('div_MData').style.visibility='hidden';
    document.getElementById('div_MData').style.height='0';
}
{/literal}

{if $editItem.id}
    {literal}
    function showMData()
    {
        document.getElementById('div_MData').style.visibility='visible';
        document.getElementById('div_MData').style.height='';
        document.getElementById('div_Data').style.visibility='hidden';
        document.getElementById('div_Data').style.height='0';
    }
    document.getElementById('div_Data').style.visibility='hidden';
    document.getElementById('div_Data').style.height='0';
    {/literal}
{else}
    {literal}
    function showMData()
    {
        alert ('First Data');
    }
    {/literal}
    document.getElementById('div_MData').style.visibility='hidden';
    document.getElementById('div_MData').style.height='0';
{/if}
</script>


