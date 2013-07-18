var AIRTIME = (function(AIRTIME) {
    var mod;
    
    if (AIRTIME.history === undefined) {
        AIRTIME.history = {};
    }
    mod = AIRTIME.history;
    
    var $historyContentDiv;
    
    var oTableTools = {
        "sSwfPath": baseUrl+"js/datatables/plugin/TableTools/swf/copy_cvs_xls_pdf.swf",
        "aButtons": [
             {
                 "sExtends": "copy",
                 "fnComplete": function(nButton, oConfig, oFlash, text) {
                     var lines = text.split('\n').length,
                         len = this.s.dt.nTFoot === null ? lines-1 : lines-2,
                         plural = (len==1) ? "" : "s";
                     alert(sprintf($.i18n._('Copied %s row%s to the clipboard'), len, plural));
                 }
             },
             {
                 "sExtends": "csv",
                 "fnClick": setFlashFileName
             },
             {
                 "sExtends": "pdf",
                 "fnClick": setFlashFileName
             },
             {
                 "sExtends": "print",
                 "sInfo" : sprintf($.i18n._("%sPrint view%sPlease use your browser's print function to print this table. Press escape when finished."), "<h6>", "</h6><p>")
             }
         ]
    };
    
    var lengthMenu = [[50, 100, 500, -1], [50, 100, 500, $.i18n._("All")]];
    
    var sDom = 'lf<"dt-process-rel"r><"H"T><"dataTables_scrolling"t><"F"ip>';
    
    function getFileName(ext){
        var filename = $("#his_date_start").val()+"_"+$("#his_time_start").val()+"m--"+$("#his_date_end").val()+"_"+$("#his_time_end").val()+"m";
        filename = filename.replace(/:/g,"h");
        
        if (ext == "pdf"){
            filename = filename+".pdf";
        }
        else {
            filename = filename+".csv";
        }
        return filename;
    }

    function setFlashFileName( nButton, oConfig, oFlash ) {
        var filename = getFileName(oConfig.sExtends);
        oFlash.setFileName( filename );
        
        if (oConfig.sExtends == "pdf") {
            this.fnSetText( oFlash,
                "title:"+ this.fnGetTitle(oConfig) +"\n"+
                "message:"+ oConfig.sPdfMessage +"\n"+
                "colWidth:"+ this.fnCalcColRatios(oConfig) +"\n"+
                "orientation:"+ oConfig.sPdfOrientation +"\n"+
                "size:"+ oConfig.sPdfSize +"\n"+
                "--/TableToolsOpts--\n" +
                this.fnGetTableData(oConfig));
        }
        else {
            this.fnSetText(oFlash, this.fnGetTableData(oConfig));
        }
    }
    
    /* This callback can be used for all history tables */
    function fnServerData( sSource, aoData, fnCallback ) {
    	
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
    }
    
    function aggregateHistoryTable() {
        var oTable,
        	$historyTableDiv = $historyContentDiv.find("#history_table_aggregate"),
        	fnRowCallback;

        fnRowCallback = function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
        	var url = baseUrl+"Playouthistory/edit-aggregate-item/format/json/id/"+aData.file_id,
        		$link = $("<a/>", {
        			"href": url,
        			"text": $.i18n._("Edit")
        		});
        	
        	$('td.his_edit', nRow).html($link);
        };
        
        oTable = $historyTableDiv.dataTable( {
            
            "aoColumns": [
               {"sTitle": $.i18n._("Title"), "mDataProp": "title", "sClass": "his_title"}, /* Title */
               {"sTitle": $.i18n._("Creator"), "mDataProp": "artist", "sClass": "his_artist"}, /* Creator */
               {"sTitle": $.i18n._("Played"), "mDataProp": "played", "sClass": "his_artist"}, /* times played */
               {"sTitle": $.i18n._("Length"), "mDataProp": "length", "sClass": "his_length library_length"}, /* Length */
               {"sTitle": $.i18n._("Composer"), "mDataProp": "composer", "sClass": "his_composer"}, /* Composer */
               {"sTitle": $.i18n._("Copyright"), "mDataProp": "copyright", "sClass": "his_copyright"}, /* Copyright */
               {"sTitle" : $.i18n._("Admin"), "mDataProp": "file_id", "bSearchable" : false, "sClass": "his_edit"}, /* id of history item */
            ],
                          
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": baseUrl+"playouthistory/aggregate-history-feed",
            "sAjaxDataProp": "history",
            "fnServerData": fnServerData,
            "fnRowCallback": fnRowCallback,
            "oLanguage": datatables_dict,
            "aLengthMenu": lengthMenu,
            "iDisplayLength": 50,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": true,
            "sDom": sDom, 
            "oTableTools": oTableTools
        });
        oTable.fnSetFilteringDelay(350);
       
        return oTable;
    }
    
    function itemHistoryTable() {
        var oTable,
        	$historyTableDiv = $historyContentDiv.find("#history_table_list"),
        	fnRowCallback;

        fnRowCallback = function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
        	var url = baseUrl+"playouthistory/edit-list-item/format/json/id/"+aData.history_id,
        		$link = $("<a/>", {
        			"href": url,
        			"text": $.i18n._("Edit")
        		});
        	
        	$('td.his_edit', nRow).html($link);
        };
        
        oTable = $historyTableDiv.dataTable( {
            
            "aoColumns": [
               {"sTitle": $.i18n._("Start"), "mDataProp": "starts", "sClass": "his_starts"}, /* Starts */
               {"sTitle": $.i18n._("End"), "mDataProp": "ends", "sClass": "his_ends"}, /* Ends */
               {"sTitle": $.i18n._("Title"), "mDataProp": "title", "sClass": "his_title"}, /* Title */
               {"sTitle": $.i18n._("Creator"), "mDataProp": "artist", "sClass": "his_artist"}, /* Creator */
               {"sTitle" : $.i18n._("Admin"), "mDataProp": "history_id", "bSearchable" : false, "sClass": "his_edit"}, /* id of history item */
            ],
                          
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": baseUrl+"playouthistory/item-history-feed",
            "sAjaxDataProp": "history",
            "fnServerData": fnServerData,
            "fnRowCallback": fnRowCallback,
            "oLanguage": datatables_dict,
            "aLengthMenu": lengthMenu,
            "iDisplayLength": 50,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": true,
            "sDom": sDom, 
            "oTableTools": oTableTools
        });
        oTable.fnSetFilteringDelay(350);
       
        return oTable;
    }
    
    mod.onReady = function() {
    	
    	var viewport = AIRTIME.utilities.findViewportDimensions(),
    		widgetHeight = viewport.height - 185,
    		screenWidth = Math.floor(viewport.width - 110),
    		oBaseDatePickerSettings,
    		oBaseTimePickerSettings,
    		oTable,
    		dateStartId = "#his_date_start",
    		timeStartId = "#his_time_start",
    		dateEndId = "#his_date_end",
    		timeEndId = "#his_time_end",
    		$hisDialogEl;
    	
    	$historyContentDiv = $("#history_content");
    	
    	function removeHistoryDialog() {
    		$hisDialogEl.dialog("destroy");
        	$hisDialogEl.remove();
    	}
    	
    	function makeHistoryDialog(html) {
    		$hisDialogEl = $(html);
    	     
    		$hisDialogEl.dialog({	       
    	        title: $.i18n._("Edit History Record"),
    	        modal: true,
    	        close: function() {
    	        	removeHistoryDialog();
    	        }
    	    });
    	}
    	
    	/*
         * Icon hover states for search.
         */
    	$historyContentDiv.on("mouseenter", ".his-timerange .ui-button", function(ev) {
        	$(this).addClass("ui-state-hover"); 	
        });
    	$historyContentDiv.on("mouseleave", ".his-timerange .ui-button", function(ev) {
        	$(this).removeClass("ui-state-hover");
        });
    	
    	$historyContentDiv
    		.height(widgetHeight)
    		.width(screenWidth);
    	
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
    	
    	oTable = aggregateHistoryTable();
    	itemHistoryTable();
    	
    	$historyContentDiv.find(dateStartId).datepicker(oBaseDatePickerSettings);
    	$historyContentDiv.find(timeStartId).timepicker(oBaseTimePickerSettings);
    	$historyContentDiv.find(dateEndId).datepicker(oBaseDatePickerSettings);
    	$historyContentDiv.find(timeEndId).timepicker(oBaseTimePickerSettings);
    	
    	$historyContentDiv.on("click", "td.his_edit", function(e) {
    		var url = e.target.href;
    		
    		e.preventDefault();
    		
    		$.get(url, function(json) {
    			
    			makeHistoryDialog(json.dialog);
    			
    		}, "json");
    	});
    	
    	$('body').on("click", ".his_file_save", function(e) {
    		
    		e.preventDefault();
    		
    		var $form = $(this).parents("form");
    		var data = $form.serializeArray();
    		
    		var url = baseUrl+"Playouthistory/update-aggregate-item/format/json";
    		
    		$.post(url, data, function(json) {
    			
    			//TODO put errors on form.
    			if (json.data !== "true") {
    				//makeHistoryDialog(json.dialog);
    			}
    			else {
    				removeHistoryDialog();
    				oTable.fnDraw();
    			}
    		    	
    		}, "json");
    		
    	});
    	
    	$historyContentDiv.find("#his_submit").click(function(ev){
    		var fn,
    			oRange;
    		
    		oRange = AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);
    		
    	    fn = oTable.fnSettings().fnServerData;
    	    fn.start = oRange.start;
    	    fn.end = oRange.end;
    	    
    		oTable.fnDraw();
    	});
    	
    	$historyContentDiv.find("#his-tabs").tabs();
    	
    };
    
return AIRTIME;
    
}(AIRTIME || {}));

$(document).ready(AIRTIME.history.onReady);