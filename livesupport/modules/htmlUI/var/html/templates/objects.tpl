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
            {if $structure.tree}
                {str_repeat str='&nbsp;' count=$o.level}<span id="ID{$o.id}">
            {else}
                {str_repeat str='&nbsp;' count=3}       <span id="ID{$o.id}">
            {/if}
            <a {if $o.type eq 'Folder'}href="{$UI_BROWSER}?id={$o.id}" {/if}>[{$o.name}]</a>:&nbsp;&nbsp;
                                                        </span>

          </td>
          <td>
              {$a.$o.type}
              &nbsp;<a href="javascript:frename('{$o.name}', '{$o.id}')" class="button">[rename]</a>
              &nbsp;<a href="javascript:fmove('{$o.id}', '.')" class="button">[move]</a>
              &nbsp;<a href="javascript:fcopy('{$o.id}', '.')" class="button">[copy]</a>
              {*
              &nbsp;<a href="javascript:freplicate('<?php echo$o['name']?>', '{$o.id}')" class="button">[replicate]</a>
              *}
              &nbsp;<a href="{$UI_BROWSER}?act=permissions&id={$o.id}" class="button">[permissions]</a>
              <br>
              &nbsp;
              {if ($delOverride eq $o.id)}
                  <a href="{$UI_HANDLER}?act=delete&id={$o.id}&delOverride={$o.id}" class="button"
                    onClick="return confirm('Really delete non empty object &quot;{$o.name}&quot; now?')">[DEL]</a>
              {else}
                  <a href="{$UI_HANDLER}?act=delete&id={$o.id}" class="button"
                    onClick="return confirm('Delete object &quot;{$o.name}&quot;?')">[DEL]</a>
              {/if}
              {if $o.type != 'Folder'}
                  &nbsp;<a href="{$UI_BROWSER}?act=getFile&id={$o.id}" class="button">[Access]</a>
                  &nbsp;<a href="{$UI_BROWSER}?act=getInfo&id={$o.id}" class="button">[Analyze]</a>
                  &nbsp;<a href="{$UI_BROWSER}?act=editMetaDataValues&id={$o.id}" class="button">[MetaData]</a>
              {/if}
              {if $o.type eq 'Replica'}
                  &nbsp; (-&gt;$o.target})
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