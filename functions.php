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

// is login and show user data
function simple_user_info()
{

	if (is_user_logged_in()) {
		$output = '<div class="container">';
		$current_user = wp_get_current_user();
		$output .= '<strong>Username:</strong>' . $current_user->user_login . "\n";
		$output .= '</br>User display name: ' . $current_user->display_name . "\n";
		$output .= '</br>User email: ' . $current_user->user_email . "\n";
		$output .= '</br>User Role: ' . $current_user->roles[0] . "\n"; // administrator, contributor
		$output .= '</br>User ID: ' . $current_user->ID . "\n";
		$output .= '</br>User first name: ' . $current_user->user_firstname . "\n";
		$output .= '</br>User last name: ' . $current_user->user_lastname . "\n";
		$output .= '</div>';
	}

	return $output;
}
add_shortcode('shortcode_userInfo', 'simple_user_info');

// form regis
//add_action( 'admin_post_nopriv_contact_form', 'process_contact_form' );

add_action('admin_post_contact_form', 'process_contact_form');

function process_contact_form()
{
	$current_user = wp_get_current_user();

	print_r($_POST);

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_user_logged_in() && $current_user->roles[0] == 'contributor') {

		global $wpdb;

		$params = $_POST;

		submitsForm($params);

		die;
	}
}

