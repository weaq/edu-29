<?php
/*
 * Template Name: Show Score Template
 * Template Post Type: page
 */

get_header();

?>

<div class="container mt-3 mb-5">
    <?php

    $current_user = wp_get_current_user();

    global $wpdb;

    // group status
    $arr_group_status = [
        "1" => ["short_name" => "อปท.", "name" => "การแข่งขันทักษะวิชาการ",],
        "21" => ["short_name" => "สพป.", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
        "22" => ["short_name" => "สพม.", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
    ];


    $sql = "SELECT a.* FROM `wp_groupsara` a INNER JOIN wp_school_record b on a.group_id = b.group_id 
            WHERE a.group_id = {$_GET['group_id']} AND b.school_id = '{$current_user->user_login}' ";
    $chk_permision = $wpdb->get_results($sql, ARRAY_A);

    echo '<div class="h4">ผลการประกวดแข่งขัน</div>';

    $sql = "SELECT * FROM wp_schools WHERE school_id = {$current_user->user_login}";
    $wp_schools = $wpdb->get_results($sql, ARRAY_A);

    //echo '<div class="fs-4">' . $wp_schools[0]['school_name'] . '</div>';

    echo '<div class="row">';
    echo '<div class="col-md-4">';
    echo '<strong>กลุ่ม : </strong>';
    echo '<select name="group" onchange="location = this.value;">';
    echo '<option value="?group_status_id=0" selected >กรุณาเลือกกลุ่ม</option>';

    foreach ($arr_group_status as $key => $value) {
        $selected = ($_GET['group_status_id'] == $key) ? "selected" : "";
        // list
        echo '<option value="?group_status_id=' . $key . '" ' . $selected . '>' . $value['short_name'] . '</option>';
    }

    echo '</select>';
    echo '</div>';


    // group
    if ($_GET['group_status_id']) {
        $sql = "SELECT DISTINCT(group_id) as group_id, group_name FROM wp_groupsara WHERE group_status = '{$_GET['group_status_id']}' ORDER BY group_id ASC";
        $query = $wpdb->get_results($sql, ARRAY_A);

        echo '<div class="col-md-8">';
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

    echo "</div>";

    // class
    if ($_GET['group_status_id'] && $_GET['group_id']) {

        // get class array
        $sql = "SELECT DISTINCT(class_id) ,class_name FROM `wp_groupsara` WHERE group_status = '{$_GET['group_status_id']}' AND group_id = '{$_GET['group_id']}' ORDER BY class_id ASC";
        $arr_class = $wpdb->get_results($sql, ARRAY_A);


        //
        echo '<div>';

        $sql = "SELECT COUNT(activity_name), activity_name, group_name FROM wp_groupsara WHERE group_status = '{$_GET['group_status_id']}' AND group_id = '{$_GET['group_id']}' GROUP BY activity_name ORDER BY activity_id ASC";

        $query = $wpdb->get_results($sql, ARRAY_A);

        echo '<div class="fs-3 mt-3">รายการ' . $arr_group_status[$_GET['group_status_id']]['name'] . ' (' . $arr_group_status[$_GET['group_status_id']]['short_name'] . ') <br/>กลุ่มสาระการเรียนรู้ : ' . $query[0]['group_name'] . '</div>';

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


                                $sql = "SELECT COUNT(DISTINCT(school_id)) as school_count FROM `wp_studentreg` WHERE groupsara_id = '{$result_activity[0]['ID']}' ";
                                $school_id_count = $wpdb->get_results($sql, ARRAY_A);

                                if ($school_id_count[0]['school_count']) {

                                    $sql = "SELECT * FROM wp_school_score WHERE groupsara_id = '{$result_activity[0]['ID']}'";
                                    $result_school_score = $wpdb->get_results($sql, ARRAY_A);

                                    if (empty($result_school_score[0]['id'])) {
                                        echo '<td class="text-center">';
                                        if (count($chk_permision) > 0) {
                                            echo '<a href="../score-form/?sID=' . $result_activity[0]['ID'] . '" target="_blank" >';
                                        }
                                        echo 'รอผล';
                                        if (count($chk_permision) > 0) {
                                            echo '</a>';
                                        }
                                        echo '</td>';
                                    } else {
                                        echo '<td class="text-center">';
                                        echo '<a href="../score-activity/?sID=' . $result_activity[0]['ID'] . '" target="_blank" >ผลการประกวด</a>';
                                        if (count($chk_permision) > 0) {
                                            echo '&nbsp; &nbsp; <a href="../score-form/?sID=' . $result_activity[0]['ID'] . '" target="_blank" >แก้ไข</a>';
                                        }
                                        echo '</td>';
                                    }
                                } else {
                                    echo '<td class="text-center">ไม่มีผู้สมัคร</td>';
                                }
                            } else {
                                echo '<td class="bg-secondary">&nbsp;</td>';
                            }
                        

                        if ($result_activity[0]['student_no'] == 1) {
                            $txt_activity_type = "เดี่ยว";
                        } else if ($result_activity[0]['student_no'] == 2) {
                            $txt_activity_type = "คู่";
                        } else if ($result_activity[0]['student_no'] >= 3) {
                            if ($result_activity[0]['student_no'] == $result_activity[0]['student_no_min']) {
                                $txt_activity_type = "ทีม " . $result_activity[0]['student_no'] . " คน";
                            } else {
                                $txt_activity_type = "ทีม " . $result_activity[0]['student_no_min'] . "-" . $result_activity[0]['student_no'] . " คน";
                            }
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

    echo '</div>';

    ?>
</div>

<?php

get_footer();

?>