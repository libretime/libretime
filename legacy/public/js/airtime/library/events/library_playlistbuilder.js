var AIRTIME = (function (AIRTIME) {
  var mod;

  if (AIRTIME.library === undefined) {
    AIRTIME.library = {};
  }

  mod = AIRTIME.library;

  mod.checkAddButton = function () {
    var selected = mod.getChosenItemsLength(),
      sortable = $(".spl_sortable:visible"),
      check = false,
      blockType = $("input[name=sp_type]:checked", "#smart-block-form").val();

    // make sure audioclips are selected and a playlist or static block is currently open.
    // static blocks have value of 0
    // dynamic blocks have value of 1
    if (selected !== 0 && (sortable.length !== 0 || blockType === "0")) {
      check = true;
    }

    if (check === true) {
      AIRTIME.button.enableButton("btn-group #library-plus", false);
    } else {
      AIRTIME.button.disableButton("btn-group #library-plus", false);
    }

    var objType = $(".obj_type").val(),
      btnText;
    if (objType === "playlist") {
      btnText = " " + $.i18n._("Add to current playlist");
    } else if (objType === "block") {
      btnText = " " + $.i18n._("Add to current smart block");
    } else {
      btnText = " " + $.i18n._("Add to current playlist");
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
    } else if (aData.ftype === "block") {
      $nRow.addClass("lib-block");
    } else {
      $nRow.addClass("lib-pl");
    }

    $nRow
      .attr("id", aData["tr_id"])
      .data("aData", aData)
      .data("screen", "playlist");

    if (aData["bl_type"] !== undefined) {
      $nRow.attr("bl_type", aData["bl_type"]);
    }
  };

  mod.fnDrawCallback = function () {
    mod.redrawChosen();
    mod.checkToolBarIcons();

    $(
      "#library_display tr.lib-audio, tr.lib-stream, tr.lib-pl, tr.lib-block",
    ).draggable({
      helper: function () {
        var $el = $(this),
          selected = mod.getChosenAudioFilesLength(),
          container,
          message,
          li = $(".side_playlist ul[id='spl_sortable'] li:first"),
          width = li.width(),
          height = 55;
        if (width > 798) width = 798;

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

        container = $('<div class="helper"/>')
          .append("<li/>")
          .find("li")
          .addClass("ui-state-default")
          .append("<div/>")
          .find("div")
          .addClass("list-item-container")
          .append(message)
          .end()
          .width(width)
          .height(height)
          .end();

        return container;
      },
      cursor: "pointer",
      cursorAt: {
        top: 30,
        left: 100,
      },
      connectToSortable: ".spl_sortable",
    });
  };

  mod.dblClickAdd = function (data, type) {
    var i,
      aMediaIds = [];

    // process selected files/playlists.
    aMediaIds.push(new Array(data.id, data.ftype));

    // check if a playlist/block is open before adding items
    if (
      $('input[id="obj_type"]').val() == "playlist" ||
      $('input[id="obj_type"]').val() == "block"
    ) {
      AIRTIME.playlist.fnAddItems(aMediaIds, undefined, "after");
    }
  };

  mod.setupLibraryToolbar = function () {
    var $toolbar = $(".lib-content .fg-toolbar:first");

    mod.createToolbarButtons();

    $toolbar.append($menu);

    // add to playlist button
    $toolbar
      .find(".icon-plus")
      .parent()
      .click(function () {
        if (AIRTIME.button.isDisabled("btn-group #library-plus") === true) {
          return;
        }

        var aData = AIRTIME.library.getSelectedData(),
          i,
          temp,
          length,
          aMediaIds = [];

        // process selected files/playlists.
        for (i = 0, length = aData.length; i < length; i++) {
          temp = aData[i];
          if (
            temp.ftype === "audioclip" ||
            temp.ftype === "block" ||
            (temp.ftype === "stream" && $(".obj_type").val() === "playlist")
          ) {
            aMediaIds.push(new Array(temp.id, temp.ftype));
          }
        }
        if (aMediaIds.length > 0) {
          AIRTIME.playlist.fnAddItems(aMediaIds, undefined, "after");
        } else {
          if ($(".obj_type").val() == "block") {
            alert($.i18n._("You can only add tracks to smart blocks."));
          } else if ($(".obj_type").val() == "playlist") {
            alert(
              $.i18n._(
                "You can only add tracks, smart blocks, and webstreams to playlists.",
              ),
            );
          }
        }
      });

    // delete from library.
    $toolbar
      .find(".icon-trash")
      .parent()
      .click(function () {
        if (AIRTIME.button.isDisabled("icon-trash") === true) {
          return;
        }

        AIRTIME.library.fnDeleteSelectedItems();
      });

    mod.createToolbarDropDown();
  };

  return AIRTIME;
})(AIRTIME || {});
