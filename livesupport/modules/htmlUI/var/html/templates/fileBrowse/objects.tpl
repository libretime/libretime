<div id="objects">
{include file="sub/x.tpl"}

<table border="0" align="center">
   {if $GLOBALS.pid}
        <tr><td colspan="2" align="right"><a href="{$UI_BROWSER}?act=fileBrowse&id={$GLOBALS.pid}">[go up]</a_</td></tr>
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
                   <a {if $o.type eq 'Folder'}href="{$UI_BROWSER}?act=fileBrowse&id={$o.id}" {/if}>[{$o.title}]</a>:{$o.type}
               </span>
             </td>

             <td>
                 &nbsp;<a href="javascript:frename('{$o.name}', '{$o.id}')">[rename]</a>
                 &nbsp;<a href="javascript:fmove('{$o.id}', '.')">[move]</a>
                 &nbsp;<a href="javascript:fcopy('{$o.id}', '.')">[copy]</a>
                 &nbsp;<a href="{$UI_BROWSER}?act=permissions&id={$o.id}">[permissions]</a>
                 <br>
                 &nbsp;
                 {if ($delOverride eq $o.id)}
                     <a href="{$UI_HANDLER}?act=delete&id={$o.id}&delOverride={$o.id}"
                       onClick="return confirm('Really delete non empty Folder &quot;{$o.name}&quot; now?')">[DEL]</a>
                 {else}
                     <a href="{$UI_HANDLER}?act=delete&id={$o.id}"
                       onClick="return confirm('Delete &quot;{$o.name}&quot;?')">[DEL]</a>
                 {/if}
                 {if $o.type != 'Folder'}
                     &nbsp;<a href="{$UI_BROWSER}?act=getFile&id={$o.id}">[Access]</a>
                     &nbsp;<a href="{$UI_BROWSER}?act=getMData&id={$o.id}">[vMData]</a>
                     &nbsp;<a href="{$UI_BROWSER}?act=_analyzeFile&id={$o.id}">[Analyze]</a>
                     &nbsp;
                     {if $o.type eq 'webstream'}
                         <a href="{$UI_BROWSER}?act=addWebstream&id={$o.id}&replace=1">[Replace]</a>
                     {elseif $o.type eq 'audioclip'}
                         <a href="{$UI_BROWSER}?act=uploadFile&id={$o.id}&replace=1">[Replace]</a>
                     {/if}
                     &nbsp;<a href="{$UI_BROWSER}?act=editMetaData&id={$o.id}">[eMData]</a>
                     &nbsp;<a href="#" onclick="hpopup('{$UI_HANDLER}?act=SP.addItem&id={$o.id}', '2SP')">[SP]</a>
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