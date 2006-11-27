{assign var="mainURL" value=$UI_BROWSER|cat:"?id="|cat:$START.id|cat:"&act="}

{assign var="browseURL" value=$mainURL|cat:"BROWSE"}
{assign var="searchURL" value=$mainURL|cat:"SEARCH"}
{assign var="hubBrowseURL" value=$UI_BROWSER|cat:"?id="|cat:$START.id|cat:"&popup[]=HUBBROWSE.getResults"}
{assign var="hubSearchURL" value=$mainURL|cat:"HUBSEARCH"}
{assign var="transfersURL" value=$mainURL|cat:"TRANSFERS"}

{if $searchForm}
		<div id="tabnav">
			<ul>
				<li><a href="{$browseURL}">##Browse##</a></li>
				<li><a href="#" class="active">##Search##</a></li>
				{* <li><a href="{$hubBrowseURL}">##Hub Browse##</a></li>*}
				<li><a href="{$hubSearchURL}">##Hub Search##</a></li>
				<li><a href="{$transfersURL}">##Transfers##</a></li>
			</ul>
		</div>
        <div class="content">
        <!-- start search -->
        <div class="container_elements" style="width: 607px;">
        <h1>##Search##</h1>
    {assign var="_act_prefix" value="SEARCH"}
    {assign var="_form" value=$searchForm}
    {include file="library/searchForm.tpl"}
    {SEARCH->getResult assign=_results}
    {SEARCH->getCriteria assign=_criteria}
{/if}


{if $browseForm}
		<div id="tabnav">
			<ul>
				<li><a href="#" class="active">##Browse##</a></li>
				<li><a href="{$searchURL}">##Search##</a></li>
				{*<li><a href="{$hubBrowseURL}">##Hub Browse##</a></li>*}
				<li><a href="{$hubSearchURL}">##Hub Search##</a></li>
				<li><a href="{$transfersURL}">##Transfers##</a></li>
			</ul>
		</div>
        <div class="content">
        <!-- start browsing -->
		<div class="container_elements" style="width: 607px;">
		<h1>##Browse##</h1>
    {assign var="_act_prefix" value="BROWSE"}
    {include file="library/browseForm.tpl"}
    {BROWSE->getResult assign=_results}
    {BROWSE->getCriteria assign=_criteria}
{/if}


{if $hubBrowseForm}
		<div id="tabnav">
			<ul>
				<li><a href="{$browseURL}">##Browse##</a></li>
				<li><a href="{$searchURL}">##Search##</a></li>
				{*<li><a href="#" class="active">##Hub Browse##</a></li>*}
				<li><a href="{$hubSearchURL}">##Hub Search##</a></li>
				<li><a href="{$transfersURL}">##Transfers##</a></li>
			</ul>
		</div>
        <div class="content">
        <!-- start hub browsing -->
		<div class="container_elements" style="width: 607px;">
		<h1>##Hub Browse##</h1>
    {assign var="_act_prefix" value="HUBBROWSE"}
    {include file="library/hubBrowseForm.tpl"}
    {HUBBROWSE->getResult assign=_results}
    {HUBBROWSE->getCriteria assign=_criteria}
{/if}


{if $hubSearchForm}
		<div id="tabnav">
			<ul>
				<li><a href="{$browseURL}">##Browse##</a></li>
				<li><a href="{$searchURL}">##Search##</a></li>
				{*<li><a href="{$hubBrowseURL}">##Hub Browse##</a></li>*}
				<li><a href="#" class="active">##Hub Search##</a></li>
				<li><a href="{$transfersURL}">##Transfers##</a></li>
			</ul>
		</div>
        <div class="content">
        <!-- start hub search -->
		<div class="container_elements" style="width: 607px;">
		<h1>##Hub Search##</h1>
    {assign var="_act_prefix" value="HUBSEARCH"}
    {assign var="_form" value=$hubSearchForm}
    {include file="library/searchForm.tpl"}
    {HUBSEARCH->getResult assign=_results}
    {HUBSEARCH->getCriteria assign=_criteria}
{/if}


{if $transfersForm}
		<div id="tabnav">
			<ul>
				<li><a href="{$browseURL}">##Browse##</a></li>
				<li><a href="{$searchURL}">##Search##</a></li>
				{*<li><a href="{$hubBrowseURL}">##Hub Browse##</a></li>*}
				<li><a href="{$hubSearchURL}">##Hub Search##</a></li>
				<li><a href="#" class="active">##Transfers##</a></li>
			</ul>
		</div>
        <div class="content">
        <!-- start transfers -->
		<div class="container_elements" style="width: 607px;">
		<h1>##Transfers##</h1>
    {assign var="_act_prefix" value="TRANSFERS"}
    {TRANSFERS->getTransfers assign=_results}
    {include file="library/transfersForm.tpl"}
{else}
	{include file="library/results.tpl"}
{/if}

    </div>
</div>