{if $searchForm}
		<div id="tabnav">
			<ul>
				<li><a href="#" class="active">Search</a></li>
				<li><a href="{$UI_BROWSER}?id={$START.id}&act=BROWSE">Browse</a></li>					
			</ul>
		</div>		
        <div class="content">	
    {assign var="_act_prefix" value="SEARCH"}
    {include file="library/searchForm.tpl"}
    {SEARCH->getResult assign=_results}
    {SEARCH->getCriteria assign=_criteria}
{/if}

{if $browseForm}
		<div id="tabnav">
			<ul>
				<li><a href="{$UI_BROWSER}?id={$START.id}&act=SEARCH">Search</a></li>
				<li><a href="#" class="active">Browse</a></li>					
			</ul>
		</div>			
        <div class="content">
    {assign var="_act_prefix" value="BROWSE"}
    {include file="library/browseForm.tpl"}
    {BROWSE->getResult assign=_results}
    {BROWSE->getCriteria assign=_criteria}
{/if}

{include file="library/results.tpl"}
</div>