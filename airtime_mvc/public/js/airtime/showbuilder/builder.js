var AIRTIME = (function(AIRTIME){
    var mod,
        oSchedTable,
        SB_SELECTED_CLASS = "sb-selected",
        CURSOR_SELECTED_CLASS = "cursor-selected-row",
        NOW_PLAYING_CLASS = "sb-now-playing",
        $sbContent,
        $sbTable,
        $toolbar,
        $lib,
        builderCheckTimeout,
        internalTimestamp = -1,
        internalShowInstances = [],
        hasGottenFirstAjax = false,
        //check to see if preferences have actually changed or not for datatables
        currentDatatablesSettings = {};
    
    if (AIRTIME.showbuilder === undefined) {
        AIRTIME.showbuilder = {};
    }
    mod = AIRTIME.showbuilder;
   
    mod.timeout = undefined;

    function resetTimestamp() {
        
    	internalTimestamp = -1;
    }
    
    function setTimestamp(timestamp) {
        
    	internalTimestamp = timestamp;
    }

    function getTimestamp() {
        
        return internalTimestamp;
    };
    
    function setShowInstances(showInstances) {
    	internalShowInstances = showInstances;
    };
    
    function getShowInstances() {
        return internalShowInstances;
    };
    
    function checkScheduleUpdates() {
        var data = {},
        	oTable = $('#show_builder_table').dataTable(),
        	fn = oTable.fnSettings().fnServerData,
        	start = fn.start,
        	end = fn.end;

        data["format"] = "json";
        data["start"] = start;
        data["end"] = end;
        data["timestamp"] = getTimestamp();
        data["instances"] = getShowInstances();

        if (fn.hasOwnProperty("ops")) {
            data["myShows"] = fn.ops.myShows;
            data["showFilter"] = fn.ops.showFilter;
            data["showInstanceFilter"] = fn.ops.showInstanceFilter;
        }

        $.ajax( {
            "dataType" : "json",
            "type" : "GET",
            "url" : baseUrl+"showbuilder/check-builder-feed",
            "data" : data,
            "success" : function(json) {
                if (json.update === true) {
                	console.log("need to update builder table.");
                    oTable.fnDraw();
                }
                builderCheckTimeout = setTimeout(checkScheduleUpdates, 5000);
            }
        });
    }
    
    mod.refresh = function() {
       
    	oSchedTable.fnDraw();
    };
    
    mod.checkSelectButton = function() {
        var $selectable = $sbTable.find("tbody").find("input:checkbox"),
        	$button = $(".sb-select");
        
        if ($selectable.length !== 0) {
            AIRTIME.button.enableButton($button);
        }
        else {
            AIRTIME.button.disableButton($button);
        }
    };
    
    mod.checkTrimButton = function() {
        var $over = $sbTable.find(".sb-over.sb-allowed").not(".sb-past"),
        	$button = $(".sb-overbooked");
        
        if ($over.length !== 0) {
            AIRTIME.button.enableButton($button);
        }
        else {
            AIRTIME.button.disableButton($button);
        }
    };
    
    mod.checkDeleteButton = function() {
        var $selected = $sbTable.find("tbody").find("input:checkbox").filter(":checked"),
        	$button = $(".sb-trash");
        
        if ($selected.length !== 0) {
            AIRTIME.button.enableButton($button);
        }
        else {
            AIRTIME.button.disableButton($button);
        }
    };
    
    mod.checkJumpToCurrentButton = function() {
        var $current = $sbTable.find("."+NOW_PLAYING_CLASS),
        	$button = $(".sb-current");
        
        if ($current.length !== 0) {
            AIRTIME.button.enableButton($button);
        }
        else {
            AIRTIME.button.disableButton($button);
        }
    };
    
    mod.checkCancelButton = function() {
    	
        var $current = $sbTable.find(".sb-current-show"),
            //this user type should be refactored into a separate users module later
            //when there's more time and more JS will need to know user data.
            userType = localStorage.getItem('user-type'),
            canCancel = false,
            $button = $(".sb-cancel");
        
        if ($current.length !== 0 && $current.hasClass("sb-allowed")) {
        	canCancel = true;
        }
        else if ($current.length !== 0 && (userType === 'A' || userType === 'P')) {
            canCancel = true;
        }
       
        if (canCancel === true) {
        	AIRTIME.button.enableButton($button);
        }
        else {
        	AIRTIME.button.disableButton($button);
        }
    };
    
    mod.checkToolBarIcons = function() {
    	
    	//library may not be on the page.
    	if (AIRTIME.library !== undefined) {
    		//AIRTIME.library.checkAddButton();
    	}
        
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
    	//don't bother checking to update schedule while action is being performed.
    	clearTimeout(builderCheckTimeout);

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
        
        builderCheckTimeout = setTimeout(checkScheduleUpdates, 5000);
    };
    
    mod.fnRedrawSchedule = function(json) {
        
        oSchedTable.fnDraw();
        mod.enableUI();
    };
    
    mod.fnAdd = function(aMediaIds, aSchedIds) {
        mod.disableUI();
        
        $.post(baseUrl+"showbuilder/schedule-add", 
            {"format": "json", "mediaIds": aMediaIds, "schedIds": aSchedIds}, 
            mod.fnRedrawSchedule
        );
    };
    
    mod.fnMove = function(aSelect, aAfter) {
        
        mod.disableUI();
        
        $.post(baseUrl+"showbuilder/schedule-move", 
            {"format": "json", "selectedItem": aSelect, "afterItem": aAfter},  
            mod.fnRedrawSchedule
        );
    };
    
    mod.fnRemove = function(aItems) {
        
        mod.disableUI();
        if (confirm($.i18n._("Delete selected item(s)?"))) {
	        $.post( baseUrl+"showbuilder/schedule-remove",
	            {"items": aItems, "format": "json"},
	            mod.fnRedrawSchedule
	        );
        }
        else {
        	mod.enableUI();
        }
    };
    
    mod.fnRemoveSelectedItems = function() {
        var aData = mod.getSelectedData(),
            i,
            length,
            temp,
            aItems = [];

        for (i = 0, length = aData.length; i < length; i++) {
            temp = aData[i];
            aItems.push({"id": temp.id, "instance": temp.instance, "timestamp": temp.timestamp});   
        }
        
        mod.fnRemove(aItems);
    };
    
    mod.fnServerData = function fnBuilderServerData( sSource, aoData, fnCallback ) {
    	
    	clearTimeout(mod.timeout);

    	console.log("getting builder feed.");
       
        aoData.push( { name: "timestamp", value: getTimestamp()} );
        aoData.push( { name: "instances", value: getShowInstances()} );
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
            aoData.push( { name: "showInstanceFilter", value: mod.fnServerData.ops.showInstanceFilter} );
        }
        
        $.ajax({
            "dataType": "json",
            "type": "POST",
            "url": sSource,
            "data": aoData,
            "success": function(json) {
            	if (json.error !== undefined) {
                    alert(json.error);
                }
            	else {
            		fnCallback(json);
            		setTimestamp(json.timestamp);
                    setShowInstances(json.instances);
                    
                    if (!hasGottenFirstAjax) {
                    	
                    	hasGottenFirstAjax = true;
                    	
                    	//start this timeout
                    	//check if the timeline view needs updating.
                    	builderCheckTimeout = setTimeout(checkScheduleUpdates, 5000);
                    }
            	}   
            }
        });
    };
    
    mod.jumpToCurrentTrack = function() {
        var $scroll = $sbContent.find(".dataTables_scrolling");
        var scrolled = $scroll.scrollTop();
        var scrollingTop = $scroll.offset().top;
        var oTable = $('#show_builder_table').dataTable();
        var current = $sbTable.find("."+NOW_PLAYING_CLASS);
        var currentTop = current.offset().top;

        $scroll.scrollTop(currentTop - scrollingTop + scrolled);
    };
    
    function stackTrace() {
        var err = new Error();
        console.log(err.stack);
    }
    
    function builderNeedSave(oData) {
    	var settings = currentDatatablesSettings;
    	
    	//This will happen when we have nothing saved in cc_pref for this table.
    	if (settings["abVisCols"] === undefined || settings["ColReorder"] === undefined) {
    		
    		settings["abVisCols"] = oData.abVisCols;
    		settings["ColReorder"] = oData.ColReorder;
    		
    		return true;
    	}
    	
    	if (settings["abVisCols"].join() === oData.abVisCols.join()
    			&& settings["ColReorder"].join() === oData.ColReorder.join()) {
    		return false;
    	}
    	
    	return true;
    }
    
    mod.builderDataTable = function() {
        $sbContent = $('#show_builder');
        $lib = $("#library_content"),
        $sbTable = $sbContent.find('table');
        
        oSchedTable = $sbTable.dataTable( {
            "aoColumns": [
            /* checkbox */ {"mDataProp": "allowed", "sTitle": "", "sWidth": "15px", "sClass": "sb-checkbox"},
            /* Type */ {"mDataProp": "image", "sTitle": "", "sClass": "library_image sb-image", "sWidth": "16px"},
            /* starts */ {"mDataProp": "starts", "sTitle": $.i18n._("Start"), "sClass": "sb-starts", "sWidth": "60px"},
            /* ends */ {"mDataProp": "ends", "sTitle": $.i18n._("End"), "sClass": "sb-ends", "sWidth": "60px"},
            /* runtime */ {"mDataProp": "runtime", "sTitle": $.i18n._("Duration"), "sClass": "library_length sb-length", "sWidth": "65px"},
            /* title */ {"mDataProp": "title", "sTitle": $.i18n._("Title"), "sClass": "sb-title"},
            /* creator */ {"mDataProp": "creator", "sTitle": $.i18n._("Creator"), "sClass": "sb-creator"},
            /* album */ {"mDataProp": "album", "sTitle": $.i18n._("Album"), "sClass": "sb-album"},
            /* cue in */ {"mDataProp": "cuein", "sTitle": $.i18n._("Cue In"), "bVisible": false, "sClass": "sb-cue-in"},
            /* cue out */ {"mDataProp": "cueout", "sTitle": $.i18n._("Cue Out"), "bVisible": false, "sClass": "sb-cue-out"},
            /* fade in */ {"mDataProp": "fadein", "sTitle": $.i18n._("Fade In"), "bVisible": false, "sClass": "sb-fade-in"},
            /* fade out */ {"mDataProp": "fadeout", "sTitle": $.i18n._("Fade Out"), "bVisible": false, "sClass": "sb-fade-out"},
            /* Mime */  {"mDataProp" : "mime", "sTitle" : $.i18n._("Mime"), "bVisible": false, "sClass": "sb-mime"}
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
            	
            	if (builderNeedSave(oData)) {
            		console.log("saving timeline");
            		
            		localStorage.setItem('datatables-timeline', JSON.stringify(oData));
                    
                    $.ajax({
                        url: baseUrl+"usersettings/set-timeline-datatable",
                        type: "POST",
                        data: {settings : oData, format: "json"},
                        dataType: "json"
                    });
            	}  
            },
            "fnStateLoad": function fnBuilderStateLoad(oSettings) {
                var settings = localStorage.getItem('datatables-timeline');
                
                try {
                    return JSON.parse(settings);
                }
                catch (e) {
                    return null;
                }   
            },
            "fnStateLoadParams": function (oSettings, oData) {
            	
            	console.log("loading timeline");
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
                
                currentDatatablesSettings["abVisCols"] = a.slice(0);
                
                a = oData.ColReorder;
                for (i = 0, length = a.length; i < length; i++) {   
                    if (typeof(a[i]) === "string") {
                        a[i] = parseInt(a[i], 10);
                    }
                }
                
                currentDatatablesSettings["ColReorder"] = a.slice(0);
               
                oData.iCreate = parseInt(oData.iCreate, 10);
            },
            
            "fnServerData": mod.fnServerData,
            "fnRowCallback": function fnRowCallback( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
            	//console.time("datatablessinglerowrender");
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
                    headerIcon,
                    $wrapper,
                    $marker;
                
                //http://stackoverflow.com/a/6119684
                $wrapper = $("<div/>", {
                	"class": "innerWrapper"
                });
                
                $marker = $("<div/>", {
                	"class": "marker"
                });
                
                $node = $(nRow.children[0]);
                $node.html($wrapper);

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
                    cl = 'sb-header';
                    
                    if (aData.record === true) {
                        
                        headerIcon =  (aData.soundcloud_id > 0) ? "soundcloud" : "recording";
                        
                        $div = $("<div/>", {
                            "class": "small-icon " + headerIcon
                        });
                        $wrapper.append($div);
                    }
                    else if (aData.rebroadcast === true) {
                        $div = $("<div/>", {
                            "class": "small-icon rebroadcast"
                        });
                        $wrapper.append($div);
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
                    cl = 'sb-footer';
                    
                    //check the show's content status.
                    if (aData.runtime >= 0) {
                        $wrapper.html('<span class="ui-icon ui-icon-check"></span>');
                        cl = cl + ' ui-state-highlight';
                    }
                    else {
                        $wrapper.html('<span class="ui-icon ui-icon-notice"></span>');
                        cl = cl + ' ui-state-error';
                    }
                        
                    sSeparatorHTML = '<span>'+aData.fRuntime+'</span>';
                    fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
                }
                else if (aData.empty === true) {
                    //remove the column classes from all tds.
                    $nRow.find('td').removeClass();

                    //putting the cursor on the screen
                	if (aData.allowed && $lib.length > 0 && $lib.filter(":visible").length > 0) {
                		//using this input just for cursor positioning purposes.
                		$wrapper.append('<input type="checkbox" style="visibility:hidden" disabled="disabled"></input>');
                		$wrapper.append($marker);
                	}
                    
                    sSeparatorHTML = '<span>'+$.i18n._("Show Empty")+'</span>';
                    cl = cl + " sb-empty odd";
                    
                    fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
                }
                else if (aData.record === true) {
                    //remove the column classes from all tds.
                    $nRow.find('td').removeClass();
                    
                    sSeparatorHTML = '<span>'+$.i18n._("Recording From Line In")+'</span>';
                    cl = cl + " sb-record odd";
                    
                    fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
                }
                else {
                     //add the play function if the file exists on disk.
                    $image = $nRow.find('td.sb-image');
                    //check if the file exists.
                    if (aData.image === true) {
                    	//$image.html('<img title="'+$.i18n._("Track preview")+'" src="'+baseUrl+'css/images/icon_audioclip.png"></img>');
                    	
                        if (!AIRTIME.playerPreview.isAudioSupported(aData.mime)) {
                            $image.html('<span class="ui-icon ui-icon-locked"></span>');
                        } 
                        else {
                            $image.html('<img title="'+$.i18n._("Track preview")+'" src="'+baseUrl+'css/images/icon_audioclip.png"></img>');
                        }
						
                    }
                    else {
                        $image.html('<span class="ui-icon ui-icon-alert"></span>');
                        $image.find(".ui-icon-alert").qtip({
                            content: {
                                text: $.i18n._("Airtime is unsure about the status of this file. This can happen when the file is on a remote drive that is unaccessible or the file is in a directory that isn't \"watched\" anymore.")
                            },
                            style: {
                                classes: "ui-tooltip-dark"
                            },
                            show: 'mouseover',
                            hide: 'mouseout'
                        });
                    }
                    
                    if (aData.allowed === true && aData.scheduled >= 1) {
                    	$wrapper.append('<input type="checkbox" name="'+aData.id+'"></input>');
                        
                        //putting the cursor on the screen
                    	if ($lib.length > 0 && $lib.filter(":visible").length > 0) {
                    		$wrapper.append($marker);
                    	}                       	
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
                
                //save some info for reordering purposes.
                $nRow.data({"aData": aData});
                
                //console.timeEnd("datatablessinglerowrender");
            },
            //remove any selected nodes before the draw.
            "fnPreDrawCallback": function( oSettings ) {
            	
            	console.time("datatablesrender");

                //make sure any dragging helpers are removed or else they'll be stranded on the screen.
                $("#draggingContainer").remove();
                
                console.time("datatablesrowrender");
            },
            "fnDrawCallback": function fnBuilderDrawCallback(oSettings, json) {
            	console.timeEnd("datatablesrowrender");
            	
				var aData,
	                elements,
	                i, length, temp,
	                $cursorRows;

                if ($(this).find("."+NOW_PLAYING_CLASS).length > 0) {
                    mod.jumpToCurrentTrack();
                }                

                //only create the cursor arrows if the library is on the page.
                if ($lib.length > 0 && $lib.filter(":visible").length > 0) {  	

                	$cursorRows = $sbTable.find("div.marker").parents("tr");

                    //if there is only 1 cursor on the page highlight it by default.
                    if ($cursorRows.length === 1) {
                        $cursorRows.addClass("cursor-selected-row");
                    }
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
                        // max time interval
						// setTimeout allows only up to (2^31)-1 millisecs timeout value
						maxRefreshInterval = Math.pow(2, 31) - 1;
						refreshInterval = aData.refresh * 1000;
						if (refreshInterval > maxRefreshInterval){
							refreshInterval = maxRefreshInterval;
						}
						mod.timeout = setTimeout(mod.refresh, refreshInterval); //need refresh in milliseconds
                        break;
                    }
                }
                
                mod.checkToolBarIcons();
                
                console.timeEnd("datatablesrender");
            },
            
            // R = ColReorder, C = ColVis
            "sDom": 'R<"dt-process-rel"r><"sb-padded"<"H"C>><"dataTables_scrolling sb-padded"t>',
            
            "oColVis": {
                "aiExclude": [ 0, 1 ],
                "buttonText": $.i18n._("Show / hide columns"),
                "fnStateChange": function ( iColumn, bVisible ) {
                	//row callback doesn't seem to be called anymore, 
                	//make sure headers and footers don't looks weird.
                	$(".sb-header, .sb-footer").find("td:gt(1)").hide();
                }
            },
            
            "oColReorder": {
                "iFixedColumns": 2
            },
             
            "sAjaxDataProp": "schedule",
            "oLanguage": datatables_dict,
            "sAjaxSource": baseUrl+"showbuilder/builder-feed"  
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
                    alert($.i18n._("Cannot schedule outside a show."));
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
                        message = $.i18n._("Moving 1 Item");
                    }
                    else {
                        message = sprintf($.i18n._("Moving %s Items"), selected.length);
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
                items: 'tr:not(:first, :last, .sb-header, .sb-not-allowed, .sb-past, .sb-now-playing, .sb-empty)',
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
                    
                    var elements = $sbTable.find('tr input:checked').parents('tr').not("."+NOW_PLAYING_CLASS);
                    
                    elements.hide();
                }
            };
        }());
        
        $sbTable.sortable(sortableConf);
        
        //start setup of the builder toolbar.
        $toolbar = $(".sb-content .fg-toolbar");

        var $menu = $("<div class='btn-toolbar'/>");
        $menu.append("<div class='btn-group'>" +
                     "<button class='btn btn-small dropdown-toggle sb-select'  id='timeline-select' data-toggle='dropdown'>" +
                         $.i18n._("Select")+" <span class='caret'></span>" +
                     "</button>" +
                     "<ul class='dropdown-menu'>" +
                         "<li id='timeline-sa'><a href='#'>"+$.i18n._("Select all")+"</a></li>" +
                         "<li id='timeline-sn'><a href='#'>"+$.i18n._("Select none")+"</a></li>" +
                     "</ul>" +
                     "</div>")
            .append("<div class='btn-group'>" +
                    "<button title='"+$.i18n._("Remove overbooked tracks")+"' class='ui-state-disabled btn btn-small sb-overbooked' disabled='disabled'>" +
                    "<i class='icon-white icon-cut'></i></button></div>")
            .append("<div class='btn-group'>" +
                    "<button title='"+$.i18n._("Remove selected scheduled items")+"' class='ui-state-disabled btn btn-small sb-trash' disabled='disabled'>" +
                    "<i class='icon-white icon-trash'></i></button></div>");

            $menu.append("<div class='btn-group'>" +
                    "<button  title='"+$.i18n._("Jump to the current playing track")+"' class='ui-state-disabled btn btn-small sb-current' disabled='disabled'>" +
                    "<i class='icon-white icon-step-forward'></i></button></div>")
            .append("<div class='btn-group'>" +
                    "<button title='"+$.i18n._("Cancel current show")+"' class='ui-state-disabled btn btn-small btn-danger sb-cancel' disabled='disabled'>" +
                    "<i class='icon-white icon-ban-circle'></i></button></div>");

        $toolbar.append($menu);
       
        $('#timeline-sa').click(mod.selectAll);
        $('#timeline-sn').click(mod.selectNone);
        
        //cancel current show
        $toolbar.find('.sb-cancel').click(function() {
            var $tr,
                data,
                msg = $.i18n._('Cancel Current Show?');
            
            $tr = $sbTable.find('tr.sb-future:first');
            
            if ($tr.hasClass('sb-current-show')) {
                data = $tr.data("aData");
                
                if (data.record === true) {
                    msg = $.i18n._('Stop recording current show?');
                }
                
                if (confirm(msg)) {
                    var url = baseUrl+"Schedule/cancel-current-show";
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
        $toolbar.find('.sb-current').click(function() {
            mod.jumpToCurrentTrack();
        });
        
        //delete overbooked tracks.
        $toolbar.find('.sb-overbooked').click(function() {
            var temp,
                aItems = [],
                $trs = $sbTable.find(".sb-over.sb-future.sb-allowed");
    
            $trs.each(function(){
                temp = $(this).data("aData");
                aItems.push({"id": temp.id, "instance": temp.instance, "timestamp": temp.timestamp});   
            });
            
            mod.fnRemove(aItems);
        });
        
        //delete selected tracks
        $toolbar.find('.sb-trash').click(function() {
            mod.fnRemoveSelectedItems();
        });
        
        //add events to cursors.
        $sbTable.on("click", "div.marker", function(event) {
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
        
        //call the context menu so we can prevent the event from propagating.
        $sbTable.on("mousedown", "td:gt(1)", function(e) {
    		//only trigger context menu on right click.
    		if (e.which === 3) {
    			e.preventDefault();
        		e.stopPropagation();
        		
    			var $el = $(this);
    			
    			$el.contextMenu({x: e.pageX, y: e.pageY});
    		}
        });
        
        //preview the media item with jPlayer.
        $sbTable.on("click", "td.sb-image img", function(event) {
        	var $tr = $(this).parents("tr"),
        		aData = $tr.data("aData");
        	
        	AIRTIME.playerPreview.previewMedia(aData.mediaId);
            return false;
        });
        
        //begin context menu initialization.
        $.contextMenu({
            selector: '.sb-content table tbody tr:not(.sb-empty, .sb-footer, .sb-header, .sb-record) td:not(.sb-checkbox, .sb-image)',
            trigger: "none",
            ignoreRightClick: false,
            
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
                        	AIRTIME.playerPreview.previewMedia(data.mediaId);
                        };
                        
                        oItems.preview.callback = callback;
                    }
                    
                    //define a select cursor callback.
                    if (oItems.selCurs !== undefined) {
                        
                        callback = function() {
                            var $tr = $(this).parents('tr');
                            
                            mod.selectCursor($tr);
                        };
                        
                        oItems.selCurs.callback = callback;
                    }
                    
                   //define a remove cursor callback.
                    if (oItems.delCurs !== undefined) {
                        
                        callback = function() {
                            var $tr = $(this).parents('tr');
                            
                            mod.removeCursor($tr);
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
                    
                    //only show the cursor selecting options if the library is visible on the page.
                    if ($tr.find('div.marker').length === 0) {
                        delete oItems.selCurs;
                        delete oItems.delCurs;
                    }
                    //check to include either select or remove cursor.
                    else {
                        if ($tr.hasClass(cursorClass)) {
                            delete oItems.selCurs;
                        }
                        else {
                            delete oItems.delCurs;
                        }
                    }       
                           
                    items = oItems;
                }
                
                request = $.ajax({
                  url: baseUrl+"showbuilder/context-menu",
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
