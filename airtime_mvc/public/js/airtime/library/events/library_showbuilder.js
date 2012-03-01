var AIRTIME = (function(AIRTIME){
	var mod;
	
	if (AIRTIME.library === undefined) {
		AIRTIME.library = {};
	}
	
	AIRTIME.library.events = {};
	mod = AIRTIME.library.events;
	
	mod.enableAddButtonCheck = function() {
    	var selected = $('#library_display tr input[type=checkbox]').filter(":checked"),
    		cursor = $('tr.cursor-selected-row'),
    		check = false;
    	
    	//make sure library items are selected and a cursor is selected.
    	if (selected.length !== 0 && cursor.length !== 0) {
    		check = true;
    	}
    	
    	if (check === true) {
	    	AIRTIME.button.enableButton("library_group_add");
	    }
	    else {
	    	AIRTIME.button.disableButton("library_group_add");
	    }
    };
	
	mod.fnRowCallback = function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
		var $nRow = $(nRow);
		
		$nRow.attr("id", aData["tr_id"])
	    	.data("aData", aData)
	    	.data("screen", "timeline");
	};
	
	mod.fnDrawCallback = function() {
		
		$('#library_display tr:not(:first)').draggable({
			helper: function(){
			    var selected = $('#library_display tr:not(:first) input:checked').parents('tr'),
			    	container,
			    	thead = $("#show_builder_table thead"),
			    	colspan = thead.find("th").length,
			    	width = thead.find("tr:first").width(),
			    	message;
			    
			    //if nothing is checked select the dragged item.
			    if (selected.length === 0) {
			    	selected = $(this);
			    }
			    
			    if (selected.length === 1) {
			    	message = "Moving "+selected.length+" Item.";
			    }
			    else {
			    	message = "Moving "+selected.length+" Items.";
			    }
			    
			    container = $('<div/>').attr('id', 'draggingContainer')
			    	.append('<tr/>')
			    	.find("tr")
				    	.append('<td/>')
				    	.find("td")
				    		.attr("colspan", colspan)
				    		.width(width)
				    		.addClass("ui-state-highlight")
				    		.append(message)
				    		.end()
				    	.end();
			    
			    return container; 
		    },
			cursor: 'pointer',
			connectToSortable: '#show_builder_table'
		});	
	};
	
	mod.setupLibraryToolbar = function(oLibTable) {
		var aButtons,
			fnAddSelectedItems,
		
		fnAddSelectedItems = function() {
			var oLibTT = TableTools.fnGetInstance('library_display'),
				aData = oLibTT.fnGetSelectedData(),
				i,
				length,
				temp,
				aMediaIds = [],
				aSchedIds = [];
			
			//process selected files/playlists.
			for (i = 0, length = aData.length; i < length; i++) {
				temp = aData[i];
				aMediaIds.push({"id": temp.id, "type": temp.ftype});	
			}
			
			aData = [];
			$("#show_builder_table tr.cursor-selected-row").each(function(i, el){
				aData.push($(el).data("aData"));
			});
		
			//process selected schedule rows to add media after.
			for (i=0, length = aData.length; i < length; i++) {
				temp = aData[i];
				aSchedIds.push({"id": temp.id, "instance": temp.instance, "timestamp": temp.timestamp}); 	
			}
			
			AIRTIME.showbuilder.fnAdd(aMediaIds, aSchedIds);	
		};
		
		//[0] = button text
		//[1] = id 
		//[2] = enabled
		//[3] = click event
		aButtons = [["Delete", "library_group_delete", false, AIRTIME.library.fnDeleteSelectedItems], 
		           ["Add", "library_group_add", false, fnAddSelectedItems]];
		
		addToolBarButtonsLibrary(aButtons);
	};
	
	return AIRTIME;
	
}(AIRTIME || {}));