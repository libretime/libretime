{include file="popup/header.tpl"}



<table height="100%" width="100%">
    <tr>
        <td style="border: 0">
            <center>
                <table width="100%" height="100%">
                    <tr><td style="border: 0">
                        {include file="sub/dynForm_plain.tpl}
                    </td></tr>
                </table>
            </center>
        </td>
    </tr>
</table>

<script language="javascript">
{literal}   
function PL_setClipLength(changed_elem)
{
    var duration   = parseInt(document.forms[0].elements['duration'].value);
    var clipLength = parseInt(document.forms[0].elements['clipLength'].value);
    var clipStart  = parseInt(document.forms[0].elements['clipStart'].value);
    var clipEnd    = parseInt(document.forms[0].elements['clipEnd'].value);
    
    if (changed_elem.name == 'clipLength') {
        document.forms[0].elements['clipEnd'].value = clipLength + clipStart;     
    } else {
        if (clipEnd - clipStart <=0 ) {
            alert('##Remaining cliplength need to has a positive value.##');   
            return false;
        }
        document.forms[0].elements['clipLength'].value = clipEnd - clipStart;      
    }
}
{/literal} 
</script>

</body>
</html>

