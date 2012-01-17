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