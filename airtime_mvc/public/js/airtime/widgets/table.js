/**
 * Created by asantoni on 11/09/15.
 */

var AIRTIME = (function(AIRTIME) {

    //Module initialization
    if (AIRTIME.widgets === undefined) {
        AIRTIME.widgets = {};
    }
    if (AIRTIME.widgets.table === undefined) {
        AIRTIME.widgets.table = {};
    }

    var self;
    var self = AIRTIME.widgets.table;

    //TODO: Wrap everything into the prototype


    //Constants and enumerations
    self.SELECTION_MODE = {
        SINGLE : 0,
        MULTI_SHIFT : 1,
        MULTI_CTRL : 2
    }

    self.HUGE_INT = Math.pow(2, 53) - 1;

    /** Predefined toolbar buttons that you can add to the table. Use getStandardToolbarButtons(). */
    self.TOOLBAR_BUTTON_ROLES = {
        NEW :    0,
        EDIT :   1,
        DELETE : 2
    };
    Object.freeze(self.TOOLBAR_BUTTON_ROLES);

    //Set of standard buttons. Use getStandardToolbarButtons() to grab these and pass them to the init() function.
    self._STANDARD_TOOLBAR_BUTTONS = {};
    self._STANDARD_TOOLBAR_BUTTONS[self.TOOLBAR_BUTTON_ROLES.NEW] = { 'title' : $.i18n._('New'), 'iconClass' : "icon-plus", extraBtnClass : "", elementId : 'sb-new', eventHandlers : {} };
    self._STANDARD_TOOLBAR_BUTTONS[self.TOOLBAR_BUTTON_ROLES.EDIT] = { 'title' : $.i18n._('Edit'), 'iconClass' : "icon-pencil", extraBtnClass : "", elementId : 'sb-edit', eventHandlers : {} };
    self._STANDARD_TOOLBAR_BUTTONS[self.TOOLBAR_BUTTON_ROLES.DELETE] = { 'title' : $.i18n._('Delete'), 'iconClass' : "icon-trash", extraBtnClass : "btn-danger", elementId : 'sb-trash', eventHandlers : {} };
    Object.freeze(self._STANDARD_TOOLBAR_BUTTONS);

    //Member variables
    self._datatable = null;
    self._selectedRows = []; //An array containing the underlying objects for each selected row. (Easy to use!)
    //self._selectedRowVisualIdxMap = []; //A map of the visual index of a selected rows onto the actual row data.
    self._selectedRowVisualIdxMin = self.HUGE_INT;
    self._selectedRowVisualIdxMax = -1;
    self._$wrapperDOMNode = null;
    self._toolbarButtons = null;


    //Member functions
    self.init = function(wrapperDOMNode, bItemSelection, toolbarButtons, dataTablesOptions) {
        self._$wrapperDOMNode = $(wrapperDOMNode);

        self._toolbarButtons = toolbarButtons;

        // If selection is enabled, add in the checkbox column.
        if (bItemSelection) {
            dataTablesOptions["aoColumns"].unshift(
                /* Checkbox */        { "sTitle" : "", "mData" : self._datatablesCheckboxDataDelegate, "bSortable"   : false                 , "bSearchable" : false                   , "sWidth" : "16px"         , "sClass" : "library_checkbox" }
            );
        }

        var options = {
            "aoColumns": [
                /* Title */           { "sTitle" : $.i18n._("Make sure to override me")              , "mDataProp" : "track_title"  , "sClass"      : "library_title"       , "sWidth"      : "170px"                 },
            ],
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": baseUrl+"rest/media", //Override me
            "sAjaxDataProp": "aaData",
            "bScrollCollapse": false,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "aaSorting": [],
            "oLanguage" : getDatatablesStrings({
                "sEmptyTable": $.i18n._(""),
                "sZeroRecords": $.i18n._("No matching results found.")
            }),
            "oColVis": {
                "sAlign": "right",
                "buttonText": $.i18n._("Columns"),
                "iOverlayFade": 0
            },
            // z = ColResize, R = ColReorder, C = ColVis
            "sDom": 'Rf<"dt-process-rel"r><"H"<"table_toolbar"C>><"dataTables_scrolling"t<"#library_empty"<"#library_empty_image"><"#library_empty_text">>><"F"lip>>',

            "fnServerData": self._fetchData,
            //"fnDrawCallback" : self._tableDrawCallback
        };

        //Override any options with those passed in as arguments to this constructor.
        for (var key in dataTablesOptions)
        {
            options[key] = dataTablesOptions[key];
        }

        self._datatable = self._$wrapperDOMNode.dataTable(options);

        self._setupEventHandlers(bItemSelection);

        return self._datatable;
    };

    self._handleAjaxError = function(r) {
        // If the request was denied due to permissioning
        if (r.status === 403) {
            // Hide the processing div
            /*
             $("#library_display_wrapper").find(".dt-process-rel").hide();
             $.getJSON( "ajax/library_placeholders.json", function( data ) {
             $('#library_empty_text').text($.i18n._(data.unauthorized));
             })  ;

             $('#library_empty').show();
             */
        }
    };

    //
    self._fetchData = function ( sSource, aoData, fnCallback, oSettings ) {

        var echo = aoData[0].value; //Datatables state tracking. Must be included.

        var sortColName = "";
        var sortDir = "";
        if (oSettings.aaSorting.length > 0) {
            var sortColIdx = oSettings.aaSorting[0][0];
            sortColName = oSettings.aoColumns[sortColIdx].mDataProp;
            sortDir = oSettings.aaSorting[0][1].toUpperCase();
        }

        $.ajax({
            "dataType": 'json',
            "type": "GET",
            "url": sSource,
            "data": {
                "limit": oSettings._iDisplayLength,
                "offset": oSettings._iDisplayStart,
                "sort": sortColName,
                'sort_dir': sortDir,
            },
            "success": function (json, textStatus, jqXHR) {
                var rawResponseJSON = json;
                json = [];
                json.aaData = rawResponseJSON;
                json.iTotalRecords = jqXHR.getResponseHeader('X-TOTAL-COUNT');
                json.iTotalDisplayRecords = json.iTotalRecords;
                json.sEcho = echo;

                //Pass it along to datatables.
                fnCallback(json);
            },
            "error": self._handleAjaxError
        }).done(function (data) {
            /*
             if (data.iTotalRecords > data.iTotalDisplayRecords) {
             $('#filter_message').text(
             $.i18n._("Filtering out ") + (data.iTotalRecords - data.iTotalDisplayRecords)
             + $.i18n._(" of ") + data.iTotalRecords
             + $.i18n._(" records")
             );
             $('#library_empty').hide();
             $('#library_display').find('tr:has(td.dataTables_empty)').show();
             } else {
             $('#filter_message').text("");
             }
             $('#library_content').find('.dataTables_filter input[type="text"]')
             .css('padding-right', $('#advanced-options').find('button').outerWidth());
             */
        });
    };

    self._datatablesCheckboxDataDelegate = function(rowData, callType, dataToSave) {


        if (callType == undefined) {
            //Supposed to return the raw data for the type here.
            return null;
        } else if (callType == 'display') {
            return "<input type='checkbox' class='airtime_table_checkbox'>";
        } else if (callType == 'sort') {
            return null;
        } else if (callType == 'type') {
            return "input";
        } else if (callType == 'set') {
            //The data to set is in dataToSave.
            return;
        } else if (callType == 'filter') {
            return null;
        }

        //For all other calls, just return the data as this:
        return "check";
    };

    /*
    self._rowCreatedCallback = function(nRow, aData, iDisplayIndex) {

        return nRow;

    };*/

    /*
    self._tableDrawCallback = function(oSettings) {


    };*/


    /* Set up global event handlers for the datatable.
    *  @param bItemSelection Whether or not row selection behaviour should be enabled for this widget.
    * */
    self._setupEventHandlers = function(bItemSelection) {

        /** This table row event handler is created once and catches events for any row. (It's less resource intensive
         *  than having a per-row callback...)
         */
        if (bItemSelection) {
            $(self._datatable, 'tbody tr').on('click contextmenu', 'tr', function (e) {
                var aData = $(this).data(); //Neat trick - thanks DataTables!
                var iDisplayIndex = $(this).index(); //The index of the row in the current page in the table.
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
            });

            $(self._datatable, 'tbody tr').on('click', 'input.airtime_table_checkbox', function(e) {
                $this = $(this);

                var iVisualRowIdx = $this.parent().parent().index();
                var aData = $this.parent().parent().data();
                var selectionMode = self.SELECTION_MODE.MULTI_CTRL; //Behaviour for checkboxes.
                if (e.shiftKey) {
                    selectionMode = self.SELECTION_MODE.MULTI_SHIFT;
                }
                self.selectRow($this.parent().parent(), aData, selectionMode, iVisualRowIdx); //Always multiselect for checkboxes
                e.stopPropagation();
                return true;
            });
        }

        $(self._datatable).on('init', function(e) {
            self._setupToolbarButtons(self._toolbarButtons);
        });
    }

    self.getStandardToolbarButtons = function() {

        //Return a deep copy
        return jQuery.extend(true, {}, self._STANDARD_TOOLBAR_BUTTONS);
    };

    /** Populate the toolbar with buttons.
     *
     * @param buttons A list of objects which contain button definitions. See self.TOOLBAR_BUTTON_ROLES for an example, or use getStandardToolbarButtons() to get a list of them.
     * @private
     */
    self._setupToolbarButtons = function(buttons) {
        var $menu = self._$wrapperDOMNode.parent().parent().find("div.table_toolbar");
        $menu.addClass("btn-toolbar");

        $.each(buttons, function(idx, btn) {
            console.log(btn.eventHandlers);

            var buttonElement = self._createToolbarButton(btn.title, btn.iconClass, btn.extraBtnClass, btn.elementId);
            $menu.append(buttonElement);
            btn.element = buttonElement; //Save this guy in case you need it later.
            $.each(btn.eventHandlers, function(eventName, eventCallback) {
                console.log(eventName, eventCallback);
                $(buttonElement).on(eventName, eventCallback);
            });
        });

        //$menu.append(self._createToolbarButton($.i18n._('Delete'), "icon-trash", "btn-danger", 'sb-trash'));


        /*
        if (bIncludeDefaultActions)
        {
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

            $menu.append(
                "<div class='btn-group' title=" + $.i18n._('Delete') + ">" +
                "<button class='btn btn-small btn-danger' id='sb-trash'>" +
                "<i class='icon-white icon-trash'></i>" +
                "<span>" + $.i18n._('Delete') + "</span>" +
                "</button>" +
                "</div>"
            );
        }*/
    };

    /** Create the DOM element for a toolbar button and return it. */
    self._createToolbarButton = function(title, iconClass, extraBtnClass, elementId) {

        if (!iconClass) {
            iconClass = 'icon-plus';
        }

       // var title = $.i18n._('Delete');
        var outerDiv = document.createElement("div");
        outerDiv.className = 'btn-group';
        outerDiv.title = title;
        var innerButton = document.createElement("button");
        innerButton.className = 'btn btn-small ' + extraBtnClass;
        innerButton.id = elementId;
        var innerIcon = document.createElement("i");
        innerIcon.className = 'icon-white ' + iconClass;
        var innerTextSpan = document.createElement('span');
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

    self._clearSelection = function() {
        self._selectedRows = [];
        //self._selectedRowVisualIdxMap = [];
        self._selectedRowVisualIdxMin = self.HUGE_INT;
        self._selectedRowVisualIdxMax = -1;
        self._$wrapperDOMNode.find('.selected').removeClass('selected');
        self._$wrapperDOMNode.find('input.airtime_table_checkbox').attr('checked', false);
    };

    /** @param nRow is a tr DOM node (non-jQuery)
     * @param aData is an array containing the raw data for the row. Can be null if you don't have it.
     * @param selectionMode is an SELECT_MODE enum. Specify what selection mode you want to use for this action.
     * @param iVisualRowIdx is an integer which corresponds to the index of the clicked row, as it appears to the user.
     *             eg. The 5th row in the table will have an iVisualRowIdx of 4 (0-based).
     */
    self.selectRow = function(nRow, aData, selectionMode, iVisualRowIdx) {

        //Default to single item selection.
        if (selectionMode == undefined) {
            selectionMode = self.SELECTION_MODE.SINGLE;
        }

        var $nRow = $(nRow);

        //Regular single left-click mode
        if (selectionMode == self.SELECTION_MODE.SINGLE) {

            self._clearSelection();

            self._selectedRows.push(aData);
            self._selectedRowVisualIdxMin = iVisualRowIdx;
            self._selectedRowVisualIdxMax = iVisualRowIdx;
            //self._selectedRowVisualIdxMap[iVisualRowIdx] = aData;

            $nRow.addClass('selected');
            $nRow.find('input.airtime_table_checkbox').attr('checked', true);
        }
        //Ctrl-click multi row selection mode
        else if (selectionMode == self.SELECTION_MODE.MULTI_CTRL) {

            var foundAtIdx = $.inArray(aData, self._selectedRows)

            console.log('checkbox mouse', iVisualRowIdx, foundAtIdx);
            //XXX: Debugging -- Bug here-ish
            if (foundAtIdx >= 0) {
                console.log(aData, self._selectedRows[foundAtIdx]);
            } else {
                console.log("clicked row not detected as already selected");
            }

            if (foundAtIdx >= 0 && self._selectedRows.length > 1) {
                self._selectedRows.splice(foundAtIdx, 1);
                $nRow.removeClass('selected');
                $nRow.find('input.airtime_table_checkbox').attr('checked', false);
            }
            else {
                self._selectedRows.push(aData);

                self._selectedRowVisualIdxMin = iVisualRowIdx;
                self._selectedRowVisualIdxMax = iVisualRowIdx;

                $nRow.addClass('selected');
                $nRow.find('input.airtime_table_checkbox').attr('checked', true);
            }
        }
        //Shift-click multi row selection mode
        else if (selectionMode == self.SELECTION_MODE.MULTI_SHIFT) {

            //If there's no rows selected, just behave like single selection.
            if (self._selectedRows.length == 0) {
                return self.selectRow(nRow, aData, self.SELECTION_MODE.SINGLE, iVisualRowIdx);
            }

            if (iVisualRowIdx > self._selectedRowVisualIdxMax) {
                self._selectedRowVisualIdxMax = iVisualRowIdx;
            }
            if (iVisualRowIdx < self._selectedRowVisualIdxMin) {
                self._selectedRowVisualIdxMin = iVisualRowIdx;
            }

            var selectionStartRowIdx = Math.min(iVisualRowIdx, self._selectedRowVisualIdxMin);
            var selectionEndRowIdx = Math.min(iVisualRowIdx, self._selectedRowVisualIdxMax);


            //We can assume there's at least 1 row already selected now.
            var allRows = self._datatable.fnGetData();

            self._selectedRows = [];
            for (var i = self._selectedRowVisualIdxMin; i <= self._selectedRowVisualIdxMax; i++)
            {
                self._selectedRows.push(allRows[i]);
                $row = $($nRow.parent().children()[i]);
                $row.addClass('selected');
                $row.find('input.airtime_table_checkbox').attr('checked', true);
            }

        }
        else {
            console.log("Unimplemented selection mode");
        }

    };

    self.getSelectedRows = function() {
        return self._selectedRows;
    }

    return AIRTIME;

}(AIRTIME || {}));


