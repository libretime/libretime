$(document).ready(function() {

    $("#Panel").stickyPanel({
	    topPadding: 1,
	    afterDetachCSSClass: "floated-panel",
	    savePanelSpace: true
    });
});

function adjustDateToServerDate(date, serverTimezoneOffset){
    //date object stores time in the browser's localtime. We need to artificially shift 
    //it to 
    var timezoneOffset = date.getTimezoneOffset()*60*1000;
    
    date.setTime(date.getTime() + timezoneOffset + serverTimezoneOffset*1000);
    
    /* date object has been shifted to artificial UTC time. Now let's
     * shift it to the server's timezone */
    
    return date;
}
