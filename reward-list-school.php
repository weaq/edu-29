<?php
/*
 * Template Name: Reward School List Template
 * Template Post Type: page
 */

get_header();

?>

<div class="container mt-3 mb-5">
    <?php

    $school_id = $_GET['school_id'];

    if ($school_id) {
        $sql = "SELECT * FROM `wp_schools` WHERE `school_id` LIKE '{$school_id}' ";
        $activity_list = $wpdb->get_results($sql, ARRAY_A);
        $school_name = $activity_list[0]['school_name'];
    }

    $current_user = wp_get_current_user();

    global $wpdb;

    // group status
    $arr_group_status = [
        "1" => ["short_name" => "อปท.", "name" => "การแข่งขันทักษะวิชาการ",],
        "21" => ["short_name" => "สพป.", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
        "22" => ["short_name" => "สพม.", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
    ];

    echo '<div class="h4">สรุปอันดับรางวัลตามเกณฑ์ ของ ' . $school_name . '</div>';

    foreach ($arr_group_status as $key_group_status => $value_group_status) {
        //echo $key_group_status . " = " . $value_group_status['short_name'] . '<br/>';

        echo '<div class="h4 mt-3 mb-2">' . $value_group_status['name'] . " " .  $value_group_status['short_name'] . '</div>';

        for ($i = 1; $i <= 3; $i++) {

            $sql = "SELECT * FROM `wp_groupsara` a 
                    RIGHT JOIN (SELECT *  FROM `wp_school_score` WHERE `school_id` LIKE '{$school_id}'  AND `ranking` = '{$i}' )  b 
                    ON a.ID = b.groupsara_id
                    WHERE a.group_status = '{$key_group_status}' ORDER BY a.group_id, a.class_id ASC ";

            $activity_list = $wpdb->get_results($sql, ARRAY_A);
            if (count($activity_list) > 0) {
                echo '<div class="h5 ms-4 mt-3">' . ranking_txt($i) . '</div>';

                $tmp_group_id = "";

                foreach ($activity_list as $key => $value) {
                    echo '<div class="ms-5">';
                    if ($tmp_group_id != $value['group_id']) {
                        echo '<div class="mt-3"><strong> กลุ่มสาระ : ' . $value['group_name'] . '</strong></div>';
                        $tmp_group_id = $value['group_id'];
                    }
                    echo '<div class="ms-4">';
                    echo " กิจกรรม" . $value['activity_name'] . " " . $value['class_name'];
                    echo " ได้คะแนน " . $value['score'] . " ";
                    echo " " . aword($value['score']);
                    echo '</div>';
                    echo '</div>';
                }
            }
        }
    }
    ?>


</div>

<?php


function ranking_txt($ranking)
{
    if ($ranking == 1) {
        $ranking_txt = " ชนะเลิศ";
    } else  if ($ranking == 2) {
        $ranking_txt = " รองชนะเลิศ อันดับ 1";
    } else if ($ranking == 3) {
        $ranking_txt = " รองชนะเลิศ อันดับ 2";
    } else {
        $ranking_txt = "";
    }
    return $ranking_txt;
}

function aword($score)
{
    if ($score >= 80) {
        $award = " ระดับเกียรติบัตรเหรียญทอง";
    } else if ($score >= 70) {
        $award = " ระดับเกียรติบัตรเหรียญเงิน";
    } else if ($score >= 60) {
        $award = " ระดับเกียรติบัตรเหรียญทองแดง";
    } else if ($score >= 1) {
        $award = " ระดับเกียรติบัตรชมเชย";
    } else {
        $award = "";
    }
    return $award;
}


get_footer();

?>