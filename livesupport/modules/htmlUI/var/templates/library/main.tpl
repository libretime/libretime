{if $searchForm}
    {assign var="_act_prefix" value="SEARCH"}
    {include file="library/searchForm.tpl"}
    {SEARCH->getResult assign=_results}
    {SEARCH->getCriteria assign=_criteria}
{/if}

{if $browseForm}
    {assign var="_act_prefix" value="BROWSE"}
    {include file="library/browseForm.tpl"}
    {BROWSE->getResult assign=_results}
    {BROWSE->getCriteria assign=_criteria}
{/if}

{include file="library/results.tpl"}