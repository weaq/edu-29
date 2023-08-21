<?php
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');
function my_theme_enqueue_styles()
{
	$parenthandle = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
	$theme        = wp_get_theme();
	wp_enqueue_style(
		$parenthandle,
		get_template_directory_uri() . '/style.css',
		array(),  // If the parent theme code has a dependency, copy it to here.
		$theme->parent()->get('Version')
	);
	wp_enqueue_style(
		'child-style',
		get_stylesheet_uri(),
		array($parenthandle),
		$theme->get('Version') // This only works if you have Version defined in the style header.
	);
}

/*
// Custom Function to Include
*/

// form regis
//add_action( 'admin_post_nopriv_contact_form', 'process_contact_form' );

add_action('admin_post_contact_form', 'process_contact_form');

function process_contact_form()
{
	$current_user = wp_get_current_user();

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_user_logged_in() && ($current_user->roles[0] == 'administrator' || $current_user->roles[0] == 'contributor')) {
		global $wpdb;

		$params = $_POST;

		$table_name = $wpdb->prefix . 'studentreg';

		submitsForm($table_name, $params);


		die;
	}
}

function submitsForm($table_name, $params)
{

	global $wpdb;

	$student_count = count($params['student_firstname']);
	$error = 0;

	for ($i = 0; $i < $student_count; $i++) {

		$school_id = filter_var($params['school_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$go_id = filter_var($params['go_id'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$groupsara_id = filter_var($params['groupsara_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$activity_id = filter_var($params['activity_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$class_id = filter_var($params['class_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$student_prefix = filter_var($params['student_prefix'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$student_firstname = filter_var($params['student_firstname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$student_lastname = filter_var($params['student_lastname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

		$sql = "INSERT INTO {$table_name} (ID, reg_id, school_id, go_id, groupsara_id, activity_id, class_id, reg_status, student_prefix, student_firstname, student_lastname, display_name, student_image) 
	VALUES (NULL, CURRENT_TIMESTAMP, '{$school_id}', '{$go_id}', '{$groupsara_id}', '{$activity_id}', '{$class_id}', NULL, '{$student_prefix}', '{$student_firstname}', '{$student_lastname}', NULL, NULL);
	";


		if ($wpdb->query($sql)) {
			//wp_redirect($params['base_page'] . '?success=1');
		} else {
			$error = 1;
		}
	}

	if ($error) {
		wp_redirect($params['base_page'] . '?success=1');
	} else {
		wp_redirect($params['base_page'] . '?error=1');
	}

}
