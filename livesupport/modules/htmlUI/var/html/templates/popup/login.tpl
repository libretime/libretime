{*Smarty template*}

{include file="header.tpl"}
{$login.javascript}

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

</body>
</html>

