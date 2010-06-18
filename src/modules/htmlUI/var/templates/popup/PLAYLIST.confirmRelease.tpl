{include file="popup/header.tpl"}

{if $PL->changed}
    <center>
    ##Do you want to save changes?##
    <br><br>
    <input type="button" class="button" onClick="window.close()" value="Cancel">
    <input type="button" class="button" onClick="location.href='{$UI_HANDLER}?act=PL.revertANDclose'" value="No">
    <input type="button" class="button" onClick="location.href='{$UI_HANDLER}?act=PL.release'" value="Yes">
    </center>
{else}
    <script language="javascript">
        location.href = '{$UI_HANDLER}?act=PL.release';
    </script>
{/if}

</body>
</html>
