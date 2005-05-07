    <!-- start nav -->
        <div class="container_nav">
            <ul id="nav">
                <li><a href="{$UI_BROWSER}?folderId={$START.fid}&act=editFile">##Add Audio##</a>
                    <ul>
                        <li><a href="{$UI_BROWSER}?folderId={$START.fid}&act=editFile">##File##</a></li>
                        <li><a href="{$UI_BROWSER}?folderId={$START.fid}&act=editWebstream">##Stream##</a></li>
                    </ul>
                </li>
                <li><a href="{$UI_BROWSER}?id={$START.id}&act=BROWSE">##Media Library##</a>
                    <ul>
                        <li><a href="{$UI_BROWSER}?id={$START.id}&act=BROWSE">##Browse##</a></li>
                        <li><a href="{$UI_BROWSER}?id={$START.id}&act=SEARCH">##Search##</a></li>
                    </ul>
                </li>
                <li><a href="{$UI_BROWSER}?id={$START.fid}&act=PL.simpleManagement">##Playlist Editor##</a></li>
                <li><a href="{$UI_BROWSER}?act=SCHEDULER">##Scheduler##</a></li>

                {if $SUBJECTS->Base->gb->checkPerm($SUBJECTS->Base->userid, 'subjects')}
                <li><a href="{$UI_BROWSER}?act=changeStationPrefs">##Station Settings##</a>
                    <ul>
                        <li><a href="{$UI_BROWSER}?act=changeStationPrefs">##Station Settings##</a></li>
                        <li><a href="{$UI_BROWSER}?act=SUBJECTS">##User/Groups##</a></li>
                        <li><a href="{$UI_BROWSER}?act=fileList&id={$START.fid}">##File List##</a></li>
                    </ul>
                </li>
                {/if}
                <li><a href="#" onClick="window.open('help.html','','scrollbars=yes,resizable=yes,width=500,height=400')">##Help##</a></li>
            </ul>
        </div>
    <!-- end nav -->
