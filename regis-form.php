<?php
/*
 * Template Name: Register Form Template
 * Template Post Type: page
 */

get_header();

global $wpdb;

$domain = 'http://127.0.0.1/wordpress/';
//$domain = '/';
?>

<?php if (isset($_GET['success'])) : ?>
    <div class="alert alert-success text-center">
        <div class="h3">บันทึกข้อมูลเรียบร้อยแล้ว</div>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])) : ?>
    <div class="alert alert-danger">
        <h3>Sorry! Unable to submit the form.</h3>
    </div>
<?php endif; ?>

<?php if (isset($_GET['rm'])) : ?>
    <div class="alert alert-success text-center">

        <?php
        if ($_GET['sID'] && is_user_logged_in() && $current_user->roles[0] == 'contributor' && isset($_GET['rm'])) {

            $error_delete = 1;

            $sql = "SELECT ID, activity_id, activity_name, group_status, class_id, class_name, student_no, teacher_no FROM wp_groupsara WHERE ID = '" . $_GET['sID'] . "' ORDER BY activity_name ASC ";
            $wp_groupsara = $wpdb->get_results($sql, ARRAY_A);

            $sql = "DELETE FROM wp_studentreg WHERE school_id = {$current_user->user_login} AND groupsara_id = {$_GET['sID']} ";
            if ($wpdb->query($sql)) {
                $error_delete = 0;
            } else {
                $error_delete = 1;
            }

            $sql = "DELETE FROM wp_teacherreg WHERE school_id = {$current_user->user_login} AND groupsara_id = {$_GET['sID']} ";
            if ($wpdb->query($sql)) {
                $error_delete = 0;
            } else {
                $error_delete = 1;
            }

            if ($error_delete ==  0) {
                echo '<div class="h3">ยกเลิกการสมัคร ' . $wp_groupsara[0]['activity_name'] . ' ' . $wp_groupsara[0]['class_name'] . ' เรียบร้อยแล้ว</div>';
            }

        }
        ?>

    </div>
<?php endif; ?>

