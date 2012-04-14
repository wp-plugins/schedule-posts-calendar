function GetScriptIndex(name)
{
	// Loop through all the scripts in the current document to find the one we want.
	for( i = 0; i < document.scripts.length; i++) 
		{
		// Make a temporary copy of the URI and find out where the query string starts.
		var tmp_src = String(document.scripts[i].src);
		var qs_index = tmp_src.indexOf('?');

		// Check if the script is the script we are looking for and if it has a QS, if so return the current index.
		if( tmp_src.indexOf(name) >= 0 && qs_index >= 0)
			{
			return i;
			}
		
		}
		
	return -1;
}

function GetScriptVariable(index, name, vardef)
{
	// If a negitive index has been passed in it's because we didn't find any matching script with a query
	// string, so just return the default value.
	if( index < 0 )
		{
		return vardef;
		}

	// Make a temporary copy of the URI and find out where the query string starts.
	var tmp_src = String(document.scripts[index].src);
	var qs_index = tmp_src.indexOf('?');

	// Split the query string ino var/value pairs.  ie: 'var1=value1', 'var2=value2', ...
	var params_raw = tmp_src.substr(qs_index + 1).split('&');

	// Now look for the one we want.
	for( j = 0; j < params_raw.length; j++)
		{
		// Split names from the values.
		var pp_raw = params_raw[j].split('=');

		// If this is the one we're looking for, simply return it.
		if( pp_raw[0] == name )
			{
			// Check to make sure a value was actualy passed in, otherwise we should return the default later on.
			if( typeof(pp_raw[1]) != 'undefined' )
				{
				return pp_raw[1];
				}
			}
		}

	// If we fell through the loop and didn't find ANY matching variable, simply return the default value.
	return vardef;
}

function AddCalendar(sDay, sMon, sYear, sHour, sMin, id)
{
	// Find the timesteampdiv <div> in the current page.
	var parent = document.getElementById('calendarHere-' + id);
	
	// If we didn't find the parent, don't bother doing anything else.
	if( parent )
		{
		// Retrive the script options from the URI
		var GSI = GetScriptIndex('schedule-posts-calendar.js');
		var startOfWeek = GetScriptVariable(GSI, 'startofweek', 7);
		var theme = GetScriptVariable(GSI, 'theme', 'omega');
		var popupCalendar = GetScriptVariable(GSI, 'popupcalendar', 0);

		// Setup a date object to use to set the inital calendar date to display from the values in the WordPress controls.
		var startingDate = new Date();
		startingDate.setDate(sDay);
		startingDate.setMonth(sMon);
		startingDate.setFullYear(sYear);
		startingDate.setHours(sHour);
		startingDate.setMinutes(sMin);


		// Finally create the calendar and replace the <div>/<input> we inserted earlier with the proper calendar control.  Also, set the calendar display properties and then finnally show the control.
		myCalendar = new dhtmlXCalendarObject("calendarHere-" + id);
		myCalendar.setWeekStartDay(startOfWeek);
		myCalendar.setDate(startingDate);
		myCalendar.setSkin(theme);
		myCalendar.setDateFormat('%d/%m/%Y %H:%i');

		myCalendar.show();
		
		
		// We have to attach two events to the calendar to catch when the user clicks on a new date or time.  They both do the exactly same thing, but the first catches the date change and the second the time change.
		var myEvent = myCalendar.attachEvent("onClick", function (selectedDate){
				document.getElementById('eis_date_value_' + id).value = eis_format_date( selectedDate.getDate(), selectedDate.getMonth()+1, selectedDate.getFullYear(), selectedDate.getHours(), selectedDate.getMinutes() );
				})
		var myEvent = myCalendar.attachEvent("onChange", function (selectedDate){
				document.getElementById('eis_date_value_' + id).value = eis_format_date( selectedDate.getDate(), selectedDate.getMonth()+1, selectedDate.getFullYear(), selectedDate.getHours(), selectedDate.getMinutes() );
				})
	}
}

function eis_format_date(sDay, sMon, sYear, sHour, sMin)
	{
	var dateString = '';
	if( sDay.toString().length < 2 ) { dateString += '0'; }
	dateString += sDay + '/';
	if( sMon.toString().length < 2 ) { dateString += '0'; }
	dateString += sMon + '/' + sYear + ' ';
	if( sHour.toString().length < 2 ) { dateString += '0'; }
	dateString += sHour + ':';
	if( sMin.toString().length < 2 ) { dateString += '0'; }
	dateString += sMin;
	
	return dateString;
	}
	
