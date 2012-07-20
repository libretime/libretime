var AIRTIME = (function(AIRTIME){
	var mod;
	
	if (AIRTIME.library === undefined) {
		AIRTIME.library = {};
	}
	
	mod = AIRTIME.library;

    mod.checkAddButton = function() {
    	var selected = mod.getChosenItemsLength(),
    		sortable = $('#spl_sortable'),
    		check = false;
    	
    	//make sure audioclips are selected and a playlist is currently open.
    	if (selected !== 0 && sortable.length !== 0) {
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
		
		if (aData.ftype === "audioclip") {
			$nRow.addClass("lib-audio");
		} else if (aData.ftype === "stream"){
            $nRow.addClass("lib-stream");
        } else {
			$nRow.addClass("lib-pl");
		}
		
		$nRow.attr("id", aData["tr_id"])
	    	.data("aData", aData)
	    	.data("screen", "playlist");
	};
	
	mod.fnDrawCallback = function() {
		
		mod.redrawChosen();
		mod.checkToolBarIcons();
		
		$('#library_display tr.lib-audio, tr.lib-stream, tr.lib-pl').draggable({
			helper: function(){
					
			    var $el = $(this),
			    	selected = mod.getChosenAudioFilesLength(),
			    	container,
			    	message,
			    	li = $("#side_playlist ul li:first"),
			    	width = li.width(),
			    	height = li.height();
			    
			    //dragging an element that has an unselected checkbox.
			    if (mod.isChosenItem($el) === false) {
			    	selected++;
			    }
			   
			    if (selected === 1) {
			    	message = "Adding 1 Item.";
			    }
			    else {
			    	message = "Adding "+selected+" Items.";
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
	
	mod.dblClickAdd = function(id, type) {
        var i,
            aMediaIds = [];
        
        //process selected files/playlists.
        if (type === "audioclip") {
            aMediaIds.push(id);
        }
    
        AIRTIME.playlist.fnAddItems(aMediaIds, undefined, 'after');
	};
	
	mod.setupLibraryToolbar = function() {
		var $toolbar = $(".lib-content .fg-toolbar:first");
		
		$toolbar
			.append("<ul />")
			.find('ul')
				.append('<li class="ui-state-default lib-button-select" title="Select"><span class="ui-icon ui-icon-document-b"></span></li>')
				.append('<li class="ui-state-default ui-state-disabled lib-button-add" title="Add selected library items to the current playlist"><span class="ui-icon ui-icon-plusthick"></span></li>')
				.append('<li class="ui-state-default ui-state-disabled lib-button-delete" title="Delete selected library items"><span class="ui-icon ui-icon-trash"></span></li>');
		
		//add to playlist button
		$toolbar.find('.lib-button-add')
			.click(function() {
				
				if (AIRTIME.button.isDisabled('lib-button-add') === true) {
					return;
				}
				
				var aData = AIRTIME.library.getSelectedData(),
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
		
		mod.createToolbarDropDown();
	};
	
	return AIRTIME;
	
}(AIRTIME || {}));
