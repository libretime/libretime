<div class="standardFrame">
{include file="sub/x.tpl"}
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
    document.getElementById('div_Data').style.visibility='visible';
    document.getElementById('div_Data').style.height='';
    document.getElementById('div_MData').style.visibility='hidden';
    document.getElementById('div_MData').style.height='0';
}
function showMData()
{
    document.getElementById('div_MData').style.visibility='visible';
    document.getElementById('div_MData').style.height='';
    document.getElementById('div_Data').style.visibility='hidden';
    document.getElementById('div_Data').style.height='0';
}

document.getElementById('div_MData').style.visibility='hidden';
document.getElementById('div_MData').style.height='0';
</script>

{/literal}
