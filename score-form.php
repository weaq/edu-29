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

<?php
if (isset($_GET['sID'])) {
    $sID = $_GET['sID'];
?>

    <div class="container mt-3 mb-5">
        <form name="contact_form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" autocomplete="on" accept-charset="utf-8">
            <div class="row">

                <?php

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
                        INNER JOIN (SELECT DISTINCT(school_id) AS school_id FROM wp_studentreg WHERE groupsara_id = {$sID}) b 
                        ON a.school_id = b.school_id ORDER BY a.ID ASC";
                $wp_schools = $wpdb->get_results($sql, ARRAY_A);

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
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary mx-3 my-3">บันทึกข้อมูล</button>
                    </div>
                </div>
        </form>
    </div>

<?php
}
?>

<?php

get_footer();

?>