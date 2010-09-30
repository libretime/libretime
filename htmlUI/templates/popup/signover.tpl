{include file="popup/header.tpl"}

<center>
<div id="logout">
  <form>
    <table border=0>
        <tr><td>##Are you sure you want to logout?##</td></tr>
        <tr><td align="right">
            <input type="button" value="##Cancel##" onclick="javascript: window.close()">
            <input type="button" value="##OK##" onclick="javascript: location.href='{$UI_HANDLER}?act=signover'">&nbsp;
        </td></tr>
    </table>
  </form>
</div>
</center>

</body>
</html>