<?php
/*
 * Template Name: Show Score Template
 * Template Post Type: page
 */

get_header();

?>

<div class="container mt-3 mb-5">
    <?php

    $sID = $_GET['sID'];

    $current_user = wp_get_current_user();

    global $wpdb;

    // group status
    $arr_group_status = [
        "1" => ["short_name" => "อปท", "name" => "การแข่งขันทักษะวิชาการ",],
        "21" => ["short_name" => "สพป", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
        "22" => ["short_name" => "สพม", "name" => "การแข่งขันงานศิลปหัตถกรรมนักเรียน",],
    ];

    $sql = "SELECT * FROM wp_groupsara WHERE ID = {$sID} ";
    $wp_groupsara = $wpdb->get_results($sql, ARRAY_A);
    echo '<div class="text-center h3 my-3">';
    echo '<div>' . $arr_group_status[$wp_groupsara[0]['group_status']]['name'] . ' (' .  $arr_group_status[$wp_groupsara[0]['group_status']]['short_name'] . ')</div>';
    echo '<div>' . $wp_groupsara[0]['group_name'] . '</div>';
    echo '<div>' . $wp_groupsara[0]['activity_name'] . ' ' . $wp_groupsara[0]['class_name'] . '</div>';
    echo '</div>';

    $sql = "SELECT * FROM wp_schools a 
    INNER JOIN (SELECT * FROM wp_school_score WHERE groupsara_id LIKE '{$sID}' ) b 
    ON a.school_id = b.school_id 
    ORDER BY b.score DESC ";
    $school_score = $wpdb->get_results($sql, ARRAY_A);

    foreach ($school_score as $key => $value) {

        echo '<div class="row">';
        echo '  <div class="col-6 text-end">';
        echo '      <div class="h5">name</div>';
        echo '  </div>';
        echo '  <div class="col-6 text-start">';
        echo '  </div>';
        echo '  <div class="col-6 text-start">';
        echo '  </div>';
        echo '  <div class="col-6 text-start">';
        echo '  </div>';
        echo '  <div class="col-6 text-start">';
        echo '  </div>';
        echo '  <div class="col-6 text-start">';
        echo '  </div>';
        echo '</div>';
    }
    ?>

</div>

<?php

get_footer();

?>