var AIRTIME = (function(AIRTIME){
	var mod;
	
	if (AIRTIME.library === undefined) {
		AIRTIME.library = {}
	}
	
	AIRTIME.library.events = {};
	mod = AIRTIME.library.events;
	
	mod.fnRowCallback = function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
		var $nRow = $(nRow);
		
		$nRow.attr("id", aData["tr_id"])
	    	.data("aData", aData)
	    	.data("screen", "playlist");
	}
	
	mod.fnDrawCallback = function() {
		
		$('#library_display tr[id ^= "au"]').draggable({
			helper: 'clone',
			/* customize the helper on dragging to look like a pl item
			 * 
			helper: function(ev) {
				var data, li;
				
				data = $(ev.currentTarget).data("aData");
				
				li = $("<li></li>");
				li.append(data.track_title);
				
				return li;
			},
			*/
			cursor: 'pointer',
			connectToSortable: '#spl_sortable'
		});
	}
	
	/*
	 * @param oTable the datatables instance for the library.
	 */
	mod.setupLibraryToolbar = function( oLibTable ) {
		var aButtons,
			fnResetCol,
			fnAddSelectedItems;
		
		fnResetCol = function () {
			ColReorder.fnReset( oLibTable );
			return false;
		};
		
		fnAddSelectedItems = function() {
			var oLibTT = TableTools.fnGetInstance('library_display'),
				aData = oLibTT.fnGetSelectedData(),
				item,
				temp,
				aMediaIds = [];
			
			//process selected files/playlists.
			for (item in aData) {
				temp = aData[item];
				if (temp !== null && temp.hasOwnProperty('id') && temp.ftype === "audioclip") {
					aMediaIds.push(temp.id);
				} 	
			}
		
			AIRTIME.playlist.fnAddItems(aMediaIds, undefined, 'after');
		};
		
		//[0] = button text
		//[1] = id 
		//[2] = enabled
		//[3] = click event
		aButtons = [["Reset Order", "library_order_reset", true, fnResetCol], 
		                ["Delete", "library_group_delete", true], 
		                ["Add", "library_group_add", true, fnAddSelectedItems]];
		
		addToolBarButtonsLibrary(aButtons);
	}
	

	return AIRTIME;
	
}(AIRTIME || {}));
