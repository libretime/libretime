function dtRowCallback( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {

    $(nRow).attr("id", aData["id"]);
    
    $(nRow).data("show_builder", {"id": aData["id"], "length": aData["length"]});

    return nRow;
}

function dtDrawCallback() {
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

	$('#library_display tbody tr td').not('[class=library_checkbox]')
		.jjmenu("click",
			[{get:"/Library/context-menu/format/json/id/#id#/type/#type#"}],
			{id: getId, type: getType},
			{xposition: "mouse", yposition: "mouse"});

}