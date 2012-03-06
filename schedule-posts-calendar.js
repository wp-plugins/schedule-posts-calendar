function AddCalendar()
{
	// Find the timesteampdiv <div> in the current page.
	var parent = document.getElementById('timestampdiv');
	
	// If we didn't find the parent, don't bother doing anything else.
	if( parent )
		{
		// Create a new div element and setup it's style and id to be inserted.
		var elmnt = document.createElement("div");
		elmnt.setAttribute('id', 'calendarHere');
		elmnt.setAttribute('style', 'position:relative;height:250px;');

		// Insert the div we just created in to the current page as the first child under 'timestampdiv'. 
		parent.insertBefore(elmnt,parent.firstChild);

		// Setup a date object to use to set the inital calendar date to display from the values in the WordPress controls.
		var startingDate = new Date();
		startingDate.setDate(document.getElementById('jj').value);
		startingDate.setMonth(document.getElementById('mm').selectedIndex);
		startingDate.setFullYear(document.getElementById('aa').value);
		startingDate.setHours(document.getElementById('hh').value);
		startingDate.setMinutes(document.getElementById('mn').value);
		
		// Finally create the calendar and replace the <div> we inserted earlier with the proper calendar control.  Also, set the calendar display properties and then finnally show the control.
		myCalendar = new dhtmlXCalendarObject("calendarHere");
		myCalendar.setWeekStartDay(7);
		myCalendar.setDate(startingDate);
		myCalendar.setSkin('omega');
		myCalendar.show();
		
		// We have to attach two events to the calendar to catch when the user clicks on a new date or time.  They both do the exactly same thing, but the first catches the date change and the second the time change.
		var myEvent = myCalendar.attachEvent("onClick", function (selectedDate){
				document.getElementById('mm').selectedIndex = selectedDate.getMonth();
				document.getElementById('jj').value = selectedDate.getDate();
				document.getElementById('aa').value = selectedDate.getFullYear();
				document.getElementById('hh').value = selectedDate.getHours();
				document.getElementById('mn').value = selectedDate.getMinutes();})
		var myEvent = myCalendar.attachEvent("onChange", function (selectedDate){
				document.getElementById('mm').selectedIndex = selectedDate.getMonth();
				document.getElementById('jj').value = selectedDate.getDate();
				document.getElementById('aa').value = selectedDate.getFullYear();
				document.getElementById('hh').value = selectedDate.getHours();
				document.getElementById('mn').value = selectedDate.getMinutes();})
	}
}

// Use an event listerner to add the calendar on a page load instead of .OnLoad as we might otherwise get overwritten by another plugin
window.addEventListener ? window.addEventListener("load",AddCalendar,false) : window.attachEvent && window.attachEvent("onload",AddCalendar);