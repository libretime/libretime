var AIRTIME = (function(AIRTIME){
	var mod;
	
	if (AIRTIME.library === undefined) {
		AIRTIME.library = {};
	}
	
	mod = AIRTIME.library;
	
	mod.checkAddButton = function() {
		var selected = mod.getChosenItemsLength(),
    		$cursor = $('tr.cursor-selected-row'),
    		check = false;
    	
    	//make sure library items are selected and a cursor is selected.
    	if (selected !== 0 && $cursor.length !== 0) {
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
	    	.data("screen", "timeline");
	};
	
	mod.fnDrawCallback = function fnLibDrawCallback() {
		
		mod.redrawChosen();
		mod.checkToolBarIcons();
		
		$('#library_display tr.lib-audio, tr.lib-pl, tr.lib-stream').draggable({
			helper: function(){
				
			    var $el = $(this),
			    	selected = mod.getChosenItemsLength(),
			    	container,
			    	thead = $("#show_builder_table thead"),
			    	colspan = thead.find("th").length,
			    	width = thead.find("tr:first").width(),
			    	message;
			    
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
	
	mod.dblClickAdd = function(id, type) {
        
        var i,
            length,
            temp,
            aMediaIds = [],
            aSchedIds = [],
            aData = [];
        
        //process selected files/playlists.
        aMediaIds.push({"id": id, "type": type});
        
        $("#show_builder_table tr.cursor-selected-row").each(function(i, el){
            aData.push($(el).prev().data("aData"));
        });
    
        //process selected schedule rows to add media after.
        for (i=0, length = aData.length; i < length; i++) {
            temp = aData[i];
            aSchedIds.push({"id": temp.id, "instance": temp.instance, "timestamp": temp.timestamp});    
        }
        
        if(aSchedIds.length == 0){
            alert("Please select a cursor position on timeline.");
            return false;
        }
        AIRTIME.showbuilder.fnAdd(aMediaIds, aSchedIds);
	};
	
	mod.setupLibraryToolbar = function() {
		var $toolbar = $(".lib-content .fg-toolbar:first");
		
		$toolbar
			.append("<ul />")
			.find('ul')
				.append('<li class="ui-state-default lib-button-select" title="Select"><span class="ui-icon ui-icon-document-b"></span></li>')
				.append('<li class="ui-state-default ui-state-disabled lib-button-add" title="Add library items after selected cursors in the timeline"><span class="ui-icon ui-icon-plusthick"></span></li>')
				.append('<li class="ui-state-default ui-state-disabled lib-button-delete" title="Delete selected library items"><span class="ui-icon ui-icon-trash"></span></li>');
		
		//add to timeline button
		$toolbar.find('.lib-button-add')
			.click(function() {
				
				if (AIRTIME.button.isDisabled('lib-button-add') === true) {
					return;
				}
				
				var selected = AIRTIME.library.getSelectedData(),
					data,
					i,
					length,
					temp,
					aMediaIds = [],
					aSchedIds = [],
					aData = [];
				
				//process selected files/playlists.
				for (i = 0, length = selected.length; i < length; i++) {
					data = selected[i];
					aMediaIds.push({"id": data.id, "type": data.ftype});	
				}
				
				$("#show_builder_table tr.cursor-selected-row").each(function(i, el){
					aData.push($(el).prev().data("aData"));
				});
			
				//process selected schedule rows to add media after.
				for (i=0, length = aData.length; i < length; i++) {
					temp = aData[i];
					aSchedIds.push({"id": temp.id, "instance": temp.instance, "timestamp": temp.timestamp}); 	
				}
				
				AIRTIME.showbuilder.fnAdd(aMediaIds, aSchedIds);	
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
