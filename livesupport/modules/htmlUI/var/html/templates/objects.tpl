<div id="objects">
<table border="0" align="center">

{if $structure.msg}
    <script langauge="javascript">
        <!--
        alert('{$structure.msg}');
        -->
    </script>
{/if}

{if count($structure.listdata)}
    {foreach from=$structure.listdata item=o}
        <tr bgcolor="{cycle values='#eeeeee, #dadada"'}">
          <td>
            <span id="ID{$o.id}">
                {if $structure.tree}
                    {str_repeat str='&nbsp;' count=$o.level}
                {else}
                    {str_repeat str='&nbsp;' count=3}
                {/if}
                <a {if $o.type eq 'Folder'}href="{$UI_BROWSER}?id={$o.id}" {/if}>[{$o.title}]</a>:&nbsp;&nbsp;
            </span>
          </td>

          <td>
              {$a.$o.type}
              &nbsp;<a href="javascript:frename('{$o.name}', '{$o.id}')">[rename]</a>
              &nbsp;<a href="javascript:fmove('{$o.id}', '.')">[move]</a>
              &nbsp;<a href="javascript:fcopy('{$o.id}', '.')">[copy]</a>
              &nbsp;<a href="{$UI_BROWSER}?act=permissions&id={$o.id}">[permissions]</a>
              <br>
              &nbsp;
              {if ($delOverride eq $o.id)}
                  <a href="{$UI_HANDLER}?act=delete&id={$o.id}&delOverride={$o.id}"
                    onClick="return confirm('Really delete non empty object &quot;{$o.name}&quot; now?')">[DEL]</a>
              {else}
                  <a href="{$UI_HANDLER}?act=delete&id={$o.id}"
                    onClick="return confirm('Delete object &quot;{$o.name}&quot;?')">[DEL]</a>
              {/if}
              {if $o.type != 'Folder'}
                  &nbsp;<a href="{$UI_BROWSER}?act=getFile&id={$o.id}">[Access]</a>
                  &nbsp;<a href="{$UI_BROWSER}?act=getMData&id={$o.id}">[vMData]</a>
                  &nbsp;<a href="{$UI_BROWSER}?act=_analyzeFile&id={$o.id}">[Analyze]</a>
                  &nbsp;<a href="{$UI_BROWSER}?act=editFile&id={$o.id}">[Edit]</a>
                  &nbsp;<a href="{$UI_BROWSER}?act=editMetaData&id={$o.id}">[eMData]</a>
                  &nbsp;<a href="#" onclick="hpopup('{$UI_HANDLER}?act=SP.addItem&SPid={$o.id}', '2SP')">[SP]</a>
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