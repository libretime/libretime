<div class="standardFrame">
{include file="sub/x.tpl"}

<h4>
{if $editItem.id}
    Edit
{else}
    New
{/if}
{$editItem.type|capitalize}
</h4>

{if $editItem.type == 'audioclip' || $editItem.type == 'file'}
<input type="button" onClick="showData()" value="Data">
<input type="button" onClick="showMData()" value="MData">
<div id="div_Data">
    {include file="file/fileform.tpl"}
</div>
<div id="div_MData">
    {include file="file/metadataform.tpl"}
</div>
{/if}


{if $editItem.type == 'webstream'}
<input type="button" onClick="showData()" value="Data">
<input type="button" onClick="showMData()" value="MData">
<div id="div_Data">
    {include file="file/webstreamform.tpl"}
</div>
<div id="div_MData">
    {include file="file/metadataform.tpl"}
</div>
{/if}

{if $editItem.type == 'playlist'}
<div id="div_MData">
    {include file="file/metadataform.tpl"}
</div>
{/if}

</div>


<script>

function showData()
{literal}
{
{/literal}
    {if $editItem.id && $editItem.type == 'file'}
        alert('Sorry, function temporary disabled');
        return false;
    {/if}

    document.getElementById('div_Data').style.display   = 'inherit';
    document.getElementById('div_MData').style.display  = 'none';
{literal}
}
{/literal}

{if $editItem.id}
    {literal}
    function showMData()
    {
        document.getElementById('div_MData').style.display  = 'inherit';
        document.getElementById('div_Data').style.display   = 'none';
    }
    document.getElementById('div_Data').style.display   = 'none';
    {/literal}
{else}
    {literal}
    function showMData()
    {
        alert ('Data first!');
    }
    {/literal}
    document.getElementById('div_MData').style.display  = 'none';
{/if}
</script>


