$(document).ready(function() {
    listenerstat_content = $("#listenerstat_content")
    dateStartId = "#his_date_start",
    timeStartId = "#his_time_start",
    dateEndId = "#his_date_end",
    timeEndId = "#his_time_end";
    
    getDataAndPlot();
    
    listenerstat_content.find("#his_submit").click(function(){
        startTimestamp = AIRTIME.utilities.fnGetTimestamp(dateStartId, timeStartId);
        endTimestamp = AIRTIME.utilities.fnGetTimestamp(dateEndId, timeEndId);
        getDataAndPlot(startTimestamp, endTimestamp);
    });
});

function getDataAndPlot(startTimestamp, endTimestamp){
    // get data
    $.get('/Listenerstat/get-data', {startTimestamp: startTimestamp, endTimestamp: endTimestamp}, function(data){
        data = JSON.parse(data);
        out = new Object();
        $.each(data, function(mpName, v){
            plotData = new Object();
            plotData.data = new Array();
            $.each(v, function(i, ele){
                plotData.label = mpName;
                var d = new Date(0);
                d.setUTCSeconds(ele.timestamp);
                plotData.data.push([d, ele.listener_count]);
            })
            out[mpName] = plotData;
        });
        plot(out);
    })
}

function plot(datasets){
    data = null;
    function plotByChoice(doAll)
    {
        // largest date object that you can set
        firstTimestamp = new Date(8640000000000000);
        // smallest
        lastTimestamp = new Date(0);
        
        data = [];
        if (doAll != null)
        {
            $.each(datasets, function(key, val) {
                if (firstTimestamp.getTime() > val.data[0][0].getTime()) {
                    firstTimestamp = val.data[0][0];
                }
                if (lastTimestamp.getTime() < val.data[val.data.length-1][0].getTime()) {
                    lastTimestamp = val.data[val.data.length-1][0];
                }
                data.push(val);
            });
        }   
        else
        {
            $('#legend .legendCB').each(
                function(){
                    if (this.checked)
                    {         
                         data.push(datasets[this.id]);
                         if (firstTimestamp.getTime() > datasets[this.id].data[0][0].getTime()) {
                             firstTimestamp = datasets[this.id].data[0][0];
                         }
                         if (lastTimestamp.getTime() < datasets[this.id].data[datasets[this.id].data.length-1][0].getTime()) {
                             lastTimestamp = datasets[this.id].data[datasets[this.id].data.length-1][0];
                         }
                    }
                    else
                    {
                         data.push({label: this.id, data: []})
                    }
                }
            );
        }
        
        numOfTicks = 10;
        tickSize = (lastTimestamp.getTime() - firstTimestamp.getTime())/1000/numOfTicks;
        
        $.plot($("#flot_placeholder"), data, {
            yaxis: { min: 0, tickDecimals: 0 },
            xaxis: { mode: "time", timeformat:"%y/%m/%0d %H:%M", tickSize: [tickSize, "second"] },
            legend: {
                container: $('#legend'),
                noColumns: 5,
                labelFormatter: function (label, series) {
                    var cb = '<input style="float:left;" class="legendCB" type="checkbox" ';
                    if (series.data.length > 0){
                        cb += 'checked="true" ';
                    }
                    cb += 'id="'+label+'" /> ';
                    cb += label;
                    return cb;
                 }
            }
        });
        
        $('#legend').find("input").click(function(){setTimeout(plotByChoice,100);});
    }
    
    plotByChoice(true);  
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
}