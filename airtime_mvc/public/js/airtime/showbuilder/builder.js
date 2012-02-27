var AIRTIME = (function(AIRTIME){
	var mod,
		oSchedTable;
	
	if (AIRTIME.showbuilder === undefined) {
		AIRTIME.showbuilder = {};
	}
	mod = AIRTIME.showbuilder;
	
	function checkError(json) {
		if (json.error !== undefined) {
			alert(json.error);
		}
	}
	
	mod.fnAdd = function(aMediaIds, aSchedIds) {
		var oLibTT = TableTools.fnGetInstance('library_display');
		
		$.post("/showbuilder/schedule-add", 
			{"format": "json", "mediaIds": aMediaIds, "schedIds": aSchedIds}, 
			function(json){
				checkError(json);
				oSchedTable.fnDraw();
				oLibTT.fnSelectNone();
			});
	};
	
	mod.fnMove = function(aSelect, aAfter) {
		
		$.post("/showbuilder/schedule-move", 
			{"format": "json", "selectedItem": aSelect, "afterItem": aAfter},  
			function(json){
				checkError(json);
				oSchedTable.fnDraw();
			});
	};
	
	mod.fnRemove = function(aItems) {
		
		$.post( "/showbuilder/schedule-remove",
			{"items": aItems, "format": "json"},
			function(json) {
				checkError(json);
				oSchedTable.fnDraw();
			});
	};
	
	mod.init = function(oTable) {
		oSchedTable = oTable;
	};
	
	return AIRTIME;
	
}(AIRTIME || {}));


