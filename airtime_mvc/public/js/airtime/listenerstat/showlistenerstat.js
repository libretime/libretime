$(document).ready(function() {
    showlistenerstat_content = $("#showlistenerstat_content")
    dateStartId = "#his_date_start",
        timeStartId = "#his_time_start",
        dateEndId = "#his_date_end",
        timeEndId = "#his_time_end",
        show_id = "#his_show_filter";

    // set width dynamically
    var width = $("#showlistenerstat_content").width();
    width = width * .91;
    addDatePicker();

    showlistenerstat_content.find("#his_submit").click(function(){
//        var show_id = $("#sb_show_filter").val();
        var oRange = AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);
        var start = oRange.start;
        var end = oRange.end;
        brokeDataTable();
    });
});

function getShowData(startTimestamp, endTimestamp, show_id) {
    // get data
    $.get(baseUrl+'Listenerstat/get-all-show-data', {start: startTimestamp, end: endTimestamp }, function(data) {
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

function getStartEnd() {

    return AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);
}


function showSummaryList(start, end) {
    var url = baseUrl+"playouthistory/show-history-feed",
        data = {
            format: "json",
            start: start,
            end: end
        };

    $.post(url, data, function(json) {
        drawShowList(json);
    });
}


function brokeDataTable() {
    var oRange = AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);
    var start = oRange.start;
    var end = oRange.end;
    var show_id = $("#sb_show_filter").val();
    var dt = $('#show_stats_datatable');
    info = getStartEnd();
    dt.dataTable({
        "aoColumns": [
            /* first name */ {"sName": "show", "mDataProp": "show"},
            /* air date */   {"sName": "time", "mDataProp": "time"},
            /* last name */  {"sName": "average_number_of_listeners", "mDataProp": "average_number_of_listeners"},
            /* last name */  {"sName": "maximum_number_of_listeners", "mDataProp": "maximum_number_of_listeners"}],
        "sAjaxSource":         baseUrl+'Listenerstat/get-all-show-data',
        "sAjaxDataProp": "",
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            aoData.push({"start": start, "end": end});
          $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": {"start": start, "end": end},
                "success": fnCallback
            } );
        },
    });
}