<form name="contact_form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" autocomplete="on" accept-charset="utf-8">

    <?php
    $current_user = wp_get_current_user();
    $output = "";

    if ($_GET['sID'] && is_user_logged_in() && $current_user->roles[0] == 'contributor' && empty($_GET['rm'])) {

        $sql = "SELECT * FROM wp_schools WHERE school_id = {$current_user->user_login}";
        $wp_schools = $wpdb->get_results($sql, ARRAY_A);

        $sql = "SELECT ID, activity_id, activity_name, group_status, class_id, class_name, student_no, teacher_no FROM wp_groupsara WHERE ID = '" . $_GET['sID'] . "' ORDER BY activity_name ASC ";
        $wp_groupsara = $wpdb->get_results($sql, ARRAY_A);

        $sql = "SELECT * FROM wp_studentreg WHERE school_id = {$current_user->user_login} AND groupsara_id = {$_GET['sID']} ";
        $student_reg_chk = $wpdb->get_results($sql, ARRAY_A);

        $sql = "SELECT * FROM wp_teacherreg WHERE school_id = {$current_user->user_login} AND groupsara_id = {$_GET['sID']} ";
        $teacher_reg_chk = $wpdb->get_results($sql, ARRAY_A);

        // group status
        $arr_group_status = [
            "1" => ["short_name" => "อปท", "name" => "การแข่งขันทักษะวิชาการ",],
            "21" => ["short_name" => "สพป", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
            "22" => ["short_name" => "สพม", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
        ];

        $output = '
		<div class="container my-3">
            <div class="fs-4">' . $wp_schools[0]['school_name'] . '</div>
            <div class="fs-4 fw-bold">ลงทะเบียน' . $arr_group_status[$wp_groupsara[0]['group_status']]['name'] . '<br/>' . $wp_groupsara[0]['activity_name'] . ' '  . $wp_groupsara[0]['class_name'] . ' '  . '</div>


			<div class="fs-5 fw-bold text-center mt-2">ชื่อผู้แข่งขัน</div>
		';

        for ($i = 0; $i < $wp_groupsara[0]['student_no']; $i++) {
            $tmp_num = $i + 1;

            $img_url = './img-upload/student_img/' . $current_user->user_login . '-' . $student_reg_chk[$i]['ID'] . '.jpg';

            $img_url = (file_exists($img_url)) ? $domain . 'img-upload/student_img/' . $current_user->user_login . '-' . $student_reg_chk[$i]['ID'] . '.jpg' : $domain . 'img-upload/unknow.jpg';

            $output .= '
            <div class="border px-3 py-3 my-3">
                <div class="fw-bold">ผู้แข่งขัน คนที่ ' . $tmp_num . '</div>
                <div class="row">
                <div class="col-md-10">
                    <div class="row">
                        <div class="mt-2 col-md-4">
                            <label class="form-label">คำนำหน้า</label>
                            <input type="text" class="form-control" id="student_prefix[' . $i . ']" name="student_prefix[' . $i . ']" value="' . $student_reg_chk[$i]['student_prefix'] . '" >
                        </div>
                        <div class="mt-2 col-md-4">
                            <label class="form-label">ชื่อ</label>
                            <input type="text" class="form-control" id="student_firstname[' . $i . ']" name="student_firstname[' . $i . ']" value="' . $student_reg_chk[$i]['student_firstname'] . '" >
                        </div>
                        <div class="mt-2 col-md-4">
                            <label class="form-label">สกุล</label>
                            <input type="text" class="form-control" id="student_lastname[' . $i . ']" name="student_lastname[' . $i . ']" value="' . $student_reg_chk[$i]['student_lastname'] . '" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group mt-2">
                            <input type="file" class="form-control" id="student_img[' . $i . ']" name="student_img[' . $i . ']">
                            <label class="input-group-text" for="student_img[' . $i . ']">เลือกรูปถ่าย</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <img src="' . $img_url . '" class="rounded img-fluid" >
                </div>
                </div>
            </div>
			';
        }


        if ($wp_groupsara[0]['class_id'] != "11") {
            $output .= '
			<div class="fs-5 fw-bold text-center mt-3">ชื่อผู้ควบคุม</div>
			';
            for ($i = 0; $i < $wp_groupsara[0]['teacher_no']; $i++) {
                $tmp_num = $i + 1;
                $img_url = './img-upload/coach_img/' . $current_user->user_login . '-' . $teacher_reg_chk[$i]['ID'] . '.jpg';

                $img_url = (file_exists($img_url)) ? $domain . 'img-upload/coach_img/' . $current_user->user_login . '-' . $teacher_reg_chk[$i]['ID'] . '.jpg' : $domain . 'img-upload/unknow.jpg';

                $output .= '
            <div class="border px-3 py-3 my-3">
			<div class="fw-bold">ผู้ควบคุม คนที่ ' . $tmp_num . '</div>
                <div class="row">
                    <div class="col-md-10">
                        <div class="row">
                            <div class="mt-2 col-md-3">
                                <label class="form-label">คำนำหน้า</label>
                                <input type="text" class="form-control" id="coach_prefix[' . $i . ']" name="coach_prefix[' . $i . ']" value="' . $teacher_reg_chk[$i]['teacher_prefix'] . '" >
                            </div>
                            <div class="mt-2 col-md-3">
                                <label class="form-label">ชื่อ</label>
                                <input type="text" class="form-control" id="coach_firstname[' . $i . ']" name="coach_firstname[' . $i . ']" value="' . $teacher_reg_chk[$i]['teacher_firstname'] . '" >
                            </div>
                            <div class="mt-2 col-md-3">
                                <label class="form-label">สกุล</label>
                                <input type="text" class="form-control" id="coach_lastname[' . $i . ']" name="coach_lastname[' . $i . ']" value="' . $teacher_reg_chk[$i]['teacher_lastname'] . '" >
                            </div>
                            <div class="mt-2 col-md-3">
                                <label class="form-label">หมายเลขโทรศัพท์</label>
                                <input type="text" class="form-control" id="coach_tel[' . $i . ']" name="coach_tel[' . $i . ']" value="' . $teacher_reg_chk[$i]['tel'] . '" >
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-group mt-2 mb-2">
                                <input type="file" class="form-control" id="coach_img[' . $i . ']" name="coach_img[' . $i . ']">
                                <label class="input-group-text" for="coach_img[' . $i . ']">เลือกรูปถ่าย</label>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-2">
                        <img src="' . $img_url . '" class="rounded img-fluid" >
                    </div>
                </div>
            </div>

			';
            }
        }

        $output .= '
        <div class="mt-3 mb-3 text-center">
            <input type="hidden" id="school_id" name="school_id" value="' . $current_user->user_login . '">
            <input type="hidden" id="go_id" name="go_id" value="' . $wp_schools[0]['go_id'] . '">
            <input type="hidden" id="groupsara_id" name="groupsara_id" value="' . $wp_groupsara[0]['ID'] . '">
            <input type="hidden" id="activity_id" name="activity_id" value="' . $wp_groupsara[0]['activity_id'] . '">
            <input type="hidden" id="class_id" name="class_id" value="' . $wp_groupsara[0]['class_id'] . '">

            <input type="hidden" name="action" value="contact_form">
            <input type="hidden" name="base_page" value="' . get_permalink(get_queried_object_id()) . '">

            <div class="row">
            <div class="col-md-6 text-center">
                <div class="btn btn-warning mx-3 my-3" onclick="js_remove_record()">ยกเลิกการลงทะเบียน</div>
                </div>
                <div class="col-md-6 text-center">
                <button type="submit" class="btn btn-primary mx-3 my-3">บันทึกข้อมูล</button>
                </div>
            </div>
        </div>
        ';
    }


    echo $output;
    ?>

</form>

<?php

get_footer();

?>