{assign var="_PL_activeId" value=$PL->getActiveId()}
<div class="contenttabnav">
{if $START.pid}
    <a href="{$UI_BROWSER}?act=fileList&id={$START.pid}">##go up##</a>
{/if}
{include file="file/path.tpl"}
</div>
    <div class="head" style="width:555px; height: 21px;">&nbsp;
</div>
    <div class="container_table" style="width: 555px; height: auto;">
<table>
    <tr class="blue_head">
            <td style="width: 130px">Title</td>
            <td style="width: 50px">Type</td>
            <td style="width: 447px; border: 0">  &nbsp;
            </td>
        </tr>
    {if count($structure.listdata)}
        {foreach from=$structure.listdata item=i}
            {assign var="_listen_gunid" value=$i.gunid}
            <tr class="{cycle values='blue1, blue2'}" {assign var="moreContextBefore" value=", 'SP.addItem'"}{include file="sub/contextmenu.tpl"}>
              <td>
                <span id="ID{$i.id}">
                    {if $i.type eq 'Folder'}
                         <a href="{$UI_BROWSER}?act=fileList&id={$i.id}" >[{$i.title|truncate:30}]</b>
                    {else}
                        {if $_PL_activeId == $i.id}
                            <b>{$i.title|truncate:30}</b>
                        {else}
                            {$i.title|truncate:30}
                        {/if}
                    {/if}
                </span>
              </td>
              <td>{$i.type}</td>
              <td style="border: 0">
                  <!-- &nbsp;<a href="javascript:frename('{$i.name}', '{$i.id}')">[rename]</a> -->
                  &nbsp;<a href="javascript:fmove('{$i.id}', '.')">##move##</a>
                  &nbsp;<a href="javascript:fcopy('{$i.id}', '.')">##copy##</a>
                  &nbsp;<a href="{$UI_BROWSER}?act=permissions&id={$i.id}">##permissions##</a>
                  <!--
                  {if ($delOverride eq $i.id)}
                      <a href="{$UI_HANDLER}?act=delete&id={$i.id}&delOverride={$i.id}"
                        onClick="return confirm('Really delete non empty Folder &quot;{$i.name}&quot; now?')">[DEL]</a>
                  {else}
                      <a href="{$UI_HANDLER}?act=delete&id={$i.id}"
                        onClick="return confirm('Delete &quot;{$i.name}&quot;?')">[DEL]</a>
                  {/if} -->
                  {if $i.type != 'Folder'}
                      &nbsp;<a href="{$UI_BROWSER}?act=getMData&id={$i.id}">##MDataXML##</a>
                      <!-- &nbsp;<a href="{$UI_BROWSER}?act=editMetaData&id={$i.id}">[MDataForm]</a> -->
                      {if $i.type eq 'webstream'}
                          &nbsp;<a href="{$UI_BROWSER}?act=editWebstream&id={$i.id}">##Edit##</a>
                      {elseif $i.type eq 'audioclip'}
                          &nbsp;<a href="{$UI_BROWSER}?act=editFile&id={$i.id}">##Edit##</a>
                          &nbsp;<a href="{$CONFIG.accessRawAudioUrl}?id={$i.gunid}&sessid={$START.sessid}">##Access##</a>
                          &nbsp;<a href="{$UI_BROWSER}?act=_analyzeFile&id={$i.id}">##RawAnalyze##</a>
                      {/if}
                      <!-- &nbsp;<a href="#" onclick="hpopup('{$UI_HANDLER}?act=SP.addItem&id={$i.id}', '2SP')">[SP]</a> -->
                  {/if}
                  &nbsp;
              </td>
           </tr>
        {/foreach}
    {else}
        <tr><td align="center" width="400">##No objects##</td></tr>
    {/if}
</table>
</div>