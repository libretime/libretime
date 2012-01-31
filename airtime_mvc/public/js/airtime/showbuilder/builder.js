$(document).ready(function() {
	var tableDiv = $('#show_builder_table'),
		oTable,
		oBaseDatePickerSettings,
		oBaseTimePickerSettings,
		fnAddSelectedItems,
		fnRemoveSelectedItems;
	
	oBaseDatePickerSettings = {
		dateFormat: 'yy-mm-dd',
		onSelect: function(sDate, oDatePicker) {
			var oDate,
				dInput;
			
			dInput = $(this);			
			oDate = dInput.datepicker( "setDate", sDate );
		}
	};
	
	oBaseTimePickerSettings = {
		showPeriodLabels: false,
		showCloseButton: true,
		showLeadingZero: false,
		defaultTime: '0:00'
	};
	
	/*
	 * Get the schedule range start in unix timestamp form (in seconds).
	 * defaults to NOW if nothing is selected.
	 * 
	 * @param String sDatePickerId
	 * 
	 * @param String sTimePickerId
	 * 
	 * @return Number iTime
	 */
	function fnGetUIPickerUnixTimestamp(sDatePickerId, sTimePickerId) {
		var oDate, 
			oTimePicker = $( sTimePickerId ),
			iTime,
			iHour,
			iMin,
			iServerOffset,
			iClientOffset;
		
		oDate = $( sDatePickerId ).datepicker( "getDate" );
		
		//nothing has been selected from this datepicker.
		if (oDate === null) {
			oDate = new Date();
		}
		else {
			iHour = oTimePicker.timepicker('getHour');
			iMin = oTimePicker.timepicker('getMinute');
			
			oDate.setHours(iHour, iMin);
		}
		
		iTime = oDate.getTime(); //value is in millisec.
		iTime = Math.round(iTime / 1000);
		iServerOffset = serverTimezoneOffset;
		iClientOffset = oDate.getTimezoneOffset() * 60;//function returns minutes
		
		//adjust for the fact the the Date object is
		iTime = iTime + iServerOffset + iClientOffset;
		
		return iTime;
	}
	/*
	 * Returns an object containing a unix timestamp in seconds for the start/end range
	 * 
	 * @return Object {"start", "end", "range"}
	 */
	function fnGetScheduleRange() {
		var iStart, 
			iEnd, 
			iRange,
			MIN_RANGE = 60*60*24;
		
		iStart = fnGetUIPickerUnixTimestamp("#show_builder_datepicker_start", "#show_builder_timepicker_start");
		iEnd = fnGetUIPickerUnixTimestamp("#show_builder_datepicker_end", "#show_builder_timepicker_end");
		
		iRange = iEnd - iStart;
		
		//return min range
		if (iRange < MIN_RANGE){
			iEnd = iStart + MIN_RANGE;
			iRange = MIN_RANGE;
		}
			
		return {
			start: iStart,
			end: iEnd,
			range: iRange
		};
	}

	var fnServerData = function ( sSource, aoData, fnCallback ) {
		aoData.push( { name: "format", value: "json"} );
		
		if (fnServerData.hasOwnProperty("start")) {
			aoData.push( { name: "start", value: fnServerData.start} );
		}
		if (fnServerData.hasOwnProperty("end")) {
			aoData.push( { name: "end", value: fnServerData.end} );
		}
		
		$.ajax( {
			"dataType": "json",
			"type": "GET",
			"url": sSource,
			"data": aoData,
			"success": fnCallback
		} );
	};

	var fnShowBuilderRowCallback = function ( nRow, aData, iDisplayIndex, iDisplayIndexFull ){
		var i,
			sSeparatorHTML,
			fnPrepareSeparatorRow,
			node;
		
		//save some info for reordering purposes.
		$(nRow).data({aData: aData});
		
		fnPrepareSeparatorRow = function(sRowContent, sClass) {
			
			node = nRow.children[1];
			node.innerHTML = sRowContent;
			node.setAttribute('colspan',100);
			for (i = 2; i < nRow.children.length; i = i+1) {
				node = nRow.children[i];
				node.innerHTML = "";
				node.setAttribute("style", "display : none");
			}
			
			nRow.className = sClass;
		};
		
		if (aData.header === true) {
			node = nRow.children[0];
			node.innerHTML = '<span class="ui-icon ui-icon-play"></span>';
			
			sSeparatorHTML = '<span>'+aData.title+'</span><span>'+aData.starts+'</span><span>'+aData.ends+'</span>';
			fnPrepareSeparatorRow(sSeparatorHTML, "show-builder-header");
		}
		else if (aData.footer === true) {
			
			node = nRow.children[0];
			node.innerHTML = '<span class="ui-icon ui-icon-check"></span>';
				
			sSeparatorHTML = '<span>Show Footer</span>';
			fnPrepareSeparatorRow(sSeparatorHTML, "show-builder-footer");
		}
		else if (aData.empty === true) {
			
		}
		else {
			$(nRow).attr("id", "sched_"+aData.id);
			
			node = nRow.children[0];
			if (aData.checkbox === true) {
				node.innerHTML = '<input type="checkbox" name="'+aData.id+'"></input>';
			}
			else {
				node.innerHTML = '';
				
				$(nRow).addClass("show-builder-not-allowed");
			}
		}
		
		return nRow;
	};

	fnRemoveSelectedItems = function() {
		var oTT = TableTools.fnGetInstance('show_builder_table'),
			aData = oTT.fnGetSelectedData(),
			item,
			temp,
			ids = [];
	
		for (item in aData) {
			temp = aData[item];
			if (temp !== null && temp.hasOwnProperty('id')) {
				ids.push(temp.id);
			} 	
		}
		
		$.post( "/showbuilder/schedule-remove",
			{"ids": ids, "format": "json"},
			function(data) {
				var x;
			});
	};
	
	oTable = tableDiv.dataTable( {
		"aoColumns": [
		    /* checkbox */ {"mDataProp": "checkbox", "sTitle": "<input type='checkbox' name='sb_cb_all'>", "sWidth": "15px"},
		   // /* scheduled id */{"mDataProp": "id", "sTitle": "id"},
		   // /* instance */{"mDataProp": "instance", "sTitle": "si_id"},
            /* starts */{"mDataProp": "starts", "sTitle": "Airtime"},
            /* ends */{"mDataProp": "ends", "sTitle": "Off Air"},
            /* runtime */{"mDataProp": "runtime", "sTitle": "Runtime"},
            /* title */{"mDataProp": "title", "sTitle": "Title"},
            /* creator */{"mDataProp": "creator", "sTitle": "Creator"},
            /* album */{"mDataProp": "album", "sTitle": "Album"}
        ],
        
        "asStripClasses": [ 'odd' ],
        
        "bJQueryUI": true,
        "bSort": false,
        "bFilter": false,
        "bProcessing": true,
		"bServerSide": true,
		"bInfo": false,
		"bAutoWidth": false,
        
		"fnServerData": fnServerData,
		"fnRowCallback": fnShowBuilderRowCallback,
		
		"oColVis": {
			"aiExclude": [ 0, 1 ]
		},
		
		"oTableTools": {
        	"sRowSelect": "multi",
			"aButtons": [],
			"fnRowSelected": function ( node ) {
                var x;
                        
                //seems to happen if everything is selected
                if ( node === null) {
                	oTable.find("input[type=checkbox]").attr("checked", true);
                }
                else {
                	$(node).find("input[type=checkbox]").attr("checked", true);
                }
            },
            "fnRowDeselected": function ( node ) {
                var x;
                
              //seems to happen if everything is deselected
                if ( node === null) {
                	oTable.find("input[type=checkbox]").attr("checked", false);
                }
                else {
                	$(node).find("input[type=checkbox]").attr("checked", false);
                }
            }
		},
		
        // R = ColReorder, C = ColVis, see datatables doc for others
        "sDom": 'Rr<"H"CT<"#show_builder_toolbar">>t<"F">',
        
        //options for infinite scrolling
        //"bScrollInfinite": true,
        //"bScrollCollapse": true,
        //"sScrollY": "400px",
        
        "sAjaxDataProp": "schedule",
		"sAjaxSource": "/showbuilder/builder-feed"
		
	});
	
	$('[name="sb_cb_all"]').click(function(){
    	var oTT = TableTools.fnGetInstance('show_builder_table');
    	
    	if ($(this).is(":checked")) {
    		oTT.fnSelectAll();
    	}
    	else {
    		oTT.fnSelectNone();
    	}       
    });
	
	$( "#show_builder_datepicker_start" ).datepicker(oBaseDatePickerSettings);
	
	$( "#show_builder_timepicker_start" ).timepicker(oBaseTimePickerSettings);
	
	$( "#show_builder_datepicker_end" ).datepicker(oBaseDatePickerSettings);
	
	$( "#show_builder_timepicker_end" ).timepicker(oBaseTimePickerSettings);
	
	$( "#show_builder_timerange_button" ).click(function(ev){
		var oSettings,
			oRange;
		
		oRange = fnGetScheduleRange();
		
	    oSettings = oTable.fnSettings();
	    oSettings.fnServerData.start = oRange.start;
	    oSettings.fnServerData.end = oRange.end;
		
		oTable.fnDraw();
	});
	
	tableDiv.sortable({
		placeholder: "placeholder show-builder-placeholder",
		forceHelperSize: true,
		forcePlaceholderSize: true,
		items: 'tr:not(.show-builder-header):not(.show-builder-footer)',
		//cancel: ".show-builder-header .show-builder-footer",
		receive: function(event, ui) {
			var x;
		},
		update: function(event, ui) {
			var oItemData = ui.item.data("aData"),
				oPrevData = ui.item.prev().data("aData"),
				oRequestData = {format: "json"};
			
			//if prev item is a footer, item is not scheduled into a show.
			if (oPrevData.footer === false) {
				oRequestData.instance = oPrevData.instance;
			}
			
			//set the start time appropriately
			if (oPrevData.header === true) {
				oRequestData.start = oPrevData.startsUnix;
			}
			else {
				oRequestData.start = oPrevData.endsUnix;
			}
			
			oRequestData.id = oItemData.id;
			oRequestData.duration = oItemData.duration;
			
			/*
			$.post("/showbuilder/schedule", oRequestData, function(json){
				var x;
			});
			*/
		}
	});
	
	$("#show_builder_toolbar")
		.append('<span class="ui-icon ui-icon-trash"></span>')
		.find(".ui-icon-trash")
			.click(fnRemoveSelectedItems);
	
});
