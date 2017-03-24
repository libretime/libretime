var AIRTIME = (function(AIRTIME) {
    var mod,
        libraryInit,
        oTable,
        $libContent,
        $libTable,
        LIB_SELECTED_CLASS = "lib-selected",
        chosenItems = {},
        visibleChosenItems = {},
        $previouslySelected,
        flagForDeselection = false,
        $datatables = {},
        onDashboard = (window.location.href.indexOf("showbuilder") > -1);


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
        "description" : "s",
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

    /*  ############################################
                       CONFIGURATION
        ############################################ */

    mod.MediaTypeIntegerEnum = Object.freeze({
        DEFAULT: 1,
        FILE: 1,
        PLAYLIST: 2,
        BLOCK: 3,
        WEBSTREAM: 4,
        PODCAST: 5
    });

    mod.MediaTypeStringEnum = Object.freeze({
        FILE: "au",
        PLAYLIST: "pl",
        BLOCK: "bl",
        WEBSTREAM: "ws",
        PODCAST: "pc"
    });

    mod.MediaTypeFullToStringEnum = Object.freeze({
        //FILE: "au",
        "playlist": "pl",
        "block": "bl",
        "stream": "ws"
    });

    mod.DataTableTypeEnum = Object.freeze({
        LIBRARY         : "library",
        PODCAST         : "podcast",
        PODCAST_EPISODES: "podcastEpisodes"
    });

    // TODO: once the new manual pages are added, change links!
    mod.placeholder = function(mediaType) {
        switch (mediaType) {
            // TODO: remove duplication in a nice way?
            case mod.MediaTypeIntegerEnum.FILE:
                return {
                    "media": "tracks",
                    "icon": "icon-music",
                    "subtext": "Click 'Upload' to add some now.",
                    "href": "http://libretime.org/manual/add-media/"
                };
            case mod.MediaTypeIntegerEnum.PLAYLIST:
                return {
                    "media": "playlists",
                    "icon": "icon-list",
                    "subtext": "Click 'New' to create one now.",
                    "href": "http://libretime.org/manual/library/"
                };
            case mod.MediaTypeIntegerEnum.BLOCK:
                return {
                    "media": "smart blocks",
                    "icon": "icon-time",
                    "subtext": "Click 'New' to create one now.",
                    "href": "http://libretime.org/manual/library/"
                };
            case mod.MediaTypeIntegerEnum.WEBSTREAM:
                return {
                    "media": "webstreams",
                    "icon": "icon-random",
                    "subtext": "Click 'New' to create one now.",
                    "href": "http://libretime.org/manual/library/"
                };
            case mod.MediaTypeIntegerEnum.PODCAST:
                return {
                    "media": "podcasts",
                    "icon": "icon-headphones",
                    "subtext": "Click 'Add' to create one now.",
                    "href": "http://www.apple.com/ca/itunes/podcasts/fanfaq.html"
                };
            default:
                break;
        }
    };

    /*  ############################################
                     END CONFIGURATION
        ############################################ */

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

    mod.createToolbarButtons = function () {
        $menu = $("<div class='btn-toolbar' />");
        if ($(".ui-dialog-content").length === 0) {
            $menu
                .append(
                    "<div class='btn-group' title=" + $.i18n._('New') + ">" +
                        "<button class='btn btn-small btn-new' id='sb-new'>" +
                            "<i class='icon-white icon-plus'></i>" +
                            "<span>" + $.i18n._('New') + "</span>" +
                        "</button>" +
                    "</div>"
                ).append(
                    "<div class='btn-group' title=" + $.i18n._('Edit') + ">" +
                        "<button class='btn btn-small' id='sb-edit'>" +
                            "<i class='icon-white icon-pencil'></i>" +
                            "<span>" + $.i18n._('Edit') + "</span>" +
                        "</button>" +
                    "</div>"
                );
        }

        $menu
            .append(
                "<div class='btn-group'>" +
                    "<button class='btn btn-small' id='library-plus'>" +
                        "<i class='icon-white icon-plus'></i>" +
                        "<span id='lib-plus-text'></span>" +
                    "</button>" +
                "</div>"
            ).append(
                "<div class='btn-group' title=" + $.i18n._('Delete') + ">" +
                    "<button class='btn btn-small btn-danger' id='sb-delete'>" +
                        "<i class='icon-white icon-trash'></i>" +
                        "<span>" + $.i18n._('Delete') + "</span>" +
                    "</button>" +
                "</div>"
            );

        if (onDashboard) {
            $menu.append(
                "<div class='btn-group' title=" + $.i18n._('Publish') + ">" +
                "<button class='btn btn-small' id='publish-btn'>" +
                "<i class='icon-soundcloud-white'></i>" +
                "<span>" + $.i18n._('Publish') + "</span>" +
                "</button>" +
                "</div>"
            );
        }
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
            AIRTIME.button.enableButton("btn-group #sb-delete", false);
        }
        else {
            AIRTIME.button.disableButton("btn-group #sb-delete", false);
        }
    };

    mod.checkEditButton = function() {
        var selected = mod.getChosenItemsLength(),
            check = false;

        if (selected >= 1) {
            check = true;
        }

        if (check === true) {
            AIRTIME.button.enableButton("btn-group #sb-edit", false);
        }
        else {
            AIRTIME.button.disableButton("btn-group #sb-edit", false);
        }
    };

    mod.checkNewButton = function() {
        var selected = $(".media_type_selector.selected").data("selection-id"),
            check = false;

        if (selected != AIRTIME.library.MediaTypeIntegerEnum.FILE) {
            check = true;
        }

        if (check === true) {
            AIRTIME.button.enableButton("btn-group #sb-new", false);
        }
        else {
            AIRTIME.button.disableButton("btn-group #sb-new", false);
        }
    };

    mod.checkPublishButton = function() {
        var selected = mod.getChosenItemsLength(),
            mediaType = $(".media_type_selector.selected").data("selection-id"),
            check = false;

        if (mediaType == AIRTIME.library.MediaTypeIntegerEnum.FILE && selected > 0) {
            check = true;
        }

        if (check === true) {
            AIRTIME.button.enableButton("btn-group #publish-btn", false);
        } else {
            AIRTIME.button.disableButton("btn-group #publish-btn", false);
        }
    };

    mod.checkToolBarIcons = function() {
        AIRTIME.library.checkAddButton();
        AIRTIME.library.checkDeleteButton();
        AIRTIME.library.checkEditButton();
        AIRTIME.library.checkNewButton();
        AIRTIME.library.checkPublishButton();
    };

    mod.getSelectedData = function() {
        var id,
            data = [],
            cItem,
            $trs;

        //$.fn.reverse = [].reverse;
        //$trs = $libTable.find("tr").reverse();

        // Get visible items and check if any chosenItems are visible
        $trs = $libTable.find("tr");
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
                mod.checkItem($el);
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

    mod.checkItem = function($el) {
        $el.find(".library_checkbox > input").prop('checked', true);
        $("#super-checkbox").prop("checked", true);
    };

    mod.uncheckItem = function($el) {
        $el.find(".library_checkbox > input").prop('checked', false);
        if ($("." + LIB_SELECTED_CLASS).length == 0) {
            $("#super-checkbox").prop("checked", false);
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
        mod.checkItem($el);
        // Remember this row so we can properly multiselect
        $previouslySelected = $el;

        mod.checkToolBarIcons();
    };

    mod.deselectItem = function($el) {
        mod.unHighlightItem($el);
        mod.removeFromChosen($el);
        mod.uncheckItem($el);
        mod.checkToolBarIcons();
    };

    mod.selectAll = function($els) {
        $els.each(function(i, el){
            mod.highlightItem($(el));
            mod.addToChosen($(el));
            mod.checkItem($(el));
        });
        $previouslySelected = $els.last();
        mod.checkToolBarIcons();
    };

    mod.deselectAll = function($els) {
        $els.each(function(i, el){
            mod.unHighlightItem($(el));
            mod.removeFromChosen($(el));
            mod.uncheckItem($(el));
        });
        $previouslySelected = undefined;
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
        mod.selectAll($trs);
    };

    /*
     * deselects all items that the user can currently see. (behaviour taken
     * from gmail)
     */
    mod.deselectCurrentPage = function() {
        var $trs = $libTable.find("tr");
        mod.deselectAll($trs);
    };

    mod.selectNone = function() {
        var $trs = $libTable.find("tr");
        chosenItems = {};
        mod.deselectAll($trs);
    };

    mod.fnRedraw = function() {
        oTable.fnStandingRedraw();
    };

    mod.fnDeleteItems = function(aMedia) {
        //Prevent the user from spamming the delete button while the AJAX request is in progress
        AIRTIME.button.disableButton("btn-group #sb-delete", false);
        var openTabObjectIds = $(".obj_id"),
            mediaIds = [];
        for (var i in aMedia) {
            mediaIds.push(parseInt(aMedia[i].id));
        }

        openTabObjectIds.each(function(i, el) {
            var v = parseInt($(el).val());
            if ($.inArray(v, mediaIds) > -1) {
                AIRTIME.tabs.get($(el).closest(".pl-content").attr("data-tab-id")).close();
            }
        });

        $.post(baseUrl+"library/delete",
            {"format": "json", "media": aMedia},
            function(json){
                if (json.message !== undefined) {
                    alert(json.message);
                }

                chosenItems = {};
                if (oTable == $datatables[mod.DataTableTypeEnum.PODCAST_EPISODES]) {
                    mod.podcastEpisodeTableWidget.reload();
                } else {
                    oTable.fnStandingRedraw();
                }

                //Re-enable the delete button
                AIRTIME.button.enableButton("btn-group #sb-delete", false);
            });
        mod.selectNone();
    };

    mod.fnDeleteSelectedItems = function() {
        if (confirm($.i18n._('Are you sure you want to delete the selected item(s)?'))) {
            var aData = AIRTIME.library.getSelectedData(),
                item,
                temp,
                aMedia = [],
                currentObjId = $(".side_playlist.active-tab").find(".obj_id").val(),
                currentObjType = $(".side_playlist.active-tab").find(".obj_type").val(),
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
        }
    };

    mod.handleAjaxError = function (r) {
        // If the request was denied due to permissioning
        if (r.status === 403) {
            // Hide the processing div
            var wrapper = $("#library_display_wrapper");
            wrapper.find(".dt-process-rel").hide();
            wrapper.find('.empty_placeholder_text').text($.i18n._("You don't have permission to view the library."));
            wrapper.find('.empty_placeholder').show();
        }
    };


    libraryInit = function() {

        $libContent = $("#library_content");

        /*
         * Icon hover states in the toolbar.
         */

        var colReorderMap = new Array();

        $libTable = $("#library_display");

        // put hidden columns at the top to insure they can never be visible
        // on the table through column reordering.

        //IMPORTANT: WHEN ADDING A NEW COLUMN PLEASE CONSULT WITH THE WIKI
        // https://wiki.sourcefabric.org/display/CC/Adding+a+new+library+datatable+column
        var cols = [
            /* ftype */           { "sTitle" : ""                             , "mDataProp" : "ftype"        , "bSearchable" : false                 , "bVisible"    : false                   },
            /* Checkbox */        { "sTitle" : ""                             , "mDataProp" : "checkbox"     , "bSortable"   : false                 , "bSearchable" : false                   , "sWidth" : "16px"         , "sClass" : "library_checkbox" },
            /* Type */            { "sTitle" : ""                             , "mDataProp" : "image"        , "bSortable"   : false                 , "bSearchable" : false                   , "sWidth" : "16px"    , "sClass" : "library_type" , "iDataSort" : 0 },
            /* Is Scheduled */    { "sTitle" : $.i18n._("Scheduled")          , "mDataProp" : "is_scheduled" , "bVisible"    : false                 , "bSearchable" : false                   , "sWidth" : "90px"    , "sClass" : "library_is_scheduled" },
            ///* Is Playlist */     { "sTitle" : $.i18n._("Playlist / Block")   , "mDataProp" : "is_playlist"  , "bSearchable" : false                 , "sWidth"      : "110px"                  , "sClass" : "library_is_playlist"}  ,
            /* Title */           { "sTitle" : $.i18n._("Title")              , "mDataProp" : "track_title"  , "sClass"      : "library_title"       , "sWidth"      : "170px"                 },
            /* Creator */         { "sTitle" : $.i18n._("Creator")            , "mDataProp" : "artist_name"  , "sClass"      : "library_creator"     , "sWidth"      : "160px"                 },
            /* Album */           { "sTitle" : $.i18n._("Album")              , "mDataProp" : "album_title"  , "sClass"      : "library_album"       , "sWidth"      : "150px"                 },
            /* Bit Rate */        { "sTitle" : $.i18n._("Bit Rate")           , "mDataProp" : "bit_rate"     , "bVisible"    : false                 , "sClass"      : "library_bitrate"       , "sWidth" : "80px"         },
            /* BPM */             { "sTitle" : $.i18n._("BPM")                , "mDataProp" : "bpm"          , "bVisible"    : false                 , "sClass"      : "library_bpm"           , "sWidth" : "50px"         },
            /* Composer */        { "sTitle" : $.i18n._("Composer")           , "mDataProp" : "composer"     , "bVisible"    : false                 , "sClass"      : "library_composer"      , "sWidth" : "150px"        },
            /* Conductor */       { "sTitle" : $.i18n._("Conductor")          , "mDataProp" : "conductor"    , "bVisible"    : false                 , "sClass"      : "library_conductor"     , "sWidth" : "125px"        },
            /* Copyright */       { "sTitle" : $.i18n._("Copyright")          , "mDataProp" : "copyright"    , "bVisible"    : false                 , "sClass"      : "library_copyright"     , "sWidth" : "125px"        },
            /* Cue In */          { "sTitle" : $.i18n._("Cue In")             , "mDataProp" : "cuein"        , "bVisible"    : false                 , "sClass"      : "library_length"        , "sWidth" : "80px"         },
            /* Cue Out */         { "sTitle" : $.i18n._("Cue Out")            , "mDataProp" : "cueout"       , "bVisible"    : false                 , "sClass"      : "library_length"        , "sWidth" : "80px"         },
            /* Description */     { "sTitle" : $.i18n._("Description")        , "mDataProp" : "description"  , "bVisible"    : false                 , "sClass"      : "library_description"   , "sWidth" : "150px"        },
            /* Encoded */         { "sTitle" : $.i18n._("Encoded By")         , "mDataProp" : "encoded_by"   , "bVisible"    : false                 , "sClass"      : "library_encoded"       , "sWidth" : "150px"        },
            /* Genre */           { "sTitle" : $.i18n._("Genre")              , "mDataProp" : "genre"        , "bVisible"    : false                 , "sClass"      : "library_genre"         , "sWidth" : "100px"        },
            /* ISRC Number */     { "sTitle" : $.i18n._("ISRC")               , "mDataProp" : "isrc_number"  , "bVisible"    : false                 , "sClass"      : "library_isrc"          , "sWidth" : "150px"        },
            /* Label */           { "sTitle" : $.i18n._("Label")              , "mDataProp" : "label"        , "bVisible"    : false                 , "sClass"      : "library_label"         , "sWidth" : "125px"        },
            /* Language */        { "sTitle" : $.i18n._("Language")           , "mDataProp" : "language"     , "bVisible"    : false                 , "sClass"      : "library_language"      , "sWidth" : "125px"        },
            /* Last Modified */   { "sTitle" : $.i18n._("Last Modified")      , "mDataProp" : "mtime"        , "bVisible"    : false                 , "sClass"      : "library_modified_time" , "sWidth" : "155px"        },
            /* Last Played */     { "sTitle" : $.i18n._("Last Played")        , "mDataProp" : "lptime"       , "bVisible"    : false                 , "sClass"      : "library_modified_time" , "sWidth" : "155px"        },
            /* Length */          { "sTitle" : $.i18n._("Length")             , "mDataProp" : "length"       , "sClass"      : "library_length"      , "sWidth"      : "80px"                  },
            /* Mime */            { "sTitle" : $.i18n._("Mime")               , "mDataProp" : "mime"         , "bVisible"    : false                 , "sClass"      : "library_mime"          , "sWidth" : "80px"         },
            /* Mood */            { "sTitle" : $.i18n._("Mood")               , "mDataProp" : "mood"         , "bVisible"    : false                 , "sClass"      : "library_mood"          , "sWidth" : "70px"         },
            /* Owner */           { "sTitle" : $.i18n._("Owner")              , "mDataProp" : "owner_id"     , "bVisible"    : false                 , "sClass"      : "library_language"      , "sWidth" : "125px"        },
            /* Replay Gain */     { "sTitle" : $.i18n._("Replay Gain")        , "mDataProp" : "replay_gain"  , "bVisible"    : false                 , "sClass"      : "library_replay_gain"   , "sWidth" : "125px"        },
            /* Sample Rate */     { "sTitle" : $.i18n._("Sample Rate")        , "mDataProp" : "sample_rate"  , "bVisible"    : false                 , "sClass"      : "library_sr"            , "sWidth" : "125px"        },
            /* Track Number */    { "sTitle" : $.i18n._("Track Number")       , "mDataProp" : "track_number" , "bVisible"    : false                 , "sClass"      : "library_track"         , "sWidth" : "125px"        },
            /* Upload Time */     { "sTitle" : $.i18n._("Uploaded")           , "mDataProp" : "utime"        , "bVisible"    : false                 , "sClass"      : "library_upload_time"   , "sWidth" : "155px"        },
            /* Website */         { "sTitle" : $.i18n._("Website")            , "mDataProp" : "info_url"     , "bVisible"    : false                 , "sClass"      : "library_url"           , "sWidth" : "150px"        },
            /* Year */            { "sTitle" : $.i18n._("Year")               , "mDataProp" : "year"         , "bVisible"    : false                 , "sClass"      : "library_year"          , "sWidth" : "60px"         }
        ];

        if (onDashboard) {
            cols.push(
            /* Context Menu */    { "sTitle" : ""                             , "mDataProp" : "options"      , "bSortable"   : false                 , "bSearchable" : false                   , "sWidth" : "20px", "sClass" : "library_actions" }
            );
        }

        var colExclude = onDashboard ? [0, 1, 2, 32] : [0, 1, 2];

        /*  ############################################
                            DATATABLES
            ############################################ */

        mod.libraryDataTable = $libTable.dataTable({

            // put hidden columns at the top to insure they can never be visible
            // on the table through column reordering.

            //IMPORTANT: WHEN ADDING A NEW COLUMN PLEASE CONSULT WITH THE WIKI
            // https://wiki.sourcefabric.org/display/CC/Adding+a+new+library+datatable+column
            "aoColumns": cols,
            "bProcessing": true,
            "bServerSide": true,
            "aLengthMenu": [25, 50, 100],
            "bStateSave": true,
            "fnStateSaveParams": function (oSettings, oData) {
                // remove oData components we don't want to save.
                delete oData.oSearch;
                delete oData.aoSearchCols;
            },
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem('datatables-library', JSON.stringify(oData));

                // Sadly, this is necessary because we need to unscramble the colReorder map on the backend
                $.ajax({
                    url: baseUrl + "usersettings/set-library-datatable",
                    type: "POST",
                    data: {settings: oData, format: "json"},
                    dataType: "json"
                });

                colReorderMap = oData.ColReorder;
            },
            "fnStateLoad": function fnLibStateLoad(oSettings) {
                var settings = JSON.parse(localStorage.getItem('datatables-library'));

                // local storage was empty lets get something from the backend
                if (settings === null) {
                    // we have a datatables implementation that is so old we need to async:false ;(
                    // see http://legacy.datatables.net/usage/callbacks#fnStateLoad for info and
                    // feel free to start trying to port this to a modern version ;)
                    $.ajax({
                        'url': baseUrl + 'usersettings/get-library-datatable',
                        'async': false, // <<< every sane browser will warn that this is not nice
                        'dataType': 'json',
                        'success': function(oData) {
                            localStorage.setItem('datatables-library', JSON.stringify(oData));
                            settings = oData;
                        }
                    });
                }
                // Hacky; always set the visibility of the last column (actions buttons) to true
                if (settings && settings.abVisCols) settings.abVisCols[settings.abVisCols.length - 1] = true;

                return settings;
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
                            a[i] = (a[i] === "true");
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

            "sAjaxSource": baseUrl + "Library/contents-feed",
            "sAjaxDataProp": "files",

            "fnServerData": function (sSource, aoData, fnCallback) {
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
                aoData.push({name: "format", value: "json"});
                aoData.push({name: "advSearch", value: advSearchValid});

                // push whether to search files/playlists or all.
                type = $(".media_type_selector.selected").data("selection-id");
                type = (type === undefined) ? AIRTIME.library.MediaTypeIntegerEnum.DEFAULT : type;
                aoData.push({name: "type", value: type});

                $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aoData,
                    "success": fnCallback,
                    "error": mod.handleAjaxError
                }).done(function (data) {
                    var filterMessage = $libContent.find('.filter-message');
                    if (data.iTotalRecords > data.iTotalDisplayRecords) {
                        filterMessage.text(
                            $.i18n._("Filtering out ") + (data.iTotalRecords - data.iTotalDisplayRecords)
                            + $.i18n._(" of ") + data.iTotalRecords
                            + $.i18n._(" records")
                        );
                        $('.empty_placeholder').hide();
                        $libTable.find('tr:has(td.dataTables_empty)').show();
                    } else {
                        filterMessage.text("");
                    }
                    $libContent.find('.dataTables_filter input[type="text"]')
                        .css('padding-right', $('#advanced-options').find('button').outerWidth());
                });
            },
            "fnRowCallback": AIRTIME.library.fnRowCallback,
            "fnCreatedRow": function (nRow, aData, iDataIndex) {
                // add checkbox
                $(nRow).find('td.library_checkbox').html("<input type='checkbox' name='cb_" + aData.id + "'>");

                if (onDashboard) {
                    $(nRow).find('td.library_actions')
                        .text("...")
                        .on('click', function (e) {
                            $(this).contextMenu({x: $(e.target).offset().left, y: $(e.target).offset().top})
                        }).html("<div class='library_actions_btn'>...</div>");
                }

                // add audio preview image/button
                if (aData.ftype === "audioclip") {
                    $(nRow).find('td.library_type').html('<img title="' + $.i18n._("Track preview") + '" src="' + baseUrl + 'css/images/icon_audioclip.png">');
                } else if (aData.ftype === "playlist") {
                    $(nRow).find('td.library_type').html('<img title="' + $.i18n._("Playlist preview") + '" src="' + baseUrl + 'css/images/icon_playlist.png">');
                } else if (aData.ftype === "block") {
                    $(nRow).find('td.library_type').html('<img title="' + $.i18n._("Smart Block") + '" src="' + baseUrl + 'css/images/icon_smart-block.png">');
                } else if (aData.ftype === "stream") {
                    $(nRow).find('td.library_type').html('<img title="' + $.i18n._("Webstream preview") + '" src="' + baseUrl + 'css/images/icon_webstream.png">');
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
            },
            // remove any selected nodes before the draw.
            "fnPreDrawCallback": function (oSettings) {

                // make sure any dragging helpers are removed or else they'll be
                // stranded on the screen.
                $("#draggingContainer").remove();
            },
            "fnDrawCallback": AIRTIME.library.fnDrawCallback,

            "aaSorting": [[5, 'asc']],
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "oLanguage": getLibraryDatatableStrings(),

            // z = ColResize, R = ColReorder, C = ColVis
            "sDom": 'Rf<"dt-process-rel"r><"H"<"library_toolbar"C>><"dataTables_scrolling"t<".empty_placeholder"<".empty_placeholder_image"><".empty_placeholder_text">>><"F"lip>>',

            "oColVis": {
                "sAlign": "right",
                "aiExclude": colExclude,
                "sSize": "css",
                "fnStateChange": setFilterElement,
                "buttonText": $.i18n._("Columns"),
                "iOverlayFade": 0
            },

            "oColReorder": {
                "iFixedColumnsRight": 1,
                "iFixedColumns": 3
            },

            "bScrollCollapse": false

        });

        $datatables[mod.DataTableTypeEnum.LIBRARY] = mod.libraryDataTable;

        /*  ############################################
                          END DATATABLES
            ############################################ */

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
                            "<label class='control-label'"+labelStyle+">"+ele.sTitle+"</label>" +
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
        }

        function getLibraryDatatableStrings() {
            //Set up the datatables string translation table with different strings depending on
            //whether you're viewing files, playlists, smart blocks, etc.
            var type = parseInt($(".media_type_selector.selected").data("selection-id"));
            type = (type === undefined) ? AIRTIME.library.MediaTypeIntegerEnum.DEFAULT : type;

            //FIXME: The code that calls this function doesn't work as intended because you can't
            //       change the oLanguage property of a datatable dynamically. :(

            switch (type) {
                /*
                 case 0:
                 return getDatatablesStrings({
                 "sEmptyTable": $.i18n._("No files found"),
                 });
                 break;
                 case 1:
                 return getDatatablesStrings({
                 "sEmptyTable": $.i18n._("No playlists found"),
                 });
                 break;
                 case 2:
                 return getDatatablesStrings({
                 "sEmptyTable": $.i18n._("No smart blocks found"),
                 });
                 break;*/
                default:
                    return getDatatablesStrings({
                        "sEmptyTable": $.i18n._(""),
                        "sZeroRecords": $.i18n._("No matching results found.")
                    });
                    break;
            }

        }

        var selected = $("a[href$='"+location.hash+"']"), table;
        if (selected.parent().data("selection-id") == AIRTIME.library.MediaTypeIntegerEnum.PODCAST) {
            table = mod.DataTableTypeEnum.PODCAST;
        } else {
            table = mod.DataTableTypeEnum.LIBRARY;
        }

        AIRTIME.library.setCurrentTable(table, false);
        oTable = $datatables[table];
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
            }
        });

        AIRTIME.library.setupLibraryToolbar(oTable);

        $libTable.find("tbody").on("dblclick", "tr[class*='lib'] > td:not(.dataTables_empty)", function(ev) {
            var tr = $(this).parent(), data = tr.data("aData");
            AIRTIME.library.dblClickAdd(data, data.ftype);
        });

        $libTable.find("tbody").on("mousedown", "tr[class*='lib'] > td.library_checkbox", function(ev) {
            var $tr = $(this).parent(),
            // Get the ID of the selected row
                $rowId = $tr.attr("id");

            if (!$tr.hasClass(LIB_SELECTED_CLASS)) {
                if (ev.shiftKey && $previouslySelected !== undefined) {
                    if ($previouslySelected.attr("id") == $rowId) {
                        return;
                    }

                    // If the selected row comes before the previously selected row,
                    // we want to select previous rows, otherwise we select next
                    if ($previouslySelected.prevAll("#" + $rowId).length !== 0) {
                        $previouslySelected.prevUntil($tr).each(function (i, el) {
                            mod.selectItem($(el));
                        });
                    } else {
                        $previouslySelected.nextUntil($tr).each(function (i, el) {
                            mod.selectItem($(el));
                        });
                    }
                }
                mod.selectItem($tr);
            } else {
                flagForDeselection = true;
            }
        });

        // add the play function to the library_type td
        $libTable.on("click", "td.library_type", function () {
            var aData = $(this).parent().data().aData;

            if (aData.ftype === 'playlist' && aData.length !== '0.0') {
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

        $libTable.find("tbody").on("mousedown", "tr[class*='lib'] > td:not(.library_checkbox, .dataTables_empty)", function(ev) {
            var $tr = $(this).parent(),
            // Get the ID of the selected row
                $rowId = $tr.attr("id");

            if (ev.which === 3 /* Right click */) {
                mod.selectNone();
                mod.selectItem($tr);
                return;
            }

            if (!$tr.hasClass(LIB_SELECTED_CLASS)) {
                if (ev.shiftKey && $previouslySelected !== undefined) {
                    if ($previouslySelected.attr("id") == $rowId) {
                        return;
                    }

                    // If the selected row comes before the previously selected row,
                    // we want to select previous rows, otherwise we select next
                    if ($previouslySelected.prevAll("#" + $rowId).length !== 0) {
                        $previouslySelected.prevUntil($tr).each(function (i, el) {
                            mod.selectItem($(el));
                        });
                    } else {
                        $previouslySelected.nextUntil($tr).each(function (i, el) {
                            mod.selectItem($(el));
                        });
                    }
                } else if (!ev.ctrlKey) {
                    mod.selectNone();
                }

                mod.selectItem($tr);
            } else if (ev.ctrlKey) {
                flagForDeselection = true;
            }
        });

        $libTable.find("tbody").on("click", "tr > td.library_checkbox", function() {
            var tr = $(this).parent();
            if (flagForDeselection) {
                flagForDeselection = false;
                $previouslySelected = undefined;
                mod.deselectItem(tr);
            } else {
                mod.checkItem(tr);
            }
        });

        $libTable.find("tbody").on("click", "tr > td:not(.library_checkbox, .dataTables_empty)", function(e) {
            var tr = $(this).parent();
            if (flagForDeselection) {
                flagForDeselection = false;
                $previouslySelected = undefined;
                mod.deselectItem(tr);
            } else if (!(e.shiftKey || e.ctrlKey)) {
                mod.selectNone();
                mod.selectItem(tr);
            }
        });

        $libTable.find("thead").on("click", "th > input[type='checkbox']", function() {
            if ($(this).is(":checked")) {
                AIRTIME.library.selectCurrentPage();
                $(this).prop("checked", true);
            } else {
                AIRTIME.library.selectNone();
                $(this).prop("checked", false);
            }
        });

        $('#sb-actions').on("click", function(e) {
            $("#library_display tr:has(td)").contextMenu({x: $(e.target).offset().left, y: $(e.target).offset().top});
        });

        // begin context menu initialization.
        AIRTIME.library.ctxMenu = !onDashboard ? {} : $.contextMenu({
            selector: "#library_display tr[class*='lib']:has(td)",
            //trigger: "left",
            trigger: "custom",

            build: function($el, e) {
                var data, screen, items, callback, $tr;

                $tr = $el;
                data = $tr.data("aData");
                screen = $tr.data("screen");

                function processMenuItems(oItems) {

                        // define an add to playlist callback.
                    if (oItems.pl_add !== undefined) {
                        var aItems = [];

                        callback = function() {
                            aItems.push(new Array(data.id, data.ftype));
                            AIRTIME.playlist.fnAddItems(aItems, undefined, 'after');
                        };

                        oItems.pl_add.callback = callback;
                    }

                    // define an edit callback.
                    if (oItems.edit !== undefined) {
                        if (data.ftype === "audioclip") {
                            callback = function() {
                                $.get(oItems.edit.url, {format: "json"}, function(json){
                                    AIRTIME.playlist.fileMdEdit(json, data.tr_id);
                                });
                            };
                        } else if (data.ftype === "playlist" || data.ftype === "block") {
                            callback = function() {
                                AIRTIME.playlist.fnEdit(data, baseUrl+'playlist/edit');
                                AIRTIME.playlist.validatePlaylistElements();
                            };
                        } else if (data.ftype === "stream") {
                            callback = function() {
                                AIRTIME.playlist.fnEdit(data, baseUrl + 'webstream/edit');
                            }
                        } else {
                            throw new Exception($.i18n._("Unknown type: ") + data.ftype);
                        }
                        oItems.edit.callback = callback;
                    }

                    // define a play callback.
                    if (oItems.play !== undefined) {

                        if (oItems.play.mime !== undefined) {
                            if (!isAudioSupported(oItems.play.mime)) {
                                oItems.play.disabled = true;
                            }
                        }

                        callback = function() {
                            if (data.ftype === 'playlist' && data.length !== '0.0'){
                                playlistIndex = $(this).attr('id').substring(3); // remove the pl_
                                open_playlist_preview(playlistIndex, 0);
                            } else if (data.ftype === 'audioclip' || data.ftype === 'stream') {
                                open_audio_preview(data.ftype, data.id);
                            } else if (data.ftype === 'block') {
                                blockIndex = $(this).attr('id').substring(3); // remove the pl_
                                open_block_preview(blockIndex, 0);
                            }
                        };
                        oItems.play.callback = callback;
                    }

                    // define a delete callback.
                    if (oItems.del !== undefined) {

                        // delete through the playlist controller, will reset
                        // playlist screen if this is the currently edited
                        // playlist.
                        if ((data.ftype === "playlist" || data.ftype === "block") && screen === "playlist") {
                            callback = function() {
                                aMedia = [];
                                aMedia.push({"id": data.id, "type": data.ftype});
                                if (confirm($.i18n._('Are you sure you want to delete the selected item?'))) {
                                    AIRTIME.library.fnDeleteItems(aMedia);
                                }
                            };
                        }
                        else {
                            callback = function() {
                                var media = [];

                                if (confirm($.i18n._('Are you sure you want to delete the selected item?'))) {

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

                    // Publish.
                    if (oItems.publish !== undefined) {
                        if (data.ftype === "audioclip") {
                            callback = function() {
                                AIRTIME.publish.openPublishDialog(data.id);
                            };
                        }
                        oItems.publish.callback = callback;
                    }


                    // define a download callback.
                    if (oItems.download !== undefined) {

                        callback = function() {
                            document.location.href = oItems.download.url;
                        };
                        oItems.download.callback = callback;
                    }
                    // add callbacks for Soundcloud menu items.
                    if (oItems.soundcloud !== undefined) {
                        var soundcloud = oItems.soundcloud.items;

                        if (soundcloud.update !== undefined) {
                            callback = function() {
                                $.post(soundcloud.update.url, function () {});
                            };
                            soundcloud.update.callback = callback;
                        }

                        // define a view on soundcloud callback
                        if (soundcloud.view !== undefined) {
                            callback = function() {
                                window.open(soundcloud.view.url);
                            };
                            soundcloud.view.callback = callback;
                        }
                    }
                    // add callbacks for duplicate menu items.
                    if (oItems.duplicate !== undefined) {
                        var url = oItems.duplicate.url;
                        callback = function() {
                            $.post(url, {format: "json", id: data.id }, function(json){
                                oTable.fnStandingRedraw();
                            });
                        };
                        oItems.duplicate.callback = callback;
                    }
                    // remove 'Add to smart block' option if the current
                    // block is dynamic
                    if ($('input:radio[name=sp_type]:checked').val() === "1") {
                        delete oItems.pl_add;
                    }
                    items = oItems;
                }

                request = $.ajax({
                    url: baseUrl+"library/context-menu",
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

    /**
     * Show the given table in the left-hand pane of the dashboard and give it internal focus
     *
     * @param {string} table            the string name of the table to show
     * @param {boolean} [redraw=true]   whether or not to redraw the table
     */
    mod.setCurrentTable = function (table, redraw) {
        if (typeof redraw === 'undefined') {
            redraw = true;
        }
        var dt = $datatables[table],
            wrapper = $(dt).closest(".dataTables_wrapper");
        if (dt && typeof dt.fnClearTable === 'function') {
            dt.fnClearTable(false);
        }
        // Don't redraw if we're switching to another hash for the library table
        $.when(redraw ? dt.fnDraw() : function () {}).done(function () {
            $("#library_content").find(".dataTables_wrapper").hide();
            wrapper.show();
        });
        oTable = dt;
    };

    mod.getCurrentTable = function () {
        return oTable;
    };

    mod.openPodcastEpisodeDialog = function () {
        var episode = mod.podcastEpisodeTableWidget.getSelectedRows()[0];
        $("body").append("<div id='podcast_episode_dialog'></div>");
        var dialog = $("#podcast_episode_dialog").html(episode.description);
        dialog.html(dialog.text());
        dialog.dialog({
            title: episode.title,
            width: "auto",
            height: "auto",
            modal: true,
            resizable: false,
            dialogClass: "podcast-episode-dialog",
            open: function () {
                // Sometimes the dialog scrolls down partway if there are elements that need to render,
                // like images or video, so scroll it back up on open.
                $(this).parent().scrollTop(0);
            },
            close: function() {
                $(this).dialog('destroy').remove();
            }
        });
    };

    /**
     * Create the podcast datatable widget
     *
     * XXX: should this be moved to podcast.js
     */
    mod.initPodcastDatatable = function()
    {
        var aoColumns = [
            /* Title */          { "sTitle" : $.i18n._("Title")              , "mDataProp" : "title"        , "sClass"      : "library_title"       , "sWidth"  : "170px" },
            /* Creator */        { "sTitle" : $.i18n._("Creator")            , "mDataProp" : "creator"      , "sClass"      : "library_creator"     , "sWidth"  : "160px" },
            /* Website */        { "sTitle" : $.i18n._("Description")        , "mDataProp" : "description"  , "bVisible"    : false                 , "sWidth"  : "150px" },
            /* Year */           { "sTitle" : $.i18n._("Owner")              , "mDataProp" : "owner"        , "bVisible"    : false                 , "sWidth"  : "60px"  },
            /* URL */            { "sTitle" : $.i18n._("Feed URL")           , "mDataProp" : "url"          , "bVisible"    : false                 , "sWidth"  : "60px"  }
            ],
            ajaxSourceURL = baseUrl+"rest/podcast",
            podcastToolbarButtons = AIRTIME.widgets.Table.getStandardToolbarButtons();

        $.extend(true, podcastToolbarButtons[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.NEW],
            {
                title: $.i18n._('Add'),  //"New" Podcast is misleading
                eventHandlers: {
                    click: AIRTIME.podcast.createUrlDialog
                },
                validateConstraints: function () { return true; }
            });
        $.extend(true, podcastToolbarButtons[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.EDIT],
            {
                title: $.i18n._('Settings'),
                iconClass : "icon-cog",
                eventHandlers: {
                    click: AIRTIME.podcast.editSelectedPodcasts
                },
                validateConstraints: function () {
                    return this.getSelectedRows().length >= 1;
                }
            });
        $.extend(true, podcastToolbarButtons[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.DELETE],
            {
                eventHandlers: {
                    click: AIRTIME.podcast.deleteSelectedPodcasts
                },
                validateConstraints: function () {
                    return this.getSelectedRows().length >= 1;
                }
            });

        var openPodcastEpisodeTable = function (podcast) {
            $("#library_filter").append(" - " + podcast.title);
            mod.podcastEpisodeTableWidget.reload(podcast.id);
            mod.podcastTableWidget.clearSelection();
            mod.setCurrentTable(mod.DataTableTypeEnum.PODCAST_EPISODES);
            mod.podcastEpisodeDataTable.closest(".dataTables_wrapper").find(".dataTables_processing")
                .addClass("block-overlay").css("visibility", "visible");
        };

        podcastToolbarButtons["ViewEpisodes"] = {
            title : $.i18n._("View Episodes"),
            iconClass : "icon-chevron-right",
            extraBtnClass : "btn-small",
            elementId : "",
            eventHandlers : {
                click: function () {
                    var podcast = mod.podcastTableWidget.getSelectedRows()[0];
                    openPodcastEpisodeTable(podcast);
                }
            },
            validateConstraints: function () {
                return this.getSelectedRows().length == 1;
            }
        };

        //Set up the div with id "podcast_table" as a datatable.
        mod.podcastTableWidget = new AIRTIME.widgets.Table(
            $('#podcast_table'), //DOM node to create the table inside.
            true,                //Enable item selection
            podcastToolbarButtons, //Toolbar buttons
            {                    //Datatables overrides.
                aoColumns : aoColumns,
                sAjaxSource : ajaxSourceURL,
                oColReorder: {
                    iFixedColumns: 1  // Checkbox
                },
                fnDrawCallback: function () {
                    AIRTIME.library.drawEmptyPlaceholder(this);
                }
            });

        mod._initPodcastEpisodeDatatable();
        // On double click, open a table showing the selected podcast's episodes
        // in the left-hand pane.
        mod.podcastTableWidget.assignDblClickHandler(function () {
            var podcast = mod.podcastDataTable.fnGetData(this);
            openPodcastEpisodeTable(podcast);
        });

        mod.podcastDataTable = mod.podcastTableWidget.getDatatable();
        $datatables[mod.DataTableTypeEnum.PODCAST] = mod.podcastDataTable;
    };

    /**
     * Initialize the podcast episode view for the left-hand pane
     *
     * @private
     */
    mod._initPodcastEpisodeDatatable = function () {
        var buttons = {
                backBtn: {
                    title           : $.i18n._('Back to Podcasts'),
                    iconClass       : 'icon-chevron-left',
                    extraBtnClass   : 'btn-small',
                    elementId       : '',
                    eventHandlers   : {
                        click: function () {
                            $("#library_filter").text($.i18n._("Podcasts"));
                            mod.setCurrentTable(mod.DataTableTypeEnum.PODCAST);
                        }
                    },
                    validateConstraints: function () { return true; }
                }
            },
            defaults = AIRTIME.widgets.Table.getStandardToolbarButtons();

        /**
         * Check the import statuses of each selected episode to see which
         * buttons should be enabled or disabled.
         *
         * @param shouldBeImported whether or not the selected item(s)
         *        should be imported to obtain a valid result.
         *
         * @returns {boolean} true if all selected episodes are valid and
         *                    the button should be enabled, otherwise false.
         */
        var checkSelectedEpisodeImportStatus = function (shouldBeImported) {
            var selected = this.getSelectedRows(), isValid = true;
            if (selected.length == 0) return false;
            $.each(selected, function () {
                if (this.ingested < 0) isValid = false;
                var isImported  = !$.isEmptyObject(this.file);
                if (shouldBeImported ? !isImported : isImported) {
                    isValid = false;
                }
            });
            return isValid;
        };

        // Setup the default buttons (new, edit, delete)
        $.extend(true, defaults[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.NEW],
            {
                title: "Import",
                eventHandlers: {
                    click: function () {
                        var episodes = mod.podcastEpisodeTableWidget.getSelectedRows();
                        AIRTIME.podcast.importSelectedEpisodes(episodes, mod.podcastEpisodeTableWidget);
                    }
                },
                validateConstraints: function () {
                    return checkSelectedEpisodeImportStatus.call(this, false);
                }
            });
        $.extend(true, defaults[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.EDIT],
            {
                eventHandlers: {
                    click: function () {
                        var episodes = mod.podcastEpisodeTableWidget.getSelectedRows();
                        AIRTIME.podcast.editSelectedEpisodes(episodes);
                    }
                },
                validateConstraints: function () {
                    return checkSelectedEpisodeImportStatus.call(this, true);
                }
            });
        $.extend(true, defaults[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.DELETE],
            {
                eventHandlers: {
                    click: function () {
                        var data = [], episodes = mod.podcastEpisodeTableWidget.getSelectedRows();
                        $.each(episodes, function () {
                            data.push({id: this.file.id, type: this.file.ftype});
                        });
                        mod.fnDeleteItems(data);
                    }
                },
                validateConstraints: function () {
                    return checkSelectedEpisodeImportStatus.call(this, true);
                }
            });

        // Reassign these because integer keys take precedence in iteration order - we want to order based on insertion
        // FIXME: this is a pretty flimsy way to try to set up iteration order (possibly not xbrowser compatible?)
        defaults = {
            newBtn : defaults[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.NEW],
            editBtn: defaults[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.EDIT],
            delBtn : defaults[AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.DELETE]
        };

        $.extend(true, buttons, defaults, {
            addToScheduleBtn: {
                title           : $.i18n._('Add to Schedule'),
                iconClass       : '',
                extraBtnClass   : 'btn-small',
                elementId       : '',
                eventHandlers   : {
                    click: function () {
                        var data = [], selected = mod.podcastEpisodeTableWidget.getSelectedRows();
                        $.each(selected, function () { data.push(this.file); });
                        mod.addToSchedule(data);
                    }
                },
                validateConstraints: function () {
                    // TODO: change text + behaviour for playlists, smart blocks, etc.
                    return checkSelectedEpisodeImportStatus.call(this, true);
                }
            },
            viewDescBtn: {
                title : $.i18n._("View"),
                iconClass : "icon-globe",
                extraBtnClass : "btn-small",
                elementId : "",
                eventHandlers : {
                    click: mod.openPodcastEpisodeDialog
                },
                validateConstraints: function () {
                    return this.getSelectedRows().length == 1;
                }
            }
        });

        mod.podcastEpisodeTableWidget = AIRTIME.podcast.initPodcastEpisodeDatatable(
            $("#podcast_episodes_table"),
            {
                aoColumns   : [
                    /* GUID */              { "sTitle" : ""                            , "mDataProp" : "guid"           , "sClass" : "podcast_episodes_guid"        , "bVisible" : false },
                    /* Ingested */          { "sTitle" : $.i18n._("Imported?")         , "mDataProp" : "importIcon"     , "sClass" : "podcast_episodes_imported"    , "sWidth" : "120px" },
                    /* Title */             { "sTitle" : $.i18n._("Title")             , "mDataProp" : "title"          , "sClass" : "podcast_episodes_title"       , "sWidth" : "170px" },
                    /* Author */            { "sTitle" : $.i18n._("Author")            , "mDataProp" : "author"         , "sClass" : "podcast_episodes_author"      , "sWidth" : "170px" },
                    /* Description */       { "sTitle" : $.i18n._("Description")       , "mDataProp" : "description"    , "sClass" : "podcast_episodes_description" , "sWidth" : "300px" },
                    /* Link */              { "sTitle" : $.i18n._("Link")              , "mDataProp" : "link"           , "sClass" : "podcast_episodes_link"        , "sWidth" : "170px" },
                    /* Publication Date */  { "sTitle" : $.i18n._("Publication Date")  , "mDataProp" : "pub_date"       , "sClass" : "podcast_episodes_pub_date"    , "sWidth" : "170px" }
                ],
                bServerSide : false,
                sAjaxSource : null,
                // Initialize the table with empty data so we can defer loading
                // If we load sequentially there's a delay before the table appears
                aaData      : {},
                oColVis     : {
                    buttonText: $.i18n._("Columns"),
                    iOverlayFade: 0,
                    aiExclude: [0, 1, 2]
                },
                oColReorder: {
                    iFixedColumns: 3  // Checkbox + imported
                }
            },
            buttons,
            {
                hideIngestCheckboxes: false,
                emptyPlaceholder: {
                    iconClass: "icon-white icon-th-list",
                    html: $.i18n._("This podcast doesn't have any episodes!")
                    + "<br/>" + $.i18n._("Make sure the RSS feed contains audio items (with enclosure tags).")
                    + "<br/><a target='_blank' href='http://www.apple.com/ca/itunes/podcasts/specs.html'>" + $.i18n._("Learn about podcasts") + "</a>"
                }
            }
        );

        mod.podcastEpisodeDataTable = $datatables[mod.DataTableTypeEnum.PODCAST_EPISODES] = mod.podcastEpisodeTableWidget.getDatatable();
        mod.podcastEpisodeTableWidget.assignDblClickHandler(function () {
            var data = mod.podcastEpisodeDataTable.fnGetData(this);
            if (!$.isEmptyObject(data.file)) {
                mod.dblClickAdd(data.file, data.file.ftype);
            } else {
                if (data.ingested >= 0) {  // Only import if the file isn't pending
                    AIRTIME.podcast.importSelectedEpisodes([data], mod.podcastEpisodeTableWidget);
                }
            }
        });
    };

    mod.libraryInit = libraryInit;

    return AIRTIME;

}(AIRTIME || {}));

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
        fieldName = $(div).children('div').attr('id');
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
                if ($(field).closest('div').prev().hasClass('checked-icon') ||
                    $(field).closest('div').prev().hasClass('not-available-icon')) {
                    $(field).closest('div').prev().remove();
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
        if (!field.closest('div').prev().hasClass('checked-icon')) {
            // remove invalid icon before adding valid icon
            if (field.closest('div').prev().hasClass('not-available-icon')) {
                field.closest('div').prev().remove();
            }
            field.closest('div').before(validIndicator);
        }
    } else {
        if (!field.closest('div').prev().hasClass('not-available-icon')) {
            // remove valid icon before adding invalid icon
            if (field.closest('div').prev().hasClass('checked-icon')) {
                field.closest('div').prev().remove();
            }
            field.closest('div').before(invalidIndicator);
        }
    }
}

function resizeAdvancedSearch() {
    var s = $("#advanced_search");
    s.css("max-height", $(window).height() / 4);
    s.css("overflow", "auto");
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
    "description" : "s",
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
    if (window.location.href.indexOf("showbuilder") > -1) {
        AIRTIME.library.initPodcastDatatable();
    }

    $("#advanced-options").on("click", function() {
        resizeAdvancedSearch();
    });

    $(window).resize(function() {
        resizeAdvancedSearch();
    });
});

