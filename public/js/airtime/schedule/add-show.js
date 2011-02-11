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
	
    var form = $("#add-show-form");

	form.find("h3").click(function(){
        $(this).next().toggle();
    });

    if(!form.find("#add_show_repeats").attr('checked')) {
        form.find("#schedule-show-when > fieldset:last").hide();
    }

    form.find("#add_show_repeats").click(function(){
        form.find("#schedule-show-when > fieldset:last").toggle();
    });

    form.find("#add_show_repeat_type").change(function(){
        if($(this).val() == 2) {
            form.find("#add_show_day_check-label, #add_show_day_check-element").hide();
        }
        else {
            form.find("#add_show_day_check-label, #add_show_day_check-element").show();
        }
    });

    form.find("#add_show_day_check-label").addClass("block-display");
    form.find("#add_show_day_check-element").addClass("block-display clearfix");
    form.find("#add_show_day_check-element label").addClass("wrapp-label");
    form.find("#add_show_day_check-element br").remove();

    form.find("#add_show_no_end").click(function(){
        form.find("#add_show_end_date").toggle();
    });

	createDateInput(form.find("#add_show_start_date"), startDpSelect);
	createDateInput(form.find("#add_show_end_date"), endDpSelect);

    form.find("#add_show_start_time").timepicker();
    form.find("#add_show_duration").timepicker({
        amPmText: ['', ''],
        defaultTime: '01:00' 
    });

	form.find("#add_show_hosts_autocomplete").autocomplete({
		source: findHosts,
		select: autoSelect,
        delay: 200 
	});

	form.find("#schedule-show-style input").ColorPicker({
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


    form.find("#add-show-close")
		.click(function(event){
            event.stopPropagation();
            event.preventDefault();

            var y = $("#schedule_calendar").width();
            var z = $("#schedule-add-show").width();
            $("#schedule_calendar").width(y+z+50);
            $("#schedule_calendar").fullCalendar('render');
			$("#add-show-form").hide();
		});

	form.find("#add-show-submit")
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

            var days = $('#add_show_day_check-element input').map(function() {
                if($(this).attr("checked")) {
                    return $(this).val();
                }
            }).get();

            $.post("/Schedule/add-show", {format: "json", data: data, hosts: hosts, days: days}, function(json){
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
