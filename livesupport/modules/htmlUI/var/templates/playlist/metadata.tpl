{assign var="_form" value=$PL->metaDataForm()}
{assign var="dynform" value=$_form.main}

{$_form.langswitch}
{include file="sub/dynForm_plain.tpl"}

<script type="text/javascript">
{literal}
function switchMDataLang()
{
    if (validate_editMetaData(document.forms['editMetaData'])) {
        document.forms['editMetaData'].elements['langid'].value = document.forms['langswitch'].elements['langid'].value;
        document.forms['editMetaData'].submit();
    }
    return false;
}
{/literal}
</script>
