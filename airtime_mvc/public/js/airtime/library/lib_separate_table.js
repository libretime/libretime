var AIRTIME = (function(AIRTIME) {
	
	if (AIRTIME.library === undefined) {
        AIRTIME.library = {};
    }
    mod = AIRTIME.library;
    
    function createDatatable(config) {
    	
    	$("#"+config.id).dataTable({
    		"aoColumns": config.columns,
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": config.source,
			"sAjaxDataProp": config.prop,
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
		});
    }
     
    mod.onReady = function () {

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
		    	source: baseUrl+"media/audio-file-feed",
		    	dataprop: "audiofiles"
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
    			}
    			
    			tab.always();
			},
			select: function( event, ui ) {
				var x;
			}
    	});
    };

	return AIRTIME;
	
}(AIRTIME || {}));

$(document).ready(AIRTIME.library.onReady);