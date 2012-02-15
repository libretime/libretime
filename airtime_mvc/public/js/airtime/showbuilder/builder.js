$(document).ready(function() {
	var tableDiv = $('#show_builder_table'),
		oTable,
		oBaseDatePickerSettings,
		oBaseTimePickerSettings,
		fnAddSelectedItems,
		fnRemoveSelectedItems,
		oRange,
		fnServerData,
		fnShowBuilderRowCallback;
	
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
	function fnGetTimestamp(sDatePickerId, sTimePickerId) {
		var date, 
			time,
			iTime,
			iServerOffset,
			iClientOffset;
	
		if ($(sDatePickerId).val() === "") {
			return 0;
		}
		
		date = $(sDatePickerId).val();
		time = $(sTimePickerId).val();
		
		date = date.split("-");
		time = time.split(":");
		
		//0 based month in js.
		oDate = new Date(date[0], date[1]-1, date[2], time[0], time[1]);
		
		iTime = oDate.getTime(); //value is in millisec.
		iTime = Math.round(iTime / 1000);
		iServerOffset = serverTimezoneOffset;
		iClientOffset = oDate.getTimezoneOffset() * 60;//function returns minutes
		
		//adjust for the fact the the Date object is in clent time.
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
			DEFAULT_RANGE = 60*60*24;
		
		iStart = fnGetTimestamp("#sb_date_start", "#sb_time_start");
		iEnd = fnGetTimestamp("#sb_date_end", "#sb_time_end");
		
		iRange = iEnd - iStart;
		
		if (iRange === 0 || iEnd < iStart) {
			iEnd = iStart + DEFAULT_RANGE;
			iRange = DEFAULT_RANGE;
		}
		
		return {
			start: iStart,
			end: iEnd,
			range: iRange
		};
	}

	fnServerData = function ( sSource, aoData, fnCallback ) {
		aoData.push( { name: "format", value: "json"} );
		
		if (fnServerData.hasOwnProperty("start")) {
			aoData.push( { name: "start", value: fnServerData.start} );
		}
		if (fnServerData.hasOwnProperty("end")) {
			aoData.push( { name: "end", value: fnServerData.end} );
		}
		if (fnServerData.hasOwnProperty("ops")) {
			aoData.push( { name: "myShows", value: fnServerData.ops.myShows} );
			aoData.push( { name: "showFilter", value: fnServerData.ops.showFilter} );
		}
		
		$.ajax( {
			"dataType": "json",
			"type": "GET",
			"url": sSource,
			"data": aoData,
			"success": fnCallback
		} );
	};
	
	oRange = fnGetScheduleRange();	
	fnServerData.start = oRange.start;
	fnServerData.end = oRange.end;

	fnShowBuilderRowCallback = function ( nRow, aData, iDisplayIndex, iDisplayIndexFull ){
		var i,
			sSeparatorHTML,
			fnPrepareSeparatorRow,
			node,
			cl="";
		
		//save some info for reordering purposes.
		$(nRow).data({"aData": aData});
		
		fnPrepareSeparatorRow = function(sRowContent, sClass, iNodeIndex) {
			
			node = nRow.children[iNodeIndex];
			node.innerHTML = sRowContent;
			node.setAttribute('colspan',100);
			for (i = iNodeIndex + 1; i < nRow.children.length; i = i+1) {
				node = nRow.children[i];
				node.innerHTML = "";
				node.setAttribute("style", "display : none");
			}
			
			nRow.className = sClass;
		};
		
		if (aData.header === true) {
			cl = 'sb-header';
			
			sSeparatorHTML = '<span>'+aData.title+'</span><span>'+aData.starts+'</span><span>'+aData.ends+'</span>';
			fnPrepareSeparatorRow(sSeparatorHTML, cl, 0);
		}
		else if (aData.footer === true) {
			node = nRow.children[0];
			cl = 'sb-footer';
			
			//check the show's content status.
			if (aData.runtime > 0) {
				node.innerHTML = '<span class="ui-icon ui-icon-check"></span>';
				cl = cl + ' ui-state-highlight';
			}
			else {
				node.innerHTML = '<span class="ui-icon ui-icon-notice"></span>';
				cl = cl + ' ui-state-error';
			}
				
			sSeparatorHTML = '<span>'+aData.fRuntime+'</span>';
			fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
		}		
		else {
			//$(nRow).attr("id", "sched_"+aData.id);
			
			node = nRow.children[0];
			if (aData.checkbox === true) {
				node.innerHTML = '<input type="checkbox" name="'+aData.id+'"></input>';
			}
			else {
				node.innerHTML = '';
				cl = cl + " sb-not-allowed";
			}
			
			if (aData.empty === true) {
				
				sSeparatorHTML = '<span>Show Empty</span>';
				cl = cl + " sb-empty odd";
				
				fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
			}
		}
	};

	fnRemoveSelectedItems = function() {
		var oTT = TableTools.fnGetInstance('show_builder_table'),
			aData = oTT.fnGetSelectedData(),
			item,
			temp,
			aItems = [];
	
		for (item in aData) {
			temp = aData[item];
			if (temp !== null && temp.hasOwnProperty('id')) {
				aItems.push({"id": temp.id, "instance": temp.instance});
			} 	
		}
		
		$.post( "/showbuilder/schedule-remove",
			{"items": aItems, "format": "json"},
			function(data) {
				oTable.fnDraw();
			});
	};
	
	oTable = tableDiv.dataTable( {
		"aoColumns": [
	    /* checkbox */ {"mDataProp": "checkbox", "sTitle": "<input type='checkbox' name='sb_cb_all'>", "sWidth": "15px"},
        /* starts */{"mDataProp": "starts", "sTitle": "Airtime"},
        /* ends */{"mDataProp": "ends", "sTitle": "Off Air"},
        /* runtime */{"mDataProp": "runtime", "sTitle": "Runtime"},
        /* title */{"mDataProp": "title", "sTitle": "Title"},
        /* creator */{"mDataProp": "creator", "sTitle": "Creator"},
        /* album */{"mDataProp": "album", "sTitle": "Album"}
        ],
        
        "bJQueryUI": true,
        "bSort": false,
        "bFilter": false,
        "bProcessing": true,
		"bServerSide": true,
		"bInfo": false,
		"bAutoWidth": false,
        
		"fnServerData": fnServerData,
		"fnRowCallback": fnShowBuilderRowCallback,
		"fnHeaderCallback": function(nHead) {
			$(nHead).find("input[type=checkbox]").attr("checked", false);
		},
		//remove any selected nodes before the draw.
		"fnPreDrawCallback": function( oSettings ) {
			var oTT = TableTools.fnGetInstance('show_builder_table');
			oTT.fnSelectNone();
	    },
		
		"oColVis": {
			"aiExclude": [ 0, 1 ]
		},
		
		"oColReorder": {
			"iFixedColumns": 2
		},
		
		"oTableTools": {
        	"sRowSelect": "multi",
			"aButtons": [],
			"fnPreRowSelect": function ( e ) {
				var node = e.currentTarget;
				//don't select separating rows, or shows without privileges.
				if ($(node).hasClass("sb-header")
						|| $(node).hasClass("sb-footer")
						|| $(node).hasClass("sb-not-allowed")){
					return false;
				}
				return true;
            },
			"fnRowSelected": function ( node ) {

                //seems to happen if everything is selected
                if ( node === null) {
                	oTable.find("input[type=checkbox]").attr("checked", true);
                }
                else {
                	$(node).find("input[type=checkbox]").attr("checked", true);
                }
            },
            "fnRowDeselected": function ( node ) {
	
              //seems to happen if everything is deselected
                if ( node === null) {
                	var oTable = $("#show_builder_table").dataTable();
                	oTable.find("input[type=checkbox]").attr("checked", false);
                }
                else {
                	$(node).find("input[type=checkbox]").attr("checked", false);
                }
            }
		},
		
        // R = ColReorderResize, C = ColVis, T = TableTools
        "sDom": 'Rr<"H"CT>t<"F">',
        
        "sAjaxDataProp": "schedule",
		"sAjaxSource": "/showbuilder/builder-feed"	
	});
	
	$('[name="sb_cb_all"]').click(function(){
    	var oTT = TableTools.fnGetInstance('show_builder_table');
    	
    	if ($(this).is(":checked")) {
    		var allowedNodes;
    		
    		allowedNodes = oTable.find('tr:not(:first):not(.sb-header):not(.sb-footer):not(.sb-not-allowed)');
    		
    		allowedNodes.each(function(i, el){
    			oTT.fnSelect(el);
    		});	
    	}
    	else {
    		oTT.fnSelectNone();
    	}       
    });
	
	$("#sb_date_start").datepicker(oBaseDatePickerSettings);
	$("#sb_time_start").timepicker(oBaseTimePickerSettings);
	$("#sb_date_end").datepicker(oBaseDatePickerSettings);
	$("#sb_time_end").timepicker(oBaseTimePickerSettings);
	
	$("#sb_submit").click(function(ev){
		var fn,
			oRange,
			op;
		
		oRange = fnGetScheduleRange();
		
	    fn = oTable.fnSettings().fnServerData;
	    fn.start = oRange.start;
	    fn.end = oRange.end;
	    
	    op = $("div.sb-advanced-options");
	    if (op.is(":visible")) {
	    	
	    	if (fn.ops === undefined) {
	    		fn.ops = {};
	    	}
	    	fn.ops.showFilter = op.find("#sb_show_filter").val();
	    	fn.ops.myShows = op.find("#sb_my_shows").is(":checked") ? 1 : 0;
	    }
		
		oTable.fnDraw();
	});
	
	var sortableConf = (function(){
		var origRow,
			oItemData,
			oPrevData,
			fnAdd,
			fnMove,
			fnReceive,
			fnUpdate;
		
		fnAdd = function() {
			var aMediaIds = [],
			aSchedIds = [];
			
			aSchedIds.push({"id": oPrevData.id, "instance": oPrevData.instance});
			aMediaIds.push({"id": oItemData.id, "type": oItemData.ftype});

			$.post("/showbuilder/schedule-add", 
				{"format": "json", "mediaIds": aMediaIds, "schedIds": aSchedIds}, 
				function(json){
					oTable.fnDraw();
				});
		};
		
		fnMove = function() {
			var aSelect = [],
				aAfter = [];
		
			aSelect.push({"id": oItemData.id, "instance": oItemData.instance});
			aAfter.push({"id": oPrevData.id, "instance": oPrevData.instance});
	
			$.post("/showbuilder/schedule-move", 
				{"format": "json", "selectedItem": aSelect, "afterItem": aAfter},  
				function(json){
					oTable.fnDraw();
				});
		};
		
		fnReceive = function(event, ui) {
			origRow = ui.item;
		};
		
		fnUpdate = function(event, ui) {
			oPrevData = ui.item.prev().data("aData");
			
			//item was dragged in
			if (origRow !== undefined) {
				oItemData = origRow.data("aData");
				origRow = undefined;
				fnAdd();
			}
			//item was reordered.
			else {
				oItemData = ui.item.data("aData");
				fnMove();
			}
		};
		
		return {
			placeholder: "placeholder show-builder-placeholder ui-state-highlight",
			forcePlaceholderSize: true,
			items: 'tr:not(:first):not(.sb-header):not(.sb-footer):not(.sb-not-allowed)',
			receive: fnReceive,
			update: fnUpdate,
			start: function(event, ui) {
				//ui.placeholder.html("PLACE HOLDER");
			}
		};
	}());
	
	tableDiv.sortable(sortableConf);
	
	$("#show_builder .fg-toolbar")
		.append('<div class="ColVis TableTools"><button class="ui-button ui-state-default"><span>Delete</span></button></div>')
		.click(fnRemoveSelectedItems);
	
});
