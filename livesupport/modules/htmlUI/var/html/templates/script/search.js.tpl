<script type='text/javascript'>
{literal}

function displayRow(row)
{
    document.getElementById('searchRow_' + row).style.visibility = 'visible';
    document.getElementById('searchRow_' + row).style.height     = '30px';
}

function addRow()
{
    if (document.forms['search'].elements['counter'].value < document.forms['search'].elements['max_rows'].value) {
        document.forms['search'].elements['counter'].value++;
        displayRow(document.forms['search'].elements['counter'].value);
        return true;
    } else {
        alert('Maximum reached');
        return false;
    }
}


function hideRow(row)
{
    document.getElementById('searchRow_' + row).style.visibility = 'hidden';
    document.getElementById('searchRow_' + row).style.height     = '0px';
    document.forms['search'].elements['row_' + Number(row) + '[0]'].value = '';
    document.forms['search'].elements['row_' + Number(row) + '[1]'].value = '';
    document.forms['search'].elements['row_' + Number(row) + '[2]'].value = '';
}

function dropRow(row)
{
    var n;
    for (n=row; n<document.forms['search'].elements['counter'].value; n++) {
        document.forms['search'].elements['row_' + Number(n) + '[0]'].value = document.forms['search'].elements['row_' + (Number(n)+1) + '[0]'].value;
        document.forms['search'].elements['row_' + Number(n) + '[1]'].value = document.forms['search'].elements['row_' + (Number(n)+1) + '[1]'].value;
        document.forms['search'].elements['row_' + Number(n) + '[2]'].value = document.forms['search'].elements['row_' + (Number(n)+1) + '[2]'].value;
    }
    document.forms['search'].elements['counter'].value--;
    hideRow(Number(n));

}

{/literal}
</script>

