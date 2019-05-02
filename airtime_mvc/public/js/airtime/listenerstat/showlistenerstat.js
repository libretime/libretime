$(document).ready(function() {
    showlistenerstat_content = $("#showlistenerstat_content")
    dateStartId = "#his_date_start",
        timeStartId = "#his_time_start",
        dateEndId = "#his_date_end",
        timeEndId = "#his_time_end",
        show_id = "#his_show_filter";

    console.log(show_id);
    // set width dynamically
    var width = $("#showlistenerstat_content").width();
    width = width * .91;
    addDatePicker();


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

function addDatePicker() {

    oBaseDatePickerSettings = {
        dateFormat: 'yy-mm-dd',
        //i18n_months, i18n_days_short are in common.js
        monthNames: i18n_months,
        dayNamesMin: i18n_days_short,
        onSelect: function(sDate, oDatePicker) {
            $(this).datepicker( "setDate", sDate );
        }
    };

    oBaseTimePickerSettings = {
        showPeriodLabels: false,
        showCloseButton: true,
        closeButtonText: $.i18n._("Done"),
        showLeadingZero: false,
        defaultTime: '0:00',
        hourText: $.i18n._("Hour"),
        minuteText: $.i18n._("Minute")
    };

    showlistenerstat_content.find(dateStartId).datepicker(oBaseDatePickerSettings);
    showlistenerstat_content.find(timeStartId).timepicker(oBaseTimePickerSettings);
    showlistenerstat_content.find(dateEndId).datepicker(oBaseDatePickerSettings);
    showlistenerstat_content.find(timeEndId).timepicker(oBaseTimePickerSettings);
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