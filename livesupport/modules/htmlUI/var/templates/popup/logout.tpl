{include file="popup/header.tpl"}

<table height="100%" width="100%">
    <tr>
        <td style="border: 0">
            <center>
              <form>
                <table width="100%" height="100%">
                    <tr><td style="border: 0">{tra 0='Are you sure to logout $1' 1=$USER.login}</td></tr>
                    <tr><td align="right" style="border: 0">
                        <input type="button" value="{tra 0=OK}" onclick="javascript: location.href='{$UI_HANDLER}?act={$logouttype}'">&nbsp;
                        <input type="button" value="{tra 0=Cancel}" onclick="javascript: window.close()">
                    </td></tr>
                </table>
              </form>
            </center>
        </td>
    </tr>
</table>

</body>
</html>
