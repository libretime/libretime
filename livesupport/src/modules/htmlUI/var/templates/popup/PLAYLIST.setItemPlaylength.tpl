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
function PL_checkItemPlaylength()
{
    zero = new Date('january 01 1970 00:00:00');
    max  = new Date('january 01 1970 '+document.forms[0].elements['duration'].value);
    curr = new Date('january 01 1970 '+document.forms[0].elements['playlength[H]'].value+':'
                                      +document.forms[0].elements['playlength[i]'].value+':'
                                      +document.forms[0].elements['playlength[s]'].value);

    if (max.getTime() != zero.getTime() && max.getTime() < curr.getTime()) {
        alert('##Playlength cannot be longer than item duration##');
        return false;
    } 
    if (curr.getTime() == zero.getTime()) {
        alert('##Playlength cannot be zero##');   
        return false;  
    }
    
    document.forms[0].submit();
}   
{/literal} 
</script>

</body>
</html>

