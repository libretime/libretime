{include file="popup/header.tpl"}
{$login.javascript}

<table height="100%" border="0" width="100%">
    <tr>
        <td valign="middle">
            <center>
              <form {$login.attributes}>
                {$login.hidden}
                <table>
                    <tr><td align="right">{$login.login.label}</td><td>{$login.login.html}</td></tr>
                    <tr><td align="right">{$login.pass.label}</td><td>{$login.pass.html}</td></tr>
                    <tr><td align="right">{$login.langid.label}</td><td>{$login.langid.html}</td></tr>
                    <tr><td>{$login.requirednote}</td><td>{$login.Submit.html} {$login.cancel.html}</td></tr>
                </table>
              </form>
            </center>
        </td>
    </tr>
</table>
</body>
</html>

