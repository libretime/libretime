        <div class="copyright">{$UI_VERSION} &copy;{$UI_COPYRIGHT_DATE}
            <a href="http://www.sourcefabric.org" target="_blank">Sourcefabric</a>
            - maintained and distributed under GNU/GPL v3
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
