<div class="container_table" style="width: 555px; height: auto;">
<table>
    <tr class="blue_head">
            <td style="width: 300px">Subject Name</td>
            <td style="width: 50px">Action</td>
            <td style="width: 50px;">Permission</td>
            <td style="width: 50px; border: 0">&nbsp;</td>
        </tr>


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
                <td style="border:0">
                    <a href="{$UI_HANDLER}?act=removePerm&permid={$row.permid}&oid={$permissions.id}&id={$permissions.id}"
                    onClick="return confirm('Delete permission &quot;{$da}&nbsp;{$row.action}&quot; for user {$row.login}?')">[remove]</a>
                </td>
            </tr>
        {/foreach}
    {else}
        <tr><td colspan="4" style="border:0">No Permissions set.</td></tr>
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