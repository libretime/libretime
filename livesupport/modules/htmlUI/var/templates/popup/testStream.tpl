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
    {else}
        {tra str='Stream has wrong content type <font color="red">$1</font>.' 1=$data.type.type}
    {/if}
    </div>

    <br><br>

    <div>
    {if $data.header}
        Returned connection header:
        <pre>{$data.header}</pre>
    {/if}
    </div>

{else}
     <div>{tra str='Connection to $1 port $2 $3' 1=$data.host 2=$data.port 3='<font color="red">failed</font>'}</div>
{/if}


</body>
</html>

