//--------------------------------------------------------------------------------------------------------------------------------
// Playlist Functions
//--------------------------------------------------------------------------------------------------------------------------------

var AIRTIME = (function(AIRTIME){
    
    if (AIRTIME.playlist === undefined) {
        AIRTIME.playlist = {};
    }
    
    var mod = AIRTIME.playlist,
        viewport,
        $lib,
        $pl,
        $togglePl = $("<button id='pl_edit' class='btn btn-small' href='#' title='"+$.i18n._("Open Media Builder")+"'>"+$.i18n._("Open Media Builder")+"</button>"),
        widgetHeight,
        resizeTimeout,
        width;
    
    function isTimeValid(time) {
        //var regExpr = new RegExp("^\\d{2}[:]\\d{2}[:]\\d{2}([.]\\d{1,6})?$");
        var regExpr = new RegExp("^\\d{2}[:]([0-5]){1}([0-9]){1}[:]([0-5]){1}([0-9]){1}([.]\\d{1})?$");
        
        return regExpr.test(time);
    }
    
    function isFadeValid(fade) {
        var regExpr = new RegExp("^\\d{1}(\\d{1})?([.]\\d{1})?$");

        return regExpr.test(fade);
    }
    
    function playlistError(json) {
        alert(json.error);
        openPlaylist(json);
    }
    
    function stopAudioPreview() {
        // stop any preview playing
        $('#jquery_jplayer_1').jPlayer('stop');
    }
    
    function highlightActive(el) {

        $(el).addClass("ui-state-active");
    }

    function unHighlightActive(el) {

        $(el).removeClass("ui-state-active");
    }

    function showError(el, error) {
        $(el).parent().next()
            .empty()
            .append(error)
            .show();
    }

    function hideError(el) {
         $(el).parent().next()
            .empty()
            .hide();
    }

    function changeCueIn(event) {
        event.stopPropagation();
        var span = $(this),
            id = span.parent().attr("id").split("_").pop(),
            url = baseUrl+"Playlist/set-cue",
            cueIn = $.trim(span.text()),
            li = span.parents("li"),
            unqid = li.attr("unqid"),
            lastMod = getModified(),
            type = $('#obj_type').val();
        
        if (!isTimeValid(cueIn)){
            showError(span, $.i18n._("please put in a time '00:00:00 (.0)'"));
            return;
        }
        $.post(url, 
            {format: "json", cueIn: cueIn, id: id, modified: lastMod, type: type}, 
            function(json){

                if (json.error !== undefined){
                    playlistError(json);
                    return;
                }
                if (json.cue_error !== undefined) {
                    showError(span, json.cue_error);
                    return;
                }

                setPlaylistContent(json);

                li = $('#side_playlist li[unqid='+unqid+']');
                li.find(".cue-edit").toggle();
                highlightActive(li);
                highlightActive(li.find('.spl_cue'));
            });
    }

    function changeCueOut(event) {
        event.stopPropagation();
        var span = $(this),
            id = span.parent().attr("id").split("_").pop(),
            url = baseUrl+"Playlist/set-cue",
            cueOut = $.trim(span.text()),
            li = span.parents("li"),
            unqid = li.attr("unqid"),
            lastMod = getModified(),
            type = $('#obj_type').val();

        if (!isTimeValid(cueOut)){
            showError(span, $.i18n._("please put in a time '00:00:00 (.0)'"));
            return;
        }
        
        $.post(url, 
            {format: "json", cueOut: cueOut, id: id, modified: lastMod, type: type}, 
            function(json){

                if (json.error !== undefined){
                    playlistError(json);
                    return;
                }
                if (json.cue_error !== undefined) {
                    showError(span, json.cue_error);
                    return;
                }

                setPlaylistContent(json);

                li = $('#side_playlist li[unqid='+unqid+']');
                li.find(".cue-edit").toggle();
                highlightActive(li);
                highlightActive(li.find('.spl_cue'));
            });
        }
    
    /* used from waveform pop-up */
    function changeCues($el, id, cueIn, cueOut) {
        
        var url = baseUrl+"Playlist/set-cue",
            lastMod = getModified(),
            type = $('#obj_type').val(),
            li,
            span;
        
        if (!isTimeValid(cueIn)){
            $el.find('.cue-in-error').val($.i18n._("please put in a time '00:00:00 (.0)'")).show();
            return;
        }
        else {
            $el.find('.cue-in-error').hide();
        }
        
        if (!isTimeValid(cueOut)){
            $el.find('.cue-out-error').val($.i18n._("please put in a time '00:00:00 (.0)'")).show();
            return;
        }
        else {
            $el.find('.cue-out-error').hide();
        }
        
        $.post(url, 
            {format: "json", cueIn: cueIn, cueOut: cueOut, id: id, modified: lastMod, type: type}, 
            function(json){
                
                $el.dialog('destroy');
                $el.remove();

                if (json.error !== undefined){
                    playlistError(json);
                    return;
                }
                if (json.cue_error !== undefined) {
                    
                    li = $('#side_playlist li[unqid='+id+']');
                    
                    if (json.code === 0) {
                        
                        span = $('#spl_cue_in_'+id).find('span');
                        showError(span, json.cue_error);
                        span = $('#spl_cue_out_'+id).find('span');
                        showError(span, json.cue_error);
                    }
                    else if (json.code === 1) {
                        
                        span = $('#spl_cue_in_'+id).find('span');
                        showError(span, json.cue_error);
                    }
                    else if (json.code === 2) {
                        
                        span = $('#spl_cue_out_'+id).find('span');
                        showError(span, json.cue_error);
                    }
                    
                    return;
                }

                setPlaylistContent(json);

                li = $('#side_playlist li[unqid='+id+']');
                li.find(".cue-edit").toggle();
                highlightActive(li);
                highlightActive(li.find('.spl_cue'));
            });
    }
    
    /* used from waveform pop-up */
    function changeCrossfade($el, id1, id2, fadeIn, fadeOut, offset, id) {
        
        var url = baseUrl+"Playlist/set-crossfade",
            lastMod = getModified(),
            type = $('#obj_type').val();
        
        $.post(url, 
            {format: "json", fadeIn: fadeIn, fadeOut: fadeOut, id1: id1, id2: id2, offset: offset, modified: lastMod, type: type}, 
            function(json){
                
                $el.dialog('destroy');
                $el.remove();

                if (json.error !== undefined){
                    playlistError(json);
                    return;
                }
                
                setPlaylistContent(json);

                $li = $('#side_playlist li[unqid='+id+']');
                $li.find('.crossfade').toggle();
                highlightActive($li.find('.spl_fade_control'));
            });
    }

    function changeFadeIn(event) {
        event.preventDefault();

        var span = $(this),
            id = span.parent().attr("id").split("_").pop(),
            url = baseUrl+"Playlist/set-fade",
            fadeIn = $.trim(span.text()),
            li = span.parents("li"),
            unqid = li.attr("unqid"),
            lastMod = getModified(),
            type = $('#obj_type').val();

        if (!isFadeValid(fadeIn)){
            showError(span, $.i18n._("please put in a time in seconds '00 (.0)'"));
            return;
        }

        $.post(url, 
            {format: "json", fadeIn: fadeIn, id: id, modified: lastMod, type: type}, 
            function(json){

                if (json.error !== undefined){
                    playlistError(json);
                    return;
                }
                if (json.fade_error !== undefined) {
                    showError(span, json.fade_error);
                    return;
                }

                setPlaylistContent(json);

                li = $('#side_playlist li[unqid='+unqid+']');
                li.find('.crossfade').toggle();
                highlightActive(li.find('.spl_fade_control'));
            });
    }

    function changeFadeOut(event) {
        event.stopPropagation();

        var span = $(this),
            id = span.parent().attr("id").split("_").pop(),
            url = baseUrl+"Playlist/set-fade",
            fadeOut = $.trim(span.text()),
            li = span.parents("li"),
            unqid = li.attr("unqid"),
            lastMod = getModified(),
            type = $('#obj_type').val();

        if (!isFadeValid(fadeOut)){
            showError(span, $.i18n._("please put in a time in seconds '00 (.0)'"));
            return;
        }

        $.post(url, 
            {format: "json", fadeOut: fadeOut, id: id, modified: lastMod, type: type}, 
            function(json){
            
                if (json.error !== undefined){
                    playlistError(json);
                    return;
                }
                if (json.fade_error !== undefined) {
                    showError(span, json.fade_error);
                    return;
                }
    
                setPlaylistContent(json);
                
                li = $('#side_playlist li[unqid='+unqid+']');
                li.find('.crossfade').toggle();
                highlightActive(li.find('.spl_fade_control'));
            });
    }

    function submitOnEnter(event) {
        //enter was pressed
        if(event.keyCode === 13) {
            event.preventDefault();
            $(this).blur();
        }
    }

    function openFadeEditor(event) {
        var li;
        
        event.stopPropagation();  

        li = $(this).parents("li");
        li.find(".crossfade").toggle();

        if ($(this).hasClass("ui-state-active")) {
            unHighlightActive(this);
        }
        else {
            highlightActive(this);
        }
    }

    function openCueEditor(event) {
        var li, icon;
        
        event.stopPropagation();

        icon = $(this);
        li = $(this).parents("li"); 
        li.find(".cue-edit").toggle();

        if (li.hasClass("ui-state-active")) {
            unHighlightActive(li);
            unHighlightActive(icon);
        }
        else {
            highlightActive(li);
            highlightActive(icon);
        }
    }
    
    function editName() {
        var nameElement = $(this),
            lastMod = getModified(),
            type = $('#obj_type').val();
            //remove any newlines if user somehow snuck them in (easy to do if dragging/dropping text)
            nameElement.text(nameElement.text().replace("\n", ""));
       
        /* --until we decide whether Playlist name should autosave or not

        url = baseUrl+'Playlist/set-playlist-name';

        $.post(url, 
            {format: "json", name: nameElement.text(), modified: lastMod, type: type}, 
            function(json){
            
                if (json.error !== undefined) {
                    playlistError(json);
                }
                else {
                    setModified(json.modified);
                    nameElement.text(json.playlistName);
                    redrawLib();
                }
            });
           */
    }
        
    function redrawLib() {
        var dt = $lib.find("#library_display").dataTable();
        
        dt.fnStandingRedraw();
        AIRTIME.library.redrawChosen();
    }
    
    function setPlaylistContent(json) {
        var $html = $(json.html);
        
        $('#spl_name > a')
            .empty()
            .append(json.name);
        $('#obj_length')
            .empty()
            .append(json.length);
        $('#fieldset-metadate_change textarea')
            .empty()
            .val(json.description);
        
        $('#spl_sortable').off('focusout keydown');
        $('#spl_sortable')
        .empty()
        .append($html);
        setCueEvents();
        setFadeEvents();
        setModified(json.modified);
        AIRTIME.playlist.validatePlaylistElements();
        redrawLib();
    }
    
    function setFadeIcon(){
        var contents = $("#spl_sortable");
        var show = contents.is(":visible");
        var empty = $(".spl_empty");
        
        if (!show || empty.length > 0) {
            $("#spl_crossfade").hide();
        } else {
            //get list of playlist contents
            var list = contents.children();
            
            //if first and last items are blocks, hide the fade icon
            var first = list.first();
            var last = list.last();
            if (first.find(':first-child').children().attr('blockid') !== undefined &&
                last.find(':first-child').children().attr('blockid') !== undefined) {
                $("#spl_crossfade").hide();
            } else {
                $("#spl_crossfade").show();
            }
        }
    }
    
    function getId() {
        return parseInt($("#obj_id").val(), 10);
    }
    
    function getModified() {
        return parseInt($("#obj_lastMod").val(), 10);
    }
    
    function setModified(modified) {
        $("#obj_lastMod").val(modified);
    }
    
    function openPlaylist(json) {
        $("#side_playlist")
            .empty()
            .append(json.html);
                
        setUpPlaylist();
        setCueEvents();
        setFadeEvents();
        
        // functions in smart_blockbuilder.js
        setupUI();
        appendAddButton();
        appendModAddButton();
        removeButtonCheck();
    }
    
    function openPlaylistPanel() {
        var screenWidth = Math.floor(viewport.width - 40);
        viewport = AIRTIME.utilities.findViewportDimensions();
        widgetHeight = viewport.height - 185;

        $lib.width(Math.floor(screenWidth * 0.53));
        $pl.show().width(Math.floor(screenWidth * 0.44));
        $pl.height(widgetHeight);
        $("#pl_edit").hide();
    }

    //Purpose of this function is to iterate over all playlist elements
    //and verify whether they can be previewed by the browser or not. If not
    //then the playlist element is greyed out
    mod.validatePlaylistElements = function(){
        $.each($("div .big_play"), function(index, value){
            if ($(value).attr('blockId') === undefined) {
                var mime = $(value).attr("data-mime-type");
                //If mime is undefined it is likely because the file was
                //deleted from the library. This case is handled in mod.onReady()
                if (mime !== undefined) {
                    if (isAudioSupported(mime)) {
                        $(value).bind("click", openAudioPreview);
                    } else {
                        $(value).attr("class", "big_play_disabled dark_class"); 
                        $(value).qtip({
                           content: $.i18n._("Your browser does not support playing this file type: ")+ mime,
                           show: 'mouseover',
                            hide: {
                                delay: 500,
                                fixed: true
                            },
                            style: {
                                border: {
                                    width: 0,
                                    radius: 4
                                },
                                classes: "ui-tooltip-dark ui-tooltip-rounded"
                            },
                            position: {
                                my: "left bottom",
                                at: "right center"
                            },
                        }) 
                    }
                }
            } else {
                if ($(value).attr('blocktype') === 'dynamic') {
                    $(value).attr("class", "big_play_disabled dark_class"); 
                    $(value).qtip({
                       content: $.i18n._('Dynamic block is not previewable'),
                       show: 'mouseover',
                        hide: {
                            delay: 500,
                            fixed: true
                        },
                        style: {
                            border: {
                                width: 0,
                                radius: 4
                            },
                            classes: "ui-tooltip-dark ui-tooltip-rounded"
                        },
                        position: {
                            my: "left bottom",
                            at: "right center"
                        },
                    })
                } else {
                    $(value).bind("click", openAudioPreview);
                }
            }
        });
    }
    
    //sets events dynamically for playlist entries (each row in the playlist)
    function setPlaylistEntryEvents() {
        
        $pl.delegate("#spl_sortable .ui-icon-closethick", 
                {"click": function(ev){
                    var id;
                    id = parseInt($(this).attr("id").split("_").pop(), 10);
                    AIRTIME.playlist.fnDeleteItems([id]);
                }});

        $pl.delegate(".spl_fade_control", 
                {"click": openFadeEditor});
        
        $pl.delegate(".spl_cue", 
                {"click": openCueEditor});
            
        $pl.delegate(".spl_block_expand",
                {"click": function(ev){
                    var id = parseInt($(this).attr("id").split("_").pop(), 10);
                    var blockId = parseInt($(this).attr("blockId"), 10);
                    if ($(this).hasClass('close')) {
                        var sUrl = baseUrl+"playlist/get-block-info";
                        mod.disableUI();
                        $.post(sUrl, {format:"json", id:blockId}, function(data){
                            $html = "";
                            var isStatic = data.isStatic;
                            delete data.type;
                            if (isStatic) {
                                $.each(data, function(index, ele){
                                    if (ele.track_title !== undefined) {
                                        if (ele.creator === null) {
                                            ele.creator = "";
                                        }
                                        if (ele.track_title === null) {
                                            ele.track_title = "";
                                        }
                                        $html += "<li>" +
                                            "<span class='block-item-title'>"+ele.track_title+" - </span>" +
                                            "<span class='block-item-author'>"+ele.creator+"</span>" +
                                            "<span class='block-item-time'>"+ele.length+"</span>" + 
                                            "</li>";
                                    }
                                })
                            } else {
                                for (var key in data.crit){
                                    $.each(data.crit[key], function(index, ele){
                                        var extra = (ele['extra']==null)?"":"- "+ele['extra'];
                                        $html += "<li>" +
                                            "<span class='block-item-title'>"+ele['display_name']+"</span>" +
                                            "<span class='block-item-criteria'>"+ele['display_modifier']+"</span>" +
                                            "<span class='block-item-criteria'>"+ele['value']+"</span>" +
                                            "<span class='block-item-criteria'>"+extra+"</span>" + 
                                            "</li>";
                                    });
                                }
                                $html += "<li><br /><span class='block-item-title'>"+$.i18n._("Limit to: ")+data.limit.value+"  "+data.limit.display_modifier+"</span></li>";
                            }
                            $pl.find("#block_"+id+"_info").html($html).show();
                            mod.enableUI();
                        });
                        $(this).removeClass('close');
                    } else {
                        $pl.find("#block_"+id+"_info").html("").hide();
                        $(this).addClass('close');
                    }
                }});
    }
    
    //sets events dynamically for the cue editor.
    function setCueEvents() {
        var temp = $('#spl_sortable');
        temp.on("focusout", ".spl_cue_in span", changeCueIn);
        temp.on("keydown", ".spl_cue_in span", submitOnEnter);
        
        temp.on("focusout", ".spl_cue_out span", changeCueOut);
        temp.on("keydown", ".spl_cue_out span", submitOnEnter);
        
        //remove show waveform buttons since web audio api is not supported.
        if (!(window.AudioContext || window.webkitAudioContext)) {
            temp.find('.pl-waveform-cues-btn')
                .parent()
                .html($.i18n._("Waveform features are available in a browser supporting the Web Audio API"));
        }
    }
    
    //sets events dynamically for the fade editor.
    function setFadeEvents() {
        var temp = $('#spl_sortable');
        temp.on("focusout", ".spl_fade_in span", changeFadeIn);
        temp.on("keydown", ".spl_fade_in span", submitOnEnter);
        
        temp.on("focusout", ".spl_fade_out span", changeFadeOut);
        temp.on("keydown", ".spl_fade_out span", submitOnEnter);
        
      //remove show waveform buttons since web audio api is not supported.
        if (!(window.AudioContext || window.webkitAudioContext)) {
            temp.find('.pl-waveform-fades-btn')
                .parent()
                .html($.i18n._("Waveform features are available in a browser supporting the Web Audio API"));
        }
    }
    
    function initialEvents() {    
        var cachedDescription;
        
        //main playlist fades events
        $pl.on("click", "#spl_crossfade", function() {
            var lastMod = getModified(),
                type = $('#obj_type').val();

            if ($(this).hasClass("ui-state-active")) {
                $(this).removeClass("ui-state-active");
                $pl.find("#crossfade_main").hide();
            }
            else {
                $(this).addClass("ui-state-active");

                var url = baseUrl+'Playlist/get-playlist-fades';
                $.post(url, 
                    {format: "json", modified: lastMod, type: type},
                    function(json){
                        if (json.error !== undefined){
                            playlistError(json);
                        }
                        else {
                            var fadeIn = $pl.find("span.spl_main_fade_in");
                            var fadeOut = $pl.find("span.spl_main_fade_out");
                            if (json.fadeIn == null) {
                                fadeIn.parent().prev().hide();
                                fadeIn.hide();
                            } else {
                                fadeIn.parent().prev().show();
                                fadeIn.show();
                                fadeIn.empty().append(json.fadeIn);
                            }
                            if (json.fadeOut == null) {
                                fadeOut.parent().prev().hide();
                                fadeOut.hide();
                            } else {
                                fadeOut.parent().prev().show();
                                fadeOut.show();
                                fadeOut.empty().append(json.fadeOut);
                            }
                            if (json.fadeIn != null || json.fadeOut != null) {
                                $pl.find("#crossfade_main").show();
                            }
                        }
                    });
            }
        });
        
        $pl.on("blur", "span.spl_main_fade_in", function(event){
            event.stopPropagation();

            var url = baseUrl+"Playlist/set-playlist-fades",
                span = $(this),
                fadeIn = $.trim(span.text()), 
                lastMod = getModified(),
                type = $('#obj_type').val();
            
            if (!isFadeValid(fadeIn)){
                showError(span, $.i18n._("please put in a time in seconds '00 (.0)'"));
                return;
            }

            $.post(url, 
                {format: "json", fadeIn: fadeIn, modified: lastMod, type: type}, 
                function(json){       
                    hideError(span);
                    if (json.modified !== undefined) {
                        setModified(json.modified);
                    }
                });
        });

        $pl.on("blur", "span.spl_main_fade_out", function(event){
            event.stopPropagation();

            var url = baseUrl+"Playlist/set-playlist-fades",
                span = $(this),
                fadeOut = $.trim(span.text()), 
                lastMod = getModified(),
                type = $('#obj_type').val();

            if (!isFadeValid(fadeOut)){
                showError(span, $.i18n._("please put in a time in seconds '00 (.0)'"));
                return;
            }

            $.post(url, 
                {format: "json", fadeOut: fadeOut, modified: lastMod, type: type}, 
                function(json){
                    hideError(span);
                    if (json.modified !== undefined) {
                        setModified(json.modified);
                    }
                });
        });

        $pl.on("keydown", "span.spl_main_fade_in, span.spl_main_fade_out", submitOnEnter);

        $pl.on("click", "#crossfade_main > .ui-icon-closethick", function(){
            $pl.find("#spl_crossfade").removeClass("ui-state-active");
            $pl.find("#crossfade_main").hide();
        });
        //end main playlist fades.

        //edit playlist name event
        $pl.on("keydown", "#playlist_name_display", submitOnEnter);
        $pl.on("blur", "#playlist_name_display", editName);
        
        //edit playlist description events
        $pl.on("click", "legend", function(){
            var $fs = $(this).parents("fieldset");

            if ($fs.hasClass("closed")) {
                cachedDescription = $fs.find("textarea").val();
                $fs.removeClass("closed");
            }
            else {
                $fs.addClass("closed");
            }
        });
        

        $pl.on("click", 'button[id="playlist_shuffle_button"]', function(){
            obj_id = $('input[id="obj_id"]').val();
            url = baseUrl+"Playlist/shuffle";
            enableLoadingIcon();
            $.post(url, {format: "json", obj_id: obj_id}, function(json){

                if (json.error !== undefined) {
                    alert(json.error);
                }
                AIRTIME.playlist.fnOpenPlaylist(json);
                if (json.result == "0") {
                    $pl.find('.success').text($.i18n._('Playlist shuffled'));
                    $pl.find('.success').show();
                }
                disableLoadingIcon();
                setTimeout(removeSuccessMsg, 5000);
            });
        })

        $pl.on("click", "#webstream_save", function(){
            //get all fields and POST to server
            //description
            //stream url
            //default_length  
            //playlist name
            var id = $pl.find("#obj_id").attr("value"); 
            var description = $pl.find("#description").val();
            var streamurl = $pl.find("#streamurl-element input").val();
            var length = $pl.find("#streamlength-element input").val();
            var name = $pl.find("#playlist_name_display").text(); 
            
            //hide any previous errors (if any)
            $("#side_playlist .errors").empty().hide();
        
            var url = baseUrl+'Webstream/save';
            $.post(url, 
                {format: "json", id:id, description: description, url:streamurl, length: length, name: name}, 
                function(json){
                    if (json.analysis){
                        for (var s in json.analysis){
                            var field = json.analysis[s];
                            
                            if (!field[0]) {
                                var elemId = "#"+s+"-error";
                                var $div = $("#side_playlist " + elemId).text(field[1]).show();
                            }
                        }
                    } else {
                        var $status = $("#side_playlist .status");
                        $status.html(json.statusMessage);
                        $status.show();
                        setTimeout(function(){$status.fadeOut("slow", function(){$status.empty()})}, 5000);

                        var $ws_id = $("#obj_id");
                        $ws_id.attr("value", json.streamId);

                        var $ws_id = $("#ws_delete");
                        $ws_id.show();


                        var length = $("#side_playlist #ws_length");
                        length.text(json.length);

                        //redraw the library to show the new webstream
                        redrawLib();
                    }
                    
                });    
        
        
        });

        $lib.on("click", "#pl_edit", function() {
            openPlaylistPanel();
            $.ajax( {
                url : baseUrl+"usersettings/set-library-screen-settings",
                type : "POST",
                data : {
                    settings : {
                        playlist : true
                    },
                    format : "json"
                },
                dataType : "json"
            });
        });

        $pl.on("click", "#lib_pl_close", function() {
            var screenWidth = Math.floor(viewport.width - 40);
            $pl.hide();
            $lib.width(screenWidth).find("#library_display_length").append($togglePl.show());

            $.ajax( {
                url : baseUrl+"usersettings/set-library-screen-settings",
                type : "POST",
                data : {
                    settings : {
                        playlist : false
                    },
                    format : "json"
                },
                dataType : "json"
            });
        });

        $('#save_button').live("click", function(event){
            /* Smart blocks: get name, description, and criteria
             * Playlists: get name, description
             */
            var criteria = $('form').serializeArray(),
                block_name = $('#playlist_name_display').text(),
                block_desc = $('textarea[name="description"]').val(),
                save_action = baseUrl+'Playlist/save',
                obj_id = $('input[id="obj_id"]').val(),
                obj_type = $('#obj_type').val(),
                lastMod = getModified(),
                dt = $('table[id="library_display"]').dataTable();
            enableLoadingIcon();
            $.post(save_action,
                    {format: "json", data: criteria, name: block_name, description: block_desc, obj_id: obj_id, type: obj_type, modified: lastMod},
                    function(json){
                        if (json.error !== undefined) {
                            alert(json.error);
                        }
                        if (json.html !== undefined) {
                            AIRTIME.playlist.fnOpenPlaylist(json);
                        }
                        setModified(json.modified);
                        if (obj_type == "block") {
                            callback(json, "save");
                        } else {
                            $('.success').text($.i18n._('Playlist saved'));
                            $('.success').show();
                            setTimeout(removeSuccessMsg, 5000);
                            dt.fnStandingRedraw();
                        }
                        setFadeIcon();
                        disableLoadingIcon();
                    }
            );
        });

        $("#pl-bl-clear-content").live("click", function(event) {
            var sUrl = baseUrl+"playlist/empty-content",
                oData = {};
            playlistRequest(sUrl, oData);
        });
    }
    
    function setUpPlaylist() {
        var sortableConf;
        
        sortableConf = (function(){
            var aReceiveItems,
                html,
                fnReceive,
                fnUpdate;        
            
            fnReceive = function(event, ui) {
                var aItems = [],
                    aSelected,
                    i,
                    length;
                
                AIRTIME.library.addToChosen(ui.item);
                
                //filter out anything that isn't an audiofile.
                aSelected = AIRTIME.library.getSelectedData();
                
                for (i = 0, length = aSelected.length; i < length; i++) {
                    aItems.push(new Array(aSelected[i].id, aSelected[i].ftype));
                }
    
                aReceiveItems = aItems;
                html = ui.helper.html();
                
                AIRTIME.library.removeFromChosen(ui.item);
            };
            
            fnUpdate = function(event, ui) {
                var prev,
                    aItems = [],
                    iAfter,
                    sAddType;
                
                prev = ui.item.prev();
                if (prev.hasClass("spl_empty") || prev.length === 0) {
                    iAfter = undefined;
                    sAddType = 'before';
                }
                else {
                    iAfter = parseInt(prev.attr("id").split("_").pop(), 10);
                    sAddType = 'after';
                }
                
                //item was dragged in from library datatable
                if (aReceiveItems !== undefined) {
                    
                    $pl.find("tr.ui-draggable")
                        .after(html)
                        .empty();
                    
                    aItems = aReceiveItems;
                    aReceiveItems = undefined;
                    
                    AIRTIME.playlist.fnAddItems(aItems, iAfter, sAddType);
                }
                //item was reordered.
                else {
                    aItems.push(parseInt(ui.item.attr("id").split("_").pop(), 10));
                    AIRTIME.playlist.fnMoveItems(aItems, iAfter);
                }
            };
            
            return {
                items: 'li',
                //hack taken from
                //http://stackoverflow.com/questions/2150002/jquery-ui-sortable-how-can-i-change-the-appearance-of-the-placeholder-object
                placeholder: {
                    element: function(currentItem) {
                        
                        return $('<li class="placeholder ui-state-highlight"></li>')[0];
                    },
                    update: function(container, p) {
                        return;
                    }
                },
                forcePlaceholderSize: true,
                handle: 'div.list-item-container',
                start: function(event, ui) {
                    ui.placeholder.height(56);
                },
                receive: fnReceive,
                update: fnUpdate
            };
        }());

        $pl.find("#spl_sortable").sortable(sortableConf);
        AIRTIME.playlist.validatePlaylistElements();
    }
    
    mod.fnNew = function() {
        var url = baseUrl+'Playlist/new';

        stopAudioPreview();
        
        $.post(url, 
            {format: "json", type: 'playlist'}, 
            function(json){
                openPlaylist(json);
                redrawLib();
            });
    };
    
    mod.fnWsNew = function() {
        var url = baseUrl+'Webstream/new';

        stopAudioPreview();
        
        $.post(url, 
            {format: "json"}, 
            function(json){
                openPlaylist(json);
                redrawLib();
            });
    };
    

    mod.fnNewBlock = function() {
        var url = baseUrl+'Playlist/new';

        stopAudioPreview();
        
        $.post(url, 
            {format: "json", type: 'block'}, 
            function(json){
                openPlaylist(json);
                redrawLib();
            });
    };
    
    mod.fnEdit = function(id, type, url) {
        if ($pl.is(":hidden")) {
            openPlaylistPanel();
        }
        stopAudioPreview();    
        
        $.post(url, 
            {format: "json", id: id, type: type}, 
            function(json){
                openPlaylist(json);
            });
    };

    
    mod.fnDelete = function(plid) {
        var url, id, lastMod;
        
        stopAudioPreview();    
        id = (plid === undefined) ? getId() : plid;
        lastMod = getModified();
        type = $('#obj_type').val();
        url = baseUrl+'Playlist/delete';

        $.post(url, 
            {format: "json", ids: id, modified: lastMod, type: type}, 
            function(json){
                openPlaylist(json);
                redrawLib();
            });
    };
    
    mod.fnWsDelete = function(wsid) {
        var url, id, lastMod;
        
        stopAudioPreview();    
        id = (wsid === undefined) ? getId() : wsid;
        lastMod = getModified();
        type = $('#obj_type').val();
        url = baseUrl+'Webstream/delete';
        
        $.post(url, 
            {format: "json", ids: id, modified: lastMod, type: type}, 
            function(json){
                openPlaylist(json);
                redrawLib();
            });
    };
    
    mod.disableUI = function() {
        
        $lib.block({ 
            message: "",
            theme: true,
            applyPlatformOpacityRules: false
        });
        
        $pl.block({ 
            message: "",
            theme: true,
            applyPlatformOpacityRules: false
        });
    };
    
    mod.fnOpenPlaylist = function(json) {
        openPlaylist(json);
    };
    
    mod.enableUI = function() {
        
        $lib.unblock();
        $pl.unblock();
        
        //Block UI changes the postion to relative to display the messages.
        $lib.css("position", "static");
        $pl.css("position", "static");
        setupUI();
    };
    
    function playlistResponse(json){    
        
        if (json.error !== undefined) {
            playlistError(json);
        }
        else {
            setPlaylistContent(json);
            setFadeIcon();
        }
        
        mod.enableUI();
    }
    
    function playlistRequest(sUrl, oData) {
        var lastMod,
            obj_type = $('#obj_type').val();
        
        mod.disableUI();
        
        lastMod = getModified();
        
        oData["modified"] = lastMod;
        oData["obj_type"] = obj_type;
        oData["format"] = "json";
        
        $.post(
            sUrl, 
            oData, 
            playlistResponse
        );
    }
    
    mod.fnAddItems = function(aItems, iAfter, sAddType) {
        var sUrl = baseUrl+"playlist/add-items";
            oData = {"aItems": aItems, "afterItem": iAfter, "type": sAddType};
        playlistRequest(sUrl, oData);
    };
    
    mod.fnMoveItems = function(aIds, iAfter) {
        var sUrl = baseUrl+"playlist/move-items",
            oData = {"ids": aIds, "afterItem": iAfter};
        
        playlistRequest(sUrl, oData);
    };
    
    mod.fnDeleteItems = function(aItems) {
        var sUrl = baseUrl+"playlist/delete-items",
            oData = {"ids": aItems};
        
        playlistRequest(sUrl, oData);
    };
    
    mod.showFadesWaveform = function(e) {
        var $el = $(e.target),
            $parent = $el.parents("dl"),
            $li = $el.parents("li"),
            $fadeOut = $parent.find(".spl_fade_out"),
            $fadeIn = $parent.find(".spl_fade_in"),
            $html = $($("#tmpl-pl-fades").html()),
            tracks = [],
            dim = AIRTIME.utilities.findViewportDimensions(),
            playlistEditor,
            id1, id2,
            id = $li.attr("unqid");
        
        
        function removeDialog() {
            playlistEditor.stop();
            
            $html.dialog("destroy");
            $html.remove();
        }
        
        if ($fadeOut.length > 0) {
            
            tracks.push({
                src: $fadeOut.data("fadeout"),
                cuein: $fadeOut.data("cuein"),
                cueout: $fadeOut.data("cueout"),
                fades: [{
                    shape: $fadeOut.data("type"),
                    type: "FadeOut",
                    end: $fadeOut.data("cueout") - $fadeOut.data("cuein"),
                    start: $fadeOut.data("cueout") - $fadeOut.data("cuein") - $fadeOut.data("length")
                }],
                states: {
                    'fadein': false,
                    'shift': false
                }
            });
            
            id1 = $fadeOut.data("item");
        }

        if ($fadeIn.length > 0) {
            
            tracks.push({
                src: $fadeIn.data("fadein"),
                start: $fadeIn.data("offset"),
                cuein: $fadeIn.data("cuein"),
                cueout: $fadeIn.data("cueout"),
                fades: [{
                    shape: $fadeIn.data("type"),
                    type: "FadeIn",
                    end: $fadeIn.data("length"),
                    start: 0
                }],
                states: {
                    'fadeout': false,
                    'shift': false
                }
            });
            
            id2 = $fadeIn.data("item");
        }
        
        //set the first track to not be moveable (might only be one track depending on what follows)
        //tracks[0].states["shift"] = false;
        
        $html.dialog({
            modal: true,
            title: $.i18n._("Fade Editor"),
            show: 'clip',
            hide: 'clip',
            width: dim.width - 100,
            height: 350,
            buttons: [
                {text: $.i18n._("Cancel"), class: "btn btn-small", click: removeDialog},
                {text: $.i18n._("Save"), class: "btn btn-small btn-inverse", click: function() {
                    var json = playlistEditor.getJson(),
                        offset, 
                        fadeIn, fadeOut,
                        fade;
                    
                    playlistEditor.stop();
                    
                    if (json.length === 0)
                    {
                        id1 = undefined;
                        id2 = undefined;
                    }
                    else if (json.length === 1) {
                        
                        fade = json[0]["fades"][0];
                        
                        if (fade["type"] === "FadeOut") {
                            fadeOut = fade["end"] - fade["start"];
                            id2 = undefined; //incase of track decode error.
                        }
                        else {
                            fadeIn = fade["end"] - fade["start"];
                            id1 = undefined; //incase of track decode error.
                        }
                    }
                    else {
                        
                        offset = json[0]["end"] - json[1]["start"];
                        
                        fade = json[0]["fades"][0];
                        fadeOut = fade["end"] - fade["start"];
                        
                        fade = json[1]["fades"][0];
                        fadeIn = fade["end"] - fade["start"];
                    }
                    
                    fadeIn = (fadeIn === undefined) ? undefined : fadeIn.toFixed(1);
                    fadeOut = (fadeOut === undefined) ? undefined : fadeOut.toFixed(1);
                    
                    changeCrossfade($html, id1, id2, fadeIn, fadeOut, offset, id);
                }}
            ],
            open: function (event, ui) {
                
                var config = new Config({
                    resolution: 15000,
                    state: "cursor",
                    mono: true,
                    timescale: true,
                    waveHeight: 80,
                    container: $html[0],
                    UITheme: "jQueryUI",
                    timeFormat: 'hh:mm:ss.u'
                });
                
                playlistEditor = new PlaylistEditor();
                playlistEditor.setConfig(config);
                playlistEditor.init(tracks);
            },
            close: removeDialog,
            resizeStop: function(event, ui) {
                playlistEditor.resize();
            }
        });        
    };
    
    mod.showCuesWaveform = function(e) {
        var $el = $(e.target),
            $li = $el.parents("li"), 
            id = $li.attr("unqid"),
            $parent = $el.parent(),
            uri = $parent.data("uri"),
            $html = $($("#tmpl-pl-cues").html()),
            cueIn = $li.find('.spl_cue_in').data("cueIn"),
            cueOut = $li.find('.spl_cue_out').data("cueOut"),
            cueInSec = $li.find('.spl_cue_in').data("cueSec"),
            cueOutSec = $li.find('.spl_cue_out').data("cueSec"),
            tracks = [{
                src: uri,
                selected: {
                    start: cueInSec,
                    end: cueOutSec
                }
            }],
            dim = AIRTIME.utilities.findViewportDimensions(),
            playlistEditor;
        
        function removeDialog() {
            playlistEditor.stop();
            
            $html.dialog("destroy");
            $html.remove();
        }
        
        $html.find('.editor-cue-in').html(cueIn);
        $html.find('.editor-cue-out').html(cueOut);
        
        $html.on("click", ".set-cue-in", function(e) {
            var cueIn = $html.find('.audio_start').val();
            
            $html.find('.editor-cue-in').html(cueIn);
        });
        
        $html.on("click", ".set-cue-out", function(e) {
            var cueOut = $html.find('.audio_end').val();
            
            $html.find('.editor-cue-out').html(cueOut);
        });
        
        $html.dialog({
            modal: true,
            title: $.i18n._("Cue Editor"),
            show: 'clip',
            hide: 'clip',
            width: dim.width - 100,
            height: 325,
            buttons: [
                {text: $.i18n._("Cancel"), class: "btn btn-small", click: removeDialog},
                {text: $.i18n._("Save"),  class: "btn btn-small btn-inverse", click: function() {
                    var cueIn = $html.find('.editor-cue-in').html(),
                        cueOut = $html.find('.editor-cue-out').html();
                    
                    playlistEditor.stop();
                    
                    changeCues($html, id, cueIn, cueOut);
                }}
            ],
            open: function (event, ui) {
                
                var config = new Config({
                    resolution: 15000,
                    mono: true,
                    timescale: true,
                    waveHeight: 80,
                    container: $html[0],
                    UITheme: "jQueryUI",
                    timeFormat: 'hh:mm:ss.u'
                });
                
                playlistEditor = new PlaylistEditor();
                playlistEditor.setConfig(config);
                playlistEditor.init(tracks);    
            },
            close: removeDialog,
            resizeStop: function(event, ui) {
                playlistEditor.resize();
            }
        });    
    };
    
    mod.init = function() {
        /*
        $.contextMenu({
            selector: '#spl_new, #ws_new',
            trigger: "left",
            ignoreRightClick: true,
            items: {
                "sp": {name: "New Playlist", callback: AIRTIME.playlist.fnNew},
                "sb": {name: "New Smart Block", callback: AIRTIME.playlist.fnNewBlock},
                "ws": {name: "New Webstream", callback: AIRTIME.playlist.fnWsNew}
            }
        });
        */
        $('#lib-new-pl').live('click', function(){AIRTIME.playlist.fnNew();});
        $('#lib-new-bl').live('click', function(){AIRTIME.playlist.fnNewBlock();});
        $('#lib-new-ws').live('click', function(){AIRTIME.playlist.fnWsNew();});
        /*
        $pl.delegate("#spl_new", 
                {"click": AIRTIME.playlist.fnNew});*/

        $pl.delegate("#spl_delete", {"click": function(ev){
            AIRTIME.playlist.fnDelete();
        }});
        
        $pl.delegate("#ws_delete", {"click": function(ev){
            AIRTIME.playlist.fnWsDelete();
        }});
        
        $pl.delegate(".pl-waveform-cues-btn", {"click": function(ev){
            AIRTIME.playlist.showCuesWaveform(ev);
        }});
        
        $pl.delegate(".pl-waveform-fades-btn", {"click": function(ev){
            AIRTIME.playlist.showFadesWaveform(ev);
        }});
        
        setPlaylistEntryEvents();
        setCueEvents();
        setFadeEvents();
        setFadeIcon();
        
        initialEvents();
        setUpPlaylist();
    };
    
    function setWidgetSize() {
        viewport = AIRTIME.utilities.findViewportDimensions();
        widgetHeight = viewport.height - 185;
        width = Math.floor(viewport.width - 80);

        var libTableHeight = widgetHeight - 175;

        if (!$pl.is(':hidden')) {
            $lib.height(widgetHeight)
                .find(".dataTables_scrolling")
                    .css("max-height", libTableHeight)
                    .end()
                .width(Math.floor(width * 0.55));

            $pl.height(widgetHeight)
                .width(Math.floor(width * 0.45));
        } else {
            $lib.height(widgetHeight)
                .find(".dataTables_scrolling")
                    .css("max-height", libTableHeight)
                    .end()
                .width(width + 40);
        }
    }
    
    mod.onReady = function() {
        $lib = $("#library_content");
        $pl = $("#side_playlist");

        setWidgetSize();
        
        AIRTIME.library.libraryInit();
        AIRTIME.playlist.init();

        if ($pl.is(':hidden')) {
            $lib.find("#library_display_length").append($togglePl.show());
        }

        $pl.find(".ui-icon-alert").qtip({
            content: {
                text: sprintf($.i18n._("%s is unsure about the status of this file. This can happen when the file is on a remote drive that is unaccessible or the file is in a directory that isn't 'watched' anymore."), PRODUCT_NAME)
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
            style: {
                classes: "ui-tooltip-dark"
            },
            show: 'mouseover',
            hide: 'mouseout'
        });
    };
    
    mod.onResize = function() {
        
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(setWidgetSize, 100);
    };
    
    return AIRTIME;
    
}(AIRTIME || {}));


$(document).ready(AIRTIME.playlist.onReady);
$(window).resize(AIRTIME.playlist.onResize);
