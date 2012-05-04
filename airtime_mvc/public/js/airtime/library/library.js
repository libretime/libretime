var AIRTIME = (function(AIRTIME) {
    var mod,
        libraryInit,
	    oTable,
	    $libContent,
	    $libTable,
	    LIB_SELECTED_CLASS = "lib-selected",
	    chosenItems = {};
    
    if (AIRTIME.library === undefined) {
        AIRTIME.library = {};
    }
    mod = AIRTIME.library;
    
    mod.getChosenItemsLength = function(){
    	var selected = Object.keys(chosenItems).length;
    	
    	return selected;
    };
    
    mod.getChosenAudioFilesLength = function(){
    	var files = Object.keys(chosenItems),
    		i, length,
    		count = 0,
    		reAudio=/^au/ ;
    	
    	for (i = 0, length = files.length; i < length; i++) {
    		
    		if (files[i].search(reAudio) !== -1) {
    			count++;
    		}
    	}
    	
    	return count;
    };
    
    mod.createToolbarDropDown = function() {
    	
    	$.contextMenu({
            selector: '#library_content .ui-icon-document-b',
            trigger: "left",
            ignoreRightClick: true,
            items: {
                "sp": {name: "Select This Page", callback: mod.selectCurrentPage},
                "dp": {name: "Deselect This Page", callback: mod.deselectCurrentPage},
                "sn": {name: "Deselect All", callback: mod.selectNone}
            }
        }); 	
    };
    
    mod.checkDeleteButton = function() {
    	var selected = mod.getChosenItemsLength(),
    		check = false;
    	
    	if (selected !== 0) {
    		check = true;
    	}
    	
    	if (check === true) {
	    	AIRTIME.button.enableButton("lib-button-delete");
	    }
	    else {
	    	AIRTIME.button.disableButton("lib-button-delete");
	    }
    };
    
    mod.checkToolBarIcons = function() {
    	
    	AIRTIME.library.checkAddButton();
    	AIRTIME.library.checkDeleteButton();    	
    };
    
    mod.getSelectedData = function() {
    	var id,
    		data = [];
    	
    	for (id in chosenItems) {
    		
    		if (chosenItems.hasOwnProperty(id)) {
    			data.push(chosenItems[id]);
    		}
    	}
    	
    	return data;
    };
    
    mod.redrawChosen = function() {
    	var ids = Object.keys(chosenItems),
    		i, length,
    		$el;
    	
    	for (i = 0, length = ids.length; i < length; i++) {
    		$el = $libTable.find("#"+ids[i]);
    		
    		if ($el.length !== 0) {
    			mod.highlightItem($el);
    		}
    	}
    };
    
    mod.highlightItem = function($el) {
    	var $input = $el.find("input");
	
		$input.attr("checked", true);
		$el.addClass(LIB_SELECTED_CLASS);
    };
    
    mod.selectItem = function($el) {
    	var id;
    	
    	mod.highlightItem($el);
    	
    	id = $el.attr("id"); 
		chosenItems[id] = $el.data('aData');
		
		mod.checkToolBarIcons();
    };
    
    mod.deselectItem = function($el) {
    	var id,
    		$input = $el.find("input");
    	
    	$input.attr("checked", false);
    	$el.removeClass(LIB_SELECTED_CLASS);
    	
    	id = $el.attr("id"); 
		delete chosenItems[id];
		
		mod.checkToolBarIcons();
    };
    
    /*
     * selects all items which the user can currently see.
     * (behaviour taken from gmail)
     */
    mod.selectCurrentPage = function() {
    	var $trs = $libTable.find("tbody input:checkbox").parents("tr");
    	
    	$trs.each(function(i, el){
    		$el = $(this);
    		
    		mod.selectItem($el);
    	});
    };
    
    /*
     * deselects all items that the user can currently see.
     * (behaviour taken from gmail)
     */
    mod.deselectCurrentPage = function() {
    	
    	var $trs = $libTable.find("tbody input:checkbox").filter(":checked").parents("tr");
		
		$trs.each(function(i, el){
			$el = $(this);
			
			mod.deselectItem($el);
		}); 	
    };
    
    mod.selectNone = function() {
    	var $inputs = $libTable.find("tbody input:checkbox"),
    		$trs = $inputs.parents("tr");
    	
    	$inputs.attr("checked", false);
    	$trs.removeClass(LIB_SELECTED_CLASS);
    	
    	chosenItems = {};
    	
    	mod.checkToolBarIcons();
    };
    
    mod.fnDeleteItems = function(aMedia) {
       
        $.post("/library/delete", 
            {"format": "json", "media": aMedia}, 
            function(json){
                if (json.message !== undefined) {
                    alert(json.message);
                }
                chosenItems = {};
                oTable.fnDraw();
            });
    };
    
    mod.fnDeleteSelectedItems = function() {
        var aData = AIRTIME.library.getSelectedData(),
            item,
            temp,
            aMedia = [];
        
        //process selected files/playlists.
        for (item in aData) {
            temp = aData[item];
            if (temp !== null && temp.hasOwnProperty('id') ) {
                aMedia.push({"id": temp.id, "type": temp.ftype});
            } 	
        }
    
        AIRTIME.library.fnDeleteItems(aMedia);
    };
    
    libraryInit = function() {
    	
        $libContent = $("#library_content");
        $libTable = $libContent.find("table");
        
        var tableHeight = $libContent.height() - 130;
        
        oTable = $libTable.dataTable( {
            
            //put hidden columns at the top to insure they can never be visible on the table through column reordering.
            "aoColumns": [
              /* ftype */         {"sTitle": "", "mDataProp": "ftype", "bSearchable": false, "bVisible": false},
              /* Checkbox */      {"sTitle": "", "mDataProp": "checkbox", "bSortable": false, "bSearchable": false, "sWidth": "25px", "sClass": "library_checkbox"},
              /* Type */          {"sTitle": "", "mDataProp": "image", "bSearchable": false, "sWidth": "25px", "sClass": "library_type", "iDataSort": 0},
              /* Title */         {"sTitle": "Title", "mDataProp": "track_title", "sClass": "library_title", "sWidth": "170px"},
              /* Creator */       {"sTitle": "Creator", "mDataProp": "artist_name", "sClass": "library_creator", "sWidth": "160px"},
              /* Album */         {"sTitle": "Album", "mDataProp": "album_title", "sClass": "library_album", "sWidth": "150px"},
              /* Genre */         {"sTitle": "Genre", "mDataProp": "genre", "sClass": "library_genre", "sWidth": "100px"},
              /* Year */          {"sTitle": "Year", "mDataProp": "year", "sClass": "library_year", "sWidth": "60px"},
              /* Length */        {"sTitle": "Length", "mDataProp": "length", "sClass": "library_length", "sWidth": "80px"},
              /* Upload Time */   {"sTitle": "Uploaded", "mDataProp": "utime", "sClass": "library_upload_time", "sWidth": "125px"},
              /* Last Modified */ {"sTitle": "Last Modified", "mDataProp": "mtime", "bVisible": false, "sClass": "library_modified_time", "sWidth": "125px"},
              /* Track Number */  {"sTitle": "Track", "mDataProp": "track_number", "bSearchable": false, "bVisible": false, "sClass": "library_track", "sWidth": "65px"},
              /* Mood */          {"sTitle": "Mood", "mDataProp": "mood", "bSearchable": false, "bVisible": false, "sClass": "library_mood", "sWidth": "70px"},
              /* BPM */  {"sTitle": "BPM", "mDataProp": "bpm", "bSearchable": false, "bVisible": false, "sClass": "library_bpm", "sWidth": "50px"},
              /* Composer */  {"sTitle": "Composer", "mDataProp": "composer", "bSearchable": false, "bVisible": false, "sClass": "library_composer", "sWidth": "150px"},
              /* Website */  {"sTitle": "Website", "mDataProp": "info_url", "bSearchable": false, "bVisible": false, "sClass": "library_url", "sWidth": "150px"},
              /* Bit Rate */  {"sTitle": "Bit Rate", "mDataProp": "bit_rate", "bSearchable": false, "bVisible": false, "sClass": "library_bitrate", "sWidth": "80px"},
              /* Sample Rate */  {"sTitle": "Sample", "mDataProp": "sample_rate", "bSearchable": false, "bVisible": false, "sClass": "library_sr", "sWidth": "80px"},
              /* ISRC Number */  {"sTitle": "ISRC", "mDataProp": "isrc_number", "bSearchable": false, "bVisible": false, "sClass": "library_isrc", "sWidth": "150px"},
              /* Encoded */  {"sTitle": "Encoded", "mDataProp": "encoded_by", "bSearchable": false, "bVisible": false, "sClass": "library_encoded", "sWidth": "150px"},
              /* Label */  {"sTitle": "Label", "mDataProp": "label", "bSearchable": false, "bVisible": false, "sClass": "library_label", "sWidth": "125px"},
              /* Copyright */  {"sTitle": "Copyright", "mDataProp": "copyright", "bSearchable": false, "bVisible": false, "sClass": "library_copyright", "sWidth": "125px"},
              /* Mime */  {"sTitle": "Mime", "mDataProp": "mime", "bSearchable": false, "bVisible": false, "sClass": "library_mime", "sWidth": "80px"},
              /* Language */  {"sTitle": "Language", "mDataProp": "language", "bSearchable": false, "bVisible": false, "sClass": "library_language", "sWidth": "125px"}
              ],
                          
            "bProcessing": true,
            "bServerSide": true,
            
            "aLengthMenu": [[5, 10, 15, 20, 25, 50, 100], [5, 10, 15, 20, 25, 50, 100]],
                 
            "bStateSave": true,
            "fnStateSaveParams": function (oSettings, oData) {
                //remove oData components we don't want to save.
                delete oData.oSearch;
                delete oData.aoSearchCols;
            },
            "fnStateSave": function (oSettings, oData) {
               
            	localStorage.setItem('datatables-library', JSON.stringify(oData));
            	
            	$.ajax({
                    url: "/usersettings/set-library-datatable",
                    type: "POST",
                    data: {settings : oData, format: "json"},
                    dataType: "json"
                  });
            },
            "fnStateLoad": function fnLibStateLoad(oSettings) {
            	var settings = localStorage.getItem('datatables-library');
            	
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
               
                oData.iEnd = parseInt(oData.iEnd, 10);
                oData.iLength = parseInt(oData.iLength, 10);
                oData.iStart = parseInt(oData.iStart, 10);
                oData.iCreate = parseInt(oData.iCreate, 10);
            },
            
            "sAjaxSource": "/Library/contents-feed",
            "sAjaxDataProp": "files",
            
            "fnServerData": function ( sSource, aoData, fnCallback ) {
                var type;
                
                aoData.push( { name: "format", value: "json"} );
                
                //push whether to search files/playlists or all.
                type = $("#library_display_type").find("select").val();
                type = (type === undefined) ? 0 : type;
                aoData.push( { name: "type", value: type} );
                
                $.ajax( {
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aoData,
                    "success": fnCallback
                } );
            },
            "fnRowCallback": AIRTIME.library.fnRowCallback,
            "fnCreatedRow": function( nRow, aData, iDataIndex ) {
                
                //add the play function to the library_type td
                $(nRow).find('td.library_type').click(function(){
                    if (aData.ftype === 'playlist' && aData.length !== '0.0'){
                        playlistIndex = $(this).parent().attr('id').substring(3); //remove the pl_
                        open_playlist_preview(playlistIndex, 0);
                    } else if (aData.ftype === 'audioclip') {
                        open_audio_preview(aData.audioFile, aData.track_title, aData.artist_name);
                    }
                    return false;
                });
                
                //call the context menu so we can prevent the event from propagating.
                $(nRow).find('td:not(.library_checkbox, .library_type)').click(function(e){
                    
                    $(this).contextMenu({x: e.pageX, y: e.pageY});
                    
                    return false;
                });
                
                //add a tool tip to appear when the user clicks on the type icon.
                $(nRow).find("td:not(:first, td>img)").qtip({
                    content: {
                        text: "Loading...",
                        title: {
                            text: aData.track_title
                        },
                        ajax: {
                            url: "/Library/get-file-meta-data",
                            type: "get",
                            data: ({format: "html", id : aData.id, type: aData.ftype}),
                            success: function(data, status) {
                                this.set('content.text', data);
                            }
                        }
                    },
                    position: {
                        target: 'event',
                        adjust: {
                            resize: true,
                            method: "flip flip"
                        },
                        my: 'left center',
                        at: 'right center',
                        viewport: $(window), // Keep the tooltip on-screen at all times
                        effect: false // Disable positioning animation
                    },
                    style: {
                        classes: "ui-tooltip-dark"
                    },
                    show: 'mousedown',
                    events: {
                       show: function(event, api) {
                         // Only show the tooltip if it was a right-click
                         if(event.originalEvent.button !== 2) {
                            event.preventDefault();
                         }
                       }
                    },
                    hide: 'mouseout'  
                });
            },
           //remove any selected nodes before the draw.
			"fnPreDrawCallback": function( oSettings ) {
				
				//make sure any dragging helpers are removed or else they'll be stranded on the screen.
				$("#draggingContainer").remove();
		    },
            "fnDrawCallback": AIRTIME.library.fnDrawCallback,
            
            "aaSorting": [[3, 'asc']],
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "oLanguage": {
                "sSearch": "",
                "sLengthMenu": "Show _MENU_"
            },
            
            // R = ColReorder, C = ColVis
            "sDom": 'Rl<"#library_display_type">f<"dt-process-rel"r><"H"<"library_toolbar"C>><"dataTables_scrolling"t><"F"ip>',
            
            "oColVis": {
                "sAlign": "right",
                "aiExclude": [0, 1, 2],
                "sSize": "css"
            },
            
            "oColReorder": {
                "iFixedColumns": 3
            }
            
        });
        oTable.fnSetFilteringDelay(350);
       
        $libContent.find(".dataTables_scrolling").css("max-height", tableHeight);
        
        AIRTIME.library.setupLibraryToolbar(oTable);
        
        $("#library_display_type")
            .addClass("dataTables_type")
            .append('<select name="library_display_type" />')
            .find("select")
                .append('<option value="0">All</option>')
                .append('<option value="1">Files</option>')
                .append('<option value="2">Playlists</option>')
                .end()
            .change(function(ev){
                oTable.fnDraw();
            });
        
        $libTable.find("tbody").on("click", "input[type=checkbox]", function(ev) {
        	
        	var $cb = $(this),
        		$prev,
        		$tr = $cb.parents("tr"),
        		$trs;
        	
        	if ($cb.is(":checked")) {
        		
        		if (ev.shiftKey) {
        			$prev = $libTable.find("tbody").find("tr."+LIB_SELECTED_CLASS).eq(-1);
        			$trs = $prev.nextUntil($tr);
        			
        			$trs.each(function(i, el){
        				mod.selectItem($(el));
        			});
        		}

        		mod.selectItem($tr);
        	}
        	else {
        		mod.deselectItem($tr);	
        	}
        });
       
        checkImportStatus();
        setInterval(checkImportStatus, 5000);
        setInterval(checkSCUploadStatus, 5000);
        
        addQtipToSCIcons();
       
        //begin context menu initialization.
        $.contextMenu({
            selector: '#library_display td:not(.library_checkbox)',
            trigger: "left",
            ignoreRightClick: true,
            
            build: function($el, e) {
                var data, screen, items, callback, $tr;
                
                $tr = $el.parent();
                data = $tr.data("aData");
                screen = $tr.data("screen");
                
                function processMenuItems(oItems) {
                    
                    //define an add to playlist callback.
                    if (oItems.pl_add !== undefined) {
                        
                        callback = function() {
                            AIRTIME.playlist.fnAddItems([data.id], undefined, 'after');
                        };
                        
                        oItems.pl_add.callback = callback;
                    }
                    
                    //define an edit callback.
                    if (oItems.edit !== undefined) {
                        
                        if (data.ftype === "audioclip") {
                            callback = function() {
                                document.location.href = oItems.edit.url;
                            };
                        }
                        else {
                            callback = function() {
                                AIRTIME.playlist.fnEdit(data.id);
                            };
                        }
                        oItems.edit.callback = callback;
                    }

                    //define a play callback.
                    if (oItems.play !== undefined) {
                        callback = function() {
                           if (data.ftype === 'playlist' && data.length !== '0.0'){
                                playlistIndex = $(this).parent().attr('id').substring(3); //remove the pl_
                                open_playlist_preview(playlistIndex, 0);
                            } else if (data.ftype === 'audioclip') {
                                open_audio_preview(data.audioFile, data.track_title, data.artist_name);
                            }
                        };
                        oItems.play.callback = callback;
                    }
                    
                    //define a delete callback.
                    if (oItems.del !== undefined) {
                        
                        //delete through the playlist controller, will reset
                        //playlist screen if this is the currently edited playlist.
                        if (data.ftype === "playlist" && screen === "playlist") {
                            callback = function() {
                                
                                if (confirm('Are you sure you want to delete the selected item?')) {
                                    AIRTIME.playlist.fnDelete(data.id);
                                }
                            };
                        }
                        else {
                            callback = function() {
                                var media = [];
                                
                                if (confirm('Are you sure you want to delete the selected item?')) {
                                    
                                    media.push({"id": data.id, "type": data.ftype});
                                    $.post(oItems.del.url, {format: "json", media: media }, function(json){
                                        var oTable;
                                        
                                        if (json.message) {
                                            alert(json.message);
                                        }
                                        
                                        oTable = $("#library_display").dataTable();
                                        oTable.fnDeleteRow( $tr[0] );
                                    });
                                }
                            };
                        }
                        
                        oItems.del.callback = callback;
                    }
                    
                    //define a download callback.
                    if (oItems.download !== undefined) {
                        
                        callback = function() {
                            document.location.href = oItems.download.url;
                        };
                        oItems.download.callback = callback;
                    }
                    //add callbacks for Soundcloud menu items.
                    if (oItems.soundcloud !== undefined) {
                        var soundcloud = oItems.soundcloud.items;
                        
                        //define an upload to soundcloud callback.
                        if (soundcloud.upload !== undefined) {
                            
                            callback = function() {
                                $.post(soundcloud.upload.url, function(){
                                    addProgressIcon(data.id);
                                });
                            };
                            soundcloud.upload.callback = callback;
                        }
                        
                        //define a view on soundcloud callback
                        if (soundcloud.view !== undefined) {
                            
                            callback = function() {
                                window.open(soundcloud.view.url);
                            };
                            soundcloud.view.callback = callback;
                        }
                    }
                
                    items = oItems;
                }
                
                request = $.ajax({
                  url: "/library/context-menu",
                  type: "GET",
                  data: {id : data.id, type: data.ftype, format: "json", "screen": screen},
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
    mod.libraryInit = libraryInit;
    
    return AIRTIME;
    
}(AIRTIME || {}));

function checkImportStatus() {
    $.getJSON('/Preference/is-import-in-progress', function(data){
        var div = $('#import_status');
        var table = $('#library_display').dataTable();
        if (data == true){
            div.show();
        }
        else{
            if ($(div).is(':visible')) {
                table.fnDraw();
            }
            div.hide();
        }
    });
}
    
function addProgressIcon(id) {
    var tr = $("#au_"+id),
        span;
    
    span = tr.find("td.library_title").find("span");
    
    if (span.length > 0){	
        span.removeClass()
            .addClass("small-icon progress");
    }
    else{
        tr.find("td.library_title")
            .append('<span class="small-icon progress"></span>');
    }
}
    
function checkSCUploadStatus(){
    
    var url = '/Library/get-upload-to-soundcloud-status';
    
    $("span[class*=progress]").each(function(){
        var span, id;
        
        span = $(this);
        id = span.parent().parent().data("aData").id;
       
        $.post(url, {format: "json", id: id, type:"file"}, function(json){
            if (json.sc_id > 0) {
                span.removeClass("progress")
                    .addClass("soundcloud");
                
            }
            else if (json.sc_id == "-3") {
                span.removeClass("progress")
                    .addClass("sc-error");
            }
        });
    });
}
    
function addQtipToSCIcons(){
    $(".progress, .soundcloud, .sc-error").live('mouseover', function(){
        
        var id = $(this).parent().parent().data("aData").id;
        
        if ($(this).hasClass("progress")){
            $(this).qtip({
                content: {
                    text: "Uploading in progress..."
                },
                position:{
                    adjust: {
                    resize: true,
                    method: "flip flip"
                    },
                    at: "right center",
                    my: "left top",
                    viewport: $(window)
                },
                show: {
                    ready: true // Needed to make it show on first mouseover event
                }
            });
        }
        else if($(this).hasClass("soundcloud")){
            $(this).qtip({
                content: {
                    text: "Retreiving data from the server...",
                    ajax: {
                        url: "/Library/get-upload-to-soundcloud-status",
                        type: "post",
                        data: ({format: "json", id : id, type: "file"}),
                        success: function(json, status){
                            this.set('content.text', "The soundcloud id for this file is: "+json.sc_id);
                        }
                    }
                },
                position:{
                    adjust: {
                    resize: true,
                    method: "flip flip"
                    },
                    at: "right center",
                    my: "left top",
                    viewport: $(window)
                },
                show: {
                    ready: true // Needed to make it show on first mouseover event
                }
            });
        }else if($(this).hasClass("sc-error")){
            $(this).qtip({
                content: {
                    text: "Retreiving data from the server...",
                    ajax: {
                        url: "/Library/get-upload-to-soundcloud-status",
                        type: "post",
                        data: ({format: "json", id : id, type: "file"}),
                        success: function(json, status){
                            this.set('content.text', "There was error while uploading to soundcloud.<br>"+"Error code: "+json.error_code+
                                    "<br>"+"Error msg: "+json.error_msg+"<br>");
                        }
                    }
                },
                position:{
                    adjust: {
                    resize: true,
                    method: "flip flip"
                    },
                    at: "right center",
                    my: "left top",
                    viewport: $(window)
                },
                show: {
                    ready: true // Needed to make it show on first mouseover event
                }
            });
        }
    });
}
