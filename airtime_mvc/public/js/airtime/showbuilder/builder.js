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
		var $sbContent = $('#show_builder'),
			$sbTable = $sbContent.find('table'),
			oTable,
			fnRemoveSelectedItems,
			tableHeight,
			$toolbar;

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
		
		oTable = $sbTable.dataTable( {
			"aoColumns": [
		    /* checkbox */ {"mDataProp": "allowed", "sTitle": "<input type='checkbox' name='sb_cb_all'>", "sWidth": "15px", "sClass": "sb-checkbox"},
            /* Type */ {"mDataProp": "image", "sTitle": "", "sClass": "library_image sb-image", "sWidth": "16px"},
	        /* starts */{"mDataProp": "starts", "sTitle": "Start", "sClass": "sb-starts", "sWidth": "60px"},
	        /* ends */{"mDataProp": "ends", "sTitle": "End", "sClass": "sb-ends", "sWidth": "60px"},
	        /* runtime */{"mDataProp": "runtime", "sTitle": "Duration", "sClass": "library_length sb-length", "sWidth": "65px"},
	        /* title */{"mDataProp": "title", "sTitle": "Title", "sClass": "sb-title"},
	        /* creator */{"mDataProp": "creator", "sTitle": "Creator", "sClass": "sb-creator"},
	        /* album */{"mDataProp": "album", "sTitle": "Album", "sClass": "sb-album"},
	        /* cue in */{"mDataProp": "cuein", "sTitle": "Cue In", "bVisible": false, "sClass": "sb-cue-in"},
	        /* cue out */{"mDataProp": "cueout", "sTitle": "Cue Out", "bVisible": false, "sClass": "sb-cue-out"},
	        /* fade in */{"mDataProp": "fadein", "sTitle": "Fade In", "bVisible": false, "sClass": "sb-fade-in"},
	        /* fade out */{"mDataProp": "fadeout", "sTitle": "Fade Out", "bVisible": false, "sClass": "sb-fade-out"}
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
					cl="",
					//background-color to imitate calendar color.
					r,g,b,a,
					$nRow = $(nRow),
					$image;
				
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
					//remove the column classes from all tds.
					$(nRow).find('td').removeClass();
					
					node = nRow.children[0];
					node.innerHTML = '';
					cl = 'sb-header';
					
					sSeparatorHTML = '<span class="show-title">'+aData.title+'</span>';
					sSeparatorHTML += '<span class="push-right"><span class="show-time">'+aData.starts+'</span>-<span class="show-time">'+aData.ends+'</span></span>';
					fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
				}
				else if (aData.footer === true) {
					//remove the column classes from all tds.
					$(nRow).find('td').removeClass();
					
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
					//remove the column classes from all tds.
					$(nRow).find('td').removeClass();
					
					node = nRow.children[0];
					node.innerHTML = '';
					
					sSeparatorHTML = '<span>Show Empty</span>';
					cl = cl + " sb-empty odd";
					
					fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
				}
				else if (aData.record === true) {
					//remove the column classes from all tds.
					$(nRow).find('td').removeClass();
					
					node = nRow.children[0];
					node.innerHTML = '';
					
					sSeparatorHTML = '<span>Recording From Line In</span>';
					cl = cl + " sb-record odd";
					
					fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
				}
				else {
					
					 //add the play function if the file exists on disk.
					$image = $(nRow).find('td.sb-image');
					//check if the file exists.
					if (aData.image === true) {
						$image.html('<img src="/css/images/icon_audioclip.png"></img>')
							.click(function() {
			                    open_show_preview(aData.instance, aData.pos);
			                    return false;
			                });
					}
					else {
						$image.html('<span class="ui-icon ui-icon-alert"></span>');
					}
					
					node = nRow.children[0];
					if (aData.allowed === true && aData.scheduled >= 1) {
						node.innerHTML = '<input type="checkbox" name="'+aData.id+'"></input>';
					}
					else {
						node.innerHTML = '';
					}
				}
				
				//add the show colour to the leftmost td
                if (aData.footer !== true) {
                	
                	if ($nRow.hasClass('sb-header')) {
                		a = 1;
                	}
                	else if ($nRow.hasClass('odd')) {
                		a = 0.3;
                	}
                	else if ($nRow.hasClass('even')) {
                		a = 0.4;
                	}
                	
                	//convert from hex to rgb.
                	r = parseInt((aData.backgroundColor).substring(0,2), 16);
                	g = parseInt((aData.backgroundColor).substring(2,4), 16);
                	b = parseInt((aData.backgroundColor).substring(4,6), 16);
                	
                	$nRow.find('td:first').css('background', 'rgba('+r+', '+g+', '+b+', '+a+')');
                }
                
                //save some info for reordering purposes.
				$(nRow).data({"aData": aData});
				
				if (aData.scheduled === 1) {
					$(nRow).addClass("sb-now-playing");
				}
				else if (aData.scheduled === 0) {
					$(nRow).addClass("sb-past");
				}
				else {
					$(nRow).addClass("sb-future");
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
                 
                //call the context menu so we can prevent the event from propagating.
                $(nRow).find('td:gt(1)').click(function(e){
                    
                    $(this).contextMenu({x: e.pageX, y: e.pageY});
                    
                    return false;
                });
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
					$sbTable.find("tr:not(:first, .sb-header, .sb-empty, .sb-now-playing, .sb-past, .sb-not-allowed)").each(function(i, el) {
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
				tr = $sbTable.find("tr.sb-now-playing");
				
				if (tr.length > 0) {
					//enable jump to current button.
					AIRTIME.button.enableButton("sb-button-current");
					
					aData = tr.data("aData");
					
					setTimeout(function(){
						AIRTIME.showbuilder.resetTimestamp();
						oTable.fnDraw();
					}, aData.refresh * 1000); //need refresh in milliseconds
					
				}
				//current song is not set, set a timeout to refresh when the first item on the timeline starts.
				else {
					tr = $sbTable.find("tbody tr.sb-future.sb-header:first");
					
					if (tr.length > 0) {
						aData = tr.data("aData");
						
						AIRTIME.showbuilder.timeout = setTimeout(function(){
							AIRTIME.showbuilder.resetTimestamp();
							oTable.fnDraw();
						}, aData.timeUntil * 1000); //need refresh in milliseconds
					}	
				}
				
				//check if there are any overbooked tracks on screen to enable the trim button.
				tr = $sbTable.find("tr.sb-over.sb-future");
				
				if (tr.length > 0) {
					//enable deleting of overbooked tracks.
					AIRTIME.button.enableButton("sb-button-trim");
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
				
				//disable jump to current button.
				AIRTIME.button.disableButton("sb-button-current");
				//disable deleting of overbooked tracks.
				AIRTIME.button.disableButton("sb-button-trim");
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
						|| $(node).hasClass("sb-not-allowed")
						|| $(node).hasClass("sb-past")) {
						return false;
					}
					return true;
	            },
				"fnRowSelected": function ( node ) {

	                //seems to happen if everything is selected
	                if ( node === null) {
	                	$sbTable.find("tbody input[type=checkbox]").attr("checked", true);
	                }
	                else {
	                	$(node).find("input[type=checkbox]").attr("checked", true);
	                }
	                
	                //checking to enable buttons
	                AIRTIME.button.enableButton("sb-button-delete");
	            },
	            "fnRowDeselected": function ( node ) {
	            	var selected;
	            		       	
	              //seems to happen if everything is deselected
	                if ( node === null) {
	                	$sbTable.find("input[type=checkbox]").attr("checked", false);
	                	selected = [];
	                }
	                else {
	                	$(node).find("input[type=checkbox]").attr("checked", false);
	                	selected = $sbTable.find("input[type=checkbox]").filter(":checked");
	                }
	                
	                //checking to disable buttons
	                if (selected.length === 0) {
	                	AIRTIME.button.disableButton("sb-button-delete");
	                }
	            }
			},
			
	        // R = ColReorderResize, C = ColVis, T = TableTools
	        "sDom": 'R<"dt-process-rel"r><"sb-padded"<"H"CT>><"dataTables_scrolling sb-padded"t>',
	        
	        "sAjaxDataProp": "schedule",
			"sAjaxSource": "/showbuilder/builder-feed"	
		});
		
		$('[name="sb_cb_all"]').click(function(){
	    	var oTT = TableTools.fnGetInstance('show_builder_table');
	    	
	    	if ($(this).is(":checked")) {
	    		var allowedNodes;
	    		
	    		allowedNodes = oTable.find('tr:not(:first, .sb-header, .sb-empty, .sb-footer, .sb-not-allowed, .sb-past)');
	    		
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
					
					$sbTable.find("tr.ui-draggable")
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
				placeholder: "sb-placeholder ui-state-highlight",
				forcePlaceholderSize: true,
				distance: 10,
				helper: function(event, item) {
					var oTT = TableTools.fnGetInstance('show_builder_table'),
				    	selected = oTT.fnGetSelectedData(),
				    	elements = $sbTable.find('tr:not(:first) input:checked').parents('tr'),				    	
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
				items: 'tr:not(:first, :last, .sb-header, .sb-not-allowed, .sb-past, .sb-now-playing)',
				cancel: '.sb-footer',
				receive: fnReceive,
				update: fnUpdate,
				start: function(event, ui) {
					var elements = $sbTable.find('tr:not(:first) input:checked').parents('tr');
					
					elements.hide();
				}
			};
		}());
		
		$sbTable.sortable(sortableConf);
		
		//start setup of the builder toolbar.
		$toolbar = $(".sb-content .fg-toolbar");
		
		$toolbar
			.append("<ul />")
			.find('ul')
				.append('<li class="ui-state-default ui-state-disabled sb-button-current" title="jump to current item"><span class="ui-icon ui-icon-arrowstop-1-s"></span></li>')
				.append('<li class="ui-state-default ui-state-disabled sb-button-trim" title="delete all overbooked tracks"><span class="ui-icon ui-icon-scissors"></span></li>')
				.append('<li class="ui-state-default ui-state-disabled sb-button-delete" title="delete selected items"><span class="ui-icon ui-icon-trash"></span></li>');
		
		//jump to current
		$toolbar.find('.sb-button-current')
			.click(function() {
				
				if (AIRTIME.button.isDisabled('sb-button-current') === true) {
					return;
				}
				
				var $scroll = $sbContent.find(".dataTables_scrolling"),
					scrolled = $scroll.scrollTop(),
					scrollingTop = $scroll.offset().top,
					current = $sbTable.find(".sb-now-playing"),
					currentTop = current.offset().top;
		
				$scroll.scrollTop(currentTop - scrollingTop + scrolled);
			});
		
		//delete overbooked tracks.
		$toolbar.find('.sb-button-trim')
			.click(function() {
				
				if (AIRTIME.button.isDisabled('sb-button-trim') === true) {
					return;
				}
				
				var temp,
					aItems = [],
					trs = $sbTable.find(".sb-over.sb-future");
		
				trs.each(function(){
					temp = $(this).data("aData");
					aItems.push({"id": temp.id, "instance": temp.instance, "timestamp": temp.timestamp}); 	
				});
				
				AIRTIME.showbuilder.fnRemove(aItems);
			});
		
		//delete selected tracks
		$toolbar.find('.sb-button-delete')
			.click(function() {
				
				if (AIRTIME.button.isDisabled('sb-button-delete') === true) {
					return;
				}
				
				fnRemoveSelectedItems();
			});
		
		//set things like a reference to the table.
		AIRTIME.showbuilder.init(oTable);
		
		//add events to cursors.
		$sbTable.find("tbody").on("click", "div.marker", function(event) {
			var $tr = $(this).parents("tr"),
				cursorSelClass = "cursor-selected-row";
			
			if ($tr.hasClass(cursorSelClass)) {
				$tr.removeClass(cursorSelClass);
			}
			else {
				$tr.addClass(cursorSelClass);
			}
			
			if (event.ctrlKey === false) {
				$sbTable.find('.'+cursorSelClass)
					.not($tr)
					.removeClass(cursorSelClass);
			}
			
			//check if add button can still be enabled.
			AIRTIME.library.events.enableAddButtonCheck();

			return false;
		});
		
		//begin context menu initialization.
        $.contextMenu({
            selector: '.sb-content table tbody tr:not(.sb-empty, .sb-footer, .sb-header) td:not(.sb-checkbox, .sb-image)',
            trigger: "left",
            ignoreRightClick: true,
            
            build: function($el, e) {
                var data, items, callback, $tr;
                
                $tr = $el.parent();
                data = $tr.data("aData");
                
                function processMenuItems(oItems) {
                	
                	//define a select cursor.
                    if (oItems.selCurs !== undefined) {
                        
                        callback = function() {
                            $(this).parents('tr').next().addClass("cursor-selected-row");
                        };
                        
                        oItems.selCurs.callback = callback;
                    }
                    
                   //define a remove cursor.
                    if (oItems.delCurs !== undefined) {
                        
                        callback = function() {
                            $(this).parents('tr').next().removeClass("cursor-selected-row");
                        };
                        
                        oItems.delCurs.callback = callback;
                    }
                    
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