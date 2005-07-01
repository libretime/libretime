<div class="content">
{if $_REQUEST.act == addFileMData || $_REQUEST.act == addWebstreamMData || $_REQUEST.act == editItem}
    {include file="file/tabswitch.tpl"}
{/if}
    <div class="container_elements" style="width: 607px;">
        <h1>
        {if $_REQUEST.act == addFileData || $_REQUEST.act == addFileMData || $_REQUEST.act == addWebstreamData || $_REQUEST.act == addWebstreamMData}
            ##New##
        {else}
            ##Edit##
        {/if}
        {$editItem.type|capitalize}
        </h1>

    {if $editItem.type == 'audioclip' || $editItem.type == 'file'}
        <div id="div_Data">
        {if $_REQUEST.act == 'addFileData'}

                {UIBROWSER->fileForm id=$editItem.id folderId=$editItem.folderId assign="dynform"}
                {include file="sub/dynForm_plain.tpl}
                {assign var="_uploadform" value=null}
        {/if}
        </div>

        <div id="div_MData">
            {include file="file/metadataform.tpl"}
        </div>
    {/if}

    {if $editItem.type == 'webstream'}
        <div id="div_Data">
            {UIBROWSER->webstreamForm id=$editItem.id folderId=$editItem.folderId assign="dynform"}
            {include file="sub/dynForm_plain.tpl}
            {assign var="_uploadform" value=null}
        </div>

        <div id="div_MData">
            {include file="file/metadataform.tpl"}
        </div>
    {/if}

    {if $editItem.type == 'playlist'}
        {include file="file/metadataform.tpl"}
    {/if}

    </div>
</div>

<script language="javascript">

{if $_REQUEST.act == addFileData || $_REQUEST.act == addWebstreamData}
    document.getElementById('div_MData').style.display   = 'none';
{else}
    document.getElementById('div_Data').style.display  = 'none';
    showMain();
{/if}

</script>


