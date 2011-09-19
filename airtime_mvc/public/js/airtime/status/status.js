function success(data, textStatus, jqXHR){
    var services = data.status.services;

    for (var i=0; i<services.length; i++){
        var children = $("#"+services[i].name).children();
        console.log(services[i].status);
        $($(children[1]).children()[0]).attr("class", services[i].status ? "checked-icon": "not-available-icon");
        $(children[2]).text(services[i].uptime_seconds);
    }
}

function updateStatus(){
    $.getJSON( "api/status/format/json", null, success);
    
}

$(document).ready(function() {
    updateStatus();
    setInterval(updateStatus, 5000);
});
