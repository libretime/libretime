function dtRowCallback( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {

    $(nRow).attr("id", aData["id"]);

    return nRow;
}

function dtDrawCallback() {
    addLibraryItemEvents();
    addMetadataQtip();
    //saveNumEntriesSetting();
    setupGroupActions();
}

function setupLibraryToolbar() {
	$("div.library_toolbar").html('<span class="fg-button ui-button ui-state-default" id="library_order_reset">Reset Order</span>' + 
	        '<span class="fg-button ui-button ui-state-default ui-state-disabled" id="library_group_delete">Delete</span>' + 
	        '<span class="fg-button ui-button ui-state-default ui-state-disabled" id="library_group_add">Add</span>');	
}

