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

// remove data from db
function custom_js_remove_record()
{
	$sID = $_GET['sID'];
	$url = "?sID=" . $sID . "&rm=1";
?>
	<script>
		function js_remove_record() {
			let text = "คุณแน่ใจหรือว่าต้องการ ลบข้อมูล ?";
			if (confirm(text) == true) {
				window.location = '<?php echo $url; ?>'
			} else {
				text = "ยกเลิกการลบข้อมูล";
			}
		}
	</script>
<?php
}
add_action('wp_head', 'custom_js_remove_record');

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

// owner school register activity list
function owner_regis_activity_list()
{
	global $wpdb;
	$current_user = wp_get_current_user();

	$output = "";

	if (is_user_logged_in() && $current_user->roles[0] == 'contributor') {
		// group status
		$arr_group_status = [
			"1" => ["short_name" => "อปท.", "name" => "การแข่งขันทักษะวิชาการ",],
			"21" => ["short_name" => "สพป.", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
			"22" => ["short_name" => "สพม.", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
		];
		$tmp_group_status = "";
		$tmp_group_id = "";

		$sql = "SELECT * FROM wp_schools WHERE school_id = {$current_user->user_login}";
		$wp_schools = $wpdb->get_results($sql, ARRAY_A);

		$output .= '<div class="fs-3 mb-1">รายการสมัครแข่งขันของ ' . $wp_schools[0]['school_name'] . '</div>';

		$sql = "SELECT * FROM wp_groupsara a
				INNER JOIN (SELECT COUNT(ID) AS count_student, groupsara_id FROM wp_studentreg 
				WHERE school_id = '{$current_user->user_login}' GROUP BY groupsara_id) b 
				ON a.ID = b.groupsara_id  
				ORDER BY a.group_status, a.group_id ASC ";
		$results = $wpdb->get_results($sql, ARRAY_A);
		foreach ($results as $key => $value) {
			if ($value['group_status'] != $tmp_group_status) {
				$tmp_group_status = $value['group_status'];
				$output .= '<div class="fs-6 mt-2"><strong>' . $arr_group_status[$tmp_group_status]['name'] . " (" . $arr_group_status[$tmp_group_status]['short_name'] . ")</strong></div>";
			}
			if ($value['group_id'] != $tmp_group_id) {
				$tmp_group_id = $value['group_id'];
				$output .= '<div class="fs-6 ms-3"><strong>กลุ่มสาระการเรียนรู้ : ' . $value['group_name'] . "</strong></div>";
			}
			if ($value['count_student'] >= $value['student_no_min']) {
				$txt = '(ส่งครบ)';
				$css_coler = "text-success";
			} else {
				$student_not_enough = $value['student_no_min'] - $value['count_student'];
				$css_coler = "text-danger";
				$txt = ' (ขาด ' . $student_not_enough . ' คน)';
			}
			$output .= '<div class="ms-5 ' . $css_coler . ' ">' . $value['activity_name'] . ' ' . $value['class_name'] . " " . $txt . "</div>";
		}
	}

	return $output;
}
add_shortcode('shortcode_ownerRegisActivityList', 'owner_regis_activity_list');

// form regis
//add_action( 'admin_post_nopriv_contact_form', 'process_contact_form' );

add_action('admin_post_contact_form', 'process_contact_form');

function process_contact_form()
{
	$current_user = wp_get_current_user();

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_user_logged_in() && $current_user->roles[0] == 'contributor') {

		global $wpdb;

		$params = $_POST;

		// date diff check

		$sql = "SELECT * FROM wp_groupsara WHERE ID = {$params['groupsara_id']} ";
		$wp_groupsara = $wpdb->get_results($sql, ARRAY_A);		

		$match_date = strtotime($wp_groupsara[0]['match_date']);

		$datediff = time() - $match_date;

		$day_diff = round($datediff / (60 * 60 * 24));

		if ($day_diff <= 0) {
			submitsForm($params);
		} else {
			echo "ปิดระบบการลงทะเบียนแล้ว " . $day_diff ;
		}

		die;
	}
}

function submitsForm($params)
{

	global $wpdb;

	$error = 0;
	$arr_insert_id = [];

	$current_user = wp_get_current_user();

	$sql = "SELECT * FROM wp_schools WHERE school_id = {$current_user->user_login}";
	$wp_schools = $wpdb->get_results($sql, ARRAY_A);

	$school_id = filter_var($wp_schools[0]['school_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$go_id = filter_var($wp_schools[0]['go_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$groupsara_id = filter_var($params['groupsara_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$activity_id = filter_var($params['activity_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$class_id = filter_var($params['class_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$sql = "SELECT ID, activity_id, activity_name, group_status, class_id, class_name, student_no, teacher_no FROM wp_groupsara WHERE ID = '" . $groupsara_id . "' ";
	$wp_groupsara = $wpdb->get_results($sql, ARRAY_A);

	$student_count = count($params['student_firstname']);
	$student_count = ($wp_groupsara[0]['student_no'] > $student_count) ? $student_count : $wp_groupsara[0]['student_no'];

	if ($wp_groupsara[0]['class_id'] != "11") {
		$teacher_count = count($params['coach_firstname']);
		$teacher_count = ($wp_groupsara[0]['teacher_no'] > $teacher_count) ? $teacher_count : $wp_groupsara[0]['teacher_no'];
	}


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
			$student_tel = !empty($params['student_tel'][$i]) ? filter_var($params['student_tel'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "NULL";

			if (!empty($student_prefix) && !empty($student_firstname) && !empty($student_lastname)) {

				$sql = "INSERT INTO wp_studentreg (ID, reg_id, school_id, go_id, groupsara_id, activity_id, class_id, reg_status, student_prefix, student_firstname, student_lastname, display_name, student_image, tel) 
				VALUES (NULL, CURRENT_TIMESTAMP, '{$school_id}', '{$go_id}', '{$groupsara_id}', '{$activity_id}', '{$class_id}', NULL, '{$student_prefix}', '{$student_firstname}', '{$student_lastname}', NULL, NULL, {$student_tel});
				";

				if ($wpdb->query($sql)) {

					if (isset($_FILES['student_img']['tmp_name'][$i]) && $_FILES['student_img']['size'][$i] > 0) {
						echo $_FILES['student_img']['tmp_name'][$i];
						echo "<br/>";
					}


					$arr_insert_id['student'][$i] = $wpdb->insert_id;

					if (isset($_FILES['student_img']['tmp_name'][$i]) && $_FILES['student_img']['size'][$i] > 0) {
						list($width, $height, $type, $attr) = getimagesize($_FILES['student_img']['tmp_name'][$i]);
						if ($width >= 200 && $width < 2000 && $height >= 200 && $height < 2000) {
							upload_img($_FILES['student_img']['name'][$i], $_FILES['student_img']['size'][$i], $_FILES['student_img']['tmp_name'][$i], $_FILES['student_img']['type'][$i], $wpdb->insert_id, "student_img", $school_id);
						}
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
			$student_tel = !empty($params['student_tel'][$i]) ? filter_var($params['student_tel'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "NULL";

			if (!empty($student_prefix) && !empty($student_firstname) && !empty($student_lastname)) {

				$sql = "UPDATE wp_studentreg SET student_prefix = '{$student_prefix}', student_firstname = '{$student_firstname}', student_lastname = '{$student_lastname}', tel = '$student_tel' WHERE ID = {$student_reg_chk[$i]['ID']} 
				";

				$arr_insert_id['student'][$i] = $student_reg_chk[$i]['ID'];

				if (isset($_FILES['student_img']['tmp_name'][$i]) && $_FILES['student_img']['size'][$i] > 0) {
					list($width, $height, $type, $attr) = getimagesize($_FILES['student_img']['tmp_name'][$i]);
					if ($width >= 200 && $width < 2000 && $height >= 200 && $height < 2000) {
						upload_img($_FILES['student_img']['name'][$i], $_FILES['student_img']['size'][$i], $_FILES['student_img']['tmp_name'][$i], $_FILES['student_img']['type'][$i], $student_reg_chk[$i]['ID'], "student_img", $school_id);
					}
				}

				if ($wpdb->query($sql)) {
				} else {
					$error = 2;
				}
			}
		}

		// insert studentreg
		for ($i = $i; $i < $wp_groupsara[0]['student_no']; $i++) {

			$student_prefix = filter_var($params['student_prefix'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$student_firstname = filter_var($params['student_firstname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$student_lastname = filter_var($params['student_lastname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$student_tel = !empty($params['student_tel'][$i]) ? filter_var($params['student_tel'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "NULL";

			if (!empty($student_prefix) && !empty($student_firstname) && !empty($student_lastname)) {

				$sql = "INSERT INTO wp_studentreg (ID, reg_id, school_id, go_id, groupsara_id, activity_id, class_id, reg_status, student_prefix, student_firstname, student_lastname, display_name, student_image, tel) 
				VALUES (NULL, CURRENT_TIMESTAMP, '{$school_id}', '{$go_id}', '{$groupsara_id}', '{$activity_id}', '{$class_id}', NULL, '{$student_prefix}', '{$student_firstname}', '{$student_lastname}', NULL, NULL, $student_tel);
				";

				if ($wpdb->query($sql)) {
					$arr_insert_id['student'][$i] = $wpdb->insert_id;

					if (isset($_FILES['student_img']['tmp_name'][$i]) && $_FILES['student_img']['size'][$i] > 0) {
						list($width, $height, $type, $attr) = getimagesize($_FILES['student_img']['tmp_name'][$i]);
						if ($width >= 200 && $width < 2000 && $height >= 200 && $height < 2000) {
							upload_img($_FILES['student_img']['name'][$i], $_FILES['student_img']['size'][$i], $_FILES['student_img']['tmp_name'][$i], $_FILES['student_img']['type'][$i], $wpdb->insert_id, "student_img", $school_id);
						}
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
				$teacher_tel = filter_var($params['coach_tel'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

				if (!empty($teacher_prefix) && !empty($teacher_firstname) && !empty($teacher_lastname)) {

					$sql = "INSERT INTO wp_teacherreg (ID, reg_id, school_id, go_id, groupsara_id, activity_id, class_id, reg_status, teacher_prefix, teacher_firstname, teacher_lastname, display_name, teacher_image, tel ) 
			VALUES (NULL, CURRENT_TIMESTAMP, '{$school_id}', '{$go_id}', '{$groupsara_id}', '{$activity_id}', '{$class_id}', NULL, '{$teacher_prefix}', '{$teacher_firstname}', '{$teacher_lastname}', NULL, NULL, '{$teacher_tel}' );
			";

					if ($wpdb->query($sql)) {
						$arr_insert_id['teacher'][$i] = $wpdb->insert_id;

						if (isset($_FILES['coach_img']['tmp_name'][$i]) && $_FILES['coach_img']['size'][$i] > 0) {
							list($width, $height, $type, $attr) = getimagesize($_FILES['coach_img']['tmp_name'][$i]);
							if ($width >= 200 && $width < 2000 && $height >= 200 && $height < 2000) {
								upload_img($_FILES['coach_img']['name'][$i], $_FILES['coach_img']['size'][$i], $_FILES['coach_img']['tmp_name'][$i], $_FILES['coach_img']['type'][$i], $wpdb->insert_id, "coach_img", $school_id);
							}
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
				$teacher_tel = filter_var($params['coach_tel'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

				if (!empty($teacher_prefix) && !empty($teacher_firstname) && !empty($teacher_lastname)) {

					$sql = "UPDATE wp_teacherreg SET teacher_prefix = '{$teacher_prefix}', teacher_firstname = '{$teacher_firstname}', teacher_lastname = '{$teacher_lastname}', tel = '{$teacher_tel}' WHERE ID = {$teacher_reg_chk[$i]['ID']} 
				";

					$arr_insert_id['teacher'][$i] = $teacher_reg_chk[$i]['ID'];

					if (isset($_FILES['coach_img']['tmp_name'][$i]) && $_FILES['coach_img']['size'][$i] > 0) {
						list($width, $height, $type, $attr) = getimagesize($_FILES['coach_img']['tmp_name'][$i]);
						if ($width >= 200 && $width < 2000 && $height >= 200 && $height < 2000) {
							upload_img($_FILES['coach_img']['name'][$i], $_FILES['coach_img']['size'][$i], $_FILES['coach_img']['tmp_name'][$i], $_FILES['coach_img']['type'][$i], $teacher_reg_chk[$i]['ID'], "coach_img", $school_id);
						}
					}

					if ($wpdb->query($sql)) {
					} else {
						$error = 5;
					}
				}
			}

			// insert teacherreg
			for ($i = $i; $i < $wp_groupsara[0]['teacher_no']; $i++) {

				$teacher_prefix = filter_var($params['coach_prefix'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$teacher_firstname = filter_var($params['coach_firstname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$teacher_lastname = filter_var($params['coach_lastname'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$teacher_tel = filter_var($params['coach_tel'][$i], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

				if (!empty($teacher_prefix) && !empty($teacher_firstname) && !empty($teacher_lastname)) {

					$sql = "INSERT INTO wp_teacherreg (ID, reg_id, school_id, go_id, groupsara_id, activity_id, class_id, reg_status, teacher_prefix, teacher_firstname, teacher_lastname, display_name, teacher_image, tel ) 
			VALUES (NULL, CURRENT_TIMESTAMP, '{$school_id}', '{$go_id}', '{$groupsara_id}', '{$activity_id}', '{$class_id}', NULL, '{$teacher_prefix}', '{$teacher_firstname}', '{$teacher_lastname}', NULL, NULL, '{$teacher_tel}' );
			";

					if ($wpdb->query($sql)) {
						$arr_insert_id['teacher'][$i] = $wpdb->insert_id;

						if (isset($_FILES['coach_img']['tmp_name'][$i]) && $_FILES['coach_img']['size'][$i] > 0) {
							list($width, $height, $type, $attr) = getimagesize($_FILES['coach_img']['tmp_name'][$i]);
							if ($width >= 200 && $width < 2000 && $height >= 200 && $height < 2000) {
								upload_img($_FILES['coach_img']['name'][$i], $_FILES['coach_img']['size'][$i], $_FILES['coach_img']['tmp_name'][$i], $_FILES['coach_img']['type'][$i], $wpdb->insert_id, "coach_img", $school_id);
							}
						}
					} else {
						$error = 6;
					}
				}
			}
		}
	}

	//print_r($arr_insert_id);
	/*
	if ($error) {
		//wp_redirect($params['base_page'] . '?error=1');
	} else {
		//wp_redirect($params['base_page'] . '?success=1');
	}
	*/

	//echo $params['base_page'];


	wp_redirect($params['base_page'] . '?success=1&sID=' . $groupsara_id);
	//exit;
}


function upload_img($fileName, $fileSize, $fileTmpName, $fileType, $id, $dir_upload, $school_id)
{
	$uploadDirectory = "../img-upload/" . $dir_upload . "/";

	$upload_errors = []; // Store errors here

	$fileExtensionsAllowed = ['jpeg', 'jpg']; //['jpeg', 'jpg', 'png']; // These will be the only file extensions allowed 

	/*
	$fileName =  $image['name'];
	$fileSize = $image['size'];
	$fileTmpName  = $image['tmp_name'];
	$fileType = $image['type'];
	*/


	$fileExtension = strtolower(end(explode('.', $fileName)));


	$uploadPath = $uploadDirectory . basename($id) . '.' . $fileExtension;

	echo "<br/>" . $uploadPath;

	if (!in_array($fileExtension, $fileExtensionsAllowed)) {
		$upload_errors[] = "This file extension is not allowed. Please upload a JPEG or PNG file";
	}

	if ($fileSize > 2000000) {
		$upload_errors[] = "File exceeds maximum size (500kB)";
	}

	if (empty($upload_errors)) {

		if (file_exists($uploadPath)) {
			chmod($uploadPath, 0755); //Change the file permissions if allowed
			unlink($uploadPath); //remove the file
		}

		$didUpload = move_uploaded_file($fileTmpName, $uploadPath);

		if ($didUpload) {
			echo "The file " . basename($fileName) . " has been uploaded";
		} else {
			echo "An error occurred. Please contact the administrator.";
		}
	} else {
		foreach ($upload_errors as $error) {
			echo $error . "The file " . basename($fileName) . " upload errors" . "<br/>\n";
		}
	}
}



// //
// owner school register activity list
function admin_view_regis_activity_list()
{
	global $wpdb;
	$current_user = wp_get_current_user();

	$output = "";
	$ii = 1;

	//if (is_user_logged_in() && $current_user->roles[0] == 'contributor' && $current_user->roles[0] == 'administrator') {
	// group status
	$arr_group_status = [
		"1" => ["short_name" => "อปท.", "name" => "การแข่งขันทักษะวิชาการ",],
		"21" => ["short_name" => "สพป.", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
		"22" => ["short_name" => "สพม.", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
	];
	$tmp_group_status = "";
	$tmp_group_id = "";
	$tmp_activity_id = "";

	$sql = "SELECT a.ID AS studentreg_id, a.school_id AS student_school_id, c.school_name, a.groupsara_id AS groupsara_id , 
				b.group_status, b.group_id, b.group_name, a.activity_id AS activity_id, b.activity_name, a.class_id, 
				b.class_name, a.student_prefix, a.student_firstname, a.student_lastname , b.student_no_min, b.student_no
				FROM wp_studentreg a 
				INNER JOIN wp_groupsara b 
				ON a.groupsara_id = b.ID 
				INNER JOIN wp_schools c 
				ON a.school_id = c.school_id
				ORDER BY b.group_status , b.group_id , a.groupsara_id , a.school_id ASC";
	$results = $wpdb->get_results($sql, ARRAY_A);
	foreach ($results as $key => $value) {

		if ($value['student_no'] == 1) {
			$txt_activity_type = "เดี่ยว";
		} else if ($value['student_no'] == 2) {
			$txt_activity_type = "คู่";
		} else if ($value['student_no'] >= 3) {
			if ($value['student_no'] == $value['student_no_min']) {
				$txt_activity_type = "ทีม " . $value['student_no'] . " คน";
			} else {
				$txt_activity_type = "ทีม " . $value['student_no_min'] . "-" . $value['student_no'] . " คน";
			}
		}

		if ($value['group_status'] != $tmp_group_status) {
			$tmp_group_status = $value['group_status'];
			$output .= '<div class="fs-5 mt-3">';
			$output .= $arr_group_status[$value['group_status']]['name'] . " (" . $arr_group_status[$value['group_status']]['short_name'] . ") ";
			$output .= '</div>';
		}
		if ($value['group_id'] != $tmp_group_id) {
			$tmp_group_id = $value['group_id'];
			$output .= '<div class="fs-6 mt-3"><strong> ' . $value['group_name'] . '</strong></div>';
		}
		if ($value['activity_id'] != $tmp_activity_id) {
			$tmp_activity_id = $value['activity_id'];
			$output .= '<div class="mt-1">' . "&nbsp; &nbsp; &nbsp; &nbsp; \t" . "กิจกรรม " . $value['activity_name'] . " " . $value['class_name'] . " (" . $txt_activity_type . ")" . "</div>";
			$ii = 1;
		} else {
			$ii++;
		}



		$output .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; \t" . $ii . ". ";
		$output .= $value['school_name'] . "\t";
		$output .= $value['student_prefix'] . "\t";
		$output .= $value['student_firstname'] . "\t";
		$output .= $value['student_lastname'] . "\t";
		$output .= "<br>";
	}

	//}

	return $output;
}

add_shortcode('shortcode_adminViewRegisActivityList', 'admin_view_regis_activity_list');


// form score
//add_action( 'admin_post_score_form', 'process_score_form' );

add_action('admin_post_score_form', 'process_score_form');

function process_score_form()
{
	$current_user = wp_get_current_user();

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_user_logged_in() && $current_user->roles[0] == 'contributor') {

		global $wpdb;

		$params = $_POST;

		$sql = "SELECT a.* FROM wp_groupsara a INNER JOIN wp_school_record b on a.group_id = b.group_id 
            WHERE a.ID = {$params['groupsara_id']} AND b.school_id = '{$current_user->user_login}' ";
		$wp_groupsara = $wpdb->get_results($sql, ARRAY_A);

		if (count($wp_groupsara) > 0) {

			submitsScoreForm($params);
		}



		die;
	}
}

function submitsScoreForm($params)
{

	global $wpdb;

	$current_user = wp_get_current_user();

	// sort value DESC
	arsort($params['score']);

	$i = 1;
	foreach ($params['score'] as $key => $value) {

		$sql = "SELECT * FROM wp_school_score WHERE groupsara_id = '{$params['groupsara_id']}' AND school_id = '{$key}'";
		$result_school_score = $wpdb->get_results($sql, ARRAY_A);

		if (COUNT($result_school_score) == 0) {
			$sql = "INSERT INTO wp_school_score (id, groupsara_id, school_id, score, ranking) 
				VALUES (NULL, '{$params['groupsara_id']}', '{$key}', '{$value}', '{$i}')
				";
			$wpdb->query($sql);
		} else {
			$tmp_ranking = ($value == 0) ? NULL : $i;
			$sql = "UPDATE wp_school_score 
					SET score = '{$value}' , ranking = '{$i}' 
					WHERE wp_school_score.id = '{$result_school_score[0]['id']}' ";
			$wpdb->query($sql);
		}

		$i++;
	}

	wp_redirect($params['base_page'] . '?success=1&sID=' . $params['groupsara_id']);
	//exit;
}

//
function buddhistCalendar($ymd)
{
	$ymd = explode("-", $ymd);
	$thai_month = array('', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม');
	$y = $ymd[0] + 543;
	$m = $thai_month[intval($ymd[1])];
	$d = intval($ymd[2]);
	$output = "วันที่ " . $d . " เดือน " . $m . " พ.ศ. " . $y;
	return $output;
}
