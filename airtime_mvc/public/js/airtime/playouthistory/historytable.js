var AIRTIME = (function(AIRTIME) {
    var mod;
    
    if (AIRTIME.history === undefined) {
        AIRTIME.history = {};
    }
    mod = AIRTIME.history;
    
    mod.historyTable = function() {
        var oTable,
        	historyContentDiv = $("#history_content"),
        	historyTableDiv = historyContentDiv.find("#history_table"),
        	tableHeight = historyContentDiv.height() - 200,
        	fnServerData;
        	
        fnServerData = function ( sSource, aoData, fnCallback ) {
        	
        	if (fnServerData.hasOwnProperty("start")) {
    			aoData.push( { name: "start", value: fnServerData.start} );
    		}
    		if (fnServerData.hasOwnProperty("end")) {
    			aoData.push( { name: "end", value: fnServerData.end} );
    		}
           
            aoData.push( { name: "format", value: "json"} );
            
            $.ajax( {
                "dataType": 'json',
                "type": "GET",
                "url": sSource,
                "data": aoData,
                "success": fnCallback
            } );
        };
        
        oTable = historyTableDiv.dataTable( {
            
            "aoColumns": [
               {"sTitle": "Title", "mDataProp": "title", "sClass": "his_title"}, /* Title */
               {"sTitle": "Artist", "mDataProp": "artist", "sClass": "his_artist"}, /* Creator */
               {"sTitle": "Played", "mDataProp": "played", "sClass": "his_artist"}, /* times played */
               {"sTitle": "Length", "mDataProp": "length", "sClass": "his_length library_length"}, /* Length */
               {"sTitle": "Composer", "mDataProp": "composer", "sClass": "his_composer"}, /* Composer */
               {"sTitle": "Copyright", "mDataProp": "copyright", "sClass": "his_copyright"} /* Copyright */
            ],
                          
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "/Playouthistory/playout-history-feed",
            "sAjaxDataProp": "history",
            
            "fnServerData": fnServerData,
            
            "oLanguage": {
                "sSearch": ""
            },
            
            "aLengthMenu": [[50, 100, 500, -1], [50, 100, 500, "All"]],
            "iDisplayLength": 50,
            
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": true,
           
            "sDom": 'lf<"dt-process-rel"r><"H"T><"dataTables_scrolling"t><"F"ip>', 
            
            "oTableTools": {
                "sSwfPath": "/js/datatables/plugin/TableTools/swf/copy_cvs_xls_pdf.swf"
            }
        });
        oTable.fnSetFilteringDelay(350);
        
        historyContentDiv.find(".dataTables_scrolling").css("max-height", tableHeight);
        
        return oTable;
    };
    
return AIRTIME;
    
}(AIRTIME || {}));

$(document).ready(function(){
	
	var viewport = AIRTIME.utilities.findViewportDimensions(),
		history_content = $("#history_content"),
		widgetHeight = viewport.height - 185,
		screenWidth = Math.floor(viewport.width - 110),
		oBaseDatePickerSettings,
		oBaseTimePickerSettings,
		oTable,
		dateStartId = "#his_date_start",
		timeStartId = "#his_time_start",
		dateEndId = "#his_date_end",
		timeEndId = "#his_time_end";
	
	/*
     * Icon hover states for search.
     */
	history_content.on("mouseenter", ".his-timerange .ui-button", function(ev) {
    	$(this).addClass("ui-state-hover"); 	
    });
	history_content.on("mouseleave", ".his-timerange .ui-button", function(ev) {
    	$(this).removeClass("ui-state-hover");
    });
	
	history_content
		.height(widgetHeight)
		.width(screenWidth);
	
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
	
	oTable = AIRTIME.history.historyTable();
	
	history_content.find(dateStartId).datepicker(oBaseDatePickerSettings);
	history_content.find(timeStartId).timepicker(oBaseTimePickerSettings);
	history_content.find(dateEndId).datepicker(oBaseDatePickerSettings);
	history_content.find(timeEndId).timepicker(oBaseTimePickerSettings);
	
	
	history_content.find("#his_submit").click(function(ev){
		var fn,
			oRange;
		
		oRange = AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);
		
	    fn = oTable.fnSettings().fnServerData;
	    fn.start = oRange.start;
	    fn.end = oRange.end;
	    
		oTable.fnDraw();
	});
	
});