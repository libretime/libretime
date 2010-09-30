<script language="javascript">
{literal}
function showMain()
{
    document.getElementById('div_Data').style.display   = 'none';
    document.getElementById('div_MData').style.display  = 'block';
    document.getElementById('div_Main').style.display   = 'inline';
    document.getElementById('div_Music').style.display  = 'none';
    document.getElementById('div_Voice').style.display   = 'none';
    document.getElementById('switch_Main').className    = 'active';
    document.getElementById('switch_Music').className   = '';
    document.getElementById('switch_Voice').className    = '';
    document.getElementById('switch_Data').className    = '';
}
function showMusic()
{
    document.getElementById('div_Data').style.display   = 'none';
    document.getElementById('div_MData').style.display  = 'block';
    document.getElementById('div_Main').style.display   = 'none';
    document.getElementById('div_Music').style.display  = 'inline';
    document.getElementById('div_Voice').style.display   = 'none';
    document.getElementById('switch_Main').className    = '';
    document.getElementById('switch_Music').className   = 'active';
    document.getElementById('switch_Voice').className    = '';
    document.getElementById('switch_Data').className    = '';
}
function showVoice()
{
    document.getElementById('div_Data').style.display   = 'none';
    document.getElementById('div_MData').style.display  = 'block';
    document.getElementById('div_Main').style.display   = 'none';
    document.getElementById('div_Music').style.display  = 'none';
    document.getElementById('div_Voice').style.display   = 'inline';
    document.getElementById('switch_Main').className    = '';
    document.getElementById('switch_Music').className   = '';
    document.getElementById('switch_Voice').className    = 'active';
    document.getElementById('switch_Data').className    = '';
}


function showData()
{
    document.getElementById('div_Data').style.display   = 'block';
    document.getElementById('div_MData').style.display  = 'none';
    document.getElementById('switch_Main').className    = '';
    document.getElementById('switch_Music').className   = '';
    document.getElementById('switch_Voice').className    = '';
    document.getElementById('switch_Data').className    = 'active';
}

function showMData()
{
    document.getElementById('div_MData').style.display  = 'block';
    document.getElementById('div_Data').style.display   = 'none';
}
{/literal}
</script>

<div id="tabnav">
    <ul>
        <li><a href="#" onClick="javascript:showMain();"  id="switch_Main">##Main##</a></li>
        <li><a href="#" onClick="javascript:showMusic();" id="switch_Music">##Music##</a></li>
        <li><a href="#" onClick="javascript:showVoice();"  id="switch_Voice">##Voice##</a></li>
        {if $editItem.type == 'webstream' && $editItem.id}
            <li><a href="#" onClick="javascript:showData();"  id="switch_Data">##Data##</a></li>
        {else}
            <div id="switch_Data"></div>
        {/if}
    </ul>
</div>