function submitsForm($params)
{

	global $wpdb;

	$error = 0;
	$arr_insert_id = [];

	$school_id = filter_var($params['school_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$go_id = filter_var($params['go_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$groupsara_id = filter_var($params['groupsara_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$activity_id = filter_var($params['activity_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$class_id = filter_var($params['class_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$sql = "SELECT ID, activity_id, activity_name, group_status, class_id, class_name, student_no, teacher_no FROM wp_groupsara WHERE ID = '" . $groupsara_id . "' ";
	$wp_groupsara = $wpdb->get_results($sql, ARRAY_A);

	$student_count = count($params['student_firstname']);
	$teacher_count = count($params['coach_firstname']);

	$student_count = ($wp_groupsara[0]['student_no'] > $student_count) ? $student_count : $wp_groupsara[0]['student_no'];
	$teacher_count = ($wp_groupsara[0]['teacher_no'] > $teacher_count) ? $teacher_count : $wp_groupsara[0]['teacher_no'];


	// studentreg 
	$sql = "SELECT ID FROM wp_studentreg WHERE school_id = {$school_id} AND groupsara_id = {$groupsara_id} ";
	$student_reg_chk = $wpdb->get_results($sql, ARRAY_A);
	$count_student_reg_chk = count($student_reg_chk);

	if ($count_student_reg_chk == 0) {

		// insert studentreg
		for ($i = 0; $i < $student_count; $i++) {

			$student_prefix = filter_var($params['student_prefix'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$student_firstname = filter_var($params['student_firstname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$student_lastname = filter_var($params['student_lastname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

			if (!empty($student_prefix) && !empty($student_firstname) && !empty($student_lastname)) {

				$sql = "INSERT INTO wp_studentreg (ID, reg_id, school_id, go_id, groupsara_id, activity_id, class_id, reg_status, student_prefix, student_firstname, student_lastname, display_name, student_image) 
				VALUES (NULL, CURRENT_TIMESTAMP, '{$school_id}', '{$go_id}', '{$groupsara_id}', '{$activity_id}', '{$class_id}', NULL, '{$student_prefix}', '{$student_firstname}', '{$student_lastname}', NULL, NULL);
				";

				if ($wpdb->query($sql)) {
					$arr_insert_id['student'][$i] = $wpdb->insert_id;
					if (isset($params['student_img'][$i])) {
						upload_img($params['student_img'][$i], $wpdb->insert_id);
					}
				} else {
					$error = 1;
				}
			}
		}
	} else {

		// update
		for ($i = 0; $i < $count_student_reg_chk; $i++) {

			$student_prefix = filter_var($params['student_prefix'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$student_firstname = filter_var($params['student_firstname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$student_lastname = filter_var($params['student_lastname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

			$img = 'student_img[' . $i . ']';

			echo isset($_FILES[$img]['name']);

			if (!empty($student_prefix) && !empty($student_firstname) && !empty($student_lastname)) {

				$sql = "UPDATE wp_studentreg SET student_prefix = '{$student_prefix}', student_firstname = '{$student_firstname}', student_lastname = '{$student_lastname}' WHERE ID = {$student_reg_chk[$i]['ID']} 
				";

				$arr_insert_id['student'][$i] = $student_reg_chk[$i]['ID'];
				if (isset($params['student_img'][$i])) {
					upload_img($params['student_img'][$i], $student_reg_chk[$i]['ID']);
				}

				if ($wpdb->query($sql)) {
				} else {
					$error = 2;
				}
			}
		}

		// insert studentreg
		for ($i = $i; $i <= ($wp_groupsara[0]['student_no'] - $count_student_reg_chk); $i++) {

			$student_prefix = filter_var($params['student_prefix'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$student_firstname = filter_var($params['student_firstname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$student_lastname = filter_var($params['student_lastname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

			if (!empty($student_prefix) && !empty($student_firstname) && !empty($student_lastname)) {

				$sql = "INSERT INTO wp_studentreg (ID, reg_id, school_id, go_id, groupsara_id, activity_id, class_id, reg_status, student_prefix, student_firstname, student_lastname, display_name, student_image) 
				VALUES (NULL, CURRENT_TIMESTAMP, '{$school_id}', '{$go_id}', '{$groupsara_id}', '{$activity_id}', '{$class_id}', NULL, '{$student_prefix}', '{$student_firstname}', '{$student_lastname}', NULL, NULL);
				";

				if ($wpdb->query($sql)) {
					$arr_insert_id['student'][$i] = $wpdb->insert_id;
					if (isset($params['student_img'][$i])) {
						upload_img($params['student_img'][$i], $wpdb->insert_id);
					}
				} else {
					$error = 3;
				}
			}
		}
	}


	if ($wp_groupsara[0]['class_id'] != "11") {
		// teacherreg 
		$sql = "SELECT ID FROM wp_teacherreg WHERE school_id = {$school_id} AND groupsara_id = {$groupsara_id} ";
		$teacher_reg_chk = $wpdb->get_results($sql, ARRAY_A);
		$count_teacher_reg_chk = count($teacher_reg_chk);

		if ($count_teacher_reg_chk == 0) {

			// insert teacherreg
			for ($i = 0; $i < $teacher_count; $i++) {

				$teacher_prefix = filter_var($params['coach_prefix'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$teacher_firstname = filter_var($params['coach_firstname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$teacher_lastname = filter_var($params['coach_lastname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$tel = filter_var($params['coach_tel'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

				if (!empty($teacher_prefix) && !empty($teacher_firstname) && !empty($teacher_lastname)) {

					$sql = "INSERT INTO wp_teacherreg (ID, reg_id, school_id, go_id, groupsara_id, activity_id, class_id, reg_status, teacher_prefix, teacher_firstname, teacher_lastname, display_name, teacher_image, tel ) 
			VALUES (NULL, CURRENT_TIMESTAMP, '{$school_id}', '{$go_id}', '{$groupsara_id}', '{$activity_id}', '{$class_id}', NULL, '{$teacher_prefix}', '{$teacher_firstname}', '{$teacher_lastname}', NULL, NULL, '{$tel}' );
			";

					if ($wpdb->query($sql)) {
						$arr_insert_id['teacher'][$i] = $wpdb->insert_id;
						if (isset($params['coach_img'][$i])) {
							upload_img($params['coach_img'][$i], $wpdb->insert_id);
						}
					} else {
						$error = 4;
					}
				}
			}
		} else {

			// update
			for ($i = 0; $i < $count_teacher_reg_chk; $i++) {

				$teacher_prefix = filter_var($params['coach_prefix'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$teacher_firstname = filter_var($params['coach_firstname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$teacher_lastname = filter_var($params['coach_lastname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$tel = filter_var($params['coach_tel'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

				if (!empty($teacher_prefix) && !empty($teacher_firstname) && !empty($teacher_lastname)) {

					$sql = "UPDATE wp_teacherreg SET teacher_prefix = '{$teacher_prefix}', teacher_firstname = '{$teacher_firstname}', teacher_lastname = '{$teacher_lastname}', tel = '{$tel}' WHERE ID = {$teacher_reg_chk[$i]['ID']} 
				";

					$arr_insert_id['teacher'][$i] = $teacher_reg_chk[$i]['ID'];
					if (isset($params['coach_img'][$i])) {
						upload_img($params['coach_img'][$i], $teacher_reg_chk[$i]['ID']);
					}

					if ($wpdb->query($sql)) {
					} else {
						$error = 5;
					}
				}
			}

			// insert teacherreg
			for ($i = $i; $i <= ($wp_groupsara[0]['teacher_no'] - $count_teacher_reg_chk); $i++) {

				$teacher_prefix = filter_var($params['coach_prefix'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$teacher_firstname = filter_var($params['coach_firstname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$teacher_lastname = filter_var($params['coach_lastname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$tel = filter_var($params['coach_tel'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

				if (!empty($teacher_prefix) && !empty($teacher_firstname) && !empty($teacher_lastname)) {

					$sql = "INSERT INTO wp_teacherreg (ID, reg_id, school_id, go_id, groupsara_id, activity_id, class_id, reg_status, teacher_prefix, teacher_firstname, teacher_lastname, display_name, teacher_image, tel ) 
			VALUES (NULL, CURRENT_TIMESTAMP, '{$school_id}', '{$go_id}', '{$groupsara_id}', '{$activity_id}', '{$class_id}', NULL, '{$teacher_prefix}', '{$teacher_firstname}', '{$teacher_lastname}', NULL, NULL, '{$tel}' );
			";

					if ($wpdb->query($sql)) {
						$arr_insert_id['teacher'][$i] = $wpdb->insert_id;
						if (isset($params['coach_img'][$i])) {
							upload_img($params['coach_img'][$i], $wpdb->insert_id);
						}
					} else {
						$error = 6;
					}
				}
			}
		}
	}

	//print_r($arr_insert_id);

	if ($error) {
		//wp_redirect($params['base_page'] . '?error=1');
	} else {
		//wp_redirect($params['base_page'] . '?success=1');
	}
}


function upload_img($image, $id)
{
	$uploadDirectory = "/img-upload/";

	$errors = []; // Store errors here

	$fileExtensionsAllowed = ['jpeg', 'jpg', 'png']; // These will be the only file extensions allowed 

	$fileName =  $id; //$image['name'];
	$fileSize = $image['size'];
	$fileTmpName  = $image['tmp_name'];
	$fileType = $image['type'];
	$fileExtension = strtolower(end(explode('.', $fileName)));

	$uploadPath = $uploadDirectory . basename($fileName);

	echo 'aaa';
	/*
	if (isset($_POST['submit'])) {

		if (!in_array($fileExtension, $fileExtensionsAllowed)) {
			$errors[] = "This file extension is not allowed. Please upload a JPEG or PNG file";
		}

		if ($fileSize > 1000000) {
			$errors[] = "File exceeds maximum size (1MB)";
		}

		if (empty($errors)) {
			$didUpload = move_uploaded_file($fileTmpName, $uploadPath);

			if ($didUpload) {
				echo "The file " . basename($fileName) . " has been uploaded";
			} else {
				echo "An error occurred. Please contact the administrator.";
			}
		} else {
			foreach ($errors as $error) {
				echo $error . "These are the errors" . "\n";
			}
		}
	}
*/
}
