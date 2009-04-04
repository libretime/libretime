{include file="popup/header.tpl"}

{if $data.connect}
    <div>{tra str='Connection to $1 port $2 $3' 1=$data.host 2=$data.port 3='<font color="green">successfull.</font>'}</div>

    <div>
    {if $data.code == 200}
        {tra str='URL is <font color="green">valid</font>.'}
    {else}
        {tra str='URL seems <font color="red">invalid</font>. Returned error-code: $1.' 1=$data.code}
    {/if}
    </div>

    <div>
    {if $data.type.valid === true}
        {tra str='Stream is wanted type <font color="green">$1</font>.' 1=$data.type.type}

    {elseif $data.type.type}
        {tra str='Stream has wrong content type <font color="red">$1</font>.' 1=$data.type.type}
    {else}
        ##No content type declared.##
    {/if}
    </div>

    <p>
        <input type="button" class="button" value="##Listen##" onclick="listenStream()">
        <input type="button" class="button" value="##Close##"  onclick="window.close()">
    </p>
    <br>

    {if $data.header}
        ##Connection header:##
        <br>
        <textarea rows="6" cols="45" readonly>{$data.header}</textarea>
    {/if}

{else}
     <div>{tra str='Connection to $1 port $2 $3' 1=$data.host 2=$data.port 3='<font color="red">failed</font>'}</div>
      <p><input type="button" class="button" value="##Close##" onclick="window.close()"></p>
{/if}

<script language="javascript">
{literal}
function listenStream()
{
{/literal}
    testStreamWin = window.open("{$UI_TESTSTREAM_MU3_TMP}", "Test Stream", "width=100, height=100");
    setTimeout("testStreamWin.close()", 5000);
    {literal}
}
{/literal}
</script>

</body>
</html>

