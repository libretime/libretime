<div class="content">
    <!-- start editor -->
    <div class="container_elements" style="width: 607px;">
    <h1>
    {if $editItem.id}
        ##Edit##
    {else}
        ##New##
    {/if}
    {$editItem.type|capitalize}
    </h1>

    {if $editItem.type == 'audioclip' || $editItem.type == 'file'}
        <div id="div_Data">     {include file="file/fileform.tpl"}          </div>
        <div id="div_MData">    {include file="file/metadataform.tpl"}      </div>
    <input type="button" class="button" onClick="showData()" value="##Data##">
    <input type="button" class="button" onClick="showMData()" value="##Metadata##">
    {/if}

    {if $editItem.type == 'webstream'}
        <div id="div_Data">     {include file="file/webstreamform.tpl"}     </div>
        <div id="div_MData">    {include file="file/metadataform.tpl"}      </div>
    <input type="button" class="button" onClick="showData()" value="##Data##">
    <input type="button" class="button" onClick="showMData()" value="##Metadata##">
    {/if}

    {if $editItem.type == 'playlist'}
        {include file="file/metadataform.tpl"}
    {/if}
</div>
<!-- end editor -->
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


