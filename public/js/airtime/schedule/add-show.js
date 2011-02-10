/**
*
*	Schedule Dialog creation methods.
*
*/

//dateText mm-dd-yy
function startDpSelect(dateText, inst) {
	var time, date;

	time = dateText.split("-");
	date = new Date(time[0], time[1] - 1, time[2]);

	$("#add_show_end_date").datepicker("option", "minDate", date);
}

function endDpSelect(dateText, inst) {
	var time, date;

	time = dateText.split("-");
	date = new Date(time[0], time[1] - 1, time[2]);

	$("#add_show_start_date").datepicker( "option", "maxDate", date);
}

function createDateInput(el, onSelect) {
	var date;

	el.datepicker({
			minDate: new Date(),
			onSelect: onSelect,
			dateFormat: 'yy-mm-dd' 
		});

	date = $.datepicker.formatDate("yy-mm-dd", new Date());
	el.val(date);
}

function autoSelect(event, ui) {

	$("#add_show_hosts-"+ui.item.value).attr("checked", "checked");
	event.preventDefault();
}

function findHosts(request, callback) {
	var search, url;

	url = "/User/get-hosts";
	search = request.term;

	$.post(url, 
		{format: "json", term: search}, 
		
		function(json) {
			callback(json.hosts);
		});
	
}

function setAddShowEvents() {
	
	$("h3").click(function(){
        $(this).next().toggle();
    });

    if(!$("#add_show_repeats").attr('checked')) {
        $("#schedule-show-when > fieldset:last").hide();
    }

    $("#add_show_repeats").click(function(){
        $("#schedule-show-when > fieldset:last").toggle();
    });

    $("#add_show_repeat_type").change(function(){
        if($(this).val() == 2) {
            $("#add_show_day_check-label, #add_show_day_check-element").hide();
        }
        else {
            $("#add_show_day_check-label, #add_show_day_check-element").show();
        }
    });

    $("#add_show_no_end").click(function(){
        $("#add_show_end_date").toggle();
    });

	createDateInput($("#add_show_start_date"), startDpSelect);
	createDateInput($("#add_show_end_date"), endDpSelect);

    $("#add_show_start_time").timepicker();
    $("#add_show_duration").timepicker({
        amPmText: ['', ''] 
    });

	$("#add_show_hosts_autocomplete").autocomplete({
		source: findHosts,
		select: autoSelect,
        delay: 200 
	});

	$("#schedule-show-style input").ColorPicker({
       onChange: function (hsb, hex, rgb, el) {
		    $(el).val(hex);
	    },
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	});


    $("#add-show-close")
		.click(function(event){
            event.stopPropagation();
            event.preventDefault();

            var y = $("#schedule_calendar").width();
            var z = $("#schedule-add-show").width();
            $("#schedule_calendar").width(y+z+50);
            $("#schedule_calendar").fullCalendar('render');
			$("#add-show-form").hide();
		});

	$("#add-show-submit")
		.button()
		.click(function(event){
            event.preventDefault();

			var data = $("form").serializeArray();
            var string = $("form").serialize();

            var hosts = $('#add_show_hosts-element input').map(function() {
                if($(this).attr("checked")) {
                    return $(this).val();
                }
            }).get();

            $.post("/Schedule/add-show", {format: "json", data: data, hosts: hosts}, function(json){
                if(json.form) {
                    $("#add-show-form")
                        .empty()
                        .append(json.form);

                    setAddShowEvents();
                    showErrorSections();    
                }
                else {
                     $("#add-show-form")
                        .empty()
                        .append(json.newForm);

                    setAddShowEvents();
                    scheduleRefetchEvents();
                }
            });
		});
}

function showErrorSections() {

    if($("#schedule-show-when .errors").length > 0) {
        $("#schedule-show-when").show();
    }
    if($("#schedule-show-who .errors").length > 0) {
        $("#schedule-show-who").show();
    }
    if($("#schedule-show-style .errors").length > 0) {
        $("#schedule-show-style").show();
    }
}

$(document).ready(function() {

	setAddShowEvents();
});
