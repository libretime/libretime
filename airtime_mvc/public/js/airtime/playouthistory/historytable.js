var AIRTIME = (function(AIRTIME) {
    var mod;
    
    if (AIRTIME.history === undefined) {
        AIRTIME.history = {};
    }
    mod = AIRTIME.history;
    
    var $historyContentDiv;
        
    var lengthMenu = [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, $.i18n._("All")]];
    
    var sDom = 'l<"dt-process-rel"r><"H"><"dataTables_scrolling"t><"F"ip>';
    
    var selectedLogItems = {};
    
    var dateStartId = "#his_date_start",
		timeStartId = "#his_time_start",
		dateEndId = "#his_date_end",
		timeEndId = "#his_time_end",
    
		oTableAgg,
		oTableItem,
		oTableShow,
		inShowsTab = false;
    
    function validateTimeRange() {
    	var oRange,
    		inputs = $('.his-timerange > input'),
    		start, end;
 
    	oRange = AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);
 
    	start = oRange.start;
    	end = oRange.end;
    
    	if (end >= start) {
    		inputs.removeClass('error');
    	}
    	else {
    		inputs.addClass('error');
		}
        
        return {
        	start: start,
       	 	end: end,
       	 	isValid: end >= start
        };
   }
    
    function getSelectedLogItems() {
    	var items = Object.keys(selectedLogItems);
    	
    	return items;
    }
    
    function addSelectedLogItem($el) {
    	var id;
    	
    	$el.addClass("his-selected");
    	id = $el.data("his-id");
    	selectedLogItems[id] = "";
    }
    
    function removeSelectedLogItem($el) {
    	var id;
    	
    	$el.removeClass("his-selected");
    	id = $el.data("his-id");
    	delete selectedLogItems[id];
    }
    
    function emptySelectedLogItems() {
    	var $inputs = $historyContentDiv.find(".his_checkbox").find("input");
    	
    	$inputs.prop('checked', false);
    	$inputs.parents("tr").removeClass("his-selected");
		
    	selectedLogItems = {};
    }
    
    function selectCurrentPage(e) {
    	var $ctx = $(e.currentTarget).parents("div.dataTables_wrapper"),
    		$inputs = $ctx.find(".his_checkbox").find("input"),
    		$tr, 
    		$input;
    	
    	$.each($inputs, function(index, input) {
    		$input = $(input);
    		$input.prop('checked', true);
    		$tr = $input.parents("tr");
    		addSelectedLogItem($tr);
		});
    }
    
    function deselectCurrentPage(e) {
    	var $ctx = $(e.currentTarget).parents("div.dataTables_wrapper"),
    		$inputs = $ctx.find(".his_checkbox").find("input"),
			$tr, 
			$input;
		
		$.each($inputs, function(index, input) {
			$input = $(input);
			$input.prop('checked', false);
			$tr = $input.parents("tr");
			removeSelectedLogItem($tr);
		});
    }
    
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
    
    /* This callback can be used for all history tables */
    function fnServerData( sSource, aoData, fnCallback ) {
    	
    	if (fnServerData.hasOwnProperty("start")) {
			aoData.push( { name: "start", value: fnServerData.start} );
		}
		if (fnServerData.hasOwnProperty("end")) {
			aoData.push( { name: "end", value: fnServerData.end} );
		}
		if (fnServerData.hasOwnProperty("instance")) {
			aoData.push( { name: "instance_id", value: fnServerData.instance} );
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
    
    function createShowAccordSection(config) {
    	var template,
    		$el;
    	
    	template = 
    		"<h3>" +
    	      "<a href='#'>" +
    	        "<span class='show-title'><%= name %></span>" +
    	        "<span class='push-right'>" +
    	          "<span class='show-date'><%= date %></span>" +
  			      "<span class='show-time'><%= startTime %></span>" +
  			      "-" +
  			      "<span class='show-time'><%= endTime %></span>" +
  			    "</span>" +
    	      "</a>" +
    	    "</h3>" +
    	 "<div " +
    	    "data-instance='<%= instance %>' " +
    	 "></div>";
    	
    	template = _.template(template);
    	$el = $(template(config));
    	
    	return $el;
    }
    
    //$el is the div in the accordian we should create the table on.
    function createShowTable($el) {
    	
    	var instance = $el.data("instance");
    	var $table = $("<table/>", {
			'cellpadding': "0", 
			'cellspacing': "0", 
			'class': "datatable",
			'id': "history_table_show"
		});
    	
    	//assign the retrieval function the show instance id.
    	fnServerData.instance = instance;
    	$el.append($table);
    	$el.css("height", "auto");
    	oTableShow = itemHistoryTable("history_table_show");
    }
    
    function drawShowList(oShows) {
    	var $showList = $historyContentDiv.find("#history_show_summary"),
    		i, 
    		len, 
    		$accordSection,
    		show,
    		tmp;
    	
    	$showList
    		.accordion( "destroy" )
    		.empty();
    	
    	for (i = 0, len = oShows.length; i < len; i++) {
    		show = oShows[i];
    		tmp = show.starts.split(" ");
    		
    		$accordSection = createShowAccordSection({
    			instance: show.instance_id,
    			name: show.name,
    			date: tmp[0],
    			startTime: tmp[1],
    			endTime: show.ends.split(" ").pop()
    		});
    		
    		$showList.append($accordSection);
    	}
    	
    	$showList.accordion({
    		animated: false,
    		create: function( event, ui ) {
    			var $div = $showList.find(".ui-accordion-content-active");
				console.log(event);
				//$div.css()
    			createShowTable($div);
    		},
		    change: function( event, ui ) {
		    	var $div = $(ui.newContent);
		    	$(ui.oldContent).empty();
		    	createShowTable($div);
		    	selectedLogItems = {};
		    }
		    //changestart: function( event, ui ) {}
		});
    }
    
    function createToolbarButtons ($el) {
        var $menu = $("<div class='btn-toolbar' />");

		$menu.append("<div class='btn-group'>" +
				"<button class='btn btn-small btn-new' id='his_create'>" +
				"<i class='icon-white icon-plus'></i>" +
				$.i18n._("New Log Entry") +
				"</button>" +
			"</div>");

		$menu.append("<div class='btn-group'>" +
            "<button class='btn btn-small dropdown-toggle' data-toggle='dropdown'>" +
                $.i18n._("Select")+" <span class='caret'></span>" +
            "</button>" +
            "<ul class='dropdown-menu'>" +
                "<li class='his-select-page'><a href='#'>"+$.i18n._("Select this page")+"</a></li>" +
                "<li class='his-dselect-page'><a href='#'>"+$.i18n._("Deselect this page")+"</a></li>" +
                "<li class='his-dselect-all'><a href='#'>"+$.i18n._("Deselect all")+"</a></li>" +
            "</ul>" +
        "</div>");
        
        $menu.append("<div class='btn-group'>" +
            "<button class='btn btn-small' id='his_trash'>" +
                "<i class='icon-white icon-trash'></i>" +
            "</button>" +
        "</div>");
                  
        $el.append($menu);
    }
    
    function aggregateHistoryTable() {
        var oTable,
        	$historyTableDiv = $historyContentDiv.find("#history_table_aggregate"),
        	columns,
        	fnRowCallback;

        fnRowCallback = function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
        	var editUrl = baseUrl+"playouthistory/edit-file-item/id/"+aData.file_id,
        		$nRow = $(nRow);
        		
        	$nRow.data('url-edit', editUrl);
        };
        
        columns = JSON.parse(localStorage.getItem('datatables-historyfile-aoColumns'));
        
        oTable = $historyTableDiv.dataTable( {
            
            "aoColumns": columns,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": baseUrl+"playouthistory/file-history-feed",
            "sAjaxDataProp": "history",
            "fnServerData": fnServerData,
            "fnRowCallback": fnRowCallback,
            "oLanguage": getDatatablesStrings({
                "sEmptyTable": $.i18n._("No tracks were played during the selected time period."),
                "sInfoEmpty":      $.i18n._("Showing 0 to 0 of 0 tracks"),
                "sInfo":           $.i18n._("Showing _START_ to _END_ of _TOTAL_ tracks"),
                "sInfoEmpty":      $.i18n._("Showing 0 to 0 of 0 tracks"),
                "sInfoFiltered":   $.i18n._("(filtered from _MAX_ total tracks)"),
            }),
            "aLengthMenu": lengthMenu,
            "iDisplayLength": 25,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": true,
            "sDom": sDom,
        });
        oTable.fnSetFilteringDelay(350);
       
        return oTable;
    }
    
    function itemHistoryTable(id) {
        var oTable,
        	$historyTableDiv = $historyContentDiv.find("#"+id),
        	$toolbar,
        	columns,
        	fnRowCallback,
        	booleans = {},
        	i, c;
        
        columns = JSON.parse(localStorage.getItem('datatables-historyitem-aoColumns'));
        
        for (i in columns) {
        	
        	c = columns[i];
        	if (c["sDataType"] === "boolean") {
        		booleans[c["mDataProp"]] = c["sTitle"];
        	}
        }

        fnRowCallback = function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
        	var editUrl = baseUrl+"playouthistory/edit-list-item/id/"+aData.history_id,
        		deleteUrl = baseUrl+"playouthistory/delete-list-item/id/"+aData.history_id,
        		emptyCheckBox = String.fromCharCode(parseInt(2610, 16)),
        		checkedCheckBox = String.fromCharCode(parseInt(2612, 16)),
        		b, 
        		text,
        		$nRow = $(nRow);
        	
        	 // add checkbox
            $nRow.find('td.his_checkbox').html("<input type='checkbox' name='cb_"+aData.history_id+"'>");
	
            $nRow.data('his-id', aData.history_id);
            $nRow.data('url-edit', editUrl);
            $nRow.data('url-delete', deleteUrl);
        	
        	for (b in booleans) {
            	
            	text = aData[b] ? checkedCheckBox : emptyCheckBox;
            	text = text + " " + booleans[b];
            	
            	$nRow.find(".his_"+b).html(text);
            }
        };

		oTable = $historyTableDiv.dataTable( {

		"aoColumns": columns,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": baseUrl+"playouthistory/item-history-feed",
            "sAjaxDataProp": "history",
            "fnServerData": fnServerData,
            "fnRowCallback": fnRowCallback,
            "oLanguage": getDatatablesStrings({
                "sEmptyTable": $.i18n._("No tracks were played during the selected time period."),
                "sInfoEmpty":      $.i18n._("Showing 0 to 0 of 0 tracks"),
                "sInfo":           $.i18n._("Showing _START_ to _END_ of _TOTAL_ tracks"),
                "sInfoEmpty":      $.i18n._("Showing 0 to 0 of 0 tracks"),
                "sInfoFiltered":   $.i18n._("(filtered from _MAX_ total tracks)"),
            }),
			"aLengthMenu": lengthMenu,
            "iDisplayLength": 25,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": true,
            "sDom": sDom,
        });
        oTable.fnSetFilteringDelay(350);
        
        $toolbar = $historyTableDiv.parents(".dataTables_wrapper").find(".fg-toolbar:first");
        createToolbarButtons($toolbar);
         
        return oTable;
    }
    
    function showSummaryList(start, end) {
    	var url = baseUrl+"playouthistory/show-history-feed",
    		data = {
    			format: "json",
	    		start: start,
	    	    end: end
	    	};
    	
    	$.post(url, data, function(json) {
    		drawShowList(json);
    	});
    }
    
    mod.onReady = function() {
    	
    	var oBaseDatePickerSettings,
    		oBaseTimePickerSettings,
    		$hisDialogEl,
    		
    		tabsInit = [
    		    {
    		    	initialized: false,
    		    	initialize: function() {
    		    		oTableItem = itemHistoryTable("history_table_list");
    		    	},
    		    	navigate: function() {
    		    		delete fnServerData.instance;
    		    		oTableItem.fnDraw();
    		    	},
    		    	always: function() {
    		    		inShowsTab = false;
    		    		emptySelectedLogItems();
    		    	}
    		    },
    		    {
    		    	initialized: false,
    		    	initialize: function() {
    		    		oTableAgg = aggregateHistoryTable();
    		    	},
    		    	navigate: function() {
    		    		delete fnServerData.instance;
    		    		oTableAgg.fnDraw();
    		    	},
    		    	always: function() {
    		    		inShowsTab = false;
    		    		emptySelectedLogItems();
    		    	}
    		    },
    		    {
    		    	initialized: false,
    		    	initialize: function() {
    		    		
    		    	},
    		    	navigate: function() {
    		    		
    		    	},
    		    	always: function() {
    		    		inShowsTab = true;
    		    		
    		    		var info = getStartEnd();
    		    		showSummaryList(info.start, info.end);
    		    		emptySelectedLogItems();
    		    	}
    		    }
    		];
    	
    	//set the locale names for the bootstrap calendar.
    	$.fn.datetimepicker.dates = {
    		daysMin: i18n_days_short,
    		months: i18n_months,
    		monthsShort: i18n_months_short
    	};
    	
    	
    	$historyContentDiv = $("#history_content");
    	
    	function redrawTables() {
    		oTableAgg && oTableAgg.fnDraw();
    		oTableItem && oTableItem.fnDraw();
    		oTableShow && oTableShow.fnDraw();
    	}
    	
    	function removeHistoryDialog() {
    		$hisDialogEl.dialog("destroy");
        	$hisDialogEl.remove();
    	}
    	
    	function initializeDialog() {
    		var $startPicker = $hisDialogEl.find('#his_item_starts_datetimepicker'),
    			$endPicker = $hisDialogEl.find('#his_item_ends_datetimepicker');
    		
        	$startPicker.datetimepicker();

        	$endPicker.datetimepicker({
        		showTimeFirst: true
        	});
        	
        	$startPicker.on('changeDate', function(e) {
        		$endPicker.data('datetimepicker').setLocalDate(e.localDate);	
    		});
    	}
    	
    	function processDialogHtml($el) {
    		
    		if (inShowsTab) {
    			$el.find("#his_choose_instance").remove();
    		}
    		
    		return $el
    	}
    	
    	function makeHistoryDialog(html) {
    		$hisDialogEl = $(html);
    		$hisDialogEl = processDialogHtml($hisDialogEl);
    		
    		$hisDialogEl.dialog({	       
    	        title: $.i18n._("Edit History Record"),
    	        modal: false,
    	        open: function( event, ui ) {
    	        	initializeDialog();	
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
    	
    	oBaseDatePickerSettings = {
    		dateFormat: 'yy-mm-dd',
            //i18n_months, i18n_days_short are in common.js
            monthNames: i18n_months,
            dayNamesMin: i18n_days_short,
    		onSelect: function(sDate, oDatePicker) {		
    			$(this).datepicker( "setDate", sDate );
    		},
    		onClose: validateTimeRange
    	};
    	
    	oBaseTimePickerSettings = {
    		showPeriodLabels: false,
    		showCloseButton: true,
            closeButtonText: $.i18n._("Done"),
    		showLeadingZero: false,
    		defaultTime: '0:00',
            hourText: $.i18n._("Hour"),
            minuteText: $.i18n._("Minute"),
            onClose: validateTimeRange
    	};

    	$historyContentDiv.find(dateStartId)
    		.datepicker(oBaseDatePickerSettings)
    		.blur(validateTimeRange);
    	
    	$historyContentDiv.find(timeStartId)
    		.timepicker(oBaseTimePickerSettings)
    		.blur(validateTimeRange);
    	
    	$historyContentDiv.find(dateEndId)
    		.datepicker(oBaseDatePickerSettings)
    		.blur(validateTimeRange);
    	
    	$historyContentDiv.find(timeEndId)
    		.timepicker(oBaseTimePickerSettings)
    		.blur(validateTimeRange);
    	
    	$historyContentDiv.on("click", "#his_create", function(e) {
    		var url = baseUrl+"playouthistory/edit-list-item/format/json"	;
    		
    		e.preventDefault();
    		
    		$.get(url, function(json) {
    			
    			makeHistoryDialog(json.dialog);
    			
    		}, "json");
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
    				redrawTables();
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
    			url,
    			$select = $hisDialogEl.find("#his_instance_select"),
    			instance;
    		
    		url = (id === "") ? createUrl : updateUrl;
    		
    		if (fnServerData.instance !== undefined) {
    			data.push({
    				name: "instance_id",
    				value: fnServerData.instance
    			});
    		}
    		else if ($select.length > 0) {
    			instance = $select.val();
    			
    			if (instance > 0) {
    				data.push({
        				name: "instance_id",
        				value: instance
        			});
    			}		
    		}
    				
    		$.post(url, data, function(json) {
    			
    			if (json.form !== undefined) {
    				var $newForm = $(json.form);
    				$newForm = processDialogHtml($newForm);
    				$hisDialogEl.html($newForm.html());
    				initializeDialog();
    			}
    			else {
    				removeHistoryDialog();
    				redrawTables();
    			}
    		    	
    		}, "json");
    		
    	});
    	
    	
    	$historyContentDiv.on("click", ".his_checkbox input", function(e) {
    		var checked = e.currentTarget.checked,
    			$tr = $(e.currentTarget).parents("tr");
    		
    		if (checked) {
    			addSelectedLogItem($tr);
    		}
    		else {
    			removeSelectedLogItem($tr);
    		}
    	});
    	
    	$('body').on("click", "#his_instance_retrieve", function(e) {
    		var startPicker = $hisDialogEl.find('#his_item_starts'),
				endPicker = $hisDialogEl.find('#his_item_ends'),
				url = baseUrl+"playouthistory/show-history-feed",
				startDate = startPicker.val(),
				endDate = endPicker.val(),
				data;
    		
    		data = {
    			start: startDate,
    			end: endDate,
    			format: "json"
    		};
    		
    		$.get(url, data, function(json) {
    			var i,
    				$select = $('<select/>', {
    					id: 'his_instance_select'
    				}),
    				$option,
    				show;

    			if (json.length > 0) { 				
    				
    				for (i = 0; i < json.length; i++) {
    					show = json[i];
    					
    					$option = $('<option/>')
    						.text(show.name)
    						.attr('value', show.instance_id);
    					
    					$select.append($option);
    				}
    			}
    			
    			$option = $('<option/>')
					.text($.i18n._("No Show"))
					.attr('value', 0);
				
				$select.append($option);
    			
    			$hisDialogEl.find("#his_instance_select").replaceWith($select);
    		});
    	});
    	
    	function getStartEnd() {  		
			
			return AIRTIME.utilities.fnGetScheduleRange(dateStartId, timeStartId, dateEndId, timeEndId);
    	}
    	
    	$historyContentDiv.find("#his_submit").click(function(ev){
    		var fn,
    			info;
    		
    		info = getStartEnd();
    		
    		fn = fnServerData;
    	    fn.start = info.start;
    	    fn.end = info.end;
    	    
    	    if (inShowsTab) {
    	    	showSummaryList(info.start, info.end);
    	    }
    	    else {
    	    	redrawTables();
    	    }  
    	});
    	
    	$historyContentDiv.on("click", ".his-select-page", selectCurrentPage);
    	$historyContentDiv.on("click", ".his-dselect-page", deselectCurrentPage);
    	$historyContentDiv.on("click", ".his-dselect-all", emptySelectedLogItems);
    	
    	$historyContentDiv.on("click", "#his_trash", function(ev){
    		var items = getSelectedLogItems(),
    			url = baseUrl+"playouthistory/delete-list-items";
    		
    		$.post(url, {ids: items, format: "json"}, function() {
    			selectedLogItems = {};
    			redrawTables();
    		});
    	});
    	
    	$historyContentDiv.find("#his-tabs").tabs({
    		show: function( event, ui ) {
    			var href = $(ui.tab).attr("href");
    			var index = href.split('-').pop();
				var tab = tabsInit[index-1];
				
				if (!tab.initialized) {
					tab.initialize();
					tab.initialized = true;
				}
				else {
					tab.navigate();
				}
				
				tab.always();
			}
    	});
    	
    	// begin context menu initialization.
        $.contextMenu({
            selector: '#history_content td:not(.his_checkbox)',
            trigger: "left",
            ignoreRightClick: true,
            
            build: function($el, e) {
                var items = {}, 
                	callback, 
                	$tr,
                	editUrl,
                	deleteUrl;
                
                $tr = $el.parents("tr");
                editUrl = $tr.data("url-edit");
                deleteUrl = $tr.data("url-delete");
                
                if (editUrl !== undefined) {
                	
                	callback = function() {
                    	$.post(editUrl, {format: "json"}, function(json) {
                			
                			makeHistoryDialog(json.dialog);
                			
                		}, "json");
                    };
                    
                    items["edit"] = {
                    	"name": $.i18n._("Edit"),
                    	"icon": "edit",
                    	"callback": callback
                    };
                }
                
                if (deleteUrl !== undefined) {
                	
                	callback = function() {
                    	var c = confirm("Delete this entry?");
                    	
                    	if (c) {
                    		$.post(deleteUrl, {format: "json"}, function(json) {
                    			redrawTables();
                    		});
                    	}	
                    };
                    
                    items["del"] = {
                    	"name": $.i18n._("Delete"),
                    	"icon": "delete",
                    	"callback": callback
                    };
                }
                
                return {
                    items: items
                };
            }
        });
    };
    
return AIRTIME;
    
}(AIRTIME || {}));

$(document).ready(AIRTIME.history.onReady);