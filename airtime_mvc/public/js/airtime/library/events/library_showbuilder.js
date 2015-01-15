var AIRTIME = (function(AIRTIME) {
    var mod;

    if (AIRTIME.library === undefined) {
        AIRTIME.library = {};
    }

    mod = AIRTIME.library;

    mod.checkAddButton = function() {
        var selected = mod.getChosenItemsLength(), $cursor = $('tr.cursor-selected-row'), check = false;

        // make sure library items are selected and a cursor is selected.
        if (selected !== 0 && $cursor.length !== 0) {
            check = true;
        }

        if (check === true) {
            AIRTIME.button.enableButton("btn-group #library-plus", false);
        } else {
            AIRTIME.button.disableButton("btn-group #library-plus", false);
        }
        
        AIRTIME.library.changeAddButtonText($('.btn-group #library-plus #lib-plus-text'), ' '+$.i18n._('Add to selected show'));
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

        $("#show_builder_table tr.cursor-selected-row").each(function(i, el) {
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
            alert($.i18n._("Please select a cursor position on timeline."));
            return false;
        }
        AIRTIME.showbuilder.fnAdd(aMediaIds, aSchedIds);
    };

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
    
                        $("#show_builder_table tr.cursor-selected-row")
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
