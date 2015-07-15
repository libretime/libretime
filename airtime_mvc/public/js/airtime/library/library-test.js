var AIRTIME = (function(AIRTIME) {
    var mod,
        libraryInit,
        oTable,
        $libContent,
        $libTable,
        LIB_SELECTED_CLASS = "lib-selected",
        chosenItems = {},
        visibleChosenItems = {},
        $previouslySelected;


    // we need to know whether the criteria value is string or
    // numeric in order to provide a single textbox or range textboxes
    // in the advanced search
    // s => string
    // n => numberic
    var libraryColumnTypes = {
        0             : "",
        "album_title" : "s",
        "artist_name" : "s",
        "bit_rate"    : "n",
        "bpm"         : "n",
        "comments"    : "s",
        "composer"    : "s",
        "conductor"   : "s",
        "copyright"   : "s",
        "cuein"       : "n",
        "cueout"      : "n",
        "utime"       : "n",
        "mtime"       : "n",
        "lptime"      : "n",
        "disc_number" : "n",
        "encoded_by"  : "s",
        "genre"       : "s",
        "isrc_number" : "s",
        "label"       : "s",
        "language"    : "s",
        "length"      : "n",
        "lyricist"    : "s",
        "mime"        : "s",
        "mood"        : "s",
        "name"        : "s",
        "orchestra"   : "s",
        "rating"      : "n",
        "sample_rate" : "n",
        "track_title" : "s",
        "track_num"   : "n",
        "year"        : "n",
        "owner_id"    : "s",
        "info_url"    : "s",
        "replay_gain" : "n"
    };
    
    if (AIRTIME.library === undefined) {
        AIRTIME.library = {};
    }
    mod = AIRTIME.library;
    
    mod.getChosenItemsLength = function(){
        var cItem,
            selected,
            $trs;

        // Get visible items and check if any chosenItems are visible
        $trs = $libTable.find("tr");
        $trs.each(function(i){
            for (cItem in chosenItems) {
                if (cItem === $(this).attr("id")) {
                    visibleChosenItems[cItem] = $(this).data('aData');
                }
            } 
        });
        
        selected = Object.keys(visibleChosenItems).length;
        visibleChosenItems = {};
        return selected;
    };
    
    mod.getChosenAudioFilesLength = function(){
        // var files = Object.keys(chosenItems),
        var files,
            $trs,
            cItem,
            i, length,
            count = 0,
            reAudio=/^(au|st|pl|bl)/ ;
            
        // Get visible items and check if any chosenItems are visible
        $trs = $libTable.find("tr");
        $trs.each(function(i){
            for (cItem in chosenItems) {
                if (cItem === $(this).attr("id")) {
                    visibleChosenItems[cItem] = $(this).data('aData');
                }
            } 
        });
        
        files = Object.keys(visibleChosenItems);
        
        for (i = 0, length = files.length; i < length; i++) {
            
            if (files[i].search(reAudio) !== -1) {
                count++;
            }
        }
        visibleChosenItems = {};
        return count;
    };
    
    mod.changeAddButtonText = function($button, btnText) {
        $button.text(btnText);
    };
    
    mod.createToolbarButtons = function() {
        $menu = $("<div class='btn-toolbar' />");
        $menu
            .append("<div class='btn-group'>" +
                        "<button class='btn btn-small dropdown-toggle' data-toggle='dropdown'>" +
                            $.i18n._("Select")+" <span class='caret'></span>" +
                        "</button>" +
                        "<ul class='dropdown-menu'>" +
                            "<li id='sb-select-page'><a href='#'>"+$.i18n._("Select this page")+"</a></li>" +
                            "<li id='sb-dselect-page'><a href='#'>"+$.i18n._("Deselect this page")+"</a></li>" +
                            "<li id='sb-dselect-all'><a href='#'>"+$.i18n._("Deselect all")+"</a></li>" +
                        "</ul>" +
                    "</div>")
            .append("<div class='btn-group'>" +
                        "<button class='btn btn-small' id='library-plus'>" +
                            "<i class='icon-white icon-plus'></i>" +
                            "<span id='lib-plus-text'></span>" +
                        "</button>" +
                    "</div>")
            .append("<div class='btn-group'>" +
                        "<button class='btn btn-small' id='sb-trash'>" +
                            "<i class='icon-white icon-trash'></i>" +
                        "</button>" +
                    "</div>");
    };
    
    mod.createToolbarDropDown = function() {
        $('#sb-select-page').click(function(){mod.selectCurrentPage();});
        $('#sb-dselect-page').click(function(){mod.deselectCurrentPage();});
        $('#sb-dselect-all').click(function(){mod.selectNone();});
    };
    
    mod.checkDeleteButton = function() {
        var selected = mod.getChosenItemsLength(),
            check = false;

        if (selected !== 0) {
            check = true;
        }
        
        if (check === true) {
            AIRTIME.button.enableButton("btn-group #sb-trash", false);
        }
        else {
            AIRTIME.button.disableButton("btn-group #sb-trash", false);
        }
    };
    
    mod.checkToolBarIcons = function() {
        
        AIRTIME.library.checkAddButton();
        AIRTIME.library.checkDeleteButton();        
    };
    
    mod.getSelectedData = function() {
        var id,
            data = [],
            cItem,
            $trs;
            
        $.fn.reverse = [].reverse;
        
        // Get visible items and check if any chosenItems are visible
        $trs = $libTable.find("tr").reverse();
        $trs.each(function(i){
            for (cItem in chosenItems) {
                if (cItem === $(this).attr("id")) {
                    visibleChosenItems[cItem] = $(this).data('aData');
                }
            } 
        });
        
        for (id in visibleChosenItems) {
            if (visibleChosenItems.hasOwnProperty(id)) {
                data.push(visibleChosenItems[id]);
            }
        }
        visibleChosenItems = {};
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
    
    mod.isChosenItem = function($el) {
        var id = $el.attr("id"),
            item = chosenItems[id];

        return item !== undefined;
    };
    
    mod.addToChosen = function($el) {
        var id = $el.attr("id");
        
        chosenItems[id] = $el.data('aData');
    };
    
    mod.removeFromChosen = function($el) {
        var id = $el.attr("id");
        
        // used to not keep dragged items selected.
        if (!$el.hasClass(LIB_SELECTED_CLASS)) {
            delete chosenItems[id];
        }   
    };
    
    mod.highlightItem = function($el) {
        $el.addClass(LIB_SELECTED_CLASS);
    };
    
    mod.unHighlightItem = function($el) {
        $el.removeClass(LIB_SELECTED_CLASS);
    };
    
    mod.selectItem = function($el) {
        mod.highlightItem($el);
        mod.addToChosen($el);
        
        mod.checkToolBarIcons();
    };
    
    mod.deselectItem = function($el) {
        mod.unHighlightItem($el);
        mod.removeFromChosen($el);
        mod.checkToolBarIcons();
    };
    
    /*
     * selects all items which the user can currently see. (behaviour taken from
     * gmail)
     * 
     * by default the items are selected in reverse order so we need to reverse
     * it back
     */
    mod.selectCurrentPage = function() {
        $.fn.reverse = [].reverse;
        var $trs = $libTable.find("tbody").find("tr").reverse();
        $trs.addClass(LIB_SELECTED_CLASS);

        $trs.each(function(i, el){
            $el = $(this);
            mod.addToChosen($el);
        });

        mod.checkToolBarIcons();
          
    };
    
    /*
     * deselects all items that the user can currently see. (behaviour taken
     * from gmail)
     */
    mod.deselectCurrentPage = function() {
        var $trs = $libTable.find("tr"), id;
        $trs.removeClass(LIB_SELECTED_CLASS);
        
        $trs.each(function(i, el){
            $el = $(this);
            id = $el.attr("id");
            delete chosenItems[id];
        });
        
        mod.checkToolBarIcons();     
    };
    
    mod.selectNone = function() {
        var $trs = $libTable.find("tr");
        $trs.removeClass(LIB_SELECTED_CLASS);
        
        chosenItems = {};
        
        mod.checkToolBarIcons();
    };
    
    mod.fnDeleteItems = function(aMedia) {

        //Prevent the user from spamming the delete button while the AJAX request is in progress
        AIRTIME.button.disableButton("btn-group #sb-trash", false);
        //Hack to immediately show the "Processing" div in DataTables to give the user some sort of feedback.
        $(".dataTables_processing").css('visibility','visible');

        $.post(baseUrl+"library/delete", 
            {"format": "json", "media": aMedia}, 
            function(json){
                if (json.message !== undefined) {
                    alert(json.message);
                }

                chosenItems = {};
                oTable.fnStandingRedraw();

                //Re-enable the delete button
                AIRTIME.button.enableButton("btn-group #sb-trash", false);
            });
    };
    
    mod.fnDeleteSelectedItems = function() {
        if (confirm($.i18n._('Are you sure you want to delete the selected item(s)?'))) {
            var aData = AIRTIME.library.getSelectedData(),
                item,
                temp,
                aMedia = [],
                currentObjId = $("#side_playlist").find("#obj_id").val(),
                currentObjType = $("#side_playlist").find("#obj_type").val(),
                closeObj = false;

            // process selected files/playlists.
            for (item in aData) {
                temp = aData[item];
                if (temp !== null && temp.hasOwnProperty('id') ) {
                    aMedia.push({"id": temp.id, "type": temp.ftype});
                    if ( (temp.id == currentObjId && temp.ftype === currentObjType) ||
                            temp.id == currentObjId && temp.ftype === "stream" && currentObjType === "webstream") {
                        closeObj = true;
                    }
                }
            }

            AIRTIME.library.fnDeleteItems(aMedia);

            // close the object (playlist/block/webstream)
            // on the right side if it was just deleted
            // from the library
            if (closeObj) {
                $.post(baseUrl+"playlist/close-playlist",
                    {"format": "json", "type": currentObjType},
                    function(json) {
                        $("#side_playlist").empty().append(json.html);
                    });
            }
        }
    };
    
    libraryInit = function() {
        
        $libContent = $("#library_content");
        
        /*
         * Icon hover states in the toolbar.
         */
        $libContent.on("mouseenter", ".fg-toolbar ul li", function(ev) {
            $el = $(this);
            
            if (!$el.hasClass("ui-state-disabled")) {
                $el.addClass("ui-state-hover");
            }       
        });
        $libContent.on("mouseleave", ".fg-toolbar ul li", function(ev) {
            $el = $(this);
            
            if (!$el.hasClass("ui-state-disabled")) {
                $el.removeClass("ui-state-hover");
            } 
        });
        
        var colReorderMap = new Array();
        
        $libTable = $libContent.find("table");
        
        function getTableHeight() {
        	return $libContent.height() - 175;
        }
        
        function setColumnFilter(oTable){
            // TODO : remove this dirty hack once js is refactored
            if (!oTable.fnSettings()) { return ; }
            var aoCols = oTable.fnSettings().aoColumns;
            var colsForAdvancedSearch = new Array();
            var advanceSearchDiv = $("div#advanced_search");
            advanceSearchDiv.empty();
            $.each(aoCols, function(i,ele){
                if (ele.bSearchable) {
                    var currentColId = ele._ColReorder_iOrigCol;
                    
                    var inputClass = 'filter_column filter_number_text'; 
                    var labelStyle = "style='margin-right:35px;'";
                    if (libraryColumnTypes[ele.mDataProp] != "s") {
                        inputClass = 'filterColumn filter_number_range';
                        labelStyle = "";
                    }
                    
                    if (ele.bVisible) {
                        advanceSearchDiv.append(
                            "<div id='advanced_search_col_"+currentColId+"' class='control-group'>" +
                                "<label class='control-label'"+labelStyle+">"+ele.sTitle+" : </label>" +
                                "<div id='"+ele.mDataProp+"' class='controls "+inputClass+"'></div>" +
                            "</div>");
                    } else {
                        advanceSearchDiv.append(
                            "<div id='advanced_search_col_"+currentColId+"' class='control-group' style='display:none;'>" +
                                "<label class='control-label'"+labelStyle+">"+ele.sTitle+"</label>" +
                                "<div id='"+ele.mDataProp+"' class='controls "+inputClass+"'></div>" +
                            "</div>");
                    }
                    
                    if (libraryColumnTypes[ele.mDataProp] == "s") {
                        var obj = { sSelector: "#"+ele.mDataProp }
                    } else {
                        var obj = { sSelector: "#"+ele.mDataProp, type: "number-range" }
                    }
                    colsForAdvancedSearch.push(obj);
                } else {
                    colsForAdvancedSearch.push(null);
                }
            });
            
            oTable.columnFilter({
                aoColumns: colsForAdvancedSearch,
                bUseColVis: true,
                sPlaceHolder: "head:before"
                }
            );
        }
        
        function setFilterElement(iColumn, bVisible){
            var actualId = colReorderMap[iColumn];
            var selector = "div#advanced_search_col_"+actualId;
            var $el = $(selector);
            
            if (bVisible) {
                $el.show();
            } else {
                $el.hide();
            }
            
            //resize to prevent double scroll bars.
            var $fs = $el.parents("fieldset"),
            	tableHeight = getTableHeight(),
            	searchHeight = $fs.height();
            
            $libContent.find(".dataTables_scrolling").css("max-height", tableHeight - searchHeight);
        }
        
        oTable = $libTable.dataTable( {
            
            // put hidden columns at the top to insure they can never be visible
            // on the table through column reordering.
            
            //IMPORTANT: WHEN ADDING A NEW COLUMN PLEASE CONSULT WITH THE WIKI
            // https://wiki.sourcefabric.org/display/CC/Adding+a+new+library+datatable+column
            "aoColumns": [
              /* ftype */           { "sTitle" : ""                             , "mDataProp" : "ftype"        , "bSearchable" : false                 , "bVisible"    : false                   }          ,
              /* Type */            { "sTitle" : ""                             , "mDataProp" : "image"        , "bSearchable" : false                 , "sWidth"      : "16px"                  , "sClass" : "library_type" , "iDataSort" : 0                  }  ,
              ///* Is Scheduled */    { "sTitle" : $.i18n._("Scheduled")          , "mDataProp" : "is_scheduled" , "bVisible"    : false                 , "bSearchable" : false                 , "sWidth"      : "90px"                  , "sClass" : "library_is_scheduled"}  ,
              /* Is Playlist */     { "sTitle" : $.i18n._("Playlist / Block")   , "mDataProp" : "is_playlist"  , "bSearchable" : false                 , "sWidth"      : "110px"                  , "sClass" : "library_is_playlist"}  ,
              /* Title */           { "sTitle" : $.i18n._("Title")              , "mDataProp" : "track_title"  , "sClass"      : "library_title"       , "sWidth"      : "170px"                 }          ,
              /* Creator */         { "sTitle" : $.i18n._("Creator")            , "mDataProp" : "artist_name"  , "sClass"      : "library_creator"     , "sWidth"      : "160px"                 }          ,
              /* Album */           { "sTitle" : $.i18n._("Album")              , "mDataProp" : "album_title"  , "sClass"      : "library_album"       , "sWidth"      : "150px"                 }          ,
              /* Bit Rate */        { "sTitle" : $.i18n._("Bit Rate")           , "mDataProp" : "bit_rate"     , "bVisible"    : false                 , "sClass"      : "library_bitrate"       , "sWidth" : "80px"         },
              /* BPM */             { "sTitle" : $.i18n._("BPM")                , "mDataProp" : "bpm"          , "bVisible"    : false                 , "sClass"      : "library_bpm"           , "sWidth" : "50px"         },
              /* Composer */        { "sTitle" : $.i18n._("Composer")           , "mDataProp" : "composer"     , "bVisible"    : false                 , "sClass"      : "library_composer"      , "sWidth" : "150px"        },
              /* Conductor */       { "sTitle" : $.i18n._("Conductor")          , "mDataProp" : "conductor"    , "bVisible"    : false                 , "sClass"      : "library_conductor"     , "sWidth" : "125px"        },
              /* Copyright */       { "sTitle" : $.i18n._("Copyright")          , "mDataProp" : "copyright"    , "bVisible"    : false                 , "sClass"      : "library_copyright"     , "sWidth" : "125px"        },
              /* Cue In */          { "sTitle" : $.i18n._("Cue In")             , "mDataProp" : "cuein"        , "bVisible"    : false                 , "sClass"      : "library_length"     , "sWidth" : "80px"        },
              /* Cue Out */         { "sTitle" : $.i18n._("Cue Out")            , "mDataProp" : "cueout"       , "bVisible"    : false                 , "sClass"      : "library_length"     , "sWidth" : "80px"        },
              /* Encoded */         { "sTitle" : $.i18n._("Encoded By")         , "mDataProp" : "encoded_by"   , "bVisible"    : false                 , "sClass"      : "library_encoded"       , "sWidth" : "150px"        },
              /* Genre */           { "sTitle" : $.i18n._("Genre")              , "mDataProp" : "genre"        , "bVisible"    : false                 , "sClass"      : "library_genre"         , "sWidth" : "100px"        },
              /* ISRC Number */     { "sTitle" : $.i18n._("ISRC")               , "mDataProp" : "isrc_number"  , "bVisible"    : false                 , "sClass"      : "library_isrc"          , "sWidth" : "150px"        },
              /* Label */           { "sTitle" : $.i18n._("Label")              , "mDataProp" : "label"        , "bVisible"    : false                 , "sClass"      : "library_label"         , "sWidth" : "125px"        },
              /* Language */        { "sTitle" : $.i18n._("Language")           , "mDataProp" : "language"     , "bVisible"    : false                 , "sClass"      : "library_language"      , "sWidth" : "125px"        },
              /* Last Modified */   { "sTitle" : $.i18n._("Last Modified")      , "mDataProp" : "mtime"        , "bVisible"    : false                 , "sClass"      : "library_modified_time" , "sWidth" : "125px"        },
              /* Last Played */     { "sTitle" : $.i18n._("Last Played")        , "mDataProp" : "lptime"       , "bVisible"    : false                 , "sClass"      : "library_modified_time" , "sWidth" : "125px"        },
              /* Length */          { "sTitle" : $.i18n._("Length")             , "mDataProp" : "length"       , "sClass"      : "library_length"      , "sWidth"      : "80px"                  }          ,
              /* Mime */            { "sTitle" : $.i18n._("Mime")               , "mDataProp" : "mime"         , "bVisible"    : false                 , "sClass"      : "library_mime"          , "sWidth" : "80px"         },
              /* Mood */            { "sTitle" : $.i18n._("Mood")               , "mDataProp" : "mood"         , "bVisible"    : false                 , "sClass"      : "library_mood"          , "sWidth" : "70px"         },
              /* Owner */           { "sTitle" : $.i18n._("Owner")              , "mDataProp" : "owner_id"     , "bVisible"    : false                 , "sClass"      : "library_language"      , "sWidth" : "125px"        },
              /* Replay Gain */     { "sTitle" : $.i18n._("Replay Gain")        , "mDataProp" : "replay_gain"  , "bVisible"    : false                 , "sClass"      : "library_replay_gain"      , "sWidth" : "80px"      },
              /* Sample Rate */     { "sTitle" : $.i18n._("Sample Rate")        , "mDataProp" : "sample_rate"  , "bVisible"    : false                 , "sClass"      : "library_sr"            , "sWidth" : "80px"         },
              /* Track Number */    { "sTitle" : $.i18n._("Track Number")       , "mDataProp" : "track_number" , "bVisible"    : false                 , "sClass"      : "library_track"         , "sWidth" : "65px"         },
              /* Upload Time */     { "sTitle" : $.i18n._("Uploaded")           , "mDataProp" : "utime"        , "bVisible"    : false                 , "sClass"      : "library_upload_time" , "sWidth"      : "125px"                 }          ,
              /* Website */         { "sTitle" : $.i18n._("Website")            , "mDataProp" : "info_url"     , "bVisible"    : false                 , "sClass"      : "library_url"           , "sWidth" : "150px"        },
              /* Year */            { "sTitle" : $.i18n._("Year")               , "mDataProp" : "year"         , "bVisible"    : false                 , "sClass"      : "library_year"          , "sWidth" : "60px"         }
              ],
            
            "bProcessing": true,
            "bServerSide": true,
            
            "aLengthMenu": [[5, 10, 15, 20, 25, 50, 100], [5, 10, 15, 20, 25, 50, 100]],
                 
            "bStateSave": true,
            "fnStateSaveParams": function (oSettings, oData) {
                // remove oData components we don't want to save.
                delete oData.oSearch;
                delete oData.aoSearchCols;
            },
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem('datatables-library', JSON.stringify(oData));
                /*
                $.ajax({
                    url: baseUrl+"usersettings/set-library-datatable",
                    type: "POST",
                    data: {settings : oData, format: "json"},
                    dataType: "json"
                  });
                  */
                
                colReorderMap = oData.ColReorder;
            },
            "fnStateLoad": function fnLibStateLoad(oSettings) {
                var settings = localStorage.getItem('datatables-library');
               
                try {
                    return JSON.parse(settings);
                } catch (e) {
                    return null;
                }
            },
            "fnStateLoadParams": function (oSettings, oData) {
                var i,
                    length,
                    a = oData.abVisCols;
                
                if (a) {
                    // putting serialized data back into the correct js type to make
                    // sure everything works properly.
                    for (i = 0, length = a.length; i < length; i++) {
                        if (typeof(a[i]) === "string") {
                            a[i] = (a[i] === "true") ? true : false;
                        } 
                    }
                }
                    
                a = oData.ColReorder;
                if (a) {
                    for (i = 0, length = a.length; i < length; i++) {
                        if (typeof(a[i]) === "string") {
                            a[i] = parseInt(a[i], 10);
                        }
                    }
                }
                
                oData.iEnd = parseInt(oData.iEnd, 10);
                oData.iLength = parseInt(oData.iLength, 10);
                oData.iStart = parseInt(oData.iStart, 10);
                oData.iCreate = parseInt(oData.iCreate, 10);
            },
            
            "sAjaxSource": baseUrl+"Library/contents-feed-test",
            "sAjaxDataProp": "files",
            
            "fnServerData": function ( sSource, aoData, fnCallback ) {
                /*
                 * The real validation check is done in
                 * dataTables.columnFilter.js We also need to check it here
                 * because datatable is redrawn everytime an action is performed
                 * in the Library page. In order for datatable to redraw the
                 * advanced search fields MUST all be valid.
                 */
                var advSearchFields = $("div#advanced_search").children(':visible');
                var advSearchValid = validateAdvancedSearch(advSearchFields);
                var type;
                aoData.push( { name: "format", value: "json"} );
                aoData.push( { name: "advSearch", value: advSearchValid} );
                
                // push whether to search files/playlists or all.
                type = $(".media_type_selector.selected").attr("selection_id");
                type = (type === undefined) ? 1 : type;
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
                // add audio preview image/button
                if (aData.ftype === "audioclip") {
                    $(nRow).find('td.library_type').html('<img title="'+$.i18n._("Track preview")+'" src="'+baseUrl+'css/images/icon_audioclip.png">');
                } else if (aData.ftype === "playlist") {
                    $(nRow).find('td.library_type').html('<img title="'+$.i18n._("Playlist preview")+'" src="'+baseUrl+'css/images/icon_playlist.png">');
                } else if (aData.ftype === "block") {
                    $(nRow).find('td.library_type').html('<img title="'+$.i18n._("Smart Block")+'" src="'+baseUrl+'css/images/icon_smart-block.png">');
                } else if (aData.ftype === "stream") {
                    $(nRow).find('td.library_type').html('<img title="'+$.i18n._("Webstream preview")+'" src="'+baseUrl+'css/images/icon_webstream.png">');
                }

                if (aData.is_scheduled) {
                    $(nRow).find("td.library_is_scheduled").html('<span class="small-icon is_scheduled"></span>');
                } else if (!aData.is_scheduled) {
                    $(nRow).find("td.library_is_scheduled").html('');
                }
                if (aData.is_playlist) {
                    $(nRow).find("td.library_is_playlist").html('<span class="small-icon is_playlist"></span>');
                } else if (!aData.is_playlist) {
                    $(nRow).find("td.library_is_playlist").html('');
                }

                // add the play function to the library_type td
                $(nRow).find('td.library_type').click(function(){
                    if (aData.ftype === 'playlist' && aData.length !== '0.0'){
                        open_playlist_preview(aData.audioFile, 0);
                    } else if (aData.ftype === 'audioclip') {
                        if (isAudioSupported(aData.mime)) {
                            open_audio_preview(aData.ftype, aData.id);
                        }
                    } else if (aData.ftype == 'stream') {
                        if (isAudioSupported(aData.mime)) {
                            open_audio_preview(aData.ftype, aData.id);
                        }
                    } else if (aData.ftype == 'block' && aData.bl_type == 'static') {
                        open_block_preview(aData.audioFile, 0);
                    }
                    return false;
                });
            },
           // remove any selected nodes before the draw.
            "fnPreDrawCallback": function( oSettings ) {
                
                // make sure any dragging helpers are removed or else they'll be
                // stranded on the screen.
                $("#draggingContainer").remove();
            },
            "fnDrawCallback": AIRTIME.library.fnDrawCallback,
            
            "aaSorting": [[5, 'asc']],
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "oLanguage": datatables_dict,
            
            // R = ColReorder, C = ColVis
            "sDom": 'Rl<"#library_display_type">f<"dt-process-rel"r><"H"<"library_toolbar"C>><"dataTables_scrolling"t><"F"ip>',
            
            "oColVis": {
                "sAlign": "right",
                "aiExclude": [0, 1, 2],
                "sSize": "css",
                "fnStateChange": setFilterElement,
                "buttonText": $.i18n._("Show / hide columns")
            },
            
            "oColReorder": {
                "iFixedColumns": 3
            }
            
        });

        setColumnFilter(oTable);
        oTable.fnSetFilteringDelay(350);

        var simpleSearchText;

        $libContent.on("click", "legend", function(){
            $simpleSearch = $libContent.find("#library_display_filter label");
            var $fs = $(this).parents("fieldset"),
            	searchHeight,
            	tableHeight = getTableHeight(),
            	height;

            if ($fs.hasClass("closed")) {
                $fs.removeClass("closed");
                searchHeight = $fs.height();

                //keep value of simple search for when user switches back to it
                simpleSearchText = $simpleSearch.find('input').val();

                // clear the simple search text field and reset datatable
                $(".dataTables_filter input").val("").keyup();

                $simpleSearch.addClass("sp-invisible");
                
                //resize the library table to avoid a double scroll bar. CC-4504
                height = tableHeight - searchHeight;
                $libContent.find(".dataTables_scrolling").css("max-height", height);
            }
            else {
                // clear the advanced search fields
                var divs = $("div#advanced_search").children(':visible');
                $.each(divs, function(i, div){
                    fields = $(div).children().find('input');
                    $.each(fields, function(i, field){
                        if ($(field).val() !== "") {
                            $(field).val("");
                            // we need to reset the results when removing
                            // an advanced search field
                            $(field).keyup();
                        }
                    });
                });
                
                //reset datatable with previous simple search results (if any)
                $(".dataTables_filter input").val(simpleSearchText).keyup();

                $simpleSearch.removeClass("sp-invisible");
                $fs.addClass("closed");
                
              //resize the library table to avoid a double scroll bar. CC-4504
                $libContent.find(".dataTables_scrolling").css("max-height", tableHeight);
            }
        });
       
        var tableHeight = getTableHeight();
        $libContent.find(".dataTables_scrolling").css("max-height", tableHeight);
        
        AIRTIME.library.setupLibraryToolbar(oTable);

        $libTable.find("tbody").on("dblclick", "tr", function(ev) {
            var $tr = $(this),
                data = $tr.data("aData");
            AIRTIME.library.dblClickAdd(data, data.ftype);
        });

        $libTable.find("tbody").on("click", "tr", function(ev) {
            var $tr = $(this),
            // Get the ID of the selected row
                $rowId = $tr.attr("id");

            if (ev.shiftKey && $previouslySelected !== undefined) {
                if ($previouslySelected.attr("id") == $rowId) {
                    return;
                }

                // If the selected row comes before the previously selected row,
                // we want to select previous rows, otherwise we select next
                if ($previouslySelected.prevAll("#"+$rowId).length !== 0) {
                    $previouslySelected.prevUntil($tr).each(function(i, el){
                        mod.selectItem($(el));
                    });
                } else {
                    $previouslySelected.nextUntil($tr).each(function(i, el){
                        mod.selectItem($(el));
                    });
                }

                mod.selectItem($tr);
            } else if (ev.ctrlKey && $previouslySelected !== undefined) {
                mod.selectItem($tr);
            } else {
                $("."+LIB_SELECTED_CLASS).each(function(i, el) {
                    mod.deselectItem($(el))
                });
                mod.selectItem($tr);
            }

            // Remember this row so we can properly multiselect
            $previouslySelected = $tr;
        });
    };
    mod.libraryInit = libraryInit;
    
    return AIRTIME;
    
}(AIRTIME || {}));

