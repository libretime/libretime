var AIRTIME = (function(AIRTIME){
	var mod;
	
	if (AIRTIME.library === undefined) {
		AIRTIME.library = {};
	}
	
	AIRTIME.library.events = {};
	mod = AIRTIME.library.events;

    mod.enableAddButtonCheck = function() {
    	var selected = $('#library_display tr[id ^= "au"] input[type=checkbox]').filter(":checked"),
    		sortable = $('#spl_sortable'),
    		check = false;
    	
    	//make sure audioclips are selected and a playlist is currently open.
    	if (selected.length !== 0 && sortable.length !== 0) {
    		check = true;
    	}
    	
    	if (check === true) {
	    	AIRTIME.button.enableButton("lib-button-add");
	    }
	    else {
	    	AIRTIME.button.disableButton("lib-button-add");
	    }
    };
	
	mod.fnRowCallback = function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
		var $nRow = $(nRow);
		
		$nRow.attr("id", aData["tr_id"])
	    	.data("aData", aData)
	    	.data("screen", "playlist");
	};
	
	mod.fnDrawCallback = function() {
		
		$('#library_display tr[id ^= "au"]').draggable({
			helper: function(){
			    var selected = $('#library_display tr:not(:first) input:checked').parents('tr[id^="au"]'),
			    	container,
			    	message,
			    	li = $("#side_playlist ul li:first"),
			    	width = li.width(),
			    	height = li.height();
			    
			    if (selected.length === 0) {
			    	selected = $(this);
			    }
			    
			    if (selected.length === 1) {
			    	message = "Adding "+selected.length+" Item.";
			    }
			    else {
			    	message = "Adding "+selected.length+" Items.";
			    }
			    
			    container = $('<div class="helper"/>')
			    	.append("<li/>")
			    	.find("li")
				    	.addClass("ui-state-default")
			    		.append("<div/>")
			    		.find("div")
			    			.addClass("list-item-container")
			    			.append(message)
			    			.end()
				    	.width(width)
				    	.height(height)
				    	.end();
			        
			    return container; 
		    },
			cursor: 'pointer',
			connectToSortable: '#spl_sortable'
		});
	};
	
	mod.setupLibraryToolbar = function() {
		var $toolbar = $(".lib-content .fg-toolbar:first");
		
		$toolbar
			.append("<ul />")
			.find('ul')
				.append('<li class="ui-state-default ui-state-disabled lib-button-add" title="add selected files to playlist"><span class="ui-icon ui-icon-plusthick"></span></li>')
				.append('<li class="ui-state-default ui-state-disabled lib-button-delete" title="delete selected files"><span class="ui-icon ui-icon-trash"></span></li>');
		
		//add to playlist button
		$toolbar.find('.lib-button-add')
			.click(function() {
				
				if (AIRTIME.button.isDisabled('lib-button-add') === true) {
					return;
				}
				
				var oLibTT = TableTools.fnGetInstance('library_display'),
					aData = oLibTT.fnGetSelectedData(),
					i,
					temp,
					length,
					aMediaIds = [];
				
				//process selected files/playlists.
				for (i = 0, length = aData.length; i < length; i++) {
					temp = aData[i];
					if (temp.ftype === "audioclip") {
						aMediaIds.push(temp.id);
					}
				}
			
				AIRTIME.playlist.fnAddItems(aMediaIds, undefined, 'after');
			});
		
		//delete from library.
		$toolbar.find('.lib-button-delete')
			.click(function() {
				
				if (AIRTIME.button.isDisabled('lib-button-delete') === true) {
					return;
				}
				
				AIRTIME.library.fnDeleteSelectedItems();
			});
	};
	
	return AIRTIME;
	
}(AIRTIME || {}));
