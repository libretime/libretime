function fnLibraryTableRowCallback( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {

    $(nRow).attr("id", aData["id"]);

    return nRow;
}

function fnLibraryTableDrawCallback() {
    addLibraryItemEvents();
    addMetadataQtip();
    //saveNumEntriesSetting();
    //setupGroupActions();
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

function setupLibraryToolbar() {
	//[0] = button text
	//[1] = id 
	//[2] = enabled
	var aButtons = [["Reset Order", "library_order_reset", true], 
	                ["Delete", "library_group_delete", false], 
	                ["Add", "library_group_add", false]];
	
	addToolBarButtonsLibrary(aButtons);
}
