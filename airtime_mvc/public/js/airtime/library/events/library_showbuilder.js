var AIRTIME = (function(AIRTIME){
	var mod;
	
	if (AIRTIME.library === undefined) {
		AIRTIME.library = {};
	}
	
	AIRTIME.library.events = {};
	mod = AIRTIME.library.events;
	
	mod.fnRowCallback = function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
		var $nRow = $(nRow);
		
		$nRow.attr("id", aData["tr_id"])
	    	.data("aData", aData)
	    	.data("screen", "timeline");
	};
	
	mod.fnDrawCallback = function() {
		
		$('#library_display tr:not(:first)').draggable({
			helper: function(){
			    var selected = $('#library_display input:checked').parents('tr');
			    if (selected.length === 0) {
			      selected = $(this);
			    }
			    var container = $('<div/>').attr('id', 'draggingContainer');
			    container.append(selected.clone());
			    return container; 
		    },
			cursor: 'pointer',
			connectToSortable: '#show_builder_table'
		});	
	};
	
	mod.setupLibraryToolbar = function(oLibTable) {
		var aButtons,
			fnTest,
			fnResetCol,
			fnAddSelectedItems,
		
		fnResetCol = function () {
			ColReorder.fnReset( oLibTable );
			return false;
		};
		
		fnAddSelectedItems = function() {
			var oLibTT = TableTools.fnGetInstance('library_display'),
				oSchedTT = TableTools.fnGetInstance('show_builder_table'),
				aData = oLibTT.fnGetSelectedData(),
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
					aSchedIds.push({"id": temp.id, "instance": temp.instance, "timestamp": temp.timestamp});
				} 	
			}
			
			AIRTIME.showbuilder.fnAdd(aMediaIds, aSchedIds);
			
		};
		//[0] = button text
		//[1] = id 
		//[2] = enabled
		//[3] = click event
		aButtons = [["Reset Order", "library_order_reset", true, fnResetCol], 
		                ["Delete", "library_group_delete", true, AIRTIME.library.fnDeleteSelectedItems], 
		                ["Add", "library_group_add", true, fnAddSelectedItems]];
		
		addToolBarButtonsLibrary(aButtons);
	};
	
	return AIRTIME;
	
}(AIRTIME || {}));