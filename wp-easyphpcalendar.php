<?php
/*
Plugin Name: WP Easy PHP Calendar Admin
Plugin URI: http://wordpress.org
Description: Adds the ability to edit and configure easyphpcalendar from inside the wordpress admin
Author: Jessica Yazbek
Author URI: http://exobi.com
*/

// Hook for adding admin menus
add_action('admin_menu', 'ephpc_add_pages');

// action function for above hook
function ephpc_add_pages() {
    // Add a new submenu under Options:
//    add_options_page('Test Options', 'Test Options', 8, 'testoptions', 'ephpc_options_page');

    // Add a new submenu under Manage:
//    add_management_page('Test Manage', 'Test Manage', 8, 'testmanage', 'ephpc_manage_page');

    // Add a new top-level menu (ill-advised):
    add_menu_page('Easy PHP Calendar', 'Easy PHP Calendar', 7, __FILE__, 'ephpc_settings_page');

	add_submenu_page(__FILE__, 'Settings', 'Settings', 7, __FILE__, array(&$this));

    // Add a submenu to the custom top-level menu:
    add_submenu_page(__FILE__, 'Add/Edit Events', 'Events', 7, 'setup', 'ephpc_events_page');

    // Add a second submenu to the custom top-level menu:
    add_submenu_page(__FILE__, 'Calendar Setup', 'Setup', 8, 'events', 'ephpc_setup_page');
}

// // ephpc_options_page() displays the page content for the Test Options submenu
// function ephpc_options_page() {
//     echo "<h2>Test Options</h2>";
// }
// 
// // ephpc_manage_page() displays the page content for the Test Manage submenu
// function ephpc_manage_page() {
//     echo "<h2>Test Manage</h2>";
// }

// ephpc_toplevel_page() displays the page content for the custom Test Toplevel menu
function ephpc_settings_page() { 
	$ephpc_opts = array('ephpc_dir');
//	print_r($_REQUEST);
  	if ($_REQUEST["updated"]) { ?>
	<div id="message" class="updated fade"><p><strong>Easy PHP Calendar settings updated</strong></p></div>
	<?php }	?>
	<div class="wrap">
	<h2>Easy PHP Calendar Settings</h2>
	<form method="post" action="options.php">
	<?php wp_nonce_field('update-options') ?>
	<p>Enter the directory where you have installed Easy PHP Calendar on your server (for example - /calendar). <br />If it is installed in the root directory, just enter a slash "/":<br />
	<input type="text" name="ephpc_dir" value="<?php echo get_option('ephpc_dir'); ?>" /></p>
	<? if ( current_user_can('switch_themes') ) {
		$ephpc_opts[] = 'ephpc_admin_user';
		$ephpc_opts[] = 'ephpc_admin_pass';
		$ephpc_opts[] = 'ephpc_events_user';
		$ephpc_opts[] = 'ephpc_events_pass';?>
		<h4>The following items are optional. If you enter them in, you will be automatically logged into the Easy PHP Calendar system.</h4>
		<p>Enter the Master Administrator username:
		<input type="text" name="ephpc_admin_user" value="<?php echo get_option('ephpc_admin_user'); ?>" /></p>
		<p>Enter the Master Administrator password:
		<input type="text" name="ephpc_admin_pass" value="<?php echo get_option('ephpc_admin_pass'); ?>" /></p>
		<p>Enter the Event Administration username:
		<input type="text" name="ephpc_events_user" value="<?php echo get_option('ephpc_events_user'); ?>" /></p>
		<p>Enter the Event Administration password:
		<input type="text" name="ephpc_events_pass" value="<?php echo get_option('ephpc_events_pass'); ?>" /></p>
	<?php } ?>
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="<?php echo implode(',',$ephpc_opts)?>" />
	<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Update Options »') ?>" />
	</p>
	</div>

<?php }

