var AIRTIME = (function(AIRTIME) {
    var mod;
    
    if (AIRTIME.history === undefined) {
        AIRTIME.history = {};
    }
    mod = AIRTIME.history;
    
    mod.historyTable = function() {
        var oTable,
        	historyContentDiv = $("#history_content"),
        	historyTableDiv = historyContentDiv.find("#history_table");
        	tableHeight = historyContentDiv.height() - 140;
        
        oTable = historyTableDiv.dataTable( {
            
            "aoColumns": [
               {"sTitle": "Title", "mDataProp": "title", "sClass": "his_title"}, /* Title */
               {"sTitle": "Artist", "mDataProp": "artist", "sClass": "his_artist"}, /* Creator */
               {"sTitle": "Played", "mDataProp": "played", "sClass": "his_artist"}, /* times played */
               {"sTitle": "Length", "mDataProp": "length", "sClass": "his_length"}, /* Length */
               {"sTitle": "Composer", "mDataProp": "composer", "sClass": "his_composer"}, /* Composer */
               {"sTitle": "Copyright", "mDataProp": "copyright", "sClass": "his_copyright"} /* Copyright */
            ],
                          
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "/Playouthistory/playout-history-feed",
            "sAjaxDataProp": "history",
            
            "fnServerData": function ( sSource, aoData, fnCallback ) {
               
                aoData.push( { name: "format", value: "json"} );
                
                $.ajax( {
                    "dataType": 'json',
                    "type": "GET",
                    "url": sSource,
                    "data": aoData,
                    "success": fnCallback
                } );
            },
            
            "oLanguage": {
                "sSearch": ""
            },
            
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
           
            "sDom": 'lfr<"H"><"dataTables_scrolling"t><"F"ip>', 
        });
    };
    
return AIRTIME;
    
}(AIRTIME || {}));

$(document).ready(function(){
	
	var viewport = AIRTIME.utilities.findViewportDimensions(),
		history_content = $("#history_content"),
		widgetHeight = viewport.height - 185,
		screenWidth = Math.floor(viewport.width - 110);
	
	history_content
		.height(widgetHeight)
		.width(screenWidth);
	
	AIRTIME.history.historyTable();
	
});