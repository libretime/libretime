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
	
	mod.timeout = undefined;
	
	mod.resetTimestamp = function() {
		var timestamp = $("#sb_timestamp");
		//reset timestamp value since input values could have changed.
		timestamp.val(-1);
	};
	
	mod.setTimestamp = function(timestamp) {
		$("#sb_timestamp").val(timestamp);
	};
	
	mod.getTimestamp = function() {
		var timestamp = $("#sb_timestamp"),
			val;
		
		//if the timestamp field is on the page return it, or give the default of -1
		//to ensure a page refresh.
		if (timestamp.length === 1) {
			val = timestamp.val();
		}
		else {
			val = -1;
		}
		
		return val;
	};
	
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
		
		aoData.push( { name: "timestamp", value: AIRTIME.showbuilder.getTimestamp()} );
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
			"type": "POST",
			"url": sSource,
			"data": aoData,
			"success": function(json) {
				AIRTIME.showbuilder.setTimestamp(json.timestamp);
				fnCallback(json);
			}
		} );
	};
	
	mod.fnServerData = fnServerData;
	
	mod.builderDataTable = function() {
		var tableDiv = $('#show_builder_table'),
			oTable,
			fnRemoveSelectedItems,
			tableHeight;

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
		    /* checkbox */ {"mDataProp": "allowed", "sTitle": "<input type='checkbox' name='sb_cb_all'>", "sWidth": "15px", "sClass": "sb_checkbox"},
            /* Type */ {"mDataProp": "image", "sTitle": "", "sClass": "library_image", "sWidth": "25px", "bVisible": true},
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
				
                //call the context menu so we can prevent the event from propagating.
                $(nRow).find('td:not(.sb_checkbox)').click(function(e){
                    
                    $(this).contextMenu({x: e.pageX, y: e.pageY});
                    
                    return false;
                });
				
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
				
                //add the play function to the library_type td or the speaker
                $(nRow).find('td.library_image').click(function(){
                    open_show_preview(aData.instance, iDisplayIndex);
                    return false;
                });
            
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
					tr,
					oTable = $('#show_builder_table').dataTable(),
					aData;
				
				clearTimeout(AIRTIME.showbuilder.timeout);
				
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
					aData = tr.data("aData");
					
					setTimeout(function(){
						AIRTIME.showbuilder.resetTimestamp();
						oTable.fnDraw();
					}, aData.refresh * 1000); //need refresh in milliseconds
				}
				//current song is not set, set a timeout to refresh when the first item on the timeline starts.
				else {
					tr = tableDiv.find("tbody tr.sb-allowed.sb-header:first");
					
					if (tr.length > 0) {
						aData = tr.data("aData");
						
						AIRTIME.showbuilder.timeout = setTimeout(function(){
							AIRTIME.showbuilder.resetTimestamp();
							oTable.fnDraw();
						}, aData.timeUntil * 1000); //need refresh in milliseconds
					}	
				}
		    },
			"fnHeaderCallback": function(nHead) {
				$(nHead).find("input[type=checkbox]").attr("checked", false);
			},
			//remove any selected nodes before the draw.
			"fnPreDrawCallback": function( oSettings ) {
				var oTT;
				
				oTT = TableTools.fnGetInstance('show_builder_table');
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
	        "sDom": 'Rr<"H"CT>t',
	        
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
				html,
				helperData,
				draggingContainer;
			
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
				
				for(i = 0; i < helperData.length; i++) {
					aSelect.push({"id": helperData[i].id, "instance": helperData[i].instance, "timestamp": helperData[i].timestamp});
				}
			
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
				if (prev.find("td:first").hasClass("dataTables_empty")
						|| prev.length === 0) {
					alert("Cannot schedule outside a show.");
					ui.item.remove();
					return;
				}
				
				//if item is added after a footer, add the item after the last item in the show.
				if (prev.hasClass("sb-footer")) {
					prev = prev.prev();
				}
				
				aItemData = [];
				oPrevData = prev.data("aData");
				
				//item was dragged in
				if (origTrs !== undefined) {
					
					tableDiv.find("tr.ui-draggable")
						.empty()
						.after(html);
					
					aItemData = origTrs;
					origTrs = undefined;
					fnAdd();
				}
				//item was reordered.
				else {
					
					ui.item
						.empty()
						.after(draggingContainer.html());
					
					aItemData.push(ui.item.data("aData"));
					fnMove();
				}
			};
			
			return {
				placeholder: "placeholder show-builder-placeholder ui-state-highlight",
				forcePlaceholderSize: true,
				helper: function(event, item) {
					var oTT = TableTools.fnGetInstance('show_builder_table'),
				    	selected = oTT.fnGetSelectedData(),
				    	elements = tableDiv.find('tr:not(:first) input:checked').parents('tr'),				    	
				    	thead = $("#show_builder_table thead"),
				    	colspan = thead.find("th").length,
				    	trfirst = thead.find("tr:first"),
				    	width = trfirst.width(),
				    	height = trfirst.height(),
				    	message;
					
					//elements.hide();
				    
				    //if nothing is checked select the dragged item.
				    if (selected.length === 0) {
				    	selected = [item.data("aData")];
				    }
				    
				    if (selected.length === 1) {
				    	message = "Moving "+selected.length+" Item.";
				    }
				    else {
				    	message = "Moving "+selected.length+" Items.";
				    }
				    
				    draggingContainer = $('<tr/>')
				    	.addClass('sb-helper')
				    	.append('<td/>')
				    	.find("td")
				    		.attr("colspan", colspan)
				    		.width(width)
				    		.height(height)
				    		.addClass("ui-state-highlight")
				    		.append(message)
				    		.end();
  
				    helperData = selected;
				    
				    return draggingContainer; 
			    },
				items: 'tr:not(:first, :last, .sb-header, .sb-footer, .sb-not-allowed)',
				receive: fnReceive,
				update: fnUpdate,
				start: function(event, ui) {
					var elements = tableDiv.find('tr:not(:first) input:checked').parents('tr');
					
					elements.hide();
				}
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
		
		//begin context menu initialization.
        $.contextMenu({
            selector: '#show_builder_table td:not(.sb_checkbox)',
            trigger: "left",
            ignoreRightClick: true,
            
            build: function($el, e) {
                var data, items, callback, $tr;
                
                $tr = $el.parent();
                data = $tr.data("aData");
                
                function processMenuItems(oItems) {
                    
                    //define a delete callback.
                    if (oItems.del !== undefined) {
                        
                        callback = function() {
                            AIRTIME.showbuilder.fnRemove([{
                            	id: data.id,
                            	timestamp: data.timestamp,
                            	instance: data.instance
                            }]);
                        };
                        
                        oItems.del.callback = callback;
                    }
                           
                    items = oItems;
                }
                
                request = $.ajax({
                  url: "/showbuilder/context-menu",
                  type: "GET",
                  data: {id : data.id, format: "json"},
                  dataType: "json",
                  async: false,
                  success: function(json){
                      processMenuItems(json.items);
                  }
                });
    
                return {
                    items: items
                };
            }
        });	
	};
	
	mod.init = function(oTable) {
		oSchedTable = oTable;
	};
	
	return AIRTIME;
	
}(AIRTIME || {}));