// ephpc_sublevel_page() displays the page content for the first submenu
// of the custom Test Toplevel menu
function ephpc_events_page() { ?>
	<div class="wrap">
	<h2>Add/Edit Events</h2>
	<?php if ($ephpc_dir = get_option('ephpc_dir')) { 
		$ephpc_dir = trim($ephpc_dir, '/');
		if ($ephpc_dir != '') {
			$ephpc_dir = "/$ephpc_dir/";
		} else {
			$ephpc_dir = "/";
		}?>
	<?php ephpc_iframe_resize() ?>
	<iframe src="<?php echo $ephpc_dir ?>events/index.php?name=<?php echo get_option('ephpc_events_user'); ?>&pwd=<?php echo get_option('ephpc_events_pass'); ?>" id="ephpc_frame" style="width: 100%; border: 0;"></iframe>
	<? } else { ?>
		<p>You did not enter the install directory for Easy PHP Calendar. Please go to the Settings page.</p>
	<? } ?>
	</div>

<?php }

// ephpc_sublevel_page2() displays the page content for the second submenu
// of the custom Test Toplevel menu
function ephpc_setup_page() { ?>
	<div class="wrap">
	<h2>Calendar Setup</h2>
	<?php if ($ephpc_dir = get_option('ephpc_dir')) { 
		$ephpc_dir = trim($ephpc_dir, '/');
		if ($ephpc_dir != '') {
			$ephpc_dir = "/$ephpc_dir/";
		} else {
			$ephpc_dir = "/";
		}?>
	<?php ephpc_iframe_resize() ?>
	<iframe src="<?php echo $ephpc_dir; ?>setup/index.php?name=<?php echo get_option('ephpc_admin_user'); ?>&pwdX=<?php echo get_option('ephpc_admin_pass'); ?>" id="ephpc_frame" style="width: 100%; border: 0;"></iframe>
	<? } else { ?>
		<p>You did not enter the install directory for Easy PHP Calendar. Please go to the Settings page.</p>
	<? } ?>
		</div>

	<?php }

function ephpc_iframe_resize() {  ?>
	<script type="text/javascript">

	//Input the IDs of the IFRAMES you wish to dynamically resize to match its content height:
	//Separate each ID with a comma. Examples: ["myframe1", "myframe2"] or ["myframe"] or [] for none:
	var iframeids=["ephpc_frame"]

	//Should script hide iframe from browsers that don't support this script (non IE5+/NS6+ browsers. Recommended):
	var iframehide="no"

	var getFFVersion=navigator.userAgent.substring(navigator.userAgent.indexOf("Firefox")).split("/")[1]
	var FFextraHeight=parseFloat(getFFVersion)>=0.1? 25 : 0 //extra height in px to add to iframe in FireFox 1.0+ browsers

	function resizeCaller() {
		var dyniframe=new Array()
		for (i=0; i<iframeids.length; i++){
			if (document.getElementById)
				resizeIframe(iframeids)
				//reveal iframe for lower end browsers? (see var above):
				if ((document.all || document.getElementById) && iframehide=="no"){
				var tempobj=document.all? document.all[iframeids] : document.getElementById(iframeids)
				tempobj.style.display="block"
			}
		}
	}

	function resizeIframe(frameid){
	var currentfr=document.getElementById(frameid)
	if (currentfr && !window.opera){
	currentfr.style.display="block"
	if (currentfr.contentDocument && currentfr.contentDocument.body.offsetHeight) //ns6 syntax
	currentfr.height = currentfr.contentDocument.body.offsetHeight+FFextraHeight; 
	else if (currentfr.Document && currentfr.Document.body.scrollHeight) //ie5+ syntax
	currentfr.height = currentfr.Document.body.scrollHeight;
	if (currentfr.addEventListener)
	currentfr.addEventListener("load", readjustIframe, false)
	else if (currentfr.attachEvent){
	currentfr.detachEvent("onload", readjustIframe) // Bug fix line
	currentfr.attachEvent("onload", readjustIframe)
	}
	}
	}

	function readjustIframe(loadevt) {
	var crossevt=(window.event)? event : loadevt
	var iframeroot=(crossevt.currentTarget)? crossevt.currentTarget : crossevt.srcElement
	if (iframeroot)
	resizeIframe(iframeroot.id);
	}

	function loadintoIframe(iframeid, url){
	if (document.getElementById)
	document.getElementById(iframeid).src=url
	}

	if (window.addEventListener)
	window.addEventListener("load", resizeCaller, false)
	else if (window.attachEvent)
	window.attachEvent("onload", resizeCaller)
	else
	window.onload=resizeCaller


	</script>
<?php }
?>