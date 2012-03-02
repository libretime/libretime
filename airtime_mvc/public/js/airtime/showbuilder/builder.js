var AIRTIME = (function(AIRTIME){
	var mod,
		oSchedTable,
		fnServerData;
	
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
	
	mod.fnServerData = fnServerData;
	
	mod.builderDataTable = function() {
		var tableDiv = $('#show_builder_table'),
			oTable,
			fnRemoveSelectedItems;

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
	        
			"fnServerData": AIRTIME.showbuilder.fnServerData,
			"fnRowCallback": function ( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
				var i,
					sSeparatorHTML,
					fnPrepareSeparatorRow,
					node,
					cl="";
				
				//save some info for reordering purposes.
				$(nRow).data({"aData": aData});
				
				if (aData.current === true) {
					$(nRow).addClass("sb-now-playing");
				}
				
				if (aData.allowed !== true) {
					$(nRow).addClass("sb-not-allowed");
				}
				else {
					$(nRow).addClass("sb-allowed");
				}
				
				//status used to colour tracks.
				if (aData.status === 2) {
					$(nRow).addClass("sb-boundry");
				}
				else if (aData.status === 0) {
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
				else if (aData.record === true) {
					
					sSeparatorHTML = '<span>Recording From Line In</span>';
					cl = cl + " sb-record odd";
					
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
					td,
					$lib = $("#library_content"),
					tr;
				
				//only create the cursor arrows if the library is on the page.
				if ($lib.length > 0 && $lib.filter(":visible").length > 0) {
					
					//create cursor arrows.
					tableDiv.find("tr.sb-now-playing, tr:not(:first, .sb-footer, .sb-empty, .sb-not-allowed)").each(function(i, el) {
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
				}
				
				//if the now playing song is visible set a timeout to redraw the table at the start of the next song.
				tr = tableDiv.find("tr.sb-now-playing");
				if (tr.length > 0) {
					var oTable = $('#show_builder_table').dataTable(),
						aData = tr.data("aData");
					
					setTimeout(function(){
						oTable.fnDraw();
					}, aData.refresh * 1000); //need refresh in milliseconds
				}
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
	                
	                //checking to enable buttons
	                AIRTIME.button.enableButton("sb_delete");
	            },
	            "fnRowDeselected": function ( node ) {
	            	var selected;
	            		       	
	              //seems to happen if everything is deselected
	                if ( node === null) {
	                	tableDiv.find("input[type=checkbox]").attr("checked", false);
	                	selected = [];
	                }
	                else {
	                	$(node).find("input[type=checkbox]").attr("checked", false);
	                	selected = tableDiv.find("input[type=checkbox]").filter(":checked");
	                }
	                
	                //checking to disable buttons
	                if (selected.length === 0) {
	                	AIRTIME.button.disableButton("sb_delete");
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
					aSchedIds = [];
				
				for(i = 0; i < aItemData.length; i++) {
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
				var aItems = [],
					oLibTT = TableTools.fnGetInstance('library_display');
			
				aItems = oLibTT.fnGetSelectedData();
				
				//if nothing is checked select the dragged item.
			    if (aItems.length === 0) {
			    	aItems.push(ui.item.data("aData"));
			    }
			    
				origTrs = aItems;
				html = ui.helper.html();
			};
			
			fnUpdate = function(event, ui) {
				var prev = ui.item.prev();
				
				//can't add items outside of shows.
				if (prev.hasClass("sb-footer") 
						|| prev.find("td:first").hasClass("dataTables_empty")
						|| prev.length === 0) {
					alert("Cannot schedule outside a show.");
					ui.item.remove();
					return;
				}
				
				aItemData = [];
				oPrevData = prev.data("aData");
				
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
				update: fnUpdate
			};
		}());
		
		tableDiv.sortable(sortableConf);
		
		$("#show_builder .fg-toolbar")
			.append('<div class="ColVis TableTools sb_delete"><button class="ui-button ui-state-default ui-state-disabled"><span>Delete</span></button></div>')
			.click(fnRemoveSelectedItems);
		
		//set things like a reference to the table.
		AIRTIME.showbuilder.init(oTable);
		
		//add event to cursors.
		tableDiv.find("tbody").on("click", "div.marker", function(event){
			var tr = $(this).parents("tr"),
				cursorSelClass = "cursor-selected-row";
			
			if (tr.hasClass(cursorSelClass)) {
				tr.removeClass(cursorSelClass);
			}
			else {
				tr.addClass(cursorSelClass);
			}
			
			//check if add button can still be enabled.
			AIRTIME.library.events.enableAddButtonCheck();
			
			return false;
		});
		
	};
	
	mod.init = function(oTable) {
		oSchedTable = oTable;
	};
	
	return AIRTIME;
	
}(AIRTIME || {}));