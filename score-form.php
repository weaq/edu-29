<?php
/*
 * Template Name: Score Form Template
 * Template Post Type: page
 */

get_header();

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

            $sql = "SELECT a.* FROM `wp_groupsara` a INNER JOIN wp_school_record b on a.group_id = b.group_id 
            WHERE a.ID = {$_GET['sID']} AND b.school_id = '{$current_user->user_login}' ";
            $wp_groupsara = $wpdb->get_results($sql, ARRAY_A);

            if (count($wp_groupsara) > 0) {



                $sql = "DELETE FROM wp_school_score WHERE groupsara_id = {$_GET['sID']}";
                if ($wpdb->query($sql)) {
                    $error_delete = 0;
                } else {
                    $error_delete = 1;
                }


                if ($error_delete ==  0) {
                    echo '<div class="h3">ยกเลิกการสมัคร ' . $wp_groupsara[0]['activity_name'] . ' ' . $wp_groupsara[0]['class_name'] . ' เรียบร้อยแล้ว</div>';
                }
            }
        }
        ?>

    </div>
<?php endif; ?>

<?php

if (isset($_GET['sID'])) {
    $sID = $_GET['sID'];


    $current_user = wp_get_current_user();

    global $wpdb;

    // group status
    $arr_group_status = [
        "1" => ["short_name" => "อปท.", "name" => "การแข่งขันทักษะวิชาการ",],
        "21" => ["short_name" => "สพป.", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
        "22" => ["short_name" => "สพม.", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
    ];

    $sql = "SELECT a.* FROM `wp_groupsara` a INNER JOIN wp_school_record b on a.group_id = b.group_id 
            WHERE a.ID = {$sID} AND b.school_id = '{$current_user->user_login}' ";
    $wp_groupsara = $wpdb->get_results($sql, ARRAY_A);

    if (count($wp_groupsara) > 0) {

        $sql = "SELECT * FROM wp_schools a 
                        INNER JOIN (SELECT DISTINCT(school_id) AS school_id FROM wp_studentreg WHERE groupsara_id = {$sID}) b 
                        ON a.school_id = b.school_id ORDER BY a.ID ASC";
        $wp_schools = $wpdb->get_results($sql, ARRAY_A);

?>

        <div class="container mt-3 mb-5">
            <div class="text-end">
                <span class="me-5"><a href="../export-data/export-form-regis.php?sID=<?php echo $sID; ?>" target="_blank">พิมพ์ใบลงทะเบียน</a></span>
                <span class="me-5"><a href="../export-data/export-competition.php?sID=<?php echo $sID; ?>" target="_blank">พิมพ์ผลการประกวด</a></span>
                <span class="me-5"><a href="../export-data/export-competition-with-name.php?sID=<?php echo $sID; ?>" target="_blank">พิมพ์ผลการประกวด + รายชื่อ</a></span>
            </div>
            <form name="contact_form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" autocomplete="on" accept-charset="utf-8">
                <div class="row">

                    <?php

                    echo '<div class="text-center h5 my-3">';
                    echo '<div>กรอกผลคะแนน' . $arr_group_status[$wp_groupsara[0]['group_status']]['name'] . '</div>';
                    echo '<div>กลุ่มสาระการเรียนรู้ ' . $wp_groupsara[0]['group_name'] . ' ตามหลักเกณฑ์ ' . $arr_group_status[$wp_groupsara[0]['group_status']]['short_name'] .  '</div>';
                    echo '<div>รายการแข่งขัน ' . $wp_groupsara[0]['activity_name'] .  '</div>';
                    echo '<div>ระดับการแข่งขัน ' . $wp_groupsara[0]['class_name'] .  '</div>';
                    echo '</div>';


                    if (count($wp_schools) > 0) {
                        foreach ($wp_schools as $key => $value) {

                            $sql = "SELECT * FROM wp_school_score WHERE groupsara_id = '{$sID}' AND school_id = '{$value['school_id']}' ";
                            $result_school_score = $wpdb->get_results($sql, ARRAY_A);

                            echo '<div class="row">';
                            echo '<div class="col-6 text-end">';
                            echo '<div class="h5">' . $value['school_name'] . '</div>';
                            echo '</div>';
                            echo '<div class="col-6 text-start">';
                            echo '<input type="number" class="form-control" id="score[' . $value['school_id'] . ']" name="score[' . $value['school_id'] . ']" value="' . $result_school_score[0]['score'] . '" min="0" max="100" step=".01" placeholder="คะแนนที่ได้" >';
                            echo '</div>';
                            echo '</div>';
                        }

                    ?>

                </div>
                <div class="mt-3 mb-3 text-center">
                    <input type="hidden" id="groupsara_id" name="groupsara_id" value="<?php echo $_GET['sID']; ?>">

                    <input type="hidden" name="action" value="score_form">
                    <input type="hidden" name="base_page" value="<?php echo get_permalink(get_queried_object_id()); ?>">

                    <div class="row">
                        <div class="col-md-6 text-center">
                            <div class="btn btn-warning mx-3 my-3" onclick="js_remove_record()">ลบข้อมูล</div>
                        </div>
                        <div class="col-md-6 text-center">
                            <button type="submit" class="btn btn-primary mx-3 my-3">บันทึกข้อมูล</button>
                        </div>
                    </div>

                <?php
                    } else {
                        echo '<div class="text-center h4 my-5">ไม่มีผู้แข่งขัน</div>';
                    }
                ?>
            </form>
        </div>

<?php
    } else {
        echo '<div class="text-center h5">ไม่ได้รับอนุญาตในการใช้งาน</div>';
    }
} else {
    echo '<div class="text-center h5">ไม่พบข้อมูล</div>';
}
?>

<?php

get_footer();

?>