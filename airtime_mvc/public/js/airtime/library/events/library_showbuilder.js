var AIRTIME = (function(AIRTIME) {
    var mod;

    if (AIRTIME.library === undefined) {
        AIRTIME.library = {};
    }

    mod = AIRTIME.library;

    mod.checkAddButton = function() {
        var selected = mod.getChosenItemsLength(), $cursor = $('tr.sb-selected'), check = false,
            shows = $('tr.sb-header'), current = $('tr.sb-current-show'),
        // TODO: this is an ugly way of doing this... we should find a more robust way of checking which view we're in.
            cursorText = (window.location.href.toLowerCase().indexOf("schedule") > -1) ? $.i18n._('Add to show') : $.i18n._('Add to next show');

        // make sure library items are selected and a cursor is selected.
        if (selected !== 0) {
            check = true;
        }

        if (shows.length === 0) {
            check = false;
        }

        if (check === true) {
            AIRTIME.button.enableButton("btn-group #library-plus", false);
        } else {
            AIRTIME.button.disableButton("btn-group #library-plus", false);
        }

        if ($cursor.length !== 0) {
            cursorText = $.i18n._('Add before selected items');
        } else if (current.length !== 0) {
            cursorText = $.i18n._('Add to current show');
        }
        AIRTIME.library.changeAddButtonText($('.btn-group #library-plus #lib-plus-text'), ' '+ cursorText);
    };

    mod.fnRowCallback = function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        var $nRow = $(nRow);

        if (aData.ftype === "audioclip") {
            $nRow.addClass("lib-audio");
            $image = $nRow.find('td.library_type');
            if (!isAudioSupported(aData.mime)) {
                $image.html('<span class="ui-icon ui-icon-locked"></span>');
                aData.image = '<span class="ui-icon ui-icon-locked"></span>';
            }
        } else if (aData.ftype === "stream") {
            $nRow.addClass("lib-stream");
        } else {
            $nRow.addClass("lib-pl");
        }

        $nRow.attr("id", aData["tr_id"]).data("aData", aData).data("screen",
                "timeline");
    };

    mod.fnDrawCallback = function fnLibDrawCallback() {

        mod.redrawChosen();
        mod.checkToolBarIcons();

        $('#library_display tr.lib-audio, tr.lib-pl, tr.lib-stream')
            .draggable(
                {
                    helper : function() {

                        var $el = $(this), selected = mod
                                .getChosenItemsLength(), container, thead = $("#show_builder_table thead"), colspan = thead
                                .find("th").length, width = thead.find(
                                "tr:first").width(), message;

                        // dragging an element that has an unselected
                        // checkbox.
                        if (mod.isChosenItem($el) === false) {
                            selected++;
                        }

                        if (selected === 1) {
                            message = $.i18n._("Adding 1 Item");
                        } else {
                            message = sprintf($.i18n._("Adding %s Items"), selected);
                        }

                        container = $('<div/>').attr('id',
                                'draggingContainer').append('<tr/>')
                                .find("tr").append('<td/>').find("td")
                                .attr("colspan", colspan).width(width)
                                .addClass("ui-state-highlight").append(
                                        message).end().end();

                        return container;
                    },
                    cursor : 'pointer',
                    cursorAt: {
                        top: 30,
                        left: 100
                    },
                    connectToSortable : '#show_builder_table'
                });
    };

    mod.dblClickAdd = function(data, type) {
        var i, length, temp, aMediaIds = [], aSchedIds = [], aData = [];

        // process selected files/playlists.
        aMediaIds.push( {
            "id" : data.id,
            "type" : type
        });

        $("#show_builder_table tr.sb-selected").each(function(i, el) {
            aData.push($(el).prev().data("aData"));
        });

        // process selected schedule rows to add media after.
        for (i = 0, length = aData.length; i < length; i++) {
            temp = aData[i];
            aSchedIds.push( {
                "id" : temp.id,
                "instance" : temp.instance,
                "timestamp" : temp.timestamp
            });
        }

        if (aSchedIds.length == 0) {
            if (!addToCurrentOrNext(aSchedIds)) {
                return;
            }
        }

        AIRTIME.showbuilder.fnAdd(aMediaIds, aSchedIds);
    };

    function addToCurrentOrNext(arr) {
        var el;
        // Get the show instance id of the first non-data row (id = 0)
        // The second last row in the table with that instance id is the
        // last schedule item for the first show. (This is important for
        // the Now Playing screen if multiple shows are in view).
        el = $("[si_id="+$("#0").attr("si_id")+"]");
        var temp = el.eq(-2).data("aData");

        if (temp === undefined) {
            alert($.i18n._("Cannot schedule outside a show."));
            return false;
        }

        arr.push({
            "id" : temp.id,
            "instance" : temp.instance,
            "timestamp" : temp.timestamp
        });

        if (!isInView(el)) {
            $('.dataTables_scrolling.sb-padded').animate({
                scrollTop: el.offset().top
            }, 0);
        }

        return arr;
    }

    mod.setupLibraryToolbar = function() {
        var $toolbar = $(".lib-content .fg-toolbar:first");

        mod.createToolbarButtons();
        
        $toolbar.append($menu);
        // add to timeline button
        $toolbar
            .find('.icon-plus').parent()
            .click(
                    function() {

                        if (AIRTIME.button.isDisabled('btn-group #library-plus') === true) {
                            return;
                        }
    
                        var selected = AIRTIME.library.getSelectedData(), data, i, length, temp, aMediaIds = [], aSchedIds = [], aData = [];
    
                        // process selected files/playlists.
                        for (i = 0, length = selected.length; i < length; i++) {
                            data = selected[i];
                            aMediaIds.push( {
                                "id" : data.id,
                                "type" : data.ftype
                            });
                        }
    
                        $("#show_builder_table tr.sb-selected")
                                .each(function(i, el) {
                                    aData.push($(el).prev().data("aData"));
                                });
    
                        // process selected schedule rows to add media
                        // after.
                        for (i = 0, length = aData.length; i < length; i++) {
                            temp = aData[i];
                            aSchedIds.push( {
                                "id" : temp.id,
                                "instance" : temp.instance,
                                "timestamp" : temp.timestamp
                            });
                        }

                        if (aSchedIds.length == 0) {
                            addToCurrentOrNext(aSchedIds);
                        }
    
                        AIRTIME.showbuilder.fnAdd(aMediaIds, aSchedIds);
                    });

        // delete from library.
        $toolbar.find('.icon-trash').parent().click(function() {

            if (AIRTIME.button.isDisabled('icon-trash') === true) {
                return;
            }

            AIRTIME.library.fnDeleteSelectedItems();
        });

        mod.createToolbarDropDown();
    };

    return AIRTIME;

}(AIRTIME || {}));
