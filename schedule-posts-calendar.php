<?php
/*
Plugin Name: Schedule Posts Calendar
Version: 2.1
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
 *	This function is called to add the .css and .js files for the calendar to 
 *  the wordpress pages.
 *	It's registered at the end of the file with an add_action() call.
 */
function schedule_posts_calendar_add_cal($theme_num, $url) 
	{
	// Register and enqueue the calendar css files, create a theme string to use later during the javascript inclusion.
	switch( $theme_num )
		{
		case 3:
			wp_register_style( 'dhtmlxcalendar_style', $url . '/skins/dhtmlxcalendar_dhx_web.css' );
			$theme = 'dhx_web';
			break;
		case 2:
			wp_register_style( 'dhtmlxcalendar_style', $url . '/skins/dhtmlxcalendar_dhx_skyblue.css' );
			$theme = 'dhx_skyblue';
			break;
		default:
			wp_register_style( 'dhtmlxcalendar_style', $url . '/skins/dhtmlxcalendar_omega.css' );
			$theme = 'omega';
			break;
		}

	wp_register_style( 'dhtmlxcalendar_style', $url . '/skins/dhtmlxcalendar_omega.css' );
	wp_register_style( 'dhtmlxcalendar', $url . '/dhtmlxcalendar.css' );
    wp_enqueue_style( 'dhtmlxcalendar_style' );
    wp_enqueue_style( 'dhtmlxcalendar' );

	// Register and enqueue the calender scripts.
	wp_register_script( 'dhtmlxcalendar', $url . '/dhtmlxcalendar.js' );
	wp_enqueue_script( 'dhtmlxcalendar' );
	}

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
	schedule_posts_calendar_add_cal( $options['theme'], $plugin_url );
	
	// Add the css file that will hide the default WordPress timestamp field.
	if( $options['hide-timestamp'] == 1 )
		{
		wp_register_style( 'hide-timestamp', $plugin_url . '/hide-timestamp.css' );
		wp_enqueue_style( 'hide-timestamp' );
		}
	
	// Register and enqueue the calender scripts.
	wp_register_script( 'schedulepostscalendar', $plugin_url . '/schedule-posts-calendar.js?theme=' . $theme . '&startofweek=' . $options['startofweek'] . '&popupcalendar=' . $options['popup-calendar'], "dhtmlxcalendar" );
	wp_enqueue_script( 'schedulepostscalendar' );
	}

/*
 *	This function is called to add the .css and .js files to the wordpress list pages.
 *	It's registered at the end of the file with an add_action() call.
 */
function schedule_posts_calendar_quick_schedule() 
	{
	// Find out where our plugin is stored.
	$plugin_url = plugins_url( '', __FILE__ );
	
	// Retreive the options.
	$options = get_option( 'schedule_posts_calendar' );

	// Register and enqueue the calendar css files, create a theme string to use later during the javascript inclusion.
	schedule_posts_calendar_add_cal( $options['theme'], $plugin_url );

	// Register and enqueue the calender scripts.
	wp_register_script( 'schedulepostscalendar', $plugin_url . '/schedule-posts-calendar-quick-schedule.js?theme=' . $theme . '&startofweek=' . $options['startofweek'] . '&popupcalendar=' . $options['popup-calendar'], "dhtmlxcalendar" );
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
		if( !isset( $_POST['schedule_posts_calendar']['hide-timestamp'] ) ) { $_POST['schedule_posts_calendar']['hide-timestamp'] = 0; }
		if( !isset( $_POST['schedule_posts_calendar']['popup-calendar'] ) ) { $_POST['schedule_posts_calendar']['popup-calendar'] = 0; }
			
		update_option( 'schedule_posts_calendar', $_POST['schedule_posts_calendar'] );
		
		print "<div id='setting-error-settings_updated' class='updated settings-error'><p><strong>Settings saved.</strong></p></div>\n";
		}

		$options = get_option( 'schedule_posts_calendar' );

	//***** Start HTML
	?>
