<div id="permissions">
{include file="sub/x.tpl"}

<table id="tbl" border="0" cellpadding="5">
    <tr><td><b>Subject Name</b></td><td><b>Action</b></td><td><b>Permission</b></td><td></td></tr>

    {if (is_array($permissions.perms) && count($permissions.perms)>0)}

        {foreach from=$permissions.perms item=row}
            {if $row.type eq 'A'}
                {assign var='da' value='allow'}
            {else}
                {if $row.type eq 'D'}
                    {assign var='da' value='deny'}
                {else}
                    {assign var='da' value=$row.type}
                {/if}
            {/if}

            <tr bgcolor="{cycle values='#eeeeee, #dadada'}">
                <td>{* <a <?php #href="alibExPList.php?id=<?php echo$row['subj']? >"?>> *}{$row.login}</a></td>
                <td>{$row.action}</td>
                <td>{$da}</td>
                <td>
                    <a href="{$UI_HANDLER}?act=removePerm&permid={$row.permid}&oid={$permissions.id}&id={$permissions.id}"
                    onClick="return confirm('Delete permission &quot;{$da}&nbsp;{$row.action}&quot; for user {$row.login}?')">[remove]</a>
                </td>
            </tr>
        {/foreach}
    {else}
        <tr><td colspan="4">No Permissions set.</td></tr>
    {/if}
</table>

<br>

<form action="{$UI_HANDLER}" method="post">

Add Permission
<select name="allowDeny">
 <option value="A">Allow</option>
 <option value="D">Deny</option>
</select>

for Action
<select name="permAction">
 <option value="_all">all</option>
    {if is_array($permissions.actions)}
        {foreach from=$permissions.actions item='it'}
            <option value="{$it}">{$it}</option>
        {/foreach}
    {/if}
</select>

to Subject
<select name="subj">
    {if is_array($permissions.subjects)}
        {foreach from=$permissions.subjects item='it'}
            <option value="{$it.id}">{$it.login}</option>
        {/foreach}
    {/if}
</select>
<input type="hidden" name="act" value="addPerm">
<input type="hidden" name="id" value="{$permissions.id}">
<input type="submit" value="Do it!">
</form>

</div>