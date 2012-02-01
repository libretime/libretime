function fnLibraryTableRowCallback( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {

    $(nRow).attr("id", aData["tr_id"]);

    return nRow;
}

function fnLibraryTableDrawCallback() {
    addMetadataQtip();
}

function addLibraryItemEvents() {

	$('#library_display tr[id ^= "au"]')
		.draggable({
			helper: 'clone',
			cursor: 'pointer'
		});

	/*
	$('#library_display tbody tr td').not('[class=library_checkbox]')
		.jjmenu("click",
			[{get:"/Library/context-menu/format/json/id/#id#/type/#type#"}],
			{id: getId, type: getType},
			{xposition: "mouse", yposition: "mouse"});
	*/

}

/*
 * @param oTable the datatables instance for the library.
 */
function setupLibraryToolbar(oTable) {
	var aButtons,
		oSettings;
	
	//[0] = button text
	//[1] = id 
	//[2] = enabled
	aButtons = [["Reset Order", "library_order_reset", true], 
	                ["Delete", "library_group_delete", false], 
	                ["Add", "library_group_add", false]];
	
	addToolBarButtonsLibrary(aButtons);
	
	oSettings = oTable.fnSettings();
    oSettings.fnServerData.start = oRange.start;
}
