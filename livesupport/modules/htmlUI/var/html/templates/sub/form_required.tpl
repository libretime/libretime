{if $error}
    <font color="red">{$label|upper}</font>
{else}
    {$label}

{/if}

{if $required}
    <font color="red" size="1">*</font>
{/if}
