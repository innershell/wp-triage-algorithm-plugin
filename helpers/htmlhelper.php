<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// safe redirect
function chained_redirect($url) {
	echo "<meta http-equiv='refresh' content='0;url=$url' />"; 
	exit;
}

// function to conditionally add DB fields
function chainedquiz_add_db_fields($fields, $table) {
		global $wpdb;
		
		// check fields
		$table_fields = $wpdb->get_results("SHOW COLUMNS FROM `$table`");
		$table_field_names = array();
		foreach($table_fields as $f) $table_field_names[] = $f->Field;		
		$fields_to_add=array();
		
		foreach($fields as $field) {
			 if(!in_array($field['name'], $table_field_names)) {
			 	  $fields_to_add[] = $field;
			 } 
		}
		
		// now if there are fields to add, run the query
		if(!empty($fields_to_add)) {
			 $sql = "ALTER TABLE `$table` ";
			 
			 foreach($fields_to_add as $cnt => $field) {
			 	 if($cnt > 0) $sql .= ", ";
			 	 $sql .= "ADD $field[name] $field[type]";
			 } 
			 
			 $wpdb->query($sql);
		}
}

// define new line for CSVs
if(!function_exists('kiboko_define_newline')) {
	function kiboko_define_newline() {
		// credit to http://yoast.com/wordpress/users-to-csv/
		$unewline = "\r\n";
		if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'win')) {
		   $unewline = "\r\n";
		} else if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'mac')) {
		   $unewline = "\r";
		} else {
		   $unewline = "\n";
		}
		return $unewline;
	}
}

if(!function_exists('kiboko_get_mime_type')) {
	function kiboko_get_mime_type()  {
		// credit to http://yoast.com/wordpress/users-to-csv/
		$USER_BROWSER_AGENT="";
	
				if (preg_match('/OPERA(\/| )([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
					$USER_BROWSER_AGENT='OPERA';
				} else if (preg_match('/MSIE ([0-9].[0-9]{1,2})/',strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
					$USER_BROWSER_AGENT='IE';
				} else if (preg_match('/OMNIWEB\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
					$USER_BROWSER_AGENT='OMNIWEB';
				} else if (preg_match('/MOZILLA\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
					$USER_BROWSER_AGENT='MOZILLA';
				} else if (preg_match('/KONQUEROR\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
			    	$USER_BROWSER_AGENT='KONQUEROR';
				} else {
			    	$USER_BROWSER_AGENT='OTHER';
				}
	
		$mime_type = ($USER_BROWSER_AGENT == 'IE' || $USER_BROWSER_AGENT == 'OPERA')
					? 'application/octetstream'
					: 'application/octet-stream';
		return $mime_type;
	}
}

// Get admin email. If not setup, use WordPress admin email.
function chained_admin_email() {
	$admin_email = stripslashes(get_option('chained_admin_emails'));
	if ($admin_email == '') {
		$admin_email = 'WordPress Admin <' . stripslashes(get_option('admin_email')) . '>';
	}
	return $admin_email;
}

// Get sender email. If not setup, use WordPress admin email.
function chained_sender_email() {
	$sender_email = stripslashes(get_option('chained_sender_name')) . ' <' . get_option('chained_sender_email') . '>';
	return $sender_email;
}

function chained_strip_tags($content) {
   if(!current_user_can('unfiltered_html') and WATUPRO_UNFILTERED_HTML != 1) {
		$content = strip_tags($content, '<b><i><em><u><a><p><br><div><span><hr><font><img><strong>');
	}
	
	return $content;
}

// makes sure all values in array are ints. Typically used to sanitize POST data from multiple checkboxes
function chained_int_array($value) {
  if(empty($value) or !is_array($value)) return array();
   $value = array_filter($value, 'is_numeric');
   return $value;
}
