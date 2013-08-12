var AIRTIME = (function(AIRTIME) {
    var mod;
    
    if (AIRTIME.history === undefined) {
        AIRTIME.history = {};
    }
    mod = AIRTIME.history;
    
    var $historyContentDiv;
    
    var oTableTools = {
        "sSwfPath": baseUrl+"js/datatables/plugin/TableTools-2.1.5/swf/copy_csv_xls_pdf.swf",
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
    
    var lengthMenu = [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, $.i18n._("All")]];
    
    var sDom = 'l<"dt-process-rel"r><"H"T><"dataTables_scrolling"t><"F"ip>';
    
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
                //"title:"+ this.fnGetTitle(oConfig) +"\n"+
            	"title: Testing the Title Out\n"+
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
        	var editUrl = baseUrl+"Playouthistory/edit-file-item/format/json/id/"+aData.file_id;
        		
        	nRow.setAttribute('url-edit', editUrl);
        };
        
        var columns = JSON.parse(localStorage.getItem('datatables-historyfile-aoColumns'));
        
        oTable = $historyTableDiv.dataTable( {
            
            "aoColumns": columns,
                          
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": baseUrl+"playouthistory/file-history-feed",
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
        
        var columns = JSON.parse(localStorage.getItem('datatables-historyitem-aoColumns'));

        fnRowCallback = function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
        	var editUrl = baseUrl+"playouthistory/edit-list-item/format/json/id/"+aData.history_id,
        		deleteUrl = baseUrl+"playouthistory/delete-list-item/format/json/id/"+aData.history_id;
	
        	nRow.setAttribute('url-edit', editUrl);
        	nRow.setAttribute('url-delete', deleteUrl);
        };
        
        oTable = $historyTableDiv.dataTable( {
            
            "aoColumns": columns,             
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
    		oTableAgg,
    		oTableItem,
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
    	        open: function( event, ui ) {
    	        	$hisDialogEl.find('.date').datetimepicker({
    	        		"pick12HourFormat": false
    	        	});
    	        },
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
    	
    	oTableItem = itemHistoryTable();
    	oTableAgg = aggregateHistoryTable();
    	
    	$historyContentDiv.find(dateStartId).datepicker(oBaseDatePickerSettings);
    	$historyContentDiv.find(timeStartId).timepicker(oBaseTimePickerSettings);
    	$historyContentDiv.find(dateEndId).datepicker(oBaseDatePickerSettings);
    	$historyContentDiv.find(timeEndId).timepicker(oBaseTimePickerSettings);
    	
    	// 'open' an information row when a row is clicked on
    	//for create/edit/delete
    	function openRow(oTable, tr) {
    		var links = ['url-edit', 'url-delete'],
    			i, len,
    			attr, 
    			name,
    			$link,
    			$div;
    		
    		$div = $("<div/>");
    		
    		for (i = 0, len = links.length; i < len; i++) {
    			
    			attr = links[i];
    			
    			if (tr.hasAttribute(attr)) {
    				name = attr.split("-")[1];
    				
    				$link = $("<a/>", {
    	    			"href": tr.getAttribute(attr),
    	    			"text": $.i18n._(name),
    	    			"class": "his_"+name
    	    		});
    				
    				$div.append($link);
    			}
    		}
    		
    		if (oTable.fnIsOpen(tr)) {
    		    oTable.fnClose(tr);
    		} 
    		else {
    		    oTable.fnOpen(tr, $div, "his_update");
    		} 
    	}
    	
    	$historyContentDiv.on("click", "#history_table_list tr", function(ev) {
    		openRow(oTableItem, this);
        });
    	
    	$historyContentDiv.on("click", "#history_table_aggregate tr", function(ev) {
    		openRow(oTableAgg, this);
        });
    	
    	$("#his_create").click(function(e) {
    		var url = baseUrl+"playouthistory/edit-list-item/format/json"	;
    		
    		e.preventDefault();
    		
    		$.get(url, function(json) {
    			
    			makeHistoryDialog(json.dialog);
    			
    		}, "json");
    	});
	    	
    	$historyContentDiv.on("click", "a.his_edit", function(e) {
    		var url = e.target.href;
    		
    		e.preventDefault();
    		
    		$.get(url, function(json) {
    			
    			makeHistoryDialog(json.dialog);
    			
    		}, "json");
    	});
    	
    	$historyContentDiv.on("click", "a.his_delete", function(e) {
    		var url = e.target.href,
    			doDelete;
    		
    		e.preventDefault();
    		
    		doDelete = confirm($.i18n._("Delete this history record?"));
    		
    		if (doDelete) {
    			$.post(url, function(json) {
    				oTableAgg.fnDraw();
    				oTableItem.fnDraw();
        			
        		}, "json");
    		}
    	});
    	
    	$('body').on("click", ".his_file_cancel, .his_item_cancel", function(e) {
    		removeHistoryDialog();
    	});
    	
    	$('body').on("click", ".his_file_save", function(e) {
    		
    		e.preventDefault();
    		
    		var $form = $(this).parents("form");
    		var data = $form.serializeArray();
    		
    		var url = baseUrl+"Playouthistory/update-file-item/format/json";
    		
    		$.post(url, data, function(json) {
    			
    			//TODO put errors on form.
    			if (json.error !== undefined) {
    				//makeHistoryDialog(json.dialog);
    			}
    			else {
    				removeHistoryDialog();
    				oTableAgg.fnDraw();
    			}
    		    	
    		}, "json");
    		
    	});
    	
    	$('body').on("click", ".his_item_save", function(e) {
    		
    		e.preventDefault();
    		
    		var $form = $(this).parents("form"),
    			data = $form.serializeArray(),
    			id = data[0].value,
    			createUrl = baseUrl+"Playouthistory/create-list-item/format/json",
    			updateUrl = baseUrl+"Playouthistory/update-list-item/format/json",
    			url;
    		
    		url = (id === "") ? createUrl : updateUrl;
    				
    		$.post(url, data, function(json) {
    			
    			//TODO put errors on form.
    			if (json.error !== undefined) {
    				
    			}
    			else {
    				removeHistoryDialog();
    				oTableItem.fnDraw();
    			}
    		    	
    		}, "json");
    		
    	});	
    	
    	$historyContentDiv.find("#his_submit").click(function(ev){
    		var fn,
    			oRange;
    		
    		oRange = AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);
    		
    		fn = fnServerData;
    	    fn.start = oRange.start;
    	    fn.end = oRange.end;
    	    
    		oTableAgg.fnDraw();
    		oTableItem.fnDraw();
    	});
    	
    	$historyContentDiv.find("#his-tabs").tabs();
    	
    };
    
return AIRTIME;
    
}(AIRTIME || {}));

$(document).ready(AIRTIME.history.onReady);