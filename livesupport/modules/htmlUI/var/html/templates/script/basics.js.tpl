<script type="text/javascript">
     function frename(name, id){literal}{{/literal}
        var s=document.getElementById('ID'+id);
        s.innerHTML='<form action="{$UI_HANDLER}" method="post" style="display:inline">'+
                        '<input type="text" name="newname" value="'+name+'" size="12">'+
                        '<input type="hidden" name="id" value="'+id+'">'+
                        '<input type="hidden" name="act" value="rename">'+
                    '</form>';
     {literal}}{/literal}
     function fmove(id, relPath){literal}{{/literal}
        var newPath=prompt('Destination folder (relative path):', relPath);
        if(newPath==null) return;
        location.href='{$UI_HANDLER}?id='+id+'&act=move&newPath='+newPath;
     {literal}}{/literal}
     function fcopy(id, relPath){literal}{{/literal}
        var newPath=prompt('Destination folder (relative path):', relPath);
        if(newPath==null) return;
        location.href='{$UI_HANDLER}?id='+id+'&act=copy&newPath='+newPath;
     {literal}}{/literal}
     function freplicate(name, id){literal}{{/literal}
        var np=prompt('Destination folder (relative path):', id);
        if(np==null) return;
        location.href='{$UI_HANDLER}?id='+id+'&act=repl&newparid='+np;
     {literal}}{/literal}
     function newFolder(){literal}{{/literal}
        var nn=prompt('New folder name:');
        if(nn==null) return;
        location.href='{$UI_HANDLER}?id={$GLOBALS.id}&act=newFolder&newname='+nn;
     {literal}}{/literal}

     {literal}
     function popup(url, name, width, height)   // popup in center of perent window
     {
        var screenX;
        var screenY;

        screenX = (window.screenX + window.innerWidth/2 - width/2);
        screenY = (window.screenY + window.innerHeight/2 - height/2);
        arg = 'width='+width+', height='+height+', scrollbars=no, menubar=no, depend=yes, left='+screenX+', top='+screenY;

        popupwin = window.open(url, name, arg);
        window.popupwin.focus();
     }

     function hpopup(url, name)                 //hidden popup!
     {
        popupwin = window.open(url, name);
        //window.parent.focus();
     }
     {/literal}

     {uiBrowser->getAlertMsg assign='alertMsg'}
     {if $alertMsg}
        alert('{$alertMsg}');
     {/if}


</script>
