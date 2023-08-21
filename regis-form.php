<?php
/*
 * Template Name: Register Form Template
 * Template Post Type: page
 */

get_header();

?>

<?php if (isset($_GET['success'])) : ?>
    <div class="alert alert-success">
        <h3>Congrats! Your Form Submitted Successfully.</h3>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])) : ?>
    <div class="alert alert-danger">
        <h3>Sorry! Unable to submit the form.</h3>
    </div>
<?php endif; ?>

<?php
   //print_r($_POST); 
?>

<form name="contact_form" method="POST" action="#" enctype="multipart/form-data" autocomplete="off" accept-charset="utf-8">

    <?php
    $current_user = wp_get_current_user();
    $output = "";

    if ($_GET['sID'] && is_user_logged_in() && ($current_user->roles[0] == 'administrator' || $current_user->roles[0] == 'contributor')) {

        global $wpdb;
        $sql = "SELECT ID, activity_id, activity_name, group_status, class_id, class_name, student_no, teacher_no FROM `wp_groupsara` WHERE ID = '" . $_GET['sID'] . "' ORDER BY `wp_groupsara`.`activity_name` ASC ";
        $query = $wpdb->get_results($sql, ARRAY_A);

        // group status
		$arr_group_status = [
			"1" => "อปท",
			"21" => "สพป",
			"22" => "สพม",
		];

        $output = '
		<div class="container my-3">
            <div class="fs-4 fw-bold">ลงทะเบียนเข้าแข่งขัน ' . $query[0]['activity_name'] . ' '  . $query[0]['class_name'] . ' '  . $arr_group_status[$query[0]['group_status']] . '</div>


			<div class="fs-5 fw-bold text-center mt-2">ชื่อผู้แข่งขัน</div>
		';

        for ($i = 0; $i < $query[0]['student_no']; $i++) {
            $tmp_num = $i+1;
            $output .= '
            <div class="border px-3 py-3 my-3">
			<div class="fw-bold">ผู้แข่งขัน คนที่ ' . $tmp_num . '</div>
            <div class="row">
                <div class="mt-2 col-md-4">
                    <label class="form-label">คำนำหน้า</label>
                    <input type="text" class="form-control" id="student_prefix[' . $i . ']" name="student_prefix[' . $i . ']" value="">
                </div>
                <div class="mt-2 col-md-4">
                    <label class="form-label">ชื่อ</label>
                    <input type="text" class="form-control" id="student_firstname[' . $i . ']" name="student_firstname[' . $i . ']" >
                </div>
                <div class="mt-2 col-md-4">
                    <label class="form-label">สกุล</label>
                    <input type="text" class="form-control" id="student_lastname[' . $i . ']" name="student_lastname[' . $i . ']" >
                </div>
            </div>
            <div class="row">
                <div class="input-group mt-2">
                    <input type="file" class="form-control" id="student_img[' . $i . ']" name="student_img[' . $i . ']">
                    <label class="input-group-text" for="student_img[' . $i . ']">เลือกรูปถ่าย</label>
                </div>
            </div>
            </div>
			';
        }

        $output .= '
			<div class="fs-5 fw-bold text-center mt-3">ชื่อผู้ควบคุม</div>
			';
        for ($i = 0; $i < $query[0]['teacher_no']; $i++) {
            $tmp_num = $i+1;
            $output .= '
            <div class="border px-3 py-3 my-3">
			<div class="fw-bold">ผู้ควบคุม คนที่ ' . $tmp_num . '</div>
            <div class="row">
                <div class="mt-2 col-md-3">
                    <label class="form-label">คำนำหน้า</label>
                    <input type="text" class="form-control" id="coach_prefix[' . $i . ']" name="coach_prefix[' . $i . ']" >
                </div>
                <div class="mt-2 col-md-3">
                    <label class="form-label">ชื่อ</label>
                    <input type="text" class="form-control" id="coach_fistname[' . $i . ']" name="coach_fistname[' . $i . ']" >
                </div>
                <div class="mt-2 col-md-3">
                    <label class="form-label">สกุล</label>
                    <input type="text" class="form-control" id="coach_lastname[' . $i . ']" name="coach_lastname[' . $i . ']" >
                </div>
                <div class="mt-2 col-md-3">
                    <label class="form-label">หมายเลขโทรศัพท์</label>
                    <input type="text" class="form-control" id="coach_tel[' . $i . ']" name="coach_tel[' . $i . ']" >
                </div>
            </div>
            <div class="row">
                <div class="input-group mt-2 mb-2">
                    <input type="file" class="form-control" id="coach_img[' . $i . ']" name="coach_img[' . $i . ']">
                    <label class="input-group-text" for="coach_img[' . $i . ']">เลือกรูปถ่าย</label>
                </div>
            </div>

			</div>

			';
        }

        $output .= '
        <div class="mt-3 mb-3 text-center">
            <input type="hidden" id="school_id" name="school_id" value="' . $current_user->user_login . '">
            <input type="hidden" id="go_id" name="go_id" value="' . $current_user->user_login . '">
            <input type="hidden" id="groupsara_id" name="groupsara_id" value="' . $query[0]['ID'] . '">
            <input type="hidden" id="activity_id" name="activity_id" value="' . $query[0]['activity_id'] . '">
            <input type="hidden" id="class_id" name="class_id" value="' . $query[0]['class_id'] . '">

            <button type="submit" class="btn btn-primary mb-3">บันทึกข้อมูล</button>
        </div>
        ';

        $output .= ' </div> ';
    }
    echo $output;
    ?>

</form>

<?php

get_footer();

?>