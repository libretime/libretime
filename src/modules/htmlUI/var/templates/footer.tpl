        <div class="copyright">{$UI_VERSION} &copy;{$UI_COPYRIGHT_DATE}
            <a href="http://www.mdlf.org" target="_blank">MDLF</a>
            - maintained and distributed under GNU/GPL by 
            <a href="http://www.campware.org" target="_blank">CAMPWARE</a>
        </div>
    </div>

<script>
    {UIBROWSER->getAlertMsg assign='alertMsg'}
    {if $alertMsg}
        alert('{$alertMsg|escape:quotes}');
    {/if}
</script>
</body>
</html>
