{* Smarty template *}
<div id="subjects">

{if $changePassForm}
    {$changePassForm}
{/if}

{if $groups}
    <table id="tree" border="0" cellpadding="5">
      <tr><th colspan="5">Subjects in group {$groups.gname}</th></tr>

    {if (is_array($groups.rows) && count($groups.rows)>0)}
        {foreach from=$groups.rows item=row}
            <tr bgcolor="{cycle values='#eeeeee, #dadada"'}">
                <td>{$row.id}</td>
                <td class="b">
                    {if ($row.type=='G')}
                        <a href="{$UI_BROWSER}?act=addsubj2group&id={$row.id}">{$row.login}</a>
                    {else}
                        {$row.login}
                    {/if}
                 </td
                 <td>{if ($row.type=='G')}(G){else}(U){/if}</td>
                <td>
                 <a href="{$UI_HANDLER}?act=removeSubjFromGr&login={urlencode str=$row.login}&gname={urlencode str=$groups.gname}&reid={$groups.id}">
                    [remove from Group]
                 </a>
                </td>
            </tr>
        {/foreach}
    {else}
        <tr class="odd"><td colspan="3">No Members</td></tr>
    {/if}

    </table>
    <br>
    {$addSubj2GroupForm}
{/if}

{if $subjects}
    <table width="100%" border="0" cellpadding="1">
        <tr><th colspan="4">Subjects</th></tr>
        <tr><th>id</th><th>Login</th><th>User/Group</th><td></td></tr>

        {if (is_array($subjects.subj) && count($subjects.subj)>0)}
            {foreach from=$subjects.subj item=c}
                <tr bgcolor="{cycle values='#eeeeee, #dadada"'}">
                    <td>{$c.id}</td>
                    <td class="b">
                        {if ($c.type eq 'G')}
                            <a href="{$UI_BROWSER}?act=groups&id={$c.id}">{$c.login}</a>
                        {else}
                            {$c.login}
                        {/if}
                    </td
                    <td>
                        {if ($c.type == 'G')}
                            G: {$c.cnt}
                        {else}
                            (U)
                        {/if}
                    </td>
                    <td>
                     <a class="lnkbutt" href="{$UI_HANDLER}?act=removeSubj&login={urlencode str=$c.login}">[remove]</a>
                     {if ($c.type != 'G')}
                        <a class="lnkbutt" href="{$UI_BROWSER}?act=passwd&uid={urlencode str=$c.id}">[change Password]</a>
                     {/if}
                    </td>
                </tr>
            {/foreach}
        {else}
            <tr><td>no subject</td></tr>
        {/if}

    </table>

    <br>
    <div align="center">
    <a href='{$UI_BROWSER}?act=addUser'>[Add User]</a>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href='{$UI_BROWSER}?act=addGroup'>[Add Group]</a>
    <br><br>
    {$addSubjectForm}
    </div>
{/if}

</div>