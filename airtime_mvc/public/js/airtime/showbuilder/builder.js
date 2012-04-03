var AIRTIME = (function(AIRTIME){
	var mod,
		oSchedTable,
		SB_SELECTED_CLASS = "sb-selected",
		$sbContent,
		$sbTable,
		$toolbar,
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
	
	mod.getSelectedData = function() {
    	var $selected = $sbTable.find("tbody").find("input:checkbox").filter(":checked").parents("tr"),
    		aData = [],
    		i, length,
    		$item;
    	
    	for (i = 0, length = $selected.length; i < length; i++) {
    		$item = $($selected.get(i));
    		aData.push($item.data('aData'));
    	}
    	
    	return aData.reverse();
    };
    
    mod.selectAll = function () {
    	$sbTable.find("input:checkbox").attr("checked", true);
    };
    
    mod.selectNone = function () {
    	$sbTable.find("input:checkbox").attr("checked", false);
    };
	
	mod.fnAdd = function(aMediaIds, aSchedIds) {
		
		$.post("/showbuilder/schedule-add", 
			{"format": "json", "mediaIds": aMediaIds, "schedIds": aSchedIds}, 
			function(json){
				checkError(json);
				oSchedTable.fnDraw();
				AIRTIME.library.selectNone();
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
	
	mod.fnServerData = function ( sSource, aoData, fnCallback ) {
		
		aoData.push( { name: "timestamp", value: AIRTIME.showbuilder.getTimestamp()} );
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
	
	mod.builderDataTable = function() {
		$sbContent = $('#show_builder');
		$lib = $("#library_content"),
		$sbTable = $sbContent.find('table');
		
		oSchedTable = $sbTable.dataTable( {
			"aoColumns": [
		    /* checkbox */ {"mDataProp": "allowed", "sTitle": "<input type='checkbox' name='sb_cb_all'>", "sWidth": "15px", "sClass": "sb-checkbox"},
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
	        "fnStateSave": function (oSettings, oData) {
	           
	    		$.ajax({
				  url: "/usersettings/set-timeline-datatable",
				  type: "POST",
				  data: {settings : oData, format: "json"},
				  dataType: "json"
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
				
				if (aData.currentShow === true) {
					$(nRow).addClass("sb-current-show");
				}
                 
                //call the context menu so we can prevent the event from propagating.
                $(nRow).find('td:gt(1)').click(function(e){
                    
                    $(this).contextMenu({x: e.pageX, y: e.pageY});
                    
                    return false;
                });
			},
			//remove any selected nodes before the draw.
			"fnPreDrawCallback": function( oSettings ) {
				mod.selectNone();
				
				//disable jump to current button.
				AIRTIME.button.disableButton("sb-button-current");
				//disable deleting of overbooked tracks.
				AIRTIME.button.disableButton("sb-button-trim");
				//disable cancelling current show.
				AIRTIME.button.disableButton("sb-button-cancel");
		    },
			"fnDrawCallback": function(oSettings, json) {
				var wrapperDiv,
					markerDiv,
					$td,
					$tr,
					aData,
					elements,
					i, length, temp,
					$cursorRows;
				
				clearTimeout(AIRTIME.showbuilder.timeout);
				
				//only create the cursor arrows if the library is on the page.
				if ($lib.length > 0 && $lib.filter(":visible").length > 0) {
					
					$cursorRows = $sbTable.find("tbody tr:not(.sb-header, .sb-empty, .sb-now-playing, .sb-past, .sb-not-allowed)");
					
					//create cursor arrows.
					$cursorRows.each(function(i, el) {
				    	$td = $(el).find("td:first");
				    	if ($td.hasClass("dataTables_empty")) {
				    		return false;
				    	}
				    	
				    	wrapperDiv = $("<div />", {
				    		"class": "innerWrapper",
				    		"css": {
				    			"height": $td.height()
				    		}
				    	});
				    	markerDiv = $("<div />", {
				    		"class": "marker"
				    	});
				    	
			    		$td.append(markerDiv).wrapInner(wrapperDiv);
				    });
					
					//if there is only 1 cursor on the page highlight it by default.
					if ($cursorRows.length === 1) {
						$td = $cursorRows.find("td:first");
				    	if (!$td.hasClass("dataTables_empty")) {
				    		$cursorRows.addClass("cursor-selected-row");
				    	}
					}
				}
				
				//order of importance of elements for setting the next timeout.
				elements = [
				    $sbTable.find("tr.sb-now-playing"),
				    $sbTable.find("tbody").find("tr.sb-future.sb-footer, tr.sb-future.sb-header").filter(":first")
				];
				
				//check which element we should set a timeout relative to.
				for (i = 0, length = elements.length; i < length; i++) {
					temp = elements[i];
					
					if (temp.length > 0) {
						aData = temp.data("aData");
						
						setTimeout(function(){
							AIRTIME.showbuilder.resetTimestamp();
							oSchedTable.fnDraw();
						}, aData.refresh * 1000); //need refresh in milliseconds
						
						break;
					}
				}
				
				//now playing item exists.
				if (elements[0].length > 0) {
					//enable jump to current button.
					AIRTIME.button.enableButton("sb-button-current");
				}
				
				//check if there are any overbooked tracks on screen to enable the trim button.
				$tr = $sbTable.find("tr.sb-over.sb-future");
				if ($tr.length > 0) {
					//enable deleting of overbooked tracks.
					AIRTIME.button.enableButton("sb-button-trim");
				}
				
				$tr = $sbTable.find('tr.sb-future:first');
				if ($tr.hasClass('sb-current-show')) {
					//enable cancelling current show.
					AIRTIME.button.enableButton("sb-button-cancel");
				}
		    },
			"fnHeaderCallback": function(nHead) {
				$(nHead).find("input[type=checkbox]").attr("checked", false);
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
		
		//adding checkbox events.
		$sbTable.find('[name="sb_cb_all"]').click(function() {
			 var $cbs = $sbTable.find("input:checkbox"),
	         	$trs;
	         
	         if ($(this).is(":checked")) {
	         	$cbs.attr("checked", true);
	         	//checking to enable buttons
	         	
	         	$trs = $cbs.parents("tr");
	     		$trs.addClass(SB_SELECTED_CLASS);
	     		
	             AIRTIME.button.enableButton("sb-button-delete");
	         }
	         else {
	         	$cbs.attr("checked", false);
	         	
	         	$trs = $cbs.parents("tr");
	     		$trs.removeClass(SB_SELECTED_CLASS);
	     		
	         	AIRTIME.button.disableButton("sb-button-delete");
	         }       
	    });
		
		$sbTable.find("tbody").on("click", "input:checkbox", function() {
        	
        	var $cb = $(this),
        		$selectedCb,
        		$tr = $cb.parents("tr");
        	
        	if ($cb.is(":checked")) {
        		
        		$tr.addClass(SB_SELECTED_CLASS);
        		//checking to enable buttons
                AIRTIME.button.enableButton("sb-button-delete");
        	}
        	else {
        		$selectedCb = $sbTable.find("tbody input:checkbox").filter(":checked");
        		$tr.removeClass(SB_SELECTED_CLASS);
        		
        		//checking to disable buttons
                if ($selectedCb.length === 0) {
                    AIRTIME.button.disableButton("sb-button-delete");
                }
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
				var aItems = [];
			
				aItems = AIRTIME.library.getSelectedData();
				
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
					var selected = mod.getSelectedData(),	    	
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
					var elements = $sbTable.find('tr:not(:first) input:checked').parents('tr');
					
					elements.hide();
				}
			};
		}());
		
		$sbTable.sortable(sortableConf);
		
		//start setup of the builder toolbar.
		$toolbar = $(".sb-content .fg-toolbar");
		
		$ul = $("<ul/>");
		$ul.append('<li class="ui-state-default ui-state-disabled sb-button-trim" title="delete all overbooked tracks"><span class="ui-icon ui-icon-scissors"></span></li>')
			.append('<li class="ui-state-default ui-state-disabled sb-button-delete" title="delete selected items"><span class="ui-icon ui-icon-trash"></span></li>');	
		$toolbar.append($ul);
		
		$ul = $("<ul/>");
		$ul.append('<li class="ui-state-default ui-state-disabled sb-button-current" title="jump to the currently playing track"><span class="ui-icon ui-icon-arrowstop-1-s"></span></li>')
			.append('<li class="ui-state-default ui-state-disabled sb-button-cancel" title="cancel current show"><span class="ui-icon ui-icon-eject"></span></li>');
		$toolbar.append($ul);
		
		//jump to current
		$toolbar.find('.sb-button-cancel')
			.click(function() {
				var $tr,
					data;
				
				if (AIRTIME.button.isDisabled('sb-button-cancel') === true) {
					return;
				}
				
				$tr = $sbTable.find('tr.sb-future:first');
				
				if ($tr.hasClass('sb-current-show')) {
					data = $tr.data("aData");
					
					if (confirm('Cancel Current Show?')) {
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
				
				mod.fnRemoveSelectedItems();
			});
		
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
                            $(this).parents('tr').next().addClass(cursorClass);
                        };
                        
                        oItems.selCurs.callback = callback;
                    }
                    
                   //define a remove cursor callback.
                    if (oItems.delCurs !== undefined) {
                        
                        callback = function() {
                            $(this).parents('tr').next().removeClass(cursorClass);
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