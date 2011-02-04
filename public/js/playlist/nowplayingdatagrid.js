var registered = false;
var datagridData;

function getDateText(obj){
	var str = obj.aData[ obj.iDataColumn ].toString();
	if (str.indexOf(" ") != -1){
		return changeTimePrecision(str.substring(0, str.indexOf(" ")));
	}
	return str;
}

function getTimeText(obj){
	var str = obj.aData[ obj.iDataColumn ].toString();
	if (str.indexOf(" ") != -1){
		return changeTimePrecision(str.substring(str.indexOf(" ")+1));
	}
	return str;
}

function changeTimePrecisionInit(obj){
	var str = obj.aData[ obj.iDataColumn ].toString();
	return changeTimePrecision(str);
}

function changeTimePrecision(str){
	if (str.indexOf(".") != -1){
		if (str.length - str.indexOf(".") > 2)
			var extraLength = str.length - str.indexOf(".") -3;
			return str.substring(0, str.length - extraLength);
	}
	return str;
}

function notifySongEnd(){
	//alert("length " + datagridData.rows.length);
	for (var i=0; i<datagridData.rows.length; i++){
		if (datagridData.rows[i][0] == "c")
			datagridData.rows[i][0] = "p";
		if (datagridData.rows[i][0] == "n"){
			datagridData.rows[i][0] = "c";
			break;
		}
	}
	
	createDataGrid();
}

/*
function updateDataGrid(){
    var table = $('#nowplayingtable');
    //table.dataTable().fnClearTable();
        
    for (var i=0; i<datagridData.rows.length; i++){
        table.dataTable().fnAddData(datagridData.rows[i]);
    }
}
*/
    var columns = [{"sTitle": "type", "bVisible":false},
        {"sTitle":"Date"},
        {"sTitle":"Start"},
        {"sTitle":"End"},
        {"sTitle":"Duration"},
        {"sTitle":"Song"},
        {"sTitle":"Artist"},
        {"sTitle":"Album"},
        {"sTitle":"Playlist"},
        {"sTitle":"Show"},
        {"sTitle":"bgcolor", "bVisible":false},
        {"sTitle":"group_id", "bVisible":false}];

function createDataGrid(){
    	
	columns[1]["fnRender"] = getDateText;
	columns[2]["fnRender"] = getTimeText;
	columns[3]["fnRender"] = getTimeText;
	columns[4]["fnRender"] = changeTimePrecisionInit;

	$('#demo').html( '<table cellpadding="0" cellspacing="0" border="0" class="datatable" id="nowplayingtable"></table>' );
	$('#nowplayingtable').dataTable( {
		"bSort" : false,
		"bJQueryUI": true,
		"bFilter": false,
		"bInfo": false,
		"bLengthChange": false,
        "bPaginate": false,
		"aaData": datagridData.rows,
		"aoColumns": columns,
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
            if (aData[aData.length-2] != "")
                $(nRow).attr("style", "background-color:#166622");
            if (aData[0] == "c")
				$(nRow).attr("style", "background-color:#61B329");
            else if (aData[0] == "b")
                $(nRow).attr("style", "background-color:#EE3B3B");
			return nRow;
		}
	} );
    
    setTimeout(init2, 5000);
}

var viewType = "now" //"day";
function setViewType(type){
    if (type == 0){
        viewType = "now";
    } else {
        viewType = "day";
    }
    init2();
}

function init2(){
	  $.ajax({ url: "/Nowplaying/get-data-grid-data/format/json/view/" + viewType, dataType:"json", success:function(data){
		datagridData = data.entries;
        createDataGrid();
	  }});
	  
	  if (typeof registerSongEndListener == 'function' && !registered){
		  registered = true;
		  registerSongEndListener(notifySongEnd);
	  }
}

$(document).ready(function() {
	init2();
});
