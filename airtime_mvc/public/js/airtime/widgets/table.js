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

    //Constants
    self.SELECTION_MODE = {
        SINGLE : 0,
        MULTI_SHIFT : 1,
        MULTI_CTRL : 2
    }

    self.HUGE_INT = Math.pow(2, 53) - 1;

    //Member variables
    self._datatable = null;
    self._selectedRows = []; //An array containing the underlying objects for each selected row. (Easy to use!)
    //self._selectedRowVisualIdxMap = []; //A map of the visual index of a selected rows onto the actual row data.
    self._selectedRowVisualIdxMin = self.HUGE_INT;
    self._selectedRowVisualIdxMax = -1;
    self._$wrapperDOMNode = null;


    //Member functions
    self.init = function(wrapperDOMNode, bItemSelection, dataTablesOptions) {
        self._$wrapperDOMNode = $(wrapperDOMNode);

        //TODO: If selection is enabled, add in the checkbox column.
        if (bItemSelection) {
            dataTablesOptions["aoColumns"].unshift(
                /* Checkbox */        { "sTitle" : "", "mData" : self._datatablesCheckboxDataDelegate, "bSortable"   : false                 , "bSearchable" : false                   , "sWidth" : "16px"         , "sClass" : "library_checkbox" }
            );

            dataTablesOptions["fnRowCallback"] = self._rowCreatedCallback;
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
            "sDom": 'Rf<"dt-process-rel"r><"H"<"library_toolbar"C>><"dataTables_scrolling"t<"#library_empty"<"#library_empty_image"><"#library_empty_text">>><"F"lip>>',

            "fnServerData": self._fetchData,
            "fnDrawCallback" : self._tableDrawCallback
        };

        //Override any options with those passed in as arguments to this constructor.
        for (var key in dataTablesOptions)
        {
            options[key] = dataTablesOptions[key];
        }

        self._datatable = self._$wrapperDOMNode.dataTable(options);

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

        //getUsabilityHint();
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
                json.iTotalDisplayRecords = json.iTotalRecords;//rawResponseJSON.length;
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

    self._rowCreatedCallback = function(nRow, aData, iDisplayIndex) {

        // Bind click event
        $(nRow).click(function(e) {
            e.stopPropagation();
            e.preventDefault();
            document.getSelection().removeAllRanges();
            //alert( 'You clicked on '+aData.track_title+'\'s row' + iDisplayIndex);
            var selectionMode = self.SELECTION_MODE.SINGLE;
            if (e.shiftKey) {
                selectionMode = self.SELECTION_MODE.MULTI_SHIFT;
            } else if (e.ctrlKey) {
                selectionMode = self.SELECTION_MODE.MULTI_CTRL;
            }
            self.selectRow(nRow, aData, selectionMode, iDisplayIndex);
        });

        return nRow;
    };

    self._tableDrawCallback = function(oSettings) {

        $('input.airtime_table_checkbox').click(function(e) {
            $this = $(this);

            var iVisualRowIdx = $this.parent().parent().index();
            self.selectRow($this.parent().parent(), null, self.SELECTION_MODE.MULTI_CTRL, iVisualRowIdx); //Always multiselect for checkboxes
            e.stopPropagation();
            return true;
        });
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

        /*
                var foundAtIdx = $.inArray(aData, self._selectedRows)

                if (foundAtIdx >= 0 && self._selectedRows.length > 1) {
                    self._selectedRows.splice(foundAtIdx, 1);
                    $nRow.removeClass('selected');
                    $nRow.find('input.airtime_table_checkbox').attr('checked', false);
                    */
        if (false) {
        } else {
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
        }
    };

    self._clearSelection = function() {
        self._selectedRows = [];
        //self._selectedRowVisualIdxMap = [];
        self._selectedRowVisualIdxMin = self.HUGE_INT;
        self._selectedRowVisualIdxMax = -1;
        self._$wrapperDOMNode.find('.selected').removeClass('selected');
        self._$wrapperDOMNode.find('input.airtime_table_checkbox').attr('checked', false);
    };

    self.getSelectedRows = function() {
        return self._selectedRows;
    }

    return AIRTIME;

}(AIRTIME || {}));


