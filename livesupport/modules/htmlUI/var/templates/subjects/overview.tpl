<div class="container_elements" style="width: 607px;">
    <h1>##User/Group Management##</h1>

    <div class="container_table" style="width: 594px;">
        <table style="width: 574px;">

            <tr class="blue_head">
                <td>##Login##</td>
                <td style="width: 40px; text-align: center">##Members##</td>
                <td style="width: 40px; border: 0; text-align: center">##Type##</td>
            </tr>

        {foreach from=$SUBJECTS->getSubjectsWCnt() item=i}
            {if $i.type|lower == 'g'}{assign var="_type" value="group"}{else}{assign var="_type" value="user"}{/if}
            <tr class="{cycle values='blue1, blue2'}"
                onClick="return contextmenu('id={$i.id}&login={urlencode str=$i.login}', {if $i.type|lower eq 'g'}'SUBJECTS.manageGroupMember', {else}'SUBJECTS.chgPasswd', {/if} 'SUBJECTS.removeSubj')"
            >
                <td>{$i.login}</td>
                <td style="width: 30px; text-align: center;">
                    {if $i.type|lower == 'g'}
                        {$i.cnt}
                    {else}
                        -
                    {/if}
                </td>
                <td style="border: 0; text-align: center;"><img src="img/{$_type}.png" border="0" alt="{$_type|capitalize}" /></td>

            </tr>
        {/foreach}

        </table>

        <br>
        <div class="container_button">
            <input type="button" class="button" value="##Add User##"  onclick="location.href='{$UI_BROWSER}?act=SUBJECTS.addUser'">
            <input type="button" class="button" value="##Add Group##" onclick="location.href='{$UI_BROWSER}?act=SUBJECTS.addGroup'">
        </div>

    </div>
</div>

{assign var="_type" value=null}
