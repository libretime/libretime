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
        location.href='{$UI_HANDLER}?id={$START.id}&act=newFolder&newname='+nn;
     {literal}}{/literal}

     {literal}
     function popup(url, name, width, height)   // popup in center of perent window
     {
        var screenX;
        var screenY;
        var Xpos;
        var Ypos;
        var Pwidth;
        var Pheight;

        if (ie5) {
            Xpos     = window.screenLeft;
            Ypos     = window.screenTop;
            Pwidth   = document.body.clientWidth;
            Pheight  = document.body.clientHeight;
        } else {
            Xpos     = window.screenX;
            Ypos     = window.screenY;
            Pwidth   = window.innerWidth;
            Pheight  = window.innerHeight;
        }
        url = url + '&is_popup=1';
        screenX = (Xpos + Pwidth/2  - width/2);
        screenY = (Ypos + Pheight/2 - height/2);
        arg = 'width='+width+', height='+height+', scrollbars=no, menubar=no, depend=yes, left='+screenX+', top='+screenY;

        popupwin = window.open(url, name, arg);
        window.popupwin.focus();
     }

     function hpopup(url, name)
     {  
        url = url + '&is_popup=1';
        popupwin = window.open(url, name, 'width=1, height=1');
        window.parent.focus();
     }
     {/literal}

     {UIBROWSER->getAlertMsg assign='alertMsg'}
     {if $alertMsg}
        alert('{$alertMsg}');
     {/if}


</script>
