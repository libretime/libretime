{*Smarty template*}

{if $searchForm}
    {include file="library/searchForm.tpl"}
{/if}

{if $showSearchResults}
    {include file="library/searchResults.tpl"}
{/if}

{if $browseForm}          
    {include file="library/browseForm.tpl"}
{/if}
