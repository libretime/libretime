    var AIRTIME = (function(AIRTIME){
    var mod,
        libraryInit;
    
    if (AIRTIME.library === undefined) {
        AIRTIME.library = {};
    }
    mod = AIRTIME.library;
    
    mod.fnDeleteItems = function(aMedia) {
        var oLibTT = TableTools.fnGetInstance('library_display'),
            oLibTable = $("#library_display").dataTable();
        
        $.post("/library/delete", 
            {"format": "json", "media": aMedia}, 
            function(json){
                if (json.message !== undefined) {
                    alert(json.message);
                }
                
                oLibTT.fnSelectNone();
                oLibTable.fnDraw();
            });
    };
    
    mod.fnDeleteSelectedItems = function() {
        var oLibTT = TableTools.fnGetInstance('library_display'),
            aData = oLibTT.fnGetSelectedData(),
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
        var oTable;
        
        oTable = $('#library_display').dataTable( {
            
            "aoColumns": [
              /* Checkbox */      {"sTitle": "<input type='checkbox' name='pl_cb_all'>", "mDataProp": "checkbox", "bSortable": false, "bSearchable": false, "sWidth": "25px", "sClass": "library_checkbox"},
              /* Type */          {"sTitle": "", "mDataProp": "image", "bSearchable": false, "sWidth": "25px", "sClass": "library_type", "iDataSort": 2},
              /* ftype */         {"sTitle": "", "mDataProp": "ftype", "bSearchable": false, "bVisible": false},
              /* Title */         {"sTitle": "Title", "mDataProp": "track_title", "sClass": "library_title"},
              /* Creator */       {"sTitle": "Creator", "mDataProp": "artist_name", "sClass": "library_creator"},
              /* Album */         {"sTitle": "Album", "mDataProp": "album_title", "sClass": "library_album"},
              /* Genre */         {"sTitle": "Genre", "mDataProp": "genre", "sClass": "library_genre"},
              /* Year */          {"sTitle": "Year", "mDataProp": "year", "sClass": "library_year", "sWidth": "60px"},
              /* Length */        {"sTitle": "Length", "mDataProp": "length", "sClass": "library_length", "sWidth": "80px"},
              /* Upload Time */   {"sTitle": "Uploaded", "mDataProp": "utime", "sClass": "library_upload_time"},
              /* Last Modified */ {"sTitle": "Last Modified", "mDataProp": "mtime", "bVisible": false, "sClass": "library_modified_time"},
              /* Track Number */  {"sTitle": "Track", "mDataProp": "track_number", "bSearchable": false, "bVisible": false, "sClass": "library_track"},
              /* Mood */  		  {"sTitle": "Mood", "mDataProp": "mood", "bSearchable": false, "bVisible": false, "sClass": "library_mood"},
              /* BPM */  {"sTitle": "BPM", "mDataProp": "bpm", "bSearchable": false, "bVisible": false, "sClass": "library_bpm"},
              /* Composer */  {"sTitle": "Composer", "mDataProp": "composer", "bSearchable": false, "bVisible": false, "sClass": "library_composer"},
              /* Website */  {"sTitle": "Website", "mDataProp": "info_url", "bSearchable": false, "bVisible": false, "sClass": "library_url"},
              /* Bit Rate */  {"sTitle": "Bit Rate", "mDataProp": "bit_rate", "bSearchable": false, "bVisible": false, "sClass": "library_bitrate", "sWidth": "80px"},
              /* Sample Rate */  {"sTitle": "Sample", "mDataProp": "sample_rate", "bSearchable": false, "bVisible": false, "sClass": "library_sr", "sWidth": "80px"},
              /* ISRC Number */  {"sTitle": "ISRC", "mDataProp": "isrc_number", "bSearchable": false, "bVisible": false, "sClass": "library_isrc"},
              /* Encoded */  {"sTitle": "Encoded", "mDataProp": "encoded_by", "bSearchable": false, "bVisible": false, "sClass": "library_encoded"},
              /* Label */  {"sTitle": "Label", "mDataProp": "label", "bSearchable": false, "bVisible": false, "sClass": "library_label"},
              /* Copyright */  {"sTitle": "Copyright", "mDataProp": "copyright", "bSearchable": false, "bVisible": false, "sClass": "library_copyright"},
              /* Mime */  {"sTitle": "Mime", "mDataProp": "mime", "bSearchable": false, "bVisible": false, "sClass": "library_mime"},
              /* Language */  {"sTitle": "Language", "mDataProp": "language", "bSearchable": false, "bVisible": false, "sClass": "library_language"}
              ],
                          
            "bProcessing": true,
            "bServerSide": true,
            
            "bStateSave": true,
            "fnStateSaveParams": function (oSettings, oData) {
                //remove oData components we don't want to save.
                delete oData.oSearch;
                delete oData.aoSearchCols;
            },
            "fnStateSave": function (oSettings, oData) {
               
                $.ajax({
                  url: "/usersettings/set-library-datatable",
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
                  url: "/usersettings/get-library-datatable",
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
               
                oData.iEnd = parseInt(oData.iEnd, 10);
                oData.iLength = parseInt(oData.iLength, 10);
                oData.iStart = parseInt(oData.iStart, 10);
                oData.iCreate = parseInt(oData.iCreate, 10);
            },
            
            "sAjaxSource": "/Library/contents",
            "fnServerData": function ( sSource, aoData, fnCallback ) {
                var type;
                
                aoData.push( { name: "format", value: "json"} );
                
                //push whether to search files/playlists or all.
                type = $("#library_display_type").find("select").val();
                type = (type === undefined) ? 0 : type;
                aoData.push( { name: "type", value: type} );
                
                $.ajax( {
                    "dataType": 'json',
                    "type": "GET",
                    "url": sSource,
                    "data": aoData,
                    "success": fnCallback
                } );
            },
            "fnRowCallback": AIRTIME.library.events.fnRowCallback,
            "fnCreatedRow": function( nRow, aData, iDataIndex ) {
                
                //add the play function to the library_type td
                $(nRow).find('td.library_type').click(function(){
                    open_audio_preview(aData.audioFile, iDataIndex);
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
            "fnDrawCallback": AIRTIME.library.events.fnDrawCallback,
            "fnHeaderCallback": function(nHead) {
                $(nHead).find("input[type=checkbox]").attr("checked", false);
            },
            
            "aaSorting": [[3, 'asc']],
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "oLanguage": {
                "sSearch": ""
            },
           
            // R = ColReorder, C = ColVis, T = TableTools
            "sDom": 'Rl<"#library_display_type">fr<"H"T<"library_toolbar"C>>t<"F"ip>',
            
            "oTableTools": {
                "sRowSelect": "multi",
                "aButtons": [],
                "fnRowSelected": function ( node ) {
                    var selected;
                        
                    //seems to happen if everything is selected
                    if ( node === null) {
                        selected = oTable.find("input[type=checkbox]");
                        selected.attr("checked", true);
                    }
                    else {
                        $(node).find("input[type=checkbox]").attr("checked", true);
                        selected = oTable.find("input[type=checkbox]").filter(":checked");
                    }
                    
                    //checking to enable buttons
                    AIRTIME.button.enableButton("library_group_delete");
                    AIRTIME.library.events.enableAddButtonCheck();
                },
                "fnRowDeselected": function ( node ) {
                    var selected;
                 
                  //seems to happen if everything is deselected
                    if ( node === null) {
                        oTable.find("input[type=checkbox]").attr("checked", false);
                        selected = [];
                    }
                    else {
                        $(node).find("input[type=checkbox]").attr("checked", false);
                        selected = oTable.find("input[type=checkbox]").filter(":checked");
                    }
                    
                    //checking to disable buttons
                    if (selected.length === 0) {
                        AIRTIME.button.disableButton("library_group_delete");
                    }
                    AIRTIME.library.events.enableAddButtonCheck();
                }
            },
            
            "oColVis": {
                "buttonText": "Show/Hide Columns",
                "sAlign": "right",
                "aiExclude": [0, 1, 2],
                "sSize": "css"
            },
            
            "oColReorder": {
                "iFixedColumns": 2
            }
            
        });
        oTable.fnSetFilteringDelay(350);
        
        AIRTIME.library.events.setupLibraryToolbar(oTable);
        
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
          
        $('[name="pl_cb_all"]').click(function(){
            var oTT = TableTools.fnGetInstance('library_display');
            
            if ($(this).is(":checked")) {
                oTT.fnSelectAll();
            }
            else {
                oTT.fnSelectNone();
            }       
        });
        
        checkImportStatus();
        setInterval( checkImportStatus, 5000 );
        setInterval( checkSCUploadStatus, 5000 );
        
        addQtipToSCIcons();
    
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
                           open_audio_preview(data.audioFile, data.id);
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
    
    function addToolBarButtonsLibrary(aButtons) {
    var i,
        length = aButtons.length,
        libToolBar = $(".library_toolbar"),
        html,
        buttonClass = '',
        DEFAULT_CLASS = 'ui-button ui-state-default',
        DISABLED_CLASS = 'ui-state-disabled',
        fn;
    
    for ( i = 0; i < length; i += 1 ) {
        buttonClass = '';
        
        //add disabled class if not enabled.
        if (aButtons[i][2] === false) {
            buttonClass += DISABLED_CLASS;
        }
        
        html = '<div class="ColVis TableTools '+aButtons[i][1]+'"><button class="'+DEFAULT_CLASS+' '+buttonClass+'"><span>'+aButtons[i][0]+'</span></button></div>';
        libToolBar.append(html);
        
        //create a closure to preserve the state of i.
        (function(index){
            
            libToolBar.find("."+aButtons[index][1]).click(function(){
                fn = function() {
                    var $button = $(this).find("button");
                    
                    //only call the passed function if the button is enabled.
                    if (!$button.hasClass(DISABLED_CLASS)) {
                        aButtons[index][3]();
                    }	
                };
                
                fn.call(this);
            });
            
        }(i));
            
    }
    }
    
    function checkImportStatus(){
    $.getJSON('/Preference/is-import-in-progress', function(data){
        var div = $('#import_status');
        if (data == true){
            div.show();
        }
        else{
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
    
    var audio_preview_window = null;
    
    function open_audio_preview(filename, index) {
        url = 'Playlist/audio-preview-player/filename/'+filename+'/index/'+index;
        //$.post(baseUri+'Playlist/audio-preview-player', {fileName: fileName, cueIn: cueIn, cueOut: cueOut, fadeIn: fadeIn, fadeInFileName: fadeInFileName, fadeOut: fadeOut, fadeOutFileName: fadeOutFileName})
        if (audio_preview_window == null || audio_preview_window.closed){
            audio_preview_window = window.open(url, 'Audio Player', 'width=400,height=95');
        } else if (!audio_preview_window.closed) {
            audio_preview_window.play(filename);
        } else {
            console.log("something else : "+baseUrl+url);
        }
    
        //Set the play button to pause.
        //var elemID = "spl_"+elemIndexString;
        //$('#'+elemID+' div.list-item-container a span').attr("class", "ui-icon ui-icon-pause");
        
         return false;
    }