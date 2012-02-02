function fnLibraryTableRowCallback( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
	var jRow = $(nRow);
	
	jRow.attr("id", aData["tr_id"]);
	jRow.addClass("lib-sb");
    
    //save some info for reordering purposes.
	jRow.data({"aData": aData});
}

function fnLibraryTableDrawCallback() {
	
	$('#library_display tr:not(:first)').draggable({
		helper: 'clone',
		cursor: 'pointer',
		connectToSortable: '#show_builder_table'
	});	
}

function setupLibraryToolbar(oLibTable) {
	var aButtons,
		fnTest,
		fnResetCol,
		fnAddSelectedItems,
		oSchedTable = $("#show_builder_table").dataTable(),
		oLibTT = TableTools.fnGetInstance('library_display'),
		oSchedTT = TableTools.fnGetInstance('show_builder_table');
	
	fnTest = function() {
		alert("hi");
	};
	
	fnResetCol = function () {
		ColReorder.fnReset( oLibTable );
		return false;
	};
	
	fnAddSelectedItems = function() {
		var aData = oLibTT.fnGetSelectedData(),
			item,
			temp,
			aMediaIds = [],
			aSchedIds = [];
		
		//process selected files/playlists.
		for (item in aData) {
			temp = aData[item];
			if (temp !== null && temp.hasOwnProperty('id')) {
				aMediaIds.push({"id": temp.id, "type": temp.ftype});
			} 	
		}
	
		aData = oSchedTT.fnGetSelectedData();
		
		//process selected schedule rows to add media after.
		for (item in aData) {
			temp = aData[item];
			if (temp !== null && temp.hasOwnProperty('id')) {
				aSchedIds.push({"id": temp.id, "instance": temp.instance});
			} 	
		}
		
		$.post("/showbuilder/schedule-add", 
			{"format": "json", "mediaIds": aMediaIds, "schedIds": aSchedIds}, 
			function(json){
				oLibTT.fnSelectNone();
				oSchedTable.fnDraw();
			});
	};
	//[0] = button text
	//[1] = id 
	//[2] = enabled
	//[3] = click event
	aButtons = [["Reset Order", "library_order_reset", true, fnResetCol], 
	                ["Delete", "library_group_delete", true, fnTest], 
	                ["Add", "library_group_add", true, fnAddSelectedItems]];
	
	addToolBarButtonsLibrary(aButtons);
}