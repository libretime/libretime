var AIRTIME = (function (AIRTIME) {
  var mod,
    oSchedTable,
    SB_SELECTED_CLASS = "sb-selected",
    CURSOR_SELECTED_CLASS = "cursor-selected-row",
    NOW_PLAYING_CLASS = "sb-now-playing",
    $sbContent,
    $sbTable,
    $toolbar,
    $lib,
    cursors = [],
    cursorIds = [],
    showInstanceIds = [],
    headerFooter = [],
    DISABLED_CLASS = "ui-state-disabled",
    selectedRows,
    $previouslySelected,
    flagForDeselection;

  if (AIRTIME.showbuilder === undefined) {
    AIRTIME.showbuilder = {};
  }
  mod = AIRTIME.showbuilder;

  function checkError(json) {
    if (json.error !== undefined) {
      alert(json.error);
    }
  }

  mod.timeout = undefined;
  mod.timestamp = -1;
  mod.showInstances = [];

  mod.resetTimestamp = function () {
    mod.timestamp = -1;
  };

  mod.setTimestamp = function (timestamp) {
    mod.timestamp = timestamp;
  };

  mod.updateCalendarStatusIcon = function (json) {
    //make sure we are only executing this code on the calendar view, not
    //the Now Playing view.
    if (window.location.pathname.toLowerCase().indexOf("schedule") < 0) {
      return;
    }

    var instance_id = json.schedule[0].instance;

    var lastElem = json.schedule[json.schedule.length - 1];
    var $elem = $("#fc-show-instance-" + instance_id);

    //if the show is linked, then replace $elem to reference all linked
    //instances
    if ($elem.data("show-linked") == "1") {
      var show_id = $elem.data("show-id");
      $elem = $('*[data-show-id="' + show_id + '"]');
    }

    $elem.find(".show-empty, .show-partial-filled").remove();
    if (json.schedule[1].empty) {
      $elem
        .find(".fc-event-inner")
        .append(
          '<span id="' +
            instance_id +
            '" title="' +
            $.i18n._("Show is empty") +
            '" class="small-icon show-empty"></span>',
        );
    } else if (lastElem["fRuntime"][0] == "-") {
      $elem
        .find(".fc-event-inner")
        .append(
          '<span id="' +
            instance_id +
            '" title="' +
            $.i18n._("Show is partially filled") +
            '" class="small-icon show-partial-filled"></span>',
        );
    }
  };

  mod.getTimestamp = function () {
    if (mod.timestamp !== undefined) {
      return mod.timestamp;
    } else {
      return -1;
    }
  };

  mod.setShowInstances = function (showInstances) {
    mod.showInstances = showInstances;
  };

  mod.getShowInstances = function () {
    return mod.showInstances;
  };

  mod.refresh = function (schedId) {
    mod.resetTimestamp();

    // once a track plays out we need to check if we can update
    // the is_scheduled flag in cc_files
    if (schedId > 0) {
      $.post(
        baseUrl + "schedule/update-future-is-scheduled",
        { format: "json", schedId: schedId },
        function (data) {
          if (data.redrawLibTable !== undefined && data.redrawLibTable) {
            $("#library_content")
              .find("#library_display")
              .dataTable()
              .fnStandingRedraw();
          }
        },
      );
      oSchedTable.fnDraw();
    }
  };

  mod.checkSelectButton = function () {
    var $selectable = $sbTable.find("tr");

    if ($selectable.length !== 0) {
      AIRTIME.button.enableButton("btn-group #timeline-select", false);
    } else {
      AIRTIME.button.disableButton("btn-group #timeline-select", false);
    }

    //need to check if the 'Select' button is disabled
    var select = $(".btn-group #timeline-select");
    if (select.is(":disabled")) {
      select.removeAttr("disabled");
    }
  };

  mod.checkTrimButton = function () {
    var $over = $sbTable.find(".sb-over.sb-allowed");

    if ($over.length !== 0) {
      AIRTIME.button.enableButton("icon-cut", true);
    } else {
      AIRTIME.button.disableButton("icon-cut", true);
    }
  };

  mod.checkDeleteButton = function () {
    var $selected = $("." + SB_SELECTED_CLASS);

    var button = $("#show_builder").find(".icon-trash").parent();
    if ($selected.length !== 0) {
      button.removeClass(DISABLED_CLASS);
      button.removeAttr("disabled");
    } else {
      button.addClass(DISABLED_CLASS);
      button.attr("disabled", "disabled");
    }
  };

  mod.checkJumpToCurrentButton = function () {
    var $current = $sbTable.find("." + NOW_PLAYING_CLASS);

    if ($current.length !== 0) {
      AIRTIME.button.enableButton("icon-step-forward", true);
    } else {
      AIRTIME.button.disableButton("icon-step-forward", true);
    }
  };

  mod.checkCancelButton = function () {
    var $current = $sbTable.find(".sb-current-show"),
      //this user type should be refactored into a separate users module later
      //when there's more time and more JS will need to know user data.
      userType = localStorage.getItem("user-type"),
      canCancel = false;

    if ($current.length !== 0 && $current.hasClass("sb-allowed")) {
      canCancel = true;
    } else if (
      $current.length !== 0 &&
      (userType === "A" || userType === "P")
    ) {
      canCancel = true;
    }

    if (canCancel === true) {
      AIRTIME.button.enableButton("icon-ban-circle", true);
    } else {
      AIRTIME.button.disableButton("icon-ban-circle", true);
    }
  };

  mod.checkToolBarIcons = function () {
    //library may not be on the page.
    if (AIRTIME.library !== undefined) {
      AIRTIME.library.checkAddButton();
    }

    mod.checkSelectButton();
    mod.checkTrimButton();
    mod.checkDeleteButton();
    mod.checkJumpToCurrentButton();
    mod.checkCancelButton();
  };

  mod.selectCursor = function ($el) {
    $el.addClass(CURSOR_SELECTED_CLASS);
    mod.checkToolBarIcons();
  };

  mod.removeCursor = function ($el) {
    $el.removeClass(CURSOR_SELECTED_CLASS);
    mod.checkToolBarIcons();
  };

  /*
   * sNot is an optional string to filter selected elements by. (ex removing the currently playing item)
   */
  mod.getSelectedData = function (sNot) {
    var $selected = $sbTable.find("tr." + SB_SELECTED_CLASS),
      aData = [],
      i,
      length,
      $item;

    if (sNot !== undefined) {
      $selected = $selected.not("." + sNot);
    }

    for (i = 0, length = $selected.length; i < length; i++) {
      $item = $($selected.get(i));
      aData.push($item.data("aData"));
    }

    return aData.reverse();
  };

  mod.selectAll = function () {
    var $trs = $sbTable.find("tr.lib-audio").not(".sb-past, .sb-empty");
    $trs
      .addClass(SB_SELECTED_CLASS)
      .find(".sb-checkbox > input")
      .prop("checked", true);

    mod.checkToolBarIcons();
  };

  mod.selectNone = function () {
    var $trs = $sbTable.find("tr.lib-audio");
    $trs
      .removeClass(SB_SELECTED_CLASS)
      .find(".sb-checkbox > input")
      .prop("checked", false);
    $previouslySelected = undefined;
    selectedRows = undefined;

    mod.checkToolBarIcons();
  };

  mod.disableUI = function () {
    $lib.block({
      message: "",
      theme: true,
      applyPlatformOpacityRules: false,
    });

    $sbContent.block({
      message: "",
      theme: true,
      applyPlatformOpacityRules: false,
    });
  };

  mod.enableUI = function () {
    $lib.unblock();
    $sbContent.unblock();
  };

  mod.fnItemCallback = function (json) {
    checkError(json);

    mod.getSelectedCursors();
    selectedRows = $("." + SB_SELECTED_CLASS);
    oSchedTable.fnDraw();
    mod.enableUI();

    //Unneccessary reload of the library pane after moving tracks in the showbuilder pane.
    //$("#library_content").find("#library_display").dataTable().fnStandingRedraw();

    getUsabilityHint();
  };

  mod.getSelectedCursors = function () {
    cursorIds = [];

    /* We need to keep record of which show the cursor belongs to
     * in the case where more than one show is displayed in the show builder
     * because header and footer rows have the same id
     */
    showInstanceIds = [];

    /* Keeps track if the row is a footer. We need to do this because
     * header and footer rows have the save cursorIds and showInstanceId
     * so both will be selected in the draw callback
     */
    headerFooter = [];

    cursors = $(".cursor-selected-row");
    for (i = 0; i < cursors.length; i++) {
      cursorIds.push($(cursors.get(i)).attr("id"));
      showInstanceIds.push($(cursors.get(i)).attr("si_id"));
      if ($(cursors.get(i)).hasClass("sb-footer")) {
        headerFooter.push("f");
      } else {
        headerFooter.push("n");
      }
    }
  };

  mod.fnAdd = function (aMediaIds, aSchedIds) {
    AIRTIME.library.selectNone();
    mod.disableUI();

    $.post(
      baseUrl + "showbuilder/schedule-add",
      { format: "json", mediaIds: aMediaIds, schedIds: aSchedIds },
      mod.fnItemCallback,
    );
  };

  mod.fnMove = function (aSelect, aAfter) {
    mod.disableUI();

    $.post(
      baseUrl + "showbuilder/schedule-move",
      { format: "json", selectedItem: aSelect, afterItem: aAfter },
      mod.fnItemCallback,
    );
  };

  mod.fnRemove = function (aItems) {
    mod.disableUI();
    if (confirm($.i18n._("Delete selected item(s)?"))) {
      mod.selectNone();
      $.post(
        baseUrl + "showbuilder/schedule-remove",
        { items: aItems, format: "json" },
        mod.fnItemCallback,
      );
    } else {
      mod.enableUI();
    }
  };

  mod.fnRemoveSelectedItems = function () {
    var aData = mod.getSelectedData(),
      i,
      length,
      temp,
      aItems = [];

    for (i = 0, length = aData.length; i < length; i++) {
      temp = aData[i];
      aItems.push({
        id: temp.id,
        instance: temp.instance,
        timestamp: temp.timestamp,
      });
    }

    mod.fnRemove(aItems);
  };

  mod.fnServerData = function fnBuilderServerData(sSource, aoData, fnCallback) {
    aoData.push({ name: "timestamp", value: mod.getTimestamp() });
    aoData.push({ name: "instances", value: mod.getShowInstances() });
    aoData.push({ name: "format", value: "json" });

    if (mod.fnServerData.hasOwnProperty("start")) {
      aoData.push({ name: "start", value: mod.fnServerData.start });
    }
    if (mod.fnServerData.hasOwnProperty("end")) {
      aoData.push({ name: "end", value: mod.fnServerData.end });
    }
    if (mod.fnServerData.hasOwnProperty("ops")) {
      aoData.push({ name: "myShows", value: mod.fnServerData.ops.myShows });
      aoData.push({
        name: "showFilter",
        value: mod.fnServerData.ops.showFilter,
      });
      aoData.push({
        name: "showInstanceFilter",
        value: mod.fnServerData.ops.showInstanceFilter,
      });
    }

    $.ajax({
      dataType: "json",
      type: "POST",
      url: sSource,
      data: aoData,
      success: function (json) {
        mod.updateCalendarStatusIcon(json);
        mod.setTimestamp(json.timestamp);
        mod.setShowInstances(json.instances);
        mod.getSelectedCursors();
        fnCallback(json);
      },
    });
  };

  mod.jumpToCurrentTrack = function () {
    var $scroll = $sbContent.find(".dataTables_scrolling");
    var scrolled = $scroll.scrollTop();
    var scrollingTop = $scroll.offset().top;
    var current = $sbTable.find("." + NOW_PLAYING_CLASS);
    var currentTop = current.offset().top;

    $scroll.scrollTop(currentTop - scrollingTop + scrolled);
  };

  mod.builderDataTable = function () {
    $sbContent = $("#show_builder");
    $lib = $("#library_content");
    $sbTable = $sbContent.find("table");
    var isInitialized = false;

    var lockedPreviewIcon = document.createElement("span");
    lockedPreviewIcon.setAttribute("class", "ui-icon ui-icon-locked");
    var previewIcon = document.createElement("img");
    previewIcon.setAttribute("src", baseUrl + "css/images/icon_audioclip.png");
    previewIcon.setAttribute("title", $.i18n._("Track preview"));

    oSchedTable = $sbTable.dataTable({
      aoColumns: [
        /* checkbox */ {
          mDataProp: "allowed",
          sTitle: "",
          sWidth: "16px",
          sClass: "sb-checkbox",
        },
        /* Type */ {
          mDataProp: "image",
          sTitle: "",
          sClass: "library_image sb-image",
          sWidth: "16px",
        },
        /* starts */ {
          mDataProp: "starts",
          sTitle: $.i18n._("Start"),
          sClass: "sb-starts",
          sWidth: "60px",
        },
        /* ends */ {
          mDataProp: "ends",
          sTitle: $.i18n._("End"),
          sClass: "sb-ends",
          sWidth: "60px",
        },
        /* runtime */ {
          mDataProp: "runtime",
          sTitle: $.i18n._("Duration"),
          sClass: "library_length sb-length",
          sWidth: "65px",
        },
        /* title */ {
          mDataProp: "title",
          sTitle: $.i18n._("Title"),
          sClass: "sb-title",
        },
        /* creator */ {
          mDataProp: "creator",
          sTitle: $.i18n._("Creator"),
          sClass: "sb-creator",
        },
        /* album */ {
          mDataProp: "album",
          sTitle: $.i18n._("Album"),
          sClass: "sb-album",
        },
        /* cue in */ {
          mDataProp: "cuein",
          sTitle: $.i18n._("Cue In"),
          bVisible: false,
          sClass: "sb-cue-in",
        },
        /* cue out */ {
          mDataProp: "cueout",
          sTitle: $.i18n._("Cue Out"),
          bVisible: false,
          sClass: "sb-cue-out",
        },
        /* fade in */ {
          mDataProp: "fadein",
          sTitle: $.i18n._("Fade In"),
          bVisible: false,
          sClass: "sb-fade-in",
        },
        /* fade out */ {
          mDataProp: "fadeout",
          sTitle: $.i18n._("Fade Out"),
          bVisible: false,
          sClass: "sb-fade-out",
        },
        /* Mime */ {
          mDataProp: "mime",
          sTitle: $.i18n._("Mime"),
          bVisible: false,
          sClass: "sb-mime",
        },
      ],

      bJQueryUI: true,
      bSort: false,
      bFilter: false,
      bProcessing: true,
      bServerSide: true,
      bInfo: false,
      bAutoWidth: false,
      bDeferRender: true,

      bStateSave: true,
      fnStateSaveParams: function (oSettings, oData) {
        //remove oData components we don't want to save.
        delete oData.oSearch;
        delete oData.aoSearchCols;
      },
      fnStateSave: function fnStateSave(oSettings, oData) {
        localStorage.setItem("datatables-timeline", JSON.stringify(oData));
      },
      fnStateLoad: function fnBuilderStateLoad(oSettings) {
        var settings = localStorage.getItem("datatables-timeline");

        if (settings !== "") {
          return JSON.parse(settings);
        }
      },
      fnStateLoadParams: function (oSettings, oData) {
        var i,
          length,
          a = oData.abVisCols;

        //putting serialized data back into the correct js type to make
        //sure everything works properly.
        for (i = 0, length = a.length; i < length; i++) {
          if (typeof a[i] === "string") {
            a[i] = a[i] === "true";
          }
        }

        oData.iCreate = parseInt(oData.iCreate, 10);
      },

      fnServerData: mod.fnServerData,
      fnRowCallback: function fnRowCallback(
        nRow,
        aData,
        iDisplayIndex,
        iDisplayIndexFull,
      ) {
        var i,
          length,
          sSeparatorHTML,
          fnPrepareSeparatorRow,
          $node,
          cl = "",
          //background-color to imitate calendar color.
          r,
          g,
          b,
          a,
          $nRow = $(nRow),
          $image,
          $div,
          headerIcon;

        fnPrepareSeparatorRow = function fnPrepareSeparatorRow(
          sRowContent,
          sClass,
          iNodeIndex,
        ) {
          //Albert:
          //$(nRow.children[iNodeIndex]).replaceWith(emptyNode);

          $node = $(nRow.children[iNodeIndex]);
          $node.html(sRowContent);

          $node.attr("colspan", 100);
          for (
            i = iNodeIndex + 1, length = nRow.children.length;
            i < length;
            i = i + 1
          ) {
            $node = $(nRow.children[i]);
            $node.html("");
            $node.attr("style", "display : none");
          }

          $nRow.addClass(sClass);
        };

        if (aData.header === true) {
          //remove the column classes from all tds.
          $nRow.find("td").removeClass();

          $node = $(nRow.children[0]);
          $node.html("");
          cl = "sb-header";

          if (aData.record === true) {
            $div = $("<div/>", {
              class: "small-icon " + headerIcon,
            });
            $node.append($div);
          } else if (aData.rebroadcast === true) {
            $div = $("<div/>", {
              class: "small-icon rebroadcast",
            });
            $node.append($div);
          }

          sSeparatorHTML =
            '<span class="show-title">' + aData.title + "</span>";

          if (aData.rebroadcast === true) {
            sSeparatorHTML += "<span>" + aData.rebroadcast_title + "</span>";
          }

          sSeparatorHTML += '<span class="push-right">';

          if (aData.startDate === aData.endDate) {
            sSeparatorHTML +=
              '<span class="show-date">' +
              aData.startDate +
              '</span><span class="show-time">' +
              aData.startTime +
              "</span>";
            sSeparatorHTML +=
              '-<span class="show-time">' + aData.endTime + "</span>";
          } else {
            sSeparatorHTML +=
              '<span class="show-date">' +
              aData.startDate +
              '</span><span class="show-time">' +
              aData.startTime +
              "</span>";
            sSeparatorHTML +=
              '-<span class="show-date">' +
              aData.endDate +
              '</span><span class="show-time">' +
              aData.endTime +
              "</span>";
          }

          sSeparatorHTML += "</span>";

          fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
        } else if (aData.footer === true) {
          //remove the column classes from all tds.
          $nRow.find("td").removeClass();

          $node = $(nRow.children[0]);
          cl = "sb-footer";

          //check the show's content status.
          if (aData.runtime >= 0) {
            $node.html('<span class="ui-icon ui-icon-check"></span>');
            cl = cl + " ui-state-highlight";
          } else {
            $node.html('<span class="ui-icon ui-icon-notice"></span>');
            cl = cl + " ui-state-error";
          }

          sSeparatorHTML = "<span>" + aData.fRuntime + "</span>";
          fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
        } else if (aData.empty === true) {
          //remove the column classes from all tds.
          $nRow.find("td").removeClass();

          $node = $(nRow.children[0]);
          if ($node) {
            $node.empty();
          }

          sSeparatorHTML =
            "<span>" +
            $.i18n._("Drag tracks here from the library") +
            "</span>";
          cl = cl + " sb-empty odd";

          fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
        } else if (aData.record === true) {
          //remove the column classes from all tds.
          $nRow.find("td").removeClass();

          $node = $(nRow.children[0]);
          $node.html("");

          sSeparatorHTML =
            "<span>" + $.i18n._("Recording From Line In") + "</span>";
          cl = cl + " sb-record odd";
          fnPrepareSeparatorRow(sSeparatorHTML, cl, 1);
        } else {
          //add the play function if the file exists on disk.
          $image = $nRow.find("td.sb-image");
          $image.empty();
          //check if the file exists.
          if (aData.image === true) {
            $nRow.addClass("lib-audio");
            if (!isAudioSupported(aData.mime)) {
              //$image.html('<span class="ui-icon ui-icon-locked"></span>');
              $image.append(lockedPreviewIcon);
            } else {
              $image.append(previewIcon.cloneNode(false));
              $image.click(function () {
                open_show_preview(aData.instance, aData.pos);
                return false;
              });
            }
          } else {
            $image.html('<span class="ui-icon ui-icon-alert"></span>');
            $image.find(".ui-icon-alert").qtip({
              content: {
                text: $.i18n._(
                  'Airtime is unsure about the status of this file. This can happen when the file is on a remote drive that is unaccessible or the file is in a directory that isn\'t "watched" anymore.',
                ),
              },
              style: {
                classes: "ui-tooltip-dark",
              },
              show: "mouseover",
              hide: "mouseout",
            });
          }

          $node = $(nRow.children[0]);
          if (
            aData.allowed === true &&
            aData.scheduled >= 1 &&
            aData.linked_allowed
          ) {
            $node.html(
              '<input type="checkbox" name="' + aData.id + '"></input>',
            );
          } else {
            $node.empty();
          }
        }

        //add the show colour to the leftmost td
        if (aData.footer !== true) {
          if ($nRow.hasClass("sb-header")) {
            a = 1;
          } else if ($nRow.hasClass("odd")) {
            a = 0.3;
          } else if ($nRow.hasClass("even")) {
            a = 0.4;
          }

          //convert from hex to rgb.
          r = parseInt(aData.backgroundColor.substring(0, 2), 16);
          g = parseInt(aData.backgroundColor.substring(2, 4), 16);
          b = parseInt(aData.backgroundColor.substring(4, 6), 16);

          $nRow
            .find("td:first")
            .css(
              "background",
              "rgba(" + r + ", " + g + ", " + b + ", " + a + ")",
            );
        }

        //save some info for reordering purposes.
        $nRow.data({ aData: aData });

        if (aData.scheduled === 1) {
          $nRow.addClass(NOW_PLAYING_CLASS);
        } else if (aData.scheduled === 0 || aData.scheduled === undefined) {
          $nRow.addClass("sb-past");
        } else {
          $nRow.addClass("sb-future");
        }

        if (aData.allowed !== true || aData.linked_allowed === false) {
          $nRow.addClass("sb-not-allowed");
        } else {
          $nRow.addClass("sb-allowed");
          $nRow.attr("id", aData.id);
          $nRow.attr("si_id", aData.instance);
        }

        //status used to colour tracks.
        if (aData.status === 2) {
          $nRow.addClass("sb-boundry");
        } else if (aData.status === 0) {
          $nRow.addClass("sb-over");
        }

        if (aData.currentShow === true) {
          $nRow.addClass("sb-current-show");
        }
      },
      //remove any selected nodes before the draw.
      fnPreDrawCallback: function (oSettings) {
        //make sure any dragging helpers are removed or else they'll be stranded on the screen.
        $("#draggingContainer").remove();
      },
      fnDrawCallback: function fnBuilderDrawCallback(oSettings, json) {
        if (!isInitialized) {
          //when coming to 'Now Playing' page we want the page
          //to jump to the current track
          if ($(this).find("." + NOW_PLAYING_CLASS).length > 0) {
            mod.jumpToCurrentTrack();
          }
        }

        isInitialized = true;
        var aData, elements, i, length, temp;

        clearTimeout(mod.timeout);

        //order of importance of elements for setting the next timeout.
        elements = [
          $sbTable.find("tr." + NOW_PLAYING_CLASS),
          $sbTable
            .find("tbody")
            .find("tr.sb-future.sb-footer, tr.sb-future.sb-header")
            .filter(":first"),
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
            if (refreshInterval > maxRefreshInterval) {
              refreshInterval = maxRefreshInterval;
            }
            mod.timeout = setTimeout(function () {
              mod.refresh(aData.id);
            }, refreshInterval); //need refresh in milliseconds
            break;
          }
        }

        mod.checkToolBarIcons();

        var sid;
        if (selectedRows !== undefined) {
          selectedRows.each(function (i, el) {
            sid = $(el).attr("id");
            var tr = $("#" + sid);
            tr.addClass(SB_SELECTED_CLASS);
            tr.find("input[type='checkbox']").prop("checked", true);
          });
          mod.checkToolBarIcons();
        }
      },

      // R = ColReorder, C = ColVis
      sDom: 'R<"dt-process-rel"r><"sb-padded"<"H"C>><"dataTables_scrolling sb-padded"t><"F">',

      oColVis: {
        aiExclude: [0, 1],
        buttonText: $.i18n._("Columns"),
      },

      oColReorder: {
        iFixedColumns: 2,
      },

      sAjaxDataProp: "schedule",
      oLanguage: getDatatablesStrings({
        sZeroRecords: $.i18n._(
          "There are no shows scheduled during the specified time period.",
        ),
        sEmptyTable: $.i18n._(
          "There are no shows scheduled during the specified time period.",
        ),
      }),
      sAjaxSource: baseUrl + "showbuilder/builder-feed",

      bScrollCollapseY: false,
    });

    $sbTable
      .find("tbody")
      .on(
        "mousedown",
        "tr:not(.sb-not-allowed, .sb-header, .sb-footer, .sb-past, .sb-empty, :has(td.dataTables_empty)) > td.sb-checkbox",
        function (ev) {
          var $tr = $(this).parent(),
            // Get the ID of the selected row
            $rowId = $tr.attr("id");

          if (!$tr.hasClass(SB_SELECTED_CLASS)) {
            if (ev.shiftKey && $previouslySelected !== undefined) {
              if ($previouslySelected.attr("id") == $rowId) {
                return;
              }

              // If the selected row comes before the previously selected row,
              // we want to select previous rows, otherwise we select next
              if ($previouslySelected.prevAll("#" + $rowId).length !== 0) {
                $previouslySelected.prevUntil($tr).each(function (i, el) {
                  $(el).addClass(SB_SELECTED_CLASS);
                  $(el).find(".sb-checkbox > input").prop("checked", true);
                });
              } else {
                $previouslySelected.nextUntil($tr).each(function (i, el) {
                  $(el).addClass(SB_SELECTED_CLASS);
                  $(el).find(".sb-checkbox > input").prop("checked", true);
                });
              }
            }

            $tr.addClass(SB_SELECTED_CLASS);
            $tr.find(".sb-checkbox > input").prop("checked", true);
          } else {
            flagForDeselection = true;
          }

          selectedRows = $("." + SB_SELECTED_CLASS);
          // Remember this row so we can properly multiselect
          $previouslySelected = $tr;
        },
      );

    $sbTable
      .find("tbody")
      .on(
        "mousedown",
        "tr:not(.sb-not-allowed, .sb-header, .sb-footer, .sb-past, .sb-empty, :has(td.dataTables_empty)) > td:not(.sb-checkbox)",
        function (ev) {
          var $tr = $(this).parent(),
            // Get the ID of the selected row
            $rowId = $tr.attr("id");

          if (ev.which === 3 /* Right click */) {
            mod.selectNone();
            $tr.addClass(SB_SELECTED_CLASS);
            $tr.find(".sb-checkbox > input").prop("checked", true);
            mod.checkToolBarIcons();
            return;
          }

          if (!$tr.hasClass(SB_SELECTED_CLASS)) {
            if (ev.shiftKey && $previouslySelected !== undefined) {
              if ($previouslySelected.attr("id") == $rowId) {
                return;
              }

              // If the selected row comes before the previously selected row,
              // we want to select previous rows, otherwise we select next
              if ($previouslySelected.prevAll("#" + $rowId).length !== 0) {
                $previouslySelected.prevUntil($tr).each(function (i, el) {
                  $(el).addClass(SB_SELECTED_CLASS);
                  $(el).find(".sb-checkbox > input").prop("checked", true);
                });
              } else {
                $previouslySelected.nextUntil($tr).each(function (i, el) {
                  $(el).addClass(SB_SELECTED_CLASS);
                  $(el).find(".sb-checkbox > input").prop("checked", true);
                });
              }
            } else if (!ev.ctrlKey) {
              mod.selectNone();
            }

            $tr.addClass(SB_SELECTED_CLASS);
            $tr.find(".sb-checkbox > input").prop("checked", true);
            mod.checkToolBarIcons();
          } else if (ev.ctrlKey) {
            flagForDeselection = true;
          }

          selectedRows = $("." + SB_SELECTED_CLASS);
          // Remember this row so we can properly multiselect
          $previouslySelected = $tr;
        },
      );

    $sbTable
      .find("tbody")
      .on(
        "click",
        "tr:not(.sb-not-allowed, .sb-header, .sb-footer, .sb-past, .sb-empty, :has(td.dataTables_empty)) > td.sb-checkbox",
        function () {
          var tr = $(this).parent();
          if (flagForDeselection) {
            flagForDeselection = false;
            $previouslySelected = undefined;
            tr.removeClass(SB_SELECTED_CLASS);
            tr.find(".sb-checkbox > input").prop("checked", false);
          } else {
            tr.addClass(SB_SELECTED_CLASS);
            tr.find(".sb-checkbox > input").prop("checked", true);
          }

          mod.checkToolBarIcons();
          selectedRows = $("." + SB_SELECTED_CLASS);
        },
      );

    $sbTable
      .find("tbody")
      .on(
        "click",
        "tr:not(.sb-not-allowed, .sb-header, .sb-footer, .sb-past, .sb-empty, :has(td.dataTables_empty)) > td:not(.sb-checkbox)",
        function (e) {
          var tr = $(this).parent();
          if (flagForDeselection) {
            flagForDeselection = false;
            $previouslySelected = undefined;
            tr.removeClass(SB_SELECTED_CLASS);
            tr.find(".sb-checkbox > input").prop("checked", false);
          } else if (!(e.shiftKey || e.ctrlKey)) {
            mod.selectNone();
            tr.addClass(SB_SELECTED_CLASS);
            tr.find(".sb-checkbox > input").prop("checked", true);
            $previouslySelected = tr;
          }

          mod.checkToolBarIcons();
          selectedRows = $("." + SB_SELECTED_CLASS);
        },
      );

    //begin context menu initialization.
    $.contextMenu({
      selector: "#show_builder tr.lib-audio:not(.sb-not-allowed, .sb-past)",
      trigger: "right",

      build: function ($el, e) {
        var items,
          $tr = $el,
          data = $tr.data("aData"),
          cursorClass = "cursor-selected-row",
          callback;

        function processMenuItems(oItems) {
          //define a preview callback.
          if (oItems.preview !== undefined) {
            callback = function () {
              open_show_preview(data.instance, data.pos);
            };

            oItems.preview.callback = callback;
          }

          //define a select cursor callback.
          if (oItems.selCurs !== undefined) {
            callback = function () {
              var $tr = $(this).parents("tr").next();

              mod.selectCursor($tr);
            };

            oItems.selCurs.callback = callback;
          }

          //define a remove cursor callback.
          if (oItems.delCurs !== undefined) {
            callback = function () {
              var $tr = $(this).parents("tr").next();

              mod.removeCursor($tr);
            };

            oItems.delCurs.callback = callback;
          }

          //define a delete callback.
          if (oItems.del !== undefined) {
            callback = function () {
              AIRTIME.showbuilder.fnRemove([
                {
                  id: data.id,
                  timestamp: data.timestamp,
                  instance: data.instance,
                },
              ]);
            };

            oItems.del.callback = callback;
          }

          //only show the cursor selecting options if the library is visible on the page.
          if ($tr.next().find(".marker").length === 0) {
            delete oItems.selCurs;
            delete oItems.delCurs;
          }
          //check to include either select or remove cursor.
          else {
            if ($tr.next().hasClass(cursorClass)) {
              delete oItems.selCurs;
            } else {
              delete oItems.delCurs;
            }
          }

          items = oItems;
        }

        request = $.ajax({
          url: baseUrl + "showbuilder/context-menu",
          type: "GET",
          data: { id: data.id, format: "json" },
          dataType: "json",
          async: false,
          success: function (json) {
            processMenuItems(json.items);
          },
        });

        return {
          items: items,
        };
      },
    });

    var sortableConf = (function () {
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

      fnAdd = function () {
        var aMediaIds = [],
          aSchedIds = [];

        for (i = 0; i < aItemData.length; i++) {
          aMediaIds.push({ id: aItemData[i].id, type: aItemData[i].ftype });
        }
        aSchedIds.push({
          id: oPrevData.id,
          instance: oPrevData.instance,
          timestamp: oPrevData.timestamp,
        });

        mod.fnAdd(aMediaIds, aSchedIds);
      };

      fnMove = function () {
        var aSelect = [],
          aAfter = [];

        for (i = 0; i < helperData.length; i++) {
          aSelect.push({
            id: helperData[i].id,
            instance: helperData[i].instance,
            timestamp: helperData[i].timestamp,
          });
        }

        aAfter.push({
          id: oPrevData.id,
          instance: oPrevData.instance,
          timestamp: oPrevData.timestamp,
        });

        mod.fnMove(aSelect, aAfter);
      };

      fnReceive = function (event, ui) {
        var aItems = [];

        if (
          AIRTIME.library.getCurrentTable() == AIRTIME.library.libraryDataTable
        ) {
          AIRTIME.library.addToChosen(ui.item);
          aItems = AIRTIME.library.getSelectedData();
          origTrs = aItems;
          html = ui.helper.html();
          AIRTIME.library.removeFromChosen(ui.item);
        } else if (
          AIRTIME.library.getCurrentTable() ==
          AIRTIME.library.podcastEpisodeDataTable
        ) {
          origTrs = [$(ui.item).data("aData")];
          html = ui.helper.html();
        }
      };

      fnUpdate = function (event, ui) {
        var prev = ui.item.prev();

        //can't add items outside of shows.
        if (
          prev.find("td:first").hasClass("dataTables_empty") ||
          prev.length === 0
        ) {
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
          $sbTable.find("tr.ui-draggable").empty().after(html);

          aItemData = origTrs;
          origTrs = undefined;
          fnAdd();
        }
        //item was reordered.
        else {
          //ui.item
          //    .empty()
          //    .after(draggingContainer.html());

          aItemData.push(ui.item.data("aData"));
          fnMove();
        }
      };

      return {
        placeholder: "sb-placeholder ui-state-highlight",
        //forcePlaceholderSize: true,
        distance: 25,
        helper: function (event, item) {
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
            message = sprintf($.i18n._("Moving %s"), selected[0].title);
            //draggingContainer = item; //Default DataTables drag and drop
            draggingContainer = $("<tr/>")
              .addClass("sb-helper")
              .append("<td/>")
              .find("td")
              .attr("colspan", colspan)
              .width(width)
              .height(height)
              .addClass("ui-state-highlight")
              .append(message)
              .end();
          } else {
            message = sprintf($.i18n._("Moving %s Items"), selected.length);
            draggingContainer = $("<tr/>")
              .addClass("sb-helper")
              .append("<td/>")
              .find("td")
              .attr("colspan", colspan)
              .width(width)
              .height(height)
              .addClass("ui-state-highlight")
              .append(message)
              .end();
          }

          helperData = selected;

          return draggingContainer;
        },
        items:
          "tr:not(:first, :last, .sb-header, .sb-not-allowed, .sb-past, .sb-now-playing, .sb-empty)",
        cancel: ".sb-footer",
        receive: fnReceive,
        update: fnUpdate,
        axis: "y",
        containment: "document",
        start: function (event, ui) {
          var elements = $sbTable
            .find("tr." + SB_SELECTED_CLASS)
            .not("." + NOW_PLAYING_CLASS);
          elements.hide();
        },
        stop: function () {
          var elements = $sbTable
            .find("tr." + SB_SELECTED_CLASS)
            .not("." + NOW_PLAYING_CLASS);
          elements.show();
        },
      };
    })();

    $sbTable.sortable(sortableConf);

    //start setup of the builder toolbar.
    $toolbar = $(".sb-content .fg-toolbar:first");
    var footer = $(".sb-content .fg-toolbar:last"),
      timerange = $(".sb-timerange");
    $toolbar.append(timerange);

    $menu = $("<div class='btn-toolbar'/>");
    $menu
      .append(
        "<div class='btn-group'>" +
          "<button class='btn btn-small dropdown-toggle'  id='timeline-select' data-toggle='dropdown'>" +
          $.i18n._("Select") +
          " <span class='caret'></span>" +
          "</button>" +
          "<ul class='dropdown-menu'>" +
          "<li id='timeline-sa'><a href='#'>" +
          $.i18n._("Select all") +
          "</a></li>" +
          "<li id='timeline-sn'><a href='#'>" +
          $.i18n._("Select none") +
          "</a></li>" +
          "</ul>" +
          "</div>",
      )
      .append(
        "<div class='btn-group'>" +
          "<button title='" +
          $.i18n._("Trim overbooked shows") +
          "' class='ui-state-disabled btn btn-small btn-icon-text' disabled='disabled'>" +
          "<i class='icon-white icon-cut'></i>" +
          "<span>" +
          $.i18n._("Trim overbooked shows") +
          "</span>" +
          "</button></div>",
      )
      .append(
        "<div class='btn-group'>" +
          "<button title='" +
          $.i18n._("Remove selected scheduled items") +
          "' class='ui-state-disabled btn btn-small btn-icon-text btn-danger' disabled='disabled'>" +
          "<i class='icon-white icon-trash'></i>" +
          "<span>" +
          $.i18n._("Remove") +
          "</span>" +
          "</button></div>",
      );

    //if 'Add/Remove content' was chosen from the context menu
    //in the Calendar do not append these buttons
    if ($(".ui-dialog-content").length === 0) {
      $menu
        .append(
          "<div class='btn-group'>" +
            "<button  title='" +
            $.i18n._("Jump to the current playing track") +
            "' class='ui-state-disabled btn btn-small btn-icon-text' disabled='disabled'>" +
            "<i class='icon-white icon-step-forward'></i>" +
            "<span>" +
            $.i18n._("Jump to Current") +
            "</span>" +
            "</button></div>",
        )
        .append(
          "<div class='btn-group'>" +
            "<button title='" +
            $.i18n._("Cancel current show") +
            "' class='ui-state-disabled btn btn-small btn-icon-text btn-danger' disabled='disabled'>" +
            "<i class='icon-white icon-ban-circle'></i>" +
            "<span>" +
            $.i18n._("Cancel current show") +
            "</span>" +
            "</button></div>",
        );
    }

    if (localStorage.getItem("user-type") != "G") {
      $toolbar.append($menu);
    }

    $menu = undefined;

    $("#timeline-sa").click(function () {
      mod.selectAll();
    });
    $("#timeline-sn").click(function () {
      mod.selectNone();
    });

    //cancel current show
    $toolbar
      .find(".icon-ban-circle")
      .parent()
      .click(function () {
        var $tr,
          data,
          msg = $.i18n._("Cancel Current Show?");

        if (AIRTIME.button.isDisabled("icon-ban-circle", true) === true) {
          return;
        }

        $tr = $sbTable.find("tr.sb-future:first");

        if ($tr.hasClass("sb-current-show")) {
          data = $tr.data("aData");

          if (data.record === true) {
            msg = $.i18n._("Stop recording current show?");
          }

          if (confirm(msg)) {
            var url = baseUrl + "Schedule/cancel-current-show";
            $.ajax({
              url: url,
              data: { format: "json", id: data.instance },
              success: function (data) {
                $("#library_content")
                  .find("#library_display")
                  .dataTable()
                  .fnStandingRedraw();
                var oTable = $sbTable.dataTable();
                oTable.fnDraw();
              },
            });
          }
        }
      });

    //jump to current
    $toolbar
      .find(".icon-step-forward")
      .parent()
      .click(function () {
        if (AIRTIME.button.isDisabled("icon-step-forward", true) === true) {
          return;
        }
        /*
                 var $scroll = $sbContent.find(".dataTables_scrolling"),
                 scrolled = $scroll.scrollTop(),
                 scrollingTop = $scroll.offset().top,
                 current = $sbTable.find("."+NOW_PLAYING_CLASS),
                 currentTop = current.offset().top;

                 $scroll.scrollTop(currentTop - scrollingTop + scrolled);
                 */
        mod.jumpToCurrentTrack();
      });

    //delete overbooked tracks.
    $toolbar
      .find(".icon-cut", true)
      .parent()
      .click(function () {
        if (AIRTIME.button.isDisabled("icon-cut", true) === true) {
          return;
        }

        var temp,
          aItems = [],
          trs = $sbTable.find(".sb-over.sb-future.sb-allowed");

        trs.each(function () {
          temp = $(this).data("aData");
          aItems.push({
            id: temp.id,
            instance: temp.instance,
            timestamp: temp.timestamp,
          });
        });

        mod.fnRemove(aItems);
      });

    //delete selected tracks
    $toolbar
      .find(".icon-trash")
      .parent()
      .click(function () {
        var button = $("#show_builder").find(".icon-trash").parent();

        if (button.hasClass(DISABLED_CLASS)) {
          return;
        }

        mod.fnRemoveSelectedItems();
      });

    //add events to cursors.
    $sbTable.find("tbody").on("click", "div.marker", function (event) {
      var $tr = $(this).parents("tr"),
        $trs;

      if ($tr.hasClass(CURSOR_SELECTED_CLASS)) {
        mod.removeCursor($tr);
      } else {
        mod.selectCursor($tr);
      }

      if (event.ctrlKey === false) {
        $trs = $sbTable.find("." + CURSOR_SELECTED_CLASS).not($tr);
        mod.removeCursor($trs);
      }

      return false;
    });

    /*
     * Select button dropdown state in the toolbar.
     * The button has to be disabled to prevent the dropdown
     * from opening
     */
    $sbContent.on("mouseenter", ".btn-group #timeline-select", function (ev) {
      $el = $(this);

      if ($el.hasClass("ui-state-disabled")) {
        $el.attr("disabled", "disabled");
      } else {
        $el.removeAttr("disabled");
      }
    });
  };

  return AIRTIME;
})(AIRTIME || {});
