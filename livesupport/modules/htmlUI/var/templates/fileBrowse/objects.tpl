<div id="objects">
{include file="sub/x.tpl"}

<table border="0" width="90%" align="center">
    {if count($structure.listdata)}
        <tr bgcolor="{cycle values='#eeeeee, #dadada"'}">
            <th>Title</th>
            <th>Type</th>
            <td align="right">
                {if $GLOBALS.pid}<a href="{$UI_BROWSER}?act=fileBrowse&id={$GLOBALS.pid}">[go up]</a>{/if}
            </td>
        </tr>
        {foreach from=$structure.listdata item=i}
            <tr bgcolor="{cycle values='#eeeeee, #dadada"'}"
                    onMouseOver="highlight()" onMouseOut="darklight()"
                    onContextmenu="return menu('{$i.id}'
                        {if $i.type == ('audioclip' || 'webstream')}
                            ,'PL.addItem', 'PL.newUsingItem', 'SP.addItem', 'delete'
                        {/if}
                        )"
            >
              <td align="center">
                <span id="ID{$i.id}">
                    {if $i.type eq 'Folder'}
                         <a href="{$UI_BROWSER}?act=fileBrowse&id={$i.id}" >[{$i.title}]</b>
                    {else}
                         {$i.title}
                    {/if}
                </span>
              </td>
              <td align="center">{$i.type}</td>
              <td>
                  &nbsp;<a href="javascript:frename('{$i.name}', '{$i.id}')">[rename]</a>
                  &nbsp;<a href="javascript:fmove('{$i.id}', '.')">[move]</a>
                  &nbsp;<a href="javascript:fcopy('{$i.id}', '.')">[copy]</a>
                  &nbsp;<a href="{$UI_BROWSER}?act=permissions&id={$i.id}">[permissions]</a>
                  <br>
                  &nbsp;
                  {if ($delOverride eq $i.id)}
                      <a href="{$UI_HANDLER}?act=delete&id={$i.id}&delOverride={$i.id}"
                        onClick="return confirm('Really delete non empty Folder &quot;{$i.name}&quot; now?')">[DEL]</a>
                  {else}
                      <a href="{$UI_HANDLER}?act=delete&id={$i.id}"
                        onClick="return confirm('Delete &quot;{$i.name}&quot;?')">[DEL]</a>
                  {/if}
                  {if $i.type != 'Folder'}
                      &nbsp;<a href="{$UI_BROWSER}?act=getFile&id={$i.id}">[Access]</a>
                      &nbsp;<a href="{$UI_BROWSER}?act=getMData&id={$i.id}">[vMData]</a>
                      &nbsp;<a href="{$UI_BROWSER}?act=_analyzeFile&id={$i.id}">[Analyze]</a>
                      &nbsp;
                      {if $i.type eq 'webstream'}
                          <a href="{$UI_BROWSER}?act=addWebstream&id={$i.id}&replace=1">[Replace]</a>
                      {elseif $i.type eq 'audioclip'}
                          <a href="{$UI_BROWSER}?act=uploadFile&id={$i.id}&replace=1">[Replace]</a>
                      {/if}
                      &nbsp;<a href="{$UI_BROWSER}?act=editMetaData&id={$i.id}">[eMData]</a>
                      &nbsp;<a href="#" onclick="hpopup('{$UI_HANDLER}?act=SP.addItem&id={$i.id}', '2SP')">[SP]</a>
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