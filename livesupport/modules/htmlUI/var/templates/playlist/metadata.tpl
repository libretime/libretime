{assign var="_form" value=$PL->metaDataForm($_PL.curr_langid)}

{assign var="dynform" value=$_form.langswitch}
{include file="sub/dynForm_plain.tpl"}

{assign var="dynform" value=$_form.main}
{include file="sub/dynForm_plain.tpl"}

<script type="text/javascript">
{literal}
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
