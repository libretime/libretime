{assign var="_gname" value=$SUBJECTS->Base->gb->getSubjName($_REQUEST.id)}

<div class="container_elements" style="width: 607px;">
    <h1>{tra 0='Manage Group: $1' 1=$_gname}</h1>

    <div class="container_table" style="width: 594px;">

        <!-- start current group member -->
        <div class="container_elements" style="float: left">
            <h1>##Current Members##</h1>
            <div class="head" style="width:255px; height: 21px;">&nbsp;</div>
            <div class="container_table" style="width:275px;">
                <table style="width:255px;">
                    <form name="GRP">
                    <tr class="blue_head">
                        <td style="width: 30px"><input type="checkbox" name="all" onClick="collector_switchAll('GRP')"></td>
                        <td style="width: 164px">##Login##</td>
                        <td style="width: 41px; border: 0; text-align: center">##Type##</td>
                    </tr>

                    {assign var="_member" value=$SUBJECTS->getGroupMember($_REQUEST.id)}
                    {if (is_array($_member) && count($_member)>0)}
                        {foreach from=$_member item="i"}
                            <tr class="{cycle values='blue1, blue2'}">
                                <td><input type="checkbox" class="checkbox" name="{$i.id}"/></td>
                                <td onClick="return contextmenu('login={urlencode str=$i.login}&gname={urlencode str=$_gname}', 'SUBJECTS.removeSubjFromGr')">{$i.login}</td>
                                <td style="border: 0; text-align: center"
                                    onClick="return contextmenu('login={urlencode str=$i.login}&gname={urlencode str=$_gname}', 'SUBJECTS.removeSubjFromGr')">
                                    {if $i.type|lower == 'u'}
                                        <img src="img/user.gif" border="0" alt="User" />
                                    {else}
                                        <img src="img/group.gif" border="0" alt="Group" />
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr><td colspan="3" align="center" style="border: 0">##No group members##</td></tr>
                    {/if}
                    </form>
                </table>
            </div>
            <div class="footer" style="width:250px;">
                <a href="" onClick="collector_submit('GRP', 'SUBJECTS.removeSubjFromGr&gname={urlencode str=$_gname}')" id="blue_head">##Remove selected##</a>
            </div>
        </div>
        <!-- end current group member -->



        <!-- start add group member -->
        <div class="container_elements" style="float: right">
            <h1>##Add Members##</h1>
            <div class="head" style="width:255px; height: 21px;">&nbsp;</div>
            <div class="container_table" style="width:275px;">
                <table style="width:255px;">
                <form name="NOGRP">
                <!-- start table header -->
                    <tr class="blue_head">
                        <td style="width: 30px"><input type="checkbox" name="all" onClick="collector_switchAll('NOGRP')"></td>
                        <td style="width: 164px">##Login##</td>
                        <td style="width: 41px; border: 0">##Type##</td>
                    </tr>
                <!-- end table header -->

                    {assign var="_nonmember" value=$SUBJECTS->getNonGroupMember($_REQUEST.id)}
                    {foreach from=$_nonmember item=i}
                        {if $i.login !== $_gname}
                            {assign var="_loop" value=true}
                            <!-- start item -->
                            <tr class="{cycle values='blue1, blue2'}">
                                <td><input type="checkbox" class="checkbox" name="{$i.id}"/></td>
                                <td onClick="return contextmenu('login={urlencode str=$i.login}&gname={urlencode str=$_gname}', 'SUBJECTS.addSubj2Gr')">{$i.login}</td>
                                <td style="border: 0; text-align: center"
                                    onClick="return contextmenu('login={urlencode str=$i.login}&gname={urlencode str=$_gname}', 'SUBJECTS.removeSubjFromGr')">
                                    {if $i.type|lower == 'u'}
                                        <img src="img/user.gif" border="0" alt="User" />
                                    {else}
                                        <img src="img/group.gif" border="0" alt="Group" />
                                    {/if}
                                </td>
                            </tr>
                            <!-- end item -->
                        {/if}
                    {/foreach}

                    {if $_loop != true}
                        <tr><td colspan="3" align="center" style="border: 0">##Nothing left##</td></tr>
                    {/if}
                </form>
                </table>
            </div>
            <div class="footer" style="width:250px;">
                <a href="" onClick="collector_submit('NOGRP', 'SUBJECTS.addSubj2Gr&gname={urlencode str=$_gname}')" id="blue_head">##Add selected##</a>
            </div>
        </div>
        <!-- end add group member -->
    </div>

    <input type="button" class="button_wide" value="##Back to overview##" onClick="location.href='{$UI_BROWSER}?act=SUBJECTS'">
</div>

