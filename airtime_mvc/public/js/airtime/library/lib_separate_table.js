var AIRTIME = (function(AIRTIME) {
	
	if (AIRTIME.library === undefined) {
        AIRTIME.library = {};
    }
    var mod = AIRTIME.library;
    
    function makeWebstreamDialog(html) {
		var $wsDialogEl = $(html);
		
		function removeDialog() {
    		$wsDialogEl.dialog("destroy");
        	$wsDialogEl.remove();
    	}
		
		function saveDialog() {
			
		}
		
		$wsDialogEl.dialog({	       
	        title: $.i18n._("Webstream"),
	        modal: true,
	        show: 'clip',
            hide: 'clip',
            width: 600,
            height: 350,
	        buttons: [
				{text: $.i18n._("Cancel"), class: "btn btn-small", click: removeDialog},
				{text: $.i18n._("Save"),  class: "btn btn-small btn-inverse", click: saveDialog}
			],
	        close: removeDialog
	    });
	}
    
    function createDatatable(config) {
    	
    	var table = $("#"+config.id).dataTable({
    		"aoColumns": config.columns,
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": config.source,
			"sAjaxDataProp": "media",
			"fnServerData": function ( sSource, aoData, fnCallback ) {
               
                aoData.push( { name: "format", value: "json"} );
               
                $.ajax( {
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aoData,
                    "success": fnCallback
                } );
            },
			"oLanguage": datatables_dict,
			"aLengthMenu": [[5, 10, 15, 20, 25, 50, 100], [5, 10, 15, 20, 25, 50, 100]],
			"iDisplayLength": 25,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": true,
			"sDom": 'Rl<"#library_display_type">f<"dt-process-rel"r><"H"<"library_toolbar"C>><"dataTables_scrolling"t><"F"ip>',
			"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
	           $(nRow).data("aData", aData);
	        }
		});
    	
    	table.fnSetFilteringDelay(350);
    }
    
    function sendContextMenuRequest(data) {
    	
    	var callback = data.callback;
    	
    	data.requestData["format"] = "json";
    	
    	$.ajax({
            url: data.requestUrl,
            type: data.requestType,
            data: data.requestData,
            dataType: "json",
            async: false,
            success: function(json) {
            	
            	var f = callback.split("."),
            		i,
            		len,
            		obj = window;
            	
            	for (i = 0, len = f.length; i < len; i++) {
            		
            		obj = obj[f[i]];
            	}
            	
            	obj(json);
            }
        });
    }
     
    mod.onReady = function () {
    	
    	var $library = $("#library_content");

    	var tabsInit = {
    		"lib_audio": {
		    	initialized: false,
		    	initialize: function() {
		    		
		    	},
		    	navigate: function() {
		    		
		    	},
		    	always: function() {
		    		
		    	},
		    	localColumns: "datatables-audiofile-aoColumns",
		    	tableId: "audio_table",
		    	source: baseUrl+"media/audio-file-feed"
		    },
		    "lib_webstreams": {
		    	initialized: false,
		    	initialize: function() {
		    		
		    	},
		    	navigate: function() {
		    		
		    	},
		    	always: function() {
		    		
		    	},
		    	localColumns: "datatables-webstream-aoColumns",
		    	tableId: "webstream_table",
		    	source: baseUrl+"media/webstream-feed"
		    },
		    "lib_playlists": {
		    	initialized: false,
		    	initialize: function() {
		    		
		    	},
		    	navigate: function() {
		    		
		    	},
		    	always: function() {
		    		
		    	},
		    	localColumns: "datatables-playlist-aoColumns",
		    	tableId: "playlist_table",
		    	source: baseUrl+"media/playlist-feed"
		    }
    	};

    	
    	$("#lib_tabs").tabs({
    		show: function( event, ui ) {
    			var tab = tabsInit[ui.panel.id];
    			
    			if (tab.initialized) {
    				
    			}
    			else {
    				
    				var columns = JSON.parse(localStorage.getItem(tab.localColumns));
    				createDatatable({
    					id: tab.tableId, 
    					columns: columns,
    					prop: tab.dataprop,
    					source: tab.source
    				});
    				
    				tab.initialized = true;
    			}
    			
    			tab.always();
			},
			select: function( event, ui ) {
				var x;
			}
    	});
    	
    	$library.on("click", "#lib_new_webstream", function(e) {
    		var url = baseUrl+"webstream/new",
    			data = {format: "json"};
    		
    		$.post(url, data, function(json) {
    			makeWebstreamDialog(json.html);
    		});
    	});
    	
    	$library.on("click", "#lib_new_playlist", function(e) {
    		var url = baseUrl+"playlist/new",
    			data = {format: "json"};
    		
    		$.post(url, data, function(json) {
    			AIRTIME.playlist.drawPlaylist(json);
    		});
    	});
    	
    	 // begin context menu initialization.
        $.contextMenu({
            selector: '#lib_tabs td',
            trigger: "left",
            ignoreRightClick: true,
            
            build: function($el, e) {
                var data, items, $tr;
                
                $tr = $el.parent();
                data = $tr.data("aData");
                 
                $.ajax({
                  url: baseUrl+"library/context-menu",
                  type: "GET",
                  data: {id : data.Id, format: "json"},
                  dataType: "json",
                  async: false,
                  success: function(json) {
                      items = json.items;
                  }
                });
    
                return {
                    items: items,
                    callback: function(key, options) {
                        var m = "clicked: " + key;
                        window.console && console.log(m);
                        sendContextMenuRequest(options.commands[key]);
                    }
                };
            }
        });
    };

	return AIRTIME;
	
}(AIRTIME || {}));

$(document).ready(AIRTIME.library.onReady);