<?php
/*
Plugin Name: Schedule Posts Calendar
Version: 1.1
Plugin URI: http://toolstack.com/SchedulePostsCalendar
Author: Greg Ross
Author URI: http://toolstack.com
Description: Adds a javascript calendar to the schedule posts options.

Compatible with WordPress 3+.

Read the accompanying readme.txt file for instructions and documentation.

Copyright (c) 2012 by Greg Ross

This software is released under the GPL v2.0, see license.txt for details
*/

define( 'SPC_VER', '1.0' );

/*
 *	This function is called to add the .css and .js files to the wordpress pages.
 *	It's registered at the end of the file with an add_action() call.
 */
function schedule_posts_calendar() 
	{
	// Find out where our plugin is stored.
	$plugin_url = plugins_url('', __FILE__);
	
	// Register and enqueue the calendar css files.
	wp_register_style( 'dhtmlxcalendar_style', $plugin_url . '/skins/dhtmlxcalendar_omega.css' );
	wp_register_style( 'dhtmlxcalendar', $plugin_url . '/dhtmlxcalendar.css' );
    wp_enqueue_style( 'dhtmlxcalendar_style' );
    wp_enqueue_style( 'dhtmlxcalendar' );
	
	// Register and enqueu the calender scripts.
	wp_register_script( 'dhtmlxcalendar', $plugin_url . '/dhtmlxcalendar.js' );
	wp_register_script( 'schedulepostscalendar', $plugin_url . '/schedule-posts-calendar.js', "dhtmlxcalendar" );
	wp_enqueue_script( 'dhtmlxcalendar' );
	wp_enqueue_script( 'schedulepostscalendar' );
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

// Time to register the .css and .js pages, if we need to of course ;)
if( SCP_Add_Calendar_Includes() ) { add_action( 'admin_init', 'schedule_posts_calendar' ); }
?>