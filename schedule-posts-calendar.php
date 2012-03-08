<?php
/*
Plugin Name: Schedule Posts Calendar
Version: 2.0
Plugin URI: http://toolstack.com/SchedulePostsCalendar
Author: Greg Ross
Author URI: http://toolstack.com
Description: Adds a javascript calendar to the schedule posts options.

Compatible with WordPress 3+.

Read the accompanying readme.txt file for instructions and documentation.

Copyright (c) 2012 by Greg Ross

This software is released under the GPL v2.0, see license.txt for details
*/

/*
 *	This function is called to add the .css and .js files to the wordpress pages.
 *	It's registered at the end of the file with an add_action() call.
 */
function schedule_posts_calendar() 
	{
	// Find out where our plugin is stored.
	$plugin_url = plugins_url( '', __FILE__ );
	
	// Retreive the options.
	$options = get_option( 'schedule_posts_calendar' );
	
	// Register and enqueue the calendar css files, create a theme string to use later during the javascript inclusion.
	switch( $options['theme'] )
		{
		case 3:
			wp_register_style( 'dhtmlxcalendar_style', $plugin_url . '/skins/dhtmlxcalendar_dhx_web.css' );
			$theme = 'dhx_web';
			break;
		case 2:
			wp_register_style( 'dhtmlxcalendar_style', $plugin_url . '/skins/dhtmlxcalendar_dhx_skyblue.css' );
			$theme = 'dhx_skyblue';
			break;
		default:
			wp_register_style( 'dhtmlxcalendar_style', $plugin_url . '/skins/dhtmlxcalendar_omega.css' );
			$theme = 'omega';
			break;
		}

	wp_register_style( 'dhtmlxcalendar_style', $plugin_url . '/skins/dhtmlxcalendar_omega.css' );
	wp_register_style( 'dhtmlxcalendar', $plugin_url . '/dhtmlxcalendar.css' );
    wp_enqueue_style( 'dhtmlxcalendar_style' );
    wp_enqueue_style( 'dhtmlxcalendar' );
	
	// Register and enqueue the calender scripts.
	wp_register_script( 'dhtmlxcalendar', $plugin_url . '/dhtmlxcalendar.js' );
	wp_register_script( 'schedulepostscalendar', $plugin_url . '/schedule-posts-calendar.js?theme=' . $theme . '&startofweek=' . $options['startofweek'], "dhtmlxcalendar" );
	wp_enqueue_script( 'dhtmlxcalendar' );
	wp_enqueue_script( 'schedulepostscalendar' );
	}

/*
 *	This function is called when you select the admin page for the plugin, it generates the HTML
 *	and is responsible to store the settings.
 */
function schedule_posts_calendar_admin_page()
	{
	if( $_POST['schedule_posts_calendar'] ) 
		{
		if( !isset( $_POST['schedule_posts_calendar']['startofweek'] ) ) { $_POST['schedule_posts_calendar']['startofweek'] = 7; }
		if( !isset( $_POST['schedule_posts_calendar']['theme'] ) ) { $_POST['schedule_posts_calendar']['theme'] = 1; }
			
		update_option( 'schedule_posts_calendar', $_POST['schedule_posts_calendar'] );
		}

		$options = get_option( 'schedule_posts_calendar' );

	//***** Start HTML
	?>
<div class="wrap">
	<form method="post">
	
		<fieldset style="border:1px solid #cecece;padding:15px" >
			<legend><h2>Schedule Posts Calendar Options</h2></legend>

			<div>Start week on: <Select name="schedule_posts_calendar[startofweek]">
<?php
			$daysoftheweek = array( "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday" );
			
			for( $i = 0; $i < 7; $i++ )
				{
				echo "			<option value=" . ($i + 1);
				if( $options['startofweek'] == $i + 1 ) { echo " SELECTED"; }
				echo ">" . $daysoftheweek[$i] . "</option>\r\n";
				}
?>
			</select></div>

			<div>&nbsp;</div>
			
			<div>Calendar theme: <Select name="schedule_posts_calendar[theme]">
<?php
			$themes = array( "Omega", "Sky Blue", "Web" );
			
			for( $i = 0; $i < 3; $i++ )
				{
				echo "			<option value=" . ($i + 1);
				if( $options['theme'] == $i + 1 ) { echo " SELECTED"; }
				echo ">" . $themes[$i] . "</option>\r\n";
				}
?>
			</select></div>

		</fieldset>
			
		<div class="submit"><input type="submit" name="info_update" value="<?php _e('Update Options') ?> &raquo;" /></div>
		
	</form>
</div>
	<?php
	//***** End HTML
	}
	
/*
 *	This function is called to check if we need to add the above .css and .js files
 *	on this page.  ONLY the posts pages need to include the files, all other admin pages
 *	don't need them.
 */
function SCP_Add_Calendar_Includes()
	{
	// First check to make sure we have a server variable set to the script name, if we
	// don't fall back to including the .css and .js files on all admin pages.
	if(isset($_SERVER['SCRIPT_NAME']) )
		{
		// Grab the lower case base name of the script file.
		$pagename = strtolower(basename($_SERVER['SCRIPT_NAME'], ".php"));
		
		// There are only two pages we really need to include the files on, so
		// use a switch to make it easier for later if we need to add more page
		// names to the list.
		switch( $pagename )
			{
			case "post":
			case "post-new":
				return true;
			default:
				return false;
			}
		}
	else
		{
		return true;
		}
	}

function schedule_posts_calendar_admin()
	{
	add_options_page( 'Schedule Posts Calendar', 'Schedule Posts Calendar', 9, basename( __FILE__ ), 'schedule_posts_calendar_admin_page');
	}	

// Time to register the .css and .js pages, if we need to of course ;)
if( SCP_Add_Calendar_Includes() ) { add_action( 'admin_init', 'schedule_posts_calendar' ); }

// Now add the admin menu items
if ( is_admin() ) { add_action( 'admin_menu', 'schedule_posts_calendar_admin', 1 ); }
?>