function schedule_posts_calendar_quick_schedule_update(id)
	{
	var new_date_string = document.getElementById('eis_date_value_' + id).value.trim();
	var new_date_split = new_date_string.split(' ');
	var new_date_parts = new_date_split[0].split('/');
	var new_time_parts = new_date_split[1].split(':');
	
	document.getElementById('eis_month_' + id).innerHTML = new_date_parts[1].trim();
	document.getElementById('eis_day_' + id).innerHTML = new_date_parts[0].trim();
	document.getElementById('eis_year_' + id).innerHTML =  new_date_parts[2].trim();
	document.getElementById('eis_hour_' + id).innerHTML = new_time_parts[0].trim();
	document.getElementById('eis_minute_' + id).innerHTML = new_time_parts[1].trim();

	var $jq = jQuery.noConflict();
	var seed_params, params, fields, page = $jq('.post_status_page').val() || '';

	if ( typeof(id) == 'object' )
		id = this.getId(id);

	$jq('table.widefat .inline-edit-save .waiting').show();

	seed_params = {
		action: 'inline-save',
		post_type: typenow,
		post_ID: id,
		edit_date: 'true',
		post_status: page
	};

	fields = "mm=" + new_date_parts[1].trim() + "&aa=" + new_date_parts[2].trim() + "&jj=" + new_date_parts[0].trim() + "&mn=" + new_time_parts[1].trim() + "&hh=" + new_time_parts[1].trim() + "&_inline_edit=" + document.getElementById('_inline_edit').value + "&post_ID=" + id;
		
	params = fields + '&' + $jq.param(seed_params);

	// make ajax request
	$jq.post('admin-ajax.php', params,
		function(r) {
			$jq('table.widefat .inline-edit-save .waiting').hide();

			if (r) {
				if ( -1 != r.indexOf('<tr') ) {
					schedule_posts_calendar_quick_schedule_cancel(id);
				}
			}
		}
	, 'html');

	}
	
function schedule_posts_calendar_quick_schedule_cancel(id)
	{
	// Find the table row we're editing.
	var show_row = document.getElementById('post-' + id);
	show_row.style.display = "";

	var table = show_row.parentElement;	

	for(i=0; i<table.rows.length; i++ )
		{
		if( table.rows[i].id == "editinlineschedule" )
			{
			table.deleteRow(i);
			i=table.rows.length;
			}
		}
	}

function schedule_posts_calendar_quick_schedule_edit(id)
	{
	// Find the table row we're editing.
	var edit_row = document.getElementById('post-' + id);
	var edit_table = edit_row.parentElement;
	
	var new_row = edit_table.insertRow( edit_row.rowIndex - 1 );
	var new_cell = new_row.insertCell(0);
	
	new_row.id = "editinlineschedule";
	new_row.className = "inline-edit-row inline-edit-row-post inline-edit-post quick-edit-row quick-edit-row-post inline-edit-post"
	new_cell.colSpan = edit_row.children.length;
		
	var row_data = document.getElementById('inline_' + id).children;
	var month, day, year, hour, minute, title;

	for( i=0; i < row_data.length; i++ )
	{
		switch( row_data[i].className )
		{
		case "mm":
			month = row_data[i].innerHTML;
			row_data[i].id = 'eis_month_' + id
			break;
		case "aa":
			year = row_data[i].innerHTML;
			row_data[i].id = 'eis_year_' + id
			break;
		case "jj":
			day = row_data[i].innerHTML;
			row_data[i].id = 'eis_day_' + id
			break;
		case "mn":
			minute = row_data[i].innerHTML;
			row_data[i].id = 'eis_minute_' + id
			break;
		case "hh":
			hour = row_data[i].innerHTML;
			row_data[i].id = 'eis_hour_' + id
			break;
		case "post_title":
			title = row_data[i].innerHTML;
			break;
		}
	}
	
	new_cell.innerHTML = "<h4>Quick Schedule - " + title + "</h4>";
	new_cell.innerHTML += "<input style='display:none' id='eis_date_value_" + id + "' value='" + day + "/" + month + "/" + year + " " + hour + ":" + minute + "'></input>";
	new_cell.innerHTML += "<div id='calendarHere-" + id + "' style='position:relative;height:230px;'></div>";
	new_cell.innerHTML += '<a accesskey="c" href="#" title="Cancel" class="button-secondary cancel alignleft" onclick="schedule_posts_calendar_quick_schedule_cancel(' + id + ')">Cancel</a>';
	new_cell.innerHTML += '<a accesskey="s" href="#" title="Update" class="button-primary save alignleft" onclick="schedule_posts_calendar_quick_schedule_update(' + id + ')" style="margin-left:70px">Update</a>';
	new_cell.innerHTML += "<BR>&nbsp;";
	
	AddCalendar(day, month-1, year, hour, minute, id);
	
	edit_row.style.display = "none";
	}
	
