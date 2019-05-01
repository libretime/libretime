$(document).ready(function() {
    showlistenerstat_content = $("#showlistenerstat_content")
    dateStartId = "#sb_date_start",
        timeStartId = "#sb_time_start",
        dateEndId = "#sb_date_end",
        timeEndId = "#sb_time_end",
        show_id = "#sb_show_filter";

    console.log(show_id);
    // set width dynamically
    var width = $("#showlistenerstat_content").width();
    width = width * .91;
    showlistenerstat_content.find("#sb_submit").click(function(){
        var show_id = $("#sb_show_filter").val();
        console.log(show_id);
        var oRange = AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);
        var start = oRange.start;
        var end = oRange.end;
        getShowData(start, end, show_id);
    });
});

function getShowData(startTimestamp, endTimestamp, show_id) {
    // get data
    $.get(baseUrl+'Listenerstat/get-show-data', {start: startTimestamp, end: endTimestamp, show_id: show_id}, function(data) {
        return data;
    });
}

function brokeDataTable() {
    var oRange = AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);
    var start = oRange.start;
    var end = oRange.end;
    var show_id = $("#sb_show_filter").val();
    var dt = $('#show_stats_datatable');
    dt.dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": baseUrl + "Listenerstat/get-show-data",
        "fnServerParams": function (aoData) {
            aoData.push({start: start, end: end, show_id: show_id});
        },
        "fnAddData": function (sSource, aoData, fnCallback) {
            $.ajax({
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback
            });
        },
        "aoColumns": [
            /* first name */ {"sName": "show", "mDataProp": "show"},
            /* air date */   {"sName": "time", "mDataProp": "time"},
            /* last name */  {"sName": "average_number_of_listeners", "mDataProp": "average_number_of_listeners"},
            /* last name */  {"sName": "max_number_of_listeners", "mDataProp": "max_number_of_listeners"},
            /* del button */ {
                "sName": "null as delete",
                "bSearchable": false,
                "bSortable": false,
                "mDataProp": "delete"
            }
        ],
        "bJQueryUI": true,
        "bAutoWidth": false,
        "bLengthChange": false,
        "oLanguage": getDatatablesStrings({
            "sEmptyTable": $.i18n._("No Show Records Found"),
            "sEmptyTable": $.i18n._("No show found"),
            "sZeroRecords": $.i18n._("No show statistics found"),
            "sInfo": $.i18n._("Showing _START_ to _END_ of _TOTAL_ users"),
            "sInfoEmpty": $.i18n._("Showing 0 to 0 of 0 users"),
            "sInfoFiltered": $.i18n._("(filtered from _MAX_ total users)"),
        }),
        "sDom": '<"H"lf<"dt-process-rel"r>><"#user_list_inner_wrapper"t><"F"ip>'
    });
}