function fnLibraryTableRowCallback( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {

    $(nRow).attr("id", aData["id"]);

    return nRow;
}

function fnLibraryTableDrawCallback() {
    addLibraryItemEvents();
    addMetadataQtip();
    //saveNumEntriesSetting();
    setupGroupActions();
}

function addLibraryItemEvents() {

	$('#library_display tr[id ^= "au"]')
		.draggable({
			helper: 'clone',
			cursor: 'pointer'
		});

	$('#library_display tbody tr td').not('[class=library_checkbox]')
		.jjmenu("click",
			[{get:"/Library/context-menu/format/json/id/#id#/type/#type#"}],
			{id: getId, type: getType},
			{xposition: "mouse", yposition: "mouse"});

}

function setupLibraryToolbar() {
	$("div.library_toolbar").html('<span class="fg-button ui-button ui-state-default" id="library_order_reset">Reset Order</span>' + 
	        '<span class="fg-button ui-button ui-state-default ui-state-disabled" id="library_group_delete">Delete</span>' + 
	        '<span class="fg-button ui-button ui-state-default ui-state-disabled" id="library_group_add">Add</span>');	
}
