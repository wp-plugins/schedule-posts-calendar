<?php
/*
Plugin Name: Schedule Posts Calendar
Version: 3.6
Plugin URI: http://toolstack.com/SchedulePostsCalendar
Author: Greg Ross
Author URI: http://toolstack.com
Description: Adds a JavaScript calendar to the schedule posts options.

Compatible with WordPress 3+.

Read the accompanying readme.txt file for instructions and documentation.

Copyright (c) 2012-13 by Greg Ross

This software is released under the GPL v2.0, see license.txt for details
*/

/*
 	This function is called to add the .css and .js files for the calendar to 
    the WordPress pages.
	
 	It's registered at the end of the file with an add_action() call.
*/
function schedule_posts_calendar_add_cal($theme_num, $url) 
	{
	// Register and enqueue the calendar css files, create a theme string to use later during the JavaScript inclusion.
	switch( $theme_num )
		{
		case 2:
			wp_register_style( 'dhtmlxcalendar_style', $url . '/skins/dhtmlxcalendar_dhx_skyblue.css' );
			break;
		case 3:
			wp_register_style( 'dhtmlxcalendar_style', $url . '/skins/dhtmlxcalendar_dhx_web.css' );
			break;
		case 4:
			wp_register_style( 'dhtmlxcalendar_style', $url . '/skins/dhtmlxcalendar_dhx_terrace.css' );
			break;
		default:
			wp_register_style( 'dhtmlxcalendar_style', $url . '/skins/dhtmlxcalendar_omega.css' );
			break;
		}

	wp_register_style( 'dhtmlxcalendar', $url . '/dhtmlxcalendar.css' );
	wp_enqueue_style( 'dhtmlxcalendar' );
	wp_enqueue_style( 'dhtmlxcalendar_style' );

	// Register and enqueue the calender scripts.
	wp_register_script( 'dhtmlxcalendar', $url . '/dhtmlxcalendar.js' );
	wp_enqueue_script( 'dhtmlxcalendar' );
	}

/*
 	This function is called to add the .css and .js files to the WordPress pages.
 	It's registered at the end of the file with an add_action() call.
*/
function schedule_posts_calendar() 
	{
	// Find out where our plugin is stored.
	$plugin_url = plugins_url( '', __FILE__ );
	
	// Retrieve the options.
	$options = get_option( 'schedule_posts_calendar' );
	
	if( !isset($options['theme']) ) { $options['theme'] = 4; }
	
	// Register and enqueue the calendar css files, create a theme string to use later during the JavaScript inclusion.
	schedule_posts_calendar_add_cal( $options['theme'], $plugin_url );
	
	// Add the css file that will hide the default WordPress timestamp field.
	if( $options['hide-timestamp'] == 1 )
		{
		wp_register_style( 'hide-timestamp', $plugin_url . '/hide-timestamp.css' );
		wp_enqueue_style( 'hide-timestamp' );
		}
	
	// Register and enqueue the calender scripts.
	wp_register_script( 'schedulepostscalendar', $plugin_url . '/schedule-posts-calendar.js?theme=' . $options['theme'] . '&startofweek=' . $options['startofweek'] . '&popupcalendar=' . $options['popup-calendar'], "dhtmlxcalendar" );
	wp_enqueue_script( 'schedulepostscalendar' );
	}

/*
 	This function is called to add the .css and .js files to the WordPress list pages.
 	It's registered at the end of the file with an add_action() call.
*/
function schedule_posts_calendar_quick_schedule() 
	{
	// Find out where our plugin is stored.
	$plugin_url = plugins_url( '', __FILE__ );
	
	// Retrieve the options.
	$options = get_option( 'schedule_posts_calendar' );

	// Register and enqueue the calendar css files, create a theme string to use later during the JavaScript inclusion.
	schedule_posts_calendar_add_cal( $options['theme'], $plugin_url );

	// Register and enqueue the calender scripts.
	wp_register_script( 'schedulepostscalendar', $plugin_url . '/schedule-posts-calendar-quick-schedule.js?theme=' . $options['theme'] . '&startofweek=' . $options['startofweek'] . '&popupcalendar=' . $options['popup-calendar'], "dhtmlxcalendar" );
	wp_enqueue_script( 'schedulepostscalendar' );
	}

