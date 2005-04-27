<div class="container_elements" style="width: 607px;">
    <h1>##User/Group Management##</h1>

    <div class="container_table" style="width: 594px;">
        <table style="width: 574px;">

            <tr class="blue_head">
                <td style="width: 189px">##Name##</td>
                <td style="width: 85px;">##Members##</td>
                <td style="width: 85px; border: 0">##Type##</td>
            </tr>

        {foreach from=$SUBJECTS->getSubjectsWCnt() item=i}
            <tr class="{cycle values='blue1, blue2'}"
                onClick="return contextmenu('id={$i.id}&login={urlencode str=$i.login}', {if $i.type|lower eq 'g'}'SUBJECTS.manageGroupMember', {else}'SUBJECTS.chgPasswd', {/if} 'SUBJECTS.removeSubj')"
            >
                <td>{$i.login}</td>
                <td>
                    {if $i.type|lower == 'g'}
                        {$i.cnt}
                    {else}
                        -
                    {/if}
                </td>
                <td style="border: 0"><img src="img/{$i.type|lower}.gif" border="0" alt="{$i.type|capitalize}" /></td>

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
