var registered = false;
var datagridData;

function getDateText(obj){
	var str = obj.aData[ obj.iDataColumn ].toString();
	datetime = str.split(" ");
    if (datetime.length == 2)
        return datetime[0];
	return str;
}

function getTimeText(obj){
	var str = obj.aData[ obj.iDataColumn ].toString();
	datetime = str.split(" ");
    if (datetime.length == 2)
        return changeTimePrecision(datetime[1]);
	return str;
}

function changeTimePrecisionInit(obj){
	var str = obj.aData[ obj.iDataColumn ].toString();
	return changeTimePrecision(str);
}

function changeTimePrecision(str){
    
    var temp = str.split(".")
    if (temp.length == 2){
        if (temp[1].length > 2)
            return temp[0]+"."+temp[1].substr(0, 2);
    }
    return str;
}

function notifySongEnd(){
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
            if (aData[aData.length-2] == "t")
                $(nRow).attr("style", "background-color:#166622");
            if (aData[0] == "c")
				$(nRow).attr("style", "background-color:#61B329");
            else if (aData[0] == "b")
                $(nRow).attr("style", "background-color:#EE3B3B");
			return nRow;
		}
	} );
    

}

function getDateString(){
    var date0 = $("#datepicker").datepicker("getDate");
    return (date0.getFullYear() + "-" + (parseInt(date0.getMonth())+1) + "-" + date0.getDate());
}

function getAJAXURL(){
    var url = "/Nowplaying/get-data-grid-data/format/json/view/"+viewType;
    
    if (viewType == "day"){
      url +=  "/date/" + getDateString();
    }
    
    return url;
}

function updateData(){
       $.ajax({ url: getAJAXURL(), dataType:"json", success:function(data){
		datagridData = data.entries;
        createDataGrid();
	  }});   
}

function init2(){	        
      updateData();	  

	  if (typeof registerSongEndListener == 'function' && !registered){
		  registered = true;
		  registerSongEndListener(notifySongEnd);
	  }

      setTimeout(init2, 5000);

}

function redirect(url){
    document.location.href = url;
}

$(document).ready(function() {
    if (viewType == "day"){
        $('#now_view').click(function(){redirect('/Nowplaying/index')});
        
        $("#datepicker").datepicker({
            onSelect: function(dateText, inst) 
                { updateData();}});
        $("#datepicker").datepicker("setDate", new Date());
    } else {
        $('#day_view').click(function(){redirect('/Nowplaying/day-view')});
    }

    init2();
});
