{*Smarty template*}

{include file="header.tpl"}
{$loginform.javascript}

<center>
<div id="login">
  <form {$loginform.attributes}>
    {$loginform.hidden}
    <table>
        <tr><td align="right">{$loginform.login.label}</td><td>{$loginform.login.html}</td></tr>
        <tr><td align="right">{$loginform.pass.label}</td><td>{$loginform.pass.html}</td></tr>
        <tr><td>{$loginform.requirednote}</td><td>{$loginform.Submit.html}</td></tr>
    </table>
  </form>
</div>
</center>
{include file="footer.tpl"}

