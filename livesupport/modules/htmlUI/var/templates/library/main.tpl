{*Smarty template*}

{if $searchForm}
    {include file="library/searchForm.tpl"}
    {SEARCH->getResult assign=searchResults}
{/if}

{if $browseForm}
    {include file="library/browseForm.tpl"}
    {BROWSE->getResult assign=searchResults}
{/if}

{include file="library/results.tpl"}