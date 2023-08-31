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


    $sql = "SELECT * FROM wp_schools a 
            INNER JOIN (SELECT DISTINCT(school_id) AS school_id FROM wp_studentreg) b 
            ON a.school_id = b.school_id 
            ORDER BY a.ID ASC ";
    $school_list = $wpdb->get_results($sql, ARRAY_A);

    ?>
    <div class="h4">สรุปอันดับรางวัลแต่ละสถานศึกษา</div>

    <table class="table text-nowrap">
        <thead>
            <tr>
                <th class="col-md-4">สถานศึกษา</th>
                <th class="col-md-2 text-center">ชนะเลิศ</th>
                <th class="col-md-2 text-center">รองชนะเลิศ อันดับ 1</th>
                <th class="col-md-2 text-center">รองชนะเลิศ อันดับ 2</th>
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
                        count(case when ranking='3' then 1 else null end) as cnt_3
                        FROM wp_school_score
                        WHERE school_id = '{$value['school_id']}' ";
                $school_cnt = $wpdb->get_results($sql, ARRAY_A);
                $school_cnt_sum = $school_cnt[0]['cnt_1'] + $school_cnt[0]['cnt_2'] + $school_cnt[0]['cnt_3'];

                $tmp_cnt_sort = ($school_cnt[0]['cnt_1'] * 3) + ($school_cnt[0]['cnt_2'] * 2) + ($school_cnt[0]['cnt_3'] * 1);

                $tmp_arr[] = [
                    "school_id" => $value['school_id'],
                    "school_name" => $value['school_name'],
                    "cnt_1" =>  $school_cnt[0]['cnt_1'],
                    "cnt_2" =>  $school_cnt[0]['cnt_2'],
                    "cnt_3" =>  $school_cnt[0]['cnt_3'],
                    "cnt_sum" => $school_cnt_sum,
                    "cnt_sort" => $tmp_cnt_sort,
                ];
            }
            

            function DescSort($val1, $val2)
            {
                #check if both the values are equal
                if ($val1['cnt_sort'] == $val2['cnt_sort']) return 0;
                #check if not equal, then compare values
                return ($val1['cnt_sort'] < $val2['cnt_sort']) ? 1 : -1;
            }
            #apply usort method on the array
            usort($tmp_arr, 'DescSort');
            //print_r($tmp_arr);

            
            foreach ($tmp_arr as $key => $value) {
                echo '<tr>';
                echo '<td class="text-start">' . $value['school_name'] . '</td>';
                echo '<td class="text-center">' . $value['cnt_1'] . '</td>';
                echo '<td class="text-center">' . $value['cnt_2'] . '</td>';
                echo '<td class="text-center">' . $value['cnt_3'] . '</td>';
                echo '<td class="text-center"><strong>' . $value['cnt_sum'] . '</strong></td>';
                echo '</tr>';
            }


            ?>
        </tbody>
    </table>


</div>

<?php

get_footer();

?>