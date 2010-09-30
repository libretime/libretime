{if $act == "SUBJECTS.addUser"}
    {assign var="dynform" value=$SUBJECTS->getAddSubjForm('addUser')}
{/if}

{if $act == "SUBJECTS.addGroup"}
    {assign var="dynform" value=$SUBJECTS->getAddSubjForm('addGroup')}
{/if}

<div class="container_elements" style="width: 607px;">
    {if $act == 'SUBJECTS.addUser'}
        <h1>##Add User##</h1>
    {else}
        <h1>##Add Group##</h1>
    {/if}
    {include file="sub/dynForm_plain.tpl"}
</div>