$(document).ready(function() {
	var tableDiv = $('#show_builder_table'),
		oTable,
		oBaseDatePickerSettings,
		oBaseTimePickerSettings,
		fnAddSelectedItems,
		fnRemoveSelectedItems,
		oRange,
		fnServerData;
	
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
		iClientOffset = oDate.getTimezoneOffset() * -60;//function returns minutes
		
		//adjust for the fact the the Date object is in client time.
		iTime = iTime + iClientOffset + iServerOffset;
		
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

	fnRemoveSelectedItems = function() {
		var oTT = TableTools.fnGetInstance('show_builder_table'),
			aData = oTT.fnGetSelectedData(),
			i,
			length,
			temp,
			aItems = [];
	
		for (i=0, length = aData.length; i < length; i++) {
			temp = aData[i];
			aItems.push({"id": temp.id, "instance": temp.instance, "timestamp": temp.timestamp}); 	
		}
		
		AIRTIME.showbuilder.fnRemove(aItems);
	};
	
	oTable = tableDiv.dataTable( {
		"aoColumns": [
	    /* checkbox */ {"mDataProp": "allowed", "sTitle": "<input type='checkbox' name='sb_cb_all'>", "sWidth": "15px"},
        /* starts */{"mDataProp": "starts", "sTitle": "Start"},
        /* ends */{"mDataProp": "ends", "sTitle": "End"},
        /* runtime */{"mDataProp": "runtime", "sTitle": "Duration", "sClass": "library_length"},
        /* title */{"mDataProp": "title", "sTitle": "Title"},
        /* creator */{"mDataProp": "creator", "sTitle": "Creator"},
        /* album */{"mDataProp": "album", "sTitle": "Album"},
        /* cue in */{"mDataProp": "cuein", "sTitle": "Cue In", "bVisible": false},
        /* cue out */{"mDataProp": "cueout", "sTitle": "Cue Out", "bVisible": false},
        /* fade in */{"mDataProp": "fadein", "sTitle": "Fade In", "bVisible": false},
        /* fade out */{"mDataProp": "fadeout", "sTitle": "Fade Out", "bVisible": false}
        ],
        
        "bJQueryUI": true,
        "bSort": false,
        "bFilter": false,
        "bProcessing": true,
		"bServerSide": true,
		"bInfo": false,
		"bAutoWidth": false,
		
		"bStateSave": true,
		"fnStateSaveParams": function (oSettings, oData) {
    		//remove oData components we don't want to save.
    		delete oData.oSearch;
    		delete oData.aoSearchCols;
	    },
        "fnStateSave": function (oSettings, oData) {
           
    		$.ajax({
			  url: "/usersettings/set-timeline-datatable",
			  type: "POST",
			  data: {settings : oData, format: "json"},
			  dataType: "json",
			  success: function(){},
			  error: function (jqXHR, textStatus, errorThrown) {
				  var x;
			  }
			});
        },
        "fnStateLoad": function (oSettings) {
        	var o;

        	$.ajax({
  			  url: "/usersettings/get-timeline-datatable",
  			  type: "GET",
  			  data: {format: "json"},
  			  dataType: "json",
  			  async: false,
  			  success: function(json){
  				  o = json.settings;
  			  },
  			  error: function (jqXHR, textStatus, errorThrown) {
				  var x;
			  }
  			});
        	
        	return o;
        },
        "fnStateLoadParams": function (oSettings, oData) {
        	var i,
				length,
				a = oData.abVisCols;
		
        	//putting serialized data back into the correct js type to make
        	//sure everything works properly.
	        for (i = 0, length = a.length; i < length; i++) {	
	        	a[i] = (a[i] === "true") ? true : false;
	        }
	        
	        a = oData.ColReorder;
	        for (i = 0, length = a.length; i < length; i++) {	
	        	a[i] = parseInt(a[i], 10);
	        }
	       
	        oData.iCreate = parseInt(oData.iCreate, 10);
        },
        
		"fnServerData": fnServerData,
		"fnRowCallback": function ( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			var i,
				sSeparatorHTML,
				fnPrepareSeparatorRow,
				node,
				cl="";
			
			//save some info for reordering purposes.
			$(nRow).data({"aData": aData});
			
			if (aData.allowed !== true) {
				$(nRow).addClass("sb-not-allowed");
			}
			
			//status used to colour tracks.
			if (aData.status === 1) {
				$(nRow).addClass("sb-boundry");
			}
			else if (aData.status === 2) {
				$(nRow).addClass("sb-over");
			}
			
			fnPrepareSeparatorRow = function(sRowContent, sClass, iNodeIndex) {
				
				node = nRow.children[iNodeIndex];
				node.innerHTML = sRowContent;
				node.setAttribute('colspan',100);
				for (i = iNodeIndex + 1; i < nRow.children.length; i = i+1) {
					node = nRow.children[i];
					node.innerHTML = "";
					node.setAttribute("style", "display : none");
				}
				
				$(nRow).addClass(sClass);
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
			else if (aData.empty === true) {
				
				sSeparatorHTML = '<span>Show Empty</span>';
				cl = cl + " sb-empty odd";
				
				fnPrepareSeparatorRow(sSeparatorHTML, cl, 0);
			}
			else {
				
				node = nRow.children[0];
				if (aData.allowed === true) {
					node.innerHTML = '<input type="checkbox" name="'+aData.id+'"></input>';
				}
				else {
					node.innerHTML = '';
				}
			}
		},
		"fnDrawCallback": function(oSettings, json) {
			var wrapperDiv,
				markerDiv,
				td;
			
			//create cursor arrows.
			tableDiv.find("tr:not(:first, .sb-footer, .sb-empty, .sb-not-allowed)").each(function(i, el) {
		    	td = $(el).find("td:first");
		    	if (td.hasClass("dataTables_empty")) {
		    		return false;
		    	}
		    	
		    	wrapperDiv = $("<div />", {
		    		"class": "innerWrapper",
		    		"css": {
		    			"height": td.height()
		    		}
		    	});
		    	markerDiv = $("<div />", {
		    		"class": "marker"
		    	});
		    	
	    		td.append(markerDiv).wrapInner(wrapperDiv);
		    });
	    },
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
					|| $(node).hasClass("sb-empty")
					|| $(node).hasClass("sb-not-allowed")) {
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
    		
    		allowedNodes = oTable.find('tr:not(:first, .sb-header, .sb-empty, .sb-footer, .sb-not-allowed)');
    		
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
		var origTrs,
			aItemData = [],
			oPrevData,
			fnAdd,
			fnMove,
			fnReceive,
			fnUpdate,
			i, 
			html;
		
		fnAdd = function() {
			var aMediaIds = [],
				aSchedIds = [],
				oLibTT = TableTools.fnGetInstance('library_display');
			
			for(i=0; i < aItemData.length; i++) {
				aMediaIds.push({"id": aItemData[i].id, "type": aItemData[i].ftype});
			}
			aSchedIds.push({"id": oPrevData.id, "instance": oPrevData.instance, "timestamp": oPrevData.timestamp});
			
			AIRTIME.showbuilder.fnAdd(aMediaIds, aSchedIds);
		};
		
		fnMove = function() {
			var aSelect = [],
				aAfter = [];
		
			aSelect.push({"id": aItemData[0].id, "instance": aItemData[0].instance, "timestamp": aItemData[0].timestamp});
			aAfter.push({"id": oPrevData.id, "instance": oPrevData.instance, "timestamp": oPrevData.timestamp});
	
			AIRTIME.showbuilder.fnMove(aSelect, aAfter);
		};
		
		fnReceive = function(event, ui) {
			var selected = $('#library_display tr:not(:first) input:checked').parents('tr'),
				aItems = [];
			
			//if nothing is checked select the dragged item.
		    if (selected.length === 0) {
		    	selected = ui.item;
		    }
		    
		    selected.each(function(i, el) { 
		    	aItems.push($(el).data("aData"));
		    });
			
			origTrs = aItems;
			html = ui.helper.html();
		};
		
		fnUpdate = function(event, ui) {
			aItemData = [];
			oPrevData = ui.item.prev().data("aData");
			
			//item was dragged in
			if (origTrs !== undefined) {
				
				$("#show_builder_table tr.ui-draggable")
					.empty()
					.after(html);
				
				aItemData = origTrs;
				origTrs = undefined;
				fnAdd();
			}
			//item was reordered.
			else {
				aItemData.push(ui.item.data("aData"));
				fnMove();
			}
		};
		
		return {
			placeholder: "placeholder show-builder-placeholder ui-state-highlight",
			forcePlaceholderSize: true,
			items: 'tr:not(:first, :last, .sb-header, .sb-footer, .sb-not-allowed)',
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
	
	//set things like a reference to the table.
	AIRTIME.showbuilder.init(oTable);
	
	//add event to cursors.
	tableDiv.find("tbody").on("click", "div.marker", function(event){
		var tr = $(this).parents("tr");
		
		if (tr.hasClass("cursor-selected-row")) {
			tr.removeClass("cursor-selected-row");
		}
		else {
			tr.addClass("cursor-selected-row");
		}
		
		return false;
	});
	
});
