{* Smarty template *}
<div id="filedata">
{include file="sub/x.tpl"}
<center>
{if $fMetaData}
    <textarea rows="25" cols="90" style="font-size:small">{htmlspecialchars str=$fMetaData}</textarea>
{/if}
{if $_analyzeFile}
    {$_analyzeFile}
{/if}
</center>



</div>