/*
 	This function is called when you select the admin page for the plugin, it generates the HTML
 	and is responsible to store the settings.
*/
function schedule_posts_calendar_admin_page()
	{
	if( $_POST['schedule_posts_calendar'] ) 
		{
		if( !isset( $_POST['schedule_posts_calendar']['startofweek'] ) ) { $_POST['schedule_posts_calendar']['startofweek'] = 7; }
		if( !isset( $_POST['schedule_posts_calendar']['theme'] ) ) { $_POST['schedule_posts_calendar']['theme'] = 4; }
		if( !isset( $_POST['schedule_posts_calendar']['hide-timestamp'] ) ) { $_POST['schedule_posts_calendar']['hide-timestamp'] = 0; }
		if( !isset( $_POST['schedule_posts_calendar']['popup-calendar'] ) ) { $_POST['schedule_posts_calendar']['popup-calendar'] = 0; }
			
		update_option( 'schedule_posts_calendar', $_POST['schedule_posts_calendar'] );
		
		print "<div id='setting-error-settings_updated' class='updated settings-error'><p><strong>Settings saved.</strong></p></div>\n";
		}

		$options = get_option( 'schedule_posts_calendar' );

		if( !isset( $options['startofweek'] ) ) { $options['startofweek'] = 7; }
		if( !isset( $options['theme'] ) ) { $options['theme'] = 4; }
		if( !isset( $options['hide-timestamp'] ) ) { $options['hide-timestamp'] = 0; }
		if( !isset( $options['popup-calendar'] ) ) { $options['popup-calendar'] = 0; }
		
	//***** Start HTML
	?>
<div class="wrap">
	
	<fieldset style="border:1px solid #cecece;padding:15px; margin-top:25px" >
		<legend><span style="font-size: 24px; font-weight: 700;">&nbsp;Schedule Posts Calendar Options&nbsp;</span></legend>
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
			$themes = array( "Omega", "Sky Blue", "Web", "Terrace" );
			
			for( $i = 0; $i < 4; $i++ )
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
		<legend><span style="font-size: 24px; font-weight: 700;">&nbsp;About&nbsp;</span></legend>
			<p>Schedule Posts Calendar Version 3.5</p>
			<p>by Greg Ross</p>
			<p>&nbsp;</p>
			<p>Licenced under the <a href="http://www.gnu.org/licenses/gpl-2.0.html" target=_blank>GPL Version 2</a></p>
			<p>To find out more, please visit the <a href='http://wordpress.org/plugins/schedule-posts-calendar/' target=_blank>WordPress Plugin Directory page</a> or the plugin home page on <a href='http://toolstack.com/schedule-posts-calendar' target=_blank>ToolStack.com</a></p> 
			<p>&nbsp;</p>
			<p>Don't forget to <a href='http://wordpress.org/support/view/plugin-reviews/schedule-posts-calendar' target=_blank>rate and review</a> it too!</p>
	</fieldset>
</div>
	<?php
	//***** End HTML
	}
	
/*
 	This function is called to check if we need to add the above .css and .js files
 	on this page.  ONLY the posts pages need to include the files, all other admin pages
 	don't need them.
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
 	This function is called to add the options page to the settings menu.
 	It's registered at the end of the file with an add_action() call.
*/	
function schedule_posts_calendar_admin()
	{
	add_options_page( 'Schedule Posts Calendar', 'Schedule Posts Calendar', 'manage_options', basename( __FILE__ ), 'schedule_posts_calendar_admin_page');
	}	

/*
   Add the link to action list for post_row_actions.
*/
function schedule_posts_calendar_link_row($actions, $post) 
	{
	$actions['schedule'] = '<a href="#" class="editinlineschedule" title="Schedule this item" onClick="schedule_posts_calendar_quick_schedule_edit(' . $post->ID . ');">Schedule</a>';
		
	return $actions;
	}

/*
   Add the link to settings from the plugin list.
*/
function schedule_posts_calendar_plugin_actions( $actions, $plugin_file, $plugin_data, $context ) 
	{
	array_unshift( $actions, '<a href="' . admin_url() . 'options-general.php?page=schedule-posts-calendar.php">Settings</a>' );
	
	return $actions;
	}
	
function schedule_posts_calendar_lang()
	{
	echo '<script type="text/javascript">' . "\n";
	echo 'function SchedulePostsCalenderLang() {' . "\n";
	echo '    dhtmlXCalendarObject.prototype.langData["wordpress"] = {' . "\n";
	echo '        monthesFNames: ["' . __("January") . '","' . __("February") . '","' . __("March") . '","' . __("April") . '","' . __("May") . '","' . __("June") . '","' . __("July") . '","' . __("August") . '","' . __("September") . '","' . __("October") . '","' . __("November") . '","' . __("December") . '"],' . "\n";
	echo '        monthesSNames: ["' . __("Jan") . '","' . __("Feb") . '","' . __("Mar") . '","' . __("Apr") . '","' . __("May") . '","' . __("Jun") . '","' . __("Jul") . '","' . __("Aug") . '","' . __("Sep") . '","' . __("Oct") . '","' . __("Nov") . '","' . __("Dec") . '"],' . "\n";
	echo '        daysFNames: ["' . __("Sunday") . '","' . __("Monday") . '","' . __("Tuesday") . '","' . __("Wednesday") . '","' . __("Thursday") . '","' . __("Friday") . '","' . __("Saturday") . '"],' . "\n";
	echo '        daysSNames: ["' . __("Sun") . '","' . __("Mon") . '","' . __("Tues") . '","' . __("Wed") . '","' . __("Thur") . '","' . __("Fri") . '","' . __("Sat") . '"]' . "\n";
	echo '        };' . "\n";
	echo '    var langs = { Today:"' . __("Today") . '"};' . "\n";
	echo '    return langs;' . "\n";
	echo '    }' . "\n";
	echo '</script>' . "\n";
	}
	
// Time to register the .css and .js pages, if we need to of course ;)

// First find out if we're in a post/page list, in a post/page edit page or somewhere we don't care about.
$fname = SCP_Add_Calendar_Includes();

// If we're somewhere we care about, do the admin_init action.
if( $fname <> "" ) 
	{
	add_action( 'admin_init', $fname ); 

	add_action('admin_print_scripts', 'schedule_posts_calendar_lang' );
	}

// If we're in the post/page list, add the quick schedule menu items.
if( $fname == "schedule_posts_calendar_quick_schedule" )
{
	add_filter('post_row_actions', 'schedule_posts_calendar_link_row',10,2);
	add_filter('page_row_actions', 'schedule_posts_calendar_link_row',10,2);
	
	add_action('admin_print_scripts', 'schedule_posts_calendar_lang' );
}

// Now add the admin menu items
if ( is_admin() ) 
	{ 
	add_action( 'admin_menu', 'schedule_posts_calendar_admin', 1 ); 
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'schedule_posts_calendar_plugin_actions', 10, 4);
	}


?>