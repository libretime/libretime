$(document).ready(function() {
    $('#users_datatable').dataTable( {
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "/User/get-user-data-table-info/format/json",
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json', 
                "type": "POST", 
                "url": sSource, 
                "data": aoData, 
                "success": fnCallback
            } );
        },
        "aoColumns": [
            /* Id */         { "sName": "id", "bSearchable": false, "bVisible": false },
            /* user name */  { "sName": "login" },
            /* user type */  { "sName": "type", "bSearchable": false },
            /* del button */ { "sName": "first_name", "bSearchable": false, "bSortable": false}
        ],
        "bJQueryUI": true,
        "bAutoWidth": false,
        "bLengthChange": false,
        //"bFilter": false
    });
});
