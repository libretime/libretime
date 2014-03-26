/*
* File:        jquery.dataTables.columnFilter.js
* Version:     1.5.3.
* Author:      Jovan Popovic 
* 
* Copyright 2011-2014 Jovan Popovic, all rights reserved.
*
* This source file is free software, under either the GPL v2 license or a
* BSD style license, as supplied with this software.
* 
* This source file is distributed in the hope that it will be useful, but 
* WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
* or FITNESS FOR A PARTICULAR PURPOSE. 
* 
* Parameters:"
* @sPlaceHolder                 String      Place where inline filtering function should be placed ("tfoot", "thead:before", "thead:after"). Default is "tfoot"
* @sRangeSeparator              String      Separator that will be used when range values are sent to the server-side. Default value is "~".
* @sRangeFormat                 string      Default format of the From ... to ... range inputs. Default is From {from} to {to}
* @aoColumns                    Array       Array of the filter settings that will be applied on the columns
*/
(function ($) {


    $.fn.columnFilter = function (options) {

        var i, label, th, j, k;

        //var sTableId = "table";
        var sRangeFormat = "From {from} to {to}";
        //Array of the functions that will override sSearch_ parameters
        var afnSearch_ = new Array();
        var aiCustomSearch_Indexes = new Array();

        var oFunctionTimeout = null;

        var fnOnFiltered = function () { };
        
        var oTable = this;

        var defaults = {
            sPlaceHolder: "foot",
            sRangeSeparator: "~",
            iFilteringDelay: 500,
            aoColumns: null,
            sRangeFormat: "From {from} to {to}",
            sDateFromToken: "from",
            sDateToToken: "to"
        };

        var properties = $.extend(defaults, options);

        function _fnColumnIndex(iColumnIndex) {
        	var s = oTable.fnSettings(),
        		order = s.oLoadedState.ColReorder,
        		index = order.indexOf(iColumnIndex);
        	
        	//iColumnIndex is the original column index.
        	//to match the rest of the data sent to the server it must give
        	//its new display index after reordering in the table.
        	return index;
        }
        
        //the original col index
        function createCustomRangeSearch(iColumnIndex) {
        	
        	//fix to return "" instead of the range separator if both fields are blank.;
            var fnSearch = function () {
                var id = oTable.attr("id"),
                	from = $("#" + id + "_range_from_" + iColumnIndex).val(),
                	to = $("#" + id + "_range_to_" + iColumnIndex).val();
                
                if (from === "" && to === "") {
                	return "";
                }
                
                return  from + properties.sRangeSeparator + to;
            };
            
            return fnSearch;
        }
        
        //the original col index
        function createCustomInputSearch(iColumnIndex) {

            var fnSearch = function () {
            	var id = oTable.attr("id") + '_input_' + iColumnIndex;
            	
            	return  $("#"+id).val();
            };
            
            return fnSearch;
        }
        
        //the original col index
        function createCustomSelectSearch(iColumnIndex) {

            var fnSearch = function () {
            	var id = oTable.attr("id") + '_select_' + iColumnIndex;

            	return  $("#"+id).val();
            };
            
            return fnSearch;
        }

        function fnCreateInput(oTable, regex, smart, bIsNumber, iFilterLength, iMaxLength) {
        	var index = i;
        	var id = oTable.attr("id") + '_input_' + index;
            var sCSSClass = "text_filter";
            if (bIsNumber) {
            	sCSSClass = "number_filter";
            }
                
            label = label.replace(/(^\s*)|(\s*$)/g, "");
            var search_init = 'search_init ';
            
            var input = $('<input id = '+id+' type="text" class="' + search_init + sCSSClass + '" placeholder="' + label + '" rel="' + index + '"/>');
            if (iMaxLength != undefined && iMaxLength != -1) {
                input.attr('maxlength', iMaxLength);
            }
            
            th.html(input);
            if (bIsNumber) {
            	th.wrapInner('<span class="filter_column filter_number" />');
            }   
            else {
            	th.wrapInner('<span class="filter_column filter_text" />');
            }
                
            aiCustomSearch_Indexes.push(index);
            afnSearch_.push(createCustomInputSearch(index));
        }

        function fnCreateRangeInput(oTable) {

            th.html(_fnRangeLabelPart(0));
            var sFromId = oTable.attr("id") + '_range_from_' + i;
            var from = $('<input type="text" class="number_range_filter" id="' + sFromId + '" rel="' + i + '"/>');
            th.append(from);
            th.append(_fnRangeLabelPart(1));
            var sToId = oTable.attr("id") + '_range_to_' + i;
            var to = $('<input type="text" class="number_range_filter" id="' + sToId + '" rel="' + i + '"/>');
            th.append(to);
            th.append(_fnRangeLabelPart(2));
            th.wrapInner('<span class="filter_column filter_number_range" />');
            var index = i;
            
            //------------start range filtering function


            /* 	Custom filtering function which will filter data in column four between two values
            *	Author: 	Allan Jardine, Modified by Jovan Popovic
            */
            oTable.dataTableExt.afnFiltering.push(
		        function (oSettings, aData, iDataIndex) {
		            if (oTable.attr("id") != oSettings.sTableId)
		                return true;
		            // Try to handle missing nodes more gracefully
		            if (document.getElementById(sFromId) == null)
		                return true;
		            var iMin = document.getElementById(sFromId).value * 1;
		            var iMax = document.getElementById(sToId).value * 1;
		            var iValue = aData[_fnColumnIndex(index)] == "-" ? 0 : aData[_fnColumnIndex(index)] * 1;
		            if (iMin == "" && iMax == "") {
		                return true;
		            }
		            else if (iMin == "" && iValue <= iMax) {
		                return true;
		            }
		            else if (iMin <= iValue && "" == iMax) {
		                return true;
		            }
		            else if (iMin <= iValue && iValue <= iMax) {
		                return true;
		            }
		            return false;
		        }
	        );
            //------------end range filtering function

            aiCustomSearch_Indexes.push(index);
            afnSearch_.push(createCustomRangeSearch(index));
        }

        function fnCreateDateRangeInput(oTable) {
        	var index = i;
            var aoFragments = sRangeFormat.split(/[}{]/);

            th.html("");
            var sFromId = oTable.attr("id") + '_range_from_' + i;
            var from = $('<input type="text" class="date_range_filter" id="' + sFromId + '" rel="' + i + '"/>');
            from.datepicker();
           
            var sToId = oTable.attr("id") + '_range_to_' + i;
            var to = $('<input type="text" class="date_range_filter" id="' + sToId + '" rel="' + i + '"/>');
            
            for (ti = 0; ti < aoFragments.length; ti++) {

                if (aoFragments[ti] == properties.sDateFromToken) {
                    th.append(from);
                } 
                else {
                    if (aoFragments[ti] == properties.sDateToToken) {
                        th.append(to);
                    } 
                    else {
                        th.append(aoFragments[ti]);
                    }
                }              
            };

            th.wrapInner('<span class="filter_column filter_date_range" />');
            to.datepicker();

            //------------start date range filtering function

            oTable.dataTableExt.afnFiltering.push(
		        function (oSettings, aData, iDataIndex) {
		            if (oTable.attr("id") != oSettings.sTableId)
		                return true;
	
		            var dStartDate = from.datepicker("getDate");
	
		            var dEndDate = to.datepicker("getDate");
	
		            if (dStartDate == null && dEndDate == null) {
		                return true;
		            }
	
		            var dCellDate = null;
		            try {
		                if (aData[_fnColumnIndex(index)] == null || aData[_fnColumnIndex(index)] == "")
		                    return false;
		                dCellDate = $.datepicker.parseDate($.datepicker.regional[""].dateFormat, aData[_fnColumnIndex(index)]);
		            } catch (ex) {
		                return false;
		            }
		            if (dCellDate == null)
		                return false;
	
	
		            if (dStartDate == null && dCellDate <= dEndDate) {
		                return true;
		            }
		            else if (dStartDate <= dCellDate && dEndDate == null) {
		                return true;
		            }
		            else if (dStartDate <= dCellDate && dCellDate <= dEndDate) {
		                return true;
		            }
		            return false;
		        }
	        );
            //------------end date range filtering function

            aiCustomSearch_Indexes.push(index);
            afnSearch_.push(createCustomRangeSearch(index));
        }

        function fnCreateColumnSelect(oTable, aValues, iColumn, nTh, sLabel, bRegex, oSelected) {
            var index = iColumn;
            var id = oTable.attr("id") + '_select_' + index;
            
            var r = '<select id = '+id+' class="search_init select_filter" rel="' + index + '"><option value="" class="search_init">' + sLabel + '</option>';
            var j = 0;
            var iLen = aValues.length;
            
            for (j = 0; j < iLen; j++) { 

                r += '<option value="' + j + '">' + aValues[j] + '</option>';  
            }

            var select = $(r + '</select>');
            nTh.html(select);
            nTh.wrapInner('<span class="filter_column filter_select" />');
           
            aiCustomSearch_Indexes.push(index);
            afnSearch_.push(createCustomSelectSearch(index));
        }

        function fnCreateSelect(oTable, aValues, bRegex, oSelected) {
           
            fnCreateColumnSelect(oTable, aValues, _fnColumnIndex(i), th, label, bRegex, oSelected);
        }

        function _fnRangeLabelPart(iPlace) {
            switch (iPlace) {
                case 0:
                    return sRangeFormat.substring(0, sRangeFormat.indexOf("{from}"));
                case 1:
                    return sRangeFormat.substring(sRangeFormat.indexOf("{from}") + 6, sRangeFormat.indexOf("{to}"));
                default:
                    return sRangeFormat.substring(sRangeFormat.indexOf("{to}") + 4);
            }
        }

        return this.each(function () {

            if (!oTable.fnSettings().oFeatures.bFilter)
                return;
            
            var aoFilterCells = oTable.fnSettings().aoFooter[0];

            var oHost = oTable.fnSettings().nTFoot; //Before fix for ColVis
            var sFilterRow = "tr"; //Before fix for ColVis

            if (properties.sPlaceHolder == "head:after") {
                var tr = $("tr:first", oTable.fnSettings().nTHead).detach();

                if (oTable.fnSettings().bSortCellsTop) {
                    tr.prependTo($(oTable.fnSettings().nTHead));
                    aoFilterCells = oTable.fnSettings().aoHeader[1];
                }
                else {
                    tr.appendTo($(oTable.fnSettings().nTHead));
                    aoFilterCells = oTable.fnSettings().aoHeader[0];
                }

                sFilterRow = "tr:last";
                oHost = oTable.fnSettings().nTHead;

            } 
            else if (properties.sPlaceHolder == "head:before") {

                if (oTable.fnSettings().bSortCellsTop) {
                    var tr = $("tr:first", oTable.fnSettings().nTHead).detach();
                    tr.appendTo($(oTable.fnSettings().nTHead));
                    aoFilterCells = oTable.fnSettings().aoHeader[1];
                } 
                else {
                    aoFilterCells = oTable.fnSettings().aoHeader[0];
                }
                
                sFilterRow = "tr:first";
                oHost = oTable.fnSettings().nTHead; 
            }

            $(aoFilterCells).each(function (index) {//fix for ColVis
                i = index;
                var aoColumn = { type: "text",
                    bRegex: false,
                    bSmart: true,
                    iMaxLenght: -1,
                    iFilterLength: 0
                };
                if (properties.aoColumns != null) {
                    if (properties.aoColumns.length < i || properties.aoColumns[i] == null)
                        return;
                    aoColumn = properties.aoColumns[i];
                }

                label = $($(this)[0].cell).text(); //Fix for ColVis
                if (aoColumn.sSelector == null) {
                    th = $($(this)[0].cell); //Fix for ColVis
                }
                else {
                    th = $(aoColumn.sSelector);
                    if (th.length == 0)
                        th = $($(this)[0].cell);
                }

                if (aoColumn != null) {
                    if (aoColumn.sRangeFormat != null)
                        sRangeFormat = aoColumn.sRangeFormat;
                    else
                        sRangeFormat = properties.sRangeFormat;
                    switch (aoColumn.type) {
                        case "null":
                            break;
                        case "number":
                            fnCreateInput(oTable, true, false, true, aoColumn.iFilterLength, aoColumn.iMaxLenght);
                            break;
                        case "select":
                            if (aoColumn.bRegex != true)
                                aoColumn.bRegex = false;
                            fnCreateSelect(oTable, aoColumn.values, aoColumn.bRegex, aoColumn.selected);
                            break;
                        case "number-range":
                            fnCreateRangeInput(oTable);
                            break;
                        case "date-range":
                            fnCreateDateRangeInput(oTable);
                            break;
                        case "text":
                        default:
                            bRegex = (aoColumn.bRegex == null ? false : aoColumn.bRegex);
                            bSmart = (aoColumn.bSmart == null ? false : aoColumn.bSmart);
                            fnCreateInput(oTable, bRegex, bSmart, false, aoColumn.iFilterLength, aoColumn.iMaxLenght);
                            break;

                    }
                }
            });
            
            if (oTable.fnSettings().oFeatures.bServerSide) {

                var fnServerDataOriginal = oTable.fnSettings().fnServerData;

                oTable.fnSettings().fnServerData = function (sSource, aoData, fnCallback) {

                    for (j = 0; j < aiCustomSearch_Indexes.length; j++) {
                    	//aiCustomSearch_Indexes holds the ORIGINAL column position.
                        var index = _fnColumnIndex(aiCustomSearch_Indexes[j]);

                        for (k = 0; k < aoData.length; k++) {
                        	//custom search modifications for range fields.
                            if (aoData[k].name == "sSearch_" + index) {
                            	aoData[k].value = afnSearch_[j]();
                            }     
                        }
                    }
                    aoData.push({ "name": "sRangeSeparator", "value": properties.sRangeSeparator });

                    fnServerDataOriginal(sSource, aoData, fnCallback, oTable.fnSettings());  
                };
            }
        });
    };

})(jQuery);