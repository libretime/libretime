function createDataGrid(datagridData){

    var columnHeaders = [
        { "sTitle": "name" },
        { "sTitle": "date" },
        { "sTitle": "start time" },
        { "sTitle": "end time" }
    ];

	$('#demo').html( '<table cellpadding="0" cellspacing="0" border="0" width="100%" id="nowplayingtable"></table>' );
	$('#nowplayingtable').dataTable( {
		"bSort" : false,
		"bJQueryUI": true,
		"bFilter": false,
		"bInfo": false,
		"bLengthChange": false,
		"aaData": datagridData.rows,
		"aoColumns": columnHeaders
	} );
    
    
      var options1 = [

    	{title:"Menu Item 1 - Go TO www.google.com", action:{type:"gourl",url:"http://www.google.com/"}},
    	{title:"Menu Item 2 - do <b style='color:red;'>nothing</b>"},
    	{title:"Menu Item 3 - submenu", type:"sub", src:[{title:"Submenu 1"},{title:"Submenu 2"},{title:"Submenu 3"}, {title:"Submenu 4 - submenu", type:"sub", src:[{title:"SubSubmenu 1"},{title:"SubSubmenu 2"}]}]},
    	{title:"Menu Item 4 - Js function", action:{type:"fn",callback:"(function(){ alert('THIS IS THE TEST'); })"}}
      ];

    
    var userData = {};

    var effects = { 
    show:"default", //type of show effect
    orientation: "auto", //type of menu orientation - to top, to bottom, auto (to bottom, if doesn't fit on screen - to top)
    xposition:"mouse", // position of menu (left side or right side of trigger element) 
    yposition:"mouse" 
    }
    
    $('#demo').jjmenu('both', options1, userData, effects );
}

function initShowListView(){

       
	  $.ajax({ url: "/Schedule/get-show-data/format/json", dataType:"text", success:function(data){
            $('#json-string').text(data);
	  }});
      
      
     
     $.ajax({ url: "/Schedule/get-show-data/format/json", dataType:"json", success:function(data){
            var temp = data.data;
            var rows = new Array();
            for (var i=0; i<temp.length; i++){
                rows[i] = [temp[i].name.toString(), temp[i].first_show.toString(), temp[i].start_time.toString(), temp[i].end_time.toString()];
                var datagridData = {rows:rows};
                createDataGrid(datagridData);
            }
	  }});

	  	
        
	  //setTimeout(initShowListView, 5000);
}
    
$(document).ready(function() {
	initShowListView();
});
