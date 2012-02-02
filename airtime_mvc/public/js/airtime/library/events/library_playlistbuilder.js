function fnLibraryTableRowCallback( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {

    $(nRow).attr("id", aData["tr_id"]);
    
	$(nRow).find('td')
		.jjmenu("rightClick",
			[{get:"/Library/context-menu/format/json/id/#id#/type/#type#"}],
			{id: aData["id"], type: aData["ftype"]},
			{xposition: "mouse", yposition: "mouse"});
}

function fnLibraryTableDrawCallback() {
	
	$('#library_display tr[id ^= "au"]').draggable({
		helper: 'clone',
		cursor: 'pointer'
	});
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
}
