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
		
		{literal}
		<script language="javascript">
		function setRefresh(frm){
			
			var selected_1 = document.getElementById("category_1");
			var selected_2 = document.getElementById("category_2");
			var selected_3 = document.getElementById("category_3");

			var value_1 = document.getElementById("category_value_1");
			var value_2 = document.getElementById("category_value_2");
			var value_3 = document.getElementById("category_value_3");

			frm.elements['cat1'].value = selected_1[selected_1.selectedIndex].value;
			frm.elements['cat2'].value = selected_2[selected_2.selectedIndex].value;
			frm.elements['cat3'].value = selected_3[selected_3.selectedIndex].value;

			frm.elements['val1'].value = value_1[value_1.selectedIndex].value;
			frm.elements['val2'].value = value_2[value_2.selectedIndex].value;
			frm.elements['val3'].value = value_3[value_3.selectedIndex].value;
		}
		</script>
		{/literal}
		
		<form action="ui_handler.php" method="post" name="browse_refresh" id="browse_refresh" onSubmit="setRefresh(this)">
				<input name="act" type="hidden" value="BROWSE.refresh" /> 
				<input name="cat1" type="hidden" value="" />
				<input name="val1" type="hidden" value="" /> 
				<input name="cat2" type="hidden" value="" />
				<input name="val2" type="hidden" value="" />
				<input name="cat3" type="hidden" value="" />
				<input name="val3" type="hidden" value="" />     
          		<input type="submit" value="Refresh metadata"/>
        </form>

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