function buildEditMetadataDialog (json){
    var dialog = $(json.dialog);
     
    dialog.dialog({
        autoOpen: false,
        title: $.i18n._("Edit Metadata"),
        width: 460,
        height: 660,
        modal: true,
        close: closeDialogLibrary
    });

    dialog.dialog('open');
}

function closeDialogLibrary(event, ui) {
    $(this).remove();
}

/*
 * This function is called from dataTables.columnFilter.js
 */
function validateAdvancedSearch(divs) {
    var valid,
        allValid = true,
        fieldName,
        fields,
        searchTerm = Array(),
        searchTermType,
        regExpr,
        timeRegEx = "\\d{2}[:]([0-5]){1}([0-9]){1}[:]([0-5]){1}([0-9]){1}([.]\\d{1,6})?",
        dateRegEx = "\\d{4}[-]\\d{2}[-]\\d{2}?",
        integerRegEx = "^\\d+$",
        numericRegEx = "^\\d+[.]?\\d*$";

    searchTerm[0] = "";
    searchTerm[1] = "";
    $.each(divs, function(i, div){
        fieldName = $(div).children(':nth-child(2)').attr('id');
        fields = $(div).children().find('input');
        searchTermType = validationTypes[fieldName];
        valid = true;
        
        $.each(fields, function(i, field){
            searchTerm[i] = $(field).val();

            if (searchTerm[i] !== "") {
                
                if (searchTermType === "l") {
                    regExpr = new RegExp("^" +timeRegEx+ "$");
                } else if (searchTermType === "t") {
                    var pieces = searchTerm[i].split(" ");
                    if (pieces.length === 2) {
                        regExpr = new RegExp("^" +dateRegEx+ " " +timeRegEx+ "$");
                    } else if (pieces.length === 1) {
                        regExpr = new RegExp("^" +dateRegEx+ "$");
                    }
                } else if (searchTermType === "i") {
                    regExpr = new RegExp(integerRegEx);
                } else if (searchTermType === "n") {
                    regExpr = new RegExp(numericRegEx);
                    if (searchTerm[i].charAt(0) === "-") {
                        searchTerm[i] = searchTerm[i].substr(1);
                    }
                }
                
                // string fields do not need validation
                if (searchTermType !== "s") {
                    valid = regExpr.test(searchTerm[i]);
                    if (!valid) allValid = false;
                }
                
                addRemoveValidationIcons(valid, $(field), searchTermType);
                
            /*
             * Empty fields should not have valid/invalid indicator Range values
             * are considered valid even if only the 'From' value is provided.
             * Therefore, if the 'To' value is empty but the 'From' value is not
             * empty we need to keep the validation icon on screen.
             */
            } else if (searchTerm[0] === "" && searchTerm[1] !== "" ||
                    searchTerm[0] === "" && searchTerm[1] === ""){
                if ($(field).closest('div').children(':last-child').hasClass('checked-icon') ||
                        $(field).closest('div').children(':last-child').hasClass('not-available-icon')) {
                    $(field).closest('div').children(':last-child').remove();
                }
            }
            
            if (!valid) {
                return false;
            }
        });
    });

    return allValid;
}

