<?php
/*
 * Template Name: Activity List Template
 * Template Post Type: page
 */

get_header();

?>

<div class="container mt-3 mb-5">
	<?php

	$current_user = wp_get_current_user();

	if (is_user_logged_in() && ($current_user->roles[0] == 'administrator' || $current_user->roles[0] == 'contributor')) {

		global $wpdb;

		// group status
		$arr_group_status = [
			"1" => "อปท",
			"21" => "สพป",
			"22" => "สพม",
		];

		echo '<div>';
		echo '<strong>กลุ่ม : </strong>';
		echo '<select name="group" onchange="location = this.value;">';
		echo '<option value="?group_status_id=0" selected >กรุณาเลือกกลุ่ม</option>';

		foreach ($arr_group_status as $key => $value) {
			$selected = ($_GET['group_status_id'] == $key) ? "selected" : "";
			// list
			echo '<option value="?group_status_id=' . $key . '" ' . $selected . '>' . $value . '</option>';
		}

		echo '</select>';
		echo '</div>';


		// group
		if ($_GET['group_status_id']) {
			$sql = "SELECT DISTINCT(group_id) as group_id, group_name FROM wp_groupsara WHERE group_status = '{$_GET['group_status_id']}' ORDER BY group_id ASC";
			$query = $wpdb->get_results($sql, ARRAY_A);

			echo '<div>';
			echo '<strong>กลุ่มสาระการเรียนรู้ : </strong>';
			echo '<select name="group" onchange="location = this.value;">';
			echo '<option value="?group_status_id=' . $_GET['group_status_id'] . '&group_id=0" selected >กรุณาเลือกกลุ่มสาระ</option>';

			foreach ($query as $row) {
				$selected = ($_GET['group_id'] == $row['group_id']) ? "selected" : "";
				// list
				echo '<option value="?group_status_id=' . $_GET['group_status_id'] . '&group_id=' . $row['group_id'] . '" ' . $selected . '>' . $row['group_name'] . '</option>';
			}

			echo '</select>';
			echo '</div>';
		}

		// class
		if ($_GET['group_status_id'] && $_GET['group_id']) {

			// get class array
			$sql = "SELECT DISTINCT(class_id) ,class_name FROM `wp_groupsara` WHERE group_status = '{$_GET['group_status_id']}' AND group_id = '{$_GET['group_id']}' ORDER BY class_id ASC";
			$arr_class = $wpdb->get_results($sql, ARRAY_A);


			//
			echo '<div>';

			$sql = "SELECT COUNT(activity_name), activity_name FROM wp_groupsara WHERE group_status = '{$_GET['group_status_id']}' AND group_id = '{$_GET['group_id']}' GROUP BY activity_name ORDER BY activity_name ASC";

			$query = $wpdb->get_results($sql, ARRAY_A);


			echo '<div class="fs-3 mt-3">กลุ่มสาระการเรียนรู้ : ' . $query[0]['group_name'] . '</div>';

	?>

			<table class="table table-hover">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th colspan="<?php echo count($arr_class); ?>" class="text-center">ระดับ</th>
						<th>&nbsp;</th>
					</tr>
					<tr>
						<th>ชื่อกิจกรรม</th>
						<?php
						foreach ($arr_class as $value) {
							echo '<th class="text-center">' . $value['class_name'] . '</th>';
						}
						?>
						<th class="text-center">ประเภท</th>
					</tr>
				</thead>
				<tbody>

					<?php

					foreach ($query as $row) {
						echo '<tr>';
						echo '<td>' . $row['activity_name'] . '</td>';

						$txt_activity_type = "";

						foreach ($arr_class as $value) {
							$sql = "SELECT * FROM wp_groupsara WHERE activity_name LIKE '%{$row['activity_name']}%' AND class_id = '{$value['class_id']}' AND group_status = '{$_GET['group_status_id']}' AND group_id = '{$_GET['group_id']}' ";
							$result_activity = $wpdb->get_results($sql, ARRAY_A);

							if ($result_activity[0]['student_no']) {

								$sql = "SELECT COUNT(id) as cid FROM `wp_studentreg` WHERE groupsara_id = '{$result_activity[0]['ID']}' ";
								$result_count_student = $wpdb->get_results($sql, ARRAY_A);

								$sql = "SELECT COUNT(id) as cid FROM `wp_teacherreg` WHERE groupsara_id = '{$result_activity[0]['ID']}' ";
								$result_count_teacher = $wpdb->get_results($sql, ARRAY_A);

								echo '<td class="text-center"><a href="/sample-page/?sID=' . $result_activity[0]['ID'] . '">' . $result_count_student[0]['cid'] . '/' . $result_count_teacher[0]['cid'] . '</a></td>';
							} else {
								echo '<td class="bg-secondary">&nbsp;</td>';
							}

							if ($result_activity[0]['student_no'] == 1) {
								$txt_activity_type = "เดี่ยว";
							} else if ($result_activity[0]['student_no'] == 2) {
								$txt_activity_type = "คู่";
							} else if ($result_activity[0]['student_no'] >= 3) {
								$txt_activity_type = "ทีม " . $result_activity[0]['student_no'] . " คน";
							}
						}

						echo '<td class="text-center">' . $txt_activity_type . '</td>';

						echo '</tr>';
					}

					?>

				</tbody>
			</table>

	<?php
		}
	}
	echo '</div>';

	?>
</div>

<?php

get_footer();

?>