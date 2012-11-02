$(document).ready(function() {
    listenerstat_content = $("#listenerstat_content")
    dateStartId = "#his_date_start",
    timeStartId = "#his_time_start",
    dateEndId = "#his_date_end",
    timeEndId = "#his_time_end";
    
    getDataAndPlot();
    
    listenerstat_content.find("#his_submit").click(function(ev){
        startTimestamp = AIRTIME.utilities.fnGetTimestamp(dateStartId, timeStartId);
        endTimestamp = AIRTIME.utilities.fnGetTimestamp(dateEndId, timeEndId);
        getDataAndPlot(startTimestamp, endTimestamp);
    });
});

function getDataAndPlot(startTimestamp, endTimestamp){
    // get data
    $.get('/Listenerstat/get-data', {startTimestamp: startTimestamp, endTimestamp: endTimestamp}, function(data){
        data = JSON.parse(data);
        out = new Array();
        $.each(data, function(index, v){
            temp = new Array();
            temp[0] = new Date(v.timestamp.replace(/-/g,"/"));
            temp[1] = v.listener_count;
            out.push(temp);
        });
        plot(out);
    })
}

function plot(d){
    oBaseDatePickerSettings = {
        dateFormat: 'yy-mm-dd',
        onSelect: function(sDate, oDatePicker) {        
            $(this).datepicker( "setDate", sDate );
        }
    };
    
    oBaseTimePickerSettings = {
        showPeriodLabels: false,
        showCloseButton: true,
        showLeadingZero: false,
        defaultTime: '0:00'
    };
    
    listenerstat_content.find(dateStartId).datepicker(oBaseDatePickerSettings);
    listenerstat_content.find(timeStartId).timepicker(oBaseTimePickerSettings);
    listenerstat_content.find(dateEndId).datepicker(oBaseDatePickerSettings);
    listenerstat_content.find(timeEndId).timepicker(oBaseTimePickerSettings);
    
    $.plot($("#flot_placeholder"), [d], { xaxis: { mode: "time", timeformat: "%y/%m/%0d %H:%M:%S" } });

    $("#whole").click(function () {
        $.plot($("#flot_placeholder"), [d], { xaxis: { mode: "time" } });
    });

    }