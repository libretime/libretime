<div class="container_elements" style="width: 607px;">
<h1>##Playlist Metadata##: {$PL->title}</h1>
{assign var="_form" value=$PL->metaDataForm($_PL.curr_langid)}

{assign var="dynform" value=$_form.langswitch}
{include file="sub/dynForm_plain.tpl"}

{assign var="dynform" value=$_form.main}
{include file="sub/dynForm_plain.tpl"}
</div>


<script type="text/javascript">
{literal}

var MData_confirmChangeVisited = false;

function MData_confirmChange(element)
{
    if (MData_confirmChangeVisited) return true;

    if (confirm("##Are you sure you want to change existing metadata?##")) {
        MData_confirmChangeVisited = true;
        return true;
    } else {
        document.forms['langswitch'].elements['target_langid'].focus();
        return false;
    }
}

function MData_submit()
{
    if (validate_editMetaData(document.forms["editMetaData"])) {
        document.forms["editMetaData"].elements["target_langid"].value = document.forms["langswitch"].elements["target_langid"].value;
        document.forms["editMetaData"].submit();
    }
    return false;
}

function MData_switchLang()
{
    document.forms["editMetaData"].elements["target_langid"].value = document.forms["langswitch"].elements["target_langid"].value;
    document.forms["editMetaData"].submit();
}

function MData_cancel()
{
    {/literal}
    location.href="{$UI_BROWSER}?act=PL.simpleManagement";
    {literal}
}

{/literal}
</script>
