var AIRTIME = (function (AIRTIME) {
  var mod;

  if (AIRTIME.library === undefined) {
    AIRTIME.library = {};
  }

  mod = AIRTIME.library;

  mod.checkAddButton = function () {
    var selected = mod.getChosenItemsLength(),
      $cursor = $("tr.sb-selected"),
      check = false,
      shows = $("tr.sb-header"),
      current = $("tr.sb-current-show"),
      // TODO: this is an ugly way of doing this... we should find a more robust way of checking which view we're in.
      btnText =
        window.location.href.toLowerCase().indexOf("schedule") > -1
          ? $.i18n._("Add to show")
          : $.i18n._("Add to next show");

    // make sure library items are selected and a cursor is selected.
    if (selected !== 0) {
      check = true;
    }

    var sortable = $(".spl_sortable");
    if ($("#show_builder_table").is(":visible")) {
      if (shows.length === 0) {
        check = false;
      }

      if ($cursor.length !== 0) {
        btnText = $.i18n._("Add after selected items");
      } else if (current.length !== 0) {
        btnText = $.i18n._("Add to current show");
      }
    } else if (sortable.length > 0 && sortable.is(":visible")) {
      var objType = $(".active-tab .obj_type").val();
      if (objType === "block") {
        btnText = $.i18n._("Add to current smart block");
      } else {
        btnText = $.i18n._("Add to current playlist");
      }
    } else {
      check = false;
    }

    if (check) {
      AIRTIME.button.enableButton("btn-group #library-plus", false);
    } else {
      AIRTIME.button.disableButton("btn-group #library-plus", false);
    }

    AIRTIME.library.changeAddButtonText(
      $(".btn-group #library-plus #lib-plus-text"),
      btnText,
    );
  };

  mod.fnRowCallback = function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
    var $nRow = $(nRow);

    if (aData.ftype === "audioclip") {
      $nRow.addClass("lib-audio");
      $image = $nRow.find("td.library_type");
      if (!isAudioSupported(aData.mime)) {
        $image.html('<span class="ui-icon ui-icon-locked"></span>');
        aData.image = '<span class="ui-icon ui-icon-locked"></span>';
      }
    } else if (aData.ftype === "stream") {
      $nRow.addClass("lib-stream");
    } else {
      $nRow.addClass("lib-pl");
    }

    $nRow
      .attr("id", aData["tr_id"])
      .data("aData", aData)
      .data("screen", "timeline");
  };

  /**
   * Draw a placeholder for the given table to show if it has no data.
   *
   * @param {Object} table jQuery object containing the table DOM node
   */
  mod.drawEmptyPlaceholder = function (table) {
    var opts;
    if (table instanceof AIRTIME.widgets.Table) {
      opts = table.getEmptyPlaceholder();
      table = table.getDatatable();
      if (!table) {
        return;
      }
    }
    var emptyRow = table.find("tr:has(td.dataTables_empty)"),
      wrapper = table.closest(".dataTables_wrapper"),
      libEmpty = wrapper.find(".empty_placeholder");
    if (emptyRow.length > 0) {
      emptyRow.hide();
      var mediaType = parseInt(
          $(".media_type_selector.selected").data("selection-id"),
        ),
        img = wrapper.find(".empty_placeholder_image");
      if (!opts && isNaN(mediaType)) {
        return;
      }
      // Remove all classes for when we change between empty media types
      img.removeClass(function () {
        return $(this).attr("class");
      });

      if (opts) {
        img.addClass("empty_placeholder_image " + opts.iconClass);
        wrapper.find(".empty_placeholder_text").html(opts.html);
      } else {
        opts = AIRTIME.library.placeholder(mediaType);
        img.addClass("empty_placeholder_image icon-white " + opts.icon);
        wrapper
          .find(".empty_placeholder_text")
          .html(
            $.i18n._("You haven't added any " + opts.media) +
              "<br/>" +
              $.i18n._(opts.subtext) +
              "<br/><a target='_blank' href='" +
              opts.href +
              "'>" +
              $.i18n._("Learn about " + opts.media) +
              "</a>",
          );
      }

      libEmpty.show();
    } else {
      libEmpty.hide();
    }
  };

  mod.fnDrawCallback = function fnLibDrawCallback() {
    var table = $("#library_display"),
      cb = table.find('th[class*="checkbox"]');
    if (cb.find("input").length == 0) {
      cb.append("<input id='super-checkbox' type='checkbox'>");
    }

    mod.redrawChosen();
    mod.checkToolBarIcons();

    mod.drawEmptyPlaceholder(table);

    var sortable;

    if ($("#show_builder_table").is(":visible")) {
      sortable = "#show_builder_table";
    } else {
      sortable = ".active-tab .spl_sortable";
    }

    $('#library_display tr[class*="lib-"]').draggable({
      helper: function () {
        var $el = $(this),
          selected = mod.getChosenItemsLength(),
          container,
          thead = $("#show_builder_table thead"),
          colspan = thead.find("th").length,
          width = $el.width(),
          message;

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

        container = $("<div/>")
          .attr("id", "draggingContainer")
          .append("<tr/>")
          .find("tr")
          .append("<td/>")
          .find("td")
          .attr("colspan", colspan)
          .width(width)
          .addClass("ui-state-highlight")
          .append(message)
          .end()
          .end();

        return container;
      },
      create: function (event, ui) {
        $(this).draggable("option", "cursorAt", {
          top: 20,
          left: Math.floor($(this).outerWidth() / 2),
        });
      },
      tolerance: "pointer",
      cursor: "move",
      distance: 25, // min-distance for dragging
      connectToSortable: sortable,
    });
  };

  mod.dblClickAdd = function (data, type) {
    var i,
      length,
      temp,
      aMediaIds = [],
      aSchedIds = [],
      aData = [];

    if ($("#show_builder_table").is(":visible")) {
      // process selected files/playlists.
      aMediaIds.push({
        id: data.id,
        type: type,
      });

      $("#show_builder_table tr.sb-selected").each(function (i, el) {
        aData.push($(el).data("aData"));
      });

      // process selected schedule rows to add media after.
      for (i = 0, length = aData.length; i < length; i++) {
        temp = aData[i];
        aSchedIds.push({
          id: temp.id,
          instance: temp.instance,
          timestamp: temp.timestamp,
        });
      }

      if (aSchedIds.length == 0) {
        if (!addToCurrentOrNext(aSchedIds)) {
          return;
        }
      }

      AIRTIME.showbuilder.fnAdd(aMediaIds, aSchedIds);
    } else {
      // process selected files/playlists.
      aMediaIds.push(new Array(data.id, data.ftype));

      // check if a playlist/block is open before adding items
      if (
        $(".active-tab .obj_type").val() == "playlist" ||
        $(".active-tab .obj_type").val() == "block"
      ) {
        AIRTIME.playlist.fnAddItems(aMediaIds, undefined, "after");
      }
    }
  };

  function addToCurrentOrNext(arr) {
    var el;
    // Add to the end of the current or next show by getting the footer
    el = $(".sb-footer.sb-future:first");
    var data = el.prev().data("aData");

    if (data === undefined) {
      alert(
        $.i18n._("Cannot schedule outside a show.\nTry creating a show first."),
      );
      return false;
    }

    arr.push({
      id: data.id,
      instance: data.instance,
      timestamp: data.timestamp,
    });

    if (!isInView(el)) {
      $(".dataTables_scrolling.sb-padded").animate(
        {
          scrollTop: el.offset().top,
        },
        0,
      );
    }

    return true;
  }

  mod.addToSchedule = function (selected) {
    console.log(selected);
    var aMediaIds = [],
      aSchedIds = [],
      aData = [];

    $.each(selected, function () {
      aMediaIds.push({
        id: this.id,
        type: this.ftype,
      });
    });

    // process selected files/playlists.
    $("#show_builder_table")
      .find("tr.sb-selected")
      .each(function (i, el) {
        aData.push($(el).data("aData"));
      });

    // process selected schedule rows to add media after.
    $.each(aData, function () {
      aSchedIds.push({
        id: this.id,
        instance: this.instance,
        timestamp: this.timestamp,
      });
    });

    if (aSchedIds.length == 0) {
      if (!addToCurrentOrNext(aSchedIds)) {
        return;
      }
    }

    AIRTIME.showbuilder.fnAdd(aMediaIds, aSchedIds);
  };

  mod.setupLibraryToolbar = function () {
    var $toolbar = $(".lib-content .fg-toolbar:first");

    mod.createToolbarButtons();
    //mod.moveSearchBarToHeader();
    $("#advanced_search").click(function (e) {
      e.stopPropagation();
    });

    if (localStorage.getItem("user-type") != "G") {
      $toolbar.append($menu);
      // add to timeline button
      $toolbar.find("#library-plus").click(function () {
        if (AIRTIME.button.isDisabled("btn-group #library-plus") === true) {
          return;
        }

        var selected = AIRTIME.library.getSelectedData(),
          aMediaIds = [];

        if ($("#show_builder_table").is(":visible")) {
          mod.addToSchedule(selected);
        } else {
          $.each(selected, function () {
            aMediaIds.push([this.id, this.ftype]);
          });

          // check if a playlist/block is open before adding items
          if (
            $(".active-tab .obj_type").val() == "playlist" ||
            $(".active-tab .obj_type").val() == "block"
          ) {
            AIRTIME.playlist.fnAddItems(aMediaIds, undefined, "after");
          }
        }
      });

      $toolbar.find("#publish-btn").click(function () {
        if (AIRTIME.button.isDisabled("btn-group #publish-btn") === true) {
          return;
        }

        var selected = $(".lib-selected");

        selected.each(function (i, el) {
          var data = $(el).data("aData");
          AIRTIME.publish.openPublishDialog(data.id);
        });
      });

      // delete from library.
      $toolbar.find("#sb-delete").click(function () {
        if (AIRTIME.button.isDisabled("btn-group #sb-delete") === true) {
          return;
        }

        AIRTIME.library.fnDeleteSelectedItems();
      });

      $toolbar.find("#sb-new").click(function () {
        if (AIRTIME.button.isDisabled("btn-group #sb-new") === true) {
          return;
        }

        var selection = $(".media_type_selector.selected").data("selection-id");

        if (selection == AIRTIME.library.MediaTypeIntegerEnum.PLAYLIST) {
          AIRTIME.playlist.fnNew();
        } else if (selection == AIRTIME.library.MediaTypeIntegerEnum.BLOCK) {
          AIRTIME.playlist.fnNewBlock();
        } else if (
          selection == AIRTIME.library.MediaTypeIntegerEnum.WEBSTREAM
        ) {
          AIRTIME.playlist.fnWsNew();
        }
      });

      $toolbar.find("#sb-edit").click(function () {
        if (AIRTIME.button.isDisabled("btn-group #sb-edit") === true) {
          return;
        }

        var selected = $(".lib-selected");

        selected.each(function (i, el) {
          var data = $(el).data("aData");

          if (data.ftype === "audioclip") {
            $.get(
              baseUrl + "library/edit-file-md/id/" + data.id,
              { format: "json" },
              function (json) {
                AIRTIME.playlist.fileMdEdit(json, data.tr_id);
                //buildEditMetadataDialog(json);
              },
            );
          } else if (data.ftype === "playlist" || data.ftype === "block") {
            AIRTIME.playlist.fnEdit(data, baseUrl + "playlist/edit");
            AIRTIME.playlist.validatePlaylistElements();
          } else if (data.ftype === "stream") {
            AIRTIME.playlist.fnEdit(data, baseUrl + "webstream/edit");
          }
        });
      });

      mod.createToolbarDropDown();
    }
  };

  return AIRTIME;
})(AIRTIME || {});
