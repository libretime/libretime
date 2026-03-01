/**
 * Created by asantoni on 11/09/15.
 */

var AIRTIME = (function (AIRTIME) {
  //Module initialization
  if (AIRTIME.widgets === undefined) {
    AIRTIME.widgets = {};
  }

  //Table widget constructor
  /**
   *
   *
   * @param wrapperDOMNode
   * @param {boolean} bItemSelection
   * @param {Object} toolbarButtons
   * @param {Object} dataTablesOptions
   * @param {Object} [emptyPlaceholder]
   * @param {string} emptyPlaceholder.html
   * @param {string} emptyPlaceholder.iconClass
   *
   * @returns {Table}
   * @constructor
   */
  var Table = function (
    wrapperDOMNode,
    bItemSelection,
    toolbarButtons,
    dataTablesOptions,
    emptyPlaceholder,
  ) {
    var self = this;

    self.HUGE_INT = Math.pow(2, 53) - 1;

    //Constants and enumerations
    self.SELECTION_MODE = {
      SINGLE: 0,
      MULTI_SHIFT: 1,
      MULTI_CTRL: 2,
    };

    //Member variables
    self._datatable = null;
    self._selectedRows = []; //An array containing the underlying objects for each selected row. (Easy to use!)
    //self._selectedRowVisualIdxMap = []; //A map of the visual index of a selected rows onto the actual row data.
    self._selectedRowVisualIdxMin = self.HUGE_INT;
    self._selectedRowVisualIdxMax = -1;
    self._$wrapperDOMNode = null;
    self._toolbarButtons = null;
    //Save some of the constructor parameters
    self._$wrapperDOMNode = $(wrapperDOMNode);
    self._toolbarButtons = toolbarButtons;
    self._emptyPlaceholder = emptyPlaceholder;

    // Exclude the leftmost column if we're implementing item selection
    self._colVisExcludeColumns = bItemSelection ? [0] : [];

    //Finish initialization of the datatable since everything is declared by now.

    // If selection is enabled, add in the checkbox column.
    if (bItemSelection) {
      dataTablesOptions["aoColumns"].unshift(
        /* Checkbox */ {
          sTitle: "",
          mData: self._datatablesCheckboxDataDelegate.bind(this),
          bSortable: false,
          bSearchable: false,
          sWidth: "24px",
          sClass: "airtime_table_checkbox",
        },
      );
    }

    var options = {
      aoColumns: [
        /* Title */ {
          sTitle: $.i18n._("Make sure to override me"),
          mDataProp: "track_title",
          sClass: "library_title",
          sWidth: "170px",
        },
      ],
      bProcessing: true,
      bServerSide: true,
      sAjaxSource: baseUrl + "rest/media", //Override me
      sAjaxDataProp: "aaData",
      bScrollCollapse: false,
      deferLoading: 1, //0 tells it there's zero elements loaded and disables the automatic AJAX. We don't want to load until after we bind all our event handlers, to prevent a race condition with the "init" event callback.
      sPaginationType: "full_numbers",
      bJQueryUI: true,
      bAutoWidth: false,
      aaSorting: [],
      iDisplayLength: 25,
      aLengthMenu: [25, 50, 100],
      oLanguage: getDatatablesStrings({
        sEmptyTable: $.i18n._(""),
        sZeroRecords: $.i18n._("No matching results found."),
      }),
      oColVis: {
        sAlign: "right",
        aiExclude: self._colVisExcludeColumns,
        buttonText: $.i18n._("Columns"),
        iOverlayFade: 0,
      },
      // z = ColResize, R = ColReorder, C = ColVis
      sDom: 'Rf<"dt-process-rel"r><"H"<"table_toolbar"C>><"dataTables_scrolling"t<".empty_placeholder"<".empty_placeholder_image"><".empty_placeholder_text">>><"F"lip>>',

      fnPreDrawCallback: function () {
        $("#draggingContainer").remove();
      },
      fnServerData: self._fetchData.bind(self),
      //"fnInitComplete" : function() { self._setupEventHandlers(bItemSelection) }
      fnDrawCallback: function () {
        self.clearSelection();
      },
    };

    //Override any options with those passed in as arguments to this constructor.
    for (var key in dataTablesOptions) {
      options[key] = dataTablesOptions[key];
    }

    if (options.fnCreatedRow) {
      options.fnCreatedRow = options.fnCreatedRow.bind(self);
    }
    if (options.fnDrawCallback) {
      options.fnDrawCallback = options.fnDrawCallback.bind(self);
    }

    self._datatable = self._$wrapperDOMNode.dataTable(options);
    // self._datatable.fnDraw(); //Load the AJAX data now that our event handlers have been bound.
    self._setupEventHandlers(bItemSelection);

    //return self._datatable;
    return self;
  };

  Table.prototype.assignDblClickHandler = function (fn) {
    $(this._datatable, "tbody tr").on(
      "dblclick",
      this._SELECTORS.SELECTION_TABLE_ROW,
      fn,
    );
  };

  /* Set up global event handlers for the datatable.
   *  @param bItemSelection Whether or not row selection behaviour should be enabled for this widget.
   * */
  Table.prototype._setupEventHandlers = function (bItemSelection) {
    var self = this;

    /** This table row event handler is created once and catches events for any row. (It's less resource intensive
     *  than having a per-row callback...)
     */
    if (bItemSelection) {
      $(self._datatable, "tbody tr").on(
        "click contextmenu",
        self._SELECTORS.SELECTION_TABLE_ROW,
        function (e) {
          var aData = self._datatable.fnGetData(this);
          var iDisplayIndex = $(this).index(); // The index of the row in the current page in the table.
          var nRow = this;

          e.stopPropagation();
          e.preventDefault();
          document.getSelection().removeAllRanges();

          var selectionMode = self.SELECTION_MODE.SINGLE;
          if (e.shiftKey) {
            selectionMode = self.SELECTION_MODE.MULTI_SHIFT;
          } else if (e.ctrlKey) {
            selectionMode = self.SELECTION_MODE.MULTI_CTRL;
          }

          if (e.button == 2) {
            selectionMode = self.SELECTION_MODE.SINGLE;
          }
          self.selectRow(nRow, aData, selectionMode, iDisplayIndex);
        },
      );

      $(self._datatable, "tbody tr").on(
        "click",
        self._SELECTORS.SELECTION_CHECKBOX,
        function (e) {
          $this = $(this);

          var iVisualRowIdx = $this.parent().index();
          var aData = self._datatable.fnGetData(iVisualRowIdx);
          var selectionMode = self.SELECTION_MODE.MULTI_CTRL; //Behaviour for checkboxes.
          if (e.shiftKey) {
            selectionMode = self.SELECTION_MODE.MULTI_SHIFT;
          }
          self.selectRow($this.parent(), aData, selectionMode, iVisualRowIdx); //Always multiselect for checkboxes
          e.stopPropagation();
          return true;
        },
      );

      // Clear selection when switching pages
      $(self._datatable).on("page", function () {
        self.clearSelection();
      });
    }

    // On filter, display the number of total and filtered results in the search bar
    $(self._datatable).on("filter", function () {
      var dt = self._datatable,
        f = dt.closest(".dataTables_wrapper").find(".filter-message"),
        totalRecords = dt.fnSettings().fnRecordsTotal(),
        totalDisplayRecords = dt.fnSettings().fnRecordsDisplay();

      if (f.length === 0) {
        var el = document.createElement("span");
        el.setAttribute("class", "filter-message");
        f = dt
          .closest(".dataTables_wrapper")
          .find(".dataTables_filter")
          .append(el)
          .find(".filter-message");
      }

      f.text(
        totalRecords > totalDisplayRecords
          ? $.i18n._("Filtering out ") +
              (totalRecords - totalDisplayRecords) +
              $.i18n._(" of ") +
              totalRecords +
              $.i18n._(" records")
          : "",
      );

      dt.closest(".dataTables_wrapper")
        .find('.dataTables_filter input[type="text"]')
        .css("padding-right", f.outerWidth());
    });

    //Since this function is already called when the datatables initialization is complete, we know the DOM
    //structure for the datatable exists and can just proceed to setup the toolbar DOM elements now.
    self._setupToolbarButtons(self._toolbarButtons);
  };

  /**
   * Member functions
   *
   */

  /** Populate the toolbar with buttons.
   *
   * @param buttons A list of objects which contain button definitions. See self.TOOLBAR_BUTTON_ROLES for an example, or use getStandardToolbarButtons() to get a list of them.
   * @private
   */
  Table.prototype._setupToolbarButtons = function (buttons) {
    var self = this;
    var $menu = self._$wrapperDOMNode
      .parent()
      .parent()
      .find("div.table_toolbar");
    $menu.addClass("btn-toolbar");

    //Create the toolbar buttons.
    $.each(buttons, function (idx, btn) {
      var buttonElement = self._createToolbarButton(
        btn.title,
        btn.iconClass,
        btn.extraBtnClass,
        btn.elementId,
      );
      $menu.append(buttonElement);
      btn.element = buttonElement; //Save this guy in case you need it later.
      //Bind event handlers to each button
      $.each(btn.eventHandlers, function (eventName, eventCallback) {
        $(buttonElement).on(eventName, function () {
          if ($(buttonElement).find("button").is(":disabled")) {
            return;
          }
          eventCallback();
        });
      });
    });

    self._checkToolbarButtons();
  };

  /**
   * Check each of the toolbar buttons for the table and disable them if their constraints are invalid.
   *
   * Passes current Table object context to function calls.
   */
  Table.prototype._checkToolbarButtons = function () {
    var self = this;
    $.each(self._toolbarButtons, function (idx, btn) {
      var btnNode = $(btn.element).find("button").get(0);
      btnNode.disabled = btn.disabled = !btn.validateConstraints.call(self);
    });
  };

  /** Create the DOM element for a toolbar button and return it. */
  Table.prototype._createToolbarButton = function (
    title,
    iconClass,
    extraBtnClass,
    elementId,
  ) {
    if (!iconClass) {
      iconClass = "icon-plus";
    }

    // var title = $.i18n._('Delete');
    var outerDiv = document.createElement("div");
    outerDiv.className = "btn-group";
    outerDiv.title = title;
    var innerButton = document.createElement("button");
    //innerButton.className = 'btn btn-small ' + extraBtnClass;
    innerButton.className = "btn " + extraBtnClass;
    innerButton.id = elementId;
    var innerIcon = document.createElement("i");
    innerIcon.className = "icon-white " + iconClass;
    var innerTextSpan = document.createElement("span");
    var innerText = document.createTextNode(title);
    innerTextSpan.appendChild(innerText);
    innerButton.appendChild(innerIcon);
    innerButton.appendChild(innerTextSpan);
    outerDiv.appendChild(innerButton);

    /* Here's an example of what the button HTML should look like:
         "<div class='btn-group' title=" + $.i18n._('Delete') + ">" +
         "<button class='btn btn-small btn-danger' id='sb-trash'>" +
         "<i class='icon-white icon-trash'></i>" +
         "<span>" + $.i18n._('Delete') + "</span>" +
         "</button>" +
         "</div>"*/
    return outerDiv;
  };

  Table.prototype.clearSelection = function () {
    this._selectedRows = [];
    //self._selectedRowVisualIdxMap = [];
    this._selectedRowVisualIdxMin = self.HUGE_INT;
    this._selectedRowVisualIdxMax = -1;
    this._$wrapperDOMNode.find(".selected").removeClass("selected");
    this._$wrapperDOMNode
      .find(this._SELECTORS.SELECTION_CHECKBOX)
      .find("input")
      .attr("checked", false);
    this._checkToolbarButtons();
  };

  /** @param nRow is a tr DOM node (non-jQuery)
   * @param aData is an array containing the raw data for the row. Can be null if you don't have it.
   * @param selectionMode is an SELECT_MODE enum. Specify what selection mode you want to use for this action.
   * @param iVisualRowIdx is an integer which corresponds to the index of the clicked row, as it appears to the user.
   *             eg. The 5th row in the table will have an iVisualRowIdx of 4 (0-based).
   */
  Table.prototype.selectRow = function (
    nRow,
    aData,
    selectionMode,
    iVisualRowIdx,
  ) {
    var self = this;

    //Default to single item selection.
    if (selectionMode == undefined) {
      selectionMode = self.SELECTION_MODE.SINGLE;
    }

    var $nRow = $(nRow);

    //Regular single left-click mode
    if (selectionMode == self.SELECTION_MODE.SINGLE) {
      self.clearSelection();

      self._selectedRows.push(aData);
      self._selectedRowVisualIdxMin = iVisualRowIdx;
      self._selectedRowVisualIdxMax = iVisualRowIdx;
      //self._selectedRowVisualIdxMap[iVisualRowIdx] = aData;

      $nRow.addClass("selected");
      $nRow
        .find(self._SELECTORS.SELECTION_CHECKBOX)
        .find("input")
        .attr("checked", true);
    }
    //Ctrl-click multi row selection mode
    else if (selectionMode == self.SELECTION_MODE.MULTI_CTRL) {
      var foundAtIdx = $.inArray(aData, self._selectedRows);

      //console.log('checkbox mouse', iVisualRowIdx, foundAtIdx);

      //If the clicked row is already selected, deselect it.
      if (foundAtIdx >= 0 && self._selectedRows.length >= 1) {
        self._selectedRows.splice(foundAtIdx, 1);
        $nRow.removeClass("selected");
        $nRow
          .find(self._SELECTORS.SELECTION_CHECKBOX)
          .find("input")
          .attr("checked", false);
      } else {
        self._selectedRows.push(aData);

        self._selectedRowVisualIdxMin = iVisualRowIdx;
        self._selectedRowVisualIdxMax = iVisualRowIdx;

        $nRow.addClass("selected");
        $nRow
          .find(self._SELECTORS.SELECTION_CHECKBOX)
          .find("input")
          .attr("checked", true);
      }
    }
    //Shift-click multi row selection mode
    else if (selectionMode == self.SELECTION_MODE.MULTI_SHIFT) {
      //If there's no rows selected, just behave like single selection.
      if (self._selectedRows.length == 0) {
        return self.selectRow(
          nRow,
          aData,
          self.SELECTION_MODE.SINGLE,
          iVisualRowIdx,
        );
      }

      if (iVisualRowIdx > self._selectedRowVisualIdxMax) {
        self._selectedRowVisualIdxMax = iVisualRowIdx;
      }
      if (iVisualRowIdx < self._selectedRowVisualIdxMin) {
        self._selectedRowVisualIdxMin = iVisualRowIdx;
      }

      var selectionStartRowIdx = Math.min(
        iVisualRowIdx,
        self._selectedRowVisualIdxMin,
      );
      var selectionEndRowIdx = Math.min(
        iVisualRowIdx,
        self._selectedRowVisualIdxMax,
      );

      //We can assume there's at least 1 row already selected now.
      var allRows = self._datatable.fnGetData();

      self._selectedRows = [];
      for (
        var i = self._selectedRowVisualIdxMin;
        i <= self._selectedRowVisualIdxMax;
        i++
      ) {
        self._selectedRows.push(allRows[i]);
        $row = $($nRow.parent().children()[i]);
        $row.addClass("selected");
        $row
          .find(self._SELECTORS.SELECTION_CHECKBOX)
          .find("input")
          .attr("checked", true);
      }
    } else {
      console.log("Unimplemented selection mode");
    }

    self._checkToolbarButtons();
  };

  Table.prototype.getSelectedRows = function () {
    return this._selectedRows;
  };

  Table.prototype.getEmptyPlaceholder = function () {
    return this._emptyPlaceholder;
  };

  Table.prototype._handleAjaxError = function (r) {
    // If the request was denied due to permissioning
    if (r.status === 403) {
      $(".dt-process-rel").hide();
      $(".empty_placeholder_text").text(
        $.i18n._("You don't have permission to view this resource."),
      );
      $(".empty_placeholder").show();
    }
  };

  /** Grab data from a REST API and format so that DataTables can display it.
   *  This is the DataTables REST adapter function, basically.
   * */
  Table.prototype._fetchData = function (
    sSource,
    aoData,
    fnCallback,
    oSettings,
  ) {
    var self = this;
    var echo = aoData[0].value; //Datatables state tracking. Must be included.

    var sortColName = "";
    var sortDir = "";
    var search = self._$wrapperDOMNode
      .closest(".dataTables_wrapper")
      .find(".dataTables_filter")
      .find("input")
      .val();
    if (oSettings.aaSorting.length > 0) {
      var sortColIdx = oSettings.aaSorting[0][0];
      sortColName = oSettings.aoColumns[sortColIdx].mDataProp;
      sortDir = oSettings.aaSorting[0][1].toUpperCase();
    }

    // FIXME: We should probably just be sending aoData back here..?
    $.ajax({
      dataType: "json",
      type: "GET",
      url: sSource,
      data: {
        limit: oSettings._iDisplayLength,
        offset: oSettings._iDisplayStart,
        sort: sortColName,
        sort_dir: sortDir,
        search: search,
      },
      success: function (json, textStatus, jqXHR) {
        var rawResponseJSON = json;
        json = [];
        json.aaData = rawResponseJSON;
        json.iTotalRecords = jqXHR.getResponseHeader("X-TOTAL-COUNT");
        json.iTotalDisplayRecords = json.iTotalRecords;
        json.sEcho = echo;

        //Pass it along to datatables.
        fnCallback(json);
      },
      error: self._handleAjaxError,
    });
  };

  Table.prototype._datatablesCheckboxDataDelegate = function (
    rowData,
    callType,
    dataToSave,
  ) {
    if (callType == undefined) {
      //Supposed to return the raw data for the type here.
      return null;
    } else if (callType == "display") {
      return "<input type='checkbox'>";
    } else if (callType == "sort") {
      return null;
    } else if (callType == "type") {
      return "input";
    } else if (callType == "set") {
      //The data to set is in dataToSave.
      return;
    } else if (callType == "filter") {
      return null;
    }

    //For all other calls, just return the data as this:
    return "check";
  };

  //Accessors / Mutators

  Table.prototype.getDatatable = function () {
    return this._datatable;
  };

  //Static initializers / Class variables

  Table.prototype._SELECTORS = Object.freeze({
    SELECTION_CHECKBOX: ".airtime_table_checkbox",
    SELECTION_TABLE_ROW: "tr",
  });

  Table.TOOLBAR_BUTTON_ROLES = {
    NEW: 0,
    EDIT: 1,
    DELETE: 2,
  };
  Object.freeze(Table.TOOLBAR_BUTTON_ROLES);

  //Set of standard buttons. Use getStandardToolbarButtons() to grab these and pass them to the init() function.
  Table._STANDARD_TOOLBAR_BUTTONS = {};
  Table._STANDARD_TOOLBAR_BUTTONS[Table.TOOLBAR_BUTTON_ROLES.NEW] = {
    title: $.i18n._("New"),
    iconClass: "icon-plus",
    extraBtnClass: "btn-small btn-new",
    elementId: "",
    eventHandlers: {},
    validateConstraints: function () {
      return true;
    },
  };
  Table._STANDARD_TOOLBAR_BUTTONS[Table.TOOLBAR_BUTTON_ROLES.EDIT] = {
    title: $.i18n._("Edit"),
    iconClass: "icon-pencil",
    extraBtnClass: "btn-small",
    elementId: "",
    eventHandlers: {},
    validateConstraints: function () {
      return true;
    },
  };
  Table._STANDARD_TOOLBAR_BUTTONS[Table.TOOLBAR_BUTTON_ROLES.DELETE] = {
    title: $.i18n._("Delete"),
    iconClass: "icon-trash",
    extraBtnClass: "btn-small btn-danger",
    elementId: "",
    eventHandlers: {},
    validateConstraints: function () {
      return true;
    },
  };
  Object.freeze(Table._STANDARD_TOOLBAR_BUTTONS);

  //Static method
  Table.getStandardToolbarButtons = function () {
    //Return a deep copy
    return jQuery.extend(true, {}, Table._STANDARD_TOOLBAR_BUTTONS);
  };

  //Add Table to the widgets namespace
  AIRTIME.widgets.Table = Table;

  return AIRTIME;
})(AIRTIME || {});
