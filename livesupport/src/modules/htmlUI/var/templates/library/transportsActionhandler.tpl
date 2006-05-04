onClick="return contextmenu('{$i.id}'
	{if $i.state == 'running'}
	  , 'TR.pause'
	{elseif $i.state == 'paused'}
	  , 'TR.resume'
	{/if}

    , 'TR.cancel'
)"