function addRemoveValidationIcons(valid, field, searchTermType) {
    var title = '';
    if (searchTermType === 'i') {
        title = $.i18n._('Input must be a positive number');
    } else if (searchTermType === 'n') {
        title = $.i18n._('Input must be a number');
    } else if (searchTermType === 't') {
        title = $.i18n._('Input must be in the format: yyyy-mm-dd');
    } else if (searchTermType === 'l') {
        title = $.i18n._('Input must be in the format: hh:mm:ss.t');
    }
    
    var validIndicator = " <span class='checked-icon sp-checked-icon'></span>",
        invalidIndicator = " <span title='"+title+"' class='not-available-icon sp-checked-icon'></span>";
    
    if (valid) {
        if (!field.closest('div').children(':last-child').hasClass('checked-icon')) {
            // remove invalid icon before adding valid icon
            if (field.closest('div').children(':last-child').hasClass('not-available-icon')) {
                field.closest('div').children(':last-child').remove();
            }
            field.closest('div').append(validIndicator);
        }
    } else {
        if (!field.closest('div').children(':last-child').hasClass('not-available-icon')) {
            // remove valid icon before adding invalid icon
            if (field.closest('div').children(':last-child').hasClass('checked-icon')) {
                field.closest('div').children(':last-child').remove();
            }
            field.closest('div').append(invalidIndicator);
        }
    }
}

