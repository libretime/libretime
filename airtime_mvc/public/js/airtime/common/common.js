$(document).ready(function() {

    //this statement tells the browser to fade out any success message after 5 seconds
    setTimeout(function(){$(".success").fadeOut("slow", function(){$(this).empty()});}, 5000);
});

/*
 * i18n_months and i18n_days_short are used in jquery datepickers
 * which we use in multiple places
 */
var i18n_months = [
    $.i18n._("January"),
    $.i18n._("February"),
    $.i18n._("March"),
    $.i18n._("April"),
    $.i18n._("May"),
    $.i18n._("June"),
    $.i18n._("July"),
    $.i18n._("August"),
    $.i18n._("September"),
    $.i18n._("October"),
    $.i18n._("November"),
    $.i18n._("December")
];

var i18n_months_short = [
    $.i18n._("Jan"),
    $.i18n._("Feb"),
    $.i18n._("Mar"),
    $.i18n._("Apr"),
    $.i18n._("May"),
    $.i18n._("Jun"),
    $.i18n._("Jul"),
    $.i18n._("Aug"),
    $.i18n._("Sep"),
    $.i18n._("Oct"),
    $.i18n._("Nov"),
    $.i18n._("Dec")
];

var i18n_days_short = [
    $.i18n._("Su"),
    $.i18n._("Mo"),
    $.i18n._("Tu"),
    $.i18n._("We"),
    $.i18n._("Th"),
    $.i18n._("Fr"),
    $.i18n._("Sa"),
];

//set jQuery datepicker to match Airtime date format.
$.datepicker.regional[""].dateFormat = 'yy-mm-dd';
$.datepicker.regional[""].monthNames = i18n_months,
$.datepicker.regional[""].dayNamesMin = i18n_days_short,
$.datepicker.regional[""].closeText = $.i18n._('Close'),
$.datepicker.setDefaults($.datepicker.regional[""]);

function adjustDateToServerDate(date, serverTimezoneOffset){
    //date object stores time in the browser's localtime. We need to artificially shift 
    //it to 
    var timezoneOffset = date.getTimezoneOffset()*60*1000;
    
    date.setTime(date.getTime() + timezoneOffset + serverTimezoneOffset*1000);
    
    /* date object has been shifted to artificial UTC time. Now let's
     * shift it to the server's timezone */
    return date;
}

function pad(number, length) {
    return sprintf("%'0"+length+"d", number);
}

function removeSuccessMsg() {
    var $status = $('.success');
    
    $status.fadeOut("slow", function(){$status.empty()});
}
