<div id="objects">
{include file="sub/x.tpl"}

<table border="0" width="90%" align="center">
    <tr bgcolor="{cycle values='#eeeeee, #dadada"'}">
            <th>Title</th>
            <th>Type</th>
            <td align="right" width='70%'>  &nbsp;
                {if $START.pid}<a href="{$UI_BROWSER}?act=fileBrowse&id={$GLOBALS.pid}">[go up]</a>{/if}
            </td>
        </tr>
    {if count($structure.listdata)}
        {foreach from=$structure.listdata item=i}
            <tr bgcolor="{cycle values='#eeeeee, #dadada"'}"
                    onMouseOver="highlight()" onMouseOut="darklight()"
                    onContextmenu="return menu('{$i.id}'
                        {if ($i.type == 'audioclip' || $i.type == 'webstream')}
                            ,'PL.addItem', 'PL.newUsingItem', 'SP.addItem', 'delete'
                        {/if}
                        {if ($i.type == 'playlist')}
                            ,'PL.activate'
                            {if $PLAYLIST.id == $i.id}
                                ,'PL.release'
                            {else}
                                ,'PL.addItem', 'SP.addItem', 'delete'
                            {/if}
                        {/if}
                        )"
            >
              <td align="center">
                <span id="ID{$i.id}">
                    {if $i.type eq 'Folder'}
                         <a href="{$UI_BROWSER}?act=fileBrowse&id={$i.id}" >[{$i.title|truncate:30}]</b>
                    {else}
                         {$i.title|truncate:30}
                    {/if}
                </span>
              </td>
              <td align="center">{$i.type}</td>
              <td>
                  <!-- &nbsp;<a href="javascript:frename('{$i.name}', '{$i.id}')">[rename]</a> -->
                  &nbsp;<a href="javascript:fmove('{$i.id}', '.')">[move]</a>
                  &nbsp;<a href="javascript:fcopy('{$i.id}', '.')">[copy]</a>
                  &nbsp;<a href="{$UI_BROWSER}?act=permissions&id={$i.id}">[permissions]</a>
                  <!--
                  {if ($delOverride eq $i.id)}
                      <a href="{$UI_HANDLER}?act=delete&id={$i.id}&delOverride={$i.id}"
                        onClick="return confirm('Really delete non empty Folder &quot;{$i.name}&quot; now?')">[DEL]</a>
                  {else}
                      <a href="{$UI_HANDLER}?act=delete&id={$i.id}"
                        onClick="return confirm('Delete &quot;{$i.name}&quot;?')">[DEL]</a>
                  {/if} -->
                  {if $i.type != 'Folder'}
                      <br>
                      &nbsp;<a href="{$UI_BROWSER}?act=getMData&id={$i.id}">[MDataXML]</a>
                      &nbsp;<a href="{$UI_BROWSER}?act=editMetaData&id={$i.id}">[MDataForm]</a>
                      {if $i.type eq 'webstream'}
                          &nbsp;<a href="{$UI_BROWSER}?act=addWebstream&id={$i.id}&replace=1">[Replace]</a>
                      {elseif $i.type eq 'audioclip'}
                          &nbsp;<a href="{$UI_BROWSER}?act=uploadFile&id={$i.id}&replace=1">[Replace]</a>
                          &nbsp;<a href="{$CONFIG.accessRawAudioUrl}?id={$i.gunid}&sessid={$START.sessid}">[Access]</a>
                          &nbsp;<a href="{$UI_BROWSER}?act=_analyzeFile&id={$i.id}">[RawAnalyze]</a>
                      {/if}
                      <!-- &nbsp;<a href="#" onclick="hpopup('{$UI_HANDLER}?act=SP.addItem&id={$i.id}', '2SP')">[SP]</a> -->
                  {/if}
                  &nbsp;
              </td>
           </tr>
        {/foreach}
    {else}
        <tr><td align="center" width="400">No objects</td></tr>
    {/if}
</table>

</div>