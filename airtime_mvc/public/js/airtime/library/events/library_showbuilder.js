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
		fnAddSelectedItems;
	
	fnTest = function() {
		alert("hi");
	};
	
	fnAddSelectedItems = function() {
		var oTT = TableTools.fnGetInstance('show_builder_table'),
			aData = oTT.fnGetSelectedData(),
			i,
			length = aData.length;
		
		for (i=0, i<length; i+=1;) {
			var x;
		}
	};
	//[0] = button text
	//[1] = id 
	//[2] = enabled
	aButtons = [["Reset Order", "library_order_reset", true, fnTest], 
	                ["Delete", "library_group_delete", false, fnTest], 
	                ["Add", "library_group_add", false, fnTest]];
	
	addToolBarButtonsLibrary(aButtons);
}