<script type='text/javascript'>
{literal}

function SearchForm_displayRow(row)
{
    document.getElementById('searchRow_' + row).style.display = 'inline';
}

function SearchForm_addRow()
{
    if (document.forms['search'].elements['counter'].value < document.forms['search'].elements['max_rows'].value) {
        document.forms['search'].elements['counter'].value++;
        SearchForm_displayRow(document.forms['search'].elements['counter'].value);
        return true;
    } else {
        alert('Maximum reached');
        return false;
    }
}


function SearchForm_hideRow(row)
{
    document.getElementById('searchRow_' + row).style.display = 'none';
    document.forms['search'].elements['row_' + Number(row) + '[0]'].options[0].selected = true;
    document.forms['search'].elements['row_' + Number(row) + '[1]'].options[0].selected = true;
    document.forms['search'].elements['row_' + Number(row) + '[2]'].value = '';
}

function SearchForm_dropRow(row)
{
    if (document.forms['search'].elements['counter'].value <= 1)
        return false;
    var n;
    for (n = row; n < document.forms['search'].elements['counter'].value; n++) {
        document.forms['search'].elements['row_' + Number(n) + '[0]'].value = document.forms['search'].elements['row_' + (Number(n)+1) + '[0]'].value;
        document.forms['search'].elements['row_' + Number(n) + '[1]'].value = document.forms['search'].elements['row_' + (Number(n)+1) + '[1]'].value;
        document.forms['search'].elements['row_' + Number(n) + '[2]'].value = document.forms['search'].elements['row_' + (Number(n)+1) + '[2]'].value;
    }
    document.forms['search'].elements['counter'].value--;
    SearchForm_hideRow(Number(n));

}
{/literal}
</script>

