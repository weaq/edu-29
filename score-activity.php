<?php
/*
 * Template Name: Show Score Activity Template
 * Template Post Type: page
 */

get_header();

?>

<div class="container mt-3 mb-5">
    <?php

    $sID = $_GET['sID'];

    if ($sID) {

        $current_user = wp_get_current_user();

        global $wpdb;

        // group status
        $arr_group_status = [
            "1" => ["short_name" => "อปท.", "name" => "การแข่งขันทักษะวิชาการ",],
            "21" => ["short_name" => "สพป.", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
            "22" => ["short_name" => "สพม.", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
        ];

        $sql = "SELECT * FROM wp_groupsara WHERE ID = {$sID} ";
        $wp_groupsara = $wpdb->get_results($sql, ARRAY_A);
        echo '<div class="text-center h5 my-3">';
        echo '<div>ประกาศผลการประกวดแข่งขันทักษะทางวิชาการ</div>';
        echo '<div>กลุ่มสาระการเรียนรู้ ' . $wp_groupsara[0]['group_name'] . ' ตามหลักเกณฑ์ ' . $arr_group_status[$wp_groupsara[0]['group_status']]['short_name'] .  '</div>';
        echo '<div>รายการแข่งขัน ' . $wp_groupsara[0]['activity_name'] . '</div>';
        echo '<div>ระดับการแข่งขัน ' . $wp_groupsara[0]['class_name'] . '</div>';
        echo '<div>' . buddhistCalendar($wp_groupsara[0]['match_date']) . '</div>';
        echo '</div>';

    ?>

        <table class="table">
            <thead>
                <tr>
                    <th>สังกัด</th>
                    <th>สถานศึกษา</th>
                    <th>ผู้แข่งขัน</th>
                    <th>ผู้ควบคุม</th>
                    <th>ผลการประกวด</th>
                    <th>ผลคะแนน</th>
                </tr>
            </thead>
            <tbody>

                <?php

                $sql = "SELECT * FROM wp_schools a 
                    INNER JOIN (SELECT * FROM wp_school_score WHERE groupsara_id LIKE '{$sID}' ) b 
                    ON a.school_id = b.school_id 
                    ORDER BY b.score DESC ";
                $school_score = $wpdb->get_results($sql, ARRAY_A);

                //print_r($school_score);

                foreach ($school_score as $key => $value) {

                    $sql = "SELECT ID AS student_id, student_prefix, student_firstname, student_lastname  FROM wp_studentreg WHERE groupsara_id = {$sID} AND school_id = '{$value['school_id']}' ORDER BY student_id ASC";
                    $studentreg = $wpdb->get_results($sql, ARRAY_A);
                    $student_txt = "";
                    foreach ($studentreg as $s) {
                        $student_txt .= $s['student_prefix'] . " " . $s['student_firstname'] . " " . $s['student_lastname'] . "<br>";
                    }

                    $sql = "SELECT  ID AS teacher_id, teacher_prefix, teacher_firstname, teacher_lastname, tel AS teacher_tel  FROM wp_teacherreg WHERE groupsara_id = {$sID} AND school_id = '{$value['school_id']}' ORDER BY teacher_id ASC";
                    $teacherreg = $wpdb->get_results($sql, ARRAY_A);
                    $teacher_txt = "";
                    foreach ($teacherreg as $s) {
                        $teacher_txt .= $s['teacher_prefix'] . " " . $s['teacher_firstname'] . " " . $s['teacher_lastname'] . "<br>";
                    }

                    if ($value['score'] == 0) {
                        $ranking_txt = "ไม่ได้เข้าร่วม";
                    } else {
                        if ($value['ranking'] == 1) {
                            $ranking_txt = "ชนะเลิศ";
                        } else  if ($value['ranking'] == 2) {
                            $ranking_txt = "รองชนะเลิศ อันดับ 1";
                        } else if ($value['ranking'] == 3) {
                            $ranking_txt = "รองชนะเลิศ อันดับ 2";
                        } else {
                            $ranking_txt = "";
                        }
                    }


                    echo '<tr>';
                    echo '<td>' . $value['go_name'] . '</td>';
                    echo '<td>' . $value['school_name'] . '</td>';
                    echo '<td>' . $student_txt . '</td>';
                    echo '<td>' . $teacher_txt . '</td>';
                    echo '<td>' . $ranking_txt . '</td>';
                    echo '<td class="text-center">' . $value['score'] . '</td>';
                    echo '</tr>';
                }
                ?>

            </tbody>
        </table>


    <?php
    } else {
        // redirect to page
        echo '<div class="text-center h4">ไม่พบข้อมูล ผลการประกวด</div>';
    }
    ?>

</div>

<?php

get_footer();

?>