<div class="wrap">
	
	<fieldset style="border:1px solid #cecece;padding:15px; margin-top:25px" >
		<legend><span style="font-size: 24px; font-weight: 700;">Schedule Posts Calendar Options</span></legend>
		<form method="post">
			<div><?php _e('Start week on');?>: <Select name="schedule_posts_calendar[startofweek]">
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
			
			<div><?php _e('Calendar theme');?>: <Select name="schedule_posts_calendar[theme]">
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

			<div>&nbsp;</div>
			
			<div><input name="schedule_posts_calendar[hide-timestamp]" type="checkbox" value="1" <?php checked($options['hide-timestamp'], 1); ?> /> <?php _e("Hide WordPress's default time stamp display"); ?></div>

			<div>&nbsp;</div>
			
			<div><input name="schedule_posts_calendar[popup-calendar]" type="checkbox" value="1" <?php checked($options['popup-calendar'], 1); ?> /> <?php _e("Use a popup calendar instead of an inline one (you probably want to hide the default dispaly above)"); ?></div>

			<div class="submit"><input type="submit" name="info_update" value="<?php _e('Update Options') ?> &raquo;" /></div>
			
		</form>
	
	</fieldset>
		
	<fieldset style="border:1px solid #cecece;padding:15px; margin-top:25px" >
			<legend><span style="font-size: 24px; font-weight: 700;">About</span></legend>
			<p>Schedule Posts Calendar Version 2.1</p>
			<p>by Greg Ross</p>
			<p>&nbsp;</p>
			<p>Licenced under the <a href="http://www.gnu.org/licenses/gpl-2.0.html" target=_blank>GPL Version 2</a></p>
			<p>Visit the plug-in site at <a href="http://ToolStack.com/SchedulePostsCalendar" target=_blank>ToolStack.com</a>!</p>
	</fieldset>
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
				return "schedule_posts_calendar";
			case "edit":
				return "schedule_posts_calendar_quick_schedule";
			default:
				return "";
			}
		}
	else
		{
		return true;
		}
	}

/*
 *	This function is called to add the options page to the settings menu.
 *	It's registered at the end of the file with an add_action() call.
 */	
function schedule_posts_calendar_admin()
	{
	add_options_page( 'Schedule Posts Calendar', 'Schedule Posts Calendar', 9, basename( __FILE__ ), 'schedule_posts_calendar_admin_page');
	}	

/**
 * Add the link to action list for post_row_actions.
 */
function schedule_posts_calendar_link_row($actions, $post) 
	{
	$actions['schedule'] = '<a href="#" class="editinlineschedule" title="Schedule this item" onClick="schedule_posts_calendar_quick_schedule_edit(' . $post->ID . ');">Schedule</a>';
		
	return $actions;
	}

/**
 * Add the link to settings from the plugin list.
 */
function schedule_posts_calendar_plugin_actions( $actions, $plugin_file, $plugin_data, $context ) 
	{
	array_unshift( $actions, '<a href="' . admin_url() . 'options-general.php?page=schedule-posts-calendar.php">Settings</a>' );
	
	return $actions;
	}
	
// Time to register the .css and .js pages, if we need to of course ;)

// First find out if we're in a post/page list, in a post/page edit page or somewhere we don't care about.
$fname = SCP_Add_Calendar_Includes();

// If we're somewhere we care about, do the admin_init action.
if( $fname <> "" ) { add_action( 'admin_init', $fname ); }

// If we're in the post/page list, add the quick schedule menu itmes.
if( $fname == "schedule_posts_calendar_quick_schedule" )
{
	add_filter('post_row_actions', 'schedule_posts_calendar_link_row',10,2);
	add_filter('page_row_actions', 'schedule_posts_calendar_link_row',10,2);
}

// Now add the admin menu items
if ( is_admin() ) 
	{ 
	add_action( 'admin_menu', 'schedule_posts_calendar_admin', 1 ); 
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'schedule_posts_calendar_plugin_actions', 10, 4);
	}


?>