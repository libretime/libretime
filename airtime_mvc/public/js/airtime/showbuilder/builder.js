var AIRTIME = (function(AIRTIME){
	var mod,
		oSchedTable,
		SB_SELECTED_CLASS = "sb-selected",
		CURSOR_SELECTED_CLASS = "cursor-selected-row",
		NOW_PLAYING_CLASS = "sb-now-playing",
		$sbContent,
		$sbTable,
		$toolbar,
		$ul,
		$lib;
	
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
	mod.timestamp = -1;
	mod.showInstances = [];
	
	mod.resetTimestamp = function() {
		
		mod.timestamp = -1;
	};
	
	mod.setTimestamp = function(timestamp) {
		
		mod.timestamp = timestamp;
	};
	
	mod.getTimestamp = function() {
		
		if (mod.timestamp !== undefined) {
			return mod.timestamp;
		}
		else {
			return -1;
		}
	};
	
	mod.setShowInstances = function(showInstances) {
		mod.showInstances = showInstances;
	};
	
	mod.getShowInstances = function() {
		return mod.showInstances;
	};
	
	mod.refresh = function() {
		mod.resetTimestamp();
		oSchedTable.fnDraw();
	};
	
	mod.checkSelectButton = function() {
		var $selectable = $sbTable.find("tbody").find("input:checkbox");
		
		if ($selectable.length !== 0) {
			AIRTIME.button.enableButton("sb-button-select");
		}
		else {
			AIRTIME.button.disableButton("sb-button-select");
		}
	};
	
	mod.checkTrimButton = function() {
		var $over = $sbTable.find(".sb-over.sb-allowed");
		
		if ($over.length !== 0) {
			AIRTIME.button.enableButton("sb-button-trim");
		}
		else {
			AIRTIME.button.disableButton("sb-button-trim");
		}
	};
	
	mod.checkDeleteButton = function() {
		var $selected = $sbTable.find("tbody").find("input:checkbox").filter(":checked");
		
		if ($selected.length !== 0) {
			AIRTIME.button.enableButton("sb-button-delete");
		}
		else {
			AIRTIME.button.disableButton("sb-button-delete");
		}
	};
	
	mod.checkJumpToCurrentButton = function() {
		var $current = $sbTable.find("."+NOW_PLAYING_CLASS);
		
		if ($current.length !== 0) {
			AIRTIME.button.enableButton("sb-button-current");
		}
		else {
			AIRTIME.button.disableButton("sb-button-current");
		}
	};
	
	mod.checkCancelButton = function() {
		var $current = $sbTable.find(".sb-current-show.sb-allowed"),
			//this user type should be refactored into a separate users module later
			//when there's more time and more JS will need to know user data.
			userType = localStorage.getItem('user-type');
		
		if ($current.length !== 0 && (userType === 'A' || userType === 'P')) {
			AIRTIME.button.enableButton("sb-button-cancel");
		}
		else {
			AIRTIME.button.disableButton("sb-button-cancel");
		}
	};
	
	mod.checkToolBarIcons = function() {
    	
		AIRTIME.library.checkAddButton();
		mod.checkSelectButton();
		mod.checkTrimButton();
		mod.checkDeleteButton();
		mod.checkJumpToCurrentButton();
		mod.checkCancelButton();
    };
    
    mod.selectCursor = function($el) {
    	
    	$el.addClass(CURSOR_SELECTED_CLASS);
    	mod.checkToolBarIcons();
    };
    
    mod.removeCursor = function($el) {
    	
    	$el.removeClass(CURSOR_SELECTED_CLASS);
    	mod.checkToolBarIcons();
    };
	
    /*
     * sNot is an optional string to filter selected elements by. (ex removing the currently playing item)
     */
	mod.getSelectedData = function(sNot) {
    	var $selected = $sbTable.find("tbody").find("input:checkbox").filter(":checked").parents("tr"),
    		aData = [],
    		i, length,
    		$item;
    	
    	if (sNot !== undefined) {
    		$selected = $selected.not("."+sNot);
    	}
    	
    	for (i = 0, length = $selected.length; i < length; i++) {
    		$item = $($selected.get(i));
    		aData.push($item.data('aData'));
    	}
    	
    	return aData.reverse();
    };
    
    mod.selectAll = function () {
    	$inputs = $sbTable.find("input:checkbox");
    	
    	$inputs.attr("checked", true);
    	
    	$trs = $inputs.parents("tr");
 		$trs.addClass(SB_SELECTED_CLASS);
 		
 		mod.checkToolBarIcons();
    };
    
    mod.selectNone = function () {
    	$inputs = $sbTable.find("input:checkbox");
    	
    	$inputs.attr("checked", false);
    	
    	$trs = $inputs.parents("tr");
 		$trs.removeClass(SB_SELECTED_CLASS);
 		
 		mod.checkToolBarIcons();
    };
    
    mod.disableUI = function() {
    	
    	$lib.block({ 
            message: "",
            theme: true,
            applyPlatformOpacityRules: false
        });
    	
    	$sbContent.block({ 
            message: "",
            theme: true,
            applyPlatformOpacityRules: false
        });
    };
    
    mod.enableUI = function() {
    	
    	$lib.unblock();
    	$sbContent.unblock();
    	
    	//Block UI changes the postion to relative to display the messages.
    	$lib.css("position", "static");
    	$sbContent.css("position", "static");
    };
    
    mod.fnItemCallback = function(json) {
    	checkError(json);
		oSchedTable.fnDraw();
		
		mod.enableUI();
    };
	
	mod.fnAdd = function(aMediaIds, aSchedIds) {
		
		mod.disableUI();
		
		$.post("/showbuilder/schedule-add", 
			{"format": "json", "mediaIds": aMediaIds, "schedIds": aSchedIds}, 
			mod.fnItemCallback
		);
	};
	
	mod.fnMove = function(aSelect, aAfter) {
		
		mod.disableUI();
		
		$.post("/showbuilder/schedule-move", 
			{"format": "json", "selectedItem": aSelect, "afterItem": aAfter},  
			mod.fnItemCallback
		);
	};
	
	mod.fnRemove = function(aItems) {
		
		mod.disableUI();
		
		$.post( "/showbuilder/schedule-remove",
			{"items": aItems, "format": "json"},
			mod.fnItemCallback
		);
	};
	
	mod.fnRemoveSelectedItems = function() {
		var aData = mod.getSelectedData(),
			i,
			length,
			temp,
			aItems = [];

		for (i=0, length = aData.length; i < length; i++) {
			temp = aData[i];
			aItems.push({"id": temp.id, "instance": temp.instance, "timestamp": temp.timestamp}); 	
		}
		
		mod.fnRemove(aItems);
	};
	
	mod.fnServerData = function fnBuilderServerData( sSource, aoData, fnCallback ) {
		
		aoData.push( { name: "timestamp", value: mod.getTimestamp()} );
		aoData.push( { name: "instances", value: mod.getShowInstances()} );
		aoData.push( { name: "format", value: "json"} );
		
		if (mod.fnServerData.hasOwnProperty("start")) {
			aoData.push( { name: "start", value: mod.fnServerData.start} );
		}
		if (mod.fnServerData.hasOwnProperty("end")) {
			aoData.push( { name: "end", value: mod.fnServerData.end} );
		}
		if (mod.fnServerData.hasOwnProperty("ops")) {
			aoData.push( { name: "myShows", value: mod.fnServerData.ops.myShows} );
			aoData.push( { name: "showFilter", value: mod.fnServerData.ops.showFilter} );
		}
		
		$.ajax({
			"dataType": "json",
			"type": "POST",
			"url": sSource,
			"data": aoData,
			"success": function(json) {
				mod.setTimestamp(json.timestamp);
				mod.setShowInstances(json.instances);
				fnCallback(json);
			}
		});
	};
	
	mod.builderDataTable = function() {
		$sbContent = $('#show_builder');
		$lib = $("#library_content"),
		$sbTable = $sbContent.find('table');
		
		/*
         * Icon hover states in the toolbar.
         */
		$sbContent.on("mouseenter", ".fg-toolbar ul li", function(ev) {
        	$el = $(this);
        	
        	if (!$el.hasClass("ui-state-disabled")) {
        		$el.addClass("ui-state-hover");
        	}     	
        });
		$sbContent.on("mouseleave", ".fg-toolbar ul li", function(ev) {
        	$el = $(this);
        	
        	if (!$el.hasClass("ui-state-disabled")) {
        		$el.removeClass("ui-state-hover");
        	} 
        });
		
		oSchedTable = $sbTable.dataTable( {
			"aoColumns": [
		    /* checkbox */ {"mDataProp": "allowed", "sTitle": "", "sWidth": "15px", "sClass": "sb-checkbox"},
            /* Type */ {"mDataProp": "image", "sTitle": "", "sClass": "library_image sb-image", "sWidth": "16px"},
	        /* starts */ {"mDataProp": "starts", "sTitle": "Start", "sClass": "sb-starts", "sWidth": "60px"},
	        /* ends */ {"mDataProp": "ends", "sTitle": "End", "sClass": "sb-ends", "sWidth": "60px"},
	        /* runtime */ {"mDataProp": "runtime", "sTitle": "Duration", "sClass": "library_length sb-length", "sWidth": "65px"},
	        /* title */ {"mDataProp": "title", "sTitle": "Title", "sClass": "sb-title"},
	        /* creator */ {"mDataProp": "creator", "sTitle": "Creator", "sClass": "sb-creator"},
	        /* album */ {"mDataProp": "album", "sTitle": "Album", "sClass": "sb-album"},
	        /* cue in */ {"mDataProp": "cuein", "sTitle": "Cue In", "bVisible": false, "sClass": "sb-cue-in"},
	        /* cue out */ {"mDataProp": "cueout", "sTitle": "Cue Out", "bVisible": false, "sClass": "sb-cue-out"},
	        /* fade in */ {"mDataProp": "fadein", "sTitle": "Fade In", "bVisible": false, "sClass": "sb-fade-in"},
	        /* fade out */ {"mDataProp": "fadeout", "sTitle": "Fade Out", "bVisible": false, "sClass": "sb-fade-out"}
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
	        "fnStateSave": function fnStateSave(oSettings, oData) {
	           
	        	localStorage.setItem('datatables-timeline', JSON.stringify(oData));
	        	
	    		$.ajax({
				  url: "/usersettings/set-timeline-datatable",
				  type: "POST",
				  data: {settings : oData, format: "json"},
				  dataType: "json"
				});
	        },
	        "fnStateLoad": function fnBuilderStateLoad(oSettings) {
	        	var settings = localStorage.getItem('datatables-timeline');
            	
            	if (settings !== "") {
            		return JSON.parse(settings);
            	} 	
	        },
	        "fnStateLoadParams": function (oSettings, oData) {
	        	var i,
					length,
					a = oData.abVisCols;
			
	        	//putting serialized data back into the correct js type to make
	        	//sure everything works properly.
		        for (i = 0, length = a.length; i < length; i++) {	
		        	if (typeof(a[i]) === "string") {
                		a[i] = (a[i] === "true") ? true : false;
                	}
		        }
		        
		        a = oData.ColReorder;
		        for (i = 0, length = a.length; i < length; i++) {	
		        	if (typeof(a[i]) === "string") {
                		a[i] = parseInt(a[i], 10);
                	}
		        }
		       
		        oData.iCreate = parseInt(oData.iCreate, 10);
	        },
	        
			"fnServerData": mod.fnServerData,
			"fnRowCallback": function fnRowCallback( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
				var i, length,
					sSeparatorHTML,
					fnPrepareSeparatorRow,
					$node,
					cl="",
					//background-color to imitate calendar color.
					r,g,b,a,
					$nRow = $(nRow),
					$image,
					$div,
					headerIcon;
				
				fnPrepareSeparatorRow = function fnPrepareSeparatorRow(sRowContent, sClass, iNodeIndex) {
					
					$node = $(nRow.children[iNodeIndex]);
					$node.html(sRowContent);
					$node.attr('colspan',100);
					for (i = iNodeIndex + 1, length = nRow.children.length; i < length; i = i+1) {
						$node = $(nRow.children[i]);
						$node.html("");
						$node.attr("style", "display : none");
					}
					
					$nRow.addClass(sClass);
				};
				         
				if (aData.header === true) {
					//remove the column classes from all tds.
					$nRow.find('td').removeClass();
					
					$node = $(nRow.children[0]);
					$node.html("");
					cl = 'sb-header';
					
					if (aData.record === true) {
						
						headerIcon =  (aData.soundcloud_id > 0) ? "soundcloud" : "recording";
						
						$div = $("<div/>", {
							"class": "small-icon " + headerIcon
						});
						$node.append($div);
					}
					else if (aData.rebroadcast === true) {
						$div = $("<div/>", {
							"class": "small-icon rebroadcast"
						});
						$node.append($div);
					}
					
					sSeparatorHTML = '<span class="show-title">'+aData.title+'</span>';
					
					if (aData.rebroadcast === true) {
						sSeparatorHTML += '<span>'+aData.rebroadcast_title+'</span>';
					}
					
					sSeparatorHTML += '<span class="push-right">';
					
					if (aData.startDate === aData.endDate) {
						sSeparatorHTML += '<span class="show-date">'+aData.startDate+'</span><span class="show-time">'+aData.startTime+'</span>';
						sSeparatorHTML +='-<span class="show-time">'+aData.endTime+'</span>';
					}
					else {
						sSeparatorHTML += '<span class="show-date">'+aData.startDate+'</span><span class="show-time">'+aData.startTime+'</span>';
						sSeparatorHTML +='-<span class="show-date">'+aData.endDate+'</span><span class="show-time">'+aData.endTime+'</span>';
					}
					
					sSeparatorHTML += '</span>';
					
					fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
				}
				else if (aData.footer === true) {
					//remove the column classes from all tds.
					$nRow.find('td').removeClass();
					
					$node = $(nRow.children[0]);
					cl = 'sb-footer';
					
					//check the show's content status.
					if (aData.runtime > 0) {
						$node.html('<span class="ui-icon ui-icon-check"></span>');
						cl = cl + ' ui-state-highlight';
					}
					else {
						$node.html('<span class="ui-icon ui-icon-notice"></span>');
						cl = cl + ' ui-state-error';
					}
						
					sSeparatorHTML = '<span>'+aData.fRuntime+'</span>';
					fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
				}
				else if (aData.empty === true) {
					//remove the column classes from all tds.
					$nRow.find('td').removeClass();
					
					$node = $(nRow.children[0]);
					$node.html('');
					
					sSeparatorHTML = '<span>Show Empty</span>';
					cl = cl + " sb-empty odd";
					
					fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
				}
				else if (aData.record === true) {
					//remove the column classes from all tds.
					$nRow.find('td').removeClass();
					
					$node = $(nRow.children[0]);
					$node.html('');
					
					sSeparatorHTML = '<span>Recording From Line In</span>';
					cl = cl + " sb-record odd";
					
					fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
				}
				else {
					
					 //add the play function if the file exists on disk.
					$image = $nRow.find('td.sb-image');
					//check if the file exists.
					if (aData.image === true) {
						$image.html('<img title="Track preview" src="/css/images/icon_audioclip.png"></img>')
							.click(function() {
			                    open_show_preview(aData.instance, aData.pos);
			                    return false;
			                });
					}
					else {
						$image.html('<span class="ui-icon ui-icon-alert"></span>');
					}
					
					$node = $(nRow.children[0]);
					if (aData.allowed === true && aData.scheduled >= 1) {
						$node.html('<input type="checkbox" name="'+aData.id+'"></input>');
					}
					else {
						$node.html('');
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
				$nRow.data({"aData": aData});
				
				if (aData.scheduled === 1) {
					$nRow.addClass(NOW_PLAYING_CLASS);
				}
				else if (aData.scheduled === 0) {
					$nRow.addClass("sb-past");
				}
				else {
					$nRow.addClass("sb-future");
				}
				
				if (aData.allowed !== true) {
					$nRow.addClass("sb-not-allowed");
				}
				else {
					$nRow.addClass("sb-allowed");
				}
				
				//status used to colour tracks.
				if (aData.status === 2) {
					$nRow.addClass("sb-boundry");
				}
				else if (aData.status === 0) {
					$nRow.addClass("sb-over");
				}
				
				if (aData.currentShow === true) {
					$nRow.addClass("sb-current-show");
				}
                 
                //call the context menu so we can prevent the event from propagating.
                $nRow.find('td:gt(1)').click(function(e){
                    
                    $(this).contextMenu({x: e.pageX, y: e.pageY});
                    
                    return false;
                });
			},
			//remove any selected nodes before the draw.
			"fnPreDrawCallback": function( oSettings ) {
				
				//make sure any dragging helpers are removed or else they'll be stranded on the screen.
				$("#draggingContainer").remove();
		    },
			"fnDrawCallback": function fnBuilderDrawCallback(oSettings, json) {
				var wrapperDiv,
					markerDiv,
					$td,
					aData,
					elements,
					i, length, temp,
					$cursorRows,
					$table = $(this),
					$parent = $table.parent(),
					//use this array to cache DOM heights then we can detach the table to manipulate it to increase speed.
					heights = [];
				
				clearTimeout(mod.timeout);
				
				//only create the cursor arrows if the library is on the page.
				if ($lib.length > 0 && $lib.filter(":visible").length > 0) {

					$cursorRows = $sbTable.find("tbody tr.sb-future.sb-allowed:not(.sb-header, .sb-empty)");
					
					//need to get heights of tds while elements are still in the DOM.
					for (i = 0, length = $cursorRows.length; i < length; i++) {
						$td = $($cursorRows.get(i)).find("td:first");
						heights.push($td.height());
					}
					
					//detach the table to increase speed.
					$table.detach();
					
					for (i = 0, length = $cursorRows.length; i < length; i++) {
						
						$td = $($cursorRows.get(i)).find("td:first");
				    	if ($td.hasClass("dataTables_empty")) {
				    		$parent.append($table);
				    		return false;
				    	}
				    	
				    	wrapperDiv = $("<div />", {
				    		"class": "innerWrapper",
				    		"css": {
				    			"height": heights[i]
				    		}
				    	});
				    	markerDiv = $("<div />", {
				    		"class": "marker"
				    	});
				    	
			    		$td.append(markerDiv).wrapInner(wrapperDiv);
					}
					
					//if there is only 1 cursor on the page highlight it by default.
					if ($cursorRows.length === 1) {
						$td = $cursorRows.find("td:first");
				    	if (!$td.hasClass("dataTables_empty")) {
				    		$cursorRows.addClass("cursor-selected-row");
				    	}
					}
					
					$parent.append($table);
				}
				
				//order of importance of elements for setting the next timeout.
				elements = [
				    $sbTable.find("tr."+NOW_PLAYING_CLASS),
				    $sbTable.find("tbody").find("tr.sb-future.sb-footer, tr.sb-future.sb-header").filter(":first")
				];
				
				//check which element we should set a timeout relative to.
				for (i = 0, length = elements.length; i < length; i++) {
					temp = elements[i];
					
					if (temp.length > 0) {
						aData = temp.data("aData");
						
						mod.timeout = setTimeout(mod.refresh, aData.refresh * 1000); //need refresh in milliseconds
						break;
					}
				}
				
				mod.checkToolBarIcons();
		    },
		    
			"oColVis": {
				"aiExclude": [ 0, 1 ]
			},
			
			"oColReorder": {
				"iFixedColumns": 2
			},
			
	        // R = ColReorderResize, C = ColVis
	        "sDom": 'R<"dt-process-rel"r><"sb-padded"<"H"C>><"dataTables_scrolling sb-padded"t>',
	        
	        "sAjaxDataProp": "schedule",
			"sAjaxSource": "/showbuilder/builder-feed"	
		});
		
		$sbTable.find("tbody").on("click", "input:checkbox", function(ev) {
        	
        	var $cb = $(this),
        		$tr = $cb.parents("tr"),
        		$prev;
        	
        	if ($cb.is(":checked")) {
        		
        		if (ev.shiftKey) {
        			$prev = $sbTable.find("tbody").find("tr."+SB_SELECTED_CLASS).eq(-1);
        			
        			$prev.nextUntil($tr)
        				.addClass(SB_SELECTED_CLASS)
        				.find("input:checkbox")
        					.attr("checked", true)
        					.end();
        		}
        		
        		$tr.addClass(SB_SELECTED_CLASS);
        	}
        	else {
        		$tr.removeClass(SB_SELECTED_CLASS);
        	}
        	
        	mod.checkToolBarIcons();
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
				
				mod.fnAdd(aMediaIds, aSchedIds);
			};
			
			fnMove = function() {
				var aSelect = [],
					aAfter = [];
				
				for(i = 0; i < helperData.length; i++) {
					aSelect.push({"id": helperData[i].id, "instance": helperData[i].instance, "timestamp": helperData[i].timestamp});
				}
			
				aAfter.push({"id": oPrevData.id, "instance": oPrevData.instance, "timestamp": oPrevData.timestamp});
		
				mod.fnMove(aSelect, aAfter);
			};
			
			fnReceive = function(event, ui) {
				var aItems = [];
				
				AIRTIME.library.addToChosen(ui.item);
			
				aItems = AIRTIME.library.getSelectedData();
				origTrs = aItems;
				html = ui.helper.html();
				
				AIRTIME.library.removeFromChosen(ui.item);
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
					var selected = mod.getSelectedData(NOW_PLAYING_CLASS),	    	
				    	thead = $("#show_builder_table thead"),
				    	colspan = thead.find("th").length,
				    	trfirst = thead.find("tr:first"),
				    	width = trfirst.width(),
				    	height = trfirst.height(),
				    	message;
					
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
					/*
					var elements = $sbTable.find('tr input:checked').parents('tr')
						.not(ui.item)
						.not("."+NOW_PLAYING_CLASS);
					
					//remove all other items from the screen, 
					//don't remove ui.item or else we can not get position information when the user drops later.
					elements.remove();
					*/
					
					var elements = $sbTable.find('tr input:checked').parents('tr');
					
					elements.hide();
				}
			};
		}());
		
		$sbTable.sortable(sortableConf);
		
		//start setup of the builder toolbar.
		$toolbar = $(".sb-content .fg-toolbar");
		
		$ul = $("<ul/>");
		$ul.append('<li class="ui-state-default sb-button-select" title="Select"><span class="ui-icon ui-icon-document-b"></span></li>')
			.append('<li class="ui-state-default ui-state-disabled sb-button-trim" title="Delete all overbooked tracks"><span class="ui-icon ui-icon-scissors"></span></li>')
			.append('<li class="ui-state-default ui-state-disabled sb-button-delete" title="Delete selected scheduled items"><span class="ui-icon ui-icon-trash"></span></li>');	
		$toolbar.append($ul);
		
		$ul = $("<ul/>");
		$ul.append('<li class="ui-state-default ui-state-disabled sb-button-current" title="Jump to the currently playing track"><span class="ui-icon ui-icon-arrowstop-1-s"></span></li>')
			.append('<li class="ui-state-default ui-state-disabled sb-button-cancel" title="Cancel current show"><span class="ui-icon ui-icon-eject"></span></li>');
		$toolbar.append($ul);
		$ul = undefined;
		
		$.contextMenu({
            selector: '#show_builder .ui-icon-document-b',
            trigger: "left",
            ignoreRightClick: true,
            items: {
                "sa": {name: "Select All", callback: mod.selectAll},
                "sn": {name: "Select None", callback: mod.selectNone}
            }
        });
		
		//jump to current
		$toolbar.find('.sb-button-cancel')
			.click(function() {
				var $tr,
					data,
					msg = 'Cancel Current Show?';
				
				if (AIRTIME.button.isDisabled('sb-button-cancel') === true) {
					return;
				}
				
				$tr = $sbTable.find('tr.sb-future:first');
				
				if ($tr.hasClass('sb-current-show')) {
					data = $tr.data("aData");
					
					if (data.record === true) {
						msg = 'Stop recording current show?';
					}
					
					if (confirm(msg)) {
				        var url = "/Schedule/cancel-current-show";
				        $.ajax({
				        	url: url,
				        	data: {format: "json", id: data.instance},
				        	success: function(data){
				        		var oTable = $sbTable.dataTable();
				        		oTable.fnDraw();
				        	}
				        });
				    }
				}	
			});
		
		//jump to current
		$toolbar.find('.sb-button-current')
			.click(function() {
				
				if (AIRTIME.button.isDisabled('sb-button-current') === true) {
					return;
				}
				
				var $scroll = $sbContent.find(".dataTables_scrolling"),
					scrolled = $scroll.scrollTop(),
					scrollingTop = $scroll.offset().top,
					current = $sbTable.find("."+NOW_PLAYING_CLASS),
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
					trs = $sbTable.find(".sb-over.sb-future.sb-allowed");
		
				trs.each(function(){
					temp = $(this).data("aData");
					aItems.push({"id": temp.id, "instance": temp.instance, "timestamp": temp.timestamp}); 	
				});
				
				mod.fnRemove(aItems);
			});
		
		//delete selected tracks
		$toolbar.find('.sb-button-delete')
			.click(function() {
				
				if (AIRTIME.button.isDisabled('sb-button-delete') === true) {
					return;
				}
				
				mod.fnRemoveSelectedItems();
			});
		
		//add events to cursors.
		$sbTable.find("tbody").on("click", "div.marker", function(event) {
			var $tr = $(this).parents("tr"),
				$trs;
			
			if ($tr.hasClass(CURSOR_SELECTED_CLASS)) {
				mod.removeCursor($tr);
			}
			else {
				mod.selectCursor($tr);
			}
			
			if (event.ctrlKey === false) {
				$trs = $sbTable.find('.'+CURSOR_SELECTED_CLASS).not($tr);
				mod.removeCursor($trs);
			}
			
			return false;
		});
		
		//begin context menu initialization.
        $.contextMenu({
            selector: '.sb-content table tbody tr:not(.sb-empty, .sb-footer, .sb-header, .sb-record) td:not(.sb-checkbox, .sb-image)',
            trigger: "left",
            ignoreRightClick: true,
            
            build: function($el, e) {
                var items,  
	                $tr = $el.parent(),
	                data = $tr.data("aData"), 
	                cursorClass = "cursor-selected-row",
	                callback;

                function processMenuItems(oItems) {
                	
                	//define a preview callback.
                    if (oItems.preview !== undefined) {
                        
                        callback = function() {
                        	open_show_preview(data.instance, data.pos);
                        };
                        
                        oItems.preview.callback = callback;
                    }
                    
                	//define a select cursor callback.
                    if (oItems.selCurs !== undefined) {
                        
                        callback = function() {
                        	var $tr = $(this).parents('tr').next();
                        	
                        	mod.selectCursor($tr);
                        };
                        
                        oItems.selCurs.callback = callback;
                    }
                    
                   //define a remove cursor callback.
                    if (oItems.delCurs !== undefined) {
                        
                        callback = function() {
                        	var $tr = $(this).parents('tr').next();
                        	
                        	mod.removeCursor($tr);
                        };
                        
                        oItems.delCurs.callback = callback;
                    }
                    
                    //define a delete callback.
                    if (oItems.del !== undefined) {
                        
                        callback = function() {
                        	if (confirm("Delete selected Items?")) {
                        		AIRTIME.showbuilder.fnRemove([{
                                	id: data.id,
                                	timestamp: data.timestamp,
                                	instance: data.instance
                                }]);
                        	} 
                        };
                        
                        oItems.del.callback = callback;
                    }
                    
                    //only show the cursor selecting options if the library is visible on the page.
                    if ($tr.next().find('.marker').length === 0) {
                    	delete oItems.selCurs;
                    	delete oItems.delCurs;
                    }
                	//check to include either select or remove cursor.
                    else {
                		if ($tr.next().hasClass(cursorClass)) {
                    		delete oItems.selCurs;
                    	}
                    	else {
                    		delete oItems.delCurs;
                    	}
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
	
	return AIRTIME;
	
}(AIRTIME || {}));