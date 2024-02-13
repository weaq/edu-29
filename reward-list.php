<?php
/*
 * Template Name: Reward List Template
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


    foreach ($arr_group_status as $key_group_status => $value_group_status) {
        //echo $key_group_status . " = " . $value_group_status['short_name'] . '<br/>';       

        $sql = "SELECT DISTINCT(a.school_id) AS school_id , a.go_id, c.school_name, c.go_name 
                FROM wp_studentreg a 
                INNER JOIN (SELECT * FROM wp_groupsara ) b 
                ON a.groupsara_id = b.ID 
                INNER JOIN wp_schools c 
                ON a.school_id = c.school_id 
                WHERE b.group_status = '{$key_group_status}'
                ORDER BY school_id ASC";
            
        $school_list = $wpdb->get_results($sql, ARRAY_A);

        $sql = "SELECT * FROM `wp_school_score` a 
        LEFT JOIN wp_groupsara b  
        ON a.groupsara_id = b.ID 
        WHERE b.group_status = '{$key_group_status}' ";
        $is_record = $wpdb->get_results($sql, ARRAY_A);
        if (count($is_record) > 0) {

    ?>
            <div class="h4">สรุปอันดับรางวัลตามเกณฑ์<?php echo  $value_group_status['name'] . " " .  $value_group_status['short_name'];  ?></div>

            <table class="table text-nowrap">
                <thead>
                    <tr>
                        <th class="col-md-4">สถานศึกษา</th>
                        <th class="col-md-2 text-center">ชนะเลิศ</th>
                        <th class="col-md-2 text-center">รองชนะเลิศ อันดับ 1</th>
                        <th class="col-md-2 text-center">รองชนะเลิศ อันดับ 2</th>
                        <th class="col-md-2 text-center">ชมเชย</th>
                        <th class="col-md-2 text-center">รวม</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    $tmp_arr = [];

                    foreach ($school_list as $key => $value) {

                        $sql = "SELECT 
                        count(case when ranking='1' then 1 else null end) as cnt_1 ,
                        count(case when ranking='2' then 1 else null end) as cnt_2 ,
                        count(case when ranking='3' then 1 else null end) as cnt_3 , 
                        count(case when ranking>'3' then 1 else null end) as cnt_over
                        FROM wp_school_score a 
                        INNER JOIN wp_groupsara b 
                        ON a.groupsara_id = b.ID
                        WHERE b.group_status = '{$key_group_status}' AND a.school_id = '{$value['school_id']}' ";

                        $school_cnt = $wpdb->get_results($sql, ARRAY_A);
                        $school_cnt_sum = $school_cnt[0]['cnt_1'] + $school_cnt[0]['cnt_2'] + $school_cnt[0]['cnt_3'] + $school_cnt[0]['cnt_over'];

                        $tmp_arr[] = [
                            "school_id" => $value['school_id'],
                            "school_name" => $value['school_name'],
                            "cnt_1" =>  $school_cnt[0]['cnt_1'],
                            "cnt_2" =>  $school_cnt[0]['cnt_2'],
                            "cnt_3" =>  $school_cnt[0]['cnt_3'],
                            "cnt_over" =>  $school_cnt[0]['cnt_over'],
                            "cnt_sum" => $school_cnt_sum,
                        ];
                    }



                    #sort array
                    array_multisort(array_column($tmp_arr, 'cnt_1'), SORT_DESC, array_column($tmp_arr, 'cnt_2'), SORT_DESC, array_column($tmp_arr, 'cnt_3'), SORT_DESC, $tmp_arr);


                    //print_r($tmp_arr);


                    foreach ($tmp_arr as $key => $value) {
                        echo '<tr>';
                        echo '<td class="text-start"><a href="../reward-school-list/?school_id=' . $value['school_id'] . '" target="_blank">' . $value['school_name'] . '</a></td>';
                        echo '<td class="text-center">' . $value['cnt_1'] . '</td>';
                        echo '<td class="text-center">' . $value['cnt_2'] . '</td>';
                        echo '<td class="text-center">' . $value['cnt_3'] . '</td>';
                        echo '<td class="text-center">' . $value['cnt_over'] . '</td>';
                        echo '<td class="text-center"><strong>' . $value['cnt_sum'] . '</strong></td>';
                        echo '</tr>';
                    }


                    ?>
                </tbody>
            </table>

    <?php
        }
    }
    ?>


</div>

<?php
get_footer();

?>