/*
 * Validation types: s => string i => integer n => numeric (positive/negative,
 * whole/decimals) t => timestamp l => length
 */
var validationTypes = {
    "album_title" : "s",
    "artist_name" : "s",
    "bit_rate" : "i",
    "bpm" : "i",
    "comments" : "s",
    "composer" : "s",
    "conductor" : "s",
    "copyright" : "s",
    "cuein" : "l",
    "cueout" : "l",
    "encoded_by" : "s",
    "utime" : "t",
    "mtime" : "t",
    "lptime" : "t",
    "disc_number" : "i",
    "genre" : "s",
    "isrc_number" : "s",
    "label" : "s",
    "language" : "s",
    "length" : "l",
    "lyricist" : "s",
    "mood" : "s",
    "mime" : "s",
    "name" : "s",
    "orchestra" : "s",
    "owner_id" : "s",
    "rating" : "i",
    "replay_gain" : "n",
    "sample_rate" : "n",
    "track_title" : "s",
    "track_number" : "i",
    "info_url" : "s",
    "year" : "i"
};

$(document).ready(function() {
    $('#editmdsave').live("click", function() {
        var file_id = $('#file_id').val(),
            data = $("#edit-md-dialog form").serializeArray();
        $.post(baseUrl+'library/edit-file-md', {format: "json", id: file_id, data: data}, function() {
            $("#edit-md-dialog").dialog().remove();

            // don't redraw the library table if we are on calendar page
            // we would be on calendar if viewing recorded file metadata
            if ($("#schedule_calendar").length === 0) {
                oTable.fnStandingRedraw();
            }
        });
    });
    
    $('#editmdcancel').live("click", function() {
        $("#edit-md-dialog").dialog().remove();
    });

    $('#edit-md-dialog').live("keyup", function(event) {
        if (event.keyCode === 13) {
            $('#editmdsave').click();
        }
    });

    $(".media_type_selector").on("click", function() {
        $(".media_type_selector").each(function() {
            $(this).removeClass("selected");
        });
        $(this).addClass("selected");
        oTable.fnDraw();
    });
});

