function fnLibraryTableRowCallback( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {

    $(nRow).attr("id", aData["id"]);
    
    $(nRow).data("show_builder", {"id": aData["id"], "length": aData["length"]});

    return nRow;
}

function fnLibraryTableDrawCallback() {
    addLibraryItemEvents();
    //addMetadataQtip();
    //setupGroupActions();
}

function addLibraryItemEvents() {

	$('#library_display tr')
		.draggable({
			helper: 'clone',
			cursor: 'pointer',
			connectToSortable: '#show_builder_table'
		});
}

function setupLibraryToolbar() {
	var aButtons,
		fnTest,
		fnAddSelectedItems,
		oSettings,
		oLibTable = $("#library_display").dataTable(),
		oSchedTable = $("#show_builder_table").dataTable(),
		oLibTT = TableTools.fnGetInstance('library_display'),
		oSchedTT = TableTools.fnGetInstance('show_builder_table');
	
	fnTest = function() {
		alert("hi");
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
	aButtons = [["Reset Order", "library_order_reset", true, fnTest], 
	                ["Delete", "library_group_delete", false, fnTest], 
	                ["Add", "library_group_add", false, fnAddSelectedItems]];
	
	addToolBarButtonsLibrary(aButtons);
}