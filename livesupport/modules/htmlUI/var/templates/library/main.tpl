{*Smarty template*}

{if $searchForm}
    {assign var="_act_prefix" value="SEARCH"}
    {include file="library/searchForm.tpl"}
    {SEARCH->getResult assign=searchResults}
{/if}

{if $browseForm}
    {assign var="_act_prefix" value="BROWSE"}
    {include file="library/browseForm.tpl"}
    {BROWSE->getResult assign=searchResults}
{/if}

{include file="library/results.tpl"}