<div class="content">

{if $act == "SUBJECTS"}
    {include file="subjects/overview.tpl"}
{/if}

{if $act == "SUBJECTS.addUser" || $act == "SUBJECTS.addGroup"}
    {include file="subjects/addSubjForm.tpl"}
{/if}

{if $act == "SUBJECTS.manageGroupMember"}
    {include file="subjects/manageGroupMember.tpl"}
{/if}

{if $act == "SUBJECTS.chgPasswd"}
    {include file="subjects/chgPasswd.tpl"}
{/